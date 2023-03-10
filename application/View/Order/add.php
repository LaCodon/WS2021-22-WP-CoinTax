<?php

use Framework\Form\SelectInput;
use Framework\Form\TextInput;

?>
<section class="flexbox flexbox-center">
    <div class="w12 m05 flexbox flex-start flex-col">
        <div class="flexbox w12">
            <h2 class="h2"><?= isset($this->edit_order) ? 'Trade bearbeiten' : 'Trade hinzufügen'; ?></h2>
            <a href="<?= isset($this->edit_order) ? $this->getActionUrl('details') . '?id=' . $this->order_id : $this->getActionUrl('index') . '?' . $this->back_filter; ?>">
                <button class="btn warning flexbox"><span class="material-icons">highlight_off</span>&nbsp;Abbrechen
                </button>
            </a>
        </div>
    </div>
</section>

<section class="flexbox flexbox-center">
    <div class="w12 flexbox flex-start flex-col flex-gap">

        <div class="w12 flexbox card">
            <div class="flexbox w2 flex-col flex-gap">
                <span class="material-icons swap-icon">swap_horiz</span>
            </div>

            <div class="flexbox w8 flexbox-center">
                <div class="flexbox flexbox-center flex-col w2 flex-gap">
                    <div><span class="material-icons medium">account_balance</span></div>
                </div>
                <div><span class="material-icons">chevron_right</span></div>
                <div class="flexbox flexbox-center flex-col w2 flex-gap">
                    <div><span class="material-icons medium">account_balance_wallet</span></div>
                </div>
            </div>

            <div class="flexbox w2">

            </div>

            <div class="flexbox w12 swap-details">
                <div class="flexbox flexbox-center w12">
                    <div class="card no-bg w7">
                        <div class="flexbox flexbox-center">
                            <form style="width: 90%;" class="form"
                                  action="<?= isset($this->edit_order) ? $this->getDoActionUrl("edit") : $this->getDoActionUrl("add") ?>"
                                  method="post">

                                <?php if (isset($this->edit_order)): ?>
                                    <input type="hidden" name="id" value="<?= $this->order_id; ?>">
                                <?php endif; ?>

                                <?= TextInput::render('Ausführungszeitpunkt (MEZ)', 'datetime', type: 'datetime-local'); ?>

                                <div class="flexbox">
                                    <div class="w3">
                                        <?= TextInput::render('Menge', 'send_amount', placeholder: '11,5', pattern: '^[0-9]+([,.]{1}[0-9]+){0,1}$'); ?>
                                    </div>
                                    <div class="w3">
                                        <?= SelectInput::render('Gesendetes Token', 'send_token', $this->coin_options); ?>
                                    </div>
                                </div>

                                <div class="flexbox">
                                    <div class="w3">
                                        <?= TextInput::render('Menge', 'receive_amount', placeholder: '11,5', pattern: '^[0-9]+([,.]{1}[0-9]+){0,1}$'); ?>
                                    </div>
                                    <div class="w3">
                                        <?= SelectInput::render('Empfangenes Token', 'receive_token', $this->coin_options); ?>
                                    </div>
                                </div>

                                <div class="flexbox">
                                    <div class="w3">
                                        <?= TextInput::render('Menge', 'fee_amount', required: false, placeholder: '11,5', pattern: '^[0-9]*([,.]{1}[0-9]+){0,1}$'); ?>
                                    </div>
                                    <div class="w3">
                                        <?= SelectInput::render('Gebühren Token', 'fee_token', $this->coin_options, false); ?>
                                    </div>
                                </div>

                                <hr>
                                <div class="form-elem">
                                    <button class="btn" type="submit">Speichern</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>


    </div>
</section>

<script type="application/javascript">
    function padNumber(x) {
        if (x < 10) {
            return `0${x}`
        }

        return x
    }

    if (document.querySelector("[name=datetime]").value === '') {
        // file datetime field with current datetime
        const now = new Date()
        document.querySelector("[name=datetime]").value =
            `${now.getFullYear()}-${padNumber(now.getMonth() + 1)}-${padNumber(now.getDate())}T${padNumber(now.getHours())}:${padNumber(now.getMinutes())}`
    }
</script>