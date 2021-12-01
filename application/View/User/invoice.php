<section class="flexbox flexbox-center">
    <div class="w12 m05 flexbox flex-start flex-col">
        <div class="flexbox w12">
            <h2 class="h2">Rechnungsübersicht</h2>
        </div>
    </div>
</section>

<section class="flexbox flexbox-center">
    <div class="w12 flexbox flex-start flex-col flex-gap">

        <?php foreach ($this->payments as $paymentInfo): ?>

            <div class="w12 flexbox card">
                <div class="flexbox w2 flex-col flex-gap">
                    <a href="<?= $this->getActionUrl('index', 'report') . '?year=' . $paymentInfo->getYear() ?>"><span
                                class="material-icons swap-icon <?= $paymentInfo->isFulfilled() ? 'green' : ($paymentInfo->isFailed() ? 'red' : ''); ?>">receipt_long</span></a>
                    <span class="text-light">
                        <?= $paymentInfo->isFulfilled() ? 'Zahlung abgeschlossen' : ($paymentInfo->isFailed() ? 'Zahlung fehlgeschlagen' : 'Zahlung ausstehend'); ?>
                    </span>
                </div>

                <div class="flexbox w7 flexbox-center flex-col w2 flex-gap">
                    <div>Gewinnreport für das Steuerjahr <?= $paymentInfo->getYear() ?></div>
                    <div class="text-light">
                        <p><?= $this->user->getFirstName() ?> <?= $this->user->getLastName() ?></p>
                        <p>IBAN: <?= $paymentInfo->getIban() ?></p>
                        <p>BIC: <?= $paymentInfo->getBic() ?></p>
                    </div>
                </div>

                <div class="w3 flexbox">
                    <div class="text-light">
                        Rechnungsbetrag: 19,99 €
                    </div>
                </div>
            </div>

        <?php endforeach; ?>

        <?php if (count($this->payments) === 0): ?>
            <div class="flexbox w12 flex-center flex-top">
                <div class="container" id="no-orders-yet">Sie haben noch keinen Gewinnreport gekauft</div>
            </div>
        <?php endif; ?>

    </div>
</section>