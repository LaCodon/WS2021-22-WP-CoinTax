<?php

/**
 * This script imports a coin from coingecko into the local database and thus makes the coin available for
 * usage within CoinTax.
 */

use Config\Config;
use Core\Coingecko\CoingeckoAPI;
use Core\Repository\CoinRepository;
use Framework\Database;
use Framework\Exception\UniqueConstraintViolation;

const APPLICATION_PATH = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'application' . DIRECTORY_SEPARATOR;

require APPLICATION_PATH . 'autoloader.php';

try {
    $database = new Database(Config::databaseHost, intval(Config::databasePort),
        Config::databaseDb, Config::databaseUsername, Config::databasePassword);
} catch (PDOException $e) {
    echo 'Failed to connect to database: ' . $e->getMessage();
    exit(1);
}

// *********************************************************************************************************************

if (!isset($argv[1])) {
    echo 'please provide a coingecko id as first argument in order to import a coin';
    exit(1);
}

$coingecko = new CoingeckoAPI();
$coin = $coingecko->getCoin($argv[1]);
if ($coin === null) {
    echo 'coin not found on coingecko';
    exit(1);
}

$coinRepo = new CoinRepository($database->get());
try {
    if (!$coinRepo->insert($coin)) {
        echo 'error on insert';
        exit(1);
    }
} catch (UniqueConstraintViolation $e) {
    echo 'coin already in database';
    exit(1);
}

echo 'successfully inserted new coin';