<?php

use Framework\Html\Paginator;
use Framework\Html\TradeFilterForm;
use Framework\Html\Transaction;

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

<?= TradeFilterForm::render($this) ?>

<section class="flexbox flexbox-center">
    <div class="w12 flexbox flex-start flex-col flex-gap">
        <?= Paginator::render($this->pagination_current_page, $this->pagination_items_per_page, $this->pagination_total_items); ?>
    </div>
</section>

<section class="flexbox flexbox-center">
    <div class="w12 flexbox flex-start flex-col flex-gap">

        <?php foreach ($this->orders as $order): ?>
            <?php if ($order['fee'] !== null): ?>
                <?= Transaction::render($order['orderId'], $order['fee'], $order['feeCoin'], $order['feeValue'], true); ?>
            <?php endif; ?>

            <?php if ($order['quote'] !== null): ?>
                <?= Transaction::render($order['orderId'], $order['quote'], $order['quoteCoin'], $order['fiatValue']); ?>
            <?php endif; ?>

            <?php if ($order['base'] !== null): ?>
                <?= Transaction::render($order['orderId'], $order['base'], $order['baseCoin'], $order['fiatValue']); ?>
            <?php endif; ?>

            <div class="m01"></div>
        <?php endforeach; ?>

        <div id="ajax-content" class="w12 flexbox flex-start flex-col flex-gap"></div>

        <?php if (count($this->orders) === 0): ?>
            <div class="flexbox w12 flex-center flex-top">
                <div class="container" id="no-orders-yet">Fügen Sie zuerst Ihre Trades hinzu</div>
            </div>
        <?php else: ?>
            <?= Paginator::render($this->pagination_current_page, $this->pagination_items_per_page, $this->pagination_total_items, true, $this->getActionUrl('querytransactions', 'api')); ?>
        <?php endif; ?>

        <div id="card-loading" class="w12 flexbox card"></div>

    </div>
</section>