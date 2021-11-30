<?php

use Core\Calc\Fifo\Fifo;

?>

<section class="flexbox flexbox-center">
    <div class="w12 m05 flexbox flex-start">
        <div class="flexbox w9 flex-start flex-col">
            <h2 class="h2">Gewinnreport <?= $this->calc_year; ?></h2>
        </div>
        <div class="w3 flexbox hide-on-print">
            Jahr
            <?php for ($y = $this->calc_year - 2; $y <= $this->calc_year + 1; ++$y): ?>
                <a href="<?= $this->getActionUrl('index') . '?year=' . $y; ?>">
                    <button class="btn default <?= $this->calc_year === $y ? 'active' : ''; ?>"><?= $y ?></button>
                </a>
            <?php endfor; ?>
        </div>
    </div>
</section>

<section class="flexbox flexbox-center">
    <div class="flexbox flexbox-center flex-col flex-gap">
        <div class="w12">
            <div class="w12 m01">
                <div class="flexbox flex-gap flex-end flex-stretch">
                    <h2 class="h2" style="margin-top: 6px">Steuerlich relevante Gewinnsumme:</h2>
                    <div>
                        <span class="big-value no-margin <?= bccomp($this->report['totalTaxRelevantWinLoss'], '0.0') < 0 ? 'red' : ''; ?>"><?= bcround($this->report['totalTaxRelevantWinLoss'], 2); ?></span>
                        <span class="hint">EUR</span>
                    </div>
                </div>
            </div>

            <div class="w12 m01">
                <div class="flexbox flex-gap flex-end flex-stretch">
                    <h2 class="h2" style="margin-top: 6px">Gezahlte Gebühren:</h2>
                    <div>
                        <span class="big-value no-margin red"><?= bcround($this->report['totalPaidFeesEur'], 2); ?></span>
                        <span class="hint">EUR</span>
                    </div>
                </div>
            </div>

            <div class="w12 m01">
                <div class="flexbox flex-gap flex-end flex-stretch">
                    <h2 class="h2" style="margin-top: 6px">Steuerlich relevanter Reingewinn:</h2>
                    <div>
                        <span class="big-value no-margin <?= bccomp($this->report['cleanedWinLoss'], '0.0') < 0 ? 'red' : ''; ?>"><?= bcround($this->report['cleanedWinLoss'], 2); ?></span>
                        <span class="hint">EUR</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="flexbox flexbox-center m02">
    <div class="flexbox flexbox-center flex-col flex-gap">
        <div class="w12">
            <?php foreach ($this->report['coinReports'] as $key => $coinReport): ?>
                <?php
                if (count($coinReport['compensationReports']) === 0) {
                    continue;
                }
                ?>
                <button class="no-btn pointer" data-toggle="report-toggle-<?= $key; ?>">
                    <h3 class="h3 flexbox flex-start flex-center flex-gap"><?= $coinReport['coin']->getName(); ?> <div
                                class="hint"><?= $coinReport['coin']->getSymbol(); ?></div>
                        <span class="material-icons loupe-icon text-light">arrow_drop_down</span>
                    </h3>
                </button>

                <div id="report-toggle-<?= $key; ?>">
                    <?php foreach ($coinReport['compensationReports'] as $compensationReport): ?>
                        <?php
                        $compensation = $compensationReport['compensation'];
                        $winLoss = $compensationReport['winLoss'];
                        $isFee = $compensationReport['isFee'];
                        ?>
                        <table class="table m02">
                            <thead class="table-head">
                            <tr>
                                <th class="lh25">
                                    <?php if ($isFee): ?><span class="tag">Gebührenzahlung</span><br/><?php endif; ?>
                                    Verkaufszeitpunkt
                                </th>
                                <th>Verkaufsmenge * Preis pro Coin = Wert</th>
                                <th>Steuerlich relevante<br/>Verkaufsmenge / Wert</th>
                                <th>Steuerlich relevanter<br/>Kaufwert</th>
                                <th>Steuerlich relevanter Gewinn</th>
                            </tr>
                            </thead>
                            <tbody class="table-body">
                            <tr>
                                <td><?= $compensation[Fifo::ARRAY_ELEM_SALE]->getSellTransaction()->getDatetimeUtc()->setTimezone(new DateTimeZone('Europe/Berlin'))->format('d.m.Y H:i'); ?>
                                    Uhr
                                </td>
                                <td>
                                    <?= format_number($compensation[Fifo::ARRAY_ELEM_SALE]->getSellTransaction()->getValue()); ?>
                                    *
                                    <?= bccomp($compensation[Fifo::ARRAY_ELEM_SALE]->getSellTransaction()->getValue(), '0.00') !== 0 ? bcround(bcdiv($winLoss->getTotalSoldEurSum(), $compensation[Fifo::ARRAY_ELEM_SALE]->getSellTransaction()->getValue())) : '0,00' ?>
                                    EUR =
                                    <?= bcround($winLoss->getTotalSoldEurSum()); ?> EUR
                                </td>
                                <td>
                                    <?= format_number($winLoss->getTaxableAmount()) ?> /
                                    <?= bcround($winLoss->getTaxableSoldEurSum()); ?> EUR
                                </td>
                                <td>
                                    <?= bcround($winLoss->getTaxableBoughtEurSum()); ?> EUR
                                </td>
                                <td><?= bcround($winLoss->getTaxRelevantWinLoss()); ?> EUR</td>
                            </tr>
                            <tr>
                                <td>Zusammensetzung<br/>des Verkaufs</td>
                                <td colspan="3" class="no-padding">
                                    <table class="table">
                                        <thead class="table-head">
                                        <tr>
                                            <th>Zeitpunkt</th>
                                            <th>Gekaufte Menge</th>
                                            <th>In diesem Trade<br/>verkaufte Menge</th>
                                            <th class="no-border-right">Wert der verkauften Menge<br/>beim Kauf (Preis
                                                pro
                                                Coin)
                                            </th>
                                        </tr>
                                        </thead>
                                        <tbody class="table-body">
                                        <?php foreach ($compensation[Fifo::ARRAY_ELEM_SALE]->getBackingFifoTransactions() as $backingTx): ?>
                                            <?php list($buyPrice, $coinCost) = $backingTx->getCurrentUsedEurValue($coinReport['coin'], $this->price_converter); ?>
                                            <tr>
                                                <td><?= $backingTx->getTransaction()->getDatetimeUtc()->setTimezone(new DateTimeZone('Europe/Berlin'))->format('d.m.Y H:i'); ?>
                                                    Uhr
                                                </td>
                                                <td><?= format_number($backingTx->getTransaction()->getValue()); ?></td>
                                                <td><?= format_number($backingTx->getCurrentUsedAmount()); ?></td>
                                                <td class="no-border-right"><?= bcround($buyPrice); ?> EUR
                                                    (<?= bcround($coinCost) ?> EUR)<br>
                                                    <?php if (!$backingTx->isTaxRelevant()): ?><span class="hint">Kein steuerpflichtiger Verkauf</span><?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                        <tr>
                                            <td class="no-border-bot">SUMME</td>
                                            <td class="no-border-bot">
                                                <?php if (!$compensation[Fifo::ARRAY_ELEM_SUCCESS]): ?>
                                                    <span class="tag red">
                                                <?= $compensation[Fifo::ARRAY_ELEM_SUCCESS] ? '' : 'fehlende Kauftransaktion' ?>
                                            </span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="no-border-bot"><?= format_number($winLoss->getTotalAmount()); ?></td>
                                            <td class="no-border-bot no-border-right"><?= bcround($winLoss->getTotalBoughtEurSum()); ?>
                                                EUR
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    <?php endforeach; ?>
                </div>

                <table class="table m02">
                    <thead class="table-head">
                    </thead>
                    <tbody class="table-body">
                    <tr>
                        <td class="fix-width-col">Gewinnsumme für <?= $coinReport['coin']->getSymbol(); ?></td>
                        <td class="text-bold"><?= bcround($coinReport['totalTaxRelevantWinLoss']) ?> EUR</td>
                    </tr>
                    </tbody>
                </table>
            <?php endforeach; ?>

            <div class="hide-on-print">
                <button class="btn grey" onclick="window.print()">Drucken</button>
            </div>
        </div>
    </div>
</section>