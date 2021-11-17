<?php

namespace Core\Calc\Fifo;

use Model\Transaction;

final class FifoTransaction
{
    private string $_currentUsedAmount = '0.0';

    public bool $_isTaxRelevant = true;

    /**
     * @param string $_remainingAmount
     * @param Transaction $_transaction
     */
    public function __construct(
        public string      $_remainingAmount,
        public Transaction $_transaction
    )
    {
    }

    /**
     * @return string
     */
    public function getUsedAmount(): string
    {
        return bcsub($this->_transaction->getValue(), $this->_remainingAmount);
    }

    /**
     * @return string
     */
    public function getRemainingAmount(): string
    {
        return $this->_remainingAmount;
    }

    /**
     * @return string
     */
    public function getCurrentUsedAmount(): string
    {
        return $this->_currentUsedAmount;
    }

    /**
     * @return Transaction
     */
    public function getTransaction(): Transaction
    {
        return $this->_transaction;
    }

    /**
     * @param string $currentUsedAmount
     */
    public function setCurrentUsedAmount(string $currentUsedAmount): void
    {
        $this->_currentUsedAmount = min($currentUsedAmount, $this->getRemainingAmount());
    }

    /**
     * @return bool
     */
    public function isTaxRelevant(): bool
    {
        return $this->_isTaxRelevant;
    }
}