<?php

namespace Controller;

use Core\Repository\OrderRepository;
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
        $this->abortIfUnauthorized();

        $currentUser = Session::getAuthorizedUser();

        $orderRepo = new OrderRepository($this->db());
        $orderRepo->getAllByUserId($currentUser->getId());

        $resp->renderView('index');
    }

}