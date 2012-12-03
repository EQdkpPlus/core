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
//:: YES IT IS BETA - PLEASE DO KEEP THAT IN MIND
//::
//:://////////////////////////////////////////////

INSTALLATION

* Upload the folder "ts" to the Plugin folder of your EQDKP Installation
* Grant the file /ts/includes/debugReagents.txt rights to be written in (chmod 777 or chmod 644)
* Go to the Administrationpanel, click on Manage Plugins and there on Install
* The Administrationmenu is now extended by a Tradeskill section - you may wish to adjust some settings

//:://////////////////////////////////////////////

UPDATE FROM VERSION 0.96beta
* Backup your existing /ts folder of your EQDKP Installation on your harddrive
* Delete the existing /ts folder on the webserver
* Upload the new /ts folder found in the downloaded archive (actually this file is in it)
* Grant the file /ts/includes/debugReagents.txt rights to be written in (chmod 777 or chmod 644)
* Backup up your Database!
* Did you backup your Database? Well you should, at least the tables:
    eqdkp_tradeskills
    eqdkp_tradeskill_recipes
    eqdkp_tradeskill_users
    eqdkp_users
    eqdkp_user_tradeskills
* Open the file "ts_sql_update_096-0974.sql" (it is located in the archive) an replace the tableprefix "eqdkp_" with the prefix you used during your installation of EQDKP. Don't forget to save the file after editing!
* Run the "ts_sql_update_096-0974.sql" file you just edited in MySQLadmin (e.g.): This will achieve the necessary DB Changes
* You are all set: Go to the adminmenu and adjust the new settings page to your liking

//:://////////////////////////////////////////////

MANUAL FOR USAGE

As Corgan is setting up a Wiki "http://eqdkp.corgan-net.de/wiki/index.php/Tradeskills" I will try to put a up to date manual page up as soon as possible.

//:://////////////////////////////////////////////

IF YOU FIND BUGS CONCERNING THE AUTO-RETRIEVAL OF REAGENTS SEND YOUR BUGREPORTS INCLUDING THE debugreagents.txt FOUND IN THE includes FOLDER TO achaz@lionforge.de

THIS VERSION IS BETA -- EXPECT SOME MINOR BUGS ESPECIALLY IN THE ENGLISH VERSION AS THERE HASN'T BEEN A LOT OF TESTING

//:://////////////////////////////////////////////

KNOWN BUGS:
* if the infosite (allakhazam) is down or very busy the autoretrieval of reagents does not work - this is unfixable
* There are some differences in the naming of recipies in the infosite DB and the tooltip, then it won't be possible to retrieve the reagents correctly - please please send me a bugreport (see above)

//:://////////////////////////////////////////////

CHANGELOG FROM 0.96 TO 0.974:
0.974Beta
files: a lot ;-)
Note: 
* Auto-retrieval of reagents implemented (checks buffed.de or allakhazam.com)
* trade specific filter function for recipies added (idea and first realisation by Aoshi)
* Single-Show-Modus (Idea by Aoshi) click on a profession name to only show that profession or set the according config to only use this mode. It can be very useful on huge databases
* Changed permissions to: admin, manage, confirm, list. Manage now means the right to submit new recipes while confirm only lets you add yourself to the owner list
* Added support for Jewelcrafting (icon not known - does anybody have it?)
* Added support for Cooking (everybody can confirm recipies)
* Admin-Config: Select what tradeskills to be shown and used
* Added an administration panel: You can basically edit every entry in the database
* Users can now select an associated member, so that you can add recipes of the same trade to more than one of your chars
* Added an Edit-function to the recipies, so that a misspelled recipename can be corrected without having to create a new entry (itemcache table got annoyingly full if you had a lot of typos)
* Admin-Config: Select if you want to restrict the professions per char to 2 (+cooking if shown). A lot of DKP-Systems do not have any alts in them but the users sometimes want to add recipies an alt possesses.




