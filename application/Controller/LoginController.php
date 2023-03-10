<?php

namespace Controller;

use Framework\Exception\ViewNotFound;
use Framework\Response;
use Framework\Session;
use Framework\Validation\Input;
use Framework\Validation\InputValidator;

/**
 * Controller for /login
 */
final class LoginController extends Controller
{

    /**
     * Endpoint for GET /login/
     * @throws ViewNotFound
     */
    public function Action(Response $resp): void
    {
        if ($resp->isAuthorized()) {
            $resp->redirect($resp->getActionUrl('index', 'dashboard'));
        }

        $resp->setViewVar('require_login', isset($_GET['require_login']));

        $resp->setHtmlTitle('Login');
        $resp->renderView("index");
    }

    /**
     * Endpoint for POST /login/login.do
     * @param Response $resp
     */
    public function LoginDoAction(Response $resp): void
    {
        $this->expectMethodPost();

        $inputValidationResult = InputValidator::parseAndValidate([
            new Input(INPUT_POST, 'email', "E-Mail", _filter: FILTER_VALIDATE_EMAIL),
            new Input(INPUT_POST, 'password', "Passwort"),
        ]);

        if ($inputValidationResult->hasErrors()) {
            Session::setInputValidationResult($inputValidationResult);
            $resp->redirect($resp->getActionUrl('index'));
        }

        $userRepo = $this->_context->getUserRepo();
        $user = $userRepo->getByEmail($inputValidationResult->getValue('email'));

        if ($user === null
            || password_verify($inputValidationResult->getValue('password'), $user->getPasswordHash()) !== true) {
            $inputValidationResult->setError("email", "Login fehlgeschlagen");
            Session::setInputValidationResult($inputValidationResult);
            $resp->redirect($resp->getActionUrl('index'));
        }

        Session::regenerateId(true);
        Session::setAuthorizedUser($user);

        $resp->redirect($resp->getActionUrl('index', 'dashboard'));
    }

    /**
     * Endpoint for GET /login/logout
     * @param Response $resp
     */
    public function LogoutAction(Response $resp): void
    {
        Session::destroySession();
        Session::regenerateId(true);

        $resp->redirect($resp->getActionUrl('index'));
    }

}