\\::\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
//::///////////////////////////////////////////////
\\::
//:: EQDKP PLUGIN: Ticket - Conversation
\\:: © 2006/2007 Achaz (http://www.lionforge.de)
//:: Contact: 
\\:: Achaz - Achaz@lionforge.de
//::
\\::\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
//:://////////////////////////////////////////////
\\:: 
//:: DEPENDENCIES:
\\:: * EQDKP 1.3.1 (or EQDKP Plus)
//::
\\::\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
//:://////////////////////////////////////////////
\\:: 
//:: VERSION: 0.06
\\:: DATE: Januar 2007
//::
\\::\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
//:://////////////////////////////////////////////

INSTALLATION

* Den Ordner "ticket" in den Plugin Ordner Kopieren
* Im Administrationsbereich unter Plugins verwalten auf Installieren klicken
* Rechte für die Nutzer und Administratorenvergeben
* Einstellungen für das Plugin bearbeiten

\\::\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
//:://////////////////////////////////////////////

BENUTZUNG / FEATURES

* Benutzer können Tickets verfassen auf die alle berechtigten Admins antworten können
* Es können mehrere Antworten zu einem Ticket erstellt werden
* Die Benutzer können Antworttickets auf die Antworten der Admins erstellen.
* Email Benachrichtigungen für Admins und Nutzer sind möglich (für Nutzer default on)
* Visuelle Anzeige im Menu gibt an, ob neue Tickets/Antworten existieren
* Löschen der Tickets ist möglich

\\::\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
//:://////////////////////////////////////////////

CHANGELOG:

to 0.02
* Admin: Bei Antwort ohne Ticket wird nun bei nicht findens den Nutzernamens ein Fehler ausgegeben doch der Text bleibt erhalten
* Kleiner Bug bei Variablenzuweisung

to 0.03
* Adminbereich: Es werden nun alle Tickets angezeigt, auch wenn der Admin selbst noch keins verfasst hat (Bug Report von titan_flippi)
* Der "mit weiterem Ticket antworten" Link ist nun zentriert ausgerichtet und hat ein icon

to 0.04
* Adminbereich: Antworten werden korrekt angezeit (Bug Report von titan_flippi)
* Sprache etwas überarbeitet

to 0.05
* Adminbereich: bei vom Admin gelöschten Tickets kann nun das Löschen rückgängig gemacht werden
* Adminbereich: Das "Antworten auf Ticket" hat nun auch ein Icon
* Userbereich: Löscht ein Nutzer ein Ticket ohne die Antwort als gelesen markiert zu haben werden diese automatisch markiert
* Beim permanenten Löschen von Tickets werden nun tatsächlich die gesamten Sessions gelöscht 
* "Ticket"-Link nun in MainMenu2, da es eher eine Benutzersache ist, als eine Membersache

to 0.06
* Installation: Fehler bei erstellen des Feldes für firstreplydates versucht zu beheben durch löschen des Default Wertes
* Neuer Link für Admins in MainMenu2