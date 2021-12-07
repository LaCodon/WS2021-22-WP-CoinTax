<?php

declare(strict_types=1);
const APPLICATION_DEBUG = false;

use Config\Config;
use Core\Repository\CoinRepository;
use Core\Repository\OrderRepository;
use Core\Repository\PaymentInfoRepository;
use Core\Repository\PriceRepository;
use Core\Repository\TransactionRepository;
use Core\Repository\UserRepository;
use Framework\Context;
use Framework\Database;
use Framework\Exception\SessionsStartFailed;
use Framework\Framework;
use Framework\Response;
use Framework\Session;

const APPLICATION_PATH = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'application' . DIRECTORY_SEPARATOR;

require APPLICATION_PATH . 'globalFunctions.php';
require APPLICATION_PATH . 'autoloader.php';

if (APPLICATION_DEBUG === true) {
    require APPLICATION_PATH . 'tests.php';
}

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

$context = new Context(
    new CoinRepository($database->get()),
    new OrderRepository($database->get()),
    new PaymentInfoRepository($database->get()),
    new PriceRepository($database->get()),
    new TransactionRepository($database->get()),
    new UserRepository($database->get()),
);

$framework = new Framework($context);

if (!$framework->parseRequest()) {
    // abort request if parsing failed
    return;
}

$response = new Response($framework->getControllerName(), Config::baseUrl);

$framework->runAction($response);
