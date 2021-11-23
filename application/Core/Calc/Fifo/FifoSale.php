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
     * @return Transaction
     */
    public function getSellTransaction(): Transaction
    {
        return $this->_sellTransaction;
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
     * @return FifoWinLossResult
     */
    public function calculateWinLoss(PriceConverter $priceConverter, Coin $coin): FifoWinLossResult
    {
        $taxableAmount = $this->_sellTransaction->getValue();
        $totalAmount = '0.0';
        $totalBoughtEurSum = '0.0';
        $taxableBoughtEurSum = '0.0';

        foreach ($this->_backingFifoTransactions as $backedBy) {
            $usedQuota = bcdiv($backedBy->getCurrentUsedAmount(), $backedBy->getTransaction()->getValue());
            $totalTxEurValue = $priceConverter->getEurValueApiOptionalSingle($backedBy->getTransaction(), $coin);
            $usedEurValue = bcmul($usedQuota, $totalTxEurValue);
            $totalBoughtEurSum = bcadd($totalBoughtEurSum, $usedEurValue);

            $totalAmount = bcadd($totalAmount, $backedBy->getCurrentUsedAmount());

            if (!$backedBy->isTaxRelevant()) {
                $taxableAmount = bcsub($taxableAmount, $backedBy->getCurrentUsedAmount());
            } else {
                $taxableBoughtEurSum = bcadd($taxableBoughtEurSum, $usedEurValue);
            }
        }

        $taxableSoldEurSum = $priceConverter->getEurValueApiOptionalSingle($this->_sellTransaction, $coin, $taxableAmount);
        $totalSoldEurSum = $priceConverter->getEurValueApiOptionalSingle($this->_sellTransaction, $coin);

        $winLoss = bcsub($totalSoldEurSum, $totalBoughtEurSum);
        $taxRelevantWinLoss = bcsub($taxableSoldEurSum, $taxableBoughtEurSum);

        return new FifoWinLossResult(
            $winLoss,
            $taxRelevantWinLoss,
            $taxableAmount,
            $totalAmount,
            $totalBoughtEurSum,
            $taxableBoughtEurSum,
            $totalSoldEurSum,
            $taxableSoldEurSum,
        );
    }
}