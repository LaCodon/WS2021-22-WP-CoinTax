<?php

namespace Controller;

use Framework\Exception\ViewNotFound;
use Framework\Response;

final class TransactionController extends Controller
{

    /**
     * @throws ViewNotFound
     */
    public function Action(Response $resp): void
    {
        $this->abortIfUnauthorized();

        $orderController = new OrderController($this->_context);
        $orderController->Action($resp, false);

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

        $resp->renderView('index');
    }

}