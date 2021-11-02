<?php

namespace Controller;

use Framework\Exception\ViewNotFound;
use Framework\Response;

final class OrderController extends Controller
{

    /**
     * @throws ViewNotFound
     */
    public function Action(Response $resp): void
    {
        $this->abortIfUnauthorized();

        $resp->renderView('index');
    }

    /**
     * @throws ViewNotFound
     */
    public function AddAction(Response $resp): void
    {
        $this->abortIfUnauthorized();

        $resp->renderView('add');
    }

    public function AddDoAction(Response $resp): void
    {
        $this->abortIfUnauthorized();
        $this->expectMethodPost();

        var_dump_pre($_POST);
    }

}