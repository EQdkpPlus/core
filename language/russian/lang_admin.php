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

?>
