<?php

namespace Core\Calc\Fifo;

use Core\Calc\PriceConverter;
use Model\Coin;
use Model\Transaction;

/**
 * Wrapper class for sell transactions in order to store their corresponding (funding / backing) buy transactions
 */
final class FifoSale
{
    /**
     * @var array of FifoTransaction objects
     */
    private array $_backingFifoTransactions = [];

    /**
     * @param Transaction $_sellTransaction The transaction for which this wrapper object is meant
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
     * Calculates the total win / loss in EUR achieved by this coin sell
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

        // iterate over all buy transactions in order to calculate how much profit we made by selling the bought coins again
        foreach ($this->_backingFifoTransactions as $backedBy) {
            // prevent from dividing by zero
            $usedQuota = '1.0';
            if (bccomp($backedBy->getTransaction()->getValue(), '0.0') !== 0) {
                // the current used amount tells us how much of the buying transaction we sold in this sell transaction
                // this is required because a buy transaction can feed / back more than one sell transaction
                $usedQuota = bcdiv($backedBy->getCurrentUsedAmount(), $backedBy->getTransaction()->getValue());
            }

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