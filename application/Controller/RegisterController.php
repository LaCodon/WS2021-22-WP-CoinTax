<?php

namespace Controller;

use Framework\Exception\UniqueConstraintViolation;
use Framework\Exception\ViewNotFound;
use Framework\Response;
use Framework\Session;
use Framework\Validation\Input;
use Framework\Validation\InputValidator;
use Model\User;

/**
 * Controller for /register
 */
final class RegisterController extends Controller
{
    const RESULT_SUCCESS = 0;
    const RESULT_EMAIL_TAKEN = 1;
    const RESULT_INVALID_INPUT = 2;
    const RESULT_UNKNOWN_ERROR = 3;

    /**
     * Endpoint for GET /register/
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

    /**
     * Endpoint for POST /register/register.do
     * @param Response $resp
     * @param bool $redirect
     * @return int status code indicating the success or failure of the registration (see RESULT_* constants of this class)
     * The status code gets used by the ApiController
     */
    public function RegisterDoAction(Response $resp, bool $redirect = true): int
    {
        // $redirect is false if called from the ApiController

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
            if ($redirect === true) {
                $resp->redirect($resp->getActionUrl('index'));
            } else {
                return self::RESULT_INVALID_INPUT;
            }
        }

        $user = new User(
            htmlspecialchars(trim($inputValidationResult->getValue("firstname"))),
            htmlspecialchars(trim($inputValidationResult->getValue("lastname"))),
            htmlspecialchars($inputValidationResult->getValue("email")),
            password_hash($inputValidationResult->getValue("password"), PASSWORD_DEFAULT));

        $userRepo = $this->_context->getUserRepo();

        try {
            if ($userRepo->insert($user) !== true) {
                $inputValidationResult->setError('firstname', 'Unbekannter Fehler beim Registrieren');
                Session::setInputValidationResult($inputValidationResult);
                if ($redirect === true) {
                    $resp->redirect($resp->getActionUrl('index'));
                } else {
                    return self::RESULT_UNKNOWN_ERROR;
                }
            }
        } catch (UniqueConstraintViolation $e) {
            $inputValidationResult->setError('email', 'Es existiert bereits ein Account mit dieser E-Mail');
            Session::setInputValidationResult($inputValidationResult);
            if ($redirect === true) {
                $resp->redirect($resp->getActionUrl('index'));
            } else {
                return self::RESULT_EMAIL_TAKEN;
            }
        }

        if ($redirect === true) {
            $resp->redirect($resp->getActionUrl('index', 'login'));
        }

        return self::RESULT_SUCCESS;
    }

}