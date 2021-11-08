<section class="flexbox flexbox-center">
    <div class="w12 m05 flexbox flex-start flex-col">
        <div class="flexbox w12">
            <h2 class="h2">Orderübersicht</h2>
            <a href="<?= $this->getActionUrl('add'); ?>">
                <button class="btn flexbox"><span class="material-icons">add_circle_outline</span>&nbsp; Order
                    hinzufügen
                </button>
            </a>
        </div>
    </div>
</section>

<section class="flexbox flexbox-center">
    <div class="w12 flexbox flex-start flex-col flex-gap">

        <?php foreach ($this->orders as $order): ?>
            <div class="w12 flexbox card">
                <div class="flexbox w2 flex-col flex-gap">
                    <span class="material-icons swap-icon">swap_horiz</span>
                    <span class="text-light"><?= $order['base']->getDatetimeUtc()->format('d.m.Y H:i'); ?> Uhr</span>
                </div>

                <div class="flexbox w8 flexbox-center">
                    <div class="flexbox flexbox-center flex-col w2 flex-gap">
                        <div><img class="token-symbol"
                                  src="<?= $order['baseCoin']->getThumbnailUrl(); ?>"
                                  alt="<?= $order['baseCoin']->getName(); ?>"></div>
                        <div class="text-light">
                            <?= number_format((float)$order['base']->getValue(), 8, ',', '.'); ?>
                            <?= $order['baseCoin']->getSymbol(); ?>
                        </div>
                    </div>
                    <div><span class="material-icons">chevron_right</span></div>
                    <div class="flexbox flexbox-center flex-col w2 flex-gap">
                        <div><img class="token-symbol"
                                  src="<?= $order['quoteCoin']->getThumbnailUrl(); ?>"
                                  alt="<?= $order['quoteCoin']->getName(); ?>"></div>
                        <div class="text-light">
                            <?= number_format((float)$order['quote']->getValue(), 8, ',', '.'); ?>
                            <?= $order['quoteCoin']->getSymbol(); ?>
                        </div>
                    </div>
                </div>

                <div class="flexbox w2">
                    <div></div>
                    <div>
                        <button class="loupe-btn no-btn">
                            <span class="material-icons loupe-icon text-light">loupe</span>
                        </button>
                    </div>
                </div>

                <div class="flexbox w12 swap-details">
                    <div class="w12">
                        <!-- More details -->
                    </div>
                </div>
            </div>
        <?php endforeach; ?>

    </div>
</section>