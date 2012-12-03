<?php
 /*
 * Project:     EQdkp-Plus
 * License:     Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:       2006
 * Date:        $Date:  $
 * -----------------------------------------------------------------------
 * @author      $Author:  $
 * @copyright   2006-2008 Corgan - Stefan Knaak | Wallenium & the EQdkp-Plus Developer Team
 * @link        http://eqdkp-plus.com
 * @package     eqdkp-plus
 * @version     $Rev:  $
 * 
 * $Id:  $
 */

// Do not remove. Security Option!
if ( !defined('EQDKP_INC') ){
    header('HTTP/1.0 404 Not Found');exit;
}

//---- Main ----
$plang['pluskernel']          	= 'PLUS Конфигурация';
$plang['pk_adminmenu']         	= 'PLUS Конфигурация';
$plang['pk_settings']						= 'Настройки';
$plang['pk_date_settings']			= 'День.Месяц.Год';

//---- Javascript stuff ----
$plang['pk_plus_about']					= 'О EQDKP PLUS';
$plang['updates']								= 'Доступно обновление';
$plang['loading']								= 'Загрузка...';
$plang['pk_config_header']			= 'EQDKP PLUS Настройки';
$plang['pk_close_jswin1']      	= 'Закройте';
$plang['pk_close_jswin2']     	= 'данное окно перед открытием снова!';
$plang['pk_help_header']				= 'Помощь';

//---- Updater Stuff ----
$plang['pk_alt_attention']			= 'Attention';
$plang['pk_alt_ok']							= 'Everything OK!';
$plang['pk_updates_avail']			= 'Доступно обновление';
$plang['pk_updates_navail']			= 'Нет доступных обновлений';
$plang['pk_no_updates']					= 'Ваша версия является самой новой. Отсутствуют новые доступные версии.';
$plang['pk_act_version']				= 'Новая версия';
$plang['pk_inst_version']				= 'Установлено';
$plang['pk_changelog']					= 'Changelog';
$plang['pk_download']						= 'Скачать';
$plang['pk_upd_information']		= 'Информация';
$plang['pk_enabled']						= 'Активировано';
$plang['pk_disabled']						= 'Деактивировано';
$plang['pk_auto_updates1']			= 'The automatic update warning is';
$plang['pk_auto_updates2']			= 'If you disabled this setting, please recheck regulary for updates to prevent hacks and stay up to date..';
$plang['pk_module_name']				= 'Имя модуля';
$plang['pk_plugin_level']				= 'Уровень';
$plang['pk_release_date']				= 'Release';
$plang['pk_alt_error']					= 'Ошибка';
$plang['pk_no_conn_header']			= 'Ошибка соединения';
$plang['pk_no_server_conn']			= 'Произошла ошибка при попытке соединения с сервером обновления, ваш хост не позволяет экспортные подключения
 или ошибка была вызвана проблемой в глобальной сети. Пожалуйста, посетите  eqdkp-plugin-forum , чтобы убедиться, что у вас последняя версия.';


$plang['pk_reset_warning']			= 'Reset Warning';

//---- Update Levels ----
$plang['pk_level_other']				= 'Другое';
$updatelevel = array (
	'Bugfix'										=> 'Bugfix',
	'Feature Release'						=> 'Будущий Release',
	'Security Update'						=> 'Обновление безопасности',
	'New version'								=> 'Новая версия',
	'Release Candidate'					=> 'Release Кандидат',
	'Public Beta'								=> 'Публичная Beta',
	'Closed Beta'								=> 'Закрытая Beta',
	'Alpha'											=> 'Alpha',
);

//---- About ----
$plang['pk_version']						= 'Версия';
$plang['pk_prodcutname']				= 'Продукт';
$plang['pk_modification']				= 'Мод';
$plang['pk_tname']							= 'Шаблон';
$plang['pk_developer']					= 'Разработчик';
$plang['pk_plugin']							= 'Плагин';
$plang['pk_weblink']						= 'веб-ссылка';
$plang['pk_phpstring']					= 'PHP Строка';
$plang['pk_phpvalue']						= 'Значение';
$plang['pk_donation']						= 'Пожертвование';
$plang['pk_job']								= 'Работа';
$plang['pk_sitename']						= 'Сайт';
$plang['pk_dona_name']					= 'Имя';
$plang['pk_betateam1']					= 'Команда бета-тестеров (Германия)';
$plang['pk_betateam2']					= 'Хронологический порядок';
$plang['pk_created by']					= 'Создано';
$plang['web_url']								= 'Веб-страница';
$plang['personal_url']					= 'Частный';
$plang['pk_credits']						= 'Разработчики';
$plang['pk_sponsors']						= 'Спонсоры';
$plang['pk_plugins']						= 'Плагины';
$plang['pk_modifications']			= 'Моды';
$plang['pk_themes']							= 'Стили';
$plang['pk_additions']					= 'Добавление кода';
$plang['pk_tab_stuff']					= 'EQDKP Команда';
$plang['pk_tab_help']						= 'Помощь';
$plang['pk_tab_tech']						= 'Технология';

//---- Settings ----
$plang['pk_save']								= 'Сохранить';
$plang['pk_save_title']					= '';
$plang['pk_succ_saved']					= 'Настройки были успешно сохранены';
 // Tabs
$plang['pk_tab_global']					= 'Глобально';
$plang['pk_tab_multidkp']				= 'multiDKP';
$plang['pk_tab_links']					= 'Ссылки';
$plang['pk_tab_bosscount']			= 'BossCounter';
$plang['pk_tab_listmemb']				= 'Список участников';
$plang['pk_tab_itemstats']			= 'Статистика предметов';
// Global
$plang['pk_set_QuickDKP']				= 'Показать быстрого DKP';
$plang['pk_set_Bossloot']				= 'Показать трофеи с боссов ?';
$plang['pk_set_ClassColor']			= 'Colored ClassClassnames';
$plang['pk_set_Updatecheck']		= 'Включить авто-обновление';
$plang['pk_window_time1']				= 'Показ каждого окна';
$plang['pk_window_time2']				= 'Минуты';
// MultiDKP
$plang['pk_set_multidkp']				= 'Включить MultiDKP';
// Listmembers
$plang['pk_set_leaderboard']		= 'Показывать Leaderboard';
$plang['pk_set_lb_solo']				= 'Показывать Leaderboard per account';
$plang['pk_set_rank']						= 'Показывать Ранг';
$plang['pk_set_rank_icon']			= 'Показывать иконку Ранга';
$plang['pk_set_level']					= 'Показывать Уровень';
$plang['pk_set_lastloot']				= 'Показывать последний трофей';
$plang['pk_set_lastraid']				= 'Показывать последний Рейд';
$plang['pk_set_attendance30']		= 'Показывать Рейд Attendance 30 дней';
$plang['pk_set_attendance60']		= 'Показывать Рейд Attendance 60 дней';
$plang['pk_set_attendance90']		= 'Показывать Рейд Attendance 90 дней';
$plang['pk_set_attendanceAll']	= 'Показывать Рейд Attendance Lifetime';
// Links
$plang['pk_set_links']					= 'Активировать Links';
$plang['pk_set_linkurl']				= 'Link URL';
$plang['pk_set_linkname']				= 'Link названия';
$plang['pk_set_newwindow']			= 'открывать в новом окне?';
// BossCounter
$plang['pk_set_bosscounter']		= 'Показать Bosscounter';
//Itemstats
$plang['pk_set_itemstats']			= 'Включить статистику предметов';
$plang['pk_is_language']				= 'Язык статистики предметов';
$plang['pk_german']							=	'Немецкий';
$plang['pk_english']						= 'Английский';
$plang['pk_french']							= 'Французский';
$plang['pk_set_icon_ext']				= '';
$plang['pk_set_icon_loc']				= '';
$plang['pk_set_en_de']					= 'Перевод предметов с Английского на Немецкий';
$plang['pk_set_de_en']					= 'Перевод предметов с Немецкого на Английский';

################
# new sort
###############

//MultiDKP
//

$plang['pk_set_multi_Tooltip']						= 'Показывать DKP совет';
$plang['pk_set_multi_smartTooltip']			  = 'Шикарный совет';

//Help
$plang['pk_help_colorclassnames']				  = "If activated, the players will be shown with the WoW colors of their class and their class icon.";
$plang['pk_help_quickdkp']								= "Shows the logged-in user the points off all members that are assigned to him above the menu.";
$plang['pk_help_boosloot']								= "Если активировано, you can click the boss names in the Рейд notes and the bosscounter to have a detailed overview of the dropped items. If inactive, it will be linked to Blasc.de (Only activate if you enter a Рейд for each single boss)";
$plang['pk_help_autowarning']             = "Warns the administrator when he logs in, if updates are available.";
$plang['pk_help_warningtime']             = "How often should the warning appear?";
$plang['pk_help_multidkp']								= "MultiDKP allows the management and overview of seperate accounts. Activates the management and overview of the MultiDKP accounts.";
$plang['pk_help_dkptooltip']							= "If activated, a tooltip with detailed information about the calculation of the points will be shown, when the mouse hovers over the different points.";
$plang['pk_help_smarttooltip']						= "Shortened overview of the tooltips (activate if you got more than three events per account)";
$plang['pk_help_links']                   = "In this menu you are able to define different links, which will be displayed in the main menu.";
$plang['pk_help_bosscounter']             = "If activated, a table will be displayed below the main menu with the bosskills. The administration is being managed by the plugin Bossprogress";
$plang['pk_help_lm_leaderboard']					= "If activated, a leaderboard will be displayed above the scoretable. A leaderboard is a table, where the dkp of each class is displayed sorted in decending order.";
$plang['pk_help_lm_rank']                 = "An extra column is being displayed, which displays the rank of the member.";
$plang['pk_help_lm_rankicon']             = "Instead of the rank name, an icon is being displayed. Which items are available you can check in the folder \images\rank";
$plang['pk_help_lm_level']								= "An additional column is being displayed, which displays the level of the member. ";
$plang['pk_help_lm_lastloot']             = "An extra colums is being displayed, showing the date a member received his latest item.";
$plang['pk_help_lm_lastraid']             = "An extra column is being displayed, showing the date of the latest Рейд a member has been participated in.";
$plang['pk_help_lm_atten30']							= "An extra column is being displayed, showing a members participation in Рейд during the last 30 days (in percent).";
$plang['pk_help_lm_atten60']							= "An extra column is being displayed, showing a members participation in Рейд during the last 60 days (in percent). ";
$plang['pk_help_lm_atten90']							= "An extra column is being displayed, showing a members participation in Рейд during the last 90 days (in percent). ";
$plang['pk_help_lm_attenall']             = "An extra column is being displayed, showing a members overall Рейд participation (in percent).";
$plang['pk_help_itemstats_on']						= "Itemstats is requesting information about items entered in EQDKP in the WOW databases (Blasc, Allahkazm, Thottbot). These will be displayed in the color of the items quality including the known WOW tooltip. When active, items will be shown with a mouseover tooltip, similar to WOW.";
$plang['pk_help_itemstats_search']				= "Which database should Itemstats use first to lookup information? Blasc or Allakhazam?";
$plang['pk_help_itemstats_icon_ext']			= "Filename extension of the pictures to be shown. Usually .png or .jpg.";
$plang['pk_help_itemstats_icon_url']      = "Please enter the URL where you Itemstats pictures are being located. German: http://www.buffed.de/images/wow/32/ in 32x32 or http://www.buffed.de/images/wow/64/ in 64x64 pixels.English at Allakzam: http://www.buffed.de/images/wow/32/";
$plang['pk_help_itemstats_translate_deeng']		= "If active, information of the tooltips will be requested in german, even when the item is being entered in english.";
$plang['pk_help_itemstats_translate_engde']		= "If active, information of the tooltips will be requested in English, even if the item is being entered in german.";

$plang['pk_set_leaderboard_2row']					= 'Leaderboard in 2 lines';
$plang['pk_help_leaderboard_2row']        = 'If active, the Leaderboard will be displayed in two lines with 4 or 5 classes each.';

$plang['pk_set_leaderboard_limit']        = 'limitation of the display';
$plang['pk_help_leaderboard_limit']				= 'If a numeric number is being entered, the Leaderboard will be restricted to the entered number of members. The number 0 represents no restrictions.';

$plang['pk_set_leaderboard_zero']         = 'Hide Player with zeor DKP';
$plang['pk_help_leaderboard_zero']        = 'If activated, Players with zero DKp doesnt show in the leaderboard.';


$plang['pk_set_newsloot_limit']						= 'newsloot limit';
$plang['pk_help_newsloot_limit']          = 'How many items should be displayed in the news? This restricts the display of items, which will be displeyed in the news. The number 0 represents no restrictions.';

$plang['pk_set_itemstats_debug']          = 'debug Modus';
$plang['pk_help_itemstats_debug']					= 'If activated, Itemstats will log all transactions to /itemstats/includes_de/debug.txt. This file has to be writable, CHMOD 777 !!!';

$plang['pk_set_showclasscolumn']          = 'show classes column';
$plang['pk_help_showclasscolumn']					= 'If activated, an extra column is being displayed showing the class of the player.' ;

$plang['pk_set_show_skill']								= 'show skill column';
$plang['pk_help_show_skill']              = 'If activated, an extra column is being displayed showing the skill of the player.';

$plang['pk_set_show_arkan_resi']          = 'show arcan resistance column';
$plang['pk_help_show_arkan_resi']					= 'If activated, an extra column is being displayed showing the arcane resistance of the player.';

$plang['pk_set_show_fire_resi']						= 'show fire resistance column';
$plang['pk_help_show_fire_resi']          = 'If activated, an extra column is being displayed showing the fire resistance of the player.';

$plang['pk_set_show_nature_resi']					= 'show nature resistance column';
$plang['pk_help_show_nature_resi']        = 'If activated, an extra column is being displayed showing the nature resistance of the player.';

$plang['pk_set_show_ice_resi']            = 'show ice resistance column';
$plang['pk_help_show_ice_resi']						= 'If activated, an extra column is being displayed showing the frost resistance of the player.';

$plang['pk_set_show_shadow_resi']					= 'show shadow resistance column';
$plang['pk_help_show_shadow_resi']        = 'If activated, an extra column is being displayed showing the shadow resistance of the player.';

$plang['pk_set_show_profils']							= 'show profile link column';
$plang['pk_help_show_profils']            = 'If activated, an extra column is being displayed showing the links to the profile.';

$plang['pk_set_servername']               = 'Название Сервера';
$plang['pk_help_servername']              = 'Введите ваше название сервера здесь.';

$plang['pk_set_server_region']			  = 'Регион';
$plang['pk_help_server_region']			  = 'Американский или Европейский сервер.';


$plang['pk_help_default_multi']           = 'Choose the default DKP Acc for the leaderboard.';
$plang['pk_set_default_multi']            = 'Установить default for leaderboard';

$plang['pk_set_round_activate']           = 'Раунд DKP.';
$plang['pk_help_round_activate']          = 'If activated, DKP Point displayed rounded. 125.00 = 125DKP.';

$plang['pk_set_round_precision']          = 'Decimal place to round.';
$plang['pk_help_round_precision']         = 'Set the Decimal place to round the DKP Defualt=0';

$plang['pk_is_set_prio']                  = 'Priority of Itemdatabase';
$plang['pk_is_help_prio']                 = 'Set the query order of item databases.';

$plang['pk_is_set_alla_lang']	            = 'Language of Itemnames on Allakhazam.';
$plang['pk_is_help_alla_lang']	          = 'Which language should the requested items be?';

$plang['pk_is_set_lang']		              = 'Standard language of Item ID\'s.';
$plang['pk_is_help_lang']		              = 'Standard language of Item IDs. Example : [item]17182[/item] will choose this language';

$plang['pk_is_set_autosearch']            = 'Immediate Search';
$plang['pk_is_help_autosearch']           = 'Activated: If the item is not in the cache, search for the item information automatically. Not activated: Item information is only fetch on click on the item information.';

$plang['pk_is_set_integration_mode']      = 'Integration Modus';
$plang['pk_is_help_integration_mode']     = 'Normal: scanning text and setting tooltip in html code. Script: scanning text and set <script> tags.';

$plang['pk_is_set_tooltip_js']            = 'Look of Tooltips';
$plang['pk_is_help_tooltip_js']           = 'Overlib: The normal Tooltip. Light: Light version, faster loading times.';

$plang['pk_is_set_patch_cache']           = 'Cache Path';
$plang['pk_is_help_patch_cache']          = 'Path to the user item cache, starting from /itemstats/. Default=./xml_cache/';

$plang['pk_is_set_patch_sockets']         = 'Path of Socketpictures';
$plang['pk_is_help_patch_sockets']        = 'Path to the picture files of the socket items.';

$plang['pk_set_dkp_info']			  = 'Не показывать DKP информацию в главном меню.';
$plang['pk_help_dkp_info']			  = 'If activated "DKP Info" will be hidden from the main menu.';

$plang['pk_set_debug']			= 'Включить Eqdkp Debug Modus';
$plang['pk_set_debug_type']		= 'Мод';
$plang['pk_set_debug_type0']	= 'Debug off (Debug=0)';
$plang['pk_set_debug_type1']	= 'Debug on simple (Debug=1)';
$plang['pk_set_debug_type2']	= 'Debug on with SQL Queries (Debug=2)';
$plang['pk_set_debug_type3']	= 'Debug on extended (Debug=3)';
$plang['pk_help_debug']			= 'Если активировано, Eqdkp Plus will be running in debug mode, showing additional informations and error messages. Deaktivate if plugins abort with SQL error messages! 1=Rendering time, Query count, 2=SQL outputs, 3=Enhanced error messages.';

#RSS News
$plang['pk_set_Show_rss']			= 'Деактивировать RSS Новости';
$plang['pk_help_Show_rss']			= 'If activated, Eqdkp Plus will be show Game RSS News.';

$plang['pk_set_Show_rss_style']		= 'game-news positioning';
$plang['pk_help_Show_rss_style']	= 'RSS-Game News positioning. Top horizontal, in the menu vertical or both?';

$plang['pk_set_Show_rss_lang']		= 'RSS-Новости язык по умолчанию';
$plang['pk_help_Show_rss_lang']		= 'Get the RSS-News in wich language? (atm only german). English news available beginning 2008.';

$plang['pk_set_Show_rss_lang_de']	= 'Немецкий';
$plang['pk_set_Show_rss_lang_eng']	= 'Английский';


$plang['pk_set_Show_rss_style_both'] = 'Оба' ;
$plang['pk_set_Show_rss_style_v']	 = 'Вертикальное меню' ;
$plang['pk_set_Show_rss_style_h']	 = 'По горизонтали' ;

$plang['pk_set_Show_rss_count']		= 'Новости цен (0 or "" for all)';
$plang['pk_help_Show_rss_count']	= 'Wieviele News sollen angezeigt werden?';

$plang['pk_set_itemhistory_dia']	= 'Dont show diagrams'; # Ja negierte Abfrage
$plang['pk_help_itemhistory_dia']	= 'If activated, Eqdkp Plus show sseveral diagramm.';


#Bridge
$plang['pk_set_bridge_help']				= 'On This Tab you can tune up the settings to let an Content Management System (CMS) interact with Eqdkp Plus.
											   If you choose one of the Systems in the Drop Down Field , Registered Members of your Forum/CMS will be able to log in into Eqdkp Plus with the same credentials used in Forum/CMS.
											   The Access is only allowed for one Group, that Means that you must create a new group in your CMS/Forum which all Members belong who will be accessing Eqdkp.';

$plang['pk_set_bridge_activate']			= 'Активировать соединение с CMS';
$plang['pk_help_bridge_activate']			= 'When bridging is activated, Users of the Forum or CMS will be able to Log On in Eqdkp Plus with the same credentials as used in CMS/Forum';

$plang['pk_set_bridge_dectivate_eq_reg']	= 'Деактивировать регистрацию в Eqdkp Plus';
$plang['pk_help_bridge_dectivate_eq_reg']	= 'Когда Деактивировано новые Участники не могут регистрироваться в Eqdkp Plus. Регистрация новых Пользователей должна быть сделана в CMS/Форум';

$plang['pk_set_bridge_cms']					= 'Поддержка CMS';
$plang['pk_help_bridge_cms']				= 'Which CMS shall be bridged ';

$plang['pk_set_bridge_acess']				= 'Is the CMS/Forum on another Database than Eqdkp ?';
$plang['pk_help_bridge_acess']				= 'Если Вы используете CMS/Forum on another Database activate this and fill the Fields below';

$plang['pk_set_bridge_host']				= 'Хост';
$plang['pk_help_bridge_host']				= 'Название хоста или IP-адерсс на котором установлена база данных';

$plang['pk_set_bridge_username']			= 'Имя пользователя базы данных';
$plang['pk_help_bridge_username']			= 'Имя пользователя, используемое для подключения к базе данных';

$plang['pk_set_bridge_password']			= 'Пароль базы данных';
$plang['pk_help_bridge_password']			= 'Пароль пользователя для соединения';

$plang['pk_set_bridge_database']			= 'Название базы данных';
$plang['pk_help_bridge_database']			= 'Название базы данных, где CMS данные находится';

$plang['pk_set_bridge_prefix']				= 'Tableprefix of your CMS Installation';
$plang['pk_help_bridge_prefix']				= 'Give your Prefix of your CMS . e.g.. phpbb_ or wcf1_';

$plang['pk_set_bridge_group']				= 'ID Группы для CMS Группы';
$plang['pk_help_bridge_group']				= 'Введите здесь ID для Группы в CMS, который предоставляет доступ к Eqdkp.';


$plang['pk_set_bridge_inline']				= 'Интеграция вашего форума в EQDKP - Внимание BETA !';
$plang['pk_help_bridge_inline']				= 'Если вы вносите здесь URL, тогда дополнительная ссылка будет видна в Меню, которая будет ссылаться на указанную страницу в пределах Eqdkp. Это происходит в динамичном Iframe. Das EQDKP Plus ist aber nicht verantworltich fьr das Aussehen bzw. das Verhalten der eingebundenen Seite innerhalb eines Iframs!';

$plang['pk_set_bridge_inline_url']			= 'URL на ваш форум';
$plang['pk_help_bridge_inline_url']			= 'URL на ваш форум, в пределах представления EQDKP';

$plang['pk_set_link_type_header']			= 'Как страница должна открываться';
$plang['pk_set_link_type_help']				= 'Link im selben Browserfenster, in einem neuen Brwoserfenster oder innerhalb des Eqdkps in einem Iframe цffnen?';
$plang['pk_set_link_type_iframe_help']		= 'Указанная страница будет показана в пределах Eqdkp. Это происходит в динамичном Iframe. Das EQDKP Plus ist aber nicht verantworltich fьr das Aussehen bzw. das Verhalten der eingebundenen Seite innerhalb eines Iframs!';
$plang['pk_set_link_type_self']				= 'Норма';
$plang['pk_set_link_type_link']				= 'Новое окно';
$plang['pk_set_link_type_iframe']			= 'Заделано';

#recruitment
$plang['pk_set_recruitment_tab']			= 'Требования';
$plang['pk_set_recruitment_header']			= 'Требования - Are you looking for new Members ?';
$plang['pk_set_recruitment']				= 'Активизировать требования';
$plang['pk_help_recruitment']				= 'Если активно, то ящик с требуемыми классами и спецификацией будет показан на верху, на ряду с новостями.';
$plang['pk_recruitment_count']				= 'Цена';

$plang['pk_set_recruitment_contact_type']	= 'Linkurl';
$plang['pk_help_recruitment_contact_type']	= 'If no URL is given, it will link to the contact email.';
$plang['ps_recruitment_spec']				= 'Специализация';

$plang['pk_set_comments_disable']			= 'Деактивировать комментарии';
$plang['pk_hel_pcomments_disable']			= 'Деактивировать комментарии на всех страницах';

#Contact
$plang['pk_contact']						= 'Контактная информация';
$plang['pk_contact_name']					= 'Имя';
$plang['pk_contact_email']					= 'Email';
$plang['pk_contact_website']				= 'Веб-страница';
$plang['pk_contact_irc']					= 'IRC Канал';
$plang['pk_contact_admin_messenger']		= 'Название учетной записи (Skype, ICQ)';
$plang['pk_contact_custominfos']			= 'Дополнительная информация';
$plang['pk_contact_owner']					= 'Информация о Владельце:';

#Next_raids
$plang['pk_set_nextraids_deactive']			= 'Не показывать следующие Рейды';
$plang['pk_help_nextraids_deactive']		= 'Если Активно, следующие Рейды не будут показаны в меню';

$plang['pk_set_nextraids_limit']			= 'Лимит показа следующих Рейдов';
$plang['pk_help_nextraids_limit']			= '';

$plang['pk_set_lastitems_deactive']			= 'Не показывать последние предметы';
$plang['pk_help_lastitems_deactive']		= 'Если Активно, последние предметы не будут показаны в меню';

$plang['pk_set_lastitems_limit']			= 'Лимит показа последних предметов';
$plang['pk_help_lastitems_limit']			= '';

$plang['pk_is_help']						= '<b>Внимание: Изменения в работе Статистики предметов с Eqdkp Plus 0.5!</b><br><br>
											  Wenn ihr von Eqdp Plus 0.4 auf 0.5 aktualisiert oder die Such-Prioritдt geдndert habt, mьsst ihr euren Itemcache refreshen!!<br>
											  Empfohlene Prioritдt ist 1.Armory 2. WoWHead. oder 1. Buffed und 2. Allakhazam<br>
											  Eine Mischung aus Armory/WoWHead und Buffed/Allakhazam ist nicht mцglich, da die Tooltips und Icons nicht kompatibel sind!<br>
											  Zum aktualisieren des Itemcache dem Link folgen, danach die Buttons "Clear Cache" und danach "Update Itemtable" auswдhlen.<br><br>';

$plang['pk_set_normal_leaderbaord']			= 'Показывать Leaderboard со Слайдером';
$plang['pk_help_normal_leaderbaord']		= 'Если Активно,Leaderboard использует Слайдер.';

$plang['pk_set_lastraids']					= 'Не показывать последние Рейды';
$plang['pk_help_lastraid']					= 'Если Активно, последний Рейд не показан в меню';

$plang['pk_set_lastraids_limit']			= 'Последний лимит Рейдов';
$plang['pk_help_lastraid_limit']			= 'Последний лимит Рейдов';

$plang['pk_set_lastraids_showloot']			= 'Показывать предметы после последних Рейдов';
$plang['pk_help_lastraid_showloot']			= 'Показывать предметы после последних Рейдов';

$plang['pk_lastraids_lootLimit']		= 'Лимит Предметов';
$plang['pk_help_lastraid_lootLimit']		= 'Лимит Предметов';


$plang['pk_set_ts_active']					= 'activate TS Viewer';
$plang['pk_help_ts_active']					= 'activate TS Viewer';

$plang['pk_set_ts_title']					= 'TS-Сервер Заголовок';
$plang['pk_help_ts_title']					= 'TS-Сервер Заголовок';

$plang['pk_set_ts_serverAddress']			= 'TS-Сервер IP';
$plang['pk_help_ts_serverAddress']			= 'TS-Сервер IP';

$plang['pk_set_ts_serverQueryPort']			= 'Порт запроса';
$plang['pk_help_ts_serverQueryPort']		= 'Порт запроса';

$plang['pk_set_ts_serverUDPPort']			= 'UDP Порт';
$plang['pk_help_ts_serverUDPPort']			= 'UDP Порт';

$plang['pk_set_ts_serverPasswort']			= 'Пароль сервера';
$plang['pk_help_ts_serverPasswort']			= 'Пароль сервера';


$plang['pk_set_ts_channelflags']			= 'Показывать флаги канала (R,M,S,P и т.д.)';
$plang['pk_help_ts_channelflags']			= 'Показывать флаги канала (R,M,S,P и т.д.)';

$plang['pk_set_ts_userstatus']				= 'Показывать статус Участника (U,R,SA и т.д.)';
$plang['pk_help_ts_userstatus']				= 'Показывать статус Участника (U,R,SA и т.д.)';

$plang['pk_set_ts_showchannel']				= 'Показывать каналы? (0 = только Участник)';
$plang['pk_help_ts_showchannel']			= 'Показывать каналы? (0 = только Участник)';

$plang['pk_set_ts_showEmptychannel']		= 'Показывать пустые каналы?';
$plang['pk_help_ts_showEmptychannel']		= 'Показывать пустые каналы?';

$plang['pk_set_ts_overlib_mouseover']		= 'Show mouseover informations? ';
$plang['pk_help_ts_overlib_mouseover']		= 'Show mouseover informations? (german only atm, translation comes later)';

$plang['pk_set_ts_joinable']				= 'Show link to join the server?';
$plang['pk_help_ts_joinable']				= 'Show link to join the server';

$plang['pk_set_ts_joinableMember']			= 'Show the join link only registeres users?';
$plang['pk_help_ts_joinableMember']			= 'Link zum joinen nur eingelogten Usern anzeigen?';

$plang['pk_set_ts_ranking']					= 'Показывать Изображение Ранга';
$plang['pk_help_ts_ranking']				= 'Показывать Изображение Ранга';

$plang['pk_set_ts_ranking_url']				= 'Изображение Ранга URL like <a href=http://www.wowjutsu.com/>WoWJutsu</a> or <a href=http://www.bosskillers.com/>Bosskillers</a> ';
$plang['pk_help_ts_ranking_url']			= 'Изображение Ранга URL like <a href=http://www.wowjutsu.com/>WoWJutsu</a> or <a href=http://www.bosskillers.com/>Bosskillers</a> ';

$plang['pk_set_ts_ranking_link']			= 'Изображение Ранга Link';
$plang['pk_help_ts_ranking_link']			= 'Изображение Ранга Link';

$plang['pk_set_thirdColumn']				= 'Не показывайте третьему столбцу';
$plang['pk_help_thirdColumn']				= 'Не показывайте третьему столбцу';

#GetDKP
$plang['pk_getdkp_th']						= 'GetDKP настройки';

$plang['pk_set_getdkp_rp']					= 'Активировать raidplan';
$plang['pk_help_getdkp_rp']					= 'Активирование raidplan';

$plang['pk_set_getdkp_link']				= 'Показать getdkp ссылку в главном меню';
$plang['pk_help_getdkp_link']				= 'Показ getdkp ссылку в главном меню';

$plang['pk_set_getdkp_active']				= 'Деактивировать getdkp.php';
$plang['pk_help_getdkp_active']				= 'Деактивирование getdkp.php';

$plang['pk_set_getdkp_items']				= 'Отключить itemIDs';
$plang['pk_help_getdkp_items']				= 'Отключение itemIDs';

$plang['pk_set_recruit_embedded']			= 'open Link embedded';
$plang['pk_help_recruit_embedded']			= 'Если активировано, the link opens embedded i an iframe';


/*
$plang['pk_set_']	= '';
$plang['pk_help_']	= '';
*/
?>
