<?php

namespace Model;

use Framework\Exception\IdOverrideDisallowed;

final class Order
{

    /**
     * @param int $_baseTransactionId
     * @param int $_quoteTransactionId
     * @param int|null $_feeTransactionId
     * @param int $_id
     */
    public function __construct(
        private int      $_baseTransactionId,
        private int      $_quoteTransactionId,
        private int|null $_feeTransactionId,
        private int      $_id = -1,
    )
    {
    }

    /**
     * @return int
     */
    public function getBaseTransactionId(): int
    {
        return $this->_baseTransactionId;
    }

    /**
     * @return int
     */
    public function getFeeTransactionId(): int|null
    {
        return $this->_feeTransactionId;
    }

    /**
     * @return int
     */
    public function getQuoteTransactionId(): int
    {
        return $this->_quoteTransactionId;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->_id;
    }

    /**
     * @param int $id
     * @throws IdOverrideDisallowed
     */
    public function setId(int $id): void
    {
        if ($this->_id !== -1) {
            throw new IdOverrideDisallowed();
        }

        $this->_id = $id;
    }

    /**
     * @param int|null $feeTransactionId
     */
    public function setFeeTransactionId(int|null $feeTransactionId): void
    {
        $this->_feeTransactionId = $feeTransactionId;
    }
}