<?php

namespace Controller;

use Framework\Exception\ViewNotFound;
use Framework\Response;

final class DashboardController extends Controller
{

    /**
     * @throws ViewNotFound
     */
    public function Action(Response $resp): void
    {
        $this->abortIfUnauthorized();

        $resp->renderView('index');
    }

}