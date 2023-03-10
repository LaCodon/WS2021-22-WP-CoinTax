<?php

namespace Core\Calc\Tax;

use Core\Calc\Fifo\Fifo;
use Core\Calc\PriceConverter;
use Core\Exception\InvalidFifoException;
use Core\Exception\WinLossNotCalculableException;
use DateTime;
use DateTimeZone;
use Exception;
use Framework\Context;
use Model\Coin;
use Model\Transaction;
use Model\User;

/**
 * A WinLossCalculator can be used to calculate how much profit we made with buying and selling a specific coin or with
 * a certain transaction.
 */
final class WinLossCalculator
{

    const ARRAY_ELEM_EUR_SUM = 'eur_sum';
    const ARRAY_ELEM_COIN_SUMS = 'coin_sums';
    const ARRAY_ELEM_COIN_VALUES = 'coin_values';
    const ARRAY_ELEM_COINS = 'coins';

    const ARRAY_ELEM_TOTAL = 'total';
    const ARRAY_ELEM_PER_COIN = 'per_coin';

    public function __construct(
        private Context $_context,
    )
    {
    }

    /**
     * This method calculates either a specific compensation if $transaction is given or calculates the total
     * (optional: only tax relevant) wins/losses for the given coin.
     * @param Coin $coin is required in order to fetch the relevant buy transactions
     * @param User $owner sets the user who must own all considered transactions
     * @param Transaction|null $transaction if not null: calculate the fifo compensation for this transaction (find all backing buy transactions for this sell)
     * @param bool $onlyTaxRelevant only if $transaction is null: if true sum only tax relevant sales, otherwise sum all
     * @param int|null $year only if $transaction is null: only calculate win/loss for the given year
     * @return string|array either the total win loss or the fifo compensation #[ArrayShape(['success' => "bool", 'sale' => "FifoSale"])]
     * @throws WinLossNotCalculableException
     */
    public function calculateWinLoss(Coin $coin, User $owner, Transaction|null $transaction = null, bool $onlyTaxRelevant = true, int|null $year = null): string|array
    {
        if ($transaction !== null && $transaction->getType() !== Transaction::TYPE_SEND) {
            throw new WinLossNotCalculableException('cannot calculate win loss for non-send transactions');
        }

        $transactionRepo = $this->_context->getTransactionRepo();
        $priceConverter = new PriceConverter($this->_context);

        $transactions = $transactionRepo->getByCoin($owner->getId(), $coin->getId());

        $receiveFifo = new Fifo(Fifo::RECEIVE_FIFO);
        $sendFifo = new Fifo(FIFO::SEND_FIFO);

        foreach ($transactions as $tx) {
            if ($tx->getType() === Transaction::TYPE_RECEIVE) {
                $receiveFifo->push($tx);
            } else {
                $sendFifo->push($tx);
            }
        }

        $fifoCompensation = null;
        $totalWinLoseEur = '0.0';

        try {
            while (($fifoTx = $sendFifo->pop()) !== null) {
                $compensation = $receiveFifo->compensate($fifoTx->getTransaction());

                if (APPLICATION_DEBUG === true) {
                    echo 'Sold ' . $fifoTx->getTransaction()->getValue() . $coin->getSymbol() . ' backed by<br>';
                    foreach ($compensation[Fifo::ARRAY_ELEM_SALE]->getBackingFifoTransactions() as $tx) {
                        echo '&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp';
                        echo $tx->getTransaction()->getValue() . ' (actual used: ' . $tx->getCurrentUsedAmount() . ')';
                        echo '<br>';
                    }
                    echo '<br><br>';
                }

                if ($transaction === null && $year !== null
                    && $fifoTx->getTransaction()->getDatetimeUtc()->setTimezone(new DateTimeZone('Europe/Berlin'))->format('Y') === sprintf('%d', $year)) {
                    if ($onlyTaxRelevant === true) {
                        $totalWinLoseEur = bcadd($totalWinLoseEur, $compensation[FIFO::ARRAY_ELEM_SALE]->calculateWinLoss($priceConverter, $coin)->getTaxRelevantWinLoss());
                    } else {
                        $totalWinLoseEur = bcadd($totalWinLoseEur, $compensation[FIFO::ARRAY_ELEM_SALE]->calculateWinLoss($priceConverter, $coin)->getTotalWinLoss());
                    }
                }

                if ($transaction !== null && $fifoTx->getTransaction()->getId() === $transaction->getId()) {
                    // compensated current tx and stop calculation
                    $fifoCompensation = $compensation;
                    break;
                }
            }
        } catch (InvalidFifoException $e) {
            throw new WinLossNotCalculableException('error during win-loss-calculation: ' . $e->getMessage());
        }

        if ($transaction !== null) {
            if ($fifoCompensation === null) {
                throw new WinLossNotCalculableException('failed to compensate given transaction');
            }
            return $fifoCompensation;
        }
        return $totalWinLoseEur;
    }

    /**
     * Calculate how much of the given $coins a $user owns currently and what they are worth at the moment.
     * @param User $user
     * @param array $coins
     * @return array #[ArrayShape(['eur_sum' => "string", 'coin_sums' => "array", 'coin_values' => "array", 'coins' => "array(Coins)"])]
     * @throws Exception
     */
    public function calculatePortfolioValue(User $user, array $coins): array
    {
        $transactionRepo = $this->_context->getTransactionRepo();
        $priceConverter = new PriceConverter($this->_context);

        $result = [
            self::ARRAY_ELEM_EUR_SUM => '0.0',
            self::ARRAY_ELEM_COIN_SUMS => [],
            self::ARRAY_ELEM_COIN_VALUES => [],
            self::ARRAY_ELEM_COINS => [],
        ];

        foreach ($coins as $coin) {
            if ($coin->getSymbol() === PriceConverter::EUR_COIN_SYMBOL)
                continue;

            $transactions = $transactionRepo->getByCoin($user->getId(), $coin->getId());

            $result[self::ARRAY_ELEM_COINS][$coin->getSymbol()] = $coin;
            $result[self::ARRAY_ELEM_COIN_SUMS][$coin->getSymbol()] = '0.0';

            foreach ($transactions as $tx) {
                if ($tx->getType() === Transaction::TYPE_SEND) {
                    $result[self::ARRAY_ELEM_COIN_SUMS][$coin->getSymbol()] = bcsub($result[self::ARRAY_ELEM_COIN_SUMS][$coin->getSymbol()], $tx->getValue());
                } else {
                    $result[self::ARRAY_ELEM_COIN_SUMS][$coin->getSymbol()] = bcadd($result[self::ARRAY_ELEM_COIN_SUMS][$coin->getSymbol()], $tx->getValue());
                }
            }

            $result[self::ARRAY_ELEM_COIN_VALUES][$coin->getSymbol()] = $priceConverter->getEurValuePlainApiOptional($result[self::ARRAY_ELEM_COIN_SUMS][$coin->getSymbol()], $coin, new DateTime('now', new DateTimeZone('Europe/Berlin')));
            $result[self::ARRAY_ELEM_EUR_SUM] = bcadd($result[self::ARRAY_ELEM_EUR_SUM], $result[self::ARRAY_ELEM_COIN_VALUES][$coin->getSymbol()]);
        }

        return $result;
    }

    /**
     * Calculate how much profit the $user made by buying and selling $coins in the given $year
     * @param User $user
     * @param array $coins
     * @param int $year
     * @param bool $onlyTaxRelevant true if only tax relevant sales should be considered for the profit calculation
     * @return array #[ArrayShape([self::ARRAY_ELEM_TOTAL => "string", self::ARRAY_ELEM_PER_COIN => "array"])]
     * @throws WinLossNotCalculableException
     */
    public function calculateTotalWinLossForYear(User $user, array $coins, int $year, bool $onlyTaxRelevant): array
    {
        $result = [
            self::ARRAY_ELEM_TOTAL => '0.0',
            self::ARRAY_ELEM_PER_COIN => [],
        ];

        foreach ($coins as $coin) {
            if ($coin->getSymbol() === PriceConverter::EUR_COIN_SYMBOL) {
                continue;
            }

            $winLoseForCoin = $this->calculateWinLoss($coin, $user, onlyTaxRelevant: $onlyTaxRelevant, year: $year);

            $result[self::ARRAY_ELEM_PER_COIN][$coin->getSymbol()] = $winLoseForCoin;
            $result[self::ARRAY_ELEM_TOTAL] = bcadd($result[self::ARRAY_ELEM_TOTAL], $winLoseForCoin);
        }

        return $result;
    }

    /**
     * Returns a list of all sells (as FifoSale objects) of $coin in a given $year and also their backing buy transactions
     * @param Coin $coin
     * @param User $user
     * @param int $year
     * @return array #[ArrayShape([self::ARRAY_ELEM_TOTAL => "string", self::ARRAY_ELEM_COMPENSATIONS => "array"])]
     * @throws WinLossNotCalculableException
     */
    public function calculateWinReport(Coin $coin, User $user, int $year): array
    {
        $transactionRepo = $this->_context->getTransactionRepo();

        $transactions = $transactionRepo->getByCoin($user->getId(), $coin->getId());

        $receiveFifo = new Fifo(Fifo::RECEIVE_FIFO);
        $sendFifo = new Fifo(FIFO::SEND_FIFO);

        foreach ($transactions as $tx) {
            if ($tx->getType() === Transaction::TYPE_RECEIVE) {
                $receiveFifo->push($tx);
            } else {
                $sendFifo->push($tx);
            }
        }

        $result = [];

        try {
            while (($fifoTx = $sendFifo->pop()) !== null) {
                $compensation = $receiveFifo->compensate($fifoTx->getTransaction());

                if (APPLICATION_DEBUG === true) {
                    echo 'Sold ' . $fifoTx->getTransaction()->getValue() . $coin->getSymbol() . ' backed by<br>';
                    foreach ($compensation[Fifo::ARRAY_ELEM_SALE]->getBackingFifoTransactions() as $tx) {
                        echo '&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp';
                        echo $tx->getTransaction()->getValue() . ' (actual used: ' . $tx->getCurrentUsedAmount() . ')';
                        echo '<br>';
                    }
                    echo '<br><br>';
                }

                if ($fifoTx->getTransaction()->getDatetimeUtc()->setTimezone(new DateTimeZone('Europe/Berlin'))->format('Y') === sprintf('%d', $year)) {
                    $result[] = $compensation;
                }
            }
        } catch (InvalidFifoException $e) {
            throw new WinLossNotCalculableException('error during win-loss-calculation: ' . $e->getMessage());
        }

        return $result;
    }
}