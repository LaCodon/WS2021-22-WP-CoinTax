<?php

namespace Controller;

use Core\Calc\Fifo\Fifo;
use Core\Calc\PriceConverter;
use Core\Calc\Tax\WinLossCalculator;
use Core\Repository\CoinRepository;
use Core\Repository\PaymentInfoRepository;
use Core\Repository\TransactionRepository;
use Core\Repository\UserRepository;
use DateTime;
use DateTimeZone;
use Exception;
use Framework\Exception\IdOverrideDisallowed;
use Framework\Exception\UniqueConstraintViolation;
use Framework\Exception\ViewNotFound;
use Framework\Response;
use Framework\Session;
use Framework\Validation\Input;
use Framework\Validation\InputValidator;
use Model\PaymentInfo;

final class ReportController extends Controller
{

    /**
     * @throws ViewNotFound
     */
    public function Action(Response $resp): void
    {
        $this->abortIfUnauthorized($resp);

        $currentUser = Session::getAuthorizedUser();

        $paymentInfoRepo = new PaymentInfoRepository($this->db());
        $coinRepo = new CoinRepository($this->db());
        $transactionRepo = new TransactionRepository($this->db());
        $priceConverter = new PriceConverter($this->db());
        $winLossCalculator = new WinLossCalculator($this->db());

        $year = DashboardController::getCalcYear();

        if (!$paymentInfoRepo->hasFulfilled($currentUser->getId(), $year)) {
            $resp->redirect($resp->getActionUrl('payment') . '?year=' . $year);
        }

        $coins = $coinRepo->getUniqueCoinsByUserId($currentUser->getId());

        $report = [
            'coinReports' => [],
            'totalTaxRelevantWinLoss' => '0.0',
            'totalPaidFeesEur' => '0.0',
            'cleanedWinLoss' => '0.0',
        ];

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

    /**
     * @throws ViewNotFound
     */
    public function PaymentAction(Response $resp): void
    {
        $this->abortIfUnauthorized($resp);

        $currentUser = Session::getAuthorizedUser();
        $paymentInfoRepo = new PaymentInfoRepository($this->db());

        $input = Session::getInputValidationResult();
        $input->setValue('first_name', $currentUser->getFirstName());
        $input->setValue('last_name', $currentUser->getLastName());

        $year = DashboardController::getCalcYear(false);

        if ($paymentInfoRepo->hasFulfilled($currentUser->getId(), $year)) {
            $resp->redirect($resp->getActionUrl('index') . '?year=' . $year);
        } elseif ($paymentInfoRepo->paymentFailed($currentUser->getId(), $year)) {
            $resp->setViewVar('payment_failed', true);
        } elseif ($paymentInfoRepo->isFulfillmentPending($currentUser->getId(), $year)) {
            $resp->setViewVar('fulfillment_pending', true);
        } else {
            $resp->setViewVar('payment_required', true);
        }

        $resp->setViewVar('payment_year', $year);

        $resp->setHtmlTitle('Zahlung für Premiumfeatures');
        $resp->renderView('payment');
    }

    public function PaymentDoAction(Response $resp): void
    {
        $this->abortIfUnauthorized($resp);
        $this->expectMethodPost();

        $currentUser = Session::getAuthorizedUser();
        $paymentInfoRepo = new PaymentInfoRepository($this->db());
        $userRepo = new UserRepository($this->db());

        $year = DashboardController::getCalcYear(false);
        $thisYear = intval((new DateTime('now', new DateTimeZone('Europe/Berlin')))->format('Y'));

        if ($paymentInfoRepo->hasFulfilled($currentUser->getId(), $year) || $paymentInfoRepo->isFulfillmentPending($currentUser->getId(), $year)) {
            $resp->redirect($resp->getActionUrl('index') . '?year=' . $year);
        }

        // ----------------------------- BEGIN input validation -----------------------------
        $input = InputValidator::parseAndValidate([
            new Input(INPUT_POST, 'first_name', 'Vorname', true),
            new Input(INPUT_POST, 'last_name', 'Nachname', true),
            new Input(INPUT_POST, 'iban', 'IBAN', true),
            new Input(INPUT_POST, 'bic', 'BIC', true),
            new Input(INPUT_POST, 'tos_accept', 'AGB', true),
            new Input(INPUT_POST, 'sepa_accept', 'SEPA Lastschriftmandat', true),
        ]);

        if (preg_match('/^[A-Z]{2}[0-9]{2}(?:[ ]?[0-9]{4}){4}(?!(?:[ ]?[0-9]){3})(?:[ ]?[0-9]{2})?$/', $input->getValue('iban')) === 0) {
            $input->setError('iban', 'Der eingegebene Wert muss eine gültige IBAN sein');
        }

        if (preg_match('/^[A-Z]{8}$/', $input->getValue('bic')) === 0) {
            $input->setError('bic', 'Der eingegebene Wert muss eine gültige BIC sein');
        }

        if ($input->getValue('tos_accept') !== '1') {
            $input->setError('tos_accept', 'Bitte bestätigen Sie durch ankreuzen');
        }

        if ($input->getValue('sepa_accept') !== '1') {
            $input->setError('sepa_accept', 'Bitte bestätigen Sie durch ankreuzen');
        }

        if ($input->hasErrors() && $input->getError('first_name') === '') {
            $input->setError('first_name', 'In einem der folgenden Felder ist ein Fehler aufgetreten.');
        }

        if ($year > $thisYear && $input->getError('first_name') === '') {
            $input->setError('first_name', 'Das ausgewählte Jahr liegt in der Zukunft, ein Report kann daher noch nicht erworben werden.');
        }

        if ($input->hasErrors()) {
            Session::setInputValidationResult($input);
            $resp->redirect($resp->getActionUrl('payment') . '?year=' . $year);
        }
        // ----------------------------- END input validation -----------------------------

        $currentUser->setFirstName(htmlspecialchars(trim($input->getValue('first_name'))));
        $currentUser->setLastName(htmlspecialchars(trim($input->getValue('last_name'))));


        try {
            if ($userRepo->update($currentUser) === false) {
                // failed to update user name
                $input->setError('first_name', 'Unbekannter Fehler aufgetreten');
                Session::setInputValidationResult($input);
                $resp->redirect($resp->getActionUrl('payment') . '`?year=' . $year);
            }

            $paymentInfoRepo->insert(new PaymentInfo(
                $currentUser->getId(),
                str_replace(' ', '', $input->getValue('iban')),
                str_replace(' ', '', $input->getValue('bic')),
                $year,
            ));
        } catch (Exception $e) {
            // already hat payment
        }

        $resp->redirect($resp->getActionUrl('index') . '?year=' . $year);
    }

}