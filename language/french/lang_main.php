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

$lang['XML_LANG'] = 'fr';
$lang['ISO_LANG_SHORT'] = 'fr_FR';
$lang['ISO_LANG_NAME'] = 'Francais';

// Linknames
$lang['rp_link_name']   = "Planning";

// Titles
$lang['admin_title_prefix']   = "%1\$s %2\$s Admin";
$lang['listadj_title']        = 'Liste des ajustements de groupe';
$lang['listevents_title']     = 'Liste des événements';
$lang['listiadj_title']       = 'Liste des ajustements individuels';
$lang['listitems_title']      = 'Valeurs des objets';
$lang['listnews_title']       = 'Dernières nouvelles';
$lang['listmembers_title']    = 'Classement des membres';
$lang['listpurchased_title']  = 'Historique des objets';
$lang['listraids_title']      = 'Liste des raids';
$lang['login_title']          = 'Connexion';
$lang['message_title']        = 'EQdkp: Message';
$lang['register_title']       = 'S\'enregistrer';
$lang['settings_title']       = 'Options du compte';
$lang['stats_title']          = "%1\$s Statistiques";
$lang['summary_title']        = 'Résumé des nouvelles';
$lang['title_prefix']         = "%1\$s %2\$s";
$lang['viewevent_title']      = "Affichage de l\'historique des raids pour %1\$s";
$lang['viewitem_title']       = "Affichage de l\'historique des achats pour %1\$s";
$lang['viewmember_title']     = "Historique pour %1\$s";
$lang['viewraid_title']       = 'Résumé du raid';

// Main Menu
$lang['menu_admin_panel'] = 'Administration';
$lang['menu_events'] = 'Evénements';
$lang['menu_itemhist'] = 'Historique des objets';
$lang['menu_itemval'] = 'Valeur des objets';
$lang['menu_news'] = 'Nouvelles';
$lang['menu_raids'] = 'Raids';
$lang['menu_register'] = 'S\'enregistrer';
$lang['menu_settings'] = 'Options';
$lang['menu_members'] = 'Personnages';
$lang['menu_standings'] = 'Membres';
$lang['menu_stats'] = 'Statistiques';
$lang['menu_summary'] = 'Résumé';

// Column Headers
$lang['account'] = 'Compte';
$lang['action'] = 'Action';
$lang['active'] = 'Activé';
$lang['add'] = 'Ajouter';
$lang['added_by'] = 'Ajouté par';
$lang['adjustment'] = 'Ajustement';
$lang['administration'] = 'Administration';
$lang['administrative_options'] = 'Options d\'administration';
$lang['admin_index'] = 'Index de l\'administration';
$lang['attendance_by_event'] = 'Présence par événements';
$lang['attended'] = 'Accompagné';
$lang['attendees'] = 'Participants';
$lang['average'] = 'Moyenne';
$lang['buyer'] = 'Acheteur';
$lang['buyers'] = 'Acheteur';
$lang['class'] = 'Classe';
$lang['armor'] = 'Armure';
$lang['type'] = 'Armure';
$lang['class_distribution'] = 'Distribution par classe';
$lang['class_summary'] = "Résumé des classes : %1\$s to %2\$s";
$lang['configuration'] = 'Configuration';
$lang['config_plus']	= 'Options PLUS';
$lang['plus_vcheck']	= 'Mise à jour de la version';
$lang['current'] = 'Actuel';
$lang['date'] = 'Date';
$lang['delete'] = 'Supprimer';
$lang['delete_confirmation'] = 'Confirmation de suppression';
$lang['dkp_value'] = "%1\$s Valeur";
$lang['drops'] = 'Loots';
$lang['earned'] = 'Gagné';
$lang['enter_dates'] = 'Entrez les dates';
$lang['eqdkp_index'] = 'Index d\'EQdkp';
$lang['eqdkp_upgrade'] = 'Mise à jour d\'EQdkp';
$lang['event'] = 'Evénement';
$lang['events'] = 'Evénements';
$lang['filter'] = 'Filtre';
$lang['first'] = 'Premier';
$lang['rank'] = 'Rang';
$lang['general_admin'] = 'Administration générale';
$lang['get_new_password'] = 'Choisissez un nouveau mot de passe';
$lang['group_adj'] = 'Ajust. de groupe';
$lang['group_adjustments'] = 'Ajustements de groupe';
$lang['individual_adjustments'] = 'Ajustements individuels';
$lang['individual_adjustment_history'] = 'Historique de l\'ajustement individuel';
$lang['indiv_adj'] = 'Ajust. indiv.';
$lang['ip_address'] = 'Addresse IP';
$lang['item'] = 'Objet';
$lang['items'] = 'Objets';
$lang['item_purchase_history'] = 'Historique des objets achetés';
$lang['last'] = 'Dernier';
$lang['lastloot'] = 'Dernier loot';
$lang['lastraid'] = 'Dernier raid';
$lang['last_visit'] = 'Dernière visite';
$lang['level'] = 'Niveau';
$lang['log_date_time'] = 'Date/temps de cette session';
$lang['loot_factor'] = 'Facteur de loot';
$lang['loots'] = 'Loots';
$lang['manage'] = 'Gérer';
$lang['member'] = 'Membre';
$lang['members'] = 'Membres';
$lang['members_present_at'] = "Membres présents à %1\$s sur %2\$s";
$lang['miscellaneous'] = 'Divers';
$lang['name'] = 'Nom';
$lang['news'] = 'Nouvelles';
$lang['note'] = 'Note';
$lang['online'] = 'En ligne';
$lang['options'] = 'Options';
$lang['paste_log'] = 'Copiez un log ci-dessous';
$lang['percent'] = 'Pourcent';
$lang['permissions'] = 'Permissions';
$lang['per_day'] = 'Par jour';
$lang['per_raid'] = 'Par raid';
$lang['pct_earned_lost_to'] = 'Gains et Pertes en %';
$lang['preferences'] = 'Préférences';
$lang['purchase_history_for'] = "Historique d\'achat pour %1\$s";
$lang['quote'] = 'Citation';
$lang['race'] = 'Race';
$lang['raid'] = 'Raid';
$lang['raids'] = 'Raids';
$lang['raid_id'] = 'ID de raid';
$lang['raid_attendance_history'] = 'Historique de l\'attendance';
$lang['raids_lifetime'] = "Durée de vie (%1\$s - %2\$s)";
$lang['raids_x_days'] = "Les derniers %1\$d jours";
$lang['rank_distribution'] = 'Distribution par rang';
$lang['recorded_raid_history'] = "Historique des raids enregistré pour %1\$s";
$lang['reason'] = 'Raison';
$lang['registration_information'] = 'Informations d\'inscription';
$lang['result'] = 'Résultat';
$lang['session_id'] = 'ID de session';
$lang['settings'] = 'Options';
$lang['spent'] = 'Dépensé';
$lang['summary_dates'] = "Résume du raid : %1\$s à %2\$s";
$lang['themes'] = 'Thèmes';
$lang['time'] = 'Temps';
$lang['total'] = 'Total';
$lang['total_earned'] = 'Total gagné';
$lang['total_items'] = 'Total d\'objets';
$lang['total_raids'] = 'Total raids';
$lang['total_spent'] = 'Total dépensé';
$lang['transfer_member_history'] = 'Transférer l\'historique d\'un membre';
$lang['turn_ins'] = 'Restitutions';
$lang['type'] = 'Type';
$lang['update'] = 'Mise à jour';
$lang['updated_by'] = 'Mis à jour par';
$lang['user'] = 'Utilisateur';
$lang['username'] = 'Nom d\'utilisateur';
$lang['value'] = 'Valeur';
$lang['view'] = 'Afficher';
$lang['view_action'] = 'Afficher l\'action';
$lang['view_logs'] = 'Afficher les logs';

// Page Foot Counts
$lang['listadj_footcount']               = "...  %1\$d ajustements trouvés / %2\$d par page";
$lang['listevents_footcount']            = "...  %1\$d événements trouvés / %2\$d par page";
$lang['listiadj_footcount']              = "...  %1\$d ajustements individuels trouvés / %2\$d par page";
$lang['listitems_footcount']             = "...  %1\$d objets uniques trouvés / %2\$d par page";
$lang['listmembers_active_footcount']    = "...  %1\$d membres actifs trouvés / %2\$stout afficher</a>";
$lang['listmembers_compare_footcount']   = "... compare %1\$d members";
$lang['listmembers_footcount']           = "...  %1\$d membres";
$lang['listnews_footcount']              = "...  %1\$d nouvelles trouvées / %2\$d par page";
$lang['listpurchased_footcount']         = "...  %1\$d objets trouvés / %2\$d par page";
$lang['listraids_footcount']             = "...  %1\$d raids trouvés / %2\$d par page";
$lang['stats_active_footcount']          = "...  %1\$d membres actifs trouvés / %2\$stout afficher</a>";
$lang['stats_footcount']                 = "...  %1\$d membres trouvés ";
$lang['viewevent_footcount']             = "...  %1\$d raids trouvés ";
$lang['viewitem_footcount']              = "...  %1\$d objets trouvés ";
$lang['viewmember_adjustment_footcount'] = "...  %1\$d ajustements individuelstrouvés ";
$lang['viewmember_item_footcount']       = "...  %1\$d objets achetés trouvés / %2\$d par page";
$lang['viewmember_raid_footcount']       = "...  %1\$d participants de raid trouvés / %2\$d par page";
$lang['viewraid_attendees_footcount']    = "...  %1\$d participants trouvés ";
$lang['viewraid_drops_footcount']        = "...  %1\$d pertes trouvés ";

// Submit Buttons
$lang['close_window'] = 'Fermer la fenêtre';
$lang['compare_members'] = 'Comparer les membres';
$lang['create_news_summary'] = 'Créer le résumé des nouvelles';
$lang['login'] = 'Connexion';
$lang['logout'] = 'Déconnexion';
$lang['log_add_data'] = 'Ajouter les informations au formulaire';
$lang['lost_password'] = 'Mot de passe perdu';
$lang['no'] = 'Non';
$lang['proceed'] = 'Ajouter';
$lang['reset'] = 'Remise à zéro';
$lang['set_admin_perms'] = 'Fixer les permissions d\'administration';
$lang['submit'] = 'Envoyer';
$lang['upgrade'] = 'Mettre à jour';
$lang['yes'] = 'Oui';

// Form Element Descriptions
$lang['admin_login'] = 'Login Administrateur';
$lang['confirm_password'] = 'Confirmation du mot de passe';
$lang['confirm_password_note'] = 'Vous devez seulement confirmer votre nouveau mot de passe si vous l\'avez changé plus haut';
$lang['current_password'] = 'Mot de passe actuel';
$lang['current_password_note'] = 'Vous devez confirmer votre mot de passe si vous changer votre nom d\'utilisateur ou mot de passe';
$lang['email'] = 'Email';
$lang['email_address'] = 'Addresse email';
$lang['ending_date'] = 'Date de fin';
$lang['from'] = 'De';
$lang['guild_tag'] = 'Tag de guilde';
$lang['language'] = 'Langue';
$lang['new_password'] = 'Nouveau mot de passe';
$lang['new_password_note'] = 'Vous avez seulement besoin de fournir un nouveau mot de passe si vous voulez le changer';
$lang['password'] = 'Mot de passe';
$lang['remember_password'] = 'Se souvenir de moi (cookie)';
$lang['starting_date'] = 'Date de début';
$lang['style'] = 'Style';
$lang['to'] = 'A';
$lang['username'] = 'Nom d\'utilisateur';
$lang['users'] = 'Utilisateurs';

// Pagination
$lang['next_page'] = 'Page Suivante';
$lang['page'] = 'Page';
$lang['previous_page'] = 'Page Précédente';

// Permission Messages
$lang['noauth_default_title'] = 'Permission refusée';
$lang['noauth_u_event_list'] = 'Vous n\'avez pas la permission de lister les événements.';
$lang['noauth_u_event_view'] = 'Vous n\'avez pas la permission d\'afficher les événements.';
$lang['noauth_u_item_list'] = 'Vous n\'avez pas la permission de lister les objets.';
$lang['noauth_u_item_view'] = 'Vous n\'avez pas la permission d\'afficher les items.';
$lang['noauth_u_member_list'] = 'Vous n\'avez pas la permission d\'afficher le classement.';
$lang['noauth_u_member_view'] = 'Vous n\'avez pas la permission d\'afficher l\'historique des membres.';
$lang['noauth_u_raid_list'] = 'Vous n\'avez pas la permission de lister les raids.';
$lang['noauth_u_raid_view'] = 'Vous n\'avez pas la permission d\'afficher les raids.';

// Submission Success Messages
$lang['add_itemvote_success'] = 'Votre vote sur l\'objet a été enregistré.';
$lang['update_itemvote_success'] = 'Votre vote sur l\'objet a été mis à jour.';
$lang['update_settings_success'] = 'Les options d\'utilisateur ont été mises à jour.';

// Form Validation Errors
$lang['fv_alpha_attendees'] = 'Les noms des personnages contiennent uniquement des caractères alphabétiques.';
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
$lang['fv_required_inactivepd'] = 'Si le champ "cacher les membres inactifs" est sur "Oui", la période d\'inactivité doit être définie.';
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
$lang['additem_raidid_note'] = "Les raids au delà de 2 semaines sont cachés / %1\$safficher tout</a>";
$lang['additem_raidid_showall_note'] = 'Affiche tous les raids';
$lang['addraid_datetime_note'] = 'Si vous consultez un log, la date et le temps seront trouvés automatiquement.';
$lang['addraid_value_note'] = 'Pour un bonus unique. Si vous laissez vide, les valeurs seront celles des pré-réglages.';
$lang['add_items_from_raid'] = 'Ajouter des items de ce raid';
$lang['deleted'] = 'Supprimé';
$lang['done'] = 'Prêt';
$lang['enter_new'] = 'Entrez un nouveau';
$lang['error'] = 'Erreur';
$lang['head_admin'] = 'Admin pricipal';
$lang['hold_ctrl_note'] = 'Maintenir CTRL pour des sélections multiples.';
$lang['list'] = 'Lister';
$lang['list_groupadj'] = 'Lister les ajustements de groupe';
$lang['list_events'] = 'Lister les événements';
$lang['list_indivadj'] = 'Lister les ajustements individuels';
$lang['list_items'] = 'Lister les objets';
$lang['list_members'] = 'Lister les membres';
$lang['list_news'] = 'Lister les nouvelles';
$lang['list_raids'] = 'Lister les raids';
$lang['may_be_negative_note'] = 'peut être négative';
$lang['not_available'] = 'Pas disponible';
$lang['no_news'] = 'Pas de nouvelles trouvées.';
$lang['of_raids'] = "%1\$d%% des raids";
$lang['or'] = 'OU';
$lang['powered_by'] = 'Powered by';
$lang['preview'] = 'Visionner';
$lang['required_field_note'] = 'Les champs marqués par * sont requis.';
$lang['select_1ofx_members'] = "Sélection parmi %1\$d membres";
$lang['select_existing'] = 'Sélection existante';
$lang['select_version'] = 'Sélectionnez la version d\'EQdkp que vous allez mettre à jour';
$lang['success'] = 'Réussite';
$lang['s_admin_note'] = 'Ces champs ne sont pas modifiables par les utilisateurs.';
$lang['transfer_member_history_description'] = 'Cette action va transférer tout l\'historique d\'un membre (raids, objets, ajustements) à un autre.';
$lang['updated'] = 'Mis à jour';
$lang['upgrade_complete'] = 'Votre installation d\'EQDKP à été mis à jour avec succès.<br /><br /><b class="negative">Pour plus de sécurité, supprimez ce fichier !</b>';

// Settings
$lang['account_settings'] = 'Options du compte';
$lang['adjustments_per_page'] = 'Ajustements par page';
$lang['basic'] = 'Configuration';
$lang['events_per_page'] = 'Evénements par page';
$lang['items_per_page'] = 'Objets par page';
$lang['news_per_page'] = 'News par page';
$lang['raids_per_page'] = 'Raids par page';
$lang['associated_members'] = 'Membres associés';
$lang['guild_members'] = 'Membres de guilde';
$lang['default_locale'] = 'Local défaut';


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
$lang['news_submitter'] = 'soumise par';
$lang['news_submitat'] = 'à';
$lang['droprate_loottable'] = "Table de loot pour";
$lang['droprate_name'] = "Nom d\'objet";
$lang['droprate_count'] = "Nombre";
$lang['droprate_drop'] = "Loot %";

$lang['Itemsearch_link'] = "Rechercher un objet";
$lang['Itemsearch_search'] = "Rechercher un objet :";
$lang['Itemsearch_searchby'] = "Rechercher par :";
$lang['Itemsearch_item'] = "Objet ";
$lang['Itemsearch_buyer'] = "Acheteur ";
$lang['Itemsearch_raid'] = "Raid ";
$lang['Itemsearch_unique'] = "Résultats objets uniques :";
$lang['Itemsearch_no'] = "Non";
$lang['Itemsearch_yes'] = "Oui";

$lang['bosscount_player'] = "Membre : ";
$lang['bosscount_raids'] = "Raids : ";
$lang['bosscount_items'] = "Objets : ";
$lang['bosscount_dkptotal'] = "total DKP: ";

//MultiDKP
$lang['Plus_menuentry'] 			= "EQDKP Plus";
$lang['Multi_entryheader'] 		= "MultiDKP - Ajouter un groupe";
$lang['Multi_pageheader'] 		= "MultiDKP - Lister les groupes";
$lang['Multi_events'] 				= "Evénements:";
$lang['Multi_eventname'] 				= "Nom de l\'événement";
$lang['Multi_discnottolong'] 	= "(Nom Colonne) - Celui ci ne doit pas être trop long. Choisissez p.e MC, BWL, AQ etc. !";
$lang['Multi_kontoname_short']= "Nom du compte:";
$lang['Multi_discr'] 					= "Description:";
$lang['Multi_events'] 				= "Evénements de ce groupe";

$lang['Multi_addkonto'] 			  = "Ajouter un groupe MultiDKP";
$lang['Multi_updatekonto'] 			= "Changer de groupe";
$lang['Multi_deletekonto'] 			= "Effacer le groupe";
$lang['Multi_viewkonten']			  = "Lister les groupes MultiDKP";
$lang['Multi_chooseevents']			= "Choisissez un événement";
$lang['multi_footcount'] 				= "... %1\$d Groupes DKP / %2\$d par page";
$lang['multi_error_invalid']    = "Pas de groupe affecté.";
$lang['Multi_required_event']   = "Vous devez choisir un événement.";
$lang['Multi_required_name']    = "Vous devez saisir un nom.";
$lang['Multi_required_disc']    = "Vous devez saisir une description.";
$lang['Multi_admin_add_multi_success'] = "Le groupe %1\$s ( %2\$s ) avec les événements %3\$s a été ajouté.";
$lang['Multi_admin_update_multi_success'] = "Le groupe %1\$s ( %2\$s ) avec les événements %3\$s a été modifié.";
$lang['Multi_admin_delete_success']           = "Le groupe %1\$s a été supprimé.";
$lang['Multi_confirm_delete']    = 'Voulez-vous supprimer ce groupe ?';

$lang['Multi_total_cost']   										= 'Total des points de ce groupe';
$lang['Multi_Accs']    													= 'Groupe MultiDKP';

//update
$lang['upd_eqdkp_status']    										= 'Statut mise à jour EQDKP';
$lang['upd_system_status']    									= 'Statut du système';
$lang['upd_template_status']    								= 'Statut des modèles';
$lang['upd_gamefile_status']                    = 'Statut du jeu';
$lang['upd_update_need']    										= 'Mise à jour nécessaire !';
$lang['upd_update_need_link']    								= 'Installez les composants requis.';
$lang['upd_no_update']    											= 'Le système est à jour.';
$lang['upd_status']    													= 'Statut';
$lang['upd_state_error']    										= 'Erreur';
$lang['upd_sql_string']    											= 'Commande SQL';
$lang['upd_sql_status_done']    								= 'effectué';
$lang['upd_sql_error']    											= 'Erreur';
$lang['upd_sql_footer']    											= 'Commande SQL exécutée';
$lang['upd_sql_file_error']    									= 'Erreur: le fichier SQL %1\$s n\'a pas été trouvé !';
$lang['upd_eqdkp_system_title']    							= 'Mise à jour des composants EQDKP';
$lang['upd_plus_version']    										= 'Version EQDKP Plus';
$lang['upd_plus_feature']    										= 'Fonctions';
$lang['upd_plus_detail']    										= 'Details';
$lang['upd_update']    													= 'Mise à jour';
$lang['upd_eqdkp_template_title']    						= 'Mise à jour des modèles EQDKP';
$lang['upd_eqdkp_gamefile_title']               = 'Mise à jour du jeu EQDKP';
$lang['upd_gamefile_availversion']              = 'Version disponible';
$lang['upd_gamefile_instversion']               = 'Version installée';
$lang['upd_template_name']    									= 'Nom du modèle';
$lang['upd_template_state']    									= 'Statut du modèle';
$lang['upd_template_filestate']    							= 'Dossier des modèles disponibles';
$lang['upd_link_install']    										= 'Mise à jour';
$lang['upd_link_reinstall']    									= 'réinstallation';
$lang['upd_admin_need_update']    							= 'Une erreur de la base de données à été détectée. Veuillez mettre à jour le système.';
$lang['upd_admin_link_update']									= 'cliquez ici pour résoudre le problème';
$lang['upd_backto']    													= 'Retour à la vue générale';

// Event Icon
$lang['event_icon_header']    								  = 'Choisissez un icône d\'événement';

//update Itemstats
$lang['updi_header']    								    	= 'Rafraichir les données Itemstats';
$lang['updi_header2']    								    	= 'informations Itemstats';
$lang['updi_action']    								    	= 'action';
$lang['updi_notfound']    								    = 'Non trouvé';
$lang['updi_writeable_ok']    							  = 'Fichier modifiable';
$lang['updi_writeable_no']    								= 'fichier NON modifiable';
$lang['updi_help']    								    		= 'Description';
$lang['updi_footcount']    								    = 'Objet rafraichi';
$lang['updi_curl_bad']    								    = 'La fonction PHP cURL n\'a pas été trouvée. Itemstats ne fonctionnera pas correctement. Contactez l\'Admin.';
$lang['updi_curl_ok']    								    	= 'cURL trouvée.';
$lang['updi_fopen_bad']    								    = 'La fonction PHP fopen n\'a pas été trouvée. Itemstats ne fonctionnera pas correctement. Contactez l\'Admin.';
$lang['updi_fopen_ok']    								    = 'fopen trouvée.';
$lang['updi_nothing_found']						    		= 'Pas d\'objets trouvés.';
$lang['updi_itemscount']  						    		= 'Entrées Itemcache :';
$lang['updi_baditemscount']						    		= 'Entrées invalides :';
$lang['updi_items']										    		= 'Objets dans la table:';
$lang['updi_items_duplicate']					    		= '{avec doublons}';
$lang['updi_show_all']    								    = 'Lister tous les objets avec Itemstats.';
$lang['updi_refresh_all']    								  = 'Effacer tous les objets et les rafraichir.';
$lang['updi_refresh_bad']    								  = 'Rafraichier les objets invalides.';
$lang['updi_refresh_raidbank']    						= 'Rafraichir les objets Raidbanker';
$lang['updi_refresh_tradeskill']   						= 'Rafraichier les objets Tradeskill';
$lang['updi_help_show_all']    								= 'Tous les objets seront affichés avec leurs stats. Les stats invalides seront rafraichies. (recommandé)';
$lang['updi_help_refresh_all']  							= 'Efface l\'Itemcache courant et tente de rafraichir tous les objets listés dans EQDKP. ATTENTION : si vous partager Itemcache avec un forum, les objets du forum ne seront pas rafraichis. Suivant votre serveur et la disponibilité d\'Allakhazam.com cette action peut prendre du temps. En cas d\'exécution interdite par votre hébergeur, veuillez contacter votre administrateur.';
$lang['updi_help_refresh_bad']    						= 'Efface tous les objets invalides et les rafraichi.';
$lang['updi_help_refresh_raidbank']    				= 'Si Raidbanker est installé, Itemstats utilisera les objets de Raidbanker.';
$lang['updi_help_refresh_Tradeskill']    			= 'Si Tradeskill est installé, Itemstats utilisera les objets de Tradeskill.';

$lang['updi_active'] 					   							= 'activé';
$lang['updi_inactive']    										= 'desactivé';

$lang['fontcolor']    			  = 'Couleur de police';
$lang['Warrior']    					= 'Guerrier';
$lang['Rogue']    						= 'Voleur';
$lang['Hunter']    						= 'Chasseur';
$lang['Paladin']    					= 'Paladin';
$lang['Priest']    						= 'Prêtre';
$lang['Druid']    						= 'Druide';
$lang['Shaman']    						= 'Chaman';
$lang['Warlock']    					= 'Démoniste';
$lang['Mage']    							= 'Mage';

# Reset DB Feature
$lang['reset_header']    			= 'Reset les données EQDKP';
$lang['reset_infotext']  			= 'DANGER!!! L\'effacement des données est définitif !!! Faites une sauvegarde. Pour confirmer, tapez SUPPRIMER dans la boite d\édition.';
$lang['reset_type']    				= 'Type de données';
$lang['reset_disc']    				= 'Description';
$lang['reset_sec']    				= 'Certificat';
$lang['reset_action']    			= 'Action';

$lang['reset_news']					  = 'Nouvelles';
$lang['reset_news_disc']		  = 'Effacer touts les nouvelles de la base.';
$lang['reset_dkp'] 					  = 'DKP';
$lang['reset_dkp_disc']			  = 'Effacer tous les raids et objets de la base. Remise à zéro de tous les points DKP.';
$lang['reset_ALL']   					= 'TOUT';
$lang['reset_ALL_DISC']				= 'Effacer tous les raids, les objets et les membre. Remise à zéro complète. (Ne supprime pas les utilisateurs).';

$lang['reset_confirm_text']	  = ' insérer ici =>';
$lang['reset_confirm']			  = 'SUPPRIMER';

// Armory Menu
$lang['lm_armorylink1']				= 'Armurerie';
$lang['lm_armorylink2']				= 'Talents';
$lang['lm_armorylink3']				= 'Guilde';

$lang['updi_update_ready']			= 'Les objets on été mis à jour correctement. Vous pouvez <a href="#" onclick="javascript:parent.closeWindow()" >fermer</a> cette fenêtre.';
$lang['updi_update_alternative']= 'Méthode secondaire de mise à jour pour éviter les délais dépassés.';
$lang['zero_sum']				= ' Zéro Somme DKP';

//Hybrid
$lang['Hybrid']				= 'Hybride';

$lang['Jump_to'] 				= 'regarder la vidéo ';
$lang['News_vid_help'] 			= 'Pour intégrer des vidéos, poster le lien sans les [tags]. Sites de vidéos supportés : google, youtube, myvideo, clipfish, sevenload, metacafe et streetfire. ';

$lang['SubmitNews'] 		   = 'Envoyer la nouvelle';
$lang['SubmitNews_help'] 	   = 'Vous avez des bonnes nouvelles ? Soumettez la nouvelle et partagez avec tous les membres Eqdkp.';

$lang['MM_User_Confirm']	   = 'Sélectionnez votre compte Admin ? Si vous enlevez vos droits, ceux-ci ne pourront être rétabli que via la base.';

$lang['beta_warning']	   	   = 'Attention ! cette version Beta d\'EQDKP-Plus Beta ne doit pas être utilisée sur un serveur réel. Cette version cesse de fonctionner quand une nouvelle version stable est disponible. Vérifiez <a href="http://www.eqdkp-plus.com" >www.eqdkp-plus.com</a> pour les mises à jour !';

$lang['news_comment']        = 'Commentaire';
$lang['news_comments']       = 'Commentaires';
$lang['comments_no_comments']	   = 'Pas d\'entrées';
$lang['comments_comments_raid']	   = 'Commentaires';
$lang['comments_write_comment']	   = 'Ecrire un commentaire';
$lang['comments_send_comment']	   = 'Envoyer un commentaire';
$lang['comments_save_wait']	   	   = 'Patientez, commentaire en cours d\'enregistrement ...';

$lang['news_nocomments'] 	 		    = 'Interdire les commentaires';
$lang['news_readmore_button']  			  	= 'Fonctions étendues';
$lang['news_readmore_button_help']  			  	= 'Pour utiliser les fonction étendues, cliquez ici :';
$lang['news_message'] 				  	= 'Texte';
$lang['news_permissions']			  	= 'Permissions';

$lang['news_permissions_text']			= 'Ne pas afficher les nouvelles pour';
$lang['news_permissions_guest']			= 'Invités';
$lang['news_permissions_member']		= 'Invités et Membres';
$lang['news_permissions_all']			= 'Tout le monde';
$lang['news_readmore'] 				  	= 'Lire la suite...';

$lang['recruitment_open']				= 'Recrutement ouvert';
$lang['recruitment_contact']			= 'contact';

$lang['sig_conf']						= 'cliquez l\'image pour obtenir le BB Code';
$lang['sig_show']						= 'montrer la signature WoW de votre forum';

//Shirtshop
$lang['service']					    = 'service';
$lang['shirt_ad1']					    = 'Allez sur la boutique T-Shirt. <br> obtenez votre propre T-Shirt maintenant !';
$lang['shirt_ad2']					    = 'Choisissez votre personnage';
$lang['shirt_ad3']					    = 'bienvenue dans la boutique de guilde ';
$lang['shirt_ad4']					    = 'Wähle eines der vorgefertigten Produkte aus, oder erstell Dir mit dem Creator ein komplett eigenes Shirt.<br>
										   Du kannst jedes Shirt nach Deinen Bedürfnissen anpassen und jeden Schriftzug verändern.<br>
										   Unter Motive findest alle zur Verfügung stehenden Motive!';
$lang['error_iframe']					= "Votre navigateur n\'est pas compatible !";
$lang['new_window']						= 'Ouvrir la boutique dans une nouvelle fenêtre';
$lang['your_name']						= 'VOTRE NOM';
$lang['your_guild']						= 'VOTRE GUILDE';
$lang['your_server']					= 'VOTRE SERVEUR';

//Last Raids
$lang['last_raids']					    = 'Derniers raids';

$lang['voice_error']				    = 'Pas de connexion.';

$lang['login_bridge_notice']		    = 'Connexion passerelle.';

$lang['ads_remove']		    			= 'support EQdkp-Plus';
$lang['ads_header']	    				= 'Support EQDKP-Plus';
$lang['ads_text']		    			= 'EQDKP-Plus est un projet loisir qui est principalement développé et mis à jour par deux personnes.
											Au début ceci n\était pas un problème mais après trois années de programmation constante et de modifications,
											le coût a dépassé nos budgets. Pour le développeur et le serveur de mise à jour, nous devons dépenser
											600€ par an et maintenant nous avons aussi 1000€ de dépenses en avocat, depuis qu\'il y a quelques
											problèmes légaux. A l\'avenir nous avons prévu beaucoup plus de fonctionnalités serveur, ce qui
											nécessitera un nouveau serveur. Les coûts pour notre nouveau forum ainsi que ceux du designer s\'ajoutent également.
											Toutes ces dépenses, ainsi que notre temps de travail, de plus en plus important, ne peuvent plus être payés
											par nous-mêmes. Pour cette raison, et afin de garantir la poursuite du projet, vous verrez désormais des
											publicités dans EQDKP-Plus. Ces bannières seront très limités en contenu, vous ne verrez pas de liens
											pornographiques ou de vendeur de pièces d\'or.

											Vous avez l\'option de désactiver ces publicités :

										  <ol>
										  <li> Enregistrez vous sur <a href="http://www.eqdkp-plus.com">www.eqdkp-plus.com</a> et faites un don.
										  		Réfléchissez à combien EQDKP-Plus est important pour vous.
										  		Après le don (Amazon ou Paypal) vous recevrez un email avec un numéro de série pour
										  		les différentes versions majeures...<br><br></li>
										  <li> Enregistrez vous sur <a href="http://www.eqdkp-plus.com">www.eqdkp-plus.com</a> et faites un don de plus de 50€.
										  		Vous aurez un accès premium et un compte premium à vie, et aurez ainsi droit aux
										  		mises à jour gratuites des nouvelles versions majeures. </li><br><br>
										  <li> Enregistrez vous sur <a href="http://www.eqdkp-plus.com">www.eqdkp-plus.com</a> et faites un don de plus de 100€.
										  		Vous aurez un accès gold et un compte premium à vie, et aurez ainsi droit aux
										  		mises à jour gratuites des nouvelles version plus un support personnel
										  		gratuit de la part des développeurs EQDKP-Plus.<br><br></li>
										  <li> Tous les développeurs et les traducteurs qui ont contribués à EQDKP-Plus obtiennent gratuitement un numéro de série.<br><br></li>
										  <li> Les beta testeurs fortements impliqués recoivent aussi gratuitement un numéro de série. <br><br></li>
										  </ol>
										 Tout l\'argent récolté avec les publicités et les donations est uniquement destiné aux dépenses relatives au projet EQDKP-Plus.
										 EQDKP-Plus est toujours un projet à but non lucratif ! Vous n\'avez pas de compte Paypal ou Amazon, ou avez un problème avec la clé ?
										 Ecrivez-moi à <a href=mailto:corgan@eqdkp-plus.com>Email</a>.
										  ';


$lang['portalmanager'] = 'Gérer les modules du portail';

$lang['air_img_resize_warning'] = 'Cliquez cette barre pour afficher l\'image entière. L\'original est %1$sx%2$s.';

$lang['guild_shop'] = 'Boutique';

// LibLoader Language String
$lang['libloader_notfound']     = 'The Library Loader Class is not available.
                                  Please check if the folder  "eqdkp/libraries/" is propperly uploaded!<br/>
                                  Download: <a href="https://sourceforge.net/project/showfiles.php?group_id=167016&package_id=301378">Libraries Download</a>';
$lang['libloader_tooold']       = "The Library '%1\$s' is outdated. You have to upload Version %2\$s or higher.<br/>
                                  Download: <a href='%3\$s' target='blank'>Libraries Download</a><br/>
                                  Please download, and overwrite the existing 'eqdkp/libraries/' folder with the one you downloaded!";
$lang['libloader_tooold_plug']  = "The Library Module '%1\$s' is outdated. Version %2\$s or higher is required.
                                  This is included in the Libraries %4\$s or higher. Your current Libraries Version is %5\$s<br/>
                                  Download: <a href='%3\$s' target='blank'>Libraries Download</a><br/>
                                  Please download, and overwrite the existing 'eqdkp/libraries/' folder with the one you downloaded!";

$lang['more_plugins']   = "Pour plus de plugins visitez <a href=http://www.eqdkp-plus.com/download.php>www.eqdkp-plus.com</a>.";
$lang['more_moduls']   = "Pour plus de modules visitez <a href=http://www.eqdkp-plus.com/download.php>www.eqdkp-plus.com</a>.";
$lang['more_template']   = "Pour plus de modèles visitez <a href=http://www.eqdkp-plus.com/download.php>www.eqdkp-plus.com</a>";

// jQuery
$lang['cl_bttn_ok']      = 'Ok';
$lang['cl_bttn_cancel']  = 'Annuler';

// Update Available
$lang['upd_available_head']    = 'Système mis à jour disponible.';
$lang['upd_available_txt']     = 'Le système n\'est pas à jour. Il y a de nouvelles versions disponibles.';
$lang['upd_available_link']    = 'Cliquez pour afficher les mises à jour.';

$lang['menu_roster'] = 'Roster';


$lang['adduser_first_name'] = 'Prénom';
$lang['adduser_last_name'] = 'Nom';
$lang['adduser_addinfos'] = 'Informations sur le profil';
$lang['adduser_country'] = 'Pays';
$lang['adduser_town'] = 'Ville';
$lang['adduser_state'] = 'Etat';
$lang['adduser_ZIP_code'] = 'Code postal';
$lang['adduser_phone'] = 'Numéro de téléphone';
$lang['adduser_cellphone'] = 'Numéro de portable';
$lang['adduser_foneinfo'] = 'Les numéros de téléphone sont sauvegardés anonynement. Uniquement les administrateurs peuvent les consulter. Avec le numéro de portable vous pouvez vous échanger des messages, en cas de nouveaux raids ou d\'annulations par exemple.';
$lang['adduser_address'] = 'Rue';
$lang['adduser_allvatar_nick'] = 'Surnom';
$lang['adduser_icq'] = 'Numéro ICQ';
$lang['adduser_skype'] = 'Skype';
$lang['adduser_msn'] = 'MSN';
$lang['adduser_irq'] = 'Serveur et canaux IRC';
$lang['adduser_gender'] = 'Genre';
$lang['adduser_birthday'] = 'Anniversaire';
$lang['adduser_gender_m'] = 'Mâle';
$lang['adduser_gender_f'] = 'Femelle';
$lang['fv_required'] = 'Champs obligatoires !';
$lang['lib_cache_notwriteable'] = 'Le dossier "eqdkp/data" n\'est pas modifiable. Veuillez le chmod 777 !';
$lang['pcache_safemode_error']  = 'Safe Mode active. EQDKP-PLUS will not work wile it xould not write data to the cache folders in safe mode.';

// Ajax Image Uploader
$lang['aiupload_wrong_format']  = "Les dimensions de l\'image sont en dehors des limites (max : %1\$spx x %2\$spx).<br/>Veuillez réduire l\'image.";
$lang['aiupload_wrong_type']    = 'Type de fichier invalide ! Seules les fichiers (*.jpg, *.gif, *.png) sont autorisés.';
$lang['aiupload_upload_again']  = 'Télécharger de nouveau';

//Sticky news
$lang['sticky_news_prefix'] = 'Post-it :';
$lang['news_sticky'] = 'Créer un post-it ?';

$lang['menu_eqdkp'] = 'Menu';
$lang['menu_user'] = 'User-menu';

$lang['menu_eqdkp'] = 'Menu principal';
$lang['menu_user'] = 'Utilisateur';

//---- About ----
$lang['pk_version']					= 'Version';
$lang['pk_prodcutname']				= 'Produit';
$lang['pk_modification']				= 'Mod';
$lang['pk_tname']						= 'Template';
$lang['pk_developer']					= 'Développeurs';
$lang['pk_plugin']						= 'Plugin';
$lang['pk_weblink']					= 'Liens Internet';
$lang['pk_phpstring']					= 'PHP Raccourcis';
$lang['pk_phpvalue']					= 'Valeur';
$lang['pk_donation']					= 'Donation';
$lang['pk_job']						= 'Métier';
$lang['pk_sitename']					= 'Site';
$lang['pk_dona_name']					= 'Nom';
$lang['pk_betateam1']					= 'Beta de test de l équipe (Germany)';
$lang['pk_betateam2']					= 'Ordre chronologique';
$lang['pk_created by']					= 'Créer par';
$lang['web_url']						= 'internet';
$lang['personal_url']					= 'Privée';
$lang['pk_credits']					= 'Credits';
$lang['pk_sponsors']					= 'Donators';
$lang['pk_plugins']					= 'PlugIns';
$lang['pk_modifications']				= 'Mods';
$lang['pk_themes']						= 'Styles';
$lang['pk_additions']					= 'Code Additions';
$lang['pk_tab_stuff']					= 'L equipe EQDKP';
$lang['pk_tab_help']					= 'Aide';
$lang['pk_tab_tech']					= 'Technique';
$lang['pk_disclaimer'] = 'Disclaimer';

//pdh listmember
$lang['manage_members'] = "Manage members";
$lang['show_hidden_ranks'] = "Show hidden ranks";
$lang['show_inactive'] = "Show inactive";

// Libraries
$lang = array_merge($lang, array(
  'cl_shortlangtag'           => 'en',
    
  // Update Check
  'cl_update_box'             => 'New Version available',
  'cl_changelog_url'          => 'Changelog',
  'cl_timeformat'             => 'm/d/Y',
  'cl_noserver'               => 'An error occurred while trying to contact the update server, either your host does not allow outbound connections
                                  or the error was caused by a network problem.
                                  Please visit the eqdkp-plugin-forum to make sure you are running the latest plugin version.',
  'cl_update_available'       => "Please update the installed <i>%1\$s</i> Plugin.
                                  Your current version is <b>%2\$s</b> and the latest version is <b>%3\$s (Released at: %4\$s)</b>.<br/><br/>
                                  [release: %5\$s]%6\$s%7\$s",
  'cl_update_url'             => 'To the Download Page',

  // Plugin Updater
  'cl_update_box'             => 'Database update required',
  'cl_upd_wversion'           => "The actual Database ( Version %1\$s ) does not fit to the installed Plugin Version %2\$s.
                                  Please use the update button to perform the required updates automatically.",
  'cl_upd_woversion'          => 'A previous installation was found. The version Data is missing. 
                                  Please choose the previous installed version in the drop Down list, to perform all Database changes.',
  'cl_upd_bttn'               => 'Update Database',
  'cl_upd_no_file'            => 'Update file is missing',
  'cl_upd_glob_error'         => 'An error occured during the update process.',
  'cl_upd_ok'                 => 'The update of the Database was successful',
  'cl_upd_step'               => 'Step',
  'cl_upd_step_ok'            => 'Successfull',
  'cl_upd_step_false'         => 'Failed',
  'cl_upd_reload_txt'         => 'Settings are reloading, please wait...',
  'cl_upd_pls_choose'         => 'Please choose...',
  'cl_upd_prev_version'       => 'Previous Version',

  // HTML Class
  'cl_on'                     => 'On',
  'cl_off'                    => 'Off',
  
    // ReCaptcha Library
	'lib_captcha_head'					=> 'confirmation Code',
	'lib_captcha_insertword'		=> 'Enter the words written below',
	'lib_captcha_insertnumbers' => 'Enter the spoken Numbers',
	'lib_captcha_send'					=> 'Send confirmation Code',
));

#$lang['']    								  = '';
?>