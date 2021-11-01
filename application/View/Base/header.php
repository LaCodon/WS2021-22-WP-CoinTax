<!DOCTYPE html>
<html lang="de">
<head>
    <title>CoinTax | <?= $this->htmlTitle ?></title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Icons">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto+Mono&effect=anaglyph&text=CoinTax">
    <link rel="stylesheet" href="<?= $this->_baseUrl ?>css/style.css">
</head>
<body>

<header>
    <nav class="flexbox">
        <div class="flexbox">
            <div class="flex-elem nav-item">
                <a class="flexbox" id="logo" href="<?= $this->_baseUrl ?>">
                    <img class="flex-elem" alt="CoinTax Logo" src="<?= $this->_baseUrl ?>img/logo.svg">
                    <h1 class="flex-elem roboto-mono font-effect-anaglyph">CoinTax</h1>
                </a>
            </div>
            <?php if ($this->isAuthorized()): ?>
                <a href="." class="flex-elem nav-item">Übersicht</a>
                <a href="." class="flex-elem nav-item">Orders</a>
                <a href="." class="flex-elem nav-item">Transaktionen</a>
            <?php endif; ?>
        </div>
        <div class="flexbox has-hover-child">
            <div class="flex-elem nav-item">
                Account
            </div>
            <div class="open-on-hover dropdown-menu">
                <div class="flexbox flex-col">
                    <?php if ($this->isAuthorized()): ?>
                        <a href="<?= $this->getActionUrl('logout', 'login'); ?>" class="flex-elem nav-item">Logout</a>
                    <?php else: ?>
                        <a href="<?= $this->getActionUrl('index', 'login'); ?>" class="flex-elem nav-item">Login</a>
                        <a href="<?= $this->getActionUrl('index', 'register'); ?>"
                           class="flex-elem nav-item">Registrieren</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>
</header>