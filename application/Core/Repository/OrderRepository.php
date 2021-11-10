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
     * @throws IdOverrideDisallowed
     */
    public function updateComplete(int $orderId, int $userId, Transaction $baseTransaction, Transaction $quoteTransaction, Transaction|null $feeTransaction): Order|null
    {
        $order = $this->get($orderId);
        if ($order === null) {
            return null;
        }

        $baseTransaction->setId($order->getBaseTransactionId());
        $quoteTransaction->setId($order->getQuoteTransactionId());
        $feeTransaction?->setId($order->getFeeTransactionId());

        if ($this->_pdo->beginTransaction() !== true) {
            return null;
        }

        $transactionRepo = new TransactionRepository($this->_pdo);

        if ($feeTransaction === null && $order->getFeeTransactionId() !== null) {
            // fee was removed
            if (!$transactionRepo->delete($order->getFeeTransactionId(), $userId)) {
                $this->_pdo->rollBack();
                return null;
            }

            $order->setFeeTransactionId(null);
        } elseif ($feeTransaction !== null && $order->getFeeTransactionId() === null) {
            // fee was added
            if (!$transactionRepo->insert($feeTransaction)) {
                $this->_pdo->rollBack();
                return null;
            }

            $order->setFeeTransactionId($feeTransaction->getId());
        }

        if (!$this->update($order)) {
            $this->_pdo->rollBack();
            return null;
        }

        if (!$transactionRepo->update($baseTransaction)) {
            $this->_pdo->rollBack();
            return null;
        }

        if (!$transactionRepo->update($quoteTransaction)) {
            $this->_pdo->rollBack();
            return null;
        }

        if ($feeTransaction !== null && !$transactionRepo->update($feeTransaction)) {
            $this->_pdo->rollBack();
            return null;
        }

        if (!$this->_pdo->commit()) {
            return null;
        }

        return $order;
    }

    /**
     * @param Order $order
     * @return bool
     */
    public function update(Order $order): bool
    {
        $orderId = $order->getId();
        $baseId = $order->getBaseTransactionId();
        $quoteId = $order->getQuoteTransactionId();
        $feeId = $order->getFeeTransactionId();

        $stmt = $this->_pdo->prepare('UPDATE `order` SET 
                                                base_transaction = :baseId, 
                                                quote_transaction = :quoteId, 
                                                fee_transaction = :feeId 
                                            WHERE order_id = :orderId LIMIT 1');
        $stmt->bindParam(':baseId', $baseId, PDO::PARAM_INT);
        $stmt->bindParam(':quoteId', $quoteId, PDO::PARAM_INT);
        $stmt->bindParam(':orderId', $orderId, PDO::PARAM_INT);
        if ($feeId === null) {
            $stmt->bindParam(':feeId', $feeId, PDO::PARAM_NULL);
        } else {
            $stmt->bindParam(':feeId', $feeId, PDO::PARAM_INT);
        }

        return $stmt->execute();
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
     * @param int $id
     * @return Order|null
     */
    public function get(int $id): Order|null
    {
        $stmt = $this->_pdo->prepare('SELECT order_id, base_transaction, quote_transaction, fee_transaction FROM `order` WHERE order_id = :id LIMIT 1');
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        if ($stmt->execute() === false) {
            return null;
        }

        return $this->makeOrder($stmt->fetchObject());
    }

    /**
     * Get an array with all relevant order components (transactions, coins, ...)
     * @param int $id
     * @return array|null
     */
    public function getComplete(int $id): array|null
    {
        $stmt = $this->_pdo->prepare('SELECT t.*, c.* FROM `order` AS o
                                                JOIN `transaction` AS t ON o.base_transaction = t.transaction_id
                                                JOIN `coin` AS c ON c.coin_id = t.coin_id
                                            WHERE o.order_id = :orderId
                                            UNION
                                            SELECT t.*, c.* FROM `order` AS o
                                                JOIN `transaction` AS t ON o.quote_transaction = t.transaction_id
                                                JOIN `coin` AS c ON c.coin_id = t.coin_id
                                            WHERE o.order_id = :orderId
                                            UNION
                                            SELECT t.*, c.* FROM `order` AS o
                                                JOIN `transaction` AS t ON o.fee_transaction = t.transaction_id
                                                JOIN `coin` AS c ON c.coin_id = t.coin_id
                                            WHERE o.order_id = :orderId');
        $stmt->bindParam(':orderId', $id, PDO::PARAM_INT);

        if (!$stmt->execute()) {
            return null;
        }

        $transactionRepo = new TransactionRepository($this->_pdo);
        $coinRepo = new CoinRepository($this->_pdo);

        $result = [
            0 => null,
            1 => null,
            2 => null,
        ];

        $count = 0;
        while (($obj = $stmt->fetchObject()) !== false) {
            $result[$count] = [
                'tx' => $transactionRepo->makeTransaction($obj),
                'coin' => $coinRepo->makeCoin($obj),
            ];
            ++$count;
        }

        return [
            'base' => $result[0],
            'quote' => $result[1],
            'fee' => $result[2],
        ];
    }

    /**
     * Deletes the order and all child transactions
     * @param int $orderId
     * @param int $userId
     * @return bool
     */
    public function delete(int $orderId, int $userId): bool
    {
        $order = $this->get($orderId);
        if ($order === null) {
            return false;
        }

        $transactionRepo = new TransactionRepository($this->_pdo);

        $base = $transactionRepo->get($order->getBaseTransactionId());
        if ($base === null) {
            return false;
        }

        $quote = $transactionRepo->get($order->getQuoteTransactionId());
        if ($quote === null) {
            return false;
        }

        $fee = $transactionRepo->get($order->getFeeTransactionId());

        $this->_pdo->beginTransaction();

        if (!$transactionRepo->delete($base->getId(), $userId)) {
            $this->_pdo->rollBack();
            return false;
        }

        if (!$transactionRepo->delete($quote->getId(), $userId)) {
            $this->_pdo->rollBack();
            return false;
        }

        if ($fee !== null) {
            if (!$transactionRepo->delete($fee->getId(), $userId)) {
                $this->_pdo->rollBack();
                return false;
            }
        }

        $stmt = $this->_pdo->prepare('DELETE FROM `order` WHERE order_id = :orderId LIMIT 1');
        $stmt->bindParam(':orderId', $orderId, PDO::PARAM_INT);

        if (!$stmt->execute()) {
            $this->_pdo->rollBack();
            return false;
        }

        return $this->_pdo->commit();
    }

    /**
     * Returns true, if the given order is owned by the given user
     * @param int $orderId
     * @param int $userId
     * @return bool
     */
    public function isOwnedByUser(int $orderId, int $userId): bool
    {
        $stmt = $this->_pdo->prepare('SELECT order_id FROM `order` AS o
                                                JOIN `transaction` AS t ON o.base_transaction = t.transaction_id
                                            WHERE t.user_id = :userId AND o.order_id = :orderId LIMIT 1');
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':orderId', $orderId, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->rowCount() === 1;
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