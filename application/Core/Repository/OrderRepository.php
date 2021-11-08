<?php

namespace Core\Repository;

use Framework\Exception\IdOverrideDisallowed;
use Model\Order;
use Model\Transaction;
use \PDO;

final class OrderRepository
{
    public function __construct(
        private PDO $_pdo,
    )
    {
    }

    /**
     * @throws IdOverrideDisallowed
     */
    public function makeAndInsert(Transaction $baseTransaction, Transaction $quoteTransaction, Transaction|null $feeTransaction): Order|null
    {
        if ($this->_pdo->beginTransaction() !== true) {
            return null;
        }

        $transactionRepo = new TransactionRepository($this->_pdo);

        if ($transactionRepo->insert($baseTransaction) === false) {
            $this->_pdo->rollBack();
            return null;
        }

        if ($transactionRepo->insert($quoteTransaction) === false) {
            $this->_pdo->rollBack();
            return null;
        }
        if ($feeTransaction !== null && $transactionRepo->insert($feeTransaction) === false) {
            $this->_pdo->rollBack();
            return null;
        }

        $baseId = $baseTransaction->getId();
        $quoteId = $quoteTransaction->getId();
        $feeId = $feeTransaction?->getId();

        $stmt = $this->_pdo->prepare('INSERT INTO `order` (base_transaction, quote_transaction, fee_transaction) VALUES (:base, :quote, :fee)');
        $stmt->bindParam(":base", $baseId, PDO::PARAM_INT);
        $stmt->bindParam(":quote", $quoteId, PDO::PARAM_INT);
        if ($feeId === null)
            $stmt->bindParam(":fee", $feeId, PDO::PARAM_NULL);
        else
            $stmt->bindParam(":fee", $feeId, PDO::PARAM_INT);

        $res = $stmt->execute();
        if ($res !== true) {
            $this->_pdo->rollBack();
            return null;
        }

        if ($this->_pdo->commit() !== true) {
            return null;
        }

        return new Order($baseId, $quoteId, $feeId, $this->_pdo->lastInsertId());
    }

    /**
     * @param int $userId
     * @return array
     */
    public function getAllByUserId(int $userId): array
    {
        $stmt = $this->_pdo->prepare('SELECT order_id, base_transaction, quote_transaction, fee_transaction FROM `order` AS o
                                                JOIN `transaction` AS t ON o.base_transaction = t.transaction_id
                                            WHERE t.user_id = :userId
                                            ORDER BY t.datetime_utc DESC');
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);

        if ($stmt->execute() === false) {
            return [];
        }

        $result = [];

        while (($obj = $stmt->fetchObject()) !== false) {
            $result[] = $this->makeOrder($obj);
        }

        return $result;
    }

    /**
     * @param object|bool $resultObj
     * @return Order|null
     */
    private function makeOrder(object|bool $resultObj): Order|null
    {
        if ($resultObj === false) {
            return null;
        }

        return new Order(
            $resultObj->base_transaction,
            $resultObj->quote_transaction,
            $resultObj->fee_transaction,
            $resultObj->order_id,
        );
    }

}