<?php
declare(strict_types=1);

const APPLICATION_DEBUG = false;

use Config\Config;
use Framework\Context;
use Framework\Database;
use Framework\Exception\SessionsStartFailed;
use Framework\Framework;
use Framework\Response;
use Framework\Session;


const APPLICATION_PATH = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'application' . DIRECTORY_SEPARATOR;

require APPLICATION_PATH . 'globalFunctions.php';
require APPLICATION_PATH . 'autoloader.php';

try {
    $database = new Database(Config::databaseHost, intval(Config::databasePort),
        Config::databaseDb, Config::databaseUsername, Config::databasePassword);
} catch (PDOException $e) {
    http_response_code(Framework::HTTP_INTERNAL_SERVER_ERROR);
    echo 'Internal Server Error (Database)';
    exit(0);
}

try {
    Session::start();
} catch (SessionsStartFailed $e) {
    http_response_code(Framework::HTTP_INTERNAL_SERVER_ERROR);
    echo 'Internal Server Error (Session)';
    exit(0);
}

$framework = new Framework(new Context($database));

if (!$framework->parseRequest()) {
    // abort request if parsing failed
    return;
}

$response = new Response($framework->getControllerName(), Config::baseUrl);

$framework->runAction($response);
