<?php

namespace Controller;

use Core\Calc\Fifo\Fifo;
use Core\Calc\PriceConverter;
use Core\Calc\Tax\WinLossCalculator;
use Core\Repository\CoinRepository;
use Core\Repository\TransactionRepository;
use Exception;
use Framework\Exception\ViewNotFound;
use Framework\Response;
use Framework\Session;

final class ReportController extends Controller
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
        $priceConverter = new PriceConverter($this->db());
        $winLossCalculator = new WinLossCalculator($this->db());

        $coins = $coinRepo->getUniqueCoinsByUserId($currentUser->getId());

        $report = [
            'coinReports' => [],
            'totalTaxRelevantWinLoss' => '0.0',
            'totalPaidFeesEur' => '0.0',
            'cleanedWinLoss' => '0.0',
        ];

        $year = DashboardController::getCalcYear();

        foreach ($coins as $coin) {
            if ($coin->getSymbol() === PriceConverter::EUR_COIN_SYMBOL) {
                continue;
            }

            $coinReport = [
                'coin' => $coin,
                'compensationReports' => [],
                'error' => null,
                'totalTaxRelevantWinLoss' => '0.0',
            ];

            try {
                $data = $winLossCalculator->calculateWinReport($coin, $currentUser, $year);
                if (count($data) !== 0) {
                    $compensationReports = [];

                    foreach ($data as $compensation) {
                        $winLoss = $compensation[Fifo::ARRAY_ELEM_SALE]->calculateWinLoss($priceConverter, $coin);
                        $coinReport['totalTaxRelevantWinLoss'] = bcadd($coinReport['totalTaxRelevantWinLoss'], $winLoss->getTaxRelevantWinLoss());
                        $isFee = $transactionRepo->isFeeTransaction($compensation[Fifo::ARRAY_ELEM_SALE]->getSellTransaction()->getId());

                        $compensationReports[] = [
                            'compensation' => $compensation,
                            'winLoss' => $winLoss,
                            'isFee' => $isFee,
                        ];

                        if ($isFee) {
                            $report['totalPaidFeesEur'] = bcadd($report['totalPaidFeesEur'], $winLoss->getTotalSoldEurSum());
                        }

                    }

                    $coinReport['compensationReports'] = $compensationReports;

                    $report['totalTaxRelevantWinLoss'] = bcadd($report['totalTaxRelevantWinLoss'], $coinReport['totalTaxRelevantWinLoss']);
                }
            } catch (Exception $e) {
                $coinReport['error'] = $e->getMessage();
            }

            $report['coinReports'][] = $coinReport;
        }

        $report['cleanedWinLoss'] = bcsub($report['totalTaxRelevantWinLoss'], $report['totalPaidFeesEur']);

        $resp->setViewVar('calc_year', $year);
        $resp->setViewVar('report', $report);
        $resp->setViewVar('price_converter', $priceConverter);

        $resp->setHtmlTitle('Gewinnreport');
        $resp->renderView('index');
    }

}