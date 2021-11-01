<?php

namespace Controller;

use Framework\Exception\ViewNotFound;
use Framework\Response;

final class IndexController extends Controller
{

    /**
     * @throws ViewNotFound
     */
    public function Action(Response $resp): void
    {
        $resp->setHtmlTitle('Willkommen');
        $resp->renderView('index');
    }

}