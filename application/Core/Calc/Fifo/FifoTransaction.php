<?php

namespace Core\Calc\Fifo;

use Core\Calc\PriceConverter;
use Model\Coin;
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
     * Returns the EUR value of the current used amount and the cost per coin
     * @param Coin $coin
     * @param PriceConverter $priceConverter
     * @return array(EURValue,CostPerCoin)
     */
    public function getCurrentUsedEurValue(Coin $coin, PriceConverter $priceConverter): array
    {
        $buyPrice = $priceConverter->getEurValueApiOptionalSingle($this->getTransaction(), $coin);
        $buyPrice = bcdiv($buyPrice, $this->getTransaction()->getValue());
        return [
            bcmul($buyPrice, $this->getCurrentUsedAmount()),
            $buyPrice
        ];
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