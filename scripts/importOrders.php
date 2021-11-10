<?php

use Config\Config;
use Core\Binance\CsvImport;
use Core\Repository\CoinRepository;
use Core\Repository\OrderRepository;
use Core\Repository\UserRepository;
use Framework\Database;
use Model\Transaction;

const APPLICATION_PATH = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'application' . DIRECTORY_SEPARATOR;

require APPLICATION_PATH . 'autoloader.php';

try {
    $database = new Database(Config::databaseHost, intval(Config::databasePort),
        Config::databaseDb, Config::databaseUsername, Config::databasePassword);
} catch (PDOException $e) {
    echo 'Failed to connect to database: ' . $e->getMessage();
    exit(1);
}

function cutPriceString(string $str): array
{
    $matches = [];
    preg_match('/^([0-9.]*)([A-Z]*)$/', $str, $matches);

    return [$matches[1], $matches[2]];
}

// *********************************************************************************************************************

if ($argc !== 3) {
    echo 'please provide a user id as first argument and a path to a CSV file as second argument';
    exit(1);
}

$userId = $argv[1];
$filepath = $argv[2];

$userRepo = new UserRepository($database->get());
$orderRepo = new OrderRepository($database->get());
$coinRepo = new CoinRepository($database->get());

$user = $userRepo->get($userId);
if ($user === null) {
    echo "User not found";
    exit(1);
}

$binanceCsv = new CsvImport($filepath);
if (!$binanceCsv->open()) {
    echo "Failed to open CSV file";
    exit(1);
}

$binanceCsv->skipHeader();

while (($data = $binanceCsv->getNextLine()) !== null) {
    list ($baseValue, $baseSymbol) = cutPriceString($data['executed']);
    list ($quoteValue, $quoteSymbol) = cutPriceString($data['amount']);
    list ($feeValue, $feeSymbol) = cutPriceString($data['fee']);
    $datetime = DateTime::createFromFormat('Y-m-d H:i:s', $data['date_str'], new DateTimeZone('UTC'));

    $baseCoin = $coinRepo->getBySymbol($baseSymbol);
    $quoteCoin = $coinRepo->getBySymbol($quoteSymbol);
    $feeCoin = $coinRepo->getBySymbol($feeSymbol);

    if ($baseCoin === null || $quoteCoin === null || $feeCoin === null) {
        echo "Coin not found";
        var_dump($data);
        exit(1);
    }

    $side = $data['side'];

    $feeTransaction = new Transaction($user->getId(), $datetime, Transaction::TYPE_SEND, $feeCoin->getId(), $feeValue);
    $baseTransaction = new Transaction($user->getId(), $datetime, Transaction::TYPE_SEND, $baseCoin->getId(), $baseValue);
    $quoteTransaction = new Transaction($user->getId(), $datetime, Transaction::TYPE_RECEIVE, $quoteCoin->getId(), $quoteValue);

    if ($side === 'BUY') {
        $baseTransaction = new Transaction($user->getId(), $datetime, Transaction::TYPE_SEND, $quoteCoin->getId(), $quoteValue);
        $quoteTransaction = new Transaction($user->getId(), $datetime, Transaction::TYPE_RECEIVE, $baseCoin->getId(), $baseValue);
    }

    if (!$orderRepo->makeAndInsert($baseTransaction, $quoteTransaction, $feeTransaction)) {
        echo "Error on insert";
        exit(1);
    }
}