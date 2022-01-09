<?php

namespace Core\Calc\Fifo;

/**
 * Wrapper class for the calculation results of a fifo sale
 */
final class FifoWinLossResult
{
    /**
     * @param string $_totalWinLoss
     * @param string $_taxRelevantWinLoss
     * @param string $_taxableAmount
     * @param string $_totalAmount
     * @param string $_totalBoughtEurSum
     * @param string $_taxableBoughtEurSum
     * @param string $_totalSoldEurSum
     * @param string $_taxableSoldEurSum
     */
    public function __construct(
        private string $_totalWinLoss,
        private string $_taxRelevantWinLoss,
        private string $_taxableAmount,
        private string $_totalAmount,
        private string $_totalBoughtEurSum,
        private string $_taxableBoughtEurSum,
        private string $_totalSoldEurSum,
        private string $_taxableSoldEurSum,
    )
    {
    }

    /**
     * @return string
     */
    public function getTotalBoughtEurSum(): string
    {
        return $this->_totalBoughtEurSum;
    }

    /**
     * @return string
     */
    public function getTotalSoldEurSum(): string
    {
        return $this->_totalSoldEurSum;
    }

    /**
     * @return string
     */
    public function getTaxableAmount(): string
    {
        return $this->_taxableAmount;
    }

    /**
     * @return string
     */
    public function getTaxRelevantWinLoss(): string
    {
        return $this->_taxRelevantWinLoss;
    }

    /**
     * @return string
     */
    public function getTotalWinLoss(): string
    {
        return $this->_totalWinLoss;
    }

    /**
     * @return string
     */
    public function getTaxableBoughtEurSum(): string
    {
        return $this->_taxableBoughtEurSum;
    }

    /**
     * @return string
     */
    public function getTaxableSoldEurSum(): string
    {
        return $this->_taxableSoldEurSum;
    }

    /**
     * @return string
     */
    public function getTotalAmount(): string
    {
        return $this->_totalAmount;
    }

}