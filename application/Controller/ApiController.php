<?php

namespace Controller;

use Core\Calc\PriceConverter;
use DateTime;
use DateTimeZone;
use Framework\Framework;
use Framework\Html\Paginator;
use Framework\Response;
use Framework\Session;
use Framework\Validation\Input;
use Framework\Validation\InputValidator;
use Framework\Validation\ValidationResult;

final class ApiController extends Controller
{
    public function QuerycoinsAction(Response $resp): void
    {
        $this->abortIfNotLoggedIn();

        $input = InputValidator::parseAndValidate([
            new Input(INPUT_GET, 'query', 'Query', true)
        ]);

        if (preg_match('/^[A-Z]+$/', $input->getValue('query')) !== 1) {
            $input->setError('query', 'only uppercase letters allowed in param query');
        }

        if ($input->hasErrors()) {
            $this->abortWithError($input->getError('query'));
        }

        $coinRepo = $this->_context->getCoinRepo();
        $coins = $coinRepo->getByQuery($input->getValue('query'));

        $coinOptions = [];

        foreach ($coins as $coin) {
            $coinOptions[$coin->getSymbol()] = [
                'name' => $coin->getName(),
                'thumbnail' => $coin->getThumbnailUrl(),
            ];
        }

        header('Content-Type: application/json');
        echo json_encode($coinOptions);
    }

    public function QueryordersAction(Response $resp, bool $render = true): void
    {
        $this->abortIfNotLoggedIn();

        $currentUser = Session::getAuthorizedUser();

        $orderRepo = $this->_context->getOrderRepo();
        $priceConverter = new PriceConverter($this->_context);
        $transactionRepo = $this->_context->getTransactionRepo();
        $coinRepo = $this->_context->getCoinRepo();

        // ----------------------- BEGIN input validation -----------------------
        $input = InputValidator::parseAndValidate([
            new Input(INPUT_GET, 'from', 'Von', false),
            new Input(INPUT_GET, 'to', 'Bis', false),
            new Input(INPUT_GET, 'token', 'Token', false),
        ]);

        $filterFrom = null;
        $filterTo = null;
        $filterCoin = null;

        if ($input->getValue('from') !== '') {
            $filterFrom = DateTime::createFromFormat('Y-m-d\TH:i', $input->getValue('from'),
                new DateTimeZone('Europe/Berlin'));
            if ($filterFrom === false) {
                $input->setError('from', 'Ungültiges Format');
                $filterFrom = null;
            } else {
                $filterFrom->setTimezone(new DateTimeZone('UTC'));
            }
        }

        if ($input->getValue('to') !== '') {
            $filterTo = DateTime::createFromFormat('Y-m-d\TH:i', $input->getValue('to'),
                new DateTimeZone('Europe/Berlin'));
            if ($filterTo === false) {
                $input->setError('from', 'Ungültiges Format');
                $filterTo = null;
            } else {
                $filterTo->setTimezone(new DateTimeZone('UTC'));
            }
        }

        if ($input->getValue('token') !== '') {
            $filterCoin = $coinRepo->getBySymbol($input->getValue('token'));
            if ($filterCoin === null) {
                $input->setError('token', 'Unbekanntes Token');
            }
        }

        if (!Session::hasNonEmptyInputValidationResult()) {
            // only set data if this request is not a response to an invalid form submission
            Session::setInputValidationResult(new ValidationResult([], [
                'from' => $filterFrom !== null ? $filterFrom->setTimezone(new DateTimeZone('Europe/Berlin'))->format('Y-m-d\TH:i') : '',
                'to' => $filterTo !== null ? $filterTo->setTimezone(new DateTimeZone('Europe/Berlin'))->format('Y-m-d\TH:i') : '',
                'token' => $filterCoin !== null ? $filterCoin->getSymbol() : '',
            ]));
        }

        if ($input->hasErrors()) {
            Session::setInputValidationResult($input);
        }
        // ----------------------- END input validation -----------------------

        $itemsPerPage = 10;
        $page = Paginator::getCurrentPage();
        $orders = $orderRepo->getAllByUserIdWithFilter($currentUser->getId(), $filterFrom, $filterTo, $filterCoin, $page, $itemsPerPage);

        $totalOrderCount = $orderRepo->getAllByUserIdWithFilter($currentUser->getId(), $filterFrom, $filterTo, $filterCoin, countOnly: true);
        if (!Paginator::makePagination($resp, $itemsPerPage, $totalOrderCount)) {
            $resp->redirect($resp->getActionUrl('index') . '?' . Session::getCurrentFilterQuery());
        }

        $enrichedOrders = [];

        foreach ($orders as $order) {
            $base = $transactionRepo->get($order->getBaseTransactionId());
            $quote = $transactionRepo->get($order->getQuoteTransactionId());
            $fee = $transactionRepo->get($order->getFeeTransactionId());

            $baseCoin = $coinRepo->get($base->getCoinId());
            $quoteCoin = $coinRepo->get($quote->getCoinId());
            $feeCoin = $coinRepo->get($fee?->getCoinId());

            $enrichedOrders[$order->getId()] = [
                'base' => $base,
                'quote' => $quote,
                'fee' => $fee,

                'baseCoin' => $baseCoin,
                'quoteCoin' => $quoteCoin,
                'feeCoin' => $feeCoin,

                'fiatValue' => $priceConverter->getEurValueApiOptional($base, $quote, $baseCoin, $quoteCoin),
                'feeValue' => $fee !== null ? $priceConverter->getEurValueApiOptionalSingle($fee, $feeCoin) : '-/-',
            ];
        }

        $resp->setViewVar('orders', $enrichedOrders);
        $resp->setViewVar('filterCoin', $filterCoin);

        Session::setCurrentFilter([
            'from' => $input->getValue('from'),
            'to' => $input->getValue('to'),
            'token' => $input->getValue('token'),
        ]);

        if ($render) {
            header('Content-Type: application/json');
            echo json_encode($resp->getViewVar('orders'));
        }
    }

    private function abortIfNotLoggedIn(): void
    {
        $user = Session::getAuthorizedUser();
        if ($user === null) {
            http_response_code(Framework::HTTP_UNAUTHORIZED);
            echo 'Unauthorized';
            exit(0);
        }
    }

    private function abortWithError(string $error): void
    {
        http_response_code(Framework::HTTP_BAD_REQUEST);
        echo $error;
        exit(0);
    }

}