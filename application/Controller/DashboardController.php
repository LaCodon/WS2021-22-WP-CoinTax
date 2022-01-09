<?php

namespace Controller;

use Core\Calc\Tax\WinLossCalculator;
use DateTime;
use DateTimeZone;
use Exception;
use Framework\Framework;
use Framework\Response;
use Framework\Session;
use Framework\Validation\Input;
use Framework\Validation\InputValidator;

/**
 * Controller for /dashboard
 */
final class DashboardController extends Controller
{

    /**
     * Endpoint for /dashboard/
     * @param Response $resp
     */
    public function Action(Response $resp): void
    {
        $this->abortIfUnauthorized($resp);

        $currentUser = Session::getAuthorizedUser();
        $coinRepo = $this->_context->getCoinRepo();
        $winLossCalculator = new WinLossCalculator($this->_context);

        $coins = $coinRepo->getUniqueCoinsByUserId($currentUser->getId());

        $year = self::getCalcYear();

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

    /**
     * @param bool $setInputValidationResult
     * @return int The year selected by the user or the current year if none was selected
     */
    public static function getCalcYear(bool $setInputValidationResult = true): int
    {
        $input = InputValidator::parseAndValidate([
            new Input(INPUT_GET, 'year', 'Jahr', false, FILTER_VALIDATE_INT)
        ]);

        $year = intval((new DateTime('now', new DateTimeZone('Europe/Berlin')))->format('Y'));
        if ($input->getValue('year') !== '') {
            $year = intval($input->getValue('year'));
            $input->setValue('year', $year);
        }

        if ($setInputValidationResult === true)
            Session::setInputValidationResult($input);

        return $year;
    }

}