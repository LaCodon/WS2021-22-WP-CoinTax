# Wichtige Entscheidungen

* Um mit Währungsbeträgen korrekt zu rechnen, verwende ich die PHP BCMath Erweiterung
* Ich verwende DATETIME statt TIMESTAMP in der Datenbank, weil DATETIME einen größeren Wertebereich hat und nicht 2038 endet
* Die Datenbank speichert UTC DATETIMEs (SET @@time_zone = '+00:00'; nicht vergessen!)