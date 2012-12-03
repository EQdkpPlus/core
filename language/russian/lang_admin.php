<?php
/******************************
 * EQdkp
 * Copyright 2002-2003
 * Licensed under the GNU GPL.  See COPYING for full terms.
 * ------------------
 * lang_admin.php
 * Began: Fri January 3 2003
 *
 * $Id: lang_admin.php 1775 2008-03-23 01:46:36Z osr-corgan $
 *
 ******************************/

if ( !defined('EQDKP_INC') )
{
     die('У вас нету прав для данной файловой директории.');
}

// %1\$<type> prevents a possible error in strings caused
//      by another language re-ordering the variables
// $s is a string, $d is an integer, $f is a float

// Titles
$lang['addadj_title']         = 'Добавить/Изменить Групповое Изменение';
$lang['addevent_title']       = 'Добавить/Изменить Событие';
$lang['addiadj_title']        = 'Добавить/Изменить Индивидуальное Изменение';
$lang['additem_title']        = 'Добавить/Изменить Предмет';
$lang['addmember_title']      = 'Добавление/Изменение Участника Гильдии';
$lang['addnews_title']        = 'Добавить/Изменить Новость';
$lang['addraid_title']        = 'Добавить/Изменить Рейд';
$lang['addturnin_title']      = "Передача предметов - Шаг %1\$d";
$lang['admin_index_title']    = 'Администрирование EQDKP';
$lang['config_title']         = 'Управление EQdkp';
$lang['manage_members_title'] = 'Управление Участниками Гильдии';
$lang['manage_users_title']   = 'Управление Пользователями и Правами Доступа';
$lang['parselog_title']       = 'Синтаксический анализ лог файла';
$lang['plugins_title']        = 'Управление Плагинами';
$lang['styles_title']         = 'Управление Стилями';
$lang['viewlogs_title']       = 'Просмотр Лога';

// Page Foot Counts
$lang['listusers_footcount']             = "... найдено %1\$d Пользователь (ей) / %2\$d на каждой странице";
$lang['manage_members_footcount']        = "... найдено %1\$d участник (ов)";
$lang['online_footcount']                = "... %1\$d участников в online";
$lang['viewlogs_footcount']              = "... найдено %1\$d лога (ов) / %2\$d на каждой странице";

// Submit Buttons
$lang['add_adjustment'] = 'Добавить изменение';
$lang['add_account'] = 'Добавить Учетную запись';
$lang['add_event'] = 'Добавить Событие';
$lang['add_item'] = 'Добавить Предмет';
$lang['add_member'] = 'Добавить Участника';
$lang['add_news'] = 'Добавить Новость';
$lang['add_raid'] = 'Добавить Рейд';
$lang['add_style'] = 'Добавить Стиль';
$lang['add_turnin'] = 'Совершить передачу предметов';
$lang['delete_adjustment'] = 'Удалить Изменение';
$lang['delete_event'] = 'Удалить Событие';
$lang['delete_item'] = 'Удалить Предмет';
$lang['delete_member'] = 'Удалить Участника';
$lang['delete_news'] = 'Удалить Новость';
$lang['delete_raid'] = 'Удалить Рейд';
$lang['delete_selected_members'] = 'Удалить выбранного (ых) Участника (ов)';
$lang['delete_style'] = 'Удалить стиль';
$lang['mass_delete'] = 'Массовое удаление';
$lang['mass_update'] = 'Права доступа';
$lang['parse_log'] = 'Синтаксический анализ лога';
$lang['search_existing'] = 'Поиск среди существующего';
$lang['select'] = 'Выбрать';
$lang['transfer_history'] = 'Перенести Журнал Событий';
$lang['update_adjustment'] = 'Обновить Изменение';
$lang['update_event'] = 'Обновить Событие';
$lang['update_item'] = 'Обновить Предмет';
$lang['update_member'] = 'Обновить Участника';
$lang['update_news'] = 'Обновить Новость';
$lang['update_raid'] = 'Обновить Рейд';
$lang['update_style'] = 'Обновить Стиль';

// Misc
$lang['account_enabled'] = 'Учетная запись задействована';
$lang['adjustment_value'] = 'Количество Изменения';
$lang['adjustment_value_note'] = 'Может быть отрицательным';
$lang['code'] = 'Код';
$lang['contact'] = 'Контакт';
$lang['create'] = 'Создать';
$lang['found_members'] = "Анализ %1\$d строк, найдено d %2\$d Пользователей";
$lang['headline'] = 'Заголовок';
$lang['hide'] = 'Скрыть?';
$lang['install'] = 'Установить';
$lang['item_search'] = 'Поиск Предмета';
$lang['list_prefix'] = 'Список Префиксов';
$lang['list_suffix'] = 'Список Суффиксов';
$lang['logs'] = 'Логи';
$lang['log_find_all'] = 'Найти все(включая анонимных участников)';
$lang['manage_members'] = 'Управление Участниками';
$lang['manage_plugins'] = 'Управление Плагинами';
$lang['manage_users'] = 'Управление Пользователями';
$lang['mass_update_note'] = 'Права доступа по умолчанию для всех вновь зарегистрировованных пользователей (Ниже вы можете их изменить) нажмите "Обновление прав доступа".
                             Чтобы удалить несколько выбранных пользователей, нажмите "Массовое удаление"';
$lang['members'] = 'Участники:';
$lang['member_rank'] = 'Ранг Участника';
$lang['message_body'] = 'Текст Сообщение';
$lang['message_show_loot_raid'] = 'Показать Трофеи Рейда(ов):';
$lang['results'] = "%1\$d Результаты (\"%2\$s\")";
$lang['search'] = 'Поиск';
$lang['search_members'] = 'Поиск Участников';
$lang['should_be'] = 'Должно быть';
$lang['styles'] = 'Стили';
$lang['title'] = 'Заголовок';
$lang['uninstall'] = 'Удалить';
$lang['enable']		= 'Включено';
$lang['update_date_to'] = "Изменить дату на<br />%1\$s?";
$lang['version'] = 'Версия';
$lang['x_members_s'] = "%1\$d участник";
$lang['x_members_p'] = "%1\$d участников";

// Permission Messages
$lang['noauth_a_event_add']    = 'У вас нет прав для добавления событий';
$lang['noauth_a_event_upd']    = 'У вас нет прав для обновления событий';
$lang['noauth_a_event_del']    = 'У вас нет прав для удаления событий';
$lang['noauth_a_groupadj_add'] = 'У вас нет прав для добавления групповых изменений';
$lang['noauth_a_groupadj_upd'] = 'У вас нет прав для обновления групповых изменений';
$lang['noauth_a_groupadj_del'] = 'У вас нет прав для удаления групповых изменений';
$lang['noauth_a_indivadj_add'] = 'У вас нет прав для добавления индивидуальных изменений';
$lang['noauth_a_indivadj_upd'] = 'У вас нет прав для обновления индивидуальных изменений';
$lang['noauth_a_indivadj_del'] = 'У вас нет прав для удаления индивидуальных изменений';
$lang['noauth_a_item_add']     = 'У вас нет прав для добавления предметов';
$lang['noauth_a_item_upd']     = 'У вас нет прав для обновления предметов';
$lang['noauth_a_item_del']     = 'У вас нет прав для удаления предметов';
$lang['noauth_a_news_add']     = 'У вас нет прав для добавления новостей';
$lang['noauth_a_news_upd']     = 'У вас нет прав для обновления новостей';
$lang['noauth_a_news_del']     = 'У вас нет прав для удаления новостей';
$lang['noauth_a_raid_add']     = 'У вас нет прав для добавления Рейдов';
$lang['noauth_a_raid_upd']     = 'У вас нет прав для обновления Рейдов';
$lang['noauth_a_raid_del']     = 'У вас нет прав для удаления Рейдов';
$lang['noauth_a_turnin_add']   = 'У вас нет прав для добавления покупок';
$lang['noauth_a_config_man']   = 'У вас нет прав для управления настройками EQdkp';
$lang['noauth_a_members_man']  = 'У вас нет прав для управления участниками гильдии';
$lang['noauth_a_plugins_man']  = 'У вас нет прав для управления плагинами EQdkp';
$lang['noauth_a_styles_man']   = 'У вас нет прав для управления стилями EQdkp';
$lang['noauth_a_users_man']    = 'У вас нет прав для управления настройками учетной записи пользователя';
$lang['noauth_a_logs_view']    = 'У вас нет прав для просмотра логов EQdkp';

// Submission Success Messages
$lang['admin_add_adj_success']               = "Изменение %1\$s было внесено в %2\$.2f базы данных гильдии";
$lang['admin_add_admin_success']             = "На адрес электронной почты %1\$s было отправлено письмо с административной информацией";
$lang['admin_add_event_success']             = "Значение стоимостью в %1\$s для Рейда, назначенного на %2\$s, было добавлено в базу данных гильдии";
$lang['admin_add_iadj_success']              = "Индивидуальное %1\$s изменение значением %2\$.2f для %3\$s было добавлено в базу данных гильдии";
$lang['admin_add_item_success']              = "Передача предмета для %1\$s, купленного %2\$s за %3\$.2f было добавлено в базу данных гильдии";
$lang['admin_add_member_success']            = "%1\$s был добавлен как участник гильдии";
$lang['admin_add_news_success']              = 'Новость была добавлена в базу данных гильдии';
$lang['admin_add_raid_success']              = "Рейд %1\$d/%2\$d/%3\$d назначенный на событие %4\$s был добавлен в базу данных гильдии";
$lang['admin_add_style_success']             = 'Новый стиль был успешно добавлен';
$lang['admin_add_turnin_success']            = "Предмет '%1\$s' был передан от участника %2\$s к участнику %3\$s.";
$lang['admin_delete_adj_success']            = "Изменение %1\$s для %2\$.2f было удалено из базы данных гильдии";
$lang['admin_delete_admins_success']         = "Выбранные администраторы были удалены";
$lang['admin_delete_event_success']          = "Настройка значения %1\$s для Рейда, назначенного на событие %2\$s , было удалено из базы данных гильдии";
$lang['admin_delete_iadj_success']           = "Индивидуальное %1\$s изменение значения %2\$.2f для %3\$s было удалено из базы данных гильдии";
$lang['admin_delete_item_success']           = "Передача предмета для %1\$s, купленного %2\$s за %3\$.2f было удалено из базы данных гильдии";
$lang['admin_delete_members_success']        = "%1\$s, включая всю историю пользователя, был(а) удален(а) из базы данных гильдии";
$lang['admin_delete_news_success']           = 'Новость была удалена из базы данных гильдии';
$lang['admin_delete_raid_success']           = 'Рейд, включая все предметы, связанные с ним, был удален из базы данных гильдии';
$lang['admin_delete_style_success']          = 'Стиль удален успешно';
$lang['admin_delete_user_success']           = "Учетная запись с именем пользователя %1\$s была удалена";
$lang['admin_set_perms_success']             = "Все административные полномочия были обновлены";
$lang['admin_transfer_history_success']      = "Для пользователя %1\$s вся история была перенесена в %2\$s и пользователь %1\$s был удален из базы данных гильдии";
$lang['admin_update_account_success']        = "Ваши настройки учетной записи были обновлены в базе данных";
$lang['admin_update_adj_success']            = "Изменение %1\$s для %2\$.2f было обновлено в базе данных гильдии";
$lang['admin_update_event_success']          = "Событие %2\$s со значением в %1\$s было обновлено в базе данных гильдии";
$lang['admin_update_iadj_success']           = "Индивидуальное %1\$s изменение значения %2\$.2f для %3\$s было обновлено в базе данных гильдии";
$lang['admin_update_item_success']           = "Передача предмета для %1\$s, купленного %2\$s за %3\$.2f было обновлено в базе данных гильдии";
$lang['admin_update_member_success']         = "Персонаж %1\$s был(а) обновлен(а)";
$lang['admin_update_news_success']           = 'Новость была обновлена в базе данных гильдии';
$lang['admin_update_raid_success']           = "Рейд %1\$d/%2\$d/%3\$d назначенный на событие %4\$s был обновлен в базе данных гильдии";
$lang['admin_update_style_success']          = 'Стиль был успешно обновлен';

$lang['admin_raid_success_hideinactive']     = 'Обновление статуса активности/неактивности пользователей';

// Delete Confirmation Texts
$lang['confirm_delete_adj']     = 'Вы уверены, что хотите удалить это групповое изменение?';
$lang['confirm_delete_admins']  = 'Вы уверены, что хотите удалить выбранного Администратора?';
$lang['confirm_delete_event']   = 'Вы уверены, что хотите удалить это событие?';
$lang['confirm_delete_iadj']    = 'Вы уверены, что хотите удалить это индивидуальное изменение?';
$lang['confirm_delete_item']    = 'Вы уверены, что хотите удалить этот предмет?';
$lang['confirm_delete_members'] = 'Вы уверены, что хотите удалить указанного участника?';
$lang['confirm_delete_news']    = 'Вы уверены, что хотите удалить указанную новость?';
$lang['confirm_delete_raid']    = 'Вы уверены, что хотите удалить этот Рейд?';
$lang['confirm_delete_style']   = 'Вы уверены, что хотите удалить этот стиль?';
$lang['confirm_delete_users']   = 'Вы уверены, что хотите удалить указанную учетную запись пользователя?';

// Log Actions
$lang['action_event_added']      = 'Событие добавлено';
$lang['action_event_deleted']    = 'Событие удалено';
$lang['action_event_updated']    = 'Событие обновлено';
$lang['action_groupadj_added']   = 'Групповое изменение добавлено';
$lang['action_groupadj_deleted'] = 'Групповое изменение удалено';
$lang['action_groupadj_updated'] = 'Групповое изменение обновлено';
$lang['action_history_transfer'] = 'Журнал переноса истории пользователя';
$lang['action_indivadj_added']   = 'Индивидуальное изменение добавлено';
$lang['action_indivadj_deleted'] = 'Индивидуальное изменение удалено';
$lang['action_indivadj_updated'] = 'Индивидуальное изменение обновлено';
$lang['action_item_added']       = 'Предмет добавлен';
$lang['action_item_deleted']     = 'Предмет удален';
$lang['action_item_updated']     = 'Предмет обновлен';
$lang['action_member_added']     = 'Участник добавлен';
$lang['action_member_deleted']   = 'Участник удален';
$lang['action_member_updated']   = 'Участник обновлен';
$lang['action_news_added']       = 'Новость добавлена';
$lang['action_news_deleted']     = 'Новость удалена';
$lang['action_news_updated']     = 'Новость обновлена';
$lang['action_raid_added']       = 'Рейд добавлен';
$lang['action_raid_deleted']     = 'Рейд удален';
$lang['action_raid_updated']     = 'Рейд обновлен';
$lang['action_turnin_added']     = 'Совершена передача предмета';

// Before/After
$lang['adjustment_after']  = 'Изменение после';
$lang['adjustment_before'] = 'Изменение до';
$lang['attendees_after']   = 'Привязка после';
$lang['attendees_before']  = 'Привязка до';
$lang['buyers_after']      = 'Покупатель после';
$lang['buyers_before']     = 'Покупатель до';
$lang['class_after']       = 'Класс после';
$lang['class_before']      = 'Класс до';
$lang['earned_after']      = 'Заработано после';
$lang['earned_before']     = 'Заработано до';
$lang['event_after']       = 'Событие после';
$lang['event_before']      = 'Событие до';
$lang['headline_after']    = 'Заголовок после';
$lang['headline_before']   = 'Заголовок до';
$lang['level_after']       = 'Уровень после';
$lang['level_before']      = 'Уровень до';
$lang['members_after']     = 'Участники после';
$lang['members_before']    = 'Участники до';
$lang['message_after']     = 'Сообщение после';
$lang['message_before']    = 'сообщение до';
$lang['name_after']        = 'Имя после';
$lang['name_before']       = 'Имя до';
$lang['note_after']        = 'Примечание после';
$lang['note_before']       = 'Примечание до';
$lang['race_after']        = 'Раса после';
$lang['race_before']       = 'Раса до';
$lang['raid_id_after']     = 'Рейдовый ID после';
$lang['raid_id_before']    = 'Рейдовый ID до';
$lang['reason_after']      = 'Причина после';
$lang['reason_before']     = 'Причина до';
$lang['spent_after']       = 'Потрачено после';
$lang['spent_before']      = 'Потрачено до';
$lang['value_after']       = 'Стоимость после';
$lang['value_before']      = 'Стоимость до';

// Configuration
$lang['general_settings'] = 'Общие настройки';
$lang['guildtag'] = 'Название гильдии';
$lang['guildtag_note'] = 'Используется в заголовке большинства страниц';
$lang['parsetags'] = 'Название гильдии для анализа логов';
$lang['parsetags_note'] = 'Указанные в списке элементы будут использоваться при разборе логов Рейдов';
$lang['domain_name'] = 'Название домена';
$lang['server_port'] = 'Порт сервера';
$lang['server_port_note'] = 'Порт вашего веб-сервера. Обычно это 80';
$lang['script_path'] = 'Путь к скрипту';
$lang['script_path_note'] = 'Путь к расположению EQdkp, относительно имени домена';
$lang['site_name'] = 'Название сайта';
$lang['site_description'] = 'Описание сайта';
$lang['point_name'] = 'Название единицы';
$lang['point_name_note'] = 'Пример: DKP, RP, ДКП и т.п.';
$lang['enable_account_activation'] = 'Включить активацию учетных записей';
$lang['none'] = 'Нет';
$lang['admin'] = 'Администратор';
$lang['default_language'] = 'Язык по умолчанию';
$lang['default_locale'] = 'Локаль по умолчанию';
$lang['default_game'] = 'Игра по умолчанию';
$lang['default_game_warn'] = 'Изменение настроек умолчанию может отменить некоторые изменения в текущей сессии';
$lang['default_style'] = 'Стиль по умолчанию';
$lang['default_page'] = 'Заглавная страница по умолчанию';
$lang['hide_inactive'] = 'Скрыть неактивных участников';
$lang['hide_inactive_note'] = 'Скрыть Участников, не участвовавших в Рейдах неопределенный период дней?';
$lang['inactive_period'] = 'Период неактивности';
$lang['inactive_period_note'] = 'Количество дней, в течение которых участник не посещал Рейды перед тем, как стал неактивным';
$lang['inactive_point_adj'] = 'DKP неактивных';
$lang['inactive_point_adj_note'] = 'Описание DKP для неактивных участников';
$lang['active_point_adj'] = 'DKP активных';
$lang['active_point_adj_note'] = 'Описание DKP для активных участников';
$lang['enable_gzip'] = 'Включить сжатие Gzip';
$lang['show_item_stats'] = 'Показать характеристики предметов';
$lang['show_item_stats_note'] = 'Пытается найти характеристики предметов в интернете. Может влиять на скорость загрузки некоторых страниц';
$lang['default_permissions'] = 'Права по умолчанию';
$lang['default_permissions_note'] = 'Права доступа по умолчанию для всех вновь зарегистрированных пользователей . Пункты, выделенные <b>жирным шрифтом</b>, являются правами администрирования,
                                     настоятельно рекомендуется не выставлять данные права по умолчанию. Пункты, выделенные <i>курсивом</i>, используются только для плагинов. В дальнейшем вы можете выставлять права персонально для каждого пользователя';
$lang['plugins'] = 'Плагины';
$lang['no_plugins'] = 'Папка с плагинами (./plugins/) пуста';
$lang['cookie_settings'] = 'Настройки Cookie';
$lang['cookie_domain'] = 'Домен Cookie';
$lang['cookie_name'] = 'Название Cookie';
$lang['cookie_path'] = 'Путь Cookie';
$lang['session_length'] = 'Продолжительность сессии (в секундах)';
$lang['email_settings'] = 'Настройки E-Mail';
$lang['admin_email'] = 'Адрес E-Mail администратора';
$lang['backup_options'] = 'Опция резервной копии';

// Admin Index
$lang['anonymous'] = 'Аноним';
$lang['database_size'] = 'Размер базы данных';
$lang['eqdkp_started'] = 'Дата запуска EQdkp';
$lang['ip_address'] = 'IP-адрес';
$lang['items_per_day'] = 'Предметов в день';
$lang['last_update'] = 'Последнее обновление';
$lang['location'] = 'Местонахождение';
$lang['new_version_notice'] = "Версия EQdkp %1\$s <a href=\"http://sourceforge.net/project/showfiles.php?group_id=69529\" target=\"_blank\">доступна для скачивания</a>";
$lang['number_of_items'] = 'Количество предметов';
$lang['number_of_logs'] = 'Количество пунктов в логе';
$lang['number_of_members'] = 'Количество участников (Активных / Неактивных)';
$lang['number_of_raids'] = 'Количество Рейдов';
$lang['raids_per_day'] = 'Рейдов в день';
$lang['statistics'] = 'Статистика';
$lang['totals'] = 'Отчеты';
$lang['version_update'] = 'Обновление версии';
$lang['who_online'] = 'Кто активен';

// Style Management
$lang['style_settings'] = 'Настройки стиля';
$lang['style_name'] = 'Название стиля';
$lang['template'] = 'Шаблон';
$lang['element'] = 'Элемент';
$lang['background_color'] = 'Цвет фона';
$lang['fontface1'] = 'Тип шрифта 1';
$lang['fontface1_note'] = 'Тип шрифта по умолчанию';
$lang['fontface2'] = 'Тип шрифта 2';
$lang['fontface2_note'] = 'Тип шрифта для полей ввода';
$lang['fontface3'] = 'Тип шрифта 2';
$lang['fontface3_note'] = 'В данный момент не используется';
$lang['fontsize1'] = 'Размер шрифта 1';
$lang['fontsize1_note'] = 'Маленький';
$lang['fontsize2'] = 'Размер шрифта 2';
$lang['fontsize2_note'] = 'Средний';
$lang['fontsize3'] = 'Размер шрифта 3';
$lang['fontsize3_note'] = 'Крупный';
$lang['fontcolor1'] = 'Цвет шрифта 1';
$lang['fontcolor1_note'] = 'Цвет по умолчанию';
$lang['fontcolor2'] = 'Цвет шрифта 2';
$lang['fontcolor2_note'] = 'Цвет, используемый за пределами таблицы (меню, заголовки, копирайт)';
$lang['fontcolor3'] = 'Цвет шрифта 2';
$lang['fontcolor3_note'] = 'Цвет шрифта для полей ввода';
$lang['fontcolor_neg'] = 'Отрицательный цвет шрифта';
$lang['fontcolor_neg_note'] = 'Цвет для отрицательных/плохих чисел';
$lang['fontcolor_pos'] = 'Положительный цвет шрифта';
$lang['fontcolor_pos_note'] = 'Цвет для положительных/хороших чисел';
$lang['body_link'] = 'Link цвет';
$lang['body_link_style'] = 'Link стиль';
$lang['body_hlink'] = 'Hover Link цвет';
$lang['body_hlink_style'] = 'Hover Link стиль';
$lang['header_link'] = 'Цвет ссылки';
$lang['header_link_style'] = 'Стиль ссылки';
$lang['header_hlink'] = 'Цвет ссылки при наведении';
$lang['header_hlink_style'] = 'Стиль ссылки при наведении';
$lang['tr_color1'] = 'Цвет ряда таблицы 1';
$lang['tr_color2'] = 'Цвет ряда таблицы 2';
$lang['th_color1'] = 'Цвет заголовка таблицы';
$lang['table_border_width'] = 'Ширина рамки таблицы';
$lang['table_border_color'] = 'Цвет рамки таблицы';
$lang['table_border_style'] = 'Стиль рамки таблицы';
$lang['input_color'] = 'Цвет фона полей для ввода';
$lang['input_border_width'] = 'Ширина рамки полей для ввода';
$lang['input_border_color'] = 'Цвет рамки полей для ввода';
$lang['input_border_style'] = 'Стиль рамки полей для ввода';
$lang['style_configuration'] = 'Конфигурация стилей';
$lang['style_date_note'] = 'Для полей даты/времени, синтаксис идентичен используемой в PHP <a href="http://www.php.net/manual/en/function.date.php" target="_blank">date()</a> функции';
$lang['attendees_columns'] = 'Столбцы примечания';
$lang['attendees_columns_note'] = 'Количество столбцов используемых для примечания при просмотре Рейда';
$lang['date_notime_long'] = 'Дата без времени (длинная)';
$lang['date_notime_short'] = 'Дата без времени (короткая)';
$lang['date_time'] = 'Дата со временем';
$lang['logo_path'] = 'Имя файла логотипа';

// Errors
$lang['error_invalid_adjustment'] = 'Подходящее изменение не было предоставлено';
$lang['error_invalid_plugin']     = 'Подходящий плагин не был предоставлен';
$lang['error_invalid_style']      = 'Подходящий стиль не был предоставлен';

// Verbose log entry lines
$lang['new_actions']           = 'Последние действия Администрации';
$lang['vlog_event_added']      = "%1\$s добавил событие '%2\$s' имеющее стоимость в %3\$.2f очков";
$lang['vlog_event_updated']    = "%1\$s обновил событие '%2\$s'";
$lang['vlog_event_deleted']    = "%1\$s удалил событие '%2\$s'";
$lang['vlog_groupadj_added']   = "%1\$s добавил групповое изменение в %2\$.2f очков";
$lang['vlog_groupadj_updated'] = "%1\$s обновил групповое изменение в %2\$.2f очков";
$lang['vlog_groupadj_deleted'] = "%1\$s удалил групповое изменение в %2\$.2f очков";
$lang['vlog_history_transfer'] = "%1\$s перенес историю %2\$s в %3\$s.";
$lang['vlog_indivadj_added']   = "%1\$s добавил индивидуальное изменение %2\$.2f для %3\$d участников";
$lang['vlog_indivadj_updated'] = "%1\$s обновил индивидуальное изменение %2\$.2f для %3\$s.";
$lang['vlog_indivadj_deleted'] = "%1\$s удалил индивидуальное изменение %2\$.2f для %3\$s.";
$lang['vlog_item_added']       = "%1\$s добавил предмет '%2\$s' занесенный %3\$d участникам за %4\$.2f очков";
$lang['vlog_item_updated']     = "%1\$s обновил предмет '%2\$s' занесенный %3\$d участникам";
$lang['vlog_item_deleted']     = "%1\$s удалил предмет '%2\$s' занесенный %3\$d участникам";
$lang['vlog_member_added']     = "%1\$s добавил участника %2\$s.";
$lang['vlog_member_updated']   = "%1\$s обновил участника %2\$s.";
$lang['vlog_member_deleted']   = "%1\$s удалил участника %2\$s.";
$lang['vlog_news_added']       = "%1\$s добавил новость '%2\$s'.";
$lang['vlog_news_updated']     = "%1\$s обновил новость '%2\$s'.";
$lang['vlog_news_deleted']     = "%1\$s удалил новость '%2\$s'.";
$lang['vlog_raid_added']       = "%1\$s добавил Рейд на '%2\$s'.";
$lang['vlog_raid_updated']     = "%1\$s обновил Рейд на '%2\$s'.";
$lang['vlog_raid_deleted']     = "%1\$s удалил Рейд на '%2\$s'.";
$lang['vlog_turnin_added']     = "%1\$s совершил передачу предмета '%4\$s' от участника %2\$s к участнику %3\$s";

// Location messages
$lang['adding_groupadj'] = 'Добавляется групповое изменение';
$lang['adding_indivadj'] = 'Добавляется индивидуальное изменение';
$lang['adding_item'] = 'Добавляется предмет';
$lang['adding_news'] = 'Добавляется новость';
$lang['adding_raid'] = 'Добавляется Рейд';
$lang['adding_turnin'] = 'Передается предмет';
$lang['editing_groupadj'] = 'Редактируется групповое изменение';
$lang['editing_indivadj'] = 'Редактируется индивидуальное изменение';
$lang['editing_item'] = 'Редактируется предмет';
$lang['editing_news'] = 'Редактируется новость';
$lang['editing_raid'] = 'Редактируется Рейд';
$lang['listing_events'] = 'Список событий';
$lang['listing_groupadj'] = 'Список групповых изменений';
$lang['listing_indivadj'] = 'Список индивидуальных изменений';
$lang['listing_itemhist'] = 'История предметов';
$lang['listing_itemvals'] = 'Стоимость предметов';
$lang['listing_members'] = 'Список участников';
$lang['listing_raids'] = 'Список Рейдов';
$lang['managing_config'] = 'Управление конфигурацией EQdkp';
$lang['managing_members'] = 'Управление Участниками Гильдии';
$lang['managing_plugins'] = 'Управление плагинами';
$lang['managing_styles'] = 'Управление стилями';
$lang['managing_users'] = 'Управление Учетными записями пользователей';
$lang['parsing_log'] = 'Синтаксический анализ лога';
$lang['viewing_admin_index'] = 'Просмотр главной Админцентра';
$lang['viewing_event'] = 'Просмотр события';
$lang['viewing_item'] = 'Просмотр предметов';
$lang['viewing_logs'] = 'Просмотр логов';
$lang['viewing_member'] = 'Просмотр участника';
$lang['viewing_mysql_info'] = 'Просмотр информации MySQL';
$lang['viewing_news'] = 'Просмотр новостей';
$lang['viewing_raid'] = 'Просмотр Рейдов';
$lang['viewing_stats'] = 'Просмотр статистики';
$lang['viewing_summary'] = 'Просмотр отчета';

// Help lines
$lang['b_help'] = 'Жирный текст: [b]текст[/b] (alt+b)';
$lang['i_help'] = 'Текст курсивом: [i]текст[/i] (alt+i)';
$lang['u_help'] = 'Подчеркнутый текст: [u]текст[/u] (alt+u)';
$lang['q_help'] = 'Текст цитаты: [quote]текст[/quote] (alt+q)';
$lang['c_help'] = 'Текст по центру: [center]текст[/center] (alt+c)';
$lang['p_help'] = 'Вставить изображение: [img]http://url_изображения[/img] (alt+p)';
$lang['w_help'] = 'Вставить ссылку: [url]http://ссылка[/url] или [url=http://ссылка]текст ссылки[/url]  (alt+w)';
$lang['it_help'] = 'Insert Item: e.g. [item]Judgement Breastplate[/item] (shift+alt+t)';
$lang['ii_help'] = 'Insert ItemIcon: e.g. [itemicon]Judgement Breastplate[/itemicon] (shift+alt+o)';

// Manage Members Menu (yes, MMM)
$lang['add_member'] = 'Добавить нового участника';
$lang['list_edit_del_member'] = 'Вывести список, редактировать, удалить участников';
$lang['edit_ranks'] = 'Редактировать ранги';
$lang['transfer_history'] = 'Перенести историю пользователя';

// MySQL info
$lang['mysql'] = 'MySQL';
$lang['mysql_info'] = 'Информация MySQL';
$lang['eqdkp_tables'] = 'Таблицы EQdkp';
$lang['table_name'] = 'Название таблицы';
$lang['rows'] = 'Строки';
$lang['table_size'] = 'Размер таблицы';
$lang['index_size'] = 'Размер индекса';
$lang['num_tables'] = "%d таблицы";

//Backup
$lang['backup'] = 'Резервное копирование';
$lang['backup_title'] = 'Создание резервной копии базы данных';
$lang['create_table'] = 'Добавить параметр \'CREATE TABLE\'?';
$lang['skip_nonessential'] = 'Игнорировать несущественную информацию?<br />Не будет выполнять копирование строк таблицы сессий eqdkp_sessions';
$lang['gzip_content'] = 'Архивировать в GZIP?<br />Если включен GZIP, создаст меньшую по объему копию БД в архиве';
$lang['backup_database'] = 'Создание копии базы данных';

// plus
$lang['in_database']  = 'Сохранить в базе данных';

//Log Users Actions
$lang['action_user_added']     = 'Пользователь добавлен';
$lang['action_user_deleted']   = 'Пользователь удален';
$lang['action_user_updated']   = 'Пользователь обновлен';
$lang['vlog_user_added']     = "%1\$s Добавил пользователя %2\$s.";
$lang['vlog_user_updated']   = "%1\$s Обновил пользователя %2\$s.";
$lang['vlog_user_deleted']   = "%1\$s Удалил пользователя %2\$s.";

//MultiDKP
$lang['action_multidkp_added']     = "MultiDKP Pool добавлен";
$lang['action_multidkp_deleted']   = "MultiDKP Pool Удален";
$lang['action_multidkp_updated']   = "MultiDKP Pool Обновлен";
$lang['action_multidkp_header']    = "MultiDKP";

$lang['vlog_multidkp_added']     = "%1\$s добавил MultiDKP Pool %2\$s";
$lang['vlog_multidkp_updated']   = "%1\$s обновил MultiDKP Pool %2\$s.";
$lang['vlog_multidkp_deleted']   = "%1\$s удалил MultiDKP Pool %2\$s.";

$lang['default_style_overwrite']   = "Каждый вновь зарегистрированный пользователь использует стиль по умолчанию";

$lang['plugin_inst_sql_note'] = 'An SQL error during install does not necessary implies a broken plugin installation. Try using the plugin, if errors occur please de- and reinstall the plugin.';
//---- Main ----
$lang['pluskernel']          	= 'PLUS Конфигурация';
$lang['pk_adminmenu']         	= 'PLUS Конфигурация';
$lang['pk_settings']						= 'Настройки';
$lang['pk_date_settings']			= 'День.Месяц.Год';

//---- Javascript stuff ----
$lang['pk_plus_about']					= 'О EQDKP PLUS';
$lang['updates']								= 'Доступно обновление';
$lang['loading']								= 'Загрузка...';
$lang['pk_config_header']			= 'EQDKP PLUS Настройки';
$lang['pk_close_jswin1']      	= 'Закройте';
$lang['pk_close_jswin2']     	= 'данное окно перед открытием снова!';
$lang['pk_help_header']				= 'Помощь';

//---- Updater Stuff ----
$lang['pk_alt_attention']			= 'Attention';
$lang['pk_alt_ok']							= 'Everything OK!';
$lang['pk_updates_avail']			= 'Доступно обновление';
$lang['pk_updates_navail']			= 'Нет доступных обновлений';
$lang['pk_no_updates']					= 'Ваша версия является самой новой. Отсутствуют новые доступные версии.';
$lang['pk_act_version']				= 'Новая версия';
$lang['pk_inst_version']				= 'Установлено';
$lang['pk_changelog']					= 'Changelog';
$lang['pk_download']						= 'Скачать';
$lang['pk_upd_information']		= 'Информация';
$lang['pk_enabled']						= 'Активировано';
$lang['pk_disabled']						= 'Деактивировано';
$lang['pk_auto_updates1']			= 'The automatic update warning is';
$lang['pk_auto_updates2']			= 'If you disabled this setting, please recheck regulary for updates to prevent hacks and stay up to date..';
$lang['pk_module_name']				= 'Имя модуля';
$lang['pk_plugin_level']				= 'Уровень';
$lang['pk_release_date']				= 'Release';
$lang['pk_alt_error']					= 'Ошибка';
$lang['pk_no_conn_header']			= 'Ошибка соединения';
$lang['pk_no_server_conn']			= 'Произошла ошибка при попытке соединения с сервером обновления, ваш хост не позволяет экспортные подключения
 или ошибка была вызвана проблемой в глобальной сети. Пожалуйста, посетите  eqdkp-plugin-forum , чтобы убедиться, что у вас последняя версия.';


$lang['pk_reset_warning']			= 'Reset Warning';

//---- Update Levels ----
$lang['pk_level_other']				= 'Другое';
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
$lang['pk_version']						= 'Версия';
$lang['pk_prodcutname']				= 'Продукт';
$lang['pk_modification']				= 'Мод';
$lang['pk_tname']							= 'Шаблон';
$lang['pk_developer']					= 'Разработчик';
$lang['pk_plugin']							= 'Плагин';
$lang['pk_weblink']						= 'веб-ссылка';
$lang['pk_phpstring']					= 'PHP Строка';
$lang['pk_phpvalue']						= 'Значение';
$lang['pk_donation']						= 'Пожертвование';
$lang['pk_job']								= 'Работа';
$lang['pk_sitename']						= 'Сайт';
$lang['pk_dona_name']					= 'Имя';
$lang['pk_betateam1']					= 'Команда бета-тестеров (Германия)';
$lang['pk_betateam2']					= 'Хронологический порядок';
$lang['pk_created by']					= 'Создано';
$lang['web_url']								= 'Веб-страница';
$lang['personal_url']					= 'Частный';
$lang['pk_credits']						= 'Разработчики';
$lang['pk_sponsors']						= 'Спонсоры';
$lang['pk_plugins']						= 'Плагины';
$lang['pk_modifications']			= 'Моды';
$lang['pk_themes']							= 'Стили';
$lang['pk_additions']					= 'Добавление кода';
$lang['pk_tab_stuff']					= 'EQDKP Команда';
$lang['pk_tab_help']						= 'Помощь';
$lang['pk_tab_tech']						= 'Технология';

//---- Settings ----
$lang['pk_save']								= 'Сохранить';
$lang['pk_save_title']					= '';
$lang['pk_succ_saved']					= 'Настройки были успешно сохранены';
 // Tabs
$lang['pk_tab_global']					= 'Глобально';
$lang['pk_tab_multidkp']				= 'multiDKP';
$lang['pk_tab_links']					= 'Ссылки';
$lang['pk_tab_bosscount']			= 'BossCounter';
$lang['pk_tab_listmemb']				= 'Список участников';
$lang['pk_tab_itemstats']			= 'Статистика предметов';
// Global
$lang['pk_set_QuickDKP']				= 'Показать быстрого DKP';
$lang['pk_set_Bossloot']				= 'Показать трофеи с боссов ?';
$lang['pk_set_ClassColor']			= 'Colored ClassClassnames';
$lang['pk_set_Updatecheck']		= 'Включить авто-обновление';
$lang['pk_window_time1']				= 'Показ каждого окна';
$lang['pk_window_time2']				= 'Минуты';
// MultiDKP
$lang['pk_set_multidkp']				= 'Включить MultiDKP';
// Listmembers
$lang['pk_set_leaderboard']		= 'Показывать Leaderboard';
$lang['pk_set_lb_solo']				= 'Показывать Leaderboard per account';
$lang['pk_set_rank']						= 'Показывать Ранг';
$lang['pk_set_rank_icon']			= 'Показывать иконку Ранга';
$lang['pk_set_level']					= 'Показывать Уровень';
$lang['pk_set_lastloot']				= 'Показывать последний трофей';
$lang['pk_set_lastraid']				= 'Показывать последний Рейд';
$lang['pk_set_attendance30']		= 'Показывать Рейд Attendance 30 дней';
$lang['pk_set_attendance60']		= 'Показывать Рейд Attendance 60 дней';
$lang['pk_set_attendance90']		= 'Показывать Рейд Attendance 90 дней';
$lang['pk_set_attendanceAll']	= 'Показывать Рейд Attendance Lifetime';
// Links
$lang['pk_set_links']					= 'Активировать Links';
$lang['pk_set_linkurl']				= 'Link URL';
$lang['pk_set_linkname']				= 'Link названия';
$lang['pk_set_newwindow']			= 'открывать в новом окне?';
// BossCounter
$lang['pk_set_bosscounter']		= 'Показать Bosscounter';
//Itemstats
$lang['pk_set_itemstats']			= 'Включить статистику предметов';
$lang['pk_is_language']				= 'Язык статистики предметов';
$lang['pk_german']							=	'Немецкий';
$lang['pk_english']						= 'Английский';
$lang['pk_french']							= 'Французский';
$lang['pk_set_icon_ext']				= '';
$lang['pk_set_icon_loc']				= '';
$lang['pk_set_en_de']					= 'Перевод предметов с Английского на Немецкий';
$lang['pk_set_de_en']					= 'Перевод предметов с Немецкого на Английский';

################
# new sort
###############

//MultiDKP
//

$lang['pk_set_multi_Tooltip']						= 'Показывать DKP совет';
$lang['pk_set_multi_smartTooltip']			  = 'Шикарный совет';

//Help
$lang['pk_help_colorclassnames']				  = "If activated, the players will be shown with the WoW colors of their class and their class icon.";
$lang['pk_help_quickdkp']								= "Shows the logged-in user the points off all members that are assigned to him above the menu.";
$lang['pk_help_boosloot']								= "Если активировано, you can click the boss names in the Рейд notes and the bosscounter to have a detailed overview of the dropped items. If inactive, it will be linked to Blasc.de (Only activate if you enter a Рейд for each single boss)";
$lang['pk_help_autowarning']             = "Warns the administrator when he logs in, if updates are available.";
$lang['pk_help_warningtime']             = "How often should the warning appear?";
$lang['pk_help_multidkp']								= "MultiDKP allows the management and overview of seperate accounts. Activates the management and overview of the MultiDKP accounts.";
$lang['pk_help_dkptooltip']							= "If activated, a tooltip with detailed information about the calculation of the points will be shown, when the mouse hovers over the different points.";
$lang['pk_help_smarttooltip']						= "Shortened overview of the tooltips (activate if you got more than three events per account)";
$lang['pk_help_links']                   = "In this menu you are able to define different links, which will be displayed in the main menu.";
$lang['pk_help_bosscounter']             = "If activated, a table will be displayed below the main menu with the bosskills. The administration is being managed by the plugin Bossprogress";
$lang['pk_help_lm_leaderboard']					= "If activated, a leaderboard will be displayed above the scoretable. A leaderboard is a table, where the dkp of each class is displayed sorted in decending order.";
$lang['pk_help_lm_rank']                 = "An extra column is being displayed, which displays the rank of the member.";
$lang['pk_help_lm_rankicon']             = "Instead of the rank name, an icon is being displayed. Which items are available you can check in the folder \images\rank";
$lang['pk_help_lm_level']								= "An additional column is being displayed, which displays the level of the member. ";
$lang['pk_help_lm_lastloot']             = "An extra colums is being displayed, showing the date a member received his latest item.";
$lang['pk_help_lm_lastraid']             = "An extra column is being displayed, showing the date of the latest Рейд a member has been participated in.";
$lang['pk_help_lm_atten30']							= "An extra column is being displayed, showing a members participation in Рейд during the last 30 days (in percent).";
$lang['pk_help_lm_atten60']							= "An extra column is being displayed, showing a members participation in Рейд during the last 60 days (in percent). ";
$lang['pk_help_lm_atten90']							= "An extra column is being displayed, showing a members participation in Рейд during the last 90 days (in percent). ";
$lang['pk_help_lm_attenall']             = "An extra column is being displayed, showing a members overall Рейд participation (in percent).";
$lang['pk_help_itemstats_on']						= "Itemstats is requesting information about items entered in EQDKP in the WOW databases (Blasc, Allahkazm, Thottbot). These will be displayed in the color of the items quality including the known WOW tooltip. When active, items will be shown with a mouseover tooltip, similar to WOW.";
$lang['pk_help_itemstats_search']				= "Which database should Itemstats use first to lookup information? Blasc or Allakhazam?";
$lang['pk_help_itemstats_icon_ext']			= "Filename extension of the pictures to be shown. Usually .png or .jpg.";
$lang['pk_help_itemstats_icon_url']      = "Please enter the URL where you Itemstats pictures are being located. German: http://www.buffed.de/images/wow/32/ in 32x32 or http://www.buffed.de/images/wow/64/ in 64x64 pixels.English at Allakzam: http://www.buffed.de/images/wow/32/";
$lang['pk_help_itemstats_translate_deeng']		= "If active, information of the tooltips will be requested in german, even when the item is being entered in english.";
$lang['pk_help_itemstats_translate_engde']		= "If active, information of the tooltips will be requested in English, even if the item is being entered in german.";

$lang['pk_set_leaderboard_2row']					= 'Leaderboard in 2 lines';
$lang['pk_help_leaderboard_2row']        = 'If active, the Leaderboard will be displayed in two lines with 4 or 5 classes each.';

$lang['pk_set_leaderboard_limit']        = 'limitation of the display';
$lang['pk_help_leaderboard_limit']				= 'If a numeric number is being entered, the Leaderboard will be restricted to the entered number of members. The number 0 represents no restrictions.';

$lang['pk_set_leaderboard_zero']         = 'Hide Player with zeor DKP';
$lang['pk_help_leaderboard_zero']        = 'If activated, Players with zero DKp doesnt show in the leaderboard.';


$lang['pk_set_newsloot_limit']						= 'newsloot limit';
$lang['pk_help_newsloot_limit']          = 'How many items should be displayed in the news? This restricts the display of items, which will be displeyed in the news. The number 0 represents no restrictions.';

$lang['pk_set_itemstats_debug']          = 'debug Modus';
$lang['pk_help_itemstats_debug']					= 'If activated, Itemstats will log all transactions to /itemstats/includes_de/debug.txt. This file has to be writable, CHMOD 777 !!!';

$lang['pk_set_showclasscolumn']          = 'show classes column';
$lang['pk_help_showclasscolumn']					= 'If activated, an extra column is being displayed showing the class of the player.' ;

$lang['pk_set_show_skill']								= 'show skill column';
$lang['pk_help_show_skill']              = 'If activated, an extra column is being displayed showing the skill of the player.';

$lang['pk_set_show_arkan_resi']          = 'show arcan resistance column';
$lang['pk_help_show_arkan_resi']					= 'If activated, an extra column is being displayed showing the arcane resistance of the player.';

$lang['pk_set_show_fire_resi']						= 'show fire resistance column';
$lang['pk_help_show_fire_resi']          = 'If activated, an extra column is being displayed showing the fire resistance of the player.';

$lang['pk_set_show_nature_resi']					= 'show nature resistance column';
$lang['pk_help_show_nature_resi']        = 'If activated, an extra column is being displayed showing the nature resistance of the player.';

$lang['pk_set_show_ice_resi']            = 'show ice resistance column';
$lang['pk_help_show_ice_resi']						= 'If activated, an extra column is being displayed showing the frost resistance of the player.';

$lang['pk_set_show_shadow_resi']					= 'show shadow resistance column';
$lang['pk_help_show_shadow_resi']        = 'If activated, an extra column is being displayed showing the shadow resistance of the player.';

$lang['pk_set_show_profils']							= 'show profile link column';
$lang['pk_help_show_profils']            = 'If activated, an extra column is being displayed showing the links to the profile.';

$lang['pk_set_servername']               = 'Название Сервера';
$lang['pk_help_servername']              = 'Введите ваше название сервера здесь.';

$lang['pk_set_server_region']			  = 'Регион';
$lang['pk_help_server_region']			  = 'Американский или Европейский сервер.';


$lang['pk_help_default_multi']           = 'Choose the default DKP Acc for the leaderboard.';
$lang['pk_set_default_multi']            = 'Установить default for leaderboard';

$lang['pk_set_round_activate']           = 'Раунд DKP.';
$lang['pk_help_round_activate']          = 'If activated, DKP Point displayed rounded. 125.00 = 125DKP.';

$lang['pk_set_round_precision']          = 'Decimal place to round.';
$lang['pk_help_round_precision']         = 'Set the Decimal place to round the DKP Defualt=0';

$lang['pk_is_set_prio']                  = 'Priority of Itemdatabase';
$lang['pk_is_help_prio']                 = 'Set the query order of item databases.';

$lang['pk_is_set_alla_lang']	            = 'Language of Itemnames on Allakhazam.';
$lang['pk_is_help_alla_lang']	          = 'Which language should the requested items be?';

$lang['pk_is_set_lang']		              = 'Standard language of Item ID\'s.';
$lang['pk_is_help_lang']		              = 'Standard language of Item IDs. Example : [item]17182[/item] will choose this language';

$lang['pk_is_set_autosearch']            = 'Immediate Search';
$lang['pk_is_help_autosearch']           = 'Activated: If the item is not in the cache, search for the item information automatically. Not activated: Item information is only fetch on click on the item information.';

$lang['pk_is_set_integration_mode']      = 'Integration Modus';
$lang['pk_is_help_integration_mode']     = 'Normal: scanning text and setting tooltip in html code. Script: scanning text and set <script> tags.';

$lang['pk_is_set_tooltip_js']            = 'Look of Tooltips';
$lang['pk_is_help_tooltip_js']           = 'Overlib: The normal Tooltip. Light: Light version, faster loading times.';

$lang['pk_is_set_patch_cache']           = 'Cache Path';
$lang['pk_is_help_patch_cache']          = 'Path to the user item cache, starting from /itemstats/. Default=./xml_cache/';

$lang['pk_is_set_patch_sockets']         = 'Path of Socketpictures';
$lang['pk_is_help_patch_sockets']        = 'Path to the picture files of the socket items.';

$lang['pk_set_dkp_info']			  = 'Не показывать DKP информацию в главном меню.';
$lang['pk_help_dkp_info']			  = 'If activated "DKP Info" will be hidden from the main menu.';

$lang['pk_set_debug']			= 'Включить Eqdkp Debug Modus';
$lang['pk_set_debug_type']		= 'Мод';
$lang['pk_set_debug_type0']	= 'Debug off (Debug=0)';
$lang['pk_set_debug_type1']	= 'Debug on simple (Debug=1)';
$lang['pk_set_debug_type2']	= 'Debug on with SQL Queries (Debug=2)';
$lang['pk_set_debug_type3']	= 'Debug on extended (Debug=3)';
$lang['pk_help_debug']			= 'Если активировано, Eqdkp Plus will be running in debug mode, showing additional informations and error messages. Deaktivate if plugins abort with SQL error messages! 1=Rendering time, Query count, 2=SQL outputs, 3=Enhanced error messages.';

#RSS News
$lang['pk_set_Show_rss']			= 'Деактивировать RSS Новости';
$lang['pk_help_Show_rss']			= 'If activated, Eqdkp Plus will be show Game RSS News.';

$lang['pk_set_Show_rss_style']		= 'game-news positioning';
$lang['pk_help_Show_rss_style']	= 'RSS-Game News positioning. Top horizontal, in the menu vertical or both?';

$lang['pk_set_Show_rss_lang']		= 'RSS-Новости язык по умолчанию';
$lang['pk_help_Show_rss_lang']		= 'Get the RSS-News in wich language? (atm only german). English news available beginning 2008.';

$lang['pk_set_Show_rss_lang_de']	= 'Немецкий';
$lang['pk_set_Show_rss_lang_eng']	= 'Английский';


$lang['pk_set_Show_rss_style_both'] = 'Оба' ;
$lang['pk_set_Show_rss_style_v']	 = 'Вертикальное меню' ;
$lang['pk_set_Show_rss_style_h']	 = 'По горизонтали' ;

$lang['pk_set_Show_rss_count']		= 'Новости цен (0 or "" for all)';
$lang['pk_help_Show_rss_count']	= 'Wieviele News sollen angezeigt werden?';

$lang['pk_set_itemhistory_dia']	= 'Dont show diagrams'; # Ja negierte Abfrage
$lang['pk_help_itemhistory_dia']	= 'If activated, Eqdkp Plus show sseveral diagramm.';


#Bridge
$lang['pk_set_bridge_help']				= 'On This Tab you can tune up the settings to let an Content Management System (CMS) interact with Eqdkp Plus.
											   If you choose one of the Systems in the Drop Down Field , Registered Members of your Forum/CMS will be able to log in into Eqdkp Plus with the same credentials used in Forum/CMS.
											   The Access is only allowed for one Group, that Means that you must create a new group in your CMS/Forum which all Members belong who will be accessing Eqdkp.';

$lang['pk_set_bridge_activate']			= 'Активировать соединение с CMS';
$lang['pk_help_bridge_activate']			= 'When bridging is activated, Users of the Forum or CMS will be able to Log On in Eqdkp Plus with the same credentials as used in CMS/Forum';

$lang['pk_set_bridge_dectivate_eq_reg']	= 'Деактивировать регистрацию в Eqdkp Plus';
$lang['pk_help_bridge_dectivate_eq_reg']	= 'Когда Деактивировано новые Участники не могут регистрироваться в Eqdkp Plus. Регистрация новых Пользователей должна быть сделана в CMS/Форум';

$lang['pk_set_bridge_cms']					= 'Поддержка CMS';
$lang['pk_help_bridge_cms']				= 'Which CMS shall be bridged ';

$lang['pk_set_bridge_acess']				= 'Is the CMS/Forum on another Database than Eqdkp ?';
$lang['pk_help_bridge_acess']				= 'Если Вы используете CMS/Forum on another Database activate this and fill the Fields below';

$lang['pk_set_bridge_host']				= 'Хост';
$lang['pk_help_bridge_host']				= 'Название хоста или IP-адерсс на котором установлена база данных';

$lang['pk_set_bridge_username']			= 'Имя пользователя базы данных';
$lang['pk_help_bridge_username']			= 'Имя пользователя, используемое для подключения к базе данных';

$lang['pk_set_bridge_password']			= 'Пароль базы данных';
$lang['pk_help_bridge_password']			= 'Пароль пользователя для соединения';

$lang['pk_set_bridge_database']			= 'Название базы данных';
$lang['pk_help_bridge_database']			= 'Название базы данных, где CMS данные находится';

$lang['pk_set_bridge_prefix']				= 'Tableprefix of your CMS Installation';
$lang['pk_help_bridge_prefix']				= 'Give your Prefix of your CMS . e.g.. phpbb_ or wcf1_';

$lang['pk_set_bridge_group']				= 'ID Группы для CMS Группы';
$lang['pk_help_bridge_group']				= 'Введите здесь ID для Группы в CMS, который предоставляет доступ к Eqdkp.';


$lang['pk_set_bridge_inline']				= 'Интеграция вашего форума в EQDKP - Внимание BETA !';
$lang['pk_help_bridge_inline']				= 'Если вы вносите здесь URL, тогда дополнительная ссылка будет видна в Меню, которая будет ссылаться на указанную страницу в пределах Eqdkp. Это происходит в динамичном Iframe. Das EQDKP Plus ist aber nicht verantworltich fьr das Aussehen bzw. das Verhalten der eingebundenen Seite innerhalb eines Iframs!';

$lang['pk_set_bridge_inline_url']			= 'URL на ваш форум';
$lang['pk_help_bridge_inline_url']			= 'URL на ваш форум, в пределах представления EQDKP';

$lang['pk_set_link_type_header']			= 'Как страница должна открываться';
$lang['pk_set_link_type_help']				= 'Link im selben Browserfenster, in einem neuen Brwoserfenster oder innerhalb des Eqdkps in einem Iframe цffnen?';
$lang['pk_set_link_type_iframe_help']		= 'Указанная страница будет показана в пределах Eqdkp. Это происходит в динамичном Iframe. Das EQDKP Plus ist aber nicht verantworltich fьr das Aussehen bzw. das Verhalten der eingebundenen Seite innerhalb eines Iframs!';
$lang['pk_set_link_type_self']				= 'Норма';
$lang['pk_set_link_type_link']				= 'Новое окно';
$lang['pk_set_link_type_iframe']			= 'Заделано';

#recruitment
$lang['pk_set_recruitment_tab']			= 'Требования';
$lang['pk_set_recruitment_header']			= 'Требования - Are you looking for new Members ?';
$lang['pk_set_recruitment']				= 'Активизировать требования';
$lang['pk_help_recruitment']				= 'Если активно, то ящик с требуемыми классами и спецификацией будет показан на верху, на ряду с новостями.';
$lang['pk_recruitment_count']				= 'Цена';

$lang['pk_set_recruitment_contact_type']	= 'Linkurl';
$lang['pk_help_recruitment_contact_type']	= 'If no URL is given, it will link to the contact email.';
$lang['ps_recruitment_spec']				= 'Специализация';

$lang['pk_set_comments_disable']			= 'Деактивировать комментарии';
$lang['pk_hel_pcomments_disable']			= 'Деактивировать комментарии на всех страницах';

#Contact
$lang['pk_contact']						= 'Контактная информация';
$lang['pk_contact_name']					= 'Имя';
$lang['pk_contact_email']					= 'Email';
$lang['pk_contact_website']				= 'Веб-страница';
$lang['pk_contact_irc']					= 'IRC Канал';
$lang['pk_contact_admin_messenger']		= 'Название учетной записи (Skype, ICQ)';
$lang['pk_contact_custominfos']			= 'Дополнительная информация';
$lang['pk_contact_owner']					= 'Информация о Владельце:';

#Next_raids
$lang['pk_set_nextraids_deactive']			= 'Не показывать следующие Рейды';
$lang['pk_help_nextraids_deactive']		= 'Если Активно, следующие Рейды не будут показаны в меню';

$lang['pk_set_nextraids_limit']			= 'Лимит показа следующих Рейдов';
$lang['pk_help_nextraids_limit']			= '';

$lang['pk_set_lastitems_deactive']			= 'Не показывать последние предметы';
$lang['pk_help_lastitems_deactive']		= 'Если Активно, последние предметы не будут показаны в меню';

$lang['pk_set_lastitems_limit']			= 'Лимит показа последних предметов';
$lang['pk_help_lastitems_limit']			= '';

$lang['pk_is_help']						= '<b>Внимание: Изменения в работе Статистики предметов с Eqdkp Plus 0.5!</b><br><br>
											  Wenn ihr von Eqdp Plus 0.4 auf 0.5 aktualisiert oder die Such-Prioritдt geдndert habt, mьsst ihr euren Itemcache refreshen!!<br>
											  Empfohlene Prioritдt ist 1.Armory 2. WoWHead. oder 1. Buffed und 2. Allakhazam<br>
											  Eine Mischung aus Armory/WoWHead und Buffed/Allakhazam ist nicht mцglich, da die Tooltips und Icons nicht kompatibel sind!<br>
											  Zum aktualisieren des Itemcache dem Link folgen, danach die Buttons "Clear Cache" und danach "Update Itemtable" auswдhlen.<br><br>';

$lang['pk_set_normal_leaderbaord']			= 'Показывать Leaderboard со Слайдером';
$lang['pk_help_normal_leaderbaord']		= 'Если Активно,Leaderboard использует Слайдер.';

$lang['pk_set_lastraids']					= 'Не показывать последние Рейды';
$lang['pk_help_lastraid']					= 'Если Активно, последний Рейд не показан в меню';

$lang['pk_set_lastraids_limit']			= 'Последний лимит Рейдов';
$lang['pk_help_lastraid_limit']			= 'Последний лимит Рейдов';

$lang['pk_set_lastraids_showloot']			= 'Показывать предметы после последних Рейдов';
$lang['pk_help_lastraid_showloot']			= 'Показывать предметы после последних Рейдов';

$lang['pk_lastraids_lootLimit']		= 'Лимит Предметов';
$lang['pk_help_lastraid_lootLimit']		= 'Лимит Предметов';


$lang['pk_set_ts_active']					= 'activate TS Viewer';
$lang['pk_help_ts_active']					= 'activate TS Viewer';

$lang['pk_set_ts_title']					= 'TS-Сервер Заголовок';
$lang['pk_help_ts_title']					= 'TS-Сервер Заголовок';

$lang['pk_set_ts_serverAddress']			= 'TS-Сервер IP';
$lang['pk_help_ts_serverAddress']			= 'TS-Сервер IP';

$lang['pk_set_ts_serverQueryPort']			= 'Порт запроса';
$lang['pk_help_ts_serverQueryPort']		= 'Порт запроса';

$lang['pk_set_ts_serverUDPPort']			= 'UDP Порт';
$lang['pk_help_ts_serverUDPPort']			= 'UDP Порт';

$lang['pk_set_ts_serverPasswort']			= 'Пароль сервера';
$lang['pk_help_ts_serverPasswort']			= 'Пароль сервера';


$lang['pk_set_ts_channelflags']			= 'Показывать флаги канала (R,M,S,P и т.д.)';
$lang['pk_help_ts_channelflags']			= 'Показывать флаги канала (R,M,S,P и т.д.)';

$lang['pk_set_ts_userstatus']				= 'Показывать статус Участника (U,R,SA и т.д.)';
$lang['pk_help_ts_userstatus']				= 'Показывать статус Участника (U,R,SA и т.д.)';

$lang['pk_set_ts_showchannel']				= 'Показывать каналы? (0 = только Участник)';
$lang['pk_help_ts_showchannel']			= 'Показывать каналы? (0 = только Участник)';

$lang['pk_set_ts_showEmptychannel']		= 'Показывать пустые каналы?';
$lang['pk_help_ts_showEmptychannel']		= 'Показывать пустые каналы?';

$lang['pk_set_ts_overlib_mouseover']		= 'Show mouseover informations? ';
$lang['pk_help_ts_overlib_mouseover']		= 'Show mouseover informations? (german only atm, translation comes later)';

$lang['pk_set_ts_joinable']				= 'Show link to join the server?';
$lang['pk_help_ts_joinable']				= 'Show link to join the server';

$lang['pk_set_ts_joinableMember']			= 'Show the join link only registeres users?';
$lang['pk_help_ts_joinableMember']			= 'Link zum joinen nur eingelogten Usern anzeigen?';

$lang['pk_set_ts_ranking']					= 'Показывать Изображение Ранга';
$lang['pk_help_ts_ranking']				= 'Показывать Изображение Ранга';

$lang['pk_set_ts_ranking_url']				= 'Изображение Ранга URL like <a href=http://www.wowjutsu.com/>WoWJutsu</a> or <a href=http://www.bosskillers.com/>Bosskillers</a> ';
$lang['pk_help_ts_ranking_url']			= 'Изображение Ранга URL like <a href=http://www.wowjutsu.com/>WoWJutsu</a> or <a href=http://www.bosskillers.com/>Bosskillers</a> ';

$lang['pk_set_ts_ranking_link']			= 'Изображение Ранга Link';
$lang['pk_help_ts_ranking_link']			= 'Изображение Ранга Link';

$lang['pk_set_thirdColumn']				= 'Не показывайте третьему столбцу';
$lang['pk_help_thirdColumn']				= 'Не показывайте третьему столбцу';

#GetDKP
$lang['pk_getdkp_th']						= 'GetDKP настройки';

$lang['pk_set_getdkp_rp']					= 'Активировать raidplan';
$lang['pk_help_getdkp_rp']					= 'Активирование raidplan';

$lang['pk_set_getdkp_link']				= 'Показать getdkp ссылку в главном меню';
$lang['pk_help_getdkp_link']				= 'Показ getdkp ссылку в главном меню';

$lang['pk_set_getdkp_active']				= 'Деактивировать getdkp.php';
$lang['pk_help_getdkp_active']				= 'Деактивирование getdkp.php';

$lang['pk_set_getdkp_items']				= 'Отключить itemIDs';
$lang['pk_help_getdkp_items']				= 'Отключение itemIDs';

$lang['pk_set_recruit_embedded']			= 'open Link embedded';
$lang['pk_help_recruit_embedded']			= 'Если активировано, the link opens embedded i an iframe';

$lang['page_manager'] = 'Manage pages';

//maintenance mode
$lang['pk_maintenance_mode'] = 'Activate maintenance mode.';
$lang['pk_help_maintenance'] = 'Activating the maintenance mode will cause all non admin users to be redirected to a maintenance page and allows the admin to do maintenance on the eqdkp plus system.';
?>
