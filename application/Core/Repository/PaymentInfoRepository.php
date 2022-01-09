<?php

namespace Core\Repository;

use Framework\Exception\IdOverrideDisallowed;
use Framework\Exception\UniqueConstraintViolation;
use Model\PaymentInfo;
use PDO;
use PDOException;

/**
 * Repository for accessing the payment_info SQL table
 */
final class PaymentInfoRepository
{
    const PAYMENT_PENDING = 0;
    const PAYMENT_FULFILLED = 1;

    public function __construct(
        private PDO $_pdo,
    )
    {
    }

    /**
     * Returns true if the given user has already payed for the given year but the payment is not fulfilled yet
     * @param int $userId
     * @param int $year
     * @return bool
     */
    public function isFulfillmentPending(int $userId, int $year): bool
    {
        return $this->hasFulfillmentState($userId, $year, self::PAYMENT_PENDING);
    }

    /**
     * Returns true if the given user has a payment for the given year and already fulfilled it
     * @param int $userId
     * @param int $year
     * @return bool
     */
    public function hasFulfilled(int $userId, int $year): bool
    {
        return $this->hasFulfillmentState($userId, $year, self::PAYMENT_FULFILLED);
    }

    /**
     * Returns true if the payment for the given year has failed
     * @param int $userId
     * @param int $year
     * @return bool
     */
    public function paymentFailed(int $userId, int $year): bool
    {
        $stmt = $this->_pdo->prepare('SELECT payment_info_id FROM payment_info WHERE user_id = :userId AND year = :year AND failed = 1 LIMIT 1');
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':year', $year, PDO::PARAM_INT);

        if (!$stmt->execute()) {
            return false;
        }

        return $stmt->rowCount() === 1;
    }

    /**
     * Returns true if the given user has the given payment state for the given year
     * @param int $userId
     * @param int $year
     * @param int $state
     * @return bool
     */
    private function hasFulfillmentState(int $userId, int $year, int $state): bool
    {
        $stmt = $this->_pdo->prepare('SELECT payment_info_id FROM payment_info WHERE user_id = :userId AND year = :year AND fulfilled = :state AND failed = 0 LIMIT 1');
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':year', $year, PDO::PARAM_INT);
        $stmt->bindParam(':state', $state, PDO::PARAM_INT);

        if (!$stmt->execute()) {
            return false;
        }

        return $stmt->rowCount() === 1;
    }

    /**
     * @param PaymentInfo $paymentInfo
     * @return bool
     * @throws IdOverrideDisallowed
     * @throws UniqueConstraintViolation
     */
    public function insert(PaymentInfo $paymentInfo): bool
    {
        if ($paymentInfo->getId() !== -1) {
            // payment is already in database
            return false;
        }

        $userId = $paymentInfo->getUserId();
        $iban = $paymentInfo->getIban();
        $bic = $paymentInfo->getBic();
        $year = $paymentInfo->getYear();
        $fulfilled = $paymentInfo->isFulfilled() ? 1 : 0;
        $failed = $paymentInfo->isFailed() ? 1 : 0;

        $stmt = $this->_pdo->prepare('INSERT INTO payment_info (user_id, iban, bic, year, fulfilled, failed) VALUES (:userId, :iban, :bic, :year, :fulfilled, :failed)');
        $stmt->bindParam(":userId", $userId, PDO::PARAM_INT);
        $stmt->bindParam(":iban", $iban);
        $stmt->bindParam(":bic", $bic);
        $stmt->bindParam(":year", $year, PDO::PARAM_INT);
        $stmt->bindParam(":fulfilled", $fulfilled, PDO::PARAM_INT);
        $stmt->bindParam(":failed", $failed, PDO::PARAM_INT);

        try {
            $res = $stmt->execute();
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                // violation against unique constraint aka email already exists for another user
                throw new UniqueConstraintViolation();
            } else {
                throw $e;
            }
        }

        $paymentInfo->setId($this->_pdo->lastInsertId());

        return $res;
    }

    /**
     * Get all payments for the given userId ordered by year
     * @param int $userId
     * @return array|null
     */
    public function getAllByUserId(int $userId): array|null
    {
        $stmt = $this->_pdo->prepare('SELECT * FROM payment_info WHERE user_id = :userId ORDER BY year DESC');
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);

        if ($stmt->execute() === false) {
            return null;
        }

        $result = [];

        while (($obj = $stmt->fetchObject()) !== false) {
            $result[] = $this->makePaymentInfo($obj);
        }

        return $result;
    }

    /**
     * @param object|bool $resultObj
     * @return PaymentInfo|null
     */
    private function makePaymentInfo(object|bool $resultObj): PaymentInfo|null
    {
        if ($resultObj === false) {
            return null;
        }

        return new PaymentInfo(
            $resultObj->user_id,
            $resultObj->iban,
            $resultObj->bic,
            $resultObj->year,
            intval($resultObj->fulfilled) === 1,
            intval($resultObj->failed) === 1,
            $resultObj->payment_info_id,
        );
    }
}