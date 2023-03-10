<?php

namespace Controller;

use Framework\Context;
use Framework\Exception\ViewNotFound;
use Framework\Framework;
use Framework\Response;
use Framework\Session;

/**
 * Base class for all controllers
 */
class Controller
{
    /**
     * @param Context $_context
     */
    public function __construct(
        protected Context $_context
    )
    {
    }

    /**
     * Default action for rendering index views. This will get called if no index action is defined in a child controller
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

    /**
     * Sends an http error code to the user if the HTTP method is not POST and aborts the request
     */
    protected function expectMethodPost(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(Framework::HTTP_METHOD_NOT_ALLOWED);
            echo 'Method not allowed';
            exit(0);
        }
    }

    /**
     * Sends an HTTP 401 code to the user if not logged in and redirects the user to the login page immediately
     */
    protected function abortIfUnauthorized(Response $resp): void
    {
        $user = Session::getAuthorizedUser();
        if ($user === null) {
            $resp->redirect($resp->getActionUrl('index', 'login') . '?require_login=1');
        }
    }
}