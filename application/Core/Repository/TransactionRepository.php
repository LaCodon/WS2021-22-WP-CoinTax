<?php

namespace Core\Repository;

use DateTime;
use DateTimeZone;
use Exception;
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

    public function getThisYearByCoin(int $userId, int $coinId, int $year): array|null
    {
        $startTime = sprintf('%d-01-01 00:00:00', $year);
        $endTime = sprintf('%d-12-31 23:59:59', $year);

        try {
            $startTime = (new DateTime($startTime, new DateTimeZone('Europe/Berlin')))->setTimezone(new DateTimeZone('UTC'));
            $endTime = (new DateTime($endTime, new DateTimeZone('Europe/Berlin')))->setTimezone(new DateTimeZone('UTC'));
        } catch (Exception) {
            return null;
        }

        $startTime = $startTime->format('Y-m-d H:i:s');
        $endTime = $endTime->format('Y-m-d H:i:s');

        $stmt = $this->_pdo->prepare('SELECT * FROM transaction WHERE user_id = :userId AND datetime_utc BETWEEN :startTime AND :endTime AND coin_id = :coinId');
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':coinId', $coinId, PDO::PARAM_INT);
        $stmt->bindParam(':startTime', $startTime);
        $stmt->bindParam(':endTime', $endTime);

        if ($stmt->execute() === false) {
            return null;
        }

        $result = [];

        while (($obj = $stmt->fetchObject()) !== false) {
            $result[] = $this->makeTransaction($obj);
        }

        return $result;
    }

    /**
     * Returns all transactions of a given user with a given coin
     * @param int $userId
     * @param int $coinId
     * @return array|null
     */
    public function getByCoin(int $userId, int $coinId): array|null
    {
        $stmt = $this->_pdo->prepare('SELECT * FROM transaction WHERE user_id = :userId AND coin_id = :coinId');
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':coinId', $coinId, PDO::PARAM_INT);

        if ($stmt->execute() === false) {
            return null;
        }

        $result = [];

        while (($obj = $stmt->fetchObject()) !== false) {
            $result[] = $this->makeTransaction($obj);
        }

        return $result;
    }

    /**
     * @param Transaction $transaction
     * @return bool
     */
    public function update(Transaction $transaction): bool
    {
        $transactionId = $transaction->getId();
        $datetimeUtc = $transaction->getDatetimeUtc()->format('Y-m-d H:i:s');
        $coinId = $transaction->getCoinId();
        $coinValue = $transaction->getValue();

        $stmt = $this->_pdo->prepare('UPDATE `transaction` SET 
                                                datetime_utc = :datetime, 
                                                coin_id = :coinId, 
                                                coin_value = :value 
                                            WHERE transaction_id = :transactionId LIMIT 1');
        $stmt->bindParam(':transactionId', $transactionId, PDO::PARAM_INT);
        $stmt->bindParam(':datetime', $datetimeUtc,);
        $stmt->bindParam(':coinId', $coinId, PDO::PARAM_INT);
        $stmt->bindParam(':value', $coinValue);

        return $stmt->execute();
    }

    /**
     * @param int $transactionId
     * @param int $userId
     * @return bool
     */
    public function delete(int $transactionId, int $userId): bool
    {
        $stmt = $this->_pdo->prepare('DELETE FROM transaction WHERE transaction_id = :transId AND user_id = :userId');
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':transId', $transactionId, PDO::PARAM_INT);

        $res = $stmt->execute();

        if ($stmt->rowCount() !== 1 || $res == false) {
            return false;
        }

        return true;
    }

    /**
     * @param object|bool $resultObj
     * @return Transaction|null
     */
    public function makeTransaction(object|bool $resultObj): Transaction|null
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