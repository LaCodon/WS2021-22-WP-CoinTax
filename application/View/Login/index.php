<?php

use Framework\Form\TextInput;

?>
<div class="flexbox flexbox-center">

    <div class="flex-elem center-box">
        <div class="container">
            <h2 class="h2">Einloggen</h2>
            <div class="flexbox flexbox-center">
                <form style="width: 90%;" class="form" action="<?= $this->getDoActionUrl("login") ?>" method="post">
                    <?= TextInput::render('E-Mail', 'email', placeholder: 'mail@domain.de'); ?>
                    <?= TextInput::render('Passwort', 'password', type: 'password', placeholder: 'Passwort'); ?>
                    <hr>
                    <div class="form-elem">
                        <button class="btn" type="submit">Login</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>