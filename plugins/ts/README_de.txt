//::///////////////////////////////////////////////
//::
//:: EQDKP PLUGIN: Tradeskills
//:: © 2006 CNSDEV (http://cnsdev.dk)
//:: Contact: 
//:: Cralex_NS - cns@cnsdev.dk
//:: Achaz - Achaz@lionforge.de
//:: including
//:: Plugin to automatically receive reagents from
//:: an Infosite (by Achaz)
//::
//:://////////////////////////////////////////////
//::
//:: DEPENDENCIES:
//:: * Itemstats2 or higher
//::
//:://////////////////////////////////////////////
//:: 
//:: VERSION: 0.97.4beta
//::
//:://////////////////////////////////////////////

INSTALLATION

* Den Ordner "ts" in den Plugin Ordner Kopieren
* Der Datei /ts/includes/debugReagents.txt schreibrechte geben (chmod 777)
* Im Administrationsbereich unter Plugins verwalten auf Installieren klicken


//:://////////////////////////////////////////////
UPDATE VON VERSION 0.96beta
* Den vorhandenen alten Ordner /ts auf eurem Webserver herunterladen und sichern
* Den vorhandenen alten Ordner /ts auf eurem Webserver löschen
* Den neuen Ordner /ts hochladen 
* Der Datei /ts/includes/debugReagents.txt Schreibrechte geben (chmod 777)
* Ein Datenbank Backup der Tabellen
    eqdkp_tradeskills
    eqdkp_tradeskill_recipes
    eqdkp_tradeskill_users
    eqdkp_users
    eqdkp_user_tradeskills
  machen
* Für Sicherheitsfetischischten ein komplettes DB-Backup machen
* Die beiliengende Datei "ts_sql_update_096-097.sql" öffnen und den Tabellenprefix "eqdkp_" in euren Prefix ändern und speichern
* Die von euch bearbeitete "ts_sql_update_096-097.sql" in MySQLadmin ausführen.

//:://////////////////////////////////////////////

BENUTZUNG

Lest dazu bitte das Wiki unter http://eqdkp.corgan-net.de/wiki/index.php/Tradeskills

//:://////////////////////////////////////////////

BEI GEFUNDEN BUGS, DIE IN ZUSAMMENHANG MIT DEM AUTOMATISCHEN FINDEN DER REAGENZIEN ZUSAMMENHÄNGEN
debugreagents.txt mit kleinem Kommentar an achaz@lionforge.de

//:://////////////////////////////////////////////

KNOWN BUGS:
* Wenn buffed/allakhazam überlastet geht der automatische Bezug nicht, nicht fixbar ;-)
* Es gibt ab und an Unterschiede zwischen Tooltip-Name und Name in der Beruf-DB auf buffed/Allakhazam - falls euch so ein 
Itemspezifischer Bug auffällt bitte melden!

//:://////////////////////////////////////////////

CHANGELOG:
0.974Beta
Dateien: viele ;-)
Notiz: Filter funktionen hinzugefügt (Grundidee und erste Vorlage von Aoshi), Single-Show-Modus hinzugefügt (Idee von Aoshi), Juwelenschleifen hinzugefügt, Auswahl der angezeigten Fertigkeiten möglich, Bugfixes

--

0.971Beta
Dateien: viele ;-)
Notiz: Adminbereich implementiert, Charakterauswahl implementiert, Kochen implementiert, Edit-Funktion implementiert ...

--

0.14alpha
Datei: index.php
Notiz: Checkt man ein Rezept als bekannt und ändert danach seinen Beruf, so dass man das Rezept nicht mehr kann, wird man nun nicht mehr auf der Übersichtsseite angezeigt. In der DB steht es noch drin, also wenn man wieder den Beruf zurückwechselt, steht man wieder drin - keine Daten gehen ungewollt verloren.

---

0.13alpha
Datei: index.php, ts_plugin_class.php, language files
NOtiz: Benutzerrechte für Rezept eintragen und abhaken nun getrennt, Übersetzungsfehler behoben

---

0.12alpha
Datei: blascReagents.php
Notiz:Fertigkeiten ohne Rezept gefixed

---

0.11alpha
Datei: blascReagents.php
Notiz: blasc Zweihandwaffe(n) fix - Verzauberungsrezepte für Zweihandwaffen bitte wie im Tooltip des Rezepts mit "Zweihandwaffe - ...." eingeben



