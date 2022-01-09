<?php

namespace Framework;

use Config\Config;
use Controller\Controller;
use Framework\Exception\ViewNotFound;

/**
 * MVC Framework
 *
 * It works by extracting the controller and action names from the requested http route. After extraction, the correct
 * action will be executed and optional view rendering may happen. The Framework also injects dependencies into
 * controllers.
 */
final class Framework
{
    const HTTP_BAD_REQUEST = 400;
    const HTTP_UNAUTHORIZED = 401;
    const HTTP_NOT_FOUND = 404;
    const HTTP_METHOD_NOT_ALLOWED = 405;
    const HTTP_INTERNAL_SERVER_ERROR = 500;

    private Controller $_controller;
    private Context $_context;
    private string $_controllerName;
    private string $_actionName;

    public function __construct(Context $context)
    {
        // set number of decimals for all bcMath functions
        bcscale(25);

        $this->_context = $context;
    }

    /**
     * @return string The name of the currently executed controller
     */
    public function getControllerName(): string
    {
        return $this->_controllerName;
    }

    /**
     * Parses the to be executed controller and action from the http request. If the requested route does not exist,
     * a 404 page will be rendered immediately
     * @return bool true if the requested route was found
     */
    public function parseRequest(): bool
    {
        $requestUri = filter_input(INPUT_SERVER, 'REQUEST_URI', FILTER_SANITIZE_URL);

        $requestPath = str_replace($_SERVER['BASE'], '', $requestUri);

        if ($requestPath === '') {
            // if no path is given, redirect to index controller and index action
            $requestPath = 'index/';
        }

        $parts = explode('/', $requestPath);
        if ($parts === false || count($parts) !== 2) {
            $this->routeNotFound();
            return false;
        }

        $this->_controllerName = ucfirst(strtolower($parts[0]));

        $controllerClass = 'Controller\\' . $this->_controllerName . 'Controller';

        // remove query parameters from action part
        $parts[1] = explode('?', $parts[1], 2)[0];

        $parts[1] = ucfirst(strtolower($parts[1]));
        if (str_contains($parts[1], ".do")) {
            $parts[1] = str_replace(".do", "Do", $parts[1]);
        }
        $actionMethod = $parts[1] . 'Action';

        if (!class_exists($controllerClass)) {
            $this->routeNotFound();
            return false;
        }

        $controller = new $controllerClass($this->_context);
        if (!method_exists($controller, $actionMethod)) {
            $this->routeNotFound();
            return false;
        }

        $this->_controller = $controller;
        $this->_actionName = $actionMethod;

        return true;
    }

    /**
     * Execute the action for the current route (and implicitly render the view)
     * @param Response $response The response template
     */
    public function runAction(Response $response): void
    {
        $actionMethod = $this->_actionName;

        try {
            $this->_controller->$actionMethod($response);
        } catch (ViewNotFound $e) {
            // this is not perfect but better than printing an exception
            echo '<span style="color:red">No correct view defined</span>';
        }
    }

    /**
     * Helper method for rendering a nice 404 page view
     */
    private function routeNotFound(): void
    {
        http_response_code(self::HTTP_NOT_FOUND);

        $resp = new Response('index', Config::baseUrl);
        $resp->setHtmlTitle('Seite nicht gefunden');
        try {
            $resp->renderView('404');
        } catch (ViewNotFound $e) {
            echo '<span style="color:red">404 - Page not found</span>';
        }
    }

}