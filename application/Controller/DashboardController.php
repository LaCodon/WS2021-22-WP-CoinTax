<?php

namespace Controller;

use Cassandra\Date;
use Core\Calc\Fifo;
use Core\Repository\CoinRepository;
use Core\Repository\TransactionRepository;
use Framework\Exception\ViewNotFound;
use Framework\Response;
use Model\Transaction;

final class DashboardController extends Controller
{

    /**
     * @throws ViewNotFound
     */
    public function Action(Response $resp): void
    {
        $this->abortIfUnauthorized();

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


        $resp->renderView('index');
    }

}