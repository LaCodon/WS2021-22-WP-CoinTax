<section class="flexbox flexbox-center">
    <div class="w12 m05 flexbox flex-start flex-col">
        <div class="flexbox w12">
            <h2 class="h2">Hallo <?= $this->firstname; ?></h2>
        </div>
    </div>
</section>

<section class="flexbox flexbox-center">
    <div class="w12 flexbox flex-gap flex-top">

        <div class="w8 flexbox card lr-padding">
            <div class="container">
                df
            </div>
        </div>

        <div class="w3 flexbox card lr-padding">
            <div class="container w100">
                <h2>Portfoliowert</h2>
                <span class="hint">Stand: 10.11.2021</span>
                <div class="big-value">
                    <?= format_number($this->portfolio_value, 2, 2) ?> EUR
                </div>
            </div>
        </div>

    </div>
</section>