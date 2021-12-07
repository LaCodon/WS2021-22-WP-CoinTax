<?php

namespace Controller;

use Framework\Exception\ViewNotFound;
use Framework\Framework;
use Framework\Response;
use Framework\Session;
use Framework\Validation\Input;
use Framework\Validation\InputValidator;

final class ApiController extends Controller
{
    public function QuerycoinsAction(Response $resp): void
    {
        $this->abortIfNotLoggedIn();

        $input = InputValidator::parseAndValidate([
            new Input(INPUT_GET, 'query', 'Query', true)
        ]);

        if (preg_match('/^[A-Z]+$/', $input->getValue('query')) !== 1) {
            $input->setError('query', 'only uppercase letters allowed in param query');
        }

        if ($input->hasErrors()) {
            $this->abortWithError($input->getError('query'));
        }

        $coinRepo = $this->_context->getCoinRepo();
        $coins = $coinRepo->getByQuery($input->getValue('query'));

        $coinOptions = [];

        foreach ($coins as $coin) {
            $coinOptions[$coin->getSymbol()] = [
                'name' => $coin->getName(),
                'thumbnail' => $coin->getThumbnailUrl(),
            ];
        }

        header('Content-Type: application/json');
        echo json_encode($coinOptions);
    }

    private function abortIfNotLoggedIn(): void
    {
        $user = Session::getAuthorizedUser();
        if ($user === null) {
            http_response_code(Framework::HTTP_UNAUTHORIZED);
            echo 'Unauthorized';
            exit(0);
        }
    }

    private function abortWithError(string $error): void
    {
        http_response_code(Framework::HTTP_BAD_REQUEST);
        echo $error;
        exit(0);
    }

}