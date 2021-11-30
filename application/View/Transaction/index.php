<?php

use Framework\Form\SelectInput;
use Framework\Form\TextInput;
use Framework\Html\Paginator;
use Model\Transaction;

?>
<section class="flexbox flexbox-center">
    <div class="w12 m05 flexbox flex-start flex-col">
        <div class="flexbox w12">
            <h2 class="h2">Transaktionsübersicht</h2>
            <a href="<?= $this->getActionUrl('add', 'order'); ?>">
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

        <?php foreach ($this->orders as $orderId => $order): ?>
            <?php if ($order['fee'] !== null): ?>
                <div class="w12 flexbox card">
                    <div class="flexbox w2 flex-col flex-gap">
                        <span class="material-icons swap-icon <?= $order['fee']->getType() === Transaction::TYPE_SEND ? 'red' : 'green' ?>"><?= $order['fee']->getType() === Transaction::TYPE_SEND ? 'arrow_upward' : 'arrow_downward' ?></span>
                        <span class="text-light"><?= $order['fee']->getDatetimeUtc()->setTimezone(new DateTimeZone('Europe/Berlin'))->format('d.m.Y H:i'); ?> Uhr</span>
                    </div>

                    <div class="flexbox w8 flexbox-center">
                        <div class="flexbox flexbox-center flex-col w2 flex-gap">
                            <div><img class="token-symbol"
                                      src="<?= $order['feeCoin']->getThumbnailUrl(); ?>"
                                      alt="<?= $order['feeCoin']->getName(); ?>"></div>
                            <div class="text-light text-center">
                                <?= format_number($order['fee']->getValue(), maxDecimals: 8); ?>
                                <?= $order['feeCoin']->getSymbol(); ?><br>
                                <span class="hint">Gebühr</span>
                            </div>
                        </div>
                    </div>

                    <div class="w2">
                        <div class="text-light">
                            Wert: <?= format_number($order['feeValue'], maxDecimals: 2); ?> EUR
                        </div>
                    </div>

                </div>
            <?php endif; ?>

            <?php if ($order['quote'] !== null): ?>
                <div class="w12 flexbox card">
                    <div class="flexbox w2 flex-col flex-gap">
                        <span class="material-icons swap-icon <?= $order['quote']->getType() === Transaction::TYPE_SEND ? 'red' : 'green' ?>"><?= $order['quote']->getType() === Transaction::TYPE_SEND ? 'arrow_upward' : 'arrow_downward' ?></span>
                        <span class="text-light"><?= $order['quote']->getDatetimeUtc()->setTimezone(new DateTimeZone('Europe/Berlin'))->format('d.m.Y H:i'); ?> Uhr</span>
                    </div>

                    <div class="flexbox w8 flexbox-center">
                        <div class="flexbox flexbox-center flex-col w2 flex-gap">
                            <div><img class="token-symbol"
                                      src="<?= $order['quoteCoin']->getThumbnailUrl(); ?>"
                                      alt="<?= $order['quoteCoin']->getName(); ?>"></div>
                            <div class="text-light">
                                <?= format_number($order['quote']->getValue(), maxDecimals: 8); ?>
                                <?= $order['quoteCoin']->getSymbol(); ?>
                            </div>
                        </div>
                    </div>

                    <div class="w2">
                        <div class="text-light">
                            Wert: <?= format_number($order['fiatValue'], maxDecimals: 2); ?> EUR
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($order['base'] !== null): ?>
                <div class="w12 flexbox card">
                    <div class="flexbox w2 flex-col flex-gap">
                        <span class="material-icons swap-icon <?= $order['base']->getType() === Transaction::TYPE_SEND ? 'red' : 'green' ?>"><?= $order['base']->getType() === Transaction::TYPE_SEND ? 'arrow_upward' : 'arrow_downward' ?></span>
                        <span class="text-light"><?= $order['base']->getDatetimeUtc()->setTimezone(new DateTimeZone('Europe/Berlin'))->format('d.m.Y H:i'); ?> Uhr</span>
                    </div>

                    <div class="flexbox w8 flexbox-center">
                        <div class="flexbox flexbox-center flex-col w2 flex-gap">
                            <div><img class="token-symbol"
                                      src="<?= $order['baseCoin']->getThumbnailUrl(); ?>"
                                      alt="<?= $order['baseCoin']->getName(); ?>"></div>
                            <div class="text-light">
                                <?= format_number($order['base']->getValue(), maxDecimals: 8); ?>
                                <?= $order['baseCoin']->getSymbol(); ?>
                            </div>
                        </div>
                    </div>

                    <div class="w2 flexbox">
                        <div class="text-light">
                            Wert: <?= format_number($order['fiatValue'], maxDecimals: 2); ?> EUR
                        </div>
                    </div>
                </div>
            <?php endif; ?>


            <div class="m01"></div>
        <?php endforeach; ?>

        <?php if (count($this->orders) === 0): ?>
            <div class="flexbox w12 flex-center flex-top">
                <div class="container" id="no-orders-yet">Füge zuerst deine Orders hinzu</div>
            </div>
        <?php else: ?>
            <?= Paginator::render($this->pagination_current_page, $this->pagination_items_per_page, $this->pagination_total_items); ?>
        <?php endif; ?>

    </div>
</section>