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

        $resp->renderView('index');
    }

}