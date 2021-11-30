<?php

use Framework\Form\CheckInput;
use Framework\Form\TextInput;

?>

<section class="flexbox flexbox-center">
    <div class="w12 m05 flexbox flex-start">
        <div class="flexbox w9">
            <h2 class="h2">Gewinnreport für <?= $this->payment_year ?> kaufen</h2>
        </div>
        <div class="w3 flexbox hide-on-print">
            Jahr
            <?php for ($y = $this->payment_year - 2; $y <= $this->payment_year + 1; ++$y): ?>
                <a href="<?= $this->getActionUrl('index') . '?year=' . $y; ?>">
                    <button class="btn default <?= $this->payment_year === $y ? 'active' : ''; ?>"><?= $y ?></button>
                </a>
            <?php endfor; ?>
        </div>
    </div>
</section>

<section class="flexbox flexbox-center">
    <div class="w12 flexbox flex-start flex-col flex-gap">

        <?php if (isset($this->payment_required)): ?>
            <div class="w12 flexbox card">
                <div class="flexbox w12">
                    <div class="container">
                        <p>
                            Die Erstellung eines Gewinnreports ist kostenpflichtig. Sobald Sie den Report für ein Jahr
                            erworben haben, können Sie diesen dauerhaft und wiederholt abrufen.
                        </p>
                        <p>
                            Für jedes Steuerjahr muss ein eigener Gewinnreport gekauft werden.
                        </p>
                        <p>
                            Über das unten stehende Formular können Sie den Gewinnreport für das
                            Jahr <?= $this->payment_year ?> erwerben.
                        </p>
                        <p>
                            Die Kosten für einen Gewinnreport betragen einmalig <b>19,99 €</b>
                        </p>
                    </div>
                </div>

                <div class="flexbox w12 swap-details">
                    <div class="flexbox flexbox-center w12">
                        <div class="card no-bg w7">
                            <div class="flexbox flexbox-center">
                                <form id="js-dynamic-form" style="width: 90%;" class="form"
                                      action="<?= $this->getDoActionUrl("payment") ?>?year=<?= $this->payment_year ?>"
                                      method="post">

                                    Im Folgenden werden Ihre Zahlungsdaten für das Lastschriftverfahren per SEPA
                                    abgefragt.

                                    <div class="form">

                                        <?= TextInput::render('Vorname', 'first_name'); ?>
                                        <?= TextInput::render('Nachname', 'last_name'); ?>
                                        <?= TextInput::render('IBAN <i class="hint">DE43 1234 5345 3565 3423 45</i>', 'iban', pattern: '[A-Z]{2}[0-9]{2}(?:[ ]?[0-9]{4}){4}(?!(?:[ ]?[0-9]){3})(?:[ ]?[0-9]{2})?'); ?>
                                        <?= TextInput::render('BIC <i class="hint">MBISCCDE</i>', 'bic', pattern: '[A-Z]{8}'); ?>
                                        <?= CheckInput::render('Ich akzeptiere die <a class="link">AGBs</a>', 'tos_accept'); ?>
                                        <?= CheckInput::render('Ich bestätige, dass CoinTax den Betrag in Höhe von 19,99€ von meinem Konto per Lastschriftverfahren einziehen darf', 'sepa_accept'); ?>

                                        <button id="js-next-btn" class="btn default hide">Weiter</button>

                                    </div>

                                    <hr>
                                    <div class="form-elem">
                                        <button class="btn" type="submit">Gewinnreport jetzt kostenpflichtig erwerben
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <?php if (isset($this->fulfillment_pending)): ?>
            <div class="w12 flexbox card">
                <div class="flexbox w12">
                    <div class="container">
                        <p>
                            Sie haben diesen Gewinnreport bereits bestellt. Die Zahlung wird aktuell noch von unserem
                            Backend verarbeitet und der Report für Sie erstellt.
                        </p>
                        <p>
                            Bitte haben Sie noch einige Minuten Geduld.
                        </p>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <?php if (isset($this->payment_failed)): ?>
            <div class="w12 flexbox">
                <div class="flexbox w12 warning-alert">
                    <p>
                        Bei der Durchführung der Zahlung für diesen Gewinnreport ist ein Fehler aufgetreten. Bitte
                        setzen Sie sich mit dem Support in Kontakt.
                    </p>
                </div>
            </div>
        <?php endif; ?>

    </div>
</section>