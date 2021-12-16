<?php

use Framework\Form\SelectInput;
use Framework\Form\TextInput;
use Framework\Html\Paginator;

?>
<section class="flexbox flexbox-center">
    <div class="w12 m05 flexbox flex-start flex-col">
        <div class="flexbox w12">
            <h2 class="h2">Tradeübersicht <span class="hint">(<?= $this->pagination_total_items; ?>)</span></h2>
            <a href="<?= $this->getActionUrl('add'); ?>">
                <button class="btn flexbox"><span class="material-icons">add_circle_outline</span>&nbsp; Trade
                    hinzufügen
                </button>
            </a>
        </div>
    </div>
</section>

<section class="flexbox flexbox-center">
    <div class="w12 flexbox flex-start flex-col">
        <form method="GET" action="<?= $this->getActionUrl('index'); ?>" class="flexbox w12">
            <div class="flexbox m01 flex-gap">
                <div class="search-elem w3">
                    <?= TextInput::render('Von:', 'from', 'datetime-local', false); ?>
                </div>
                <div class="search-elem w3">
                    <?= TextInput::render('Bis:', 'to', 'datetime-local', false); ?>
                </div>
                <div class="search-elem w4">
                    <?= SelectInput::render('Token:', 'token', $this->coin_options, false); ?>
                </div>
            </div>
            <div class="flexbox flex-end w1">
                <button class="btn grey flexbox" type="submit"><span class="material-icons">filter_alt</span>&nbsp;
                    Filtern
                </button>
            </div>
        </form>
    </div>
</section>

<section class="flexbox flexbox-center">
    <div class="w12 flexbox flex-start flex-col flex-gap">
        <?= Paginator::render($this->pagination_current_page, $this->pagination_items_per_page, $this->pagination_total_items); ?>
    </div>
</section>

<section class="flexbox flexbox-center">
    <div class="w12 flexbox flex-start flex-col flex-gap">

        <?php foreach ($this->orders as $order): ?>
            <?php $orderId = $order['orderId'] ?>
            <div id="order-<?= $orderId; ?>" class="w12 flexbox card">
                <div class="flexbox w2 flex-col flex-gap">
                    <span class="material-icons swap-icon">swap_horiz</span>
                    <span class="text-light"><?= $order['base']->getDatetimeUtc()->setTimezone(new DateTimeZone('Europe/Berlin'))->format('d.m.Y H:i'); ?> Uhr</span>
                </div>

                <div class="flexbox w8 flexbox-center">
                    <div class="flexbox flexbox-center flex-col w2 flex-gap">
                        <div><img class="token-symbol"
                                  src="<?= $order['baseCoin']->getThumbnailUrl(); ?>"
                                  alt="<?= $order['baseCoin']->getName(); ?>"></div>
                        <div class="text-light">
                            <?= format_number($order['base']->getValue(), 2, 8); ?>
                            <?= $order['baseCoin']->getSymbol(); ?>
                        </div>
                    </div>
                    <div><span class="material-icons">chevron_right</span></div>
                    <div class="flexbox flexbox-center flex-col w2 flex-gap">
                        <div><img class="token-symbol"
                                  src="<?= $order['quoteCoin']->getThumbnailUrl(); ?>"
                                  alt="<?= $order['quoteCoin']->getName(); ?>"></div>
                        <div class="text-light">
                            <?= format_number($order['quote']->getValue(), 2, 8); ?>
                            <?= $order['quoteCoin']->getSymbol(); ?>
                        </div>
                    </div>
                </div>

                <div class="flexbox w2">
                    <div></div>
                    <div>
                        <button class="loupe-btn no-btn" data-toggle="order-toggle-<?= $orderId; ?>">
                            <span class="material-icons loupe-icon text-light">arrow_drop_down</span>
                        </button>
                    </div>
                </div>

                <div id="order-toggle-<?= $orderId; ?>" data-hide="true" class="flexbox w12 swap-details">
                    <div class="w12 flexbox flexbox-center">

                        <div class="w6 container">

                            <table class="table">
                                <thead class="table-head">
                                <tr>
                                    <th></th>
                                    <th>Token</th>
                                    <th>Menge</th>
                                    <th>Wert</th>
                                </tr>
                                </thead>
                                <tbody class="table-body">
                                <tr>
                                    <td>Gesendet</td>
                                    <td><?= $order['baseCoin']->getSymbol(); ?></td>
                                    <td><?= format_number($order['base']->getValue()); ?></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>Empfangen</td>
                                    <td><?= $order['quoteCoin']->getSymbol(); ?></td>
                                    <td><?= format_number($order['quote']->getValue()); ?></td>
                                    <td><?= format_number($order['fiatValue'], 2, 2); ?> EUR</td>
                                </tr>
                                <tr>
                                    <?php if ($order['fee'] !== null): ?>
                                        <td>Gebühren</td>
                                        <td><?= $order['feeCoin']->getSymbol(); ?></td>
                                        <td><?= format_number($order['fee']->getValue()); ?></td>
                                        <td><?= format_number($order['feeValue'], 2, 2); ?> EUR</td>
                                    <?php endif; ?>
                                </tr>
                                </tbody>
                            </table>

                        </div>
                        <div class="w1"></div>
                        <div class="w2 flexbox flex-col flex-gap flex-end flex-stretch">
                            <a class="flexbox flex-col flex-stretch"
                               href="<?= $this->getActionUrl('details'); ?>?id=<?= $orderId; ?>">
                                <button class="btn default flexbox flexbox-center flex-gap">
                                    <span class="material-icons">zoom_in</span>
                                    Details
                                </button>
                            </a>

                            <a class="flexbox flex-col flex-stretch"
                               href="<?= $this->getDoActionUrl('delete') ?>?id=<?= $orderId; ?>">
                                <button class="btn warning flexbox flexbox-center flex-gap"
                                        data-delete-order="<?= $orderId; ?>">
                                    <span class="material-icons">delete_outline</span>
                                    Trade löschen
                                </button>
                            </a>

                        </div>

                    </div>
                </div>
            </div>
        <?php endforeach; ?>

        <div id="ajax-content" class="w12 flexbox flex-start flex-col flex-gap"></div>

        <?php if (count($this->orders) === 0): ?>
            <div class="flexbox w12 flex-center flex-top">
                <div class="container" id="no-orders-yet">Keine Trades gefunden</div>
            </div>
        <?php else: ?>
            <?= Paginator::render($this->pagination_current_page, $this->pagination_items_per_page, $this->pagination_total_items, true, $this->getActionUrl('queryorders', 'api')); ?>
        <?php endif; ?>

        <div id="card-loading" class="w12 flexbox card"></div>

    </div>
</section>