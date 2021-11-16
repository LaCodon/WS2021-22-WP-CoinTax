<?php

namespace Controller;

use Core\Calc\Fifo\Fifo;
use Core\Calc\PriceConverter;
use Core\Exception\InvalidFifoException;
use Core\Repository\CoinRepository;
use Core\Repository\TransactionRepository;
use DateTime;
use DateTimeZone;
use Exception;
use Framework\Framework;
use Framework\Response;
use Framework\Session;
use Model\Transaction;
use Model\User;

final class DashboardController extends Controller
{

    /**
     * @param Response $resp
     */
    public function Action(Response $resp): void
    {
        $this->abortIfUnauthorized();

        $currentUser = Session::getAuthorizedUser();
        $coinRepo = new CoinRepository($this->db());

        $coins = $coinRepo->getUniqueCoinsByUserId($currentUser->getId());

        try {
            $portfolioValue = $this->calculatePortfolioValue($currentUser, $coins);
            $yearlyWinLose = $this->calculateYearlyWin($currentUser, $coins, 2021);

            $resp->setViewVar('firstname', $currentUser->getFirstName());
            $resp->setViewVar('portfolio_value', $portfolioValue['eur_sum']);
            $resp->setViewVar('coin_sums', $portfolioValue['coin_sums']);
            $resp->setViewVar('coin_values', $portfolioValue['coin_values']);
            $resp->setViewVar('coins', $portfolioValue['coins']);
            $resp->setViewVar('win_lose_eur_per_coin', $yearlyWinLose['per_coin']);
            $resp->setViewVar('win_lose_eur_total', $yearlyWinLose['total']);

            $resp->setHtmlTitle('Dashboard');
            $resp->renderView('index');
        } catch (Exception $e) {
            $resp->abort('error during tax calculation:' . $e->getMessage(), Framework::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @param User $user
     * @param array $coins
     * @return array #[ArrayShape(['eur_sum' => "string", 'coin_sums' => "array", 'coin_values' => "array", 'coins' => "array(Coins)"])]
     * @throws Exception
     */
    private function calculatePortfolioValue(User $user, array $coins): array
    {
        $transactionRepo = new TransactionRepository($this->db());
        $priceConverter = new PriceConverter($this->db());

        $result = [
            'eur_sum' => '0.0',
            'coin_sums' => [],
            'coin_values' => [],
            'coins' => [],
        ];

        foreach ($coins as $coin) {
            if ($coin->getSymbol() === PriceConverter::EUR_COIN_SYMBOL)
                continue;

            $transactions = $transactionRepo->getByCoin($user->getId(), $coin->getId());

            $result['coins'][$coin->getSymbol()] = $coin;
            $result['coin_sums'][$coin->getSymbol()] = '0.0';

            foreach ($transactions as $tx) {
                if ($tx->getType() === Transaction::TYPE_SEND) {
                    $result['coin_sums'][$coin->getSymbol()] = bcsub($result['coin_sums'][$coin->getSymbol()], $tx->getValue());
                } else {
                    $result['coin_sums'][$coin->getSymbol()] = bcadd($result['coin_sums'][$coin->getSymbol()], $tx->getValue());
                }
            }

            $result['coin_values'][$coin->getSymbol()] = $priceConverter->getEurValuePlainApiOptional($result['coin_sums'][$coin->getSymbol()], $coin, new DateTime('now', new DateTimeZone('Europe/Berlin')));
            $result['eur_sum'] = bcadd($result['eur_sum'], $result['coin_values'][$coin->getSymbol()]);
        }

        return $result;
    }

    /**
     * @param User $user
     * @param array $coins
     * @param int $year
     * @return array
     * @throws InvalidFifoException
     */
    private function calculateYearlyWin(User $user, array $coins, int $year): array
    {
        $transactionRepo = new TransactionRepository($this->db());
        $priceConverter = new PriceConverter($this->db());

        $result = [
            'total' => '0.0',
            'per_coin' => [],
        ];

        foreach ($coins as $coin) {
            $symbol = $coin->getSymbol();

            if ($symbol === PriceConverter::EUR_COIN_SYMBOL)
                continue;

            $transactions = $transactionRepo->getThisYearByCoin($user->getId(), $coin->getId(), $year);

            $receiveFifo = new Fifo(Fifo::RECEIVE_FIFO);
            $sendFifo = new Fifo(FIFO::SEND_FIFO);

            foreach ($transactions as $tx) {
                if ($tx->getType() === Transaction::TYPE_RECEIVE) {
                    $receiveFifo->push($tx);
                } else {
                    $sendFifo->push($tx);
                }
            }

            $result['per_coin'][$symbol] = [
                'total_win_lose_eur' => '0.0',
            ];

            while (($fifoTx = $sendFifo->pop()) !== null) {
                $sell = $receiveFifo->compensate($fifoTx->getTransaction());
                $result['per_coin'][$symbol]['total_win_lose_eur'] = bcadd($result['per_coin'][$symbol]['total_win_lose_eur'], $sell['sale']->calculateWinLoss($priceConverter, $coin));
            }

            $result['total'] = bcadd($result['total'], $result['per_coin'][$symbol]['total_win_lose_eur']);
        }

        return $result;
    }

}