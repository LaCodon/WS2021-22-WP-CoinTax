<?php

/**
 * Autoload PHP files if a class is used but not yet required
 * This autoloader works by splitting a class into its name and its namespace. The namespace is then used
 * as file path and the class name as file name.
 */
spl_autoload_register(function ($class) {
    $classPath = $class;

    if (str_contains($class, '\\')) {
        $classPath = '';

        $split = explode('/', str_replace('\\', '/', $class));
        foreach ($split as $part) {
            $classPath .= DIRECTORY_SEPARATOR . $part;
        }
    }

    $path = APPLICATION_PATH . $classPath . '.php';

    if (is_readable($path)) {
        require $path;
        return true;
    }

    return false;
});