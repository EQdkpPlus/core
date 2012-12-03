<?php
/******************************
* EQdkp
* Copyright 2002-2003
* Licensed under the GNU GPL.  See COPYING for full terms.
* ------------------
* lang_main.php
* begin: Wed December 18 2002
*
* $Id$
*
******************************/

if ( !defined('EQDKP_INC') )
{
     die('Do not access this file directly.');
}

// %1\$<type> prevents a possible error in strings caused
//      by another language re-ordering the variables
// $s is a string, $d is an integer, $f is a float

$lang['ENCODING'] = 'iso-8859-1';
$lang['XML_LANG'] = 'fr';

// Linknames
$lang['rp_link_name']   = "Raidplanner";

// Titles
$lang['admin_title_prefix']   = "%1\$s %2\$s Admin";
$lang['listadj_title']        = 'Group ment Listing';
$lang['listevents_title']     = 'Valeur d\'événement';
$lang['listiadj_title']       = 'Individual ment Listing';
$lang['listitems_title']      = 'Valeurs des objets';
$lang['listnews_title']       = 'News Entries';
$lang['listmembers_title']    = 'Member Standings';
$lang['listpurchased_title']  = 'Historique des objets';
$lang['listraids_title']      = 'Liste des Raids';
$lang['login_title']          = 'Login';
$lang['message_title']        = 'EQdkp: Message';
$lang['register_title']       = 'S\'enregistrer';
$lang['settings_title']       = 'Options de compte';
$lang['stats_title']          = "%1\$s Statistiques";
$lang['summary_title']        = 'Résumé des nouvelles';
$lang['title_prefix']         = "%1\$s %2\$s";
$lang['viewevent_title']      = "Affichage de l\'Historique des Raids pour %1\$s";
$lang['viewitem_title']       = "Affichage de l\'Historique des achats pour %1\$s";
$lang['viewmember_title']     = "Historique pour %1\$s";
$lang['viewraid_title']       = 'Résumé du raid';

// Main Menu
$lang['menu_admin_panel'] = 'Administration';
$lang['menu_events'] = 'Evénements';
$lang['menu_itemhist'] = 'Historique des objets';
$lang['menu_itemval'] = 'Valeur des objets';
$lang['menu_news'] = 'News';
$lang['menu_raids'] = 'Raids';
$lang['menu_register'] = 'S\'enregistrer';
$lang['menu_settings'] = 'Profil';
$lang['menu_members'] = 'Characters';
$lang['menu_standings'] = 'Membres';
$lang['menu_stats'] = 'Statistiques';
$lang['menu_summary'] = 'Résumé';

// Column Headers
$lang['account'] = 'Compte';
$lang['action'] = 'Action';
$lang['active'] = 'Activé';
$lang['add'] = 'Ajouter';
$lang['added_by'] = 'Ajouté par';
$lang['adjustment'] = 'Ajustage';
$lang['administration'] = 'Administration';
$lang['administrative_options'] = 'Options d\'Administrative';
$lang['admin_index'] = 'Index de l\'Admin';
$lang['attendance_by_event'] = 'Présence par Event';
$lang['attended'] = 'Accompagné';
$lang['attendees'] = 'Participants';
$lang['average'] = 'Moyenne';
$lang['buyer'] = 'Acheteur';
$lang['buyers'] = 'Acheteur';
$lang['class'] = 'Classe';
$lang['armor'] = 'Armure';
$lang['type'] = 'Armure';
$lang['class_distribution'] = 'Distribution par Classe';
$lang['class_summary'] = "Résumé des Classes : %1\$s to %2\$s";
$lang['configuration'] = 'Configuration';
$lang['config_plus']	= 'PLUS Settings';
$lang['plus_vcheck']	= 'Update Check';
$lang['current'] = 'Actuel';
$lang['date'] = 'Date';
$lang['delete'] = 'Supprimer';
$lang['delete_confirmation'] = 'Confirmation de suppression';
$lang['dkp_value'] = "%1\$s Valeur";
$lang['drops'] = 'Drops';
$lang['earned'] = 'Gagné';
$lang['enter_dates'] = 'Entrez les Dates';
$lang['eqdkp_index'] = 'Index d\'EQdkp';
$lang['eqdkp_upgrade'] = 'Mise à jour d\'EQdkp';
$lang['event'] = 'Evénement';
$lang['events'] = 'Evénements';
$lang['filter'] = 'Filtre';
$lang['first'] = 'Premier';
$lang['rank'] = 'Rang';
$lang['general_admin'] = 'Administration Générale';
$lang['get_new_password'] = 'Choisissez un nouveau mot de passe';
$lang['group_adj'] = 'Ajust. de Groupe';
$lang['group_adjustments'] = 'Ajustages de Groupe';
$lang['individual_adjustments'] = 'Ajustages Individuels';
$lang['individual_adjustment_history'] = 'Historique de l\'Ajustage Individuel';
$lang['indiv_adj'] = 'Ajust. Indiv.';
$lang['ip_address'] = 'Addresse IP';
$lang['item'] = 'Objet';
$lang['items'] = 'Objets';
$lang['item_purchase_history'] = 'Historique des Objets achetés';
$lang['last'] = 'Dernier';
$lang['lastloot'] = 'Dernier Loot';
$lang['lastraid'] = 'Dernier Raid';
$lang['last_visit'] = 'Dernière Visite';
$lang['level'] = 'Level';
$lang['log_date_time'] = 'Date/temps de cette session';
$lang['loot_factor'] = 'Facteur de loot';
$lang['loots'] = 'Loots';
$lang['manage'] = 'Gérer';
$lang['member'] = 'Membre';
$lang['members'] = 'Membres';
$lang['members_present_at'] = "Membres présents à %1\$s sur %2\$s";
$lang['miscellaneous'] = 'Divers';
$lang['name'] = 'Nom';
$lang['news'] = 'News';
$lang['note'] = 'Note';
$lang['online'] = 'En ligne';
$lang['options'] = 'Options';
$lang['paste_log'] = 'Copiez un log ci-dessous';
$lang['percent'] = 'Pourcent';
$lang['permissions'] = 'Permissions';
$lang['per_day'] = 'Par Jour';
$lang['per_raid'] = 'Par Raid';
$lang['pct_earned_lost_to'] = '% Earned Lost to';
$lang['preferences'] = 'Préférences';
$lang['purchase_history_for'] = "Historique d\'achat pour %1\$s";
$lang['quote'] = 'Citation';
$lang['race'] = 'Course';
$lang['raid'] = 'Raid';
$lang['raids'] = 'Raids';
$lang['raid_id'] = 'ID de Raid';
$lang['raid_attendance_history'] = 'Raid Attendance History';
$lang['raids_lifetime'] = "Durée de vie (%1\$s - %2\$s)";
$lang['raids_x_days'] = "Les %1\$d Jours Derniers";
$lang['rank_distribution'] = 'Distribution par Rang';
$lang['recorded_raid_history'] = "Historique des raids enregistré pour %1\$s";
$lang['reason'] = 'Raison';
$lang['registration_information'] = 'Information d\'Inscription';
$lang['result'] = 'Résultat';
$lang['session_id'] = 'ID de Session';
$lang['settings'] = 'Options';
$lang['spent'] = 'Dépensé';
$lang['summary_dates'] = "Résume du raid : %1\$s à %2\$s";
$lang['themes'] = 'Thèmes';
$lang['time'] = 'Temps';
$lang['total'] = 'Total';
$lang['total_earned'] = 'Total Gagné';
$lang['total_items'] = 'Total d\'Objets';
$lang['total_raids'] = 'Total Raids';
$lang['total_spent'] = 'Total Dépensé';
$lang['transfer_member_history'] = 'Transferer l\'historique d\'un membre';
$lang['turn_ins'] = 'Restituer';
$lang['type'] = 'Type';
$lang['update'] = 'Mise à jour';
$lang['updated_by'] = 'Mis à jour Par';
$lang['user'] = 'Utilisateur';
$lang['username'] = 'Nom d\'Utilisateur';
$lang['value'] = 'Valeur';
$lang['view'] = 'Voir';
$lang['view_action'] = 'Voir l\'Action';
$lang['view_logs'] = 'Voir les logs';

// Page Foot Counts
$lang['listadj_footcount']               = "...  %1\$d ajustages(s) trouvé(s) / %2\$d par page";
$lang['listevents_footcount']            = "...  %1\$d événement(s) trouvé(s) / %2\$d per page";
$lang['listiadj_footcount']              = "...  %1\$d ajustages(s) individuel(s) trouvé(s) / %2\$d par page";
$lang['listitems_footcount']             = "...  %1\$d item(s) unique(s) trouvé(s) / %2\$d per page";
$lang['listmembers_active_footcount']    = "...  %1\$d membre(s) actif(s) trouvé(s) / %2\$stout afficher</a>";
$lang['listmembers_compare_footcount']   = "... comparing %1\$d members";
$lang['listmembers_footcount']           = "...  %1\$d membres";
$lang['listnews_footcount']              = "...  %1\$d news trouvée(s) / %2\$d per page";
$lang['listpurchased_footcount']         = "...  %1\$d objet(s) trouvé(s) / %2\$d per page";
$lang['listraids_footcount']             = "...  %1\$d raid(s) trouvé(s) / %2\$d per page";
$lang['stats_active_footcount']          = "...  %1\$d membre(s) actif(s) trouvé(s) / %2\$stout afficher</a>";
$lang['stats_footcount']                 = "...  %1\$d membre(s)trouvé(s) ";
$lang['viewevent_footcount']             = "...  %1\$d raids(s)trouvé(s) ";
$lang['viewitem_footcount']              = "...  %1\$d objets(s)trouvé(s) ";
$lang['viewmember_adjustment_footcount'] = "...  %1\$d ajustage(s) individuel(s)trouvé(s) ";
$lang['viewmember_item_footcount']       = "...  %1\$d objet(s) acheté(s) trouvé(s) / %2\$d per page";
$lang['viewmember_raid_footcount']       = "...  %1\$d participant(s) de raid trouvé(s) / %2\$d per page";
$lang['viewraid_attendees_footcount']    = "...  %1\$d participant(s)trouvé(s) ";
$lang['viewraid_drops_footcount']        = "...  %1\$d drop(s)trouvé(s) ";

// Submit Buttons
$lang['close_window'] = 'Fermer la fenêtre';
$lang['compare_members'] = 'Comparer les Membres';
$lang['create_news_summary'] = 'Créer le Résumé des News';
$lang['login'] = 'Connection';
$lang['logout'] = 'Déconnection';
$lang['log_add_data'] = 'Ajouter les Informations au Formulaire';
$lang['lost_password'] = 'Mot de passe Perdu';
$lang['no'] = 'Non';
$lang['proceed'] = 'Procéder';
$lang['reset'] = 'Remise à zéro';
$lang['set_admin_perms'] = 'Fixer les permissions d\'Administration';
$lang['submit'] = 'Envoyer';
$lang['upgrade'] = 'Mettre à jour';
$lang['yes'] = 'Oui';

// Form Element Descriptions
$lang['admin_login'] = 'Login Administrateur';
$lang['confirm_password'] = 'Confirmitaion du mot de passe';
$lang['confirm_password_note'] = 'Vous devez seulement confirmer votre nouveau mot de passe si vous l\'avez changé plus haut';
$lang['current_password'] = 'Mot de passe Actuel';
$lang['current_password_note'] = 'Vous devez confirmer votre mot de passe actuel si vous souhaitez changer votre nom d\'utilisateur ou votre mot de passe';
$lang['email'] = 'Email';
$lang['email_address'] = 'Addresse Email';
$lang['ending_date'] = 'Date de fin';
$lang['from'] = 'De';
$lang['guild_tag'] = 'Tag de Guilde';
$lang['language'] = 'Langue';
$lang['new_password'] = 'Nouveau mot de passe';
$lang['new_password_note'] = 'Vous avez seulement besoin de fournir un nouveau mot de passe si vous voulez le changer';
$lang['password'] = 'Mot de passe';
$lang['remember_password'] = 'Se souvenir de moi (cookie)';
$lang['starting_date'] = 'Date de commencement';
$lang['style'] = 'Style';
$lang['to'] = 'A';
$lang['username'] = 'Nom d\'utilisateur';
$lang['users'] = 'Utilisateurs';

// Pagination
$lang['next_page'] = 'Page Suivante';
$lang['page'] = 'Page';
$lang['previous_page'] = 'Page Précédente';

// Permission Messages
$lang['noauth_default_title'] = 'Permission Refusée';
$lang['noauth_u_event_list'] = 'Vous n\'avez pas la permission de lister les événements.';
$lang['noauth_u_event_view'] = 'Vous n\'avez pas la permission de voir les événements.';
$lang['noauth_u_item_list'] = 'Vous n\'avez pas la permission de lister les objets.';
$lang['noauth_u_item_view'] = 'Vous n\'avez pas la permission de voir les items.';
$lang['noauth_u_member_list'] = 'You do not have permission to view member standings.';
$lang['noauth_u_member_view'] = 'Vous n\'avez pas la permission de voir l\'historique des membres.';
$lang['noauth_u_raid_list'] = 'Vous n\'avez pas la permission de lister les raids.';
$lang['noauth_u_raid_view'] = 'Vous n\'avez pas la permission de voir les raids.';

// Submission Success Messages
$lang['add_itemvote_success'] = 'Votre vote sur l\'objet a été enregistré.';
$lang['update_itemvote_success'] = 'Votre vote sur l\'objet a été mis à jour.';
$lang['update_settings_success'] = 'Les options d\'utilisateur ont été mises à jour.';

// Form Validation Errors
$lang['fv_alpha_attendees'] = 'Les noms des personnages d\'EverQuest contiennent uniquement des caractères alphabétiques.';
$lang['fv_already_registered_email'] = 'Cette adresse email est déjà enregistrée.';
$lang['fv_already_registered_username'] = 'Ce nom d\'utilisateur est déjà enregistré.';
$lang['fv_difference_transfer'] = 'Un transfert d\'historique doit être fait entre deux personnes différentes.';
$lang['fv_difference_turnin'] = 'Une restitution doit être faite entre deux personnes différentes.';
$lang['fv_invalid_email'] = 'L\'adresse e-mail n\'apparait pas commme une adresse valide.';
$lang['fv_match_password'] = 'Les champs mot de passe doivent correspondrent.';
$lang['fv_member_associated']  = "%1\$s est déjà associé avec un autre compte.";
$lang['fv_number'] = 'Doit être un nombre.';
$lang['fv_number_adjustment'] = 'La valeur du champ d\'ajustement doit être un nombre.';
$lang['fv_number_alimit'] = 'La limite du champ d\'ajustement doit être un nombre.';
$lang['fv_number_ilimit'] = 'La limite du champ des items doit être un nombre.';
$lang['fv_number_inactivepd'] = 'La période d\'inactivité doit être un nombre.';
$lang['fv_number_pilimit'] = 'La limite des items achetés doit être un nombre.';
$lang['fv_number_rlimit'] = 'La limite des raids doit être un nombre.';
$lang['fv_number_value'] = 'La valeur du champ doit être un nombre.';
$lang['fv_number_vote'] = 'Le champ du vote doit être un nombre.';
$lang['fv_date'] = 'Il faut que la date soit une date valide.';
$lang['fv_range_day'] = 'Le champ du jour doit être un nombre entre 1 et 31.';
$lang['fv_range_hour'] = 'Le champ de l\'heure doit être un nombre entre 0 et 23.';
$lang['fv_range_minute'] = 'Le champs des minutes doit être un nombre entre 0 et 59.';
$lang['fv_range_month'] = 'Le champs des mois doit être un nombre entre 1 et 12.';
$lang['fv_range_second'] = 'Le second champs doit être un entier entre 0 et 59.';
$lang['fv_range_year'] = 'Le champ de l\'année doit être un entier entier d\'une valeur minimale de 1998.';
$lang['fv_required'] = 'Champs requis.';
$lang['fv_required_acro'] = 'Le champ de l\'acronyme de la guilde est requis.';
$lang['fv_required_adjustment'] = 'La valeur du champ d\'ajustement est requise.';
$lang['fv_required_attendees'] = 'Il doit y avoir au moins un participant dans ce raid.';
$lang['fv_required_buyer'] = 'Un acheteur doit être sélectionné.';
$lang['fv_required_buyers'] = 'Au moins un acheteur doit être sélectionné.';
$lang['fv_required_email'] = 'L\'adresse email est requise.';
$lang['fv_required_event_name'] = 'Un événement doit être sélectionné.';
$lang['fv_required_guildtag'] = 'Le tag de la guilde est requis.';
$lang['fv_required_headline'] = 'Le titre principal est requis.';
$lang['fv_required_inactivepd'] = 'Si le champ "cacher les membres inactifs" est sur "Oui", une valeur pour la période d\'inactivité doit aussi être définie.';
$lang['fv_required_item_name'] = 'Le nom de l\'objet doit être ajouté ou alors sélectionné parmis les objets existants.';
$lang['fv_required_member'] = 'Un membre doit être sélectionné.';
$lang['fv_required_members'] = 'Au moins un membre doit être sélectionné.';
$lang['fv_required_message'] = 'Le champs message est requis.';
$lang['fv_required_name'] = 'Le champs nom est requis.';
$lang['fv_required_password'] = 'Le champs mot de passe est requis.';
$lang['fv_required_raidid'] = 'Un raid doit être sélectionné.';
$lang['fv_required_user'] = 'Le nom d\'utilisateur est requis.';
$lang['fv_required_value'] = 'Le champ valeur est requis.';
$lang['fv_required_vote'] = 'Le champ vote est requis.';

// Miscellaneous
$lang['added'] = 'Ajouté';
$lang['additem_raidid_note'] = "Les raids plus vieux que 2 semaines ne sont pas affiché / %1\$safficher tout</a>";
$lang['additem_raidid_showall_note'] = 'Affiche tous les raids';
$lang['addraid_datetime_note'] = 'Si vous consultez un log, la date et le temps seront trouvés automatiquement.';
$lang['addraid_value_note'] = 'pour un bonus unique. Si vous laissez vide, les valeurs seront celles des pré-réglages.';
$lang['add_items_from_raid'] = 'Ajouter des items de ce raid';
$lang['deleted'] = 'Supprimé';
$lang['done'] = 'Prêt';
$lang['enter_new'] = 'Entrez un nouveau';
$lang['error'] = 'Erreur';
$lang['head_admin'] = 'Admin pricipal';
$lang['hold_ctrl_note'] = 'Maintenir CTRL pour des sélections multiples.';
$lang['list'] = 'Lister';
$lang['list_groupadj'] = 'Lister les Ajustages de Groupe';
$lang['list_events'] = 'Lister les Evénements';
$lang['list_indivadj'] = 'Lister les Ajustages Individuels';
$lang['list_items'] = 'Lister les Objets';
$lang['list_members'] = 'Lister les Membres';
$lang['list_news'] = 'Lister les News';
$lang['list_raids'] = 'Lister les Raids';
$lang['may_be_negative_note'] = 'peut être negatif';
$lang['not_available'] = 'Pas disponible';
$lang['no_news'] = 'Pas de nouvelles entrés trouvées.';
$lang['of_raids'] = "%1\$d%% des raids";
$lang['or'] = 'OU';
$lang['powered_by'] = 'Powered by';
$lang['preview'] = 'Visionner';
$lang['required_field_note'] = 'Les champs marqués par * sont requis.';
$lang['select_1ofx_members'] = "Sélection d\'un 1 sur %1\$d membres...";
$lang['select_existing'] = 'Sélection existante';
$lang['select_version'] = 'Sélectionnez la version d\'EQdkp que vous allez mettre à jour';
$lang['success'] = 'Réussite';
$lang['s_admin_note'] = 'Ces champs ne sont pas modifiables par les utilisateurs.';
$lang['transfer_member_history_description'] = 'Ceci va transferer toute l\'historique d\'un membre (raids, objets, ajustages) à un autre.';
$lang['updated'] = 'Mis à jour';
$lang['upgrade_complete'] = 'Votre installation d\'EQDKP à été mis à jour avec succès.<br /><br /><b class="negative">Pour plus de sécurité, supprimez ce fichier !</b>';

// Settings
$lang['account_settings'] = 'Options de compte';
$lang['adjustments_per_page'] = 'Ajustages par Page';
$lang['basic'] = 'Basique';
$lang['events_per_page'] = 'Evénements par Page';
$lang['items_per_page'] = 'Objets par Page';
$lang['news_per_page'] = 'News par Page';
$lang['raids_per_page'] = 'Raids par Page';
$lang['associated_members'] = 'Membres Associés';
$lang['guild_members'] = 'Membre(s) de Guilde';
$lang['default_locale'] = 'Local Défaut';


// Error messages
$lang['error_account_inactive'] = 'Votre compte est inactif.';
$lang['error_already_activated'] = 'Ce compte a déjà été activé.';
$lang['error_invalid_email'] = 'Une adresse e-mail valide est requise.';
$lang['error_invalid_event_provided'] = 'Un ID d\'événement valide est requis.';
$lang['error_invalid_item_provided'] = 'Un ID d\'objet valide est requis.';
$lang['error_invalid_key'] = 'Vous avez fourni une clef d\'activation invalide.';
$lang['error_invalid_name_provided'] = 'Un nom de membre valide est requis.';
$lang['error_invalid_news_provided'] = 'Un ID de news valide est requis.';
$lang['error_invalid_raid_provided'] = 'Un ID de raid valide est requis.';
$lang['error_user_not_found'] = 'Un nom d\'utilisateur valide n\'a pas été fourni.';
$lang['incorrect_password'] = 'Mot de passe incorrect.';
$lang['invalid_login'] = 'Vous avez fourni un nom d\'utilisateur incorrect ou invalide ou un mauvais mot de passe.';
$lang['not_admin'] = 'Vous n\'êtes pas administrateur.';

// Registration
$lang['account_activated_admin']   = 'Ce compte a été activé. Un email a été envoyé à l\'utilisateur pour l\'avertir de ces changements.The account has been activated.';
$lang['account_activated_user']    = "Votre compte a été activé et vous pouvez maintenant vous %1\$sconnecter%2\$s.";
$lang['password_sent'] = 'Votre nouveau mot de passe de compte vous a été envoyé par email.';
$lang['register_activation_self']  = "Votre compte a été créé, mais avant de pouvoir l\'utiliser vous devez l\'activer.<br /><br />Un email a été envoyé à %1\$s avec les informations pour activer votre compte.";
$lang['register_activation_admin'] = "Votre compte a été créé, mais avant de pouvoir l\'utiliser un administrateur doit l\'activer.<br /><br />Un email a été envoyé à %1\$s avec plus d\'informations.";
$lang['register_activation_none']  = "Votre compte a été créé et vous pouvez maintenant vous %1\$sconnecter%2\$s.<br /><br />Un email a été envoyé à %3\$s avec plus d\'informations.";

//plus
$lang['news_submitter'] = 'submitted by';
$lang['news_submitat'] = 'at';
$lang['droprate_loottable'] = "Loot Table for";
$lang['droprate_name'] = "Item Name";
$lang['droprate_count'] = "Count";
$lang['droprate_drop'] = "Drop %";

$lang['Itemsearch_link'] = "Item-Search";
$lang['Itemsearch_search'] = "Item Search :";
$lang['Itemsearch_searchby'] = "Search by :";
$lang['Itemsearch_item'] = "Item ";
$lang['Itemsearch_buyer'] = "Buyer ";
$lang['Itemsearch_raid'] = "Raid ";
$lang['Itemsearch_unique'] = "Unique item results :";
$lang['Itemsearch_no'] = "No";
$lang['Itemsearch_yes'] = "Yes";

$lang['bosscount_player'] = "Player: ";
$lang['bosscount_raids'] = "Raids: ";
$lang['bosscount_items'] = "Items: ";
$lang['bosscount_dkptotal'] = "total DKP: ";

//MultiDKP
$lang['Plus_menuentry'] 			= "EQDKP Plus";
$lang['Multi_entryheader'] 		= "MultiDKP - Add Pool";
$lang['Multi_pageheader'] 		= "MultiDKP - Show Pools";
$lang['Multi_events'] 				= "Events:";
$lang['Multi_eventname'] 				= "Eventname";
$lang['Multi_discnottolong'] 	= "(Name of row) - this one should not be too long, the table will get large,. Choose p.e MC, BWL, AQ etc. !";
$lang['Multi_kontoname_short']= "Accountname:";
$lang['Multi_discr'] 					= "Description:";
$lang['Multi_events'] 				= "Events of this Pool";

$lang['Multi_addkonto'] 			  = "Add MultiDKP Pool";
$lang['Multi_updatekonto'] 			= "Change Pool";
$lang['Multi_deletekonto'] 			= "Delete Pool";
$lang['Multi_viewkonten']			  = "Show MultiDKP Pools";
$lang['Multi_chooseevents']			= "Choose Events";
$lang['multi_footcount'] 				= "... %1\$d DKP Pools / %2\$d per page";
$lang['multi_error_invalid']    = "No Pools assigned....";
$lang['Multi_required_event']   = "You must choose at least one event!";
$lang['Multi_required_name']    = "You must insert a name!";
$lang['Multi_required_disc']    = "You must insert a description!";
$lang['Multi_admin_add_multi_success'] = "The Pool %1\$s ( %2\$s ) with the events %3\$s was added to the database.";
$lang['Multi_admin_update_multi_success'] = "The Pool %1\$s ( %2\$s ) with the events %3\$s was changed in the database.";
$lang['Multi_admin_delete_success']           = "The Pool %1\$s was deleted in the database.";
$lang['Multi_confirm_delete']    = 'Are you really sure you want to delete that Pool?';

$lang['Multi_total_cost']   										= 'Total points for this Pool';
$lang['Multi_Accs']    													= 'MultiDKP Pool';

//update
$lang['upd_eqdkp_status']    										= 'EQDKP update status';
$lang['upd_system_status']    									= 'System status';
$lang['upd_template_status']    								= 'Template status';
$lang['upd_gamefile_status']                    = 'Game Status';
$lang['upd_update_need']    										= 'Update necessary!';
$lang['upd_update_need_link']    								= 'Install all required components';
$lang['upd_no_update']    											= 'No update necessary. The system is up to date.';
$lang['upd_status']    													= 'Status';
$lang['upd_state_error']    										= 'Error';
$lang['upd_sql_string']    											= 'SQL command';
$lang['upd_sql_status_done']    								= 'done';
$lang['upd_sql_error']    											= 'Error';
$lang['upd_sql_footer']    											= 'SQL command executed';
$lang['upd_sql_file_error']    									= 'Error: The required SQL File %1\$s could not be found!';
$lang['upd_eqdkp_system_title']    							= 'EQDKP System component update';
$lang['upd_plus_version']    										= 'EQDKP Plus version';
$lang['upd_plus_feature']    										= 'Feature';
$lang['upd_plus_detail']    										= 'Details';
$lang['upd_update']    													= 'Update';
$lang['upd_eqdkp_template_title']    						= 'EQDKP template update';
$lang['upd_eqdkp_gamefile_title']               = 'EQDKP game update';
$lang['upd_gamefile_availversion']              = 'Available Version';
$lang['upd_gamefile_instversion']               = 'Installed Version';
$lang['upd_template_name']    									= 'Template name';
$lang['upd_template_state']    									= 'Template status';
$lang['upd_template_filestate']    							= 'Template folder available';
$lang['upd_link_install']    										= 'Update';
$lang['upd_link_reinstall']    									= 're-install';
$lang['upd_admin_need_update']    							= 'A database error has been detected. The system is not up to date and needs to be updated.';
$lang['upd_admin_link_update']									= 'Click here to solve the problem.';
$lang['upd_backto']    													= 'Back to overview';

// Event Icon
$lang['event_icon_header']    								  = 'Select event icon';

//update Itemstats
$lang['updi_header']    								    	= 'Refresh Itemstats data';
$lang['updi_header2']    								    	= 'Itemstats information';
$lang['updi_action']    								    	= 'action';
$lang['updi_notfound']    								    = 'Not found';
$lang['updi_writeable_ok']    							  = 'File is writable';
$lang['updi_writeable_no']    								= 'File is NOT writable';
$lang['updi_help']    								    		= 'Description';
$lang['updi_footcount']    								    = 'Item refreshed';
$lang['updi_curl_bad']    								    = 'The required PHP function cURL could not be found. Maybe Itemstats will not work properly. Please contact your administrator.';
$lang['updi_curl_ok']    								    	= 'cURL found.';
$lang['updi_fopen_bad']    								    = 'The required PHP function fopen could not be found. Maybe Itemstats will not work properly. Please contact your administrator.';
$lang['updi_fopen_ok']    								    = 'fopen found.';
$lang['updi_nothing_found']						    		= 'No items found';
$lang['updi_itemscount']  						    		= 'Itemcache entries:';
$lang['updi_baditemscount']						    		= 'Bad entries:';
$lang['updi_items']										    		= 'Items in database:';
$lang['updi_items_duplicate']					    		= '{With double items}';
$lang['updi_show_all']    								    = 'List all items with Itemstats';
$lang['updi_refresh_all']    								  = 'Delete all Items and refresh them.';
$lang['updi_refresh_bad']    								  = 'Refresh only bad items';
$lang['updi_refresh_raidbank']    						= 'Refresh Raidbanker items';
$lang['updi_refresh_tradeskill']   						= 'Refresh Tradeskill items';
$lang['updi_help_show_all']    								= 'Therby all items will be shown with their stats. Bad stats will be refreshed. (recommended)';
$lang['updi_help_refresh_all']  							= 'Deletes the current Itemcache and tries to refresh all items that are listed in EQDKP. WARNING: If you share your Itemcache with a forum, the items from the forum cannot be refreshed. Depending on your webservers speed and the availability of Allakhazam.com this action could take several minutes. Possibly your webserver settings forbid a successful execution. In this case please contact your administrator.';
$lang['updi_help_refresh_bad']    						= 'Deletes all bad items from the cache and refreshes them.';
$lang['updi_help_refresh_raidbank']    				= 'If Raidbanker is installed, Itemstats uses the entered items of the banker.';
$lang['updi_help_refresh_Tradeskill']    			= 'When Tradeskill is installed, the entered items will be updated by Itemstats.';

$lang['updi_active'] 					   							= 'activated';
$lang['updi_inactive']    										= 'deactivated';

$lang['fontcolor']    			  = 'Fontcolor';
$lang['Warrior']    					= 'Warrior';
$lang['Rogue']    						= 'Rogue';
$lang['Hunter']    						= 'Hunter';
$lang['Paladin']    					= 'Paladin';
$lang['Priest']    						= 'Priest';
$lang['Druid']    						= 'Druid';
$lang['Shaman']    						= 'Shaman';
$lang['Warlock']    					= 'Warlock';
$lang['Mage']    							= 'Mage';

# Reset DB Feature
$lang['reset_header']    			= 'Reset EQDKP Data';
$lang['reset_infotext']  			= 'DANGER!!! Deleting the data cant be restore!!! Make a backup first. To confirm the action, insert DELETE into the editbox.';
$lang['reset_type']    				= 'Data type';
$lang['reset_disc']    				= 'Description';
$lang['reset_sec']    				= 'Certificate';
$lang['reset_action']    			= 'Action';

$lang['reset_news']					  = 'News';
$lang['reset_news_disc']		  = 'Delete all news from the database.';
$lang['reset_dkp'] 					  = 'DKP';
$lang['reset_dkp_disc']			  = 'Delete all raids and items from the database and reset all DKP points to zero.';
$lang['reset_ALL']   					= 'ALL';
$lang['reset_ALL_DISC']				= 'Delete every raid, item an members. Complete data reset. (Do not delete the users).';

$lang['reset_confirm_text']	  = ' insert here =>';
$lang['reset_confirm']			  = 'DELETE';

// Armory Menu
$lang['lm_armorylink1']				= 'Armory';
$lang['lm_armorylink2']				= 'Talents';
$lang['lm_armorylink3']				= 'Guild';

$lang['updi_update_ready']			= 'The items were successfully updated. You can <a href="#" onclick="javascript:parent.closeWindow()" >close</a> this window.';
$lang['updi_update_alternative']= 'Alternative update method to avoid timeouts.';
$lang['zero_sum']				= ' on Zero SUM DKP';

//Hybrid
$lang['Hybrid']				= 'Hybrid';

$lang['Jump_to'] 				= 'watch the video on ';
$lang['News_vid_help'] 			= 'To embed videos just post the link to the video without [tags]. supported videosites: google video, youtube, myvideo, clipfish, sevenload, metacafe and streetfire. ';

$lang['SubmitNews'] 		   = 'Submit News';
$lang['SubmitNews_help'] 	   = 'You have a good News? Submit the News and share with all Eqdkp Plus Users.';

$lang['MM_User_Confirm']	   = 'Select your Admin Account? If you take of you Admin Permission, this can only be restored in the Database';

$lang['beta_warning']	   	   = 'Warning this EQDKP-Plus Beta Version must not be used on a live system! This Version stop working if a stable version is available. Check <a href="http://www.eqdkp-plus.com" >www.eqdkp-plus.com</a> for updates!';

$lang['news_comment']        = 'Comment';
$lang['news_comments']       = 'Comments';
$lang['comments_no_comments']	   = 'No entries';
$lang['comments_comments_raid']	   = 'Comments';
$lang['comments_write_comment']	   = 'write a comment';
$lang['comments_send_comment']	   = 'save comment';
$lang['comments_save_wait']	   	   = 'Please wait, comment is saving...';

$lang['news_nocomments'] 	 		    = 'Disallow Comments';
$lang['news_readmore_button']  			  	= 'Extend News';
$lang['news_readmore_button_help']  			  	= 'To use the extended Newstext, click here:';
$lang['news_message'] 				  	= 'Newstext';
$lang['news_permissions']			  	= 'Permissions';

$lang['news_permissions_text']			= 'Dont show news for';
$lang['news_permissions_guest']			= 'Guest';
$lang['news_permissions_member']		= 'Guest and Members (only Admin can see)';
$lang['news_permissions_all']			= 'Free for all';
$lang['news_readmore'] 				  	= 'Read more...';

$lang['recruitment_open']				= 'Recruitment open';
$lang['recruitment_contact']			= 'contact';

$lang['sig_conf']						= 'click the image to get the BB Code';
$lang['sig_show']						= 'show WoW signatur for your forum';

//Shirtshop
$lang['service']					    = 'service';
$lang['shirt_ad1']					    = 'Go to the Shirt-shop. <br> get your own shirt now!';
$lang['shirt_ad2']					    = 'Choose your Char';
$lang['shirt_ad3']					    = 'welcome to your guild shop ';
$lang['shirt_ad4']					    = 'Wähle eines der vorgefertigten Produkte aus, oder erstell Dir mit dem Creator ein komplett eigenes Shirt.<br>
										   Du kannst jedes Shirt nach Deinen Bedürfnissen anpassen und jeden Schriftzug verändern.<br>
										   Unter Motive findest alle zur Verfügung stehenden Motive!';
$lang['error_iframe']					= "Your browser doesn't. support Frames!";
$lang['new_window']						= 'Open Shop in a new windows';
$lang['your_name']						= 'YOUR NAME';
$lang['your_guild']						= 'YOUR GUILD';
$lang['your_server']					= 'YOUR SERVER';

//Last Raids
$lang['last_raids']					    = 'Last Raids';

$lang['voice_error']				    = 'No connection to the server.';

$lang['login_bridge_notice']		    = 'Login - CMS-Bridge is active. Use your CSM/Board Data to login.';

$lang['ads_remove']		    			= 'support EQdkp-Plus';
$lang['ads_header']	    				= 'Support EQDKP-Plus';
$lang['ads_text']		    			= 'EQDKP-Plus is a hobby-project which was mainly developed and is kept updated by two private persons. 
											At the beginning this wasn’t a problem but after three years of constant programming and updating, 
											the cost for this grows unfortunately over our heads. Only for the developer and the update-server we 
											have to spend 600€ per year now and there are also another 1000€ in costs for an attorney, since there are 
											some legal problems at this time. For the future we have also planned many more server-based features which will 
											result in another needed server. Costs for our new forum and the designer of our new homepage add to this. 
											All these named costs plus our more and more invested working time cannot be paid anymore by ourselves. 
											For this reason and not wanting the project to die you will now sparely see ad-banners in EQDKP-Plus. 
											These banners are very limited for content, so you will not see any pornographic banners or gold/item-selling vendors.

											You do have options to turn these banners off:
										  <ol>
										  <li> Log on to <a href="http://www.eqdkp-plus.com">www.eqdkp-plus.com</a> and donate any amount you want. 
										  		Please think about it, how much is EQDKP-Plus worth for you. 
										  		After a donation (Amazon or Paypal) you will get an eMail with a serial-key for the 
										  		respective major or major-version..<br><br></li>
										  <li> Log on to <a href="http://www.eqdkp-plus.com">www.eqdkp-plus.com</a> and donate any amount exceeding 50€. 
										  		You will earn premium status and get a livetime-premium-account, making you eligible for 
										  		free upgrades to new major-versions. </li><br><br>
										  <li> Log on to <a href="http://www.eqdkp-plus.com">www.eqdkp-plus.com</a> and donate any amount exceeding 100€. 
										  		You will earn gold status and get a livetime-premium-account, 
										  		making you eligible for free upgrades to new major-versions + free personal 
										  		support from the EQDKP-Plus developers.<br><br></li>										  
										  <li> All developers and translators ever contributed to EQDKP-Plus also get a free serial-key.<br><br></li>
										  <li> Deeply committed beta-testers also get a free serial-key. <br><br></li>
										  </ol>
										 All money generated with ad-banners and donations is solely spent to pay the costs coming up with the EQDKP-Plus project.
										 EQDKP-Plus is still a non-profit project! You dont have a Paypal or Amazon Account or have trouble with you key? Write me a <a href=mailto:corgan@eqdkp-plus.com>Email</a>.
										  ';


$lang['talents'] = array(
'Paladin'   	=> array('Holy','Protection','Retribution'),
'Rogue'     	=> array('Assassination','Combat','Subtlety'),
'Warrior'   	=> array('Arms','Fury','Protection'),
'Hunter'    	=> array('Beast Mastery','Marksmanship','Survival'),
'Priest'    	=> array('Discipline','Holy','Shadow'),
'Warlock'  		=> array('Affliction','Demonology','Destruction'),
'Druid'     	=> array('Balance','Feral Combat','Restoration'),
'Mage'      	=> array('Arcane','Fire','Frost'),
'Shaman'    	=> array('Elemental','Enhancement','Restoration'),
'Death Knight'   => array('Blood','Frost','Unholy')
);

$lang['portalmanager'] = 'Manage Portal Modules';

$lang['air_img_resize_warning'] = 'Click this bar to view the full image. The original is %1$sx%2$s.';

$lang['guild_shop'] = 'Shop';

// LibLoader Language String
$lang['libloader_notfound'] = 'The Library Loader Class is not available. Please check if the folder  "eqdkp/libraries/" is propperly uploaded!<br/> Download: <a href="https://sourceforge.net/project/showfiles.php?group_id=167016&package_id=301378">Libraries Download</a>';
$lang['libloader_tooold']   = "The Library '%1\$s' is outdated. You have to upload Version %2\$s or higher.<br/> Download: <a href='%3\$s' target='blank'>Libraries Download</a><br/>Please download, and overwrite the existing 'eqdkp/libraries/' folder with the one you downloaded!";

$lang['more_plugins']   = "For more Plugins visit <a href=http://www.eqdkp-plus.com/download.php>www.eqdkp-plus.com</a>.";
$lang['more_moduls']   = "For more Modules visit <a href=http://www.eqdkp-plus.com/download.php>www.eqdkp-plus.com</a>.";
$lang['more_template']   = "For more Style visit <a href=http://www.eqdkp-plus.com/download.php>www.eqdkp-plus.com</a>";

// jQuery
$lang['cl_bttn_ok']      = 'Ok';
$lang['cl_bttn_cancel']  = 'Cancel';

// Update Available
$lang['upd_available_head']    = 'System Updated available'; 
$lang['upd_available_txt']     = 'The System is not up to date. There are updates available.';
$lang['upd_available_link']    = 'Click to show updates.';

$lang['menu_roster'] = 'Roster';

$lang['lib_cache_notwriteable'] = 'The folder "eqdkp/data" is not writable. Please chmod 777!';

//Sticky news
$lang['sticky_news_prefix'] = 'Sticky:';
$lang['news_sticky'] = 'Make it sticky?';
#$lang['']    								  = '';
?>
