<?php
/******************************
 * EQdkp
 * Copyright 2002-2003
 * Licensed under the GNU GPL.  See COPYING for full terms.
 * ------------------
 * lang_admin.php
 * Began: Fri January 3 2003
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
$lang['addadj_title']         = 'Ajouter un ajustement de groupe';
$lang['addevent_title']       = 'Ajouter un événement';
$lang['addiadj_title']        = 'Ajouter un ajustement individuel';
$lang['additem_title']        = 'Ajouter l\'achat d\'un objet';
$lang['addmember_title']      = 'Ajouter un membre';
$lang['addnews_title']        = 'Ajouter une nouvelle';
$lang['addraid_title']        = 'Ajouter un raid';
$lang['addturnin_title']      = "Ajouter une restitution - Etape %1\$d";
$lang['admin_index_title']    = 'Administration EQdkp';
$lang['config_title']         = 'Scipt de configuration';
$lang['manage_members_title'] = 'Gérer les membres de guilde';
$lang['manage_users_title']   = 'Comptes utilisateurs et permissions';
$lang['parselog_title']       = 'Parcourir un fichier log';
$lang['plugins_title']        = 'Gérer les plugins';
$lang['styles_title']         = 'Gérer les modèles';
$lang['viewlogs_title']       = 'Affichage des logs';

// Page Foot Counts
$lang['listusers_footcount']             = "... %1\$d utilisateurs trouvés / %2\$d par page";
$lang['manage_members_footcount']        = "... %1\$d membres trouvés";
$lang['online_footcount']                = "... %1\$d utilisateurs connectés";
$lang['viewlogs_footcount']              = "... %1\$d logs trouvés / %2\$d par page";

// Submit Buttons
$lang['add_adjustment'] = 'Ajouter un ajustement';
$lang['add_account'] = 'Ajouter un compte';
$lang['add_event'] = 'Ajouter un événement';
$lang['add_item'] = 'Ajouter un objet';
$lang['add_member'] = 'Ajouter un membre';
$lang['add_news'] = 'Ajouter une nouvelle';
$lang['add_raid'] = 'Ajouter un raid';
$lang['add_style'] = 'Ajouter un modèle';
$lang['add_turnin'] = 'Ajouter une restitution';
$lang['delete_adjustment'] = 'Supprimer un ajustement';
$lang['delete_event'] = 'Supprimer un événement';
$lang['delete_item'] = 'Supprimer un objet';
$lang['delete_member'] = 'Supprimer un membre';
$lang['delete_news'] = 'Supprimer une nouvelle';
$lang['delete_raid'] = 'Supprimer un raid';
$lang['delete_selected_members'] = 'Supprimer les membres sélectionnés';
$lang['delete_style'] = 'Supprimer un modèle';
$lang['mass_delete'] = 'Suppression multiples';
$lang['mass_update'] = 'Mise à jour multiples';
$lang['parse_log'] = 'Parcourir le log';
$lang['search_existing'] = 'Rechercher';
$lang['select'] = 'Selectionner';
$lang['transfer_history'] = 'Transférer l\'historique';
$lang['update_adjustment'] = 'Mettre à jour l\'ajustement';
$lang['update_event'] = 'Mettre à jour l\'événement';
$lang['update_item'] = 'Mettre à jour l\'objet';
$lang['update_member'] = 'Mettre à jour le membre';
$lang['update_news'] = 'Mettre à jour la nouvelles';
$lang['update_raid'] = 'Mettre à jour le raid';
$lang['update_style'] = 'Mettre à jour le modèle';

// Misc
$lang['account_enabled'] = 'Compte activé';
$lang['adjustment_value'] = 'Valeur d\'ajustement';
$lang['adjustment_value_note'] = 'peut être négative';
$lang['code'] = 'Code';
$lang['contact'] = 'Contact';
$lang['create'] = 'Créer';
$lang['found_members'] = "A parcouru %1\$d lignes, à trouvé %2\$d membres";
$lang['headline'] = 'Titre';
$lang['hide'] = 'Cacher ?';
$lang['install'] = 'Installer';
$lang['item_search'] = 'Rechercher un objet';
$lang['list_prefix'] = 'Prefix de liste';
$lang['list_suffix'] = 'Suffix de liste';
$lang['logs'] = 'Logs';
$lang['log_find_all'] = 'Chercher tout (anonymes compris)';
$lang['manage_members'] = 'Gérer les membres';
$lang['manage_plugins'] = 'Gérer les plugins';
$lang['manage_users'] = 'Gérer les utilisateurs';
$lang['mass_update_note'] = 'Si vous souhaitez affecter les modifications à tous les membres sélectionnés, utilisez ces commandes pour changer leurs propriétés et cliquez sur "Mise à jour multiples".
                             Pour effacer les comptes sélectionnés, cliquez uniquement sur "Suppression multiples".';
$lang['members'] = 'Membres';
$lang['member_rank'] = 'Rand du membre';
$lang['message_body'] = 'Corps du texte';
$lang['message_show_loot_raid'] = 'Afficher les loots du raid :';
$lang['results'] = "%1\$d Resultatss (\"%2\$s\")";
$lang['search'] = 'Rechercher';
$lang['search_members'] = 'Rechercher un membre';
$lang['should_be'] = 'Devrait être';
$lang['styles'] = 'Modèles';
$lang['title'] = 'Titre';
$lang['uninstall'] = 'Désinstaller';
$lang['enable']		= 'Activer';
$lang['update_date_to'] = "Mettre la date à<br />%1\$s?";
$lang['version'] = 'Version';
$lang['x_members_s'] = "%1\$d membre";
$lang['x_members_p'] = "%1\$d membres";

// Permission Messages
$lang['noauth_a_event_add']    = 'Vous n\'avez pas la permission d\'ajouter des événements.';
$lang['noauth_a_event_upd']    = 'Vous n\'avez pas la permission de mettre à jour des événements.';
$lang['noauth_a_event_del']    = 'Vous n\'avez pas la permission d\'effacer des événements.';
$lang['noauth_a_groupadj_add'] = 'Vous n\'avez pas la permission d\'ajouter des ajustements de groupe.';
$lang['noauth_a_groupadj_upd'] = 'Vous n\'avez pas la permission de mettre à jour des ajustements de groupe.';
$lang['noauth_a_groupadj_del'] = 'Vous n\'avez pas la permission d\'effacer des ajustements de groupe.';
$lang['noauth_a_indivadj_add'] = 'Vous n\'avez pas la permission d\'ajouter des ajustements individuels.';
$lang['noauth_a_indivadj_upd'] = 'Vous n\'avez pas la permission de mettre à jour des ajustements individuels.';
$lang['noauth_a_indivadj_del'] = 'Vous n\'avez pas la permission d\'effacer des ajustements individuels.';
$lang['noauth_a_item_add']     = 'Vous n\'avez pas la permission d\'ajouter des objets.';
$lang['noauth_a_item_upd']     = 'Vous n\'avez pas la permission de mettre à jour des objets.';
$lang['noauth_a_item_del']     = 'Vous n\'avez pas la permission d\'effacer des objets.';
$lang['noauth_a_news_add']     = 'Vous n\'avez pas la permission d\'ajouter des nouvelles.';
$lang['noauth_a_news_upd']     = 'Vous n\'avez pas la permission de mettre à jour des nouvelles.';
$lang['noauth_a_news_del']     = 'Vous n\'avez pas la permission d\'effacer des nouvelles.';
$lang['noauth_a_raid_add']     = 'Vous n\'avez pas la permission d\'ajouter des raids.';
$lang['noauth_a_raid_upd']     = 'Vous n\'avez pas la permission de mettre à jour des raids.';
$lang['noauth_a_raid_del']     = 'Vous n\'avez pas la permission d\'effacer des raids.';
$lang['noauth_a_turnin_add']   = 'Vous n\'avez pas la permission d\'ajouter des restitutions.';
$lang['noauth_a_config_man']   = 'Vous n\'avez pas la permission de gérer la configuration d\'EQdkp.';
$lang['noauth_a_members_man']  = 'Vous n\'avez pas la permission de gérer les membres de guilde.';
$lang['noauth_a_plugins_man']  = 'Vous n\'avez pas la permission de gérer les plugins.';
$lang['noauth_a_styles_man']   = 'Vous n\'avez pas la permission de gérer les modèles.';
$lang['noauth_a_users_man']    = 'Vous n\'avez pas la permission gérer les paramètres de compte utilisateur.';
$lang['noauth_a_logs_view']    = 'Vous n\'avez pas la permission d\'afficher les logs.';

// Submission Success Messages
$lang['admin_add_adj_success']               = "Un ajustement à %1\$s de %2\$.2f a été ajouté.";
$lang['admin_add_admin_success']             = "Un e-mail a été envoyé à %1\$s avec ses informations administratives.";
$lang['admin_add_event_success']             = "Une valeur par défaut de %1\$s pour un raid le %2\$s a été ajoutée.";
$lang['admin_add_iadj_success']              = "Un ajustement individuel à %1\$s de %2\$.2f pour %3\$s a été ajouté.";
$lang['admin_add_item_success']              = "Un achat d\'objet de  %1\$s, acquis par %2\$s pour %3\$.2f a été ajouté.";
$lang['admin_add_member_success']            = "%1\$s a été ajouté comme membre.";
$lang['admin_add_news_success']              = 'La nouvelle a été ajoutée.';
$lang['admin_add_raid_success']              = "Le %1\$d/%2\$d/%3\$d raid du %4\$s a été ajouté.";
$lang['admin_add_style_success']             = 'Le nouveau modèle a été ajouté correctement.';
$lang['admin_add_turnin_success']            = "%1\$s a été transféré de %2\$s à %3\$s.";
$lang['admin_delete_adj_success']            = "L\'ajustement à %1\$s de %2\$.2f a été supprimé.";
$lang['admin_delete_admins_success']         = "Les administrateurs sélectionnés ont été supprimés.";
$lang['admin_delete_event_success']          = "La valeur par défaut de %1\$s pour le raid du %2\$s a été supprimée.";
$lang['admin_delete_iadj_success']           = "L\'ajustement individuel à %1\$s de %2\$.2f pour %3\$s a été supprimé.";
$lang['admin_delete_item_success']           = "L\achat d\'objet de %1\$s, acquis par %2\$s pour %3\$.2f a été supprimé.";
$lang['admin_delete_members_success']        = "%1\$s, avec tout son historiques a été supprimé.";
$lang['admin_delete_news_success']           = 'La nouvelle a été supprimée.';
$lang['admin_delete_raid_success']           = 'Le raid et tous ses objets associés ont été supprimés.';
$lang['admin_delete_style_success']          = 'Le modèle a été supprimé correctement.';
$lang['admin_delete_user_success']           = "Le compte avec le nom  %1\$s a été supprimé.";
$lang['admin_set_perms_success']             = "Toutes les permissions d\'administration ont été mises à jour.";
$lang['admin_transfer_history_success']      = "Tout l'historiqe de %1\$s a été transféré à %2\$s et %1\$s a été supprimé.";
$lang['admin_update_account_success']        = "Vos paramètres de compte ont été mis à jour.";
$lang['admin_update_adj_success']            = "L\ajustement à %1\$s de %2\$.2f a été mis à jour.";
$lang['admin_update_event_success']          = "La valeur par défaut de %1\$s pour le raid du %2\$s a été mise à jour.";
$lang['admin_update_iadj_success']           = "L\'adjustment à %1\$s de %2\$.2f pour %3\$s a été mis à jour.";
$lang['admin_update_item_success']           = "L\'achat d\objet de %1\$s, acquis par %2\$s pour %3\$.2f a été mis à jour.";
$lang['admin_update_member_success']         = "Les paramètres du membre %1\$s ont été mis à jour.";
$lang['admin_update_news_success']           = 'La nouvelle a été mise à jour.';
$lang['admin_update_raid_success']           = "Le %1\$d/%2\$d/%3\$d raid du %4\$s a été mis à jour.";
$lang['admin_update_style_success']          = 'Le modèle a été mis à jour correctement.';

$lang['admin_raid_success_hideinactive']     = 'Mise à jour des membres actifs/inactifs en cours ...';

// Delete Confirmation Texts
$lang['confirm_delete_adj']     = 'Etes-vous certain de vouloir supprimer cet ajustement de groupe ?';
$lang['confirm_delete_admins']  = 'Etes-vous certain de vouloir supprimer les administrateurs sélectionnés ?';
$lang['confirm_delete_event']   = 'Etes-vous certain de vouloir supprimer cet événement ?';
$lang['confirm_delete_iadj']    = 'Etes-vous certain de vouloir supprimer cet ajustement individuel ?';
$lang['confirm_delete_item']    = 'Etes-vous certain de vouloir supprimer cet objet ?';
$lang['confirm_delete_members'] = 'Etes-vous certain de vouloir supprimer les membres suivants ?';
$lang['confirm_delete_news']    = 'Etes-vous certain de vouloir supprimer cette nouvelle ?';
$lang['confirm_delete_raid']    = 'Etes-vous certain de vouloir supprimer ce raid ?';
$lang['confirm_delete_style']   = 'Etes-vous certain de vouloir supprimer ce modèle ?';
$lang['confirm_delete_users']   = 'Etes-vous certain de vouloir supprimer les comptes d\'utilisateurs suivants ?';

// Log Actions
$lang['action_event_added']      = 'Evénement ajouté';
$lang['action_event_deleted']    = 'Evénement supprimé';
$lang['action_event_updated']    = 'Evénement mis à jour';
$lang['action_groupadj_added']   = 'Ajustement de groupe ajouté';
$lang['action_groupadj_deleted'] = 'Ajustement de groupe supprimé';
$lang['action_groupadj_updated'] = 'Ajustement de groupe mis à jour';
$lang['action_history_transfer'] = 'Transfert d\'historique d\'un membre';
$lang['action_indivadj_added']   = 'Ajustement individuel ajouté';
$lang['action_indivadj_deleted'] = 'Ajustement individuel supprimé';
$lang['action_indivadj_updated'] = 'Ajustement individuel mis à jour';
$lang['action_item_added']       = 'Objet ajouté';
$lang['action_item_deleted']     = 'Objet supprimé';
$lang['action_item_updated']     = 'Objet mis à jour';
$lang['action_member_added']     = 'Membre ajouté';
$lang['action_member_deleted']   = 'Membre supprimé';
$lang['action_member_updated']   = 'Membre mis à jour';
$lang['action_news_added']       = 'Nouvelle ajoutée';
$lang['action_news_deleted']     = 'Nouvelle supprimée';
$lang['action_news_updated']     = 'Nouvelles mise à jour';
$lang['action_raid_added']       = 'Raid ajouté';
$lang['action_raid_deleted']     = 'Raid supprimé';
$lang['action_raid_updated']     = 'Raid mis à jour';
$lang['action_turnin_added']     = 'Restitution ajoutée';

// Before/After
$lang['adjustment_after']  = 'Ajustement après';
$lang['adjustment_before'] = 'Ajustement avant';
$lang['attendees_after']   = 'Participants après';
$lang['attendees_before']  = 'Participants avant';
$lang['buyers_after']      = 'Acheteurs après';
$lang['buyers_before']     = 'Acheteurs avant';
$lang['class_after']       = 'Classe après';
$lang['class_before']      = 'Classe avant';
$lang['earned_after']      = 'Gagné après';
$lang['earned_before']     = 'Gagné avant';
$lang['event_after']       = 'Evénement après';
$lang['event_before']      = 'Evénement avant';
$lang['headline_after']    = 'Titre après';
$lang['headline_before']   = 'Titre avant';
$lang['level_after']       = 'Niveau après';
$lang['level_before']      = 'Niveau avant';
$lang['members_after']     = 'Membres après';
$lang['members_before']    = 'Membres avant';
$lang['message_after']     = 'Message après';
$lang['message_before']    = 'Message avant';
$lang['name_after']        = 'Nom après';
$lang['name_before']       = 'Nom avant';
$lang['note_after']        = 'Note après';
$lang['note_before']       = 'Note avant';
$lang['race_after']        = 'Race après';
$lang['race_before']       = 'Race avant';
$lang['raid_id_after']     = 'ID de raid après';
$lang['raid_id_before']    = 'ID de aaid avant';
$lang['reason_after']      = 'Raison après';
$lang['reason_before']     = 'Raison avant';
$lang['spent_after']       = 'Dépensé après';
$lang['spent_before']      = 'Dépensé avant';
$lang['value_after']       = 'Valeur après';
$lang['value_before']      = 'Valeur avant';

// Configuration
$lang['general_settings'] = 'Options générales';
$lang['guildtag'] = 'Nom de la guilde';
$lang['guildtag_note'] = 'Utilisé dans le titre de presque toutes les pages';
$lang['parsetags'] = 'Tags de guilde à parcourir';
$lang['parsetags_note'] = 'Ceux listés seront disponibles en option au moment de l\'analyse des logs de raid.';
$lang['domain_name'] = 'Nom de domaine';
$lang['server_port'] = 'Port du serveur';
$lang['server_port_note'] = 'Le port de votre serveur web. Généralement 80.';
$lang['script_path'] = 'Chemin (dossier) du script';
$lang['script_path_note'] = 'Chemin ou se trouve EQdkp, en relation avec le nom de domaine';
$lang['site_name'] = 'Nom du site';
$lang['site_description'] = 'Description du site';
$lang['point_name'] = 'Nom du point';
$lang['point_name_note'] = 'Ex: DKP, RP, etc.';
$lang['enable_account_activation'] = 'Activer l\'activation de compte';
$lang['none'] = 'Aucun';
$lang['admin'] = 'Admin';
$lang['default_language'] = 'Langue par défaut';
$lang['default_locale'] = 'Local par défaut (option du personnage seulement; ceci n\'affecte pas la langue)';
$lang['default_game'] = 'Jeu par défaut';
$lang['default_game_warn'] = 'Changer le jeu par défaut peut annuler les autres changements de cette session.';
$lang['default_style'] = 'Modèle par défaut';
$lang['default_page'] = 'Page d\'index par défaut';
$lang['hide_inactive'] = 'Cacher les membres inactifs';
$lang['hide_inactive_note'] = 'Cacher les membres qui n\'ont pas participés à un raid depuis [inactive period] jours ?';
$lang['inactive_period'] = 'Période d\'inactivité';
$lang['inactive_period_note'] = 'Nombre de jour qu\'un membre peut rater et rester considéré comme actif';
$lang['inactive_point_adj'] = 'Ajustement de points d\'inactivité';
$lang['inactive_point_adj_note'] = 'Points d\'ajustement d\'un membre lorsqu\'il devient inactif.';
$lang['active_point_adj'] = 'Activer les points d\'ajustement';
$lang['active_point_adj_note'] = 'Points d\'ajustement d\'un membre lorsqu\'il devient actif.';
$lang['enable_gzip'] = 'Activer la compression Gzip';
$lang['show_item_stats'] = 'Montrer les statistiques des objets';
$lang['show_item_stats_note'] = 'Essaye de récupérer les statistiques d\'un objet depuis internet. Ceci peut influencer la rapidité de certaines pages.';
$lang['default_permissions'] = 'Permissions par défaut';
$lang['default_permissions_note'] = 'Ce sont les permissions des utilisateurs qui ne sont pas connectés ainsi que des nouveaux utilisateurs quand ils s\'inscrivent. Les permissions en <b>gras</b> sont les permissions d\'administration,
                                     il est fortement recommandé de ne mettre aucune de ces permissions par défaut. Les permissions en <i>italique</i> sont exclusivement utilisées par les plugins. Vous pourrez changer les permissions individuellement en cliquant sur "Gérer les utilisateurs".';
$lang['plugins'] = 'Plugins';
$lang['no_plugins'] = 'Le dossier des plugins (./plugins/) est vide.';
$lang['cookie_settings'] = 'Options du cookie';
$lang['cookie_domain'] = 'Domaine du cookie';
$lang['cookie_name'] = 'Nom du cookie';
$lang['cookie_path'] = 'Chemin du cookie';
$lang['session_length'] = 'Temps de la session (secondes)';
$lang['email_settings'] = 'Options d\'email';
$lang['admin_email'] = 'Adresse email de l\'administrateur';

// Admin Index
$lang['anonymous'] = 'Anonyme';
$lang['database_size'] = 'Taille de la base de données';
$lang['eqdkp_started'] = 'EQdkp démarré';
$lang['ip_address'] = 'Adresse IP';
$lang['items_per_day'] = 'Objets par jour';
$lang['last_update'] = 'Dernière mise à jour';
$lang['location'] = 'Localisation';
$lang['new_version_notice'] = "EQdkp version %1\$s est <a href=\"http://sourceforge.net/project/showfiles.php?group_id=69529\" target=\"_blank\">disponible au téléchargement</a>.";
$lang['number_of_items'] = 'Nombre d\'objets';
$lang['number_of_logs'] = 'Nombre de logs';
$lang['number_of_members'] = 'Nombre de membres (actifs/inactifs)';
$lang['number_of_raids'] = 'Nombre de raids';
$lang['raids_per_day'] = 'Raids par Jour';
$lang['statistics'] = 'Statistiques';
$lang['totals'] = 'Totaux';
$lang['version_update'] = 'Mise à jour de la version';
$lang['who_online'] = 'Qui est en ligne';

// Style Management
$lang['style_settings'] = 'Options de modèle';
$lang['style_name'] = 'Nom du modèle';
$lang['template'] = 'Gabarit';
$lang['element'] = 'Elément';
$lang['background_color'] = 'Couleur de fond';
$lang['fontface1'] = 'Police 1';
$lang['fontface1_note'] = 'Police par défaut';
$lang['fontface2'] = 'Police 2';
$lang['fontface2_note'] = 'Police des champs "input"';
$lang['fontface3'] = 'Police 3';
$lang['fontface3_note'] = 'Pas utilisé actuellement';
$lang['fontsize1'] = 'Taille de la police 1';
$lang['fontsize1_note'] = 'Petit';
$lang['fontsize2'] = 'Taille de la police 2';
$lang['fontsize2_note'] = 'Moyen';
$lang['fontsize3'] = 'Taille de la police 3';
$lang['fontsize3_note'] = 'Grand';
$lang['fontcolor1'] = 'Couleur de la police 1';
$lang['fontcolor1_note'] = 'Couleur par défaut';
$lang['fontcolor2'] = 'Couleur de la police 2';
$lang['fontcolor2_note'] = 'Couleur utilisée hors des tableaux (menus, titres, copyright)';
$lang['fontcolor3'] = 'Couleur de la police 3';
$lang['fontcolor3_note'] = 'Couleur de la police des champs "entrée"';
$lang['fontcolor_neg'] = 'Couleur de la police des négatifs';
$lang['fontcolor_neg_note'] = 'Couleur pour les négatifs/mauvais nombres';
$lang['fontcolor_pos'] = 'Couleur de la police des positifs';
$lang['fontcolor_pos_note'] = 'Couleur pour les positifs/bons nombres';
$lang['body_link'] = 'Couleur des liens';
$lang['body_link_style'] = 'Style des liens';
$lang['body_hlink'] = 'Couleur des liens quand on passe dessus';
$lang['body_hlink_style'] = 'Style des liens quand on passe dessus';
$lang['header_link'] = 'Liens d\'en-tête';
$lang['header_link_style'] = 'Style des liens d\'en-tête';
$lang['header_hlink'] = 'Liens d\'en-tête quand on passe dessus';
$lang['header_hlink_style'] = 'Style des liens d\'en-tête quand on passe dessus';
$lang['tr_color1'] = 'Couleur de la table, Ligne 1';
$lang['tr_color2'] = 'Couleur de la table, Ligne 2';
$lang['th_color1'] = 'Couleur du haut de la table';
$lang['table_border_width'] = 'Epaisseur de la bordure des tableaux';
$lang['table_border_color'] = 'Couleur de la bordure des tableaux';
$lang['table_border_style'] = 'Style de la bordure des tableaux';
$lang['input_color'] = 'Couleur de fond des champs "entrée"';
$lang['input_border_width'] = 'Largeur de la bordure des champs "entrée"';
$lang['input_border_color'] = 'Couleur de la bordure des champs "entrée"';
$lang['input_border_style'] = 'Style de la bordure des champs "entrée"';
$lang['style_configuration'] = 'Configuration du modèle';
$lang['style_date_note'] = 'Pour les champs date/temps, la syntaxe utilisée est identique à la fonction <a href="http://www.php.net/manual/en/function.date.php" target="_blank">date()</a> du PHP.';
$lang['attendees_columns'] = 'Colonnes des participants';
$lang['attendees_columns_note'] = 'Nombre de colonnes utilisées pour les participants quand on regarde un raid';
$lang['date_notime_long'] = 'Date sans l\'heure (long)';
$lang['date_notime_short'] = 'Date sans l\'heure (court)';
$lang['date_time'] = 'Date avec l\'heure';
$lang['logo_path'] = 'Fichier du logo.';
$lang['logo_path_note'] = 'Sélectionnez une image à partir de /templates/template/images/ ou inserez l\'URL complète d\'une image sur internet. Insérez l\'entête http:// !)';
$lang['logo_path_config'] = 'Sélectionnez un fichier à partir de votre disque dur et télécharger le nouveau logo.';

// Errors
$lang['error_invalid_adjustment'] = 'Un ajustement valide n\'a pas été fourni.';
$lang['error_invalid_plugin']     = 'Un plugin valide n\' pas été fourni.';
$lang['error_invalid_style']      = 'Un style valide n\'a pas été fourni.';

// Verbose log entry lines
$lang['new_actions']           = 'Actions d\'administration récentes';
$lang['vlog_event_added']      = "%1\$s a ajouté l\'événement '%2\$s' pour une valeur de %3\$.2f points.";
$lang['vlog_event_updated']    = "%1\$s a mis à jour l\'événement '%2\$s'.";
$lang['vlog_event_deleted']    = "%1\$s a supprimé l\'événement '%2\$s'.";
$lang['vlog_groupadj_added']   = "%1\$s a ajouté un ajustement de groupe de %2\$.2f points.";
$lang['vlog_groupadj_updated'] = "%1\$s a mis à jour un ajustement de groupe de %2\$.2f points.";
$lang['vlog_groupadj_deleted'] = "%1\$s a supprimé un ajustement de groupe de %2\$.2f points.";
$lang['vlog_history_transfer'] = "%1\$s a transféré l\'historique de %2\$s vers %3\$s.";
$lang['vlog_indivadj_added']   = "%1\$s a ajouté un ajustement individuel de %2\$.2f à %3\$d membres.";
$lang['vlog_indivadj_updated'] = "%1\$s a mis à jour un ajustement individuel de %2\$.2f à %3\$s.";
$lang['vlog_indivadj_deleted'] = "%1\$s a supprimé un ajustement individuel de %2\$.2f à %3\$s.";
$lang['vlog_item_added']       = "%1\$s a ajouté un objet '%2\$s' à la charge de %3\$d membres pour %4\$.2f points.";
$lang['vlog_item_updated']     = "%1\$s a mis à jour un objet '%2\$s' à la charge de %3\$d membres.";
$lang['vlog_item_deleted']     = "%1\$s suppression de l\'objet '%2\$s' à la charge de %3\$d membres.";
$lang['vlog_member_added']     = "%1\$s a ajouté le membre %2\$s.";
$lang['vlog_member_updated']   = "%1\$s a mis à jour le membre %2\$s.";
$lang['vlog_member_deleted']   = "%1\$s a supprimé le membre %2\$s.";
$lang['vlog_news_added']       = "%1\$s a ajouté la nouvelle '%2\$s'.";
$lang['vlog_news_updated']     = "%1\$s a mis à jour la nouvelle '%2\$s'.";
$lang['vlog_news_deleted']     = "%1\$s a supprimé la nouvelle '%2\$s'.";
$lang['vlog_raid_added']       = "%1\$s a ajouté un raid sur '%2\$s'.";
$lang['vlog_raid_updated']     = "%1\$s a mis à jour un raid sur '%2\$s'.";
$lang['vlog_raid_deleted']     = "%1\$s a supprimé un raid sur '%2\$s'.";
$lang['vlog_turnin_added']     = "%1\$s a ajouté une restitution de %2\$s à %3\$s pour '%4\$s'.";

// Location messages
$lang['adding_groupadj'] = 'Ajout d\'un ajustement de groupe';
$lang['adding_indivadj'] = 'Ajout d\'un ajustement individuel';
$lang['adding_item'] = 'Ajout d\'un objet';
$lang['adding_news'] = 'Ajout d\'une nouvelle';
$lang['adding_raid'] = 'Ajout d\'un raid';
$lang['adding_turnin'] = 'Ajout d\'une restitution';
$lang['editing_groupadj'] = 'Edition d\'un ajustement de groupe';
$lang['editing_indivadj'] = 'Edition d\'un ajustement individuel';
$lang['editing_item'] = 'Edition d\'un objet';
$lang['editing_news'] = 'Edition d\'une nouvelle';
$lang['editing_raid'] = 'Edition d\'un raid';
$lang['listing_events'] = 'Liste les événements';
$lang['listing_groupadj'] = 'Liste des ajustements de groupes';
$lang['listing_indivadj'] = 'Liste des ajustements individuels';
$lang['listing_itemhist'] = 'Liste de l\'historique des objets';
$lang['listing_itemvals'] = 'Liste des valeurs des objets';
$lang['listing_members'] = 'Liste des membres';
$lang['listing_raids'] = 'Liste des raids';
$lang['managing_config'] = 'Gère la configuration d\'EQdkp';
$lang['managing_members'] = 'Gère les membres';
$lang['managing_plugins'] = 'Gère les plugins';
$lang['managing_styles'] = 'Gère les styles';
$lang['managing_users'] = 'Gère les utilisateurs';
$lang['parsing_log'] = 'Parcours le log';
$lang['viewing_admin_index'] = 'Regarde l\'index de l\'admin';
$lang['viewing_event'] = 'Regarde les événements';
$lang['viewing_item'] = 'Regarde les objets';
$lang['viewing_logs'] = 'Regarde les logs';
$lang['viewing_member'] = 'Regarde les membres';
$lang['viewing_mysql_info'] = 'Regarde les informations MySQL';
$lang['viewing_news'] = 'Regarde les nouvelles';
$lang['viewing_raid'] = 'Regarde les raid';
$lang['viewing_stats'] = 'Regarde les statistiques';
$lang['viewing_summary'] = 'Regarde les résumés';

// Help lines
$lang['b_help'] = 'Texte en gras : [b]texte[/b] (alt+b)';
$lang['i_help'] = 'Texte en italique : [i]texte[/i] (alt+i)';
$lang['u_help'] = 'Texte souligné : [u]texte[/u] (alt+u)';
$lang['q_help'] = 'Citation : [quote]texte[/quote] (alt+q)';
$lang['c_help'] = 'Texte centré : [center]texte[/center] (alt+c)';
$lang['p_help'] = 'Insérer une image : [img]http://image_url[/img] (alt+p)';
$lang['w_help'] = 'Insérer une URL : [url]http://url[/url] ou [url=http://url]URL texte[/url]  (alt+w)';
$lang['it_help'] = 'insérer un objet : [item]Judgement Breastplate[/item] (shift+alt+t)';
$lang['ii_help'] = 'Insérer l\icône d\'un objet : [itemicon]Judgement Breastplate[/itemicon] (shift+alt+o)';

// Manage Members Menu (yes, MMM)
$lang['add_member'] = 'Ajouter un nouveau membre';
$lang['list_edit_del_member'] = 'Lister, éditer ou supprimer un membre';
$lang['edit_ranks'] = 'Modifier les rangs de membre';
$lang['transfer_history'] = 'Transférer l\'historique d\'un membre';

// MySQL info
$lang['mysql'] = 'MySQL';
$lang['mysql_info'] = 'Informations';
$lang['eqdkp_tables'] = 'Tables EQdkp';
$lang['table_name'] = 'Nom de la table';
$lang['rows'] = 'Rangées';
$lang['table_size'] = 'Taille de la table';
$lang['index_size'] = 'Taille de l\'index';
$lang['num_tables'] = "%d tables";

//Backup
$lang['backup']            = 'Sauvegarde';
$lang['backup_database']   = 'Sauvegarder la base';
$lang['backup_title']      = 'Créer une sauvegarde de la base';
$lang['backup_type']       = 'Format de la sauvegarde';
$lang['create_table']      = 'Ajouter l\'option \'CREATE TABLE\' ?';
$lang['skip_nonessential'] = 'Ignorer les éléments mineurs ?<br />Les lignes de la table eqdkp_sessions ne seront pas insérées.';
$lang['gzip_content']      = 'Compression GZIP ?<br />Le fichier généré sera plus petit.';
$lang['backup_no_table_prefix']    = '<strong>ATTENTION:</strong> Votre installation d\'EQdkp n\'a pas de prefixe pour ses tables. Toutes les tables de plugins ne seront pas sauvegardées.';

// plus
$lang['in_database']  = 'Sauvegardé dans la base';

//Log Users Actions
$lang['action_user_added']     = 'Utilisateur ajouté';
$lang['action_user_deleted']   = 'Utilisateur supprimé';
$lang['action_user_updated']   = 'Utilisateur mis à jour';

$lang['vlog_user_added']     = "%1\$s a ajouté l\'utilisateur %2\$s.";
$lang['vlog_user_updated']   = "%1\$s a mis à jour l\'utilisateur %2\$s.";
$lang['vlog_user_deleted']   = "%1\$s a supprimé l\'utilisateur %2\$s.";

//MultiDKP
$lang['action_multidkp_added']     = "Groupe MultiDKP ajouté";
$lang['action_multidkp_deleted']   = "Groupe MultiDKP supprimé";
$lang['action_multidkp_updated']   = "Groupe MultiDKP mis à jour";
$lang['action_multidkp_header']    = "MultiDKP";

$lang['vlog_multidkp_added']     = "%1\$s a ajouté le groupe MultiDKP %2\$s.";
$lang['vlog_multidkp_updated']   = "%1\$s a mis à jour le groupe MultiDKP %2\$s.";
$lang['vlog_multidkp_deleted']   = "%1\$s a supprimé le groupe MultiDKP %2\$s.";

$lang['default_style_overwrite']   = "Remplace tous les réglages de modèle des utilisateurs (tous les utilisateurs utilisent le modèle par défaut)";
$lang['class_colors']              = "Couleurs des classes";

#Plugins
$lang['description'] = 'Description';
$lang['manual'] = 'Manuel';
$lang['homepage'] = 'Site internet';
$lang['readme'] = 'Lisez-moi';
$lang['link'] = 'Lien';
$lang['infos'] = 'Infos';

// Plugin Install / Uninstall
$lang['plugin_inst_success']  = 'Succès';
$lang['plugin_inst_error']  = 'Erreur';
$lang['plugin_inst_message']  = "Le plugin <i>%1\$s</i> a été correctement %2\$s.";
$lang['plugin_inst_installed'] = 'installé';
$lang['plugin_inst_uninstalled'] = 'désinstallé';
$lang['plugin_inst_errormsg1'] = "Des erreurs ont été détectés durant le processus d\'%1\$s : %2\$s";
$lang['plugin_inst_errormsg2']  = "%1\$s peut ne pas avoir été correctement %2\$s.";

$lang['background_image'] = 'Image d\'arrière plan ( 1000x1000px) [optional]';
$lang['css_file'] = 'Fichier CSS - ignore la plupart des réglages de couleur sur ce site. [optional]';

$lang['plugin_inst_sql_note'] = 'Une erreur SQL n\'implique pas forcément une mauvais installation du plugin. Esssayez le plugin et si des erreurs se produisent, désinstallez et installez de nouveau.';

// Plugin Update Warn Class
$lang['puc_perform_intro']          = 'Les plugins suivants nécessitent une mise à jour de leurs bases. Cliquez sur le lien "résoudre" afin d\'effectuer les modifications pour chaque plugin.<br/>Les tables suivantes sont périmées :';
$lang['puc_pluginneedupdate']       = "<b>%1\$s</b>: (Requires database updates from %2\$s to %3\$s)";
$lang['puc_solve_dbissues']         = 'résoudre';
$lang['puc_unknown']                = '[unknown]';
?>
