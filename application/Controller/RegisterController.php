<?php

namespace Controller;

use Core\Repository\UserRepository;
use Framework\Exception\UniqueConstraintViolation;
use Framework\Exception\ViewNotFound;
use Framework\Response;
use Framework\Session;
use Framework\Validation\Input;
use Framework\Validation\InputValidator;
use Model\User;

final class RegisterController extends Controller
{

    /**
     * @throws ViewNotFound
     */
    public function Action(Response $resp): void
    {
        if ($resp->isAuthorized()) {
            $resp->redirect($resp->getActionUrl('index', 'dashboard'));
        }

        $resp->setHtmlTitle('Registrieren');
        $resp->renderView("index");
    }

    public function RegisterDoAction(Response $resp): void
    {
        $this->expectMethodPost();

        $inputValidationResult = InputValidator::parseAndValidate([
            new Input(INPUT_POST, 'firstname', 'Vorname'),
            new Input(INPUT_POST, 'lastname', 'Nachname'),
            new Input(INPUT_POST, 'email', "E-Mail", _filter: FILTER_VALIDATE_EMAIL),
            new Input(INPUT_POST, 'password', "Passwort"),
            new Input(INPUT_POST, 'password-repeat', 'Passwort wiederholen')
        ]);

        if (preg_match('/^(?=.*?[a-zA-Z])(?=.*?[0-9])[\s\S]{5,}$/', $inputValidationResult->getValue('password')) === 0) {
            // password is invalid, tell rules to the user
            $inputValidationResult->setError('password', 'Das Passwort muss mindestens fünf Zeichen lang sein und einen Buchstaben und eine Zahl enthalten');
        }

        // if password is valid, password-repeat must be the same
        if ($inputValidationResult->getError('password') === ''
            && $inputValidationResult->getValue('password') !== $inputValidationResult->getValue('password-repeat')) {
            $inputValidationResult->setError('password-repeat', 'Die Passwörter müssen identisch sein');
        }

        if ($inputValidationResult->hasErrors()) {
            Session::setInputValidationResult($inputValidationResult);
            $resp->redirect($resp->getActionUrl('index'));
        }

        $user = new User(
            trim($inputValidationResult->getValue("firstname")),
            trim($inputValidationResult->getValue("lastname")),
            $inputValidationResult->getValue("email"),
            password_hash($inputValidationResult->getValue("password"), PASSWORD_DEFAULT));

        $userRepo = new UserRepository($this->db());

        try {
            if ($userRepo->insert($user) !== true) {
                $inputValidationResult->setError('firstname', 'Unbekannter Fehler beim Registrieren');
                Session::setInputValidationResult($inputValidationResult);
                $resp->redirect($resp->getActionUrl('index'));
            }
        } catch (UniqueConstraintViolation $e) {
            $inputValidationResult->setError('email', 'Es existiert bereits ein Account mit dieser E-Mail');
            Session::setInputValidationResult($inputValidationResult);
            $resp->redirect($resp->getActionUrl('index'));
        }

        $resp->redirect($resp->getActionUrl('index', 'login'));
    }

}