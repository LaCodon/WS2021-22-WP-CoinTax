<?php

use Core\Calc\Fifo\Fifo;


$totalTaxRelevantWinLoss = '0.0';
?>

<section class="flexbox flexbox-center">
    <div class="w12 m05 flexbox flex-start">
        <div class="flexbox w9 flex-start flex-col">
            <h2 class="h2">Gewinnreport <?= $this->calc_year; ?></h2>
        </div>
        <div class="w3 flexbox">
            Jahr
            <?php for ($y = $this->calc_year - 2; $y <= $this->calc_year + 1; ++$y): ?>
                <a href="<?= $this->getActionUrl('index') . '?year=' . $y; ?>">
                    <button class="btn default <?= $this->calc_year === $y ? 'active' : ''; ?>"><?= $y ?></button>
                </a>
            <?php endfor; ?>
        </div>
    </div>
</section>

<section class="flexbox flexbox-center m02">
    <div class="flexbox flexbox-center flex-col flex-gap">
        <div class="w12">
            <?php foreach ($this->report as $index => $report): ?>
                <?php
                if (count($report) === 0) {
                    continue;
                }

                $coin = $this->coins[$index];
                $totalTaxRelevantWinLossPerCoin = '0.00';
                ?>
                <h3 class="h3"><?= $coin->getName(); ?> <span class="hint"><?= $coin->getSymbol(); ?></span></h3>
                <?php foreach ($report as $compensation): ?>
                    <?php
                    $winLoss = $compensation[Fifo::ARRAY_ELEM_SALE]->calculateWinLoss($this->price_converter, $coin);
                    $totalTaxRelevantWinLossPerCoin = bcadd($totalTaxRelevantWinLossPerCoin, $winLoss->getTaxRelevantWinLoss());
                    ?>
                    <table class="table m02">
                        <thead class="table-head">
                        <tr>
                            <th>Verkaufszeitpunkt</th>
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
                                        <th class="no-border-right">Wert der verkauften Menge<br/>beim Kauf (Preis pro
                                            Coin)
                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody class="table-body">
                                    <?php foreach ($compensation[Fifo::ARRAY_ELEM_SALE]->getBackingFifoTransactions() as $backingTx): ?>
                                        <?php list($buyPrice, $coinCost) = $backingTx->getCurrentUsedEurValue($coin, $this->price_converter); ?>
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

                <table class="table m02">
                    <thead class="table-head">
                    </thead>
                    <tbody class="table-body">
                    <tr>
                        <td class="fix-width-col">Gewinnsumme für <?= $coin->getSymbol(); ?></td>
                        <td class="text-bold"><?= bcround($totalTaxRelevantWinLossPerCoin) ?> EUR</td>
                    </tr>
                    </tbody>
                </table>

                <?php
                $totalTaxRelevantWinLoss = bcadd($totalTaxRelevantWinLoss, $totalTaxRelevantWinLossPerCoin);
                ?>
            <?php endforeach; ?>
        </div>

        <div class="w12 m01">
            <div class="flexbox flex-gap flex-end flex-stretch">
                <h2 class="h2" style="margin-top: 6px">Steuerlich relevante Gewinnsumme:</h2>
                <div>
                    <span class="big-value no-margin <?= bccomp($totalTaxRelevantWinLoss, '0.0') < 0 ? 'red' : ''; ?>"><?= bcround($totalTaxRelevantWinLoss, 0); ?></span>
                    <span class="hint">EUR</span>
                </div>
            </div>
        </div>
    </div>
</section>