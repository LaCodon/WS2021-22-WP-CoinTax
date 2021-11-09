<?php

namespace Controller;

use Core\Repository\CoinRepository;
use Core\Repository\OrderRepository;
use Core\Repository\TransactionRepository;
use DateTime;
use DateTimeZone;
use Framework\Exception\ViewNotFound;
use Framework\Framework;
use Framework\Response;
use Framework\Session;
use Framework\Validation\Input;
use Framework\Validation\InputValidator;
use Model\Transaction;
use PDOException;
use ValueError;

final class OrderController extends Controller
{

    /**
     * @throws ViewNotFound
     */
    public function Action(Response $resp): void
    {
        $this->abortIfUnauthorized();

        $currentUser = Session::getAuthorizedUser();

        $orderRepo = new OrderRepository($this->db());
        $transactionRepo = new TransactionRepository($this->db());
        $coinRepo = new CoinRepository($this->db());

        $orders = $orderRepo->getAllByUserId($currentUser->getId());

        $enrichedOrders = [];

        foreach ($orders as $order) {
            $base = $transactionRepo->get($order->getBaseTransactionId());
            $quote = $transactionRepo->get($order->getQuoteTransactionId());
            $fee = $transactionRepo->get($order->getFeeTransactionId());


            $enrichedOrders[$order->getId()] = [
                'base' => $base,
                'quote' => $quote,
                'fee' => $fee,

                'baseCoin' => $coinRepo->get($base->getCoinId()),
                'quoteCoin' => $coinRepo->get($quote->getCoinId()),
                'feeCoin' => $coinRepo->get($fee?->getCoinId())
            ];
        }

        $resp->setViewVar('orders', $enrichedOrders);

        $resp->renderView('index');
    }

    /**
     * @throws ViewNotFound
     */
    public function AddAction(Response $resp): void
    {
        $this->abortIfUnauthorized();

        $coinRepo = new CoinRepository($this->db());
        $coins = $coinRepo->getAll();

        $coinOptions = [];

        foreach ($coins as $coin) {
            $coinOptions[$coin->getSymbol()] = [
                'name' => $coin->getName(),
                'thumbnail' => $coin->getThumbnailUrl(),
            ];
        }

        $resp->setViewVar('coin_options', $coinOptions);

        $resp->renderView('add');
    }

    public function AddDoAction(Response $resp): void
    {
        $this->abortIfUnauthorized();
        $this->expectMethodPost();

        $input = InputValidator::parseAndValidate([
            new Input(INPUT_POST, 'datetime', 'Ausführungszeitpunkt'),
            new Input(INPUT_POST, 'send_token', 'Gesendetes Token'),
            new Input(INPUT_POST, 'send_amount', 'Menge'),
            new Input(INPUT_POST, 'receive_token', 'Empfangenes Token'),
            new Input(INPUT_POST, 'receive_amount', 'Menge'),
            new Input(INPUT_POST, 'fee_token', 'Gebührentoken', _required: false),
            new Input(INPUT_POST, 'fee_amount', 'Menge', _required: false),
        ]);

        $datetimeUtc = DateTime::createFromFormat('Y-m-d\TH:i', $input->getValue('datetime'),
            new DateTimeZone('Europe/Berlin'));
        if ($datetimeUtc === false) {
            $input->setError('datetime', 'Ungültiges Format');
        } else {
            $datetimeUtc->setTimezone(new DateTimeZone('UTC'));
        }

        $coinRepo = new CoinRepository($this->db());

        $baseToken = $coinRepo->getBySymbol($input->getValue('send_token'));
        if ($baseToken === null) {
            $input->setError('send_token', 'Unbekanntes Token');
        }

        $quoteToken = $coinRepo->getBySymbol($input->getValue('receive_token'));
        if ($quoteToken === null) {
            $input->setError('receive_token', 'Unbekanntes Token');
        }

        $baseValue = '';
        try {
            $baseValue = bcadd(str_replace(',', '.', $input->getValue('send_amount')), '0');
        } catch (ValueError) {
            $input->setError('send_amount', 'Ungültige Eingabe');
        }

        $quoteValue = '';
        try {
            $quoteValue = bcadd(str_replace(',', '.', $input->getValue('receive_amount')), '0');
        } catch (ValueError) {
            $input->setError('receive_amount', 'Ungültige Eingabe');
        }

        if ($input->getValue('fee_token') !== '') {
            $feeToken = $coinRepo->getBySymbol($input->getValue('fee_token'));
            if ($feeToken === null) {
                $input->setError('fee_token', 'Unbekanntes Token');
            }

            $feeValue = '';
            try {
                $feeValue = bcadd(str_replace(',', '.', $input->getValue('fee_amount')), '0');
            } catch (ValueError) {
                $input->setError('fee_amount', 'Ungültige Eingabe');
            }
        }

        if ($baseToken->getId() === $quoteToken->getId()) {
            $input->setError('receive_token', 'Das Token muss sich vom gesendeten Token unterscheiden');
        }

        if ($input->hasErrors()) {
            Session::setInputValidationResult($input);
            $resp->redirect($resp->getActionUrl('add'));
        }

        // ----------------------- END validation -----------------------

        $currentUser = Session::getAuthorizedUser();

        $baseTransaction = new Transaction($currentUser->getId(), $datetimeUtc, Transaction::TYPE_SEND, $baseToken->getId(), $baseValue);
        $quoteTransaction = new Transaction($currentUser->getId(), $datetimeUtc, Transaction::TYPE_RECEIVE, $quoteToken->getId(), $quoteValue);
        $feeTransaction = null;
        if (isset($feeToken)) {
            $feeTransaction = new Transaction($currentUser->getId(), $datetimeUtc, Transaction::TYPE_SEND, $feeToken->getId(), $feeValue);
        }

        $oderRepo = new OrderRepository($this->db());
        $order = null;
        try {
            $order = $oderRepo->makeAndInsert($baseTransaction, $quoteTransaction, $feeTransaction);
        } catch (PDOException $e) {
            var_dump_pre($e);
            die();
        }

        if ($order === null) {
            $input->setError('datetime', 'Unbekannter Fehler beim einfügen der Order');
            Session::setInputValidationResult($input);
            $resp->redirect($resp->getActionUrl('add'));
        }

        $resp->redirect($resp->getActionUrl('index'));
    }

    public function DeleteDoAction(Response $resp): void
    {
        $this->abortIfUnauthorized();

        $input = InputValidator::parseAndValidate([
            new Input(INPUT_GET, 'id', 'id', _filter: FILTER_VALIDATE_INT),
            new Input(INPUT_GET, 'xhr', 'id', _required: false, _filter: FILTER_VALIDATE_INT),
        ]);

        if ($input->hasErrors()) {
            if ($input->getValue('xhr') === '') {
                $resp->redirect($resp->getActionUrl('index'));
            } else {
                $resp->abort('input errors', Framework::HTTP_BAD_REQUEST);
            }
        }

        $currentUser = Session::getAuthorizedUser();
        $orderRepo = new OrderRepository($this->db());

        if (!$orderRepo->delete($input->getValue('id'), $currentUser->getId())) {
            if ($input->getValue('xhr') === '1') {
                $resp->abort('failed to delete order', Framework::HTTP_INTERNAL_SERVER_ERROR);
            }
        }

        if ($input->getValue('xhr') === '') {
            $resp->redirect($resp->getActionUrl('index'));
        }
    }
}