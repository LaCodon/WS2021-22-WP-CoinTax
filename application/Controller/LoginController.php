<?php

namespace Controller;

use Core\Repository\UserRepository;
use Framework\Exception\ViewNotFound;
use Framework\Framework;
use Framework\Response;
use Framework\Session;
use Framework\Validation\Input;
use Framework\Validation\InputValidator;

final class LoginController extends Controller
{

    /**
     * @throws ViewNotFound
     */
    public function Action(Response $resp): void
    {
        if ($resp->isAuthorized()) {
            $resp->redirect($resp->getActionUrl('index', 'dashboard'));
        }

        $resp->setHtmlTitle('Login');
        $resp->renderView("index");
    }

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

        $userRepo = new UserRepository($this->db());
        $user = $userRepo->getByEmail($inputValidationResult->getValue('email'));

        if ($user === null
            || password_verify($inputValidationResult->getValue('password'), $user->getPasswordHash()) !== true) {
            $inputValidationResult->setError("email", "Login fehlgeschlagen");
            Session::setInputValidationResult($inputValidationResult);
            $resp->redirect($resp->getActionUrl('index'));
        }

        Session::setAuthorizedUser($user);

        $resp->redirect($resp->getActionUrl('index', 'dashboard'));
    }

    public function LogoutAction(Response $resp): void
    {
        Session::destroySession();
        Session::regenerateId(true);

        $resp->redirect($resp->getActionUrl('index'));
    }

}