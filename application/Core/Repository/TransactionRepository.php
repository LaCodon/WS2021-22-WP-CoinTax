<?php

namespace Core\Repository;

use DateTime;
use DateTimeZone;
use Framework\Exception\IdOverrideDisallowed;
use Framework\Exception\UniqueConstraintViolation;
use Model\Transaction;
use \PDO;
use Model\User;
use PDOException;

final class TransactionRepository
{
    public function __construct(
        private PDO $_pdo,
    )
    {
    }

    /**
     * @throws IdOverrideDisallowed
     */
    public function insert(Transaction $transaction): bool
    {
        if ($transaction->getId() !== -1) {
            // transaction is already in database
            return false;
        }

        $userId = $transaction->getUserId();
        $datetimeUtc = $transaction->getDatetimeUtc()->format('Y-m-d H:i:s');
        $type = $transaction->getType();
        $coinId = $transaction->getCoinId();
        $value = $transaction->getValue();

        $stmt = $this->_pdo->prepare('INSERT INTO transaction (user_id, datetime_utc, type, coin_id, coin_value) VALUES (:userId, :datetime, :type, :coinId, :value)');
        $stmt->bindParam(":userId", $userId, PDO::PARAM_INT);
        $stmt->bindParam(":datetime", $datetimeUtc);
        $stmt->bindParam(":type", $type);
        $stmt->bindParam(":coinId", $coinId, PDO::PARAM_INT);
        $stmt->bindParam(":value", $value);

        $res = $stmt->execute();

        $transaction->setId($this->_pdo->lastInsertId());

        return $res;
    }

    /**
     * @param int|null $id
     * @return Transaction|null
     */
    public function get(int|null $id): Transaction|null
    {
        if ($id === null) {
            return null;
        }

        $stmt = $this->_pdo->prepare('SELECT transaction_id, user_id, datetime_utc, type, coin_id, coin_value FROM transaction WHERE transaction_id = :id LIMIT 1');
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        if ($stmt->execute() === false) {
            return null;
        }

        return $this->makeTransaction($stmt->fetchObject());
    }

    /**
     * @param object|bool $resultObj
     * @return Transaction|null
     */
    private function makeTransaction(object|bool $resultObj): Transaction|null
    {
        if ($resultObj === false) {
            return null;
        }

        $datetime = DateTime::createFromFormat('Y-m-d H:i:s', $resultObj->datetime_utc, new DateTimeZone('UTC'));

        return new Transaction(
            $resultObj->user_id,
            $datetime,
            $resultObj->type,
            $resultObj->coin_id,
            $resultObj->coin_value,
            $resultObj->transaction_id,
        );
    }

}