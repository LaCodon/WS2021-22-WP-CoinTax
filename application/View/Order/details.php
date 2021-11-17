<?php

use Core\Calc\Fifo\FifoTransaction;
use Core\Calc\PriceConverter;
use Model\Coin;

function getBuyPrice(FifoTransaction $backingTx, Coin $coin, PriceConverter $priceConverter): array
{
    $buyPrice = $priceConverter->getEurValueApiOptionalSingle($backingTx->getTransaction(), $coin);
    $buyPrice = bcdiv($buyPrice, $backingTx->getTransaction()->getValue());
    return [
        bcmul($buyPrice, $backingTx->getCurrentUsedAmount()),
        $buyPrice
    ];
}

?>
<section class="flexbox flexbox-center">
    <div class="w12 m05 flexbox flex-start flex-col">
        <div class="flexbox w12">
            <a href="javascript:window.close()" class="breadcrumb flexbox">
                <span class="material-icons">arrow_back</span><span>Schließen</span>
            </a>
            <div class="flexbox flex-gap">
                <a href="<?= $this->getActionUrl('edit'); ?>?id=<?= $this->order_id; ?>">
                    <button class="btn flexbox"><span class="material-icons">edit</span>&nbsp; Order bearbeiten
                    </button>
                </a>
                <a class="flexbox flex-col flex-stretch"
                   href="<?= $this->getDoActionUrl('delete') ?>?id=<?= $this->order_id; ?>">
                    <button class="btn warning flexbox flexbox-center flex-gap"
                            data-delete-order="<?= $this->order_id; ?>" data-closetab="true">
                        <span class="material-icons">delete_outline</span>
                        Order löschen
                    </button>
                </a>
            </div>
        </div>
    </div>
</section>

<section class="flexbox flexbox-center">
    <div class="flexbox flexbox-center flex-col flex-gap w13">
        <div class="w12 container m01 flexbox">
            <h2 class="h2">Orderdetails</h2>
        </div>
    </div>
</section>

<?php if (($this->base_data['success'] === false && $this->order_data['base']['coin']->getSymbol() !== PriceConverter::EUR_COIN_SYMBOL)
    || ($this->fee_data !== null && $this->fee_data['success'] === false)): ?>
    <section class="flexbox flexbox-center m01">
        <div class="flexbox flexbox-center flex-col flex-gap w12">
            <div class="w11 container flexbox warning-alert">
                Diese Order verkauft mehr Token als zuvor gekauft wurden. Für die fehlenden Token wird ein Kaufpreis von
                0,00 EUR angenommen. Dies kann zu einer Überschätzung des Gewinns führen. Fügen sie zusätzliche
                Kauf-Orders hinzu, um diesen Hinweis zu beseitigen.
            </div>
        </div>
    </section>
<?php endif; ?>

<section class="flexbox flexbox-center">
    <div class="w12 flexbox flex-start flex-col flex-gap">

        <div class="w12 flexbox card">
            <div class="flexbox w2 flex-col flex-gap">
                <span class="material-icons swap-icon">swap_horiz</span>
                <span class="text-light"><?= $this->order_data['base']['tx']->getDatetimeUtc()->setTimezone(new DateTimeZone('Europe/Berlin'))->format('d.m.Y H:i'); ?> Uhr</span>
            </div>

            <div class="flexbox w8 flexbox-center">
                <div class="flexbox flexbox-center flex-col w2 flex-gap">
                    <div><img class="token-symbol"
                              src="<?= $this->order_data['base']['coin']->getThumbnailUrl(); ?>"
                              alt="<?= $this->order_data['base']['coin']->getName(); ?>"></div>
                    <div class="text-light">
                        <?= format_number($this->order_data['base']['tx']->getValue(), 2, 8); ?>
                        <?= $this->order_data['base']['coin']->getSymbol(); ?>
                    </div>
                </div>
                <div><span class="material-icons">chevron_right</span></div>
                <div class="flexbox flexbox-center flex-col w2 flex-gap">
                    <div><img class="token-symbol"
                              src="<?= $this->order_data['quote']['coin']->getThumbnailUrl(); ?>"
                              alt="<?= $this->order_data['quote']['coin']->getName(); ?>"></div>
                    <div class="text-light">
                        <?= format_number($this->order_data['quote']['tx']->getValue(), 2, 8); ?>
                        <?= $this->order_data['quote']['coin']->getSymbol(); ?>
                    </div>
                </div>
            </div>

            <div class="flexbox w2"></div>

        </div>
    </div>
</section>

<section class="flexbox flexbox-center m02">
    <div class="flexbox flexbox-center flex-col flex-gap w13">
        <div class="w12">

            <table class="table">
                <thead class="table-head">
                <tr>
                    <th>Transaktionstyp</th>
                    <th>Token</th>
                    <th>Menge</th>
                    <th>Wert der Menge (Preis)</th>
                    <th>Zugehörige Käufe</th>
                </tr>
                </thead>
                <tbody class="table-body">
                <tr>
                    <td>Verkauf</td>
                    <td><?= $this->order_data['base']['coin']->getSymbol(); ?></td>
                    <td><?= format_number($this->order_data['base']['tx']->getValue()); ?></td>
                    <td><?= format_number($this->value_eur['base'], 2, 2); ?> EUR
                        (<?= format_number(bcdiv($this->value_eur['base'], $this->order_data['base']['tx']->getValue()), 2, 2); ?>
                        EUR)
                    </td>
                    <?php if ($this->order_data['base']['coin']->getSymbol() === PriceConverter::EUR_COIN_SYMBOL): ?>
                        <td class="hint">Für EUR werden keine Herkunftskäufe berechnet, da 1 EUR immer den Preis 1 EUR
                            hat.
                        </td>
                    <?php else: ?>
                        <td class="no-padding">
                            <table class="table">
                                <thead class="table-head">
                                <tr>
                                    <th>Zeitpunkt</th>
                                    <th>Gekaufte Menge</th>
                                    <th>In dieser Order<br/>verkaufte Menge</th>
                                    <th class="no-border-right">Wert der verkauften<br/>Menge beim Kauf (Preis)</th>
                                </tr>
                                </thead>
                                <tbody class="table-body">
                                <?php
                                $tokenSum = '0.0';
                                $fiatSum = '0.0';
                                ?>
                                <?php foreach ($this->base_data['sale']->getBackingFifoTransactions() as $backingTx): ?>
                                    <?php
                                    list($buyPrice, $coinCost) = getBuyPrice($backingTx, $this->order_data['base']['coin'], $this->price_converter);

                                    $tokenSum = bcadd($tokenSum, $backingTx->getCurrentUsedAmount());
                                    $fiatSum = bcadd($fiatSum, $buyPrice);
                                    ?>
                                    <tr>
                                            <td><?= $backingTx->getTransaction()->getDatetimeUtc()->setTimezone(new DateTimeZone('Europe/Berlin'))->format('d.m.Y H:i'); ?>
                                            Uhr
                                        </td>
                                        <td><?= format_number($backingTx->getTransaction()->getValue()); ?></td>
                                        <td><?= format_number($backingTx->getCurrentUsedAmount()); ?></td>
                                        <td class="no-border-right"><?= format_number($buyPrice, 2, 2); ?> EUR
                                            (<?= format_number($coinCost, 2, 2) ?> EUR)<br>
                                            <?php if (!$backingTx->isTaxRelevant()): ?><span class="hint">Kein steuerpflichtiger Verkauf</span><?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                <tr>
                                    <td class="no-border-bot">SUMME</td>
                                    <td class="no-border-bot"></td>
                                    <td class="no-border-bot"><?= format_number($tokenSum); ?></td>
                                    <td class="no-border-bot no-border-right"><?= format_number($fiatSum, 2, 2); ?>
                                        EUR
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </td>
                    <?php endif; ?>
                </tr>
                <tr>
                    <td>Kauf</td>
                    <td><?= $this->order_data['quote']['coin']->getSymbol(); ?></td>
                    <td><?= format_number($this->order_data['quote']['tx']->getValue()); ?></td>
                    <td><?= format_number($this->value_eur['quote'], 2, 2); ?> EUR
                        (<?= format_number(bcdiv($this->value_eur['quote'], $this->order_data['quote']['tx']->getValue()), 2, 2); ?>
                        EUR)
                    </td>
                    <td></td>
                </tr>
                <tr>
                    <?php if ($this->order_data['fee'] !== null): ?>
                        <td>Verkauf (Gebühren)</td>
                        <td><?= $this->order_data['fee']['coin']->getSymbol(); ?></td>
                        <td><?= format_number($this->order_data['fee']['tx']->getValue()); ?></td>
                        <td><?= format_number($this->value_eur['fee'], 2, 2); ?> EUR
                            (<?= format_number(bcdiv($this->value_eur['fee'], $this->order_data['fee']['tx']->getValue()), 2, 2); ?>
                            EUR)
                        </td>
                        <td class="no-padding">
                            <table class="table">
                                <thead class="table-head">
                                <tr>
                                    <th>Zeitpunkt</th>
                                    <th>Gekaufte Menge</th>
                                    <th>In dieser Order<br/>verkaufte Menge</th>
                                    <th class="no-border-right">Wert der verkauften<br/> Menge beim Kauf (Preis)</th>
                                </tr>
                                </thead>
                                <tbody class="table-body">
                                <?php
                                $feeTokenSum = '0.0';
                                $feeFiatSum = '0.0';
                                ?>
                                <?php foreach ($this->fee_data['sale']->getBackingFifoTransactions() as $backingTx): ?>
                                    <?php
                                    list($buyPrice, $coinCost) = getBuyPrice($backingTx, $this->order_data['fee']['coin'], $this->price_converter);

                                    $feeTokenSum = bcadd($feeTokenSum, $backingTx->getCurrentUsedAmount());
                                    $feeFiatSum = bcadd($feeFiatSum, $buyPrice);
                                    ?>
                                    <tr>
                                        <td><?= $backingTx->getTransaction()->getDatetimeUtc()->setTimezone(new DateTimeZone('Europe/Berlin'))->format('d.m.Y H:i'); ?>
                                            Uhr
                                        </td>
                                        <td><?= format_number($backingTx->getTransaction()->getValue()); ?></td>
                                        <td><?= format_number($backingTx->getCurrentUsedAmount()); ?></td>
                                        <td class="no-border-right"><?= format_number($buyPrice, 2, 2); ?> EUR
                                            (<?= format_number($coinCost, 2, 2) ?> EUR)
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                <tr>
                                    <td class="no-border-bot">SUMME</td>
                                    <td class="no-border-bot"></td>
                                    <td class="no-border-bot"><?= format_number($feeTokenSum); ?></td>
                                    <td class="no-border-bot no-border-right"><?= format_number($feeFiatSum, 2, 2); ?>
                                        EUR
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </td>
                    <?php endif; ?>
                </tr>
                </tbody>
            </table>

        </div>
        <div class="w12 m01">
            <?php if (isset($fiatSum)): ?>
                <div class="flexbox flex-gap flex-end flex-stretch">
                    <h2 class="h2" style="margin-top: 6px">Erzielter Gewinn durch Verkauf des Tokens:</h2>
                    <div>
                        <?php
                        $winLoss = bcsub($this->value_eur['base'], $fiatSum);
                        ?>
                        <span class="big-value no-margin <?= bccomp($winLoss, '0.0') < 0 ? 'red' : ''; ?>"><?= format_number($winLoss, 2, 2) ?></span>
                        <span class="hint">EUR</span>
                    </div>
                </div>
            <?php endif; ?>
            <?php if (isset($feeFiatSum)): ?>
                <div class="flexbox flex-gap flex-end flex-stretch m01">
                    <h2 class="h2" style="margin-top: 6px">Erzielter Gewinn durch Verkauf des Gebühr-Tokens:</h2>
                    <div>
                        <?php
                        $winLoss = bcsub($this->value_eur['fee'], $feeFiatSum);
                        ?>
                        <span class="big-value no-margin <?= bccomp($winLoss, '0.0') < 0 ? 'red' : ''; ?>"><?= format_number($winLoss, 2, 2) ?></span>
                        <span class="hint">EUR</span>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>
