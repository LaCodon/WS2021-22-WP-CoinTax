<?php

use Framework\Form\TextInput;

?>
<div class="flexbox flexbox-center">

    <div class="flex-elem center-box">
        <div class="container">
            <h2 class="h2">Registrieren</h2>
            <div class="flexbox flexbox-center">
                <form style="width: 90%;" class="form" action="<?= $this->getDoActionUrl("register") ?>" method="post">
                    <?= TextInput::render('Vorname', 'firstname', placeholder: 'Vorname'); ?>
                    <?= TextInput::render('Nachname', 'lastname', placeholder: 'Nachname'); ?>
                    <?= TextInput::render('E-Mail', 'email', placeholder: 'mail@domain.de'); ?>
                    <?= TextInput::render('Passwort', 'password', type: 'password', placeholder: 'Passwort'); ?>
                    <?= TextInput::render('Passwort wiederholen', 'password-repeat', type: 'password', placeholder: 'Passwort wiederholen'); ?>
                    <hr>
                    <div class="form-elem">
                        <button class="btn" type="submit">Registrieren</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>