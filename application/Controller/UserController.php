<?php

namespace Controller;

use Framework\Exception\ViewNotFound;
use Framework\Response;
use Framework\Session;

/**
 * Controller for /user
 */
final class UserController extends Controller
{

    /**
     * Endpoint for GET /user/invoice
     * List all bought tax reports and their payment status
     * @throws ViewNotFound
     */
    public function InvoiceAction(Response $resp): void
    {
        $this->abortIfUnauthorized($resp);

        $currentUser = Session::getAuthorizedUser();
        $paymentInfoRepo = $this->_context->getPaymentInfoRepo();

        $payments = $paymentInfoRepo->getAllByUserId($currentUser->getId());

        $resp->setViewVar('user', $currentUser);
        $resp->setViewVar('payments', $payments);


        $resp->setHtmlTitle('Rechnungen');
        $resp->renderView('invoice');
    }

}