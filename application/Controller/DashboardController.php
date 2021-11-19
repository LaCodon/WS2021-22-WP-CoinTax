<?php

namespace Controller;

use Core\Calc\Fifo\Fifo;
use Core\Calc\PriceConverter;
use Core\Calc\Tax\WinLossCalculator;
use Core\Exception\InvalidFifoException;
use Core\Repository\CoinRepository;
use Core\Repository\TransactionRepository;
use DateTime;
use DateTimeZone;
use Exception;
use Framework\Framework;
use Framework\Response;
use Framework\Session;
use Framework\Validation\Input;
use Framework\Validation\InputValidator;
use Framework\Validation\ValidationResult;
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
        $winLossCalculator = new WinLossCalculator($this->db());

        $coins = $coinRepo->getUniqueCoinsByUserId($currentUser->getId());

        $input = InputValidator::parseAndValidate([
            new Input(INPUT_GET, 'year', 'Jahr', false, FILTER_VALIDATE_INT)
        ]);


        $year = intval((new DateTime('now', new DateTimeZone('Europe/Berlin')))->format('Y'));
        if ($input->getValue('year') !== '') {
            $year = intval($input->getValue('year'));
            $input->setValue('year', $year);
        }

        Session::setInputValidationResult($input);

        try {
            $portfolioValue = $winLossCalculator->calculatePortfolioValue($currentUser, $coins);
            $yearlyWinLose = $winLossCalculator->calculateTotalWinLossForYear($currentUser, $coins, $year, false);

            $resp->setViewVar('firstname', $currentUser->getFirstName());
            $resp->setViewVar('calc_year', $year);

            $resp->setViewVar('portfolio_value', $portfolioValue[WinLossCalculator::ARRAY_ELEM_EUR_SUM]);
            $resp->setViewVar('coin_sums', $portfolioValue[WinLossCalculator::ARRAY_ELEM_COIN_SUMS]);
            $resp->setViewVar('coin_values', $portfolioValue[WinLossCalculator::ARRAY_ELEM_COIN_VALUES]);
            $resp->setViewVar('coins', $portfolioValue[WinLossCalculator::ARRAY_ELEM_COINS]);
            $resp->setViewVar('win_lose_eur_per_coin', $yearlyWinLose[WinLossCalculator::ARRAY_ELEM_PER_COIN]);
            $resp->setViewVar('win_lose_eur_total', $yearlyWinLose[WinLossCalculator::ARRAY_ELEM_TOTAL]);

            $resp->setHtmlTitle('Dashboard');
            $resp->renderView('index');
        } catch (Exception $e) {
            $resp->abort('error during tax calculation:' . $e->getMessage(), Framework::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

}