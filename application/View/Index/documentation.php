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

                <p>
                    Auf dieser Seite kann sich ein Nutzer registrieren. CoinTax nutzt den Vornamen und den Nachnamen des
                    Nutzers um diesen auf der Startseite persönlich zu begrüßen. Außerdem werden die Namensfelder bei
                    der Eingabe der Zahlungsdaten für den Kauf eines Gewinnreports mit den Daten automatisch
                    vorausgefüllt.
                </p>

                <p>
                    Mittels JavaScript findet eine einfach validierung der Eingabefelder statt (alle Felder müssen
                    ausgefüllt sein). Solange nicht alle Felder ausgefüllt sind, lässt sich das Formular nicht absenden.
                </p>

                <h4>Login</h4>

                <div class="text-center">
                    <img src="<?= $this->_baseUrl ?>img/doc/ScreenshotLogin.png"
                         alt="Screenshot Login">
                    <label class="hint">Abbildung: Screenshot des Logins</label>
                </div>

                <p>
                    Das ist die Loginseite. Mittels JavaScript wird verhindert, dass eine leere Form an den Server
                    gesendet wird.
                </p>

                <h4>Dashboard</h4>

                <div class="text-center">
                    <img src="<?= $this->_baseUrl ?>img/doc/ScreenshotDashboard.png"
                         alt="Screenshot Dashboard">
                    <label class="hint">Abbildung: Screenshot des Dashboards</label>
                </div>

                <p>
                    Nach dem Login landet der Nutzer zuerst auf dem Dashboard. Das Dashboard zeigt eine Übersicht aller
                    Tokens im Portfolio des Nutzers. Diese Daten ergeben sich durch die Summe der Transaktionen des
                    Nutzers. Neben dem aktuellen Wert des Portfolios wird auch der im ausgewählten Jahr bereits
                    realisierte Gewinn (abzüglich Gebühren) angezeigt. Dabei wird jedoch noch nicht berücksichtigt, ob
                    bestimmte Verkäufe aufgrund der Haltedauer der Token steuerfrei sind. Diese Daten erhält der Nutzer
                    erst durch den Kauf eines Gewinnreports.
                </p>

                <p>
                    Oben rechts kann der Nutzer das Jahr für die Gewinnberechnung auswählen. Der aktuelle Wert des
                    Portfolios ändert sich durch anpassen der Jahresauswahl nicht. Es ändern sich nur die Gewinne, da
                    diese jahresabhängig sind. Standardmäßig ist das aktuelle Jahr vorausgewählt.
                </p>

                <p>
                    Die aktuellen Preisdaten werden von <a href="https://www.coingecko.com/" target="_blank"
                                                           class="link">CoinGecko</a> per API Request geladen (kostenlos
                    und ohne API-Key möglich). Wenn ein Nutzer sehr viele Transaktionen hat, steigt entsprechend die
                    Ladezeit des Dashboards. Falls die PHP Erweiterung APCu aktiviert ist, wird diese verwendet, um die
                    Antworten der CoinGecko API für eine begrenze Zeit zu cachen. Standardmäßig ist diese Erweiterung
                    in PHP allerdings nicht aktiviert.
                </p>

                <p>
                    Wenn der Nutzer in der Tabelle auf eine Kryptowährung klickt, öffnet sich die Liste der
                    Transaktionen, gefiltert nach dem angeklickten Token.
                </p>

                <h4>Trades anzeigen</h4>

                <div class="text-center">
                    <img src="<?= $this->_baseUrl ?>img/doc/ScreenshotTradeübersicht.png"
                         alt="Screenshot Trades">
                    <label class="hint">Abbildung: Screenshot der Tradeübersicht</label>
                </div>

                <p>
                    Diese Seite zeigt alle vorhandenen Trades des Nutzers. Standardmäßig sind diese absteigend nach
                    Ihrem Ausführungszeitpunkt sortiert. Pro Seite werden zehn Trades angezeigt. Wenn ein Nutzer mehr
                    als zehn Trades besitzt, erscheint eine Paginierung. Wenn der Nutzer die Seite nach unten scrollt,
                    wird die jeweils nächste Seite automatisch per Ajax nachgeladen. Dafür erscheint während der
                    Ladezeit auch eine Ladeanimation.
                </p>

                <p>
                    Durch einen Klick auf den rechten Pfeil bei einem Trade, können Detailinformationen eingeblendet
                    werden. Dazu zählt die exakte Menge der transferierten Token (nicht gerundet) und deren Wert in Euro
                    zum Zeitpunkt der Transaktion. Ggf. wird auch die gezahlte Gebühr angezeigt. Durch einen weiteren
                    Klick auf den blauen "Details" Button, öffnet sich die Detailseite des Trades mit steuerlichen
                    Detailinformationen.
                </p>

                <p>
                    Über der Liste der Trades kann der Nutzer eine Filterung vornehmen. Es können Transaktionen nach,
                    vor bzw. zwischen bestimmten Zeitpunkten gefiltert werden. Außerdem kann nach Transaktionen
                    gefiltert werden, die ein bestimmtes Token enthalten. Alle möglichen Filter werden UND verknüpft.
                    Der Nutzer kann zusätzlich zum Filtern auch noch die Sortierung anpassen.
                </p>

                <p>
                    Hinweis: Beim Sortieren nach Gesendeter oder Empfangener Menge, wird tatsächlich nach der Menge des
                    jeweiligen Tokens sortiert und nicht etwa nach dem Wert in Euro.
                </p>

                <p>
                    Ein neuer Trade kann oben rechts über den grünen Button hinzugefügt werden. Das Löschen von Trades
                    ist in der ausgeklappten Ansicht durch einen Klick auf den roten Button möglich. Dabei muss der
                    Nutzer den Löschvorgang in einem Popup zunächst noch einmal bestätigen. Das Löschkommando wird dann
                    per Ajax an den Server gesendet.
                </p>

                <h4>Trade Details</h4>

                <div class="text-center">
                    <img src="<?= $this->_baseUrl ?>img/doc/ScreenshotTradedetails.png"
                         alt="Screenshot Trade Details">
                    <label class="hint">Abbildung: Screenshot der Detailseite für Trades</label>
                </div>

                <p>
                    Auf dieser Seite sind alle steuerlich relevanten Details eines Trades aufgelistet. Über die Buttons
                    oben rechts kann der Trade außerdem gelöscht oder Bearbeitet werden.
                </p>

                <p>
                    Ein Trade besteht immer aus einem Token, das erworben wird, ein Token mit dem bezahlt wird und ggf.
                    einem Token mit dem die Gebühr des Handelsplatzes beglichen wird. Dementsprechend ist die Tabelle
                    auf dieser Seite auch in drei Zeilen unterteilt: Verkauf des Zahlungstokens, Kauf des erworbenen
                    Tokens und Verkauf des Gebührtokens. Pro Token ist die in diesem Trade verkaufte bzw. gekaufte Menge
                    angegeben sowie der Preis von einer Einheit des Tokens bzw. der Gesamtmenge zum Zeitpunkt des
                    Trades.
                </p>

                <p>
                    Außerdem wird zu jedem Verkauf auch angezeigt, wann die jeweils Verkauften Token gekauft wurden, zu
                    welchen Preisen dies der Fall war und ob der Kauf länger als ein Jahr her ist und dementsprechend
                    der Verkauf steuerfrei ist.
                </p>

                <p>
                    Es kann passieren, dass ein Nutzer seine Trades falsch eingibt. CoinTax kann diesen Umstand daran
                    erkennen, dass es eine Transaktion gibt, die mehr Tokens verkauft als noch im Portfolio waren. Auf
                    der Detailsseite der betroffenen Transaktion wird dann der unten stehende Hinweis eingeblendet. Der
                    Fehler wird außerdem auch im Gewinnreport deutlich gemacht.
                </p>

                <div class="text-center">
                    <img src="<?= $this->_baseUrl ?>img/doc/ScreenshotWrongTrades.png"
                         alt="Screenshot fehlende Trades">
                    <label class="hint">Abbildung: Screenshot der Warnung bei fehlenden Tokenkäufen</label>
                </div>

                <p>
                    Am Ende der Detailseite ist dann noch eine Übersicht der Steuerdetails dargestellt. Dort wird
                    vorgerechnet, wie hoch der steuerrechtlich relevante Gewinn ist. Sowohl für den Trade selbst als
                    ggf. auch für den Verkauf des Gebührtokens.
                </p>

                <p>
                    Hinweis: Historische Preisdaten erhält CoinTax ebenfalls über die API von CoinGecko. Die API von
                    CoinGecko liefert pro Tag pro Währung den durchschnittlichen Marktpreis zurück (Beispiel: Bitcoin
                    hat am 22.04.2021 40.000 Euro gekostet). Da sich diese historischen Daten nicht mehr ändern, werden
                    sie von CoinTax nach dem ersten Abruf von CoinGecko in der Datenbank gespeichert und zukünftig immer
                    von dort ausgelesen. Dies entlastet die CoinGecko API erheblich und führt zu deutlich schnelleren
                    Ladezeiten von CoinTax.
                </p>

                <h4>Trades hinzufügen und bearbeiten</h4>

                <div class="text-center">
                    <img src="<?= $this->_baseUrl ?>img/doc/ScreenshotTradeBearbeiten.png"
                         alt="Screenshot Trade bearbeiten">
                    <label class="hint">Abbildung: Screenshot der Bearbeitungsseite für Trades</label>
                </div>

                <p>
                    Die Seiten für das Hinzufügen und Bearbeiten von Trades sind im Aufbau identisch. Der Nutzer muss
                    den Ausführungszeitpunkt des Trades angeben, die gesendete Menge und den Tokentyp sowie das
                    empfangene Token und dessen Tokentyp. Optional kann auch die gezahlte Gebühr und deren Tokentyp
                    angegeben werden.
                </p>

                <p>
                    Die Textfelder für den Tokentyp besitzen eine Suchfunktion, die über JavaScript und Ajax realisiert
                    ist: Wenn der Nutzer einen Buchstaben in das Feld eingibt, werden alle Token angezeigt, die von
                    CoinTax unterstützt werden und die eingegebene Zeichenkette enthalten. Die Suchergebnisse werden als
                    Drop-Down unter dem Textfeld angezeigt. Im Drop-Down kann mit den Pfeiltasten nach oben und unten
                    navigiert werden. Ein Druck auf die Eingabetaste füllt das Textfeld mit dem gewählten Token.
                    Alternativ kann auch mit der Maus auf das gewünschte Ergebnis geklickt werden. Im Textfeld kann
                    entweder nach dem Namen des Tokens (z.B. "Bitcoin") oder nach dessen Symbol (z.B. "BTC") sowohl in
                    Klein- als auch Großschreibung gesucht werden.
                </p>

                <p>
                    Die Mengenfelder müssen mit Zahlen gefüllt werden. Dabei ist sowohl der Punkt als auch das Komma als
                    Dezimaltrennzeichen erlaubt. Es können auch keine Dezimalstellen angegeben werden. Die maximale
                    Genauigkeit von CoinTax beträgt 25 Stellen hinter dem Komma. Die Nutzereingabe wird auch
                    clientseitig mittels JavaScript geprüft.
                </p>

                <h4>Transaktionen</h4>

                <div class="text-center">
                    <img src="<?= $this->_baseUrl ?>img/doc/ScreenshotTransaktionsübersicht.png"
                         alt="Screenshot Transaktionsübersicht">
                    <label class="hint">Abbildung: Screenshot der Transaktionsübersicht</label>
                </div>

                <p>
                    Neben der Übersichtsseite für die Trades gibts es zusätzlich noch diese extra Seite für eine
                    granularere Auflistung der einzelnen Transaktionen. Vom Aufbau entspricht die Seite im Großen und
                    Ganzen der Trades Übersicht. Alle Transaktionen eines Trades werden untereinander stehend angezeigt.
                    Zu den Transaktionen des nächsten Trades ist er Abstand etwas vergrößert, um eine visuelle
                    Unterscheidung zu ermöglichen. Gebührentransaktionen sind gesondert markiert. Grüne Pfeile bedeuten,
                    dass die Transaktion eingehen war. Rote Pfeile markieren ausgehende Transaktionen. Ein Klick auf
                    einen Pfeil bringt den Nutzer zur Detailseite des zugehörigen Trades.
                </p>

                <p>
                    Diese Seite kann vor allem genutzt werden, wenn nach Transaktionen bestimmter Token gesucht wird,
                    weil hier wirklich nur die einzelne Transaktion mit ihrem Wert und nicht direkt der ganze Trade
                    gefiltert wird.
                </p>

                <p>
                    Auf einer Seite werden die Transaktionen von bis zu zehn Trades gleichzeitig angezeigt. Danach
                    erscheint eine Paginierung. Auch hier werden weitere Transaktionen beim scrollen automatisch per
                    Ajax nachgeladen.
                </p>

                <h4>Gewinnreports</h4>

                <div class="text-center">
                    <img src="<?= $this->_baseUrl ?>img/doc/ScreenshotZahlung.png"
                         alt="Screenshot Zahlung">
                    <label class="hint">Abbildung: Screenshot der Gewinnreport-Kaufseite</label>
                </div>

                <p>
                    Wenn der Nutzer die Seite eines jährlichen Gewinnreports zum ersten Mal aufruft, wird er zunächst
                    aufgefordert den Report für das ausgewählte Jahr käuflich zu erwerben. Eine Änderung des Jahres kann
                    über die Buttons oben rechts erfolgen.
                </p>

                <p>
                    Die Form auf dieser Seite ist "mehrseitig": Der Nutzer muss nacheinander der einzelnen Felder
                    ausfüllen und kann jeweils nur zum nächsten Schritt übergehen, wenn die Eingabevalidierung per
                    JavaScript erfolgreich war. Bei den Feldern für die IBAN und die BIC stehen kleine Beispieldaten
                    neben dem Inputlabel, damit man beim testen direkt eine IBAN in das Eingabefeld kopieren kann. Im
                    produktiven Betrieb würden diese "Kopiervorlagen" noch entfernt werden.
                </p>

                <p>
                    Nachdem alle Felder vom Nutzer ausgefüllt wurden, kann der kostenpflichtige Kauf durch betätigen des
                    entsprechenden Buttons erfolgen. <b>Wichtig: CoinTax ist nicht an einen echten Zahlungsdienstleister
                        angebunden. Daher stehen angestoßene Käufe danach dauerhaft auf dem Status "ausstehend". Eine
                        Zahlung muss daher aktuell noch manuell in der Datenbank auf abgeschlossen umgestellt
                        werden.</b> Dazu muss in der Tabelle <code>payment_info</code> der Wert <code>fulfilled</code>
                    auf "<code>1</code>" gesetzte werden. Wenn die Spalte <code>failed</code> auf "<code>1</code>"
                    gesetzte wird, ist die Zahlung fehlgeschlagen und der Nutzer bekommt einen entsprechenden Hinweis im
                    Frontend.
                </p>

                <p>
                    In einem realen Umfeld würde man CoinTax an einen Zahlungsdienstleister wie <a
                            href="https://stripe.com/de" target="_blank" class="link">Stripe.com</a> anbinden. Dieser
                    würde dann bei erfolgreicher oder fehlgeschlagener Zahlung einen Webhook bei CoinTax triggern, der
                    die Werte in der Datenbank entsprechend setzt. Dies zu implementieren hätte aber den Umfang der
                    Projektarbeit gesprengt.
                </p>

                <div class="text-center">
                    <img src="<?= $this->_baseUrl ?>img/doc/ScreenshotGewinnreport.png"
                         alt="Screenshot Gewinnreport">
                    <label class="hint">Abbildung: Screenshot eines Gewinnreports</label>
                </div>

                <p>
                    Wenn ein Nutzer einen Gewinnreport erfolgreich erworben hat, kann er von nun an den Report jederzeit
                    auf dieser Seite einsehen. Bei jedem Neuladen der Seite wird der Report neu generiert. Wenn also
                    nachträglich Trades zum entsprechenden Jahr hinzugefügt werden, werden diese dann ebenfalls im
                    Report berücksichtigt und angezeigt.
                </p>

                <p>
                    Der Kopf des Reports zeigt die berechneten steuerlich relevanten Gewinne und gezahlte Gebühren im
                    jeweiligen Steuerjahr. Diese Informationen kann ein Nutzer in seiner Steuererklärung eintragen.
                </p>

                <p>
                    Darunter sind dann die Berechnungen pro Kryptowährung aufgeschlüsselt. (Kryptowährungsblöcke können
                    durch einen Klick auf den kleinen Pfeil neben dem Namen der Währung aus- und eingeklappt werden).
                    Jede Verkaufstransaktion wird hier einzeln aufgeschlüsselt und es wird aufgezeigt, wie die jeweilige
                    Gewinnsumme zustande kommt. Dafür wird der Kaufpreis der verkauften Token angezeigt, der
                    Verkaufspreis der Token und der dadurch erzielte Gewinn. Gebührenzahlungen sind gesondert markiert.
                    Auch nicht steuerpflichtige Verkäufe werden hier aufgeführt und sind gesondert markiert. Diese
                    fließen in die Berechnung der steuerlich relevanten Gewinne selbstverständlich nicht mit ein.
                </p>

                <p>
                    Am Ende des Gewinnreports ist Button für die erzeugung einer Druck-Version zu finden. Diese ist über
                    ein spezielles Druck-CSS realisiert.
                </p>

                <h4>Rechnungen</h4>

                <div class="text-center">
                    <img src="<?= $this->_baseUrl ?>img/doc/ScreenshotRechnungen.png"
                         alt="Screenshot Rechnungen">
                    <label class="hint">Abbildung: Screenshot der Rechnungsübersicht</label>
                </div>

                <p>
                    Die Rechnungsübersicht zu den erworbenen Gewinnreports kann über die Kopfleiste > Account >
                    Rechnungen erreicht werden. Auf dieser Seite sieht der Nutzer eine Übersicht aller gekauften
                    Gewinnreports und ob die Zahlung erfolgreich abgeschlossen wurde, noch ausstehen oder ein Fehler
                    aufgetreten ist. Ein Klick auf das jeweilige Icon links öffnet den zugehörigen Gewinnreport.
                </p>

                <h3 id="datenmodell">7. ER-Modell und relationales Modell</h3>

                <div class="text-center">
                    <img width="700" src="<?= $this->_baseUrl ?>img/doc/ERModell.svg"
                         alt="ER-Modell">
                    <label class="hint">Abbildung: ER-Modell</label>
                </div>

                <div class="text-center">
                    <img src="<?= $this->_baseUrl ?>img/doc/db_schema.png"
                         alt="Relationales Modell">
                    <label class="hint">Abbildung: Relationales Modell</label>
                </div>

                <p>
                    Für die Abbildung der beschriebenen Anwendungslogik hätte man theoretisch die Tabellen Order und
                    Transaction auch zusammenlegen können. Allerdings plant der Autor dieser Arbeit, das Projekt nach
                    Abschluss des Semesters weiterzuführen. Zum Beispiel soll auch die Berechnung von Gewinnen aus dem
                    Krypto-Staking implementiert werden. Beim Staking gibt es jedoch keine Orders (bzw. Trades) sondern
                    nur einzelne Transaktionen. Daher macht es mehr Sinn, die Datenbank direkt so zu strukturieren, dass
                    Transaktionen auch ohne eine Order existieren könnten. Dementsprechend unterscheiden sich das
                    ursprüngliche ER-Modell und das implementierte relationale Modell in dieser Hinsicht auch.
                </p>

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

                <p>
                    CoinTax nutzt das MVC (Model-View-Controller) Pattern. Für das Projekt wurde ein eigenes Framework
                    implementiert, dass die Aufteilung des Codes in Models, Views und Controllers erzwingt. So konnte
                    eine einheitliche, verständliche und erweiterbare Codestruktur erreicht werden. Im Folgenden wird
                    zunächst der Verzeichnisbaum kurz erläutert und anschließend auf die grobe Funktionsweise des
                    Frameworks eingegangen.
                </p>

                <pre>
                    ├─ application/
                    │   ├─ Config/
                    │   │   └─ Config.php   => muss während der Installation manuell angelegt werden
                    │   ├─ Controller/      => enthält alle Controller Klassen
                    │   ├─ Core/            => enthält Anwendungslogik
                    │   │   ├─ Calc         => Klassen zur Gewinnberechnung
                    │   │   ├─ Coingecko    => Schnittstelle zur CoinGecko API
                    │   │   ├─ Exception    => Selbstdefinierte Ausnahmetypen
                    │   │   └─ Repository   => Repositoryklassen für den Datenbankzugriff
                    │   ├─ Framework/       => das Framework; das Framework ist generisch implementiert und kann für andere Projekte wiederverwendet werden
                    │   ├─ Model/           => alle Model Klassen
                    │   ├─ View/            => alle Views, sortiert nach Controller
                    │   │   └─ Base/        => generische Views für Header und Footer
                    │   ├─ autoloader.php   => PHP Autoloader zum automatischen inkludieren von Klassen
                    │   ├─ globalFunctions.php => enthält Funktionen, die global verwendet werden können (zum Beispiel Debugging Helper Funktionen)
                    │   └─ tests.php        => Unittests die ausgeführt werden, wenn APPLICATION_DEBUG=true in der index.php
                    ├─ doc/                 => Dokumente und Planungsunterlagen
                    ├─ public/              => Dieses Verzeichnis ist am Ende auf einem Webserver öffentlich zugänglich
                    │   ├─ index.php        => Der Haupteinstiegspunkt
                    │   └─ .htaccess        => Diese Apache Konfiguration stellt sicher, dass alle Requests an die index.php weitergeleitet werden
                    └─ scripts/             => Skripte zum Ausführen auf der Kommandozeile
                </pre>

                <p>
                    Das Framework ist so aufgebaut, dass alle Requests zunächst von der <code>index.php</code>
                    bearbeitet werden. Dies wird mittels einer Apache Konfigurationsdatei erreicht
                    (<code>.htaccess</code>).
                </p>

                <p>
                    Alle eingehenden Requests werden zunächst geparsed und ihre Zielroute extrahiert. Anhand der Route
                    entscheidet das Framework dann, welcher Controller geladen und welche Action darin ausgeführt werden
                    soll. Beispielsweise würde für die Route <code>/order/add</code> der <code>OrderController</code>
                    geladen und die <code>AddAction</code> ausgeführt werden.
                </p>

                <p>
                    Jede Action bekommt ein Objekt vom Typ <code>Response</code> übergeben. Dieses stellt Methoden zum
                    Weiterleiten des Nutzers zur Verfügung. Außerdem dient es als Transportobjekt für Variablen zwischen
                    Action und View. Am Ende einer Action kann optional eine View gerendert werden. Die View wird im
                    Kontext der Response ausgeführt. Dementsprechend kann darin mittels <code>$this->XXX</code> auf
                    Membervariablen der Response zugegriffen werden.
                </p>

                <p>
                    Beim Laden eines Controllers injiziert das Framework außerdem noch Abhängigkeiten in den jeweiligen
                    Controller. Zu den Abhängigkeiten zählen die Repositoryklassen und implizit die Datenbankverbindung.
                </p>

                <h3 id="checkliste">10. Abgleich mit Anforderungscheckliste</h3>

                <p>
                    Die Punkte in den folgenden Tabellen stammen aus der "Checkliste zum Erfolg", die sich in Moodle
                    befindet. Der GWP-Teil wurde ausgelassen, da dieses Projekt nur für DWP abgegeben wird.
                </p>

                <h4>Allgemein für beide Kurse GWP/DWP</h4>

                <table>
                    <thead>
                    <tr>
                        <th>Kriterium</th>
                        <th>Umgesetzt?</th>
                        <th>Beschreibung / Details</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>mind. 6 Seiten</td>
                        <td>ja</td>
                        <td>siehe <a class="link" href="#navigation">4. Navigationsstruktur</a>: Startseite, Dashboard,
                            Trades, Transaktionen, Gewinnreport, Rechnungen, ...
                        </td>
                    </tr>
                    <tr>
                        <td>mind. 3 Unterseiten</td>
                        <td>ja</td>
                        <td>siehe <a class="link" href="#navigation">4. Navigationsstruktur</a>: Jahresauswahl, Trade
                            Details, Trade hinzufügen, ...
                        </td>
                    </tr>
                    <tr>
                        <td>Einheitliche Navigation über alle Seiten</td>
                        <td>ja</td>
                        <td>Es gibt einen Header und einen Footer, die überall gleich aussehen und eine einheitliche
                            Navigation gewährleisten.
                        </td>
                    </tr>
                    <tr>
                        <td>Navigation mit Untermenü (2. Ebene)</td>
                        <td>ja</td>
                        <td>Wenn der Nutzer im Header über "Account" hovert, öffnet sich ein DropDown mit dem
                            Untermenü. Das Untermenü arbeitet mit CSS und funktioniert auch mit deaktiviertem
                            JavaScript.
                        </td>
                    </tr>
                    <tr>
                        <td>mind. 3 Formulare (Login, Registrierung, Kontakt)</td>
                        <td>ja</td>
                        <td>Zum Beispiel: Login, Registrierung, Anlegen eines neuen Trades, ...</td>
                    </tr>
                    <tr>
                        <td>Vermeidung von doppelten Code (Wiederverwendung)</td>
                        <td>ja</td>
                        <td>CoinTax nutzt das MVC Patter um Code zu strukturieren und Codedopplungen zu vermeiden.
                            Häufig verwendete View-Elemente (z.B. Textboxen) werden ebenfalls durch eigene Klassen
                            repräsentiert. So werden auch Coderedundanzen in den Views reduziert.
                        </td>
                    </tr>
                    <tr>
                        <td>Codestyle und Code-Dokumentation</td>
                        <td>ja</td>
                        <td>-/-</td>
                    </tr>
                    <tr>
                        <td>Keine Verwendung von Frameworks</td>
                        <td>ja</td>
                        <td>Es gibt zwar eine Klasse "Framework", diese und alle dazugehörigen Komponenten wurden jedoch
                            extra für dieses Projekt von Fabian Maier selbst ausgedacht implementiert.
                        </td>
                    </tr>
                    <tr>
                        <td>Projektdokumentation</td>
                        <td>ja</td>
                        <td>-/-</td>
                    </tr>
                    <tr>
                        <td>Installationshinweise</td>
                        <td>ja</td>
                        <td>-/-</td>
                    </tr>
                    <tr>
                        <td>Datenbank-Export</td>
                        <td>ja</td>
                        <td>-/-</td>
                    </tr>
                    <tr>
                        <td>Test</td>
                        <td>ja</td>
                        <td>Die Seite wurde vor der Abgabe auf einem weiteren Rechner erfolgreich installiert. Zudem
                            gibt es vereinzelte Unittests unter <code>/application/tests.php</code>. Die Unittests
                            werden bei jedem Seiten-Reload ausgeführt, wenn in der Datei <code>/public/index.php</code>
                            die Konstante <code>APPLICATION_DEBUG</code> in Zeile vier auf <code>true</code> gesetzt
                            wird.
                        </td>
                    </tr>
                    </tbody>
                </table>

                <h4>Allgemein für DWP</h4>

                <table>
                    <thead>
                    <tr>
                        <th>Kriterium</th>
                        <th>Umgesetzt?</th>
                        <th>Beschreibung / Details</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>Formulare werden mittels PHP/JavaScript behandelt und Fehler angezeigt</td>
                        <td>ja</td>
                        <td>Clientseitig wird vor allem geprüft, ob "required" Felder ausgefüllt sind und ob Felder, die
                            ein Pattern-Attribut haben entsprechend dieses Patterns ausgefüllt wurden. Erst wenn die
                            JS-Validierung erfolgreich war, kann das Formular abgesendet werden. Serverseitig
                            finden alle komplexeren Validierungen statt. Dafür gibt es eine eigene Klasse unter
                            <code>/application/Framework/Validation/InputValidator.php</code>. Fehler werden in der
                            Session des Nutzers gespeichert und anschließend im Formular angezeigt. Dabei bleiben
                            bisherige Eingaben des Nutzers erhalten (ebenfalls in der Session zwischengespeichert).
                        </td>
                    </tr>
                    <tr>
                        <td>Nutzeranmeldung</td>
                        <td>ja</td>
                        <td>-/-</td>
                    </tr>
                    <tr>
                        <td>Nutzerregistrierung (Vorname, Nachname, E-Mail, Passwort, Telefonnummer, ...)</td>
                        <td>ja</td>
                        <td>Die Telefonnummer des Nutzers wird nicht extra abgefragt, da sie für CoinTax nicht von
                            Relevanz ist.
                        </td>
                    </tr>
                    <tr>
                        <td>Validierung der Formulare mit JavaScript</td>
                        <td>ja</td>
                        <td>Die Validierung eines Formularfelds erfolgt beim Verlassen des Cursors. Fehlermeldungen
                            erscheinen schlicht aber sichtbar unter oder neben dem Eingabefeld. Das Eingabefeld wird
                            außerdem rot umrandet. Die Validierung ist maßgeblich in
                            <code>/public/js/formvalidation.js</code> implementiert.
                        </td>
                    </tr>
                    <tr>
                        <td>Validierung der Formulare serverseitig mit PHP</td>
                        <td>ja</td>
                        <td>siehe auch Punkt "Formulare werden mittels PHP/JavaScript behandelt und Fehler angezeigt"
                        </td>
                    </tr>
                    <tr>
                        <td>Funktionsbereitstellung mit und ohne JavaScript</td>
                        <td>ja</td>
                        <td>Die Seite wurde mit und ohne JavaScript getestet und ist in beiden Fällen funktionsfähig.
                        </td>
                    </tr>
                    <tr>
                        <td>Absenden von Formularen mit AJAX (außer LOGIN) ermöglichen</td>
                        <td>beispielhaft</td>
                        <td>Das Löschen von Trades aus der Trade-Übersicht (Listenansicht) heraus erfolgt über Ajax. Die
                            Registrierung erfolgt über Ajax.
                        </td>
                    </tr>
                    <tr>
                        <td>Seiten bauen sich dynamisch anhand von Daten aus der Datenbank auf (mind. eine Liste von
                            Daten)
                        </td>
                        <td>ja</td>
                        <td>Transaktionsliste, Tradeliste und zugehörige Filter- und Sortiermöglichkeiten. Es kann nach
                            Startdatum, Enddatum und Token gefiltert werden. Es kann nach Datum, gesendetem Wert und
                            empfangenem Wert sortiert werden.
                        </td>
                    </tr>
                    <tr>
                        <td>Seiten können Inhalte dynamisch über JavaScript nachladen</td>
                        <td>ja</td>
                        <td>Die Pagination ist über scrollen in der Trade- und Transaktionsübersicht möglich. Außerdem
                            werden Token gemäß der eingegebenen Zeichenkette im Suchfeld der Transaktions- und
                            Tradeübersichtsseite dynamisch per Ajax vom Server geladen.
                        </td>
                    </tr>
                    <tr>
                        <td>Fehlerbehandlung auch für nicht bekannte Seiten (404 Error)</td>
                        <td>ja</td>
                        <td>Weiterleitung auf eine dedizierte Fehlerseite</td>
                    </tr>
                    <tr>
                        <td>Es ist eine Datenbank angebunden</td>
                        <td>ja</td>
                        <td>-/-</td>
                    </tr>
                    <tr>
                        <td>Daten aus der Datenbank werden gelesen</td>
                        <td>ja</td>
                        <td>-/-</td>
                    </tr>
                    <tr>
                        <td>Daten aus der Datenbank werden geschrieben</td>
                        <td>ja</td>
                        <td>-/-</td>
                    </tr>
                    </tbody>
                </table>

                <h3 id="reflexion">11. Reflektion</h3>

                <p>
                    Das Projekt wurde im Bearbeitungszeitraum erfolgreich alleine von Fabian Maier geplant und
                    implementiert. Während der Umsetzung traten einige kleinere und größere Hürden auf, die im Folgenden
                    kurz dargelegt werden. Anschließend folgt ein Ausblick zu Weiterentwicklungsmöglichkeiten von
                    CoinTax.
                </p>

                <p>
                    Am aufwändigsten war die Implementierung der Gewinnberechnung. Es gibt in Deutschland zwei
                    Möglichkeiten Gewinne zu berechnen. Das ist zum Einen die FIFO und zum Anderen die LIFO Methode.
                    Beide haben ihre Vor- und Nachteile für den jeweiligen Einzelfall. Zu Beginn des Projekts war
                    geplant, dass beide Berechnungsmethoden von CoinTax unterstützt werden sollen. Es hat sich
                    allerdings schnell herausgestellt, dass dies den Umfang der Projektarbeit sprengen würden. Da
                    meistens sowieso die FIFO-Methode gewählt wird, weil durch sie vor allem im Kryptobereich eine
                    geringere Steuerlast anfällt hat sich der Autor dieses Projekt schließlich dazu entschieden nur die
                    FIFO-Methode zu implementieren.
                </p>

                <p>
                    Das Debugging der Kalkulationslogik war nicht immer ganz leicht, da zunächst bestimmte Sonderfälle
                    überlegt, händisch durchgerechnet und anschließend mit CoinTax verifiziert werden mussten. Um die
                    Berechnungen von CoinTax während der Entwicklung nachvollziehen zu können, wurden an einigen Stellen
                    Debug-Ausgaben eingebaut. Diese können mit der globalen Konstante <code>APPLICATION_DEBUG</code> an-
                    und ausgeschaltet werden. Das hat den Entwicklungsprozess erheblich vereinfacht.
                </p>

                <p>
                    Bereits zu Beginn des Projekts war klar, dass die Standarddatentypen von PHP nicht den
                    Genauigkeitsansprüchen zur Berechnungen von Gewinnen entsprechen (Rundungsfehler bei float,
                    Ungenauigkeit bei der maschinellen Darstellung nach IEEE 754). Daher wurde auf die PHP Erweiterung
                    BCMath gesetzt. Diese erlaubt u.a. die Durchführung von beliebig genauen Berechnungen im
                    Gleitkommabereich. BCMath funktioniert sehr gut und einfach. Allerdings gibt es keine Funktion, die
                    das mathematisch korrekte Runden von Gleitkommazahlen auf eine feste Stellenanzahl erlaubt. Da genau
                    so eine Rundung aber für die Ausgabe von Zahlen im Frontend von großer Bedeutung ist, musste eine
                    eigene Funktion zum korrekten Runden der Zahlen implementiert werden. Dies war aufwändiger als
                    erwartet, konnte aber in <code>globalFunctions.php</code> bewerkstelligt werden. Ein Unittest in
                    <code>tests.php</code> beweist außerdem die korrekte Arbeitsweise der Funktion.
                </p>

                <p>
                    Das am Anfang der Arbeit implementierte Framework hat sich im Projektverlauf sehr bewährt. Neben dem
                    Request Routing wurde es noch um Komponenten für die Inputvalidierung und für das Rendern von
                    Viewelementen erweitert.
                </p>

                <p>
                    An einigen Stellen wurden im Backend Code Texte hart codiert, die beispielsweise bei fehlerhaftem
                    Input an das Frontend gesendet werden. Dieses Design ist nicht sehr robust, falls später einmal
                    Mehrsprachigkeit eingebaut werden soll. Außerdem ist für die Korrektur von Schreibfehlern o. Ä.
                    immer eine Anpassung des Backend Codes notwendig. Dies sollte in zukünftigen Projekten mehr
                    entkoppelt werden.
                </p>

                <p>
                    Weitere Ideen für die zukünftige Weiterentwicklung von CoinTax umfassen die folgenden Punkte (die zu
                    umfangreich für den aktuellen Projektscope waren):
                </p>

                <ul>
                    <li>Steuerberechnungen / Gewinnreports cachen, um Ladezeiten zu verkürzen: Aktuell werden alle
                        Berechnungen beim Neuladen der Seite erneut ausgeführt. Dies verlangsamt die Webseite bei einer
                        großen Anzahl an Transaktionen pro Jahr erheblich
                    </li>
                    <li>"Passwort vergessen"-Funktion</li>
                    <li>E-Mail Bestätigungscode nach der Registrierung zum Aktivieren des Accounts</li>
                    <li>Unterstützung von Staking und Lending: Nur so kann eine Konkurrenzfähigkeit mit anderen
                        Anbietern
                        von Gewinnberechnungssoftware erreicht werden
                    </li>
                    <li>Transaktionsimporte von Handelsplattformen für den Nutzer ermöglichen</li>
                    <li>Differenziertere Preisstruktur (beispielsweise gestaffelt nach Zahl der Transaktionen im
                        Gewinnreport)
                    </li>
                    <li>Mehr Zahlungsoptionen</li>
                    <li>Anbindung eines Zahlungsdienstleisters</li>
                    <li>Rechtskonforme Rechnungen</li>
                </ul>

            </div>
        </div>
    </div>

</section>