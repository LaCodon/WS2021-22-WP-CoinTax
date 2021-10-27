<?php

namespace Framework;

use Controller\Controller;

final class Framework
{
    const HTTP_NOT_FOUND = 404;

    private Controller $_controller;
    private string $_controllerName;
    private string $_actionName;

    public function getControllerName(): string
    {
        return $this->_controllerName;
    }

    public function parseRequest(): bool
    {
        $requestUri = filter_input(INPUT_SERVER, 'REQUEST_URI', FILTER_SANITIZE_URL);

        $requestPath = str_replace($_SERVER['BASE'], '', $requestUri);

        $parts = explode('/', $requestPath);
        if ($parts === false || count($parts) !== 2) {
            $this->routeNotFound();
            return false;
        }

        $this->_controllerName = ucfirst(strtolower($parts[0]));

        $controllerClass = 'Controller\\' . $this->_controllerName . 'Controller';
        $actionMethod = ucfirst(strtolower($parts[1])) . 'Action';

        $controller = new $controllerClass();
        if (!method_exists($controller, $actionMethod)) {
            var_dump($actionMethod);

            $this->routeNotFound();
            return false;
        }

        $this->_controller = $controller;
        $this->_actionName = $actionMethod;

        return true;
    }

    public function runAction(Response $response): void
    {
        $actionMethod = $this->_actionName;
        $this->_controller->$actionMethod($response);
    }

    private function routeNotFound(): void
    {
        http_response_code(self::HTTP_NOT_FOUND);

        echo '404 - Page not found';
    }

}