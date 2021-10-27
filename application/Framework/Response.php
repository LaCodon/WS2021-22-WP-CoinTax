<?php

namespace Framework;

use Framework\Exception\ViewNotFound;

final class Response
{
    public string $htmlTitle = '';

    private string $_controllerName;

    public function __construct(string $controllerName)
    {
        $this->_controllerName = $controllerName;
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
}