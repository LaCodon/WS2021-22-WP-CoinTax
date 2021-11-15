<?php

namespace Core\Calc\Fifo;

use Core\Calc\PriceConverter;
use Model\Coin;
use Model\Transaction;

final class FifoSale
{
    private array $_backingFifoTransactions = [];

    /**
     * @param Transaction $_sellTransaction
     */
    public function __construct(
        private Transaction $_sellTransaction,
    )
    {
    }

    /**
     * @return array
     */
    public function getBackingFifoTransactions(): array
    {
        return $this->_backingFifoTransactions;
    }

    /**
     * @param FifoTransaction $backingFifoTransaction
     */
    public function addBackingFifoTransaction(FifoTransaction $backingFifoTransaction): void
    {
        $this->_backingFifoTransactions[] = $backingFifoTransaction;
    }

    /**
     * Calculates the total win / lost in EUR achieved by this coin sell
     * @param PriceConverter $priceConverter
     * @param Coin $coin
     * @return string
     */
    public function calculateWinLoss(PriceConverter $priceConverter, Coin $coin): string
    {
        $soldEurSum = $priceConverter->getEurValueApiOptionalSingle($this->_sellTransaction, $coin);
        $boughtEurSum = '0.0';

        foreach ($this->_backingFifoTransactions as $backedBy) {
            $usedQuota = bcdiv($backedBy->getCurrentUsedAmount(), $backedBy->getTransaction()->getValue());
            $totalTxEurValue = $priceConverter->getEurValueApiOptionalSingle($backedBy->getTransaction(), $coin);
            $usedEurValue = bcmul($usedQuota, $totalTxEurValue);
            $boughtEurSum = bcadd($boughtEurSum, $usedEurValue);
        }

        return bcsub($soldEurSum, $boughtEurSum);
    }
}