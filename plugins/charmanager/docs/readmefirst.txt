/******************************
 * EQDKP PLUGIN: Charmanager
 * (c) 2006 - 2007 by WalleniuM [Simon Wallmann]
 * http://www.wallenium.de   
 * ------------------
 * readmefirst.txt
 * Changed: January 06, 2007
 * 
 ******************************/

README
--------------------------------------------------------------------------------------------------

What is UserChars?
---------------------------------------------
Your user can select their Char (p.e. used in Raidplan) by theirself. They can 
only choose the free (unselected) chars and their own. 
They can add and edit their Chars. That would make the life og admins much easier ;)

New Install of UserChar
---------------------------------------------
1. upload the downloaded userchar folder into the plugin folder on your webspace
2. Go to the AdminPanel, Plugins. Install the UserChar Plugin.
3. Login as Administrator, go to Global DKP Configuration. There you can setup the Guest Permissions
4. Setup the Permissions for every single user of the DKP
5. You're done!

Update from an existing UserChar installation
---------------------------------------------
1. Uninstall the old Version of Userchar
2. save the /plugins/charmanager/images/upload/ directory. Reupload if you deleted it during upgrade.
2. follow the installation as shown above

FAQ
---------------------------------------------
Q: I cannot see the settings in the ACP or the links in the header. The
Plugin must be broken

A: No. You have to set the permissions for guests AND every single user registered
in the DKP
Q: I want guests to see the XYZ-plugin
A: Go to the ACP, general settings. There are the global permissions", these are for the guests

Q: i got an error on line 200 or 205
A: This is a template error. Please make shure that all templates are installed right, 
that you uploaded all templates. error 205: delete the template folder and reupload it.
error 200: download the default template, name it like your eqdkp template folder (if you're
using a custom template which is locatet in the folder "blubb", rename the default folder included
in the zip file of this addon to "blubb").