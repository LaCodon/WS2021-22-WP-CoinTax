<?php

namespace Controller;

use Core\Calc\Fifo;
use Core\Calc\PriceConverter;
use Core\Repository\CoinRepository;
use Core\Repository\PriceRepository;
use Core\Repository\TransactionRepository;
use DateTime;
use Framework\Exception\ViewNotFound;
use Framework\Response;
use Framework\Session;
use Model\Transaction;
use Model\User;

final class DashboardController extends Controller
{

    /**
     * @throws ViewNotFound
     */
    public function Action(Response $resp): void
    {
        $this->abortIfUnauthorized();

        $currentUser = Session::getAuthorizedUser();

        $coinRepo = new CoinRepository($this->db());
        $transactionRepo = new TransactionRepository($this->db());

        $coins = $coinRepo->getUniqueCoinsByUserId(13);
        $rvnTransactions = $transactionRepo->getThisYearByCoin(13, 16, 2021);

        $receiveFifo = new Fifo(Fifo::RECEIVE_FIFO);
        $sendFifo = new Fifo(FIFO::SEND_FIFO);

        foreach ($rvnTransactions as $t) {
            if ($t->getType() === Transaction::TYPE_RECEIVE) {
                $receiveFifo->push($t);
            } else {
                $sendFifo->push($t);
            }
        }

        while (($t = $sendFifo->pop()) !== null) {
            var_dump_pre($t->_transaction->getValue());
            var_dump_pre($t->_transaction->getDatetimeUtc()->setTimezone(new \DateTimeZone('Europe/Berlin'))->format('d.m.Y H:i'));
            $sell = $receiveFifo->compensate($t->_transaction);
            var_dump_pre($sell['success']);
            foreach ($sell['transactions'] as $backedBy) {
                var_dump_pre('++++++++++++++++++++');
                var_dump_pre($backedBy->_transaction->getId());
                var_dump_pre($backedBy->getUsedAmount());
                var_dump_pre($backedBy->getRemainingAmount());
            }
            var_dump_pre('------------------------------------');
        }

        $resp->setViewVar('portfolio_value', $this->calculatePortfolioValue($currentUser));
        $resp->setViewVar('firstname', $currentUser->getFirstName());

        $resp->setHtmlTitle('Dashboard');
        $resp->renderView('index');
    }

    private function calculatePortfolioValue(User $user): string
    {
        $coinRepo = new CoinRepository($this->db());
        $transactionRepo = new TransactionRepository($this->db());
        $priceConverter = new PriceConverter($this->db());

        $coins = $coinRepo->getUniqueCoinsByUserId($user->getId());

        $eurSum = '0.0';

        foreach ($coins as $coin) {
            if ($coin->getSymbol() === PriceConverter::EUR_COIN_SYMBOL)
                continue;

            $transactions = $transactionRepo->getByCoin($user->getId(), $coin->getId());

            $coinSum = '0.0';

            foreach ($transactions as $tx) {
                if ($tx->getType() === Transaction::TYPE_SEND) {
                    $coinSum = bcsub($coinSum, $tx->getValue());
                } else {
                    $coinSum = bcadd($coinSum, $tx->getValue());
                }
            }

            $eurSum = bcadd($eurSum, $priceConverter->getEurValuePlainApiOptional($coinSum, $coin, new DateTime('now', new \DateTimeZone('Europe/Berlin'))));
        }

        return $eurSum;
    }

}