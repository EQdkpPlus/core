//::///////////////////////////////////////////////
//::
//:: EQDKP PLUGIN: Ticket - Conversation
//:: © 2006 Achaz (http://www.lionforge.de)
//:: Contact: 
//:: Achaz - Achaz@lionforge.de
//::
//:://////////////////////////////////////////////
//::
//:: DEPENDENCIES:
//:: * EQDKP 1.3.1 (or EQDKP Plus)
//::
//:://////////////////////////////////////////////
//:: 
//:: VERSION: 0.06
//::
//:://////////////////////////////////////////////

INSTALLATION

* Copy the folder "ticket" into the Plugin folder
* Go to the administration panel and find "manage plugins". Click "Install"
* Adjust permissions for Users and administrators
* Edit the plugin specific settings accessable via the administrationpanel

//:://////////////////////////////////////////////

USAGE / FEATURES

* User are able to submit tickets which every admin with proper permission can answer
* More than one answer to a ticket is possible
* Users are able to submit replies to answers by admins
* Email Notification for admins und users is possible (for user the default is on)
* visual notification in the menu if new tickets/answers exist 
* Deletion of tickets is possible

//:://////////////////////////////////////////////

CHANGELOG:

to 0.02:
* Admin: when submitting a reply without a ticket an error is displayed if the username is not found and the message stays in the field
* Small bug with variable assignment fixed

to 0.03
* Adminpanel: Now every ticket is shown immediately, the admin nolonger has to have submitted a ticket himself (titan_flippi Bugreport)
* The "answer with another ticket" link for users is now centered and has an icon

to 0.04
* Adminpanel: Answers to tickets are now shown correctly (titan_flippi Bugreport)
* Language a little bit redone

to 0.05
* Adminpanel: by admins deleted tickets can be undeleted now so it is then possible to submit new answers
* Adminpanel: "answer to tickets" has now also an icon
* User: If a user deletes a ticket without marking possible answers as read they are now marked read automatically
* If tickets are deleted permanently now all replies, replytickets and replies to replytickets are deleted from the DB
* The "Ticket"-Link is now displayed in MainMenu2 as it is more a User thing than a Memberthing

to 0.06
* Installation: tried to fix error that occurs with the default value when creating the field firstreplydate
* New Link for admins in MainMenu2