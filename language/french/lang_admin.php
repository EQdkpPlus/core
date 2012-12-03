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
$lang['backup_database'] = 'Sauvegarde de la base de données (ne pas combiner avec d\'autres changements pour cette session)';
$lang['buyer'] = 'Acheteur';
$lang['buyers'] = 'Acheteur';
$lang['class'] = 'Classe';
$lang['armor'] = 'Armure';
$lang['type'] = 'Armure';
$lang['class_distribution'] = 'Distribution par Classe';
$lang['class_summary'] = "Résumé des Classes : %1\$s to %2\$s";
$lang['configuration'] = 'Configuration';
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

$lang['plugin_inst_sql_note'] = 'An SQL error during install does not necessary implies a broken plugin installation. Try using the plugin, if errors occur please de- and reinstall the plugin.';
?>
