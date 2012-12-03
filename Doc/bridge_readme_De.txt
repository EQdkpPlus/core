WICHTIG !
es handelt sich hier Um eine frühe Testversion
es wird keine Garantie für eventuelle Datenverluste übernommen !!!

Tested on
vBulletin 3.6.8
Joomla 1.5
phpBB 3.0.0
phpBB 2.0.22
e107 0.7.11
wBB 3.0.3 ( Thanks to Lightstalker for Licence)

Not Working :
Adminrechte im EQDKP für einen CMS nutzer ( im Moment muss auf Standard Umsgestellt werden und mit dem zur installation angegebenen nutzer die rechte eingestellt werden)
Anleitung :

1.) kopiert alle Dateien ins eqdkp plus verzeichnis
 
	1. a) falls ihr zum überschreiben Aufgefordert werdet tut dies (dies sollte bei common.php , session.php und settings.php der fall sein)


2.) Austesten ;)

Feedback an : webmaster <at> redpeppersworld <dot> de

oder im IRC : irc.quakenet.org #eqdkp-plus


Author : Mike "RedPepper" Becker @ 2008

How it works :

Die Bridge ist eigentlich sehr Einfach. Sie hängt sich im eqdkp plus zwischen den orginalen Loginvorgang und syncronisiert den User der sich anmelden will von der Userdatenbank des Quell-CMS bzw Quellforums und integriert diese nach erfolgreicher Authentifizierung als gültiger Nutzer des CMS bzw Forums  und als Mitglied der berechtigten Gruppe ins eqdkp mit den Standardrechten. Dieser Vorgang geht nur in eine Richtung, dies bedeutet das nur Userdaten vom CMS/Forum zu eqdkp plus übertragen werden änderungen im eqdkp plus werden NICHT übernommen.
