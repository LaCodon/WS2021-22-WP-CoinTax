<?php

namespace Controller;

use Framework\Exception\ViewNotFound;
use Framework\Response;

class Controller
{
    /**
     * Default action for rendering index views
     * @param Response $resp
     */
    public function Action(Response $resp): void
    {
        try {
            $resp->renderView("index");
        } catch (ViewNotFound $e) {
            echo 'No "index" view or "Action()" method defined for this controller (' . $resp->getControllerName() . 'Controller)';
        }
    }
}