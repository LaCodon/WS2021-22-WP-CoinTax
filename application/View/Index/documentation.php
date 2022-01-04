<section class="flexbox flexbox-center">
    <div class="w12 flexbox flex-gap flex-top">

        <div class="w12 flexbox m01 lr-padding">
            <div class="richtext container w100">
                <h2>Projektdokumentation</h2>

                <h3>Inhalt</h3>

                <ol>
                    <li><a class="link" href="#aufgabenstellung">Zielgruppe und Aufgabenstellung</a></li>
                    <li><a class="link" href="#steuerbasics">Steuerrechtliche Grundlagen</a></li>
                    <li><a class="link" href="#andereseiten">Vergleichbare Seiten im Internet</a></li>
                    <li><a class="link" href="#navigation">Navigationsstruktur</a></li>
                    <li><a class="link" href="#design">Designauswahl</a></li>
                    <li><a class="link" href="#funktionen">Beschreibung der Seitenfunktionalitäten</a></li>
                    <li><a class="link" href="#datenmodell">ER-Modell und relationales Modell</a></li>
                    <li><a class="link" href="#rollenmodell">Rollenmodell</a></li>
                    <li><a class="link" href="#codedoc">Codestruktur</a></li>
                    <li><a class="link" href="#checkliste">Abgleich mit Anforderungscheckliste</a></li>
                    <li><a class="link" href="#reflexion">Reflektion</a></li>

                </ol>

                <h3 id="aufgabenstellung">1. Zielgruppe und Aufgabenstellung</h3>

                <p>
                    Die Zielgruppe von CoinTax umfasst Privatpersonen in Deutschland bzw. Privatpersonen, die deutschem
                    Steuerrecht unterliegen und den Handel mit Kryptowährungen betreiben.
                </p>

                <p>
                    Reingewinne aus dem Handel mit Kryptowährungen sind in Deutschland steuerpflichtig. Die Gewinne
                    müssen daher in der Steuererklärung angegeben werden. Solange eine Person nur zwischen Euro und
                    einer oder mehreren Kryptowährungen handelt, ist die Berechnung der Gewinne noch vergleichsweise
                    einfach zu bewerkstelligen (Beispiel EUR -> BTC: Bitcoin mit Euro kaufen). Sobald jedoch auch
                    zwischen verschiedenen Kryptowährungen gehandelt wird (Beispiel: BTC -> ETH: Ethereum mit Bitcoin
                    kaufen), erschwert das die Berechnung bereits erheblich.
                </p>

                <p>
                    Das Ziel von CoinTax ist es, Nutzer bei der Berechnung ihrer Reingewinne (Gewinne abzüglich
                    Gebühren) zu unterstützen. Dabei soll CoinTax steuerrechtliche Besonderheiten, Freibeträge und
                    anderes berücksichtigen. Außerdem soll CoinTax die Möglichkeit bieten, eine Aufschlüsselung der
                    Berechnung des Reingewinns zu exportieren. Diesen Export benötigt ein Nutzer, falls es zu einer
                    Steuerprüfung durch das Finanzamt kommt und Nachweise eingefordert werden.
                </p>

                <h3 id="steuerbasics">2. Steuerrechtliche Grundlagen</h3>

                <p>
                    Die folgenden steuerrechtlichen Regelungen in Deutschland werden von CoinTax unterstützt:
                </p>

                <ul>
                    <li>
                        Wenn eine gekaufte Kryptowährung länger als ein Jahr im Portfolio gehalten wird, ist der Verkauf
                        dieser Token steuerfrei.
                    </li>
                    <li>
                        Gewinne werden nach der FIFO Methode berechnet: Zuerst gekaufte Token werden auch zuerst wieder
                        verkauft.
                    </li>
                    <li>
                        Gewinne werden immer im Euro-Wert der Kryptowährung berechnet. Dabei wird jeweils der Marktwert
                        zum Zeitpunkt der Kaufs- oder Verkaufstransaktion herangezogen.
                    </li>
                    <li>
                        Reingewinne berechnen sich durch: Verkaufswert - Einkaufswert - Gebühren
                    </li>
                </ul>

                <p>
                    Hinweis 1: Ein Verkauf kann Token aus mehreren verschiedenen Käufen verkaufen. Dann muss für jeden
                    Kauf einzeln die Höhe des Kaufpreises für die Gewinnberechnung Berücksichtigt werden. Außerdem muss
                    auch für jeden Kauf einzeln geprüft werden, ob die Haltedauer ein Jahr übersteigt und der Verkauf
                    dieser speziellen Token daher steuerfrei ist.
                </p>

                <p>
                    Hinweis 2: Beim Kauf einer Kryptowährung mittels einer anderen Kryptowährung (statt Euro) entsteht
                    ein impliziter Verkauf. Beispiel ETH -> BTC: Dabei wird steuerrechtlich zunächst ETH
                    verkauft(!) und anschließend BTC gekauft. Dementsprechend ist die Transaktion steuerrelevant obwohl
                    auf den ersten Blick eigentlich nur ein Kauf von BTC stattfindet. Gleiches gilt auch bei der Zahlung
                    von Gebühren mittels einer Kryptowährung. Dabei werden die Token ebenfalls erst in der benötigten
                    Menge verkauft und davon wiederum die Gebühr bezahlt.
                </p>

                <h3 id="andereseiten">3. Vergleichbare Seiten im Internet</h3>

                <p>
                    Es gibt im Internet bereits eine Menge von Anbietern, die sich auf die Berechnung der zu
                    versteuernden Gewinne beim Handel mit Kryptowährungen spezialisiert haben. Darunter unter anderem:
                </p>

                <ul>
                    <li><a class="link" target="_blank" href="https://koinly.io/de/">Koinly</a></li>
                    <li><a class="link" target="_blank" href="https://cointracking.info/">CoinTracking</a></li>
                    <li><a class="link" target="_blank" href="https://cryptotax.io/">Cryptotax</a></li>
                </ul>

                <p>
                    Eine Nutzung dieser Services ist in geringem Umfang (bis zu 25 Transaktionen pro Jahr) meist
                    kostenlos. Darüber hinaus entstehen Nutzungsgebühren, je nach dem wie hoch die Anzahl der zu
                    verarbeitenden Transaktionen ist.
                </p>

                <div class="text-center">
                    <img width="700" src="<?= $this->_baseUrl ?>img/doc/koinly-screenshot.png"
                         alt="Screenshot von Koinly">
                    <label class="hint">Abbildung: Screenshot von Koinly.io</label>
                </div>

                <p>
                    Die genannten Services unterstützen - neben der Gewinnberechnung wie CoinTax - alle den
                    Transaktionsimport von populären zentralen und dezentralen Marktplätzen wie
                    <a class="link" target="_blank" href="https://www.binance.com/en">Binance.com</a>,
                    <a class="link" target="_blank" href="https://uniswap.org/">Uniswap</a>
                    und <a class="link" target="_blank" href="https://pancakeswap.finance/">pancakeswap.finance</a>. Da
                    die Exportformate und APIs je nach Marktplatz sehr unterschiedlich sind, wurde diese Funktionalität
                    in CoinTax (bisher) nicht eingebaut. Der Nutzer muss die eigenen Transaktionen selbst eingeben.
                </p>

                <p>
                    Ausnahme: Um während der Entwicklung schnell Testdaten erzeugen zu können, wurde ein Skript zum
                    Import von Binance-Transaktionen geschrieben. Dieses ist unter <code>/scripts/importOrder.php</code>
                    abgelegt und kann mit der Kommandozeile ausgeführt werden.
                </p>

                <p>
                    Neben normalem Handel mit Tokens gibt es im Kryptobereich auch viele andere Möglichkeiten, Gewinne
                    zu erzielen. Dazu zählt zum Beispiel das Lending oder das Staking. Diese Gewinne unterliegen eigenen
                    steuerrechtlichen Gesetzen in Deutschland und können von CoinTax daher nicht verarbeitet werden. Die
                    genannten online Service hingegen bieten diese Funktion.
                </p>

                <h3 id="navigation">4. Navigationsstruktur</h3>

                <pre>
                    ├─ Startseite
                    ├─ Login
                    ├─ Registrieren
                    ├─ Nutzerbereich
                    │   ├─ Dashboard
                    │   │   ├─ Jahr 2020
                    │   │   └─ Jahr ...
                    │   ├─ Trades
                    │   │   ├─ Hinzufügen
                    │   │   ├─ Ansehen
                    │   │   └─ Bearbeiten
                    │   ├─ Transaktionen
                    │   │   └─ Ansehen
                    │   ├─ Gewinnreports
                    │   │   ├─ Jahr 2020
                    │   │   └─ Jahr ...
                    │   └─ Rechnungen
                    ├─ Impressum
                    ├─ Datenschutzerklärung
                    └─ Dokumentation
                </pre>

                <div class="text-center">
                    <img width="600" src="<?= $this->_baseUrl ?>img/doc/Navigationsbaum.svg"
                         alt="Navigationsbaum">
                    <label class="hint">Abbildung: Navigationsbaum für eingeloggte Benutzer</label>
                </div>

                <h3 id="design">5. Designauswahl</h3>

                <p>
                    Das Design von CoinTax ist stark von Koinly.io inspiriert, nutzt größtenteils CSS-Flexboxes und ist
                    nicht responsiv. Da diese Arbeit nur für das Modul Dynamische Webentwicklung entwickelt wurde, wird
                    hier nicht weiter auf Designbesonderheiten eingegangen.
                </p>

                <h3 id="funktionen">6. Beschreibung der Seitenfunktionalitäten</h3>

                <h4>Registrieren</h4>

                <div class="text-center">
                    <img src="<?= $this->_baseUrl ?>img/doc/ScreenshotRegistrieren.png"
                         alt="Screenshot Registrierung">
                    <label class="hint">Abbildung: Screenshot der Registrierung</label>
                </div>

                <h4>Login</h4>

                <div class="text-center">
                    <img src="<?= $this->_baseUrl ?>img/doc/ScreenshotLogin.png"
                         alt="Screenshot Login">
                    <label class="hint">Abbildung: Screenshot des Logins</label>
                </div>

                <h4>Dashboard</h4>

                <div class="text-center">
                    <img src="<?= $this->_baseUrl ?>img/doc/ScreenshotDashboard.png"
                         alt="Screenshot Dashboard">
                    <label class="hint">Abbildung: Screenshot des Dashboards</label>
                </div>

                <h4>Trades anzeigen</h4>

                <div class="text-center">
                    <img src="<?= $this->_baseUrl ?>img/doc/ScreenshotTradeübersicht.png"
                         alt="Screenshot Trades">
                    <label class="hint">Abbildung: Screenshot der Tradeübersicht</label>
                </div>

                <h4>Trades bearbeiten</h4>

                <div class="text-center">
                    <img src="<?= $this->_baseUrl ?>img/doc/ScreenshotTradeBearbeiten.png"
                         alt="Screenshot Trade bearbeiten">
                    <label class="hint">Abbildung: Screenshot der Bearbeitungsseite für Trades</label>
                </div>

                <h4>Trade hinzufügen</h4>

                <h4>Transaktionen</h4>

                <div class="text-center">
                    <img src="<?= $this->_baseUrl ?>img/doc/ScreenshotTransaktionsübersicht.png"
                         alt="Screenshot Transaktionsübersicht">
                    <label class="hint">Abbildung: Screenshot der Transaktionsübersicht</label>
                </div>

                <h4>Gewinnreports</h4>

                <div class="text-center">
                    <img src="<?= $this->_baseUrl ?>img/doc/ScreenshotZahlung.png"
                         alt="Screenshot Zahlung">
                    <label class="hint">Abbildung: Screenshot der Gewinnreport-Kaufseite</label>
                </div>

                <div class="text-center">
                    <img src="<?= $this->_baseUrl ?>img/doc/ScreenshotGewinnreport.png"
                         alt="Screenshot Gewinnreport">
                    <label class="hint">Abbildung: Screenshot eines Gewinnreports</label>
                </div>

                <h4>Rechnungen</h4>

                <div class="text-center">
                    <img src="<?= $this->_baseUrl ?>img/doc/ScreenshotRechnungen.png"
                         alt="Screenshot Rechnungen">
                    <label class="hint">Abbildung: Screenshot der Rechnungsübersicht</label>
                </div>


                <h3 id="datenmodell">7. ER-Modell und relationales Modell</h3>

                <h3 id="rollenmodell">8. Rollenmodell</h3>

                <p>
                    In CoinTax gibt es zwei Rollen: Anonyme Besucher und Benutzer. Anonyme Besucher können
                    ausschließlich
                    die Startseite, die Loginseite, die Seite zum Registrieren und die allgemeinen Informationsseiten
                    (Impressum, Datenschutz, Dokumentation) ansehen.
                </p>

                <p>
                    Nach der Registrierung und dem erfolgreichen Login bekommt ein Anonymer Besucher über ein Session
                    Cookie die Rolle Benutzer.
                    Die Rolle Benutzer kann zudem eine weitere Berechtigung haben: Nur zahlende Benutzer können
                    auf die detaillierten Gewinnreports zugreifen. Dafür muss für jedes Steuerjahr ein eigener
                    Gewinnreport gekauft werden. Beispielsweise kann Benutzer A nur auf den persönlichen Gewinnreport
                    des Jahres 2020 zugreifen, wenn dieser zuvor über das entsprechende Formular vom Benutzer gekauft
                    wurde.
                </p>

                <div class="text-center">
                    <img width="900" src="<?= $this->_baseUrl ?>img/doc/Rollenmodell.svg"
                         alt="Rollen und deren Seitenzugriffsrechte">
                    <label class="hint">Abbildung: Rollen und deren Seitenzugriffsrechte</label>
                </div>

                <h3 id="codedoc">9. Codestruktur</h3>

                <h3 id="checkliste">10. Abgleich mit Anforderungscheckliste</h3>

                <h3 id="reflexion">11. Reflektion</h3>

            </div>
        </div>
    </div>

</section>