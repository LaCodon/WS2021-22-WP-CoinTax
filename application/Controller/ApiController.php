<?php

namespace Controller;

use Core\Calc\PriceConverter;
use Core\Repository\OrderRepository;
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
            new Input(INPUT_GET, 'sortAfter', 'Sortieren nach', false),
            new Input(INPUT_GET, 'sortDirection', 'Reihenfolge', false),
        ]);

        $filterFrom = null;
        $filterTo = null;
        $filterCoin = null;
        $sortAfter = OrderRepository::SORT_DATE;
        $sortDirection = OrderRepository::SORT_DESC;

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

        if ($input->getValue('sortAfter') !== '') {
            if ($input->getValue('sortAfter') != OrderRepository::SORT_DATE
                && $input->getValue('sortAfter') != OrderRepository::SORT_SEND_AMOUNT
                && $input->getValue('sortAfter') != OrderRepository::SORT_RECEIVE_AMOUNT) {
                $input->setError('sortAfter', 'Ungültige Auswahl');
            } else {
                $sortAfter = intval($input->getValue('sortAfter'));
            }
        }

        if ($input->getValue('sortDirection') !== '') {
            if ($input->getValue('sortDirection') != OrderRepository::SORT_ASC && $input->getValue('sortDirection') != OrderRepository::SORT_DESC) {
                $input->setError('sortDirection', 'Ungültige Auswahl');
            } else {
                $sortDirection = intval($input->getValue('sortDirection'));
            }
        }

        if (!Session::hasNonEmptyInputValidationResult()) {
            // only set data if this request is not a response to an invalid form submission
            Session::setInputValidationResult(new ValidationResult([], [
                'from' => $filterFrom !== null ? $filterFrom->setTimezone(new DateTimeZone('Europe/Berlin'))->format('Y-m-d\TH:i') : '',
                'to' => $filterTo !== null ? $filterTo->setTimezone(new DateTimeZone('Europe/Berlin'))->format('Y-m-d\TH:i') : '',
                'token' => $filterCoin !== null ? $filterCoin->getSymbol() : '',
                'sortAfter' => $sortAfter,
                'sortDirection' => $sortDirection,
            ]));
        }

        if ($input->hasErrors()) {
            Session::setInputValidationResult($input);
        }
        // ----------------------- END input validation -----------------------

        $itemsPerPage = 10;
        $page = Paginator::getCurrentPage();
        $orders = $orderRepo->getAllByUserIdWithFilter($currentUser->getId(), $filterFrom, $filterTo, $filterCoin, $sortAfter, $sortDirection, $page, $itemsPerPage);

        $totalOrderCount = $orderRepo->getAllByUserIdWithFilter($currentUser->getId(), $filterFrom, $filterTo, $filterCoin, $sortAfter, $sortDirection, countOnly: true);
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

            $enrichedOrders[] = [
                'orderId' => $order->getId(),
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
            'sortAfter' => $input->getValue('sortAfter'),
            'sortDirection' => $input->getValue('sortDirection'),
        ]);

        if ($render) {
            header('Content-Type: application/json');
            echo json_encode($resp->getViewVar('orders'));
        }
    }

    public function QuerytransactionsAction(Response $resp, bool $render = true): void
    {
        $this->abortIfNotLoggedIn();

        $priceConverter = new PriceConverter($this->_context);

        $this->QueryordersAction($resp, false);

        $filterCoin = $resp->getViewVar('filterCoin');

        $txCount = 0;
        $orders = $resp->getViewVar('orders');
        foreach ($orders as &$order) {
            $txCount += 2;

            if ($filterCoin !== null) {
                // order controller filters whole order after given coin but we have to filter the single transactions too
                if ($order['baseCoin']->getSymbol() !== $filterCoin->getSymbol()) {
                    $order['base'] = null;
                    $order['baseCoin'] = null;
                    --$txCount;
                }

                if ($order['quoteCoin']->getSymbol() !== $filterCoin->getSymbol()) {
                    $order['quote'] = null;
                    $order['quoteCoin'] = null;
                    --$txCount;
                } else {
                    // In the OrderController, we fetch the price for the quoteCoin but only if the baseCoin is not EUR.
                    // To ensure the correct price for this single transaction, we re-fetch the price for the quote
                    // transaction at this point.
                    $order['fiatValue'] = $priceConverter->getEurValueApiOptionalSingle($order['quote'], $order['quoteCoin']);
                }

                if ($order['fee'] !== null && $order['feeCoin']->getSymbol() !== $filterCoin->getSymbol()) {
                    $order['fee'] = null;
                    $order['feeCoin'] = null;
                }
            }

            if ($order['fee'] !== null) {
                ++$txCount;
            }
        }

        $resp->setViewVar('orders', $orders);
        $resp->setViewVar('tx_count', $txCount);

        if ($render) {
            header('Content-Type: application/json');
            echo json_encode($resp->getViewVar('orders'));
        }
    }

    public function registerAction(Response $resp): void
    {
        $this->expectMethodPost();


        $registerController = new RegisterController($this->_context);
        $status = $registerController->RegisterDoAction($resp, false);

        header('Content-Type: application/json');

        switch ($status) {
            case RegisterController::RESULT_EMAIL_TAKEN:
                http_response_code(Framework::HTTP_BAD_REQUEST);
                echo json_encode(['result' => 'email_taken']);
                break;
            case RegisterController::RESULT_INVALID_INPUT:
                http_response_code(Framework::HTTP_BAD_REQUEST);
                echo json_encode(['result' => 'invalid_input', 'validation' => Session::getInputValidationResult()]);
                break;
            case RegisterController::RESULT_UNKNOWN_ERROR:
                http_response_code(Framework::HTTP_INTERNAL_SERVER_ERROR);
                echo json_encode(['result' => 'unknown_error']);
                break;
            case RegisterController::RESULT_SUCCESS:
                echo json_encode(['result' => 'success']);
                break;
        }

        // clear session because we sent it already as response
        Session::clearInputValidationResult();
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