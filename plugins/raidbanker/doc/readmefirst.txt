/******************************
 * EQdkp Raid Banker
 * Copyright 2006 by WalleniuM
 * ------------------
 *
 * Changed: October 26, 2006
 * 
 ******************************/

README
--------------------------------------------------------------------------------------------------

What is the Raidbanker?
---------------------------------------------
With Raidbanker, you can add and manage the raid banks in eqdkp. You can import them as a log file.

New Install of RaidBanker
---------------------------------------------
1. upload the downloaded raidbanker folder into the plugin folder on your webspace
2. Go to the AdminPanel, Plugins. Install the raidbanker Plugin.
3. Login as Administrator, go to Global DKP Configuration. There you can setup the Guest Permissions
4. Setup the Permissions for every single user of the DKP
5. Go to Raidbanker Settings and setup your raidbanker install
6. You're done!

Update from an existing raidbanker installation
---------------------------------------------
1. Delete the old itemspecials folder
2. upload the downloaded raidbanker folder into the plugin folder on your webspace
3. Uninstall/ Install the plugin
4. Go to Raidbanker Settings and setup your RaidBanker install
5. You're done!

How to insert a banker with a logfile
---------------------------------------------



FAQ
---------------------------------------------
Q: I cannot see the settings in the ACP or the links in the header. The
Plugin must be broken

A: No. You have to set the permissions for guests AND every single user registered
in the DKP

Q: I want the raidbanker to translate the items on import
A: Download a copy of the itemlist.xml and upload it to raidbanker/include/. Thats all.

Q: I want guests to see the XYZ-plugin
A: Go to the ACP, general settings. There are the global permissions", these are for the guests

Q: i got an error on line 200 or 205
A: This is a template error. Please make shure that all templates are installed right, 
that you uploaded all templates. error 205: delete the template folder and reupload it.
error 200: download the default template, name it like your eqdkp template folder (if you're
using a custom template which is locatet in the folder "blubb", rename the default folder included
in the zip file of this addon to "blubb").