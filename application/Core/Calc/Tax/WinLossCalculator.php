<?php

namespace Core\Calc\Tax;

use Core\Calc\Fifo\Fifo;
use Core\Calc\PriceConverter;
use Core\Exception\InvalidFifoException;
use Core\Exception\WinLossNotCalculableException;
use Core\Repository\TransactionRepository;
use DateTime;
use DateTimeZone;
use Exception;
use Model\Coin;
use Model\Transaction;
use Model\User;
use PDO;

final class WinLossCalculator
{

    const ARRAY_ELEM_EUR_SUM = 'eur_sum';
    const ARRAY_ELEM_COIN_SUMS = 'coin_sums';
    const ARRAY_ELEM_COIN_VALUES = 'coin_values';
    const ARRAY_ELEM_COINS = 'coins';

    const ARRAY_ELEM_TOTAL = 'total';
    const ARRAY_ELEM_PER_COIN = 'per_coin';
    const ARRAY_ELEM_TOTAL_WIN_LOSE_EUR = 'total_win_lose_eur';

    public function __construct(
        private PDO $_pdo,
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

        $transactionRepo = new TransactionRepository($this->_pdo);
        $priceConverter = new PriceConverter($this->_pdo);

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
                    $totalWinLoseEur = bcadd($totalWinLoseEur, $compensation[FIFO::ARRAY_ELEM_SALE]->calculateWinLoss($priceConverter, $coin, $onlyTaxRelevant));
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
     * @param User $user
     * @param array $coins
     * @return array #[ArrayShape(['eur_sum' => "string", 'coin_sums' => "array", 'coin_values' => "array", 'coins' => "array(Coins)"])]
     * @throws Exception
     */
    public function calculatePortfolioValue(User $user, array $coins): array
    {
        $transactionRepo = new TransactionRepository($this->_pdo);
        $priceConverter = new PriceConverter($this->_pdo);

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
     * @param User $user
     * @param array $coins
     * @param int $year
     * @param bool $onlyTaxRelevant
     * @return array
     * @throws WinLossNotCalculableException
     */
    public function calculateTotalWinLossForYear(User $user, array $coins, int $year, bool $onlyTaxRelevant): array
    {
        $result = [
            self::ARRAY_ELEM_TOTAL => '0.0',
            self::ARRAY_ELEM_PER_COIN => [],
        ];

        foreach ($coins as $coin) {
            $winLoseForCoin = $this->calculateWinLoss($coin, $user, onlyTaxRelevant: $onlyTaxRelevant, year: $year);

            $result[self::ARRAY_ELEM_PER_COIN][$coin->getSymbol()] = $winLoseForCoin;
            $result[self::ARRAY_ELEM_TOTAL] = bcadd($result[self::ARRAY_ELEM_TOTAL], $winLoseForCoin);
        }

        return $result;
    }
}