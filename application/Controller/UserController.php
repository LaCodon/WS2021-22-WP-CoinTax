<?php

namespace Controller;

use Core\Repository\PaymentInfoRepository;
use Framework\Exception\ViewNotFound;
use Framework\Response;
use Framework\Session;

final class UserController extends Controller
{

    /**
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