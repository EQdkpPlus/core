/******************************
 * EQdkp ItemSpecials Plugin
 * (c) 2006 - 2007 by WalleniuM [Simon Wallmann]
 * http://www.wallenium.de   
 * ------------------
 *
 * Changed: January 11, 2007
 * 
 ******************************/

README
--------------------------------------------------------------------------------------------------

What is ItemSpecials?
---------------------------------------------
Itemspecials is a toolbase-plugin for eqdkp. It includes SetItems (SetProgress),
SpecialItems and Setright. You can easily setup the Plugin via Admin Panel and much
more.

New Install of Item Specials
---------------------------------------------
1. upload the downloaded itemspecials folder into the plugin folder on your webspace
2. Go to the AdminPanel, Plugins. Install the Itemspecials Plugin.
3. Login as Administrator, go to Global DKP Configuration. There you can setup the Guest Permissions
4. Setup the Permissions for every single user of the DKP
5. Go to ItemSpecials Settings and setup your itemspecials install
6. You're done!

Update from an existing Item Specials installation
---------------------------------------------
ITEMSPECIALS 1.0 or LOWER
1. Uninstall the old Version of ItemSpecials
2. Delete the old itemspecials folder
3. upload the downloaded itemspecials folder into the plugin folder on your webspace
4. Go to the AdminPanel, Plugins. Install the Itemspecials Plugin.
5. Go to ItemSpecials Settings and setup your itemspecials install

ITEMSPECIALS 2.0
1. go to /updates/ folder, edit the sql file,
   change the prefix if needed, add it to database,
2. Thats all ;)

How to use it with a set/nonset dkp?
---------------------------------------------
1. Install it as described above
2. Go to the itemspecials settings in the admin panel
3. enable the setting "Set & Nonset Database difference"
4. insert the two databases in the fields under that option
5. you're done. it should work now


FAQ
---------------------------------------------
Q: I cannot see the settings in the ACP or the links in the header. The
Plugin must be broken

A: No. You have to set the permissions for guests AND every single user registered
in the DKP

Q: Will there be an option to import existing Setitems in a later Version of ItemSpecials?

A: No. I'll never insert such a function. Insert a Raid with 0 DKP, this should work good enough.

Q: I want guests to see the XYZ-plugin
A: Go to the ACP, general settings. There are the global permissions", these are for the guests

Q: i got an error on line 200 or 205
A: This is a template error. Please make shure that all templates are installed right, 
that you uploaded all templates. error 205: delete the template folder and reupload it.
error 200: download the default template, name it like your eqdkp template folder (if you're
using a custom template which is locatet in the folder "blubb", rename the default folder included
in the zip file of this addon to "blubb").

Q: In my config menu page, the links would duplicate:
http://{domain}/{script path}/admin http://{domain}/{script path}/plugins/itemspecials/admin/*.php?s=
A: I added a fix (in 3.0.2). Open 'itemspecials_plugin_class.php' in a texteditor, set the first variable to 'true'

Q: I added a Tier3/TierAQ Setitem. Why is it not listed in Set Progress?
A: You must add the Questitem for that Part of the Set. SetProgress will automatically list the Tier3 
Setitem if the user have the Questitem.