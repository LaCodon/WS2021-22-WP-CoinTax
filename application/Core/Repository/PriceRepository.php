<?php

namespace Core\Repository;

use Core\Calc\PriceConverter;
use Core\Coingecko\CoingeckoAPI;
use DateTime;
use DateTimeZone;
use Model\Coin;
use Model\Transaction;
use PDO;

final class PriceRepository
{
    public function __construct(
        private PDO $_pdo,
    )
    {
    }

    /**
     * Get price date from database or coingecko API
     * @param Coin $coin
     * @param DateTime $datetime
     * @return string|null
     */
    public function get(Coin $coin, DateTime $datetime): string|null
    {
        $datetime = $datetime->setTimezone(new DateTimeZone('UTC'));
        $dateStr = $datetime->format('Y-m-d');
        $coinId = $coin->getId();

        $stmt = $this->_pdo->prepare('SELECT coin_value_id, eur_value, datetime_utc, coin_id FROM coin_value WHERE datetime_utc = :datetime AND coin_id = :coinId LIMIT 1');
        $stmt->bindParam(':datetime', $dateStr);
        $stmt->bindParam(':coinId', $coinId);

        if (!$stmt->execute()) {
            return null;
        }

        if ($stmt->rowCount() === 0) {
            // first check cache if enabled
            if (function_exists('apcu_cache_info')) {
                $success = false;
                $price = apcu_fetch(sprintf('%d-%s', $coinId, $dateStr), $success);
                if ($success) {
                    return $price;
                }
            }

            $api = new CoingeckoAPI();
            $price = $api->getPriceData($coin, $datetime);
            if ($price === null) {
                return null;
            }

            // only insert price date if its not from today because price may change over the day
            $now = (new DateTime('now', new DateTimeZone('UTC')))->setTime(0, 0);
            $then = (clone $datetime)->setTimezone(new DateTimeZone('UTC'))->setTime(0, 0);
            if ((int)$now->diff($then)->format('%d') !== 0) {
                $this->insert($coin, $price, $datetime);
            } else {
                // cache price data if apcu is enabled
                if (function_exists('apcu_cache_info')) {
                    apcu_add(sprintf('%d-%s', $coinId, $dateStr), $price, 60 * 5);
                }
            }

            return $price;
        }

        $obj = $stmt->fetchObject();
        return $obj->eur_value;
    }

    /**
     * Get the EUR value of the given transaction from a corresponding transaction in the same order if available
     * @param Transaction $transaction
     * @return string|null
     */
    public function getTransactionEurValueFromOrder(Transaction $transaction): string|null
    {
        $transactionId = $transaction->getId();
        $eurSymbol = PriceConverter::EUR_COIN_SYMBOL;

        $stmt = $this->_pdo->prepare('SELECT otherT.coin_value FROM `transaction` t
                                                JOIN `order` o ON o.base_transaction = t.transaction_id OR o.quote_transaction = t.transaction_id
                                                JOIN `transaction` otherT ON 
                                                    (otherT.transaction_id = o.base_transaction AND otherT.transaction_id != t.transaction_id) 
                                                    OR (otherT.transaction_id = o.quote_transaction AND otherT.transaction_id != t.transaction_id)
                                               JOIN `coin` c ON otherT.coin_id = c.coin_id
                                            WHERE t.transaction_id = :txId AND c.symbol = :eurSymbol');
        $stmt->bindParam(':txId', $transactionId, PDO::PARAM_INT);
        $stmt->bindParam(':eurSymbol', $eurSymbol);

        if ($stmt->execute() === false || $stmt->rowCount() !== 1) {
            return null;
        }

        return $stmt->fetchObject()->coin_value;
    }

    /**
     * @param Coin $coin
     * @param string $price
     * @param DateTime $datetime
     * @return bool
     */
    private function insert(Coin $coin, string $price, DateTime $datetime): bool
    {
        $coinId = $coin->getId();
        $datetime = $datetime->setTimezone(new DateTimeZone('UTC'))->format('Y-m-d');

        $stmt = $this->_pdo->prepare('INSERT INTO coin_value (eur_value, datetime_utc, coin_id) VALUES (:value, :date, :id)');
        $stmt->bindParam(':value', $price);
        $stmt->bindParam(':date', $datetime);
        $stmt->bindParam(':id', $coinId, PDO::PARAM_INT);

        return $stmt->execute();
    }
}