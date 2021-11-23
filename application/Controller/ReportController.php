<?php

namespace Controller;

use Core\Calc\PriceConverter;
use Core\Calc\Tax\WinLossCalculator;
use Core\Exception\WinLossNotCalculableException;
use Core\Repository\CoinRepository;
use Framework\Exception\ViewNotFound;
use Framework\Response;
use Framework\Session;
use Framework\Validation\Input;
use Framework\Validation\InputValidator;

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
        $priceConverter = new PriceConverter($this->db());
        $winLossCalculator = new WinLossCalculator($this->db());

        $coins = $coinRepo->getUniqueCoinsByUserId($currentUser->getId());

        $report = [];

        $year = DashboardController::getCalcYear();

        foreach ($coins as $key => $coin) {
            if ($coin->getSymbol() === PriceConverter::EUR_COIN_SYMBOL) {
                continue;
            }

            try {
                $report[$key] = $winLossCalculator->calculateWinReport($coin, $currentUser, $year);
            } catch (WinLossNotCalculableException $e) {
                // todo: remember calc error
            }
        }

        $resp->setViewVar('calc_year', $year);

        $resp->setViewVar('report', $report);
        $resp->setViewVar('coins', $coins);
        $resp->setViewVar('price_converter', $priceConverter);

        $resp->setHtmlTitle('Gewinnreport');
        $resp->renderView('index');
    }

}