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
            <div class="nav-item">
                <a class="flexbox" id="logo" href="<?= $this->_baseUrl ?>">
                    <img alt="CoinTax Logo" src="<?= $this->_baseUrl ?>img/logo.svg">
                    <h1 class="roboto-mono font-effect-anaglyph">CoinTax</h1>
                </a>
            </div>
            <?php if ($this->isAuthorized()): ?>
                <a href="<?= $this->getActionUrl('index', 'dashboard'); ?>"
                   class="nav-item <?= $this->_controllerName === 'Dashboard' ? 'active' : '' ?>">Übersicht</a>
                <a href="<?= $this->getActionUrl('index', 'order'); ?>"
                   class="nav-item <?= $this->_controllerName === 'Order' ? 'active' : '' ?>">Trades</a>
                <a href="<?= $this->getActionUrl('index', 'transaction'); ?>"
                   class="nav-item <?= $this->_controllerName === 'Transaction' ? 'active' : '' ?>">Transaktionen</a>
                <a href="<?= $this->getActionUrl('index', 'report'); ?>"
                   class="nav-item <?= $this->_controllerName === 'Report' ? 'active' : '' ?>">Gewinnreport</a>
            <?php endif; ?>
        </div>
        <div class="flexbox has-hover-child">
            <div class="nav-item flexbox">
                <span class="material-icons">account_circle</span> &nbsp; Account
            </div>
            <div class="open-on-hover dropdown-menu">
                <div class="flexbox flex-col">
                    <?php if ($this->isAuthorized()): ?>
                        <a href="<?= $this->getActionUrl('invoice', 'user'); ?>" class="nav-item">Rechnungen</a>
                        <hr>
                        <a href="<?= $this->getActionUrl('logout', 'login'); ?>" class="nav-item">Logout</a>
                    <?php else: ?>
                        <a href="<?= $this->getActionUrl('index', 'login'); ?>" class="nav-item">Login</a>
                        <a href="<?= $this->getActionUrl('index', 'register'); ?>"
                           class="nav-item">Registrieren</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>
</header>