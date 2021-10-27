## Wichtige Entscheidungen

* Um mit Währungsbeträgen korrekt zu rechnen, verwende ich die PHP BCMath
Erweiterung
* Ich verwende DATETIME statt TIMESTAMP in der Datenbank, weil DATETIME
einen größeren Wertebereich hat und nicht 2038 endet
* Die Datenbank speichert UTC DATETIMEs (SET @@time_zone = '+00:00';
nicht vergessen!)

## (Mögliche) Probleme bisher

* Das implementierte MVC Framework macht die Benennung direkt abhängig von
den Routen. Das ist nicht entkoppelt und wenn man mal eine Route verändern
will, muss man auch die Benennung verändern bzw. die Action in einen anderen
Controller verschieben