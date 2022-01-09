# CoinTax

## Voraussetzungen für den Betrieb

* XAMPP (PHP 8.0.11, MariaDB 10.6.4)

## Installation

1. Repository in der XAMPP Installation unter `./htdocs/cointax` klonen
2. Datenbank erzeugen. Dafür muss der SQL Export aus dem Projektverzeichnis unter `doc/dbexport.sql` in den MariaDB
   Server importiert werden
3. Datenbankuser für CoinTax anlegen. Für diesen User kann ein beliebiger Username und ein beliebiges Passwort definiert
   werden
4. Konfigurationsdatei im Projektordner unter `application/Config/Config.php` anlegen. Diese muss den folgenden Inhalt
   haben (Name und Passwort des Datenbankusers muss entsprechend Schritt drei angepasst werden):
    ```php
    <?php

    namespace Config;
    
    abstract class Config
    {
        // change if CoinTax is not installed unter ./htdocs/cointax
        const baseUrl = 'http://localhost/cointax/public/';
    
        const databaseUsername = 'cointax';
        const databasePassword = 'irgendeinpasswort';
        const databaseDb = 'cointax';
        const databaseHost = '127.0.0.1';
        const databasePort = '3306';
    }
    ```
5. CoinTax im Browser unter [http://localhost/cointax](http://localhost/cointax) öffnen

## Testuser

### Login

E-Mail: `max.mustermann@example.com`\
Passwort: `max1234`

### Accountbeschreibung des Testusers

Der Testuser mit dem Namen Max Mustermann hat bereits einige Transaktionen und Käufe vorkonfiguriert:

* 2020: Erster Kauf und Verkauf; der Kauf des Gewinnreports ist fehlgeschlagen
* 2021: Weitere Käufe und Verkäufe (einige davon steuerrelevant, andere nicht, siehe Tradedetails); es liegt ein
  erfolgreich gekaufter Gewinnreport vor. Dabei spannend: Es wurden 2021 zwar Gewinne realisiert, allerdings keine
  steuerrelevanten. Demzufolge ist im Gewinnreport ein Verlust verzeichnet.
* 2022: Bisher keine Handelsaktivitäten aber noch einige Token im Portfolio
