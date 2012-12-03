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
$plang['pk_settings']					= 'Parametre';
$plang['pk_date_settings']				= 'd.m.y';

//---- Javascript stuff ----
$plang['pk_plus_about']					= 'A Propos de EQDKP PLUS';
$plang['updates']						= 'Mises à jour disponibles';
$plang['loading']						= 'Chargement...';
$plang['pk_config_header']				= 'EQDKP PLUS Parametre';
$plang['pk_close_jswin1']      			= 'Fermer le';
$plang['pk_close_jswin2']     			= 'La fenêtre avant de l ouvrir à nouveau!';
$plang['pk_help_header']				= 'Aide';
$plang['pk_plus_comments']  			= 'Commentaire';

//---- Updater Stuff ----
$plang['pk_alt_attention']				= 'Attention';
$plang['pk_alt_ok']						= 'Tout est OK!';
$plang['pk_updates_avail']				= 'Mises à jour disponibles';
$plang['pk_updates_navail']				= 'Mises à jour non disponibles';
$plang['pk_no_updates']					= 'Your Versions are all up to date. There are no newer Versions available.';
$plang['pk_act_version']				= 'Nouvelle Version';
$plang['pk_inst_version']				= 'Installation';
$plang['pk_changelog']					= 'Texte de Changement';
$plang['pk_download']					= 'Téléchargement';
$plang['pk_upd_information']			= 'Information';
$plang['pk_enabled']					= 'Autorisé';
$plang['pk_disabled']					= 'non autorisé';
$plang['pk_auto_updates1']				= 'L avertissement de mise à jour est automatique';
$plang['pk_auto_updates2']				= 'Si vous désactivez ce paramètre, s il vous plaît vérifiez régulièrement les mises à jour pour empêcher hacks et rester à jour ';
$plang['pk_module_name']				= 'Nom de Module';
$plang['pk_plugin_level']				= 'Level';
$plang['pk_release_date']				= 'Version';
$plang['pk_alt_error']					= 'Erreur';
$plang['pk_no_conn_header']				= 'Errueur de Connection';
$plang['pk_no_server_conn']				= 'Une erreur s est produite lors de la tentative de contact avec le serveur de mise à jour, soit
															votre hôte ne permet pas les connexions sortantes ou l erreur a été causée
															par un problème de réseau. S il vous plaît visitez le forum eqdkp pour
															que vous utilisez la dernière version.';
$plang['pk_reset_warning']				= 'Attention remise a zéro';

//---- Update Levels ----
$plang['pk_level_other']				= 'Autre';
$updatelevel = array (
	'Bugfix'							=> 'Bugfix',
	'Feature Release'					=> 'Texte de mise a jour',
	'Security Update'					=> 'Mise à jour de sécurité',
	'New version'						=> 'Nouvelle version',
	'Release Candidate'					=> 'Release Candidate',
	'Public Beta'						=> 'Public Beta',
	'Closed Beta'						=> 'Fermer la Beta',
	'Alpha'								=> 'Alpha',
);

//---- About ----
$plang['pk_version']					= 'Version';
$plang['pk_prodcutname']				= 'Produit';
$plang['pk_modification']				= 'Mod';
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
$plang['pk_credits']					= 'Credits';
$plang['pk_sponsors']					= 'Donators';
$plang['pk_plugins']					= 'PlugIns';
$plang['pk_modifications']				= 'Mods';
$plang['pk_themes']						= 'Styles';
$plang['pk_additions']					= 'Code Additions';
$plang['pk_tab_stuff']					= 'L equipe EQDKP';
$plang['pk_tab_help']					= 'Aide';
$plang['pk_tab_tech']					= 'Technique';

//---- Settings ----
$plang['pk_save']						= 'Sauvegarde';
$plang['pk_save_title']					= 'Parametre de sauvegarde';
$plang['pk_succ_saved']					= 'Les parametrre ont été correctement sauvegardées';
 // Tabs
$plang['pk_tab_global']					= 'Global';
$plang['pk_tab_multidkp']				= 'MultiDKP';
$plang['pk_tab_links']					= 'Liens';
$plang['pk_tab_bosscount']				= 'BossCounter';
$plang['pk_tab_listmemb']				= 'Liste de membres';
$plang['pk_tab_itemstats']				= 'Itemstats';
// Global
$plang['pk_set_QuickDKP']				= 'Afficher QuickDKP';
$plang['pk_set_Bossloot']				= 'Afficher bossloot ?';
$plang['pk_set_ClassColor']				= 'Colorier le noms des classes';
$plang['pk_set_Updatecheck']			= 'Activer la vérification des mises à jour';
$plang['pk_window_time1']				= 'Afficher la fenêtre toutes les';
$plang['pk_window_time2']				= 'Minutes';
// MultiDKP
$plang['pk_set_multidkp']				= 'Activer MultiDKP';
// Listmembers
$plang['pk_set_leaderboard']			= 'Afficher le classement';
$plang['pk_set_lb_solo']				= 'Afficher le classement par compte';
$plang['pk_set_rank']					= 'Afficher les Rangs';
$plang['pk_set_rank_icon']				= 'Afficher l icone des rangs';
$plang['pk_set_level']					= 'Afficher le Level';
$plang['pk_set_lastloot']				= 'Afficher le Dernier Loot';
$plang['pk_set_lastraid']				= 'Afficher le Dernier Raid';
$plang['pk_set_attendance30']			= 'Afficher l instance des raids des 30 dernier jours';
$plang['pk_set_attendance60']			= 'Afficher l instance des raids des 60 dernier jours';
$plang['pk_set_attendance90']			= 'Afficher l instance des raids des 90 dernier jours';
$plang['pk_set_attendanceAll']			= 'Voir la participation de tout les raids';
// Links
$plang['pk_set_links']					= 'Autoriser les liens';
$plang['pk_set_linkurl']				= 'Liens URL';
$plang['pk_set_linkname']				= 'Liens des Noms';
$plang['pk_set_newwindow']				= 'Ouvrir dans une nouvelle fenetre ?';
// BossCounter
$plang['pk_set_bosscounter']			= 'Afficher le Bosscounter';
//Itemstats
$plang['pk_set_itemstats']				= 'Afficher Itemstats';
$plang['pk_is_language']				= 'Itemstats language';
$plang['pk_german']						= 'German';
$plang['pk_english']					= 'Englais';
$plang['pk_french']						= 'Francais';
$plang['pk_set_icon_ext']				= '';
$plang['pk_set_icon_loc']				= '';
$plang['pk_set_en_de']					= 'Traduire les items de anglais vers allemand';
$plang['pk_set_de_en']					= 'Traduire les items de allemand vers anglais';

################
# new sort
###############

//MultiDKP
//

$plang['pk_set_multi_Tooltip']					= 'Afficher le tooltip de DKP';
$plang['pk_set_multi_smartTooltip']			 	= 'tooltip intelligent';

//Help
$plang['pk_help_colorclassnames']				= "Si activé, les joueurs seront présentés à la couleurs de WOW leur catégorie et leur icône de classe .";
$plang['pk_help_quickdkp']						= "Voir l'utilisateur connecté sur tous les points qui sont les membres qui lui sont assignées dans le menu ci-dessus";
$plang['pk_help_boosloot']						= "Si activé, vous pouvez cliquer sur les noms des boss de raid dans la note et le bosscounter de disposer d'un aperçu détaillé des éléments déposés. Si inactive, il sera lié à Blasc.de (activer seulement si vous entrez dans un raid pour chaque boss)";
$plang['pk_help_autowarning']             		= "Mets en garde l'administrateur quand il se connecte, si des mises à jour sont disponibles.";
$plang['pk_help_warningtime']             		= "Combien de fois la mise en garde doit apparaître?";
$plang['pk_help_multidkp']						= "MultiDKP permet la gestion et la présentation de comptes séparés. Active la gestion et l'aperçu des comptes MultiDKP.";
$plang['pk_help_dkptooltip']					= "Si activé, une info-bulle contenant des informations détaillées sur le calcul des points sera montré, lorsque la souris glisse sur les différents points.";
$plang['pk_help_smarttooltip']					= "Raccourcissement de l'ensemble des bulles d'aide (si vous avez activer plus de trois événements par compte)";
$plang['pk_help_links']                  		= "Dans ce menu, vous êtes en mesure de définir les différents liens, qui seront affiché dans le menu principal.";
$plang['pk_help_bosscounter']             		= "Si activé, un tableau sera affiché sous le menu principal avec le bosskills. L'administration est gérée par le plugin Bossprogress";
$plang['pk_help_lm_leaderboard']				= "Si activé, un classement sera affiché au-dessus de la table des scores. Un leaderboard est un tableau, où le DKP de chaque classe est affichée triée dans l'ordre décroissant.";
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
$plang['pk_help_itemstats_search']				= "Quelle base de données devrait utiliser Itemstats en premier lieu à rechercher l'information? Blasc ou Allakhazam?";
$plang['pk_help_itemstats_icon_ext']			= "Extension de fichier des images a affichées. Habituellement. Png ou. Jpg.";
$plang['pk_help_itemstats_icon_url']    		= "S'il vous plaît entrez l'URL où les images d'Itemstats sont situés. Allemand: http://www.buffed.de/images/wow/32/ en 32x32 ou 64x64 pixels.Anglais dans http://www.buffed.de/images/wow/64/ à Allakzam: http://www.buffed.de/images/wow/32 /> permuter";
$plang['pk_help_itemstats_translate_deeng']		= "Si active, l'information des bulles d'aide vous sera demandé en allemand, même lorsque la question est entré en anglais.";
$plang['pk_help_itemstats_translate_engde']		= "Si active, l'information des bulles d'aide vous sera demandé, en anglais, même si la question est entré en allemand.";

$plang['pk_set_leaderboard_2row']		  = 'Classement a 2 lignes';
$plang['pk_help_leaderboard_2row']        = 'Si active, le classement sera affiché sur deux lignes avec 4 ou 5 classes chacune.';

$plang['pk_set_leaderboard_limit']        = 'Limite de l affichage';
$plang['pk_help_leaderboard_limit']		  = 'Si un nombre numérique est en cours de saisie, le classement sera limité à l entrée de nombre de membres. Le chiffre 0 ne représente aucune restriction.';

$plang['pk_set_leaderboard_zero']         = 'Cacher les membres zero DKP';
$plang['pk_help_leaderboard_zero']        = 'Si activé, les joueurs avec zéro DKP ne seront pas afficher dans le classement';


$plang['pk_set_newsloot_limit']			  = 'Limite des nouveaux loot';
$plang['pk_help_newsloot_limit']          = 'Combien d articles doivent être affichés dans les médias? Cela restreint l affichage d articles, qui sera affiché dans les médias. Le chiffre 0 représente aucune restriction.';

$plang['pk_set_itemstats_debug']          = 'Debug Mod';
$plang['pk_help_itemstats_debug']					= '
Si activé, Itemstats va enregistrer toutes les transactions de / itemstats / includes_de / debug.txt. Ce fichier doit être en écriture, CHMOD 777 !!!';

$plang['pk_set_showclasscolumn']          = 'Afficher les colones de classe';
$plang['pk_help_showclasscolumn']		  = 'Si activé, une colonne supplémentaire est affiché indiquant la classe du joueur.' ;

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
$plang['pk_help_server_region']			  = 'US or EU serveur.';


$plang['pk_help_default_multi']           = 'Choisissez la valeur par défaut pour le classement décroissantDKP';
$plang['pk_set_default_multi']            = 'Définir par défaut pour les classements';

$plang['pk_set_round_activate']           = 'Round DKP.';
$plang['pk_help_round_activate']          = 'Si activé, les point de DKP affichés sont arrondis. 125,00 = 125DKP';

$plang['pk_set_round_precision']          = 'Placer une décimal par round.';
$plang['pk_help_round_precision']         = 'Régler la décimale à arrondir les DKP. 0 = par défaut';

$plang['pk_is_set_prio']                  = 'Prioritée de Itemdatabase';
$plang['pk_is_help_prio']                 = 'Régler l ordre des bases de données.';

$plang['pk_is_set_alla_lang']	            = 'Language du noms des items sur Allakhazam.';
$plang['pk_is_help_alla_lang']	          = 'Dans quelle langue les items requis doivent être?';

$plang['pk_is_set_lang']		              = 'Language Standard des Item ID\'s.';
$plang['pk_is_help_lang']		              = 'Language Standard des Item ID. Example : [item]17182[/item] Choisissez le language.';

$plang['pk_is_set_autosearch']            = 'Recherche immédiate';
$plang['pk_is_help_autosearch']           = 'Activer:Si l élément n est pas dans le cache, la recherche de la question des informations automatiquement. Non activé: Point d information sur la récupération n est que de cliquer sur le point des informations.';

$plang['pk_is_set_integration_mode']      = 'Integration Modus';
$plang['pk_is_help_integration_mode']     = 'Normal: la numérisation du texte et la mise en bulle dans le code html. Texte: numérisation de texte et mettre en <script> tags.';

$plang['pk_is_set_tooltip_js']            = 'Voir le Tooltips';
$plang['pk_is_help_tooltip_js']           = 'Overlib: The normal Tooltip. Light: Light version, faster loading times.';

$plang['pk_is_set_patch_cache']           = 'Cache Path';
$plang['pk_is_help_patch_cache']          = 'Chemin d accès au cache item de l utilisateur , à partir de / itemstats /. Default =. / xml_cache /';

$plang['pk_is_set_patch_sockets']         = 'Chemin du repertoire des photos ';
$plang['pk_is_help_patch_sockets']        = 'Chemin vers les fichiers image des articles.';

$plang['pk_set_dkp_info']			  = 'Ne pas afficher les info DKP sur le menu principal.';
$plang['pk_help_dkp_info']			  = 'Si activer DKP infos ne seras pas afficher dans le menu principal';

$plang['pk_set_debug']			= 'Activer Eqdkp Debug Modus';
$plang['pk_set_debug_type']		= 'Mode';
$plang['pk_set_debug_type0']	= 'Debug non autoriser (Debug=0)';
$plang['pk_set_debug_type1']	= 'Déboguage simple (Debug=1)';
$plang['pk_set_debug_type2']	= 'Débogage avec Requêtes SQL(Debug=2)';
$plang['pk_set_debug_type3']	= 'Débogage étendus (Debug=3)';
$plang['pk_help_debug']			= 'Si activé, Eqdkp Plus sera exécuté en mode de débogage, en montrant plus d informations et de messages d erreur. Désactivé si plugins avorter avec des messages d erreur SQL! 1 = temps de rendu, requete count, 2 = SQL sorties, 3 = Amélioration des messages d erreur.';

#RSS News
$plang['pk_set_Show_rss']			= 'Désactiver RSS News';
$plang['pk_help_Show_rss']			= 'Si activé, les nouvelles RSS Eqdkp Plus du jeux ne seront pas affichées ';

$plang['pk_set_Show_rss_style']		= 'Game-news positioning';
$plang['pk_help_Show_rss_style']	= 'RSS-Game News positioning. En Haut horizontalement, dans le menu vertical ou les deux?';

$plang['pk_set_Show_rss_lang']		= 'RSS-News language par défaut';
$plang['pk_help_Show_rss_lang']		= 'Recevez les nouvelles RSS en quelle langue? (atm allemand seulement). English news disponibles depuis de 2009.';

$plang['pk_set_Show_rss_lang_de']	= 'Allemand';
$plang['pk_set_Show_rss_lang_eng']	= 'Anglais';

$plang['pk_set_Show_rss_style_both'] = 'Les deux' ;
$plang['pk_set_Show_rss_style_v']	 = 'Menu verticale' ;
$plang['pk_set_Show_rss_style_h']	 = 'Haut horizontale' ;

$plang['pk_set_Show_rss_count']		= 'Nouveau compteurs (0 or "" for all)';
$plang['pk_help_Show_rss_count']	= 'Combien de nouvelles doivent être affichées?';

$plang['pk_set_itemhistory_dia']	= 'Ne pas affiché le diagrames'; # Ja negierte Abfrage
$plang['pk_help_itemhistory_dia']	= 'Si activer, Eqdkp Plus ne montreras pas le diagrames.';

#Bridge
$plang['pk_set_bridge_help']				= 'Sur cet onglet, vous pouvez régler les paramètres de laisser un système de gestion de contenu (CMS) d interagir avec Eqdkp Plus.
												Si vous choisissez l un des systèmes dans le champ du menu déroulant, inscription des membres de votre Forum / CMS sera en mesure de vous connecter en Eqdkp plus avec les mêmes références dans Forum / CMS.
												L accès n est autorisé que pour un seul groupe, ce qui signifie que vous devez créer un nouveau groupe dans votre CMS / Forum, qui appartiennent tous les membres, qui aura accès Eqdkp.';

$plang['pk_set_bridge_activate']			= 'Activate liens au CMS';
$plang['pk_help_bridge_activate']			= 'Lorsque pont est activé, les utilisateurs du forum ou CMS sera en mesure de connecter en Eqdkp plus avec les mêmes pouvoirs tel que il est utilisé dans le CMS / Forum';

$plang['pk_set_bridge_dectivate_eq_reg']	= 'Désactiver la registration a Eqdkp Plus';
$plang['pk_help_bridge_dectivate_eq_reg']	= 'Lorsqu il est activé de nouveaux utilisateurs ne sont pas en mesure de s inscrire au Eqdkp Plus. L enregistrement de nouveaux utilisateurs doivent être fait au CMS / Forum.';

$plang['pk_set_bridge_cms']					= 'Supporte CMS';
$plang['pk_help_bridge_cms']				= 'Which CMS shall be bridged ';

$plang['pk_set_bridge_acess']				= 'Est-ce que le CMS / Forum sur une autre base de données que Eqdkp?';
$plang['pk_help_bridge_acess']				= 'Si vous utilisez le CMS / Forum sur l autre activer cette base de données et remplir les champs ci-dessous';

$plang['pk_set_bridge_host']				= 'Hostname';
$plang['pk_help_bridge_host']				= 'The Hostname or IP sur lequel le serveur de base de données écoute iss';

$plang['pk_set_bridge_username']			= 'Database User';
$plang['pk_help_bridge_username']			= 'Username utiliseé pour se connecter a la Database';

$plang['pk_set_bridge_password']			= 'Database Userpassword';
$plang['pk_help_bridge_password']			= 'Mot de passe de l utilisateur pour se connecter a la database';

$plang['pk_set_bridge_database']			= 'Database Name';
$plang['pk_help_bridge_database']			= 'Nom de la database ou se trouve le CMS';

$plang['pk_set_bridge_prefix']				= 'Tableprefix De l installation du CMS';
$plang['pk_help_bridge_prefix']				= 'Give your Prefix of your CMS . e.g.. phpbb_ or wcf1_';

$plang['pk_set_bridge_group']				= 'Group ID of the CMS Group';
$plang['pk_help_bridge_group']				= 'Entrez ici l ID du groupe, dans le CMS, qui est autorisé à accéder à Eqdkp.';

$plang['pk_set_bridge_inline']				= 'Forum Integration EQDKP - BETA !';
$plang['pk_help_bridge_inline']				= 'Lorsque vous entrez une URL ici, un lien sera affiché dans le menu, qui montre le site à l intérieur de la Eqdkp. Cela se fait avec une dynamique iframe. Le Eqdkp Plus n est pas responsable de la appereance et le comportement du site inclus dans l iframe';

$plang['pk_set_bridge_inline_url']			= 'URL du Forum';
$plang['pk_help_bridge_inline_url']			= 'URL du Forum';

$plang['pk_set_link_type_header']			= 'Afficher le style';
$plang['pk_set_link_type_help']				= '';
$plang['pk_set_link_type_iframe_help']		= 'Comment le lien doit être ouvert. Embedded dynamique ne fonctionne que avec les sites installés sur le même serveur';
$plang['pk_set_link_type_self']				= 'Normal';
$plang['pk_set_link_type_link']				= 'Nouvelle fenetre';
$plang['pk_set_link_type_iframe']			= 'Embedded';

#recruitment
$plang['pk_set_recruitment_tab']			= 'Recrutement';
$plang['pk_set_recruitment_header']			= 'Recrutement - Vous pouvez voir les nouveaux membres ?';
$plang['pk_set_recruitment']				= 'Activer le recrutement';
$plang['pk_help_recruitment']				= 'Si activer, une boîte avec le besoin de classes sont afficher au sommet de la page.';
$plang['pk_recruitment_count']				= 'Nombre';
$plang['pk_set_recruitment_contact_type']	= 'Liens URL';
$plang['pk_help_recruitment_contact_type']	= 'Si aucune URL est donnée, il y auras un lien vers le contact email';
$plang['ps_recruitment_spec']				= 'Spec';

#comments
$plang['pk_set_comments_disable']			= 'Désactiver les commentaires';
$plang['pk_hel_pcomments_disable']			= 'Désactiver les commentaires sur toutes les pages';

#Contact
$plang['pk_contact']						= 'Contact infos';
$plang['pk_contact_name']					= 'Noms';
$plang['pk_contact_email']					= 'Email';
$plang['pk_contact_website']				= 'SiteWeb';
$plang['pk_contact_irc']					= 'IRC Channel';
$plang['pk_contact_admin_messenger']		= 'Nom Messenger  (Skype, ICQ)';
$plang['pk_contact_custominfos']			= 'Infos supplémentaire';
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

$plang['pk_is_help']						= ' Important: Changement de Itemstats maniérisme avec EQdkp-Plus 0.6.2.4 <br>
												Si vos articles ne doivent pas être affichés correctement plus après une mise à jour, établir de nouvelles de la "priorité du point de base de données" (nous vous recommandons Armory & WoWHead)
												Et de récupérer des points à nouveau.
												<br> la "Mise à jour Itemstat Link" au-dessous de ce message. <br>
												Le meilleur résultat sera obtenu avec le paramètre "& WoWHead Armory", puisque seuls les Blizzards Armory delievers des informations supplémentaires comme droprate,
												Mob et donjon par point diminué.
												Afin de rafraîchir la mémoire cache point suivre le lien, puis choisissez "Vider le cache" et "Mise à jour Itemtable" après cela. <br>
												Imortant: Si vous avez changé la base de données web, vous devez vider le cache, si vous n \ est un élément existant des bulles d aide ne seront pas affichées correctement. <br>';

$plang['pk_set_normal_leaderbaord']			= 'Voir le classement avec Slider';
$plang['pk_help_normal_leaderbaord']		= 'Si activer, Voir le classement avec Slider.';

$plang['pk_set_thirdColumn']				= 'Ne pas montrer la troisième colonne';
$plang['pk_help_thirdColumn']				= 'Ne pas montrer la troisième colonne';

#GetDKP
$plang['pk_getdkp_th']						= 'GetDKP configuration';

$plang['pk_set_getdkp_rp']					= 'Activer raidplan';
$plang['pk_help_getdkp_rp']					= 'Activer raidplan';

$plang['pk_set_getdkp_link']				= 'Afficher le liens getdkp dans le menu principal';
$plang['pk_help_getdkp_link']				= 'Afficher le liens getdkp dans le menu principal';

$plang['pk_set_getdkp_active']				= 'Désactiver getdkp.php';
$plang['pk_help_getdkp_active']				= 'Désactiver getdkp.php';

$plang['pk_set_getdkp_items']				= 'Annuler itemIDs';
$plang['pk_help_getdkp_items']				= 'Annuler itemIDs';

$plang['pk_set_recruit_embedded']			= 'Ouvrir le lien embedded';
$plang['pk_help_recruit_embedded']			= 'Si activer, le lien seras ouvert embedded i an iframe';


$plang['pk_set_dis_3dmember']				= 'Désactiver 3D Modelviewer pour les Membres';
$plang['pk_help_dis_3dmember']				= 'Désactiver 3D Modelviewer pour les Membres';

$plang['pk_set_dis_3ditem']					= 'Désactiver 3D Modelviewer pour les items';
$plang['pk_help_dis_3item']					= 'Désactiver 3D Modelviewer pour les items';

$plang['pk_set_disregister']				= 'Désactiver la registration des utilisateurs ';
$plang['pk_help_disregister']				= 'Désactiver la registration des utilisateurs';

# Portal Manager
$plang['portalplugin_name']         = 'Modul';
$plang['portalplugin_version']      = 'Verson';
$plang['portalplugin_contact']      = 'Contact';
$plang['portalplugin_order']        = 'Sorting';
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

$plang['pk_set_link_type_D_iframe']			= 'Embedded dynamic high';

$plang['pk_set_modelviewer_default']	= 'Default du Modelviewer';


 /* IMAGE RESIZE */
 // Lytebox settings
 $plang['pk_air_img_resize_options'] = 'Lytebox Configuration';
 $plang['pk_air_img_resize_enable'] = 'Autoriser le redimensionnement de l image';
 $plang['pk_air_max_post_img_resize_width'] = 'Largeur Maximum de l image';
 $plang['pk_air_show_warning'] = 'Activer Attention, si l image a été refaite';
 $plang['pk_air_lytebox_theme'] = 'Lytebox\'s theme';
 $plang['pk_air_lytebox_theme_explain'] = 'Thèmes: gris (par défaut), rouge, vert, bleu, or';
 $plang['pk_air_lytebox_auto_resize'] = 'Activer le rezize automatique';
 $plang['pk_air_lytebox_auto_resize_explain'] = 'Contrôles ou non si les images doivent être redimensionnées si elle sont plus grande que la dimensionsla fenêtre du navigateur ';
 $plang['pk_air_lytebox_animation'] = 'Autoriser animation';
 $plang['pk_air_lytebox_animation_explain'] = 'Contrôles ou non "animate" Lytebox, c est-à-dire la transition entre les images, de redimensionner, fade in / out des effets, etc';
 $plang['pk_air_lytebox_grey'] = 'Gris';
 $plang['pk_air_lytebox_red'] = 'Rouge';
 $plang['pk_air_lytebox_blue'] = 'Bleue';
 $plang['pk_air_lytebox_green'] = 'Vert';
 $plang['pk_air_lytebox_gold'] = 'Or';

 $plang['pk_set_hide_shop'] = 'Cacher les liens d achats';
 $plang['pk_help_hide_shop'] = 'Cacher les liens d achats';

$plang['pk_set_rss_chekurl'] = 'vérifier RSS URL bevor mise à jour';
 $plang['pk_help_rss_chekurl'] = 'Contrôles de savoir si ou non les RSS-Web sont contrôlés avant mise à jour.';

$plang['pk_set_noDKP'] = 'Cacher les fonctions DKP';
$plang['pk_help_noDKP'] = 'Si activer,toutes les autres fonctions DKP sont désactivées et aucun avis de DKP-points seront indiqués. Ne s appliquent pas à des raids et la liste. ';

$plang['pk_set_noRoster'] = 'Cacher Roster';
$plang['pk_help_noRoster'] = 'Si activer l acces a la page seras bloquer et le menu du roster ne seras pas afficher';

$plang['pk_set_noDKP'] = 'Voir le roster au lieu de l apercu DKP-point ';
$plang['pk_help_noDKP'] = 'Si activer le roster membre seras afficher a la place des points de DKP';

$plang['pk_set_noRaids'] = 'Cacher les fonctions du raid';
$plang['pk_help_noRaids'] = 'Si activer les raids seront cacher,ne s applique pas aux events';

$plang['pk_set_noEvents'] = 'Cacher les Events';
$plang['pk_help_noEvents'] = 'Si activer, toute les fonctions "Events" seront désactiver. IMPORTANT: Les "Events" sont nécessaires pour la raidplaner!';

$plang['pk_set_noItemPrices'] = 'Cacher le Prix des Items';
$plang['pk_help_noItemPrices'] = 'Si activer, Le liens du prix des items seront désactiver et cacher.';

$plang['pk_set_noItemHistoy'] = 'Cacher l historique des items';
$plang['pk_help_noItemHistoy'] = 'Si activer, Les pages de liens d historique des items seront désactiver et bloquer.';

$plang['pk_set_noStats'] = 'Masquer les résumé et statistique.';
$plang['pk_help_noStats'] = 'Si activer, Le liens de pages de statisqtique et de resumé seront cacher et bloqué.';

$plang['pk_set_cms_register_url'] = 'CMS/Forums enregistrement URL';
$plang['pk_help_cms_register_url'] = 'Le pont de l actif d enregistrement eqDKP lien avec impatience cette URL aux fins de l enregistrement';

$plang['pk_disclaimer'] = 'Disclaimer';

$plang['pk_set_link_type_menu']			= 'Menu';
$plang['pk_set_link_type_menuH']		= 'Table des Menu';

//SMS gedöns
$plang['pk_set_sms_header']			= 'SMS Settings';
$plang['pk_set_sms_info']			= 'Only Admins can send SMS';
$plang['pk_set_sms_info_temp']		= 'You need Login informations to send Messages. <br>buy here:<br>' ;
$plang['pk_set_sms_username']		= 'Username';
$plang['pk_set_sms_pass']			= 'password';
$plang['pk_set_sms_amount']			= 'Send SMS';
$plang['pk_set_sms_deactivate']		= 'Turn off SMS Feature';

$plang['pk_faction']		= 'Faction';

/*
$plang['pk_set_']	= '';
$plang['pk_help_']	= '';
*/
?>
