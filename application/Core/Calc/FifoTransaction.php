<?php

namespace Core\Calc;

use Model\Transaction;

final class FifoTransaction
{
    public function __construct(
        public string      $_remainingAmount,
        public Transaction $_transaction
    )
    {
    }

    public function getUsedAmount(): string
    {
        return bcsub($this->_transaction->getValue(), $this->_remainingAmount);
    }

    public function getRemainingAmount(): string
    {
        return $this->_remainingAmount;
    }
}