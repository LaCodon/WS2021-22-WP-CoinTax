<?php

namespace Controller;

use Framework\Exception\ViewNotFound;
use Framework\Response;
use Framework\Session;

final class TransactionController extends Controller
{

    /**
     * @throws ViewNotFound
     */
    public function Action(Response $resp): void
    {
        $this->abortIfUnauthorized($resp);

        $apiController = new ApiController($this->_context);
        $apiController->QuerytransactionsAction($resp, false);

        $orderController = new OrderController($this->_context);

        $orderController->makeCoinOptions($resp);
        $resp->setViewVar('back_filter', Session::getCurrentFilterQuery());

        $resp->setHtmlTitle('Transaktionsübersicht');
        $resp->renderView('index');
    }

}