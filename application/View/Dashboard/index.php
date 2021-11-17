<section class="flexbox flexbox-center">
    <div class="w12 m05 flexbox flex-start flex-col">
        <div class="flexbox w12 flex-start flex-col">
            <h2 class="h2">Hallo <?= $this->firstname; ?>,</h2>
            <p>
                Willkommen bei deiner Portfolio-Übersicht!
            </p>
        </div>
    </div>
</section>

<section class="flexbox flexbox-center">
    <div class="w12 flexbox flex-gap flex-top">

        <div class="w8 flexbox card lr-padding">
            <div class="container w100">

                <?php if (!empty($this->coin_sums)): ?>

                    <div class="flexbox w100 coin-holding">
                        <div class="flexbox flex-start flex-center flex-stretch">
                            <div class="flexbox w2 flex-start flex-center">
                                <div class="text-bold">Token</div>
                            </div>
                            <div class="w3 text-bold text-right">Menge</div>
                        </div>
                        <div class="flexbox flex-col">
                            <div class="text-bold">Aktueller Wert</div>
                            <span class="win-lose-tag hint text-center">Gewinn / Verlust <br> durch Verkäufe 2021</span>
                        </div>
                    </div>

                    <?php foreach ($this->coin_sums as $symbol => $coinSum): ?>
                        <a href="<?= $this->getActionUrl('index', 'order') ?>?token=<?= $symbol ?>">
                            <div class="flexbox w100 coin-holding">
                                <div class="flexbox flex-start flex-center flex-stretch">
                                    <div class="flexbox w2 flex-start flex-center flex-gap">
                                        <img class="coin-holding-thumbnail"
                                             src="<?= $this->coins[$symbol]->getThumbnailUrl(); ?>"
                                             alt="<?= $symbol ?>">
                                        <div><span class="text-bold"><?= $this->coins[$symbol]->getName(); ?></span>
                                            <span
                                                    class="hint"><?= $symbol ?></span></div>
                                    </div>
                                    <div class="w3 text-right">
                                        <?= format_number($coinSum, 2, 8); ?> <?= $symbol ?>
                                    </div>
                                </div>
                                <div class="flexbox flex-col">
                                    <div>
                                        <?= format_number($this->coin_values[$symbol], 2, 2); ?> EUR
                                    </div>
                                    <span class="win-lose-tag <?= bccomp($this->win_lose_eur_per_coin[$symbol], 0) < 0 ? 'red' : '' ?>"><?= format_number($this->win_lose_eur_per_coin[$symbol], 2, 2); ?> EUR</span>
                                </div>
                            </div>
                        </a>
                    <?php endforeach; ?>

                <?php else: ?>
                    <div class="flexbox flex-col flex-center">
                        <div id="no-orders-yet">Füge zuerst deine Orders hinzu</div>
                        <a href="<?= $this->getActionUrl('add', 'order'); ?>">
                            <button class="btn flexbox"><span class="material-icons">add_circle_outline</span>&nbsp;Order
                                hinzufügen
                            </button>
                        </a>
                    </div>
                <?php endif; ?>

            </div>
        </div>

        <div class="flexbox w3 flex-col flex-gap">
            <div class="w3 flexbox card lr-padding">
                <div class="container w100">
                    <h2>Aktueller Portfoliowert</h2>
                    <span class="hint">Stand: <?= (new DateTime('now', new DateTimeZone('Europe/Berlin')))->format('d.m.Y H:i'); ?> Uhr</span>
                    <div class="big-value <?= bccomp($this->portfolio_value, 0) < 0 ? 'red' : '' ?>">
                        <?= format_number($this->portfolio_value, 2, 2) ?> EUR
                    </div>
                </div>
            </div>
            <div class="w3 flexbox card lr-padding">
                <div class="container w100">
                    <h2>Gesamtgewinn 2021</h2>
                    <span class="hint">Stand: <?= (new DateTime('now', new DateTimeZone('Europe/Berlin')))->format('d.m.Y H:i'); ?> Uhr</span>
                    <div class="big-value <?= bccomp($this->win_lose_eur_total, 0) < 0 ? 'red' : '' ?>">
                        <?= format_number($this->win_lose_eur_total, 2, 2); ?> EUR
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>