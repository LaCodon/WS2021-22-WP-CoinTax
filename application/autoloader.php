<?php

spl_autoload_register(function ($class) {
    $classPath = $class;

    if (strpos($class, '\\') !== false) {
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