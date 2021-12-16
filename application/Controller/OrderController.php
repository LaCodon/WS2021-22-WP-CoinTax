<?php

namespace Controller;

use Core\Calc\Fifo\Fifo;
use Core\Calc\PriceConverter;
use Core\Calc\Tax\WinLossCalculator;
use Core\Exception\WinLossNotCalculableException;
use DateTime;
use DateTimeZone;
use Framework\Exception\ViewNotFound;
use Framework\Framework;
use Framework\Response;
use Framework\Session;
use Framework\Validation\Input;
use Framework\Validation\InputValidator;
use Framework\Validation\ValidationResult;
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
        $this->abortIfUnauthorized($resp);

        $apiController = new ApiController($this->_context);
        $apiController->QueryordersAction($resp, false);

        $this->makeCoinOptions($resp);
        $resp->setViewVar('back_filter', Session::getCurrentFilterQuery());

        $resp->setHtmlTitle('Tradeübersicht');
        $resp->renderView('index');
    }

    /**
     * @throws ViewNotFound
     */
    public function AddAction(Response $resp, bool $render = true): void
    {
        $this->abortIfUnauthorized($resp);

        $this->makeCoinOptions($resp);

        // render is false if we are on the OrderController.EditAction
        if ($render) {
            $resp->setViewVar('back_filter', Session::getCurrentFilterQuery());

            $resp->setHtmlTitle('Trade hinzufügen');
            $resp->renderView('add');
        }
    }

    public function makeCoinOptions(Response $resp): void
    {
        $coinRepo = $this->_context->getCoinRepo();
        $eur = $coinRepo->getBySymbol('EUR');
        $btc = $coinRepo->getBySymbol('BTC');

        $coinOptions = [
            $eur->getSymbol() => [
                'name' => $eur->getName(),
                'thumbnail' => $eur->getThumbnailUrl(),
            ],
            $btc->getSymbol() => [
                'name' => $btc->getName(),
                'thumbnail' => $btc->getThumbnailUrl(),
            ],
            '' => [
                'name' => '<span class="hint">Name oder Symbol zum Suchen eingeben...</span>'
            ]
        ];

        $resp->setViewVar('coin_options', $coinOptions);
    }

    public function AddDoAction(Response $resp, bool $edit = false): void
    {
        $this->abortIfUnauthorized($resp);
        $this->expectMethodPost();

        $oderRepo = $this->_context->getOrderRepo();
        $coinRepo = $this->_context->getCoinRepo();
        $currentUser = Session::getAuthorizedUser();

        // ----------------------- BEGIN validation -----------------------

        $inputFields = [
            new Input(INPUT_POST, 'datetime', 'Ausführungszeitpunkt'),
            new Input(INPUT_POST, 'send_token', 'Gesendetes Token'),
            new Input(INPUT_POST, 'send_amount', 'Menge'),
            new Input(INPUT_POST, 'receive_token', 'Empfangenes Token'),
            new Input(INPUT_POST, 'receive_amount', 'Menge'),
            new Input(INPUT_POST, 'fee_token', 'Gebührentoken', _required: false),
            new Input(INPUT_POST, 'fee_amount', 'Menge', _required: false),
        ];

        if ($edit) {
            $inputFields[] = new Input(INPUT_POST, 'id', 'id', _filter: FILTER_VALIDATE_INT);
        }

        $input = InputValidator::parseAndValidate($inputFields);

        if ($edit) {
            if (!$oderRepo->isOwnedByUser((int)$input->getValue('id'), $currentUser->getId())) {
                $resp->redirect($resp->getActionUrl('index'));
            }
        }

        $datetimeUtc = DateTime::createFromFormat('Y-m-d\TH:i', $input->getValue('datetime'),
            new DateTimeZone('Europe/Berlin'));
        if ($datetimeUtc === false) {
            $input->setError('datetime', 'Ungültiges Format');
        } else {
            $datetimeUtc->setTimezone(new DateTimeZone('UTC'));
        }

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

        if ($baseToken?->getId() === $quoteToken?->getId()) {
            $input->setError('receive_token', 'Das Token muss sich vom gesendeten Token unterscheiden');
        }

        if ($input->hasErrors()) {
            Session::setInputValidationResult($input);
            if ($edit) {
                $resp->redirect($resp->getActionUrl('edit') . '?id=' . $input->getValue('id'));
            } else {
                $resp->redirect($resp->getActionUrl('add'));
            }
        }

        // ----------------------- END validation -----------------------

        $baseTransaction = new Transaction($currentUser->getId(), $datetimeUtc, Transaction::TYPE_SEND, $baseToken->getId(), $baseValue);
        $quoteTransaction = new Transaction($currentUser->getId(), $datetimeUtc, Transaction::TYPE_RECEIVE, $quoteToken->getId(), $quoteValue);
        $feeTransaction = null;
        if (isset($feeToken)) {
            $feeTransaction = new Transaction($currentUser->getId(), $datetimeUtc, Transaction::TYPE_SEND, $feeToken->getId(), $feeValue);
        }

        $order = null;
        if ($edit) {
            try {
                $order = $oderRepo->updateComplete((int)$input->getValue('id'), $currentUser->getId(), $baseTransaction, $quoteTransaction, $feeTransaction);
            } catch (PDOException) {
            }
        } else {
            try {
                $order = $oderRepo->makeAndInsert($baseTransaction, $quoteTransaction, $feeTransaction);
            } catch (PDOException) {
            }
        }

        if ($order === null) {
            $input->setError('datetime', 'Unbekannter Fehler beim einfügen der Order');
            Session::setInputValidationResult($input);
            if ($edit) {
                $resp->redirect($resp->getActionUrl('edit') . '?id=' . $input->getValue('id'));
            } else {
                $resp->redirect($resp->getActionUrl('add'));
            }
        }

        if ($edit) {
            $resp->redirect($resp->getActionUrl('details') . '?id=' . $input->getValue('id'));
        } else {
            $resp->redirect($resp->getActionUrl('details') . '?id=' . $order->getId());
        }
    }

    public function DeleteDoAction(Response $resp): void
    {
        $this->abortIfUnauthorized($resp);

        $input = InputValidator::parseAndValidate([
            new Input(INPUT_GET, 'id', 'id', _filter: FILTER_VALIDATE_INT),
            new Input(INPUT_GET, 'xhr', 'id', _required: false, _filter: FILTER_VALIDATE_INT),
        ]);

        if ($input->hasErrors()) {
            if ($input->getValue('xhr') === '') {
                $resp->redirect($resp->getActionUrl('index') . '?' . Session::getCurrentFilterQuery());
            } else {
                $resp->abort('input errors', Framework::HTTP_BAD_REQUEST);
            }
        }

        $currentUser = Session::getAuthorizedUser();
        $orderRepo = $this->_context->getOrderRepo();

        if (!$orderRepo->delete($input->getValue('id'), $currentUser->getId())) {
            if ($input->getValue('xhr') === '1') {
                $resp->abort('failed to delete order', Framework::HTTP_INTERNAL_SERVER_ERROR);
            }
        }

        if ($input->getValue('xhr') === '') {
            $resp->redirect($resp->getActionUrl('index') . '?' . Session::getCurrentFilterQuery());
        }
    }

    /**
     * @throws ViewNotFound
     */
    public function DetailsAction(Response $resp, bool $render = true): void
    {
        $this->abortIfUnauthorized($resp);

        $input = InputValidator::parseAndValidate([
            new Input(INPUT_GET, 'id', 'id', _filter: FILTER_VALIDATE_INT)
        ]);

        if ($input->hasErrors()) {
            $resp->redirect($resp->getActionUrl('index'));
        }

        $orderId = $input->getValue('id');

        $currentUser = Session::getAuthorizedUser();
        $orderRepo = $this->_context->getOrderRepo();

        if (!$orderRepo->isOwnedByUser($orderId, $currentUser->getId())) {
            $resp->redirect($resp->getActionUrl('index'));
        }

        $order = $orderRepo->get($orderId);
        if ($order === null) {
            $resp->redirect($resp->getActionUrl('index'));
        }

        $orderData = $orderRepo->getComplete($orderId);

        $resp->setViewVar('order_id', $orderId);
        $resp->setViewVar('order', $order);
        $resp->setViewVar('order_data', $orderData);

        // render is false if we are in OrderController.EditAction
        if ($render) {
            $priceConverter = new PriceConverter($this->_context);
            $winLossCalculator = new WinLossCalculator($this->_context);

            $valueEur = [
                'base' => $priceConverter->getEurValueApiOptionalSingle($orderData['base']['tx'], $orderData['base']['coin']),
                'quote' => $priceConverter->getEurValueApiOptionalSingle($orderData['quote']['tx'], $orderData['quote']['coin']),
                'fee' => $orderData['fee'] !== null ? $priceConverter->getEurValueApiOptionalSingle($orderData['fee']['tx'], $orderData['fee']['coin']) : '0.0',
            ];

            $baseSell = null;
            $baseWinLoss = null;
            try {
                $baseSell = $winLossCalculator->calculateWinLoss($orderData['base']['coin'], $currentUser, $orderData['base']['tx']);
                $baseWinLoss = $baseSell[Fifo::ARRAY_ELEM_SALE]->calculateWinLoss($priceConverter, $orderData['base']['coin']);
            } catch (WinLossNotCalculableException $e) {
            }

            debug('-------------------------------<br>');

            $feeSell = null;
            $feeWinLoss = null;
            if ($orderData['fee'] !== null) {
                try {
                    $feeSell = $winLossCalculator->calculateWinLoss($orderData['fee']['coin'], $currentUser, $orderData['fee']['tx']);
                    $feeWinLoss = $feeSell[Fifo::ARRAY_ELEM_SALE]->calculateWinLoss($priceConverter, $orderData['fee']['coin']);
                } catch (WinLossNotCalculableException $e) {
                }
            }

            $resp->setViewVar('back_filter', Session::getCurrentFilterQuery());

            $resp->setViewVar('base_data', $baseSell);
            $resp->setViewVar('base_win_loss', $baseWinLoss);
            $resp->setViewVar('fee_data', $feeSell);
            $resp->setViewVar('fee_win_loss', $feeWinLoss);
            $resp->setViewVar('value_eur', $valueEur);
            $resp->setViewVar('price_converter', $priceConverter);

            $resp->setHtmlTitle('Tradedetails');
            $resp->renderView('details');
        }
    }

    /**
     * @throws ViewNotFound
     */
    public function EditAction(Response $resp): void
    {
        $this->AddAction($resp, false);

        $this->DetailsAction($resp, false);

        $orderData = $resp->getViewVar('order_data');

        if (!Session::hasNonEmptyInputValidationResult()) {
            // only set data if this request is not a response to an invalid form submission
            Session::setInputValidationResult(new ValidationResult([], [
                'datetime' => $orderData['base']['tx']->getDatetimeUtc()->setTimezone(new DateTimeZone('Europe/Berlin'))->format('Y-m-d\TH:i'),
                'send_token' => $orderData['base']['coin']->getSymbol(),
                'receive_token' => $orderData['quote']['coin']->getSymbol(),
                'fee_token' => isset($orderData['fee']) ? $orderData['fee']['coin']->getSymbol() : '',
                'send_amount' => $orderData['base']['tx']->getValue(),
                'receive_amount' => $orderData['quote']['tx']->getValue(),
                'fee_amount' => isset($orderData['fee']) ? $orderData['fee']['tx']->getValue() : '',
            ]));
        }

        $resp->setViewVar('edit_order', true);

        $resp->setHtmlTitle('Order bearbeiten');
        $resp->renderView('add');
    }

    public function EditDoAction(Response $resp): void
    {
        $this->AddDoAction($resp, true);
    }
}