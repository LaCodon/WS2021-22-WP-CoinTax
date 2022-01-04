
<footer class="flexbox flexbox-center">
    <div class="flexbox w12 flex-start">
        <div class="w3">
            <h2 class="h2 roboto-mono">CoinTax</h2>
            <p>&#9400; 2021</p>
            <p>Fabian Maier</p>
            <p>Master Angewandte Informatik</p>
            <p>Matrikelnummer: 120054321</p>
        </div>
        <div>
            <p><a class="text-white" href="<?= $this->getActionUrl('impressum', 'index'); ?>">Impressum</a></p>
            <p><a class="text-white" href="<?= $this->getActionUrl('privacy', 'index'); ?>">Datenschutzerklärung</a></p>
            <p><a class="text-white" href="<?= $this->getActionUrl('documentation', 'index'); ?>">Dokumentation</a></p>
        </div>
    </div>
</footer>

<script type="module" src="<?= $this->_baseUrl ?>js/script.js"></script>

</body>
</html>