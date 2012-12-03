<?php
 /*
 * Project:     EQdkp-Plus
 * License:     Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:       2006
 * Date:        $Date: 2009-03-17 23:56:20 +0100 (Di, 17 Mrz 2009) $
 * -----------------------------------------------------------------------
 * @author      $Author: osr-corgan $
 * @copyright   2006-2008 Corgan - Stefan Knaak | Wallenium & the EQdkp-Plus Developer Team
 * @link        http://eqdkp-plus.com
 * @package     eqdkp-plus
 * @version     $Rev: 4277 $
 *
 * $Id: lang_main.php 4277 2009-03-17 22:56:20Z osr-corgan $
 */

// Do not remove. Security Option!
if ( !defined('EQDKP_INC') ){
    header('HTTP/1.0 404 Not Found');exit;
}

//---- Main ----
$plang['pluskernel']          			= 'PLUS Config';
$plang['pk_adminmenu']         			= 'PLUS Config';
$plang['pk_settings']					= 'Paramètres';
$plang['pk_date_settings']				= 'd.m.y';

//---- Javascript stuff ----
$plang['pk_plus_about']					= 'A Propos de EQDKP-PLUS';
$plang['updates']						= 'Mises à jour disponibles';
$plang['pk_config_header']				= 'Paramètres EQDKP-PLUS ';
$plang['loading']						= 'Chargement...';
$plang['pk_close_jswin1']      			= 'Fermez la fenêtre';
$plang['pk_close_jswin2']     			= ' avant de l\'ouvrir à nouveau!';
$plang['pk_help_header']				= 'Aide';
$plang['pk_plus_comments']  			= 'Commentaires';

//---- Updater Stuff ----
$plang['pk_alt_attention']				= 'Attention';
$plang['pk_alt_ok']						= 'Tout est OK!';
$plang['pk_updates_avail']				= 'Mises à jour disponibles';
$plang['pk_updates_navail']				= 'Mises à jour non disponibles';
$plang['pk_no_updates']					= 'Vos versions sont toutes à jour. Pas de nouvelles versions disponibles.';
$plang['pk_act_version']				= 'Nouvelle Version';
$plang['pk_inst_version']				= 'Installation';
$plang['pk_changelog']					= 'Modifications';
$plang['pk_download']					= 'Téléchargement';
$plang['pk_upd_information']			= 'Information';
$plang['pk_enabled']					= 'Autorisé';
$plang['pk_disabled']					= 'non autorisé';
$plang['pk_auto_updates1']				= 'L\'avertissement de mise à jour est automatique';
$plang['pk_auto_updates2']				= 'Si vous désactivez ce paramètre, vous devrez vérifier régulièrement les mises à jour pour empêcher le piratage et rester à jour!!!';
$plang['pk_module_name']				= 'Nom de Module';
$plang['pk_plugin_level']				= 'Level';
$plang['pk_release_date']				= 'Version';
$plang['pk_alt_error']					= 'Erreur';
$plang['pk_no_conn_header']				= 'Erreur de Connexion';
$plang['pk_no_server_conn']				= 'Une erreur s\'est produite lors de la tentative de contact avec le serveur de mise à jour, soit
															votre hôte ne permet pas les connexions sortantes ou l\'erreur a été causée
															par un problème de réseau. Veuillez visiter le forum Eqdkp-Plus pour vérifier
															que vous utilisez la dernière version.';
$plang['pk_reset_warning']				= 'Attention remise a zéro';

//---- Update Levels ----
$plang['pk_level_other']				= 'Autre';
$updatelevel = array (
	'Bugfix'							=> 'Bugfix',
	'Feature Release'					=> 'Texte de mise a jour',
	'Security Update'					=> 'Mise à jour de sécurité',
	'New version'						=> 'Nouvelle version',
	'Release Candidate'					=> 'Version Finale',
	'Public Beta'						=> 'Beta Publique',
	'Closed Beta'						=> 'Beta fermée',
	'Alpha'								=> 'Alpha',
);

//---- About ----
$plang['pk_version']					= 'Version';
$plang['pk_prodcutname']				= 'Produit';
$plang['pk_modification']				= 'Modif.';
$plang['pk_tname']						= 'Template';
$plang['pk_developer']					= 'Développeurs';
$plang['pk_plugin']						= 'Plugin';
$plang['pk_weblink']					= 'Liens Internet';
$plang['pk_phpstring']					= 'PHP Raccourcis';
$plang['pk_phpvalue']					= 'Valeur';
$plang['pk_donation']					= 'Donation';
$plang['pk_job']						= 'Métier';
$plang['pk_sitename']					= 'Site';
$plang['pk_dona_name']					= 'Nom';
$plang['pk_betateam1']					= 'Beta de test de l équipe (Germany)';
$plang['pk_betateam2']					= 'Ordre chronologique';
$plang['pk_created by']					= 'Créer par';
$plang['web_url']						= 'internet';
$plang['personal_url']					= 'Privée';
$plang['pk_credits']					= 'Crédits';
$plang['pk_sponsors']					= 'Donateurs';
$plang['pk_plugins']					= 'PlugIns';
$plang['pk_modifications']				= 'Modifs.';
$plang['pk_themes']						= 'Styles';
$plang['pk_additions']					= 'Code Additions';
$plang['pk_tab_stuff']					= 'L\'équipe EQDKP';
$plang['pk_tab_help']					= 'Aide';
$plang['pk_tab_tech']					= 'Technique';

//---- Settings ----
$plang['pk_save']						= 'Sauvegarde';
$plang['pk_save_title']					= 'Paramètres de sauvegarde';
$plang['pk_succ_saved']					= 'Les paramètres ont été correctement sauvegardés';
 // Tabs
$plang['pk_tab_global']					= 'Global';
$plang['pk_tab_multidkp']				= 'MultiDKP';
$plang['pk_tab_links']					= 'Links';
$plang['pk_tab_bosscount']				= 'BossCounter';
$plang['pk_tab_listmemb']				= 'MemberList';
$plang['pk_tab_itemstats']				= 'Itemstats';
// Global
$plang['pk_set_QuickDKP']				= 'Afficher QuickDKP';
$plang['pk_set_Bossloot']				= 'Afficher bossloot ?';
$plang['pk_set_ClassColor']				= 'Nom des classes en couleur';
$plang['pk_set_Updatecheck']			= 'Activer la vérification des mises à jour';
$plang['pk_window_time1']				= 'Afficher la fenêtre toutes les';
$plang['pk_window_time2']				= 'Minutes';
// MultiDKP
$plang['pk_set_multidkp']				= 'Activer MultiDKP';
// Listmembers
$plang['pk_set_leaderboard']			= 'Afficher le classement';
$plang['pk_set_lb_solo']				= 'Afficher le classement par compte';
$plang['pk_set_rank']					= 'Afficher les rangs';
$plang['pk_set_rank_icon']				= 'Afficher l\'icône des rangs';
$plang['pk_set_level']					= 'Afficher le niveau';
$plang['pk_set_lastloot']				= 'Afficher le dernier loot';
$plang['pk_set_lastraid']				= 'Afficher le dernier raid';
$plang['pk_set_attendance30']			= 'Afficher la participation aux raids des 30 dernier jours';
$plang['pk_set_attendance60']			= 'Afficher la participation aux raids des 60 dernier jours';
$plang['pk_set_attendance90']			= 'Afficher la participation aux raids des 90 dernier jours';
$plang['pk_set_attendanceAll']			= 'Afficher la participation à tout les raids';
// Links
$plang['pk_set_links']					= 'Autoriser les liens';
$plang['pk_set_linkurl']				= 'URL du lien';
$plang['pk_set_linkname']				= 'Nom du lien';
$plang['pk_set_newwindow']				= 'Ouvrir dans une nouvelle fenêtre ?';
// BossCounter
$plang['pk_set_bosscounter']			= 'Afficher le Bosscounter';
//Itemstats
$plang['pk_set_itemstats']				= 'Afficher Itemstats';
$plang['pk_is_language']				= 'Langue Itemstats';
$plang['pk_german']						= 'Allemand';
$plang['pk_english']					= 'Anglais';
$plang['pk_french']						= 'Français';
$plang['pk_set_icon_ext']				= '';
$plang['pk_set_icon_loc']				= '';
$plang['pk_set_en_de']					= 'Traduire les objets de l\'anglais vers l\'allemand';
$plang['pk_set_de_en']					= 'Traduire les objets de l\'allemand vers l\'anglais';

################
# new sort
###############

//MultiDKP
//

$plang['pk_set_multi_Tooltip']					= 'Afficher infobulle DKP';
$plang['pk_set_multi_smartTooltip']			 	= 'Infobulle intelligent';

//Help
$plang['pk_help_colorclassnames']				= "Si activé, les joueurs seront affichés dans la couleurs de leur catégorie et leur icône de classe dans WOW.";
$plang['pk_help_quickdkp']						= "Voir l'utilisateur connecté sur tous les points qui sont les membres qui lui sont assignées dans le menu ci-dessus";
$plang['pk_help_boosloot']						= "Si activé, vous pouvez cliquer sur les noms des boss de raid dans la note et le bosscounter de disposer d'un aperçu détaillé des éléments déposés. Si inactive, il sera lié à Blasc.de (activer seulement si vous entrez dans un raid pour chaque boss)";
$plang['pk_help_autowarning']             		= "Avertit l'administrateur quand il se connecte, si des mises à jour sont disponibles.";
$plang['pk_help_warningtime']             		= "Intervalle entre les avertissements";
$plang['pk_help_multidkp']						= "MultiDKP permet la gestion et la présentation de comptes séparés. Active la gestion et l'aperçu des comptes MultiDKP.";
$plang['pk_help_dkptooltip']					= "Si activé, une info-bulle contenant des informations détaillées sur le calcul des points sera montré, lorsque la souris glisse sur les différents points.";
$plang['pk_help_smarttooltip']					= "Vue raccourcie des infobulles (A activer si vous avez plus de trois événements par compte)";
$plang['pk_help_links']                  		= "Dans ce menu, vous êtes en mesure de définir les différents liens qui seront affiché dans le menu principal.";
$plang['pk_help_bosscounter']             		= "Si activé, un tableau sera affiché sous le menu principal avec le bosskills. L'administration est gérée par le plugin Bossprogress";
$plang['pk_help_lm_leaderboard']				= "Si activé, un classement sera affiché au-dessus de la table des scores. Un classement est un tableau, où le DKP de chaque classe est affiché et trié dans l'ordre décroissant.";
$plang['pk_help_lm_rank']                 		= "Une colonne supplémentaire est affichée, qui affiche le rang du membre.";
$plang['pk_help_lm_rankicon']             		= "Au lieu de classer les noms, une icône est affichée. Quels articles sont disponibles, vous pouvez vérifier dans le dossier \ images \ rang";
$plang['pk_help_lm_level']						= "Une colonne supplémentaire est affichée, qui affiche le niveau du membre. ";
$plang['pk_help_lm_lastloot']             		= "Un supplément de colonnes est affichée, indiquant la date à laquelle un membre a reçu son dernier point.";
$plang['pk_help_lm_lastraid']             		= "Une colonne supplémentaire est affichée, indiquant la date du dernier raid auquels un membre a participé .";
$plang['pk_help_lm_atten30']					= "Une colonne supplémentaire est affichée, montrant un raid auquels les membre ont participés au cours des 30 derniers jours (en pourcentage).";
$plang['pk_help_lm_atten60']					= "Une colonne supplémentaire est affichée, montrant un raid auquels les membre ont participés au cours des 30 derniers jours (en pourcentage).";
$plang['pk_help_lm_atten90']					= "Une colonne supplémentaire est affichée, montrant un raid auquels les membre ont participés au cours des 30 derniers jours (en pourcentage). ";
$plang['pk_help_lm_attenall']             		= "Une colonne supplémentaire est affichée, montrant un raid auquels toutles membre ont participés";
$plang['pk_help_itemstats_on']				 	= "Itemstats demande d'information sur les éléments inscrits dans EQDKP dans les bases de données WOW (Blasc, Allahkazm, Thottbot). Ils seront affichés dans la couleur des articles de qualité y compris l'aide appelé WOW. Lorsque c'est activer, les éléments seront affichés avec un mouseover tooltip, semblable à WOW.";
$plang['pk_help_itemstats_search']				= "Quelle base de données devrait utiliser Itemstats en premier lieu pour rechercher l'information? Blasc ou Allakhazam?";
$plang['pk_help_itemstats_icon_ext']			= "Extension de fichier des images a afficher. Habituellement. Png ou. Jpg.";
$plang['pk_help_itemstats_icon_url']    		= "S'il vous plaît entrez l'URL où les images d'Itemstats sont situés. Allemand: http://www.buffed.de/images/wow/32/ en 32x32 ou 64x64 pixels.Anglais dans http://www.buffed.de/images/wow/64/ à Allakzam: http://www.buffed.de/images/wow/32 /> permuter";
$plang['pk_help_itemstats_translate_deeng']		= "Si active, l'information des bulles d'aide vous sera demandé en allemand, même lorsque la question est entré en anglais.";
$plang['pk_help_itemstats_translate_engde']		= "Si active, l'information des bulles d'aide vous sera demandé, en anglais, même si la question est entré en allemand.";

$plang['pk_set_leaderboard_2row']		  = 'Classement à 2 lignes';
$plang['pk_help_leaderboard_2row']        = 'Si activé, le classement sera affiché sur deux lignes avec 4 ou 5 classes chacune.';

$plang['pk_set_leaderboard_limit']        = 'Limitation de l\'affichage';
$plang['pk_help_leaderboard_limit']		  = 'Si un chiffre est saisi, les membres affichés dans le classement seront restreint à ce nombre. Le chiffre 0 ne représente aucune restriction.';

$plang['pk_set_leaderboard_zero']         = 'Cacher les membres n\'ayant pas de DKP';
$plang['pk_help_leaderboard_zero']        = 'Si activé, les joueurs avec n\'ayant pas de DKP ne seront pas affichés dans le classement';


$plang['pk_set_newsloot_limit']			  = 'Limite des nouveaux loot';
$plang['pk_help_newsloot_limit']          = 'Combien d\'articles doivent être affichés dans les médias? Cela restreint le nombre d\'articles qui sera affiché dans les médias. Le chiffre 0 ne représente aucune restriction.';

$plang['pk_set_itemstats_debug']          = 'Mode de débogage';
$plang['pk_help_itemstats_debug']					= 'Si activé, Itemstats va enregistrer toutes les transactions de / itemstats / includes_de / debug.txt. Ce fichier doit être en écriture, CHMOD 777 !!!';

$plang['pk_set_showclasscolumn']          = 'Afficher les colones de classe';
$plang['pk_help_showclasscolumn']		  = 'Si activé, une colonne supplémentaire est affichée indiquant la classe du joueur.' ;

$plang['pk_set_show_skill']				  = 'Afficher la colonne de compétences';
$plang['pk_help_show_skill']              = 'Si activé, une colonne supplémentaire est affichée montrant les compétences du joueur.';

$plang['pk_set_show_arkan_resi']          = 'Afficher la colone de résistance arcanes';
$plang['pk_help_show_arkan_resi']		  = 'Si activé, une colonne supplémentaire est affichée montrant la résistance arcane du joueur.';

$plang['pk_set_show_fire_resi']			  = 'Afficher la colone de résistance feu';
$plang['pk_help_show_fire_resi']          = 'Si activé, une colonne supplémentaire est affichée montrant la résistance feu du joueur.';

$plang['pk_set_show_nature_resi']		  = 'Afficher la colone de résistance nature';
$plang['pk_help_show_nature_resi']        = 'Si activé, une colonne supplémentaire est affichée montrant la résistance nature du joueur.';

$plang['pk_set_show_ice_resi']            = 'Afficher la colone de résistance givre';
$plang['pk_help_show_ice_resi']			  = 'Si activé, une colonne supplémentaire est affichée montrant la résistance Givre du joueur.';

$plang['pk_set_show_shadow_resi']		  = 'Afficher la colone de résistance ombre';
$plang['pk_help_show_shadow_resi']        = 'Si activé, une colonne supplémentaire est affichée montrant la résistance ombre du joueur.';

$plang['pk_set_show_profils']			  = 'Afficher la colonne du lien du profil.';
$plang['pk_help_show_profils']            = 'Si activé, une colonne supplémentaire est affichée montrant les liens pour le profil.';

$plang['pk_set_servername']               = 'Nom du serveur';
$plang['pk_help_servername']              = 'Mettre le nom du serveur';

$plang['pk_set_server_region']			  = 'Région';
$plang['pk_help_server_region']			  = 'Serveur US ou EU';


$plang['pk_help_default_multi']           = 'Choisissez la valeur par défaut pour le classement décroissant DKP';
$plang['pk_set_default_multi']            = 'Définir par défaut pour les classements';

$plang['pk_set_round_activate']           = 'Arrondir les DKP.';
$plang['pk_help_round_activate']          = 'Si activé, les points de DKP affichés sont arrondis. 125,00 = 125DKP';

$plang['pk_set_round_precision']          = 'Réglage de la décimale pour l\'arrondi.';
$plang['pk_help_round_precision']         = 'Régler la décimale pour arrondir les DKP. 0 = par défaut';

$plang['pk_is_set_prio']                  = 'Priorité de recherche des objets dans les bases de données';
$plang['pk_is_help_prio']                 = 'Définition de l\'ordre de priorité pour les recherches d\'objets dans les bases de données.';

$plang['pk_is_set_alla_lang']	          = 'Langue utilisée pour les items sur Allakhazam.';
$plang['pk_is_help_alla_lang']	          = 'Dans quelle langue doivent être affichés les items?';

$plang['pk_is_set_lang']		          = 'Langue par défaut des ID des objets.';
$plang['pk_is_help_lang']		          = 'Langue par défaut des ID des objets. Example : [item]17182[/item] choisira cette langue.';

$plang['pk_is_set_autosearch']            = 'Recherche immédiate';
$plang['pk_is_help_autosearch']           = 'Activé: Si l\'item n est pas dans le cache, récupérer automatiquement l\'information. Non activé: Récuperer les données de l\'item quand on clique dessus.';

$plang['pk_is_set_integration_mode']      = 'Mode d\'intégration';
$plang['pk_is_help_integration_mode']     = 'Normal: la numérisation du texte et la mise en bulle dans le code html. Texte: numérisation de texte et mettre en <script> tags.';

$plang['pk_is_set_tooltip_js']            = 'Voir le Tooltips';
$plang['pk_is_help_tooltip_js']           = 'Overlib: The normal Tooltip. Light: Light version, faster loading times.';

$plang['pk_is_set_patch_cache']           = 'Cache Path';
$plang['pk_is_help_patch_cache']          = 'Chemin d accès au cache item de l utilisateur , à partir de / itemstats /. Default =. / xml_cache /';

$plang['pk_is_set_patch_sockets']         = 'Chemin du repertoire des photos ';
$plang['pk_is_help_patch_sockets']        = 'Chemin vers les fichiers image des articles.';

$plang['pk_set_dkp_info']			  = 'Ne pas afficher les info DKP sur le menu principal.';
$plang['pk_help_dkp_info']			  = 'Si activer DKP infos ne seras pas afficher dans le menu principal';

$plang['pk_set_debug']			= 'Activer le mode de débogage (Debug)';
$plang['pk_set_debug_type']		= 'Mode';
$plang['pk_set_debug_type0']	= 'Débogage non autorisé (Debug=0)';
$plang['pk_set_debug_type1']	= 'Débogage simple (Debug=1)';
$plang['pk_set_debug_type2']	= 'Débogage avec requêtes SQL(Debug=2)';
$plang['pk_set_debug_type3']	= 'Débogage étendu (Debug=3)';
$plang['pk_help_debug']			= 'Si activé, Eqdkp-Plus sera exécuté en mode de débogage, en montrant plus d\'informations et de messages d\'erreur. Désactivez si les plugins stoppent avec des messages d\'erreurs SQL! 1 = temps de rendu, requete count, 2 = SQL sorties, 3 = Amélioration des messages d erreur.';

#RSS News
$plang['pk_set_Show_rss']			= 'Désactiver les nouvelles RSS';
$plang['pk_help_Show_rss']			= 'Si activé, les nouvelles RSS Eqdkp Plus du jeu ne seront pas affichées ';

$plang['pk_set_Show_rss_style']		= 'Position du Game-news';
$plang['pk_help_Show_rss_style']	= 'Positionnez le RSS-Game News. En Haut horizontalement, dans le menu vertical ou les deux?';

$plang['pk_set_Show_rss_lang']		= 'Langue par défaut pour les nouvelles RSS';
$plang['pk_help_Show_rss_lang']		= 'Dans quelle langue recevoir les nouvelles RSS?';

$plang['pk_set_Show_rss_lang_de']	= 'Allemand';
$plang['pk_set_Show_rss_lang_eng']	= 'Anglais';

$plang['pk_set_Show_rss_style_both'] = 'Les deux' ;
$plang['pk_set_Show_rss_style_v']	 = 'Menu vertical' ;
$plang['pk_set_Show_rss_style_h']	 = 'Haut horizontal' ;

$plang['pk_set_Show_rss_count']		= 'Compteur de nouvelles (0 ou "" pour toutes)';
$plang['pk_help_Show_rss_count']	= 'Combien de nouvelles doivent être affichées?';

$plang['pk_set_itemhistory_dia']	= 'Ne pas afficher le graphique'; # Ja negierte Abfrage
$plang['pk_help_itemhistory_dia']	= 'Si activé, Eqdkp-Plus ne montrera pas le graphique (à côté des objets).';

#Bridge
$plang['pk_set_bridge_help']				= 'Sur cet onglet, vous pouvez régler les paramètres pour qu\'un Content Management System (CMS) ou Forum puisse interagir avec Eqdkp-Plus. <br>
												Si vous choisissez l\'un des systèmes dans le menu déroulant, les membres enregistrés de votre CMS/Forum seront en mesure de se connecter à Eqdkp-Plus avec les mêmes droits que dans le CMS/Forum. <br>
												L\'accès n\'est autorisé que pour un seul groupe, ce qui signifie que vous devez créer un nouveau groupe dans votre CMS/Forum incluant tous les membres qui auront accès à Eqdkp-Plus.';
												
$plang['pk_set_bridge_activate']			= 'Activer le lien au CMS/Forum';
$plang['pk_help_bridge_activate']			= 'Lorsque le lien est activé, les utilisateurs du Forum ou CMS seront en mesure de se connecter à Eqdkp-Plus avec les mêmes pouvoirs que ceux définis dans le  CMS/Forum';

$plang['pk_set_bridge_dectivate_eq_reg']	= 'Désactiver l\'enregistrement à Eqdkp-Plus';
$plang['pk_help_bridge_dectivate_eq_reg']	= 'Quand activé, les nouveaux utilisateurs ne sont pas en mesure de s\'inscrire à Eqdkp-Plus. L\'enregistrement des nouveaux utilisateurs doit se faire via le CMS/Forum.';

$plang['pk_set_bridge_cms']					= 'Choix du CMS/Forum';
$plang['pk_help_bridge_cms']				= 'Quel CMS/Forum sera lié à Eqdkp-Plus ';

$plang['pk_set_bridge_acess']				= 'Est-ce que le CMS/Forum utilise une autre base de données que celle d\'Eqdkp-Plus?';
$plang['pk_help_bridge_acess']				= 'Si le CMS/Forum utilise une autre base de données, activez cette base de données en remplissant les champs ci-dessous';

$plang['pk_set_bridge_host']				= 'Nom de l\'hôte (ou addresse IP)';
$plang['pk_help_bridge_host']				= 'Nom de l\'hôte (ou addresse IP) sur lequel la base de données est hébergée';

$plang['pk_set_bridge_username']			= 'Utilisateur';
$plang['pk_help_bridge_username']			= 'Nom de l\'utilisateur pour se connecter à la base de données';

$plang['pk_set_bridge_password']			= 'Mot de passe';
$plang['pk_help_bridge_password']			= 'Mot de passe de l\'utilisateur pour se connecter à la base de données';

$plang['pk_set_bridge_database']			= 'Nom de la base de données';
$plang['pk_help_bridge_database']			= 'Nom de la base de données où se trouve le CMS/Forum';

$plang['pk_set_bridge_prefix']				= 'Préfixe des tables de l\'installation du CMS/Forum';
$plang['pk_help_bridge_prefix']				= 'Précisez le préfixe utilisé. Ex : phpbb_ ou vbb_ etc...';

$plang['pk_set_bridge_group']				= 'ID du groupe du CMS autorisé';
$plang['pk_help_bridge_group']				= 'Entrez ici l\'ID du groupe, dans le CMS, qui est autorisé à accéder à Eqdkp.';

$plang['pk_set_bridge_inline']				= 'Intégration d\'un forum dans EQDKP';
/* ** Commented - Line removed in german settings ** $plang['pk_help_bridge_inline']				= 'Lorsque vous entrez une URL ici, un lien sera affiché dans le menu, qui montre le site à l intérieur de la Eqdkp. Cela se fait avec une iframe dynamique . Le Eqdkp Plus n est pas responsable de l\'appereance et du comportement du site inclus dans l iframe';*/ 

$plang['pk_set_bridge_inline_url']			= 'URL du Forum';
$plang['pk_help_bridge_inline_url']			= 'URL du Forum';

$plang['pk_set_link_type_header']			= 'Style d\'affichage';
$plang['pk_set_link_type_help']				= '';
$plang['pk_set_link_type_iframe_help']		= 'Indique comment le lien doit être ouvert. (L\'intégration dynamique ne fonctionne qu\'avec les sites installés sur le même serveur)';
$plang['pk_set_link_type_self']				= 'Normal';
$plang['pk_set_link_type_link']				= 'Nouvelle fenêtre';
$plang['pk_set_link_type_iframe']			= 'Intégré';

#recruitment
$plang['pk_set_recruitment_tab']			= 'Recrutement';
$plang['pk_set_recruitment_header']			= 'Recrutement - Recherchez-vous de nouveaux membres ?';
$plang['pk_set_recruitment']				= 'Activer le recrutement';
$plang['pk_help_recruitment']				= 'Si activé, un encadré contenant les classes recherchées sera affiché au dessus des nouvelles.';
$plang['pk_recruitment_count']				= 'Nombre';
$plang['pk_set_recruitment_contact_type']	= 'URL';
$plang['pk_help_recruitment_contact_type']	= 'Si aucune URL n\'est entrée, vous serez redirigé vers le contact email';
$plang['ps_recruitment_spec']				= 'Spec';

#comments
$plang['pk_set_comments_disable']			= 'Désactiver les commentaires';
$plang['pk_hel_pcomments_disable']			= 'Désactiver les commentaires sur toutes les pages';

#Contact
$plang['pk_contact']						= 'Informations de contact';
$plang['pk_contact_name']					= 'Nom';
$plang['pk_contact_email']					= 'E-mail';
$plang['pk_contact_website']				= 'Site Web';
$plang['pk_contact_irc']					= 'Canal IRC';
$plang['pk_contact_admin_messenger']		= 'Nom Messenger  (Skype, ICQ)';
$plang['pk_contact_custominfos']			= 'Infos supplémentaires';
$plang['pk_contact_owner']					= 'Autres Infos:';

#Next_raids
$plang['pk_set_nextraids_deactive']			= 'Ne pas afficher les raids suivants';
$plang['pk_help_nextraids_deactive']		= 'Si active, les prochains raids ne seront pas dans le Menu';

$plang['pk_set_nextraids_limit']			= 'Limite d affichages des prochains raids';
$plang['pk_help_nextraids_limit']			= '';

$plang['pk_set_lastitems_deactive']			= 'Ne pas afficher les dernier items.';
$plang['pk_help_lastitems_deactive']		= 'Si activer les prochains items seront afficher dans le menu';

$plang['pk_set_lastitems_limit']			= 'Limite d affichage du dernier élément';
$plang['pk_help_lastitems_limit']			= 'Limite d affichage du dernier élément';

$plang['pk_is_help']						= ' <b>Important: Changements dans le comportement de Itemstats avec Eqdkp Plus 0.6.2.4!</b><br><br>
												Si, après une mise à jour, vos objets ne sont plus correctement affichés, modifiez l\'ordre de priorité des bases de données (Armory & WoWHead recommandé), <br>puis récupérez de nouveau les éléments en utilisant le lien "Update Itemstat" ci-dessous. <br>
												Le meilleur résultat sera obtenu avec le paramètre "WoWHead & Armory", puisque l\'armurie de Blizzard delivre des informations supplémentaires comme droprate,
												Mob et donjon de façon diminuée. <br><br>
												IMPORTANT: Si vous avez modifié l\'ordre de priorité des bases de données, vous devez vider le cache, car les objets existants risquent de ne pas s\'afficher correctement!!!<br>
												Pour mettre à jour la cache, cliquez sur le lien "Update Itemstat" ci-dessous, puis le bouton "Clear cache", puis "Update Itemtable". <br>';

$plang['pk_set_normal_leaderbaord']			= 'Voir le classement avec Slider';
$plang['pk_help_normal_leaderbaord']		= 'Si activer, Voir le classement avec Slider.';

$plang['pk_set_thirdColumn']				= 'Ne pas montrer la troisième colonne';
$plang['pk_help_thirdColumn']				= 'Ne pas montrer la troisième colonne';

#GetDKP
$plang['pk_getdkp_th']						= 'GetDKP configuration';

$plang['pk_set_getdkp_rp']					= 'Activer raidplan';
$plang['pk_help_getdkp_rp']					= 'Activer raidplan';

$plang['pk_set_getdkp_link']				= 'Afficher le lien getdkp dans le menu principal';
$plang['pk_help_getdkp_link']				= 'Afficher le lien getdkp dans le menu principal';

$plang['pk_set_getdkp_active']				= 'Désactiver getdkp.php';
$plang['pk_help_getdkp_active']				= 'Désactiver getdkp.php';

$plang['pk_set_getdkp_items']				= 'Annuler itemIDs';
$plang['pk_help_getdkp_items']				= 'Annuler itemIDs';

$plang['pk_set_recruit_embedded']			= 'Ouvrir le lien dans la même fenêtre';
$plang['pk_help_recruit_embedded']			= 'Si activé, le lien sera ouvert dans la même fenêtre';


$plang['pk_set_dis_3dmember']				= 'Désactiver l\'aperçu 3D pour les Membres';
$plang['pk_help_dis_3dmember']				= 'Désactiver l\'aperçu 3D pour les Membres';

$plang['pk_set_dis_3ditem']					= 'Désactiver l\'aperçu 3D pour les items';
$plang['pk_help_dis_3item']					= 'Désactiver l\'aperçu 3D pour les items';

$plang['pk_set_disregister']				= 'Désactiver l\'enregistrement des utilisateurs ';
$plang['pk_help_disregister']				= 'Désactiver l\'enregistrement des utilisateurs';

# Portal Manager
$plang['portalplugin_name']         = 'Module';
$plang['portalplugin_version']      = 'Version';
$plang['portalplugin_contact']      = 'Contact';
$plang['portalplugin_order']        = 'Tri';
$plang['portalplugin_orientation']  = 'Orientation';
$plang['portalplugin_enabled']      = 'Activer';
$plang['portalplugin_save']         = 'Sauver les changements';
$plang['portalplugin_management']   = 'Gérer les modules des portails';
$plang['portalplugin_right']        = 'Droite';
$plang['portalplugin_middle']       = 'Milieu';
$plang['portalplugin_left1']        = 'En haut a gauche du menu.';
$plang['portalplugin_left2']        = 'En bas a gauche du menu';
$plang['portalplugin_settings']     = 'Configuration';
$plang['portalplugin_winname']      = 'Configuration du module du portail';
$plang['portalplugin_edit']         = 'Editer';
$plang['portalplugin_save']         = 'Sauver';
$plang['portalplugin_rights']       = 'Visibilitée';
$plang['portal_rights0']            = 'Tous';
$plang['portal_rights1']            = 'Invités';
$plang['portal_rights2']            = 'Enregistré';
$plang['portal_collapsable']        = 'Collapsable';

$plang['pk_set_link_type_D_iframe']			= 'Intégré de façon dynamique';

$plang['pk_set_modelviewer_default']	= 'Aperçu 3D par défault';


 /* IMAGE RESIZE */
 // Lytebox settings
$plang['pk_air_img_resize_options'] 			= 'Configuration de Lytebox';
$plang['pk_air_img_resize_enable'] 				= 'Activer le redimensionnement de l\'image';
$plang['pk_air_max_post_img_resize_width'] 		= 'Largeur Maximum de l\'image';
$plang['pk_air_show_warning'] 					= 'Afficher un avertissement si l\'image a été redimensionnée';
$plang['pk_air_lytebox_theme'] 					= 'Thème de Lytebox';
$plang['pk_air_lytebox_theme_explain'] 			= 'Thèmes: gris (par défaut), rouge, vert, bleu, or';
$plang['pk_air_lytebox_auto_resize'] 			= 'Activer le redimensionnement automatique';
$plang['pk_air_lytebox_auto_resize_explain'] 	= 'Contrôles ou non si les images doivent être redimensionnées si elle sont plus grande que la dimensionsla fenêtre du navigateur ';
$plang['pk_air_lytebox_animation'] 				= 'Activer l\'animation au chargement de l\'image';
$plang['pk_air_lytebox_animation_explain'] 		= 'Contrôles ou non "animate" Lytebox, c est-à-dire la transition entre les images, de redimensionner, fondu in/out des effets, etc';
$plang['pk_air_lytebox_grey'] 					= 'Gris';
$plang['pk_air_lytebox_red'] 					= 'Rouge';
$plang['pk_air_lytebox_blue'] 					= 'Bleu';
$plang['pk_air_lytebox_green'] 					= 'Vert';
$plang['pk_air_lytebox_gold'] 					= 'Or';

$plang['pk_set_hide_shop'] = 'Cacher le lien de la boutique';
$plang['pk_help_hide_shop'] = 'Cache le lien de la boutique';

$plang['pk_set_rss_chekurl'] = 'Vérifier URL avant la mise à jour';
$plang['pk_help_rss_chekurl'] = 'Vérifie si oui ou non les RSS-Web sont contrôlées avant mise à jour.';

$plang['pk_set_features'] = 'Fonctions DKP'; 

$plang['pk_set_noDKP'] = 'Cacher les fonctions DKP';
$plang['pk_help_noDKP'] = 'Si activé,toutes les autres fonctions DKP sont désactivées et aucune information aux points DKP ne sera indiquéé. Ne s\'applique pas à la liste des raids et événements. ';

$plang['pk_set_noRoster'] = 'Cacher le roster';
$plang['pk_help_noRoster'] = 'Si activé, la page roster ne sera pas affichée dans le menu principal et l\'accès à cette page sera bloqué';

$plang['pk_set_noDKP'] = 'Voir le roster au lieu de l\'apercu des points DKP ';
$plang['pk_help_noDKP'] = 'Si activé, le roster sera affiché à la place des points de DKP';

$plang['pk_set_noRaids'] = 'Cacher les fonctions du raid';
$plang['pk_help_noRaids'] = 'Si activé, les fonctions du raids seront cachées. Ne s\'applique pas à l\'historique des événements';

$plang['pk_set_noEvents'] = 'Cacher les Evénements';
$plang['pk_help_noEvents'] = 'Si activé, toute les fonctions "Evénements" seront désactivées. IMPORTANT: Les "Evénements" sont nécessaires pour raidplaner!';

$plang['pk_set_noItemPrices'] = 'Cacher le Prix des objets';
$plang['pk_help_noItemPrices'] = 'Si activé, le lien vers la page du prix des objets sera désactivé et bloqué.';

$plang['pk_set_noItemHistoy'] = 'Cacher l\'historique des objets';
$plang['pk_help_noItemHistoy'] = 'Si activé, le lien vers la page d\'historique des objets sera désactivé et bloqué.';

$plang['pk_set_noStats'] = 'Masquer les résumés et statistiques.';
$plang['pk_help_noStats'] = 'Si activé, le lien vers la page de statistiques et de résumés sera caché et bloqué.';

$plang['pk_set_cms_register_url'] = 'Lien d\'enregistrement sur le CMS/Forum';
$plang['pk_help_cms_register_url'] = 'Lien vers le CMS/Forum vous permettant de vous y enregistrer';

$plang['pk_disclaimer'] = 'Déni/Avertissement';

$plang['pk_set_link_type_menu']			= 'Menu';
$plang['pk_set_link_type_menuH']		= 'Tabulations';

//SMS gedöns
$plang['pk_set_sms_header']			= 'Paramèttres SMS ';
$plang['pk_set_sms_info']			= 'Seul les administrateurs peuvent envoyer des SMS';
$plang['pk_set_sms_info_temp']		= 'Vous devez être connecté pour envoyer des SMS. <br>Acheter ici:<br>' ;
$plang['pk_set_sms_username']		= 'Utilisateur';
$plang['pk_set_sms_pass']			= 'Mot de passe';
$plang['pk_set_sms_amount']			= 'Envoyer SMS';
$plang['pk_set_sms_deactivate']		= 'Désactiver la fonction SMS';

$plang['pk_faction']		= 'Faction';

// Libraries Tab
$plang['pk_set_sms_tab']	= 'SMS';
$plang['pk_set_getdkp_tab']	= 'GetDKP';
$plang['pk_set_cmsbridge_tab']	= 'CMS-Bridge';
$plang['pk_set_libraries_tab']	= 'Librairies';
$plang['pk_set_news_tab']	= 'News';
$plang['pk_set_rss_tab']	= 'RSS';
$plang['pk_set_rss_tab_head']	= 'RSS News';
$plang['pk_set_global_tab_head']	= 'Global';
$plang['pk_set_eqdkp_tab_head']	= 'EQdkp';
$plang['pk_set_multidkp_tab_head']	= 'MultiDKP';
$plang['pk_set_links_tab_head']	= 'Liens';
$plang['pk_set_leaderboard_tab_head']	= 'Classement';
$plang['pk_set_listmembers_tab_head']	= 'Liste des membres';
$plang['pk_set_cmplugin_tab_head']	= 'Plugin Charmanager';
$plang['pk_set_itemstats_tab_head']	= 'Itemstats';
$plang['pk_set_updates_tab_head']	= 'Update';
$plang['pk_set_bridgeconfig_tab_head']	= 'Bridge Config';
$plang['pk_set_email_header'] = "E-Mail";
$plang['pk_set_recaptcha_header'] = "ReCaptcha";

$plang['lib_email_sender_email'] = 'Message de (Addresse)';
$plang['lib_email_sender_name'] = 'Nom';
$plang['lib_email_sendmail_path'] = 'Emplacement de Sendmail';
$plang['lib_email_method'] = 'Méthode d\'envoi';
$plang['lib_email_mail'] = 'Fonction PHP-Mail';
$plang['lib_email_sendmail'] = 'Sendmail';
$plang['lib_email_smtp'] = 'Serveur SMTP';
$plang['lib_email_settings'] = 'Paramètres de la méthode d\'envoi';
$plang['lib_email_smtp_user'] = 'Utilisateur SMTP';
$plang['lib_email_smtp_password'] = 'Mot de passe SMTP';
$plang['lib_email_smtp_host'] = 'Hôte SMTP';
$plang['lib_email_smtp_auth'] = 'Activer l\'authentification SMTP';

$plang['lib_recaptcha_okey'] = 'Clé publique de reCATPCHA';
$plang['lib_recaptcha_okey_help']	= 'Entrez ici la clé publique de votre compte sur reCAPTCHA.net.';
$plang['lib_recaptcha_pkey'] = 'Clé privée de reCATPCHA';
$plang['lib_recaptcha_pkey_help']	= 'Entrez ici la clé privée de votre compte sur reCAPTCHA.net.';

$plang['pk_itemstats_max_execution_time'] = 'seconds ItemStats max execution time';
$plang['pk_itemstats_max_execution_time_explain'] = 'Set a maximum execution time for your ItemStats to prevent fatal errors caused by exceeding the php maximum execution time and/or to bound your page loading times. Items which should have been decorated after this time will be shown as plaintext. A value of 0 will fallback to 80% of your php maximum execution time.';

$plang['pk_externals_tab']	= 'Export';
$plang['pk_externals_th']	= 'Export Settings';
$plang['pk_externals_news']	= 'disable news export';
$plang['pk_externals_items']	= 'disable items export';
$plang['pk_externals_raids']	= 'disable raids export';
$plang['pk_externals_members']	= 'disable member export';
/*
$plang['pk_set_']	= '';
$plang['pk_help_']	= '';
*/
?>
