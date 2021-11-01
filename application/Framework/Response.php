<?php

namespace Framework;

use Framework\Exception\ViewNotFound;

final class Response
{
    public string $htmlTitle = '';

    private string $_controllerName;
    private string $_baseUrl;

    public function __construct(string $controllerName, string $baseUrl)
    {
        $this->_controllerName = $controllerName;
        $this->_baseUrl = $baseUrl;
    }

    /**
     * Renders the given viewName.php from the current controllers view path (View/${ControllerName}/${viewName}.php)
     * @throws ViewNotFound
     */
    public function renderView(string $viewName): void
    {
        $path = APPLICATION_PATH . 'View' . DIRECTORY_SEPARATOR . $this->_controllerName . DIRECTORY_SEPARATOR . $viewName . '.php';
        if (!file_exists($path)) {
            throw new ViewNotFound($viewName);
        }

        require APPLICATION_PATH . 'View/Base/header.php';
        require $path;
        require APPLICATION_PATH . 'View/Base/footer.php';

        // only keep input validation results for one page reload
        Session::clearInputValidationResult();
    }

    /**
     * Allows to set any public properties for Response objects. This is used to pass variables from the controller
     * to the view.
     * @param $name
     * @param $value
     */
    public function __set($name, $value)
    {
        $this->$name = $value;
    }

    /**
     * Set a variable which then can be used in a view with $this->$name
     * @param $name
     * @param $value
     */
    public function setViewVar($name, $value)
    {
        $this->$name = $value;
    }

    /**
     * Returns the controllers name for the current request
     * @return string
     */
    public function getControllerName(): string
    {
        return $this->_controllerName;
    }

    /**
     * Set the value between the HTML <title> tag
     * @param string $title
     */
    public function setHtmlTitle(string $title)
    {
        $this->htmlTitle = $title;
    }

    /**
     * Get the URL for a form POST endpoint
     * @param string $action
     * @return string
     */
    public function getDoActionUrl(string $action): string
    {
        return strtolower($this->_baseUrl . $this->getControllerName()) . "/$action.do";
    }

    /**
     * Get the URL for a GET endpoint
     * @param string $action
     * @param string $controller
     * @return string
     */
    public function getActionUrl(string $action, string $controller = ''): string
    {
        if ($controller === '') {
            $controller = $this->getControllerName();
        }
        if ($action === "index") {
            $action = "";
        }
        return strtolower($this->_baseUrl . $controller) . "/$action";
    }

    /**
     * Redirect the user to the given url, optionally append query parameters.
     * This aborts further code execution
     * @param string $target
     * @param array $params
     */
    public function redirect(string $target, array $params = array()): void
    {
        if (count($params) !== 0) {
            $target .= '?';
        }

        foreach ($params as $name => $value) {
            $target .= $name . '=' . urlencode($value) . '&';
        }

        header('Location: ' . $target);
        exit(0);
    }

    /**
     * Returns true if current user is logged in
     * @return bool
     */
    public function isAuthorized(): bool
    {
        $user = Session::getAuthorizedUser();
        if ($user === null) {
            return false;
        }

        return true;
    }
}