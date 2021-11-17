<?php

namespace Core\Calc\Fifo;

use Core\Exception\InvalidFifoException;
use DateInterval;
use Model\Transaction;

final class Fifo
{
    const SEND_FIFO = 1;
    const RECEIVE_FIFO = 2;

    const ARRAY_ELEM_SALE = 'sale';
    const ARRAY_ELEM_SUCCESS = 'success';

    private array $_list = [];
    private bool $_sorted = false;

    public function __construct(
        private int $_type
    )
    {
    }

    /**
     * Add a transaction to this fifo
     * @param Transaction $transaction
     */
    public function push(Transaction $transaction): void
    {
        $this->_list[] = new FifoTransaction($transaction->getValue(), $transaction);
        $this->_sorted = false;
    }

    /**
     * Sorts the fifo for further use
     */
    private function sort(): void
    {
        // reverse sort -> first purchase will be last in array
        usort($this->_list, function (FifoTransaction $a, FifoTransaction $b): int {
            if ($a->_transaction->getDatetimeUtc() < $b->_transaction->getDatetimeUtc()) {
                return 1;
            } else if ($a->_transaction->getDatetimeUtc() === $b->_transaction->getDatetimeUtc()) {
                return 0;
            }

            return -1;
        });
        $this->_sorted = true;
    }

    /**
     * Pops the last element from this fifo
     * @return FifoTransaction|null
     * @throws InvalidFifoException
     */
    public function pop(): FifoTransaction|null
    {
        return $this->popInternal(false);
    }

    /**
     * @throws InvalidFifoException
     */
    private function popInternal(bool $internal = true): FifoTransaction|null
    {
        if (!$internal && $this->_type === self::RECEIVE_FIFO) {
            throw new InvalidFifoException('Cannot pop from receive fifo');
        }

        if (!$this->_sorted) {
            $this->sort();
        }

        return array_pop($this->_list);
    }

    /**
     * Return last element of the fifo but don't pop it
     * @return FifoTransaction|null
     */
    private function peek(): FifoTransaction|null
    {
        $end = end($this->_list);
        if ($end === false) {
            return null;
        }
        return $end;
    }

    /**
     * Return a list of receive transactions that fund the given send transaction. Also returns an indicator
     * about whether the funds sufficed or not
     * @param Transaction $compensateMeTx
     * @return array #[ArrayShape(['success' => "bool", 'sale' => "FifoSale"])]
     * @throws InvalidFifoException
     */
    public function compensate(Transaction $compensateMeTx): array
    {
        if ($this->_type === self::SEND_FIFO) {
            throw new InvalidFifoException('Cannot compensate an outgoing transaction with a list of outgoing transactions');
        }

        if ($compensateMeTx->getType() !== Transaction::TYPE_SEND) {
            throw new InvalidFifoException('Don\'t have to compensate an incoming transaction');
        }

        if (!$this->_sorted) {
            $this->sort();
        }

        $result = [
            self::ARRAY_ELEM_SUCCESS => false,
            self::ARRAY_ELEM_SALE => new FifoSale($compensateMeTx),
        ];

        $remaining = $compensateMeTx->getValue();

        while (bccomp($remaining, '0.0') !== 0) {
            $currenTransaction = $this->peek();
            if ($currenTransaction === null || $currenTransaction->getTransaction()->getDatetimeUtc() > $compensateMeTx->getDatetimeUtc()) {
                // no more transactions for funding left
                break;
            }

            $currenTransaction->setCurrentUsedAmount($remaining);

            // only tax relevant if purchase and sale happen within time range of one year
            $currentTxDateTime = clone $currenTransaction->getTransaction()->getDatetimeUtc();
            $currenTransaction->_isTaxRelevant = $currentTxDateTime->add(new DateInterval('P1Y')) > $compensateMeTx->getDatetimeUtc();

            switch (bccomp($currenTransaction->_remainingAmount, $remaining)) {
                case 1:
                    // amount is more than remaining -> don't use whole transaction
                    $currenTransaction->_remainingAmount = bcsub($currenTransaction->_remainingAmount, $remaining);
                    $remaining = '0.0';
                    break;
                case 0:
                    // amount is exactly remaining -> use whole transaction and pop it
                    $currenTransaction->_remainingAmount = '0.0';
                    $this->popInternal();
                    $remaining = '0.0';
                    break;
                case -1:
                    // amount is less than remaining -> use whole transaction, pop it and go on
                    $remaining = bcsub($remaining, $currenTransaction->_remainingAmount);
                    $currenTransaction->_remainingAmount = '0.0';
                    $this->popInternal();
            }

            $result[self::ARRAY_ELEM_SALE]->addBackingFifoTransaction($currenTransaction);
        }

        // compensation was only successful if whole given transaction is funded
        if (bccomp($remaining, '0.0') === 0) {
            $result[self::ARRAY_ELEM_SUCCESS] = true;
        }
        return $result;
    }
}