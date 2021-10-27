<?php

use Framework\Framework;
use Framework\Response;

const APPLICATION_PATH = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'application' . DIRECTORY_SEPARATOR;

require APPLICATION_PATH . "globalFunctions.php";
require APPLICATION_PATH . "autoloader.php";

$framework = new Framework();

if (!$framework->parseRequest()) {
    // abort request if parsing failed
    return;
}

$response = new Response($framework->getControllerName());

$framework->runAction($response);
