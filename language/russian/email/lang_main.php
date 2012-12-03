<?php
/******************************
 * EQdkp
 * Copyright 2002-2003
 * Licensed under the GNU GPL.  See COPYING for full terms.
 * ------------------
 * lang_main.php
 * begin: 19.05.2008 by AvengeR(ICQ: 340-956-443)
 *
 * $Id: lang_main.php 1844 2008-03-31 23:26:35Z osr-corgan $
 ******************************/

if ( !defined('EQDKP_INC') )
{
     die('Нет доступа к данной файловой дериктории.');
}

// %1\$<type> prevents a possible error in strings caused
//      by another language re-ordering the variables
// $s is a string, $d is an integer, $f is a float

$lang['ENCODING'] = 'windows-1251';
$lang['XML_LANG'] = 'ru';

// Titles
$lang['admin_title_prefix']   = "%1\$s %2\$s Администратор";
$lang['listadj_title']        = 'Список групповых изменений';
$lang['listevents_title']     = 'Стоимость событий';
$lang['listiadj_title']       = 'Список индивидуальных изменений';
$lang['listitems_title']      = 'Стоимость предметов';
$lang['listnews_title']       = 'Новости';
$lang['listmembers_title']    = 'Просмотр информации о DKP участников';
$lang['listpurchased_title']  = 'История предметов';
$lang['listraids_title']      = 'Список RAIDs';
$lang['login_title']          = 'Логин';
$lang['message_title']        = 'EQdkp: сообщение';
$lang['register_title']       = 'Регистрация';
$lang['settings_title']       = 'Настройки eчётной записи';
$lang['stats_title']          = "%1\$s Статистика";
$lang['summary_title']        = 'Отчет по новостям';
$lang['title_prefix']         = "%1\$s %2\$s";
$lang['viewevent_title']      = "Просмотр истории RAID(ов) %1\$s";
$lang['viewitem_title']       = "Просмотр истории покупок %1\$s";
$lang['viewmember_title']     = "Просмотр информации об участнике %1\$s";
$lang['viewraid_title']       = 'Отчет по RAID';

// Main Menu
$lang['menu_admin_panel'] = 'Панель администратора';
$lang['menu_events'] = 'События';
$lang['menu_itemhist'] = 'История предметов';
$lang['menu_itemval'] = 'Стоимость предметов';
$lang['menu_news'] = 'Новости';
$lang['menu_raids'] = 'RAIDs';
$lang['menu_register'] = 'Регистрация';
$lang['menu_settings'] = 'Личные настройки';
$lang['menu_standings'] = 'Обзор участников';
$lang['menu_stats'] = 'Статистика';
$lang['menu_summary'] = 'Отчеты';

// Column Headers
$lang['account'] = 'Учётная запись';
$lang['action'] = 'Действие';
$lang['active'] = 'Активирован';
$lang['add'] = 'Добавить';
$lang['added_by'] = 'Добавил(а)';
$lang['adjustment'] = 'Изменение';
$lang['administration'] = 'Администрирование';
$lang['administrative_options'] = 'Административные настройки';
$lang['admin_index'] = 'Главная Админцентра';
$lang['attendance_by_event'] = 'Посещение событий';
$lang['attended'] = 'Посещение';
$lang['attendees'] = 'Персоонажи';
$lang['average'] = 'Средний';
$lang['buyer'] = 'Покупатель';
$lang['buyers'] = 'Покупатели';
$lang['class'] = 'Класс';
$lang['armor'] = 'Броня';
$lang['type'] = 'Броня';
$lang['class_distribution'] = 'Распределение по классам';
$lang['class_summary'] = "Отчёт по классу: %1\$s to %2\$s";
$lang['configuration'] = 'Общее управление';
$lang['config_plus']	= 'PLUS настройки';
$lang['plus_vcheck']	= 'Проверить обновление';
$lang['current'] = 'Текущий';
$lang['date'] = 'Дата';
$lang['delete'] = 'Удалить';
$lang['delete_confirmation'] = 'Подтверждение об удалении';
$lang['dkp_value'] = "%1\$s Стоимость";
$lang['drops'] = 'Трофеи';
$lang['earned'] = 'Заработано';
$lang['enter_dates'] = 'Ввести даты';
$lang['eqdkp_index'] = 'Главная EQdkp';
$lang['eqdkp_upgrade'] = 'EQdkp обновление';
$lang['event'] = 'Событие';
$lang['events'] = 'События';
$lang['filter'] = 'Фильтр';
$lang['first'] = 'Первый';
$lang['rank'] = 'Ранг';
$lang['general_admin'] = 'Общее управление';
$lang['get_new_password'] = 'Получить новый пароль';
$lang['group_adj'] = 'Групповое изменение.';
$lang['group_adjustments'] = 'Групповые изменения';
$lang['individual_adjustments'] = 'Индивидуальные изменения';
$lang['individual_adjustment_history'] = 'История индивидуальных изменений';
$lang['indiv_adj'] = 'Индивид. изм.';
$lang['ip_address'] = 'IP-адресс';
$lang['item'] = 'Предмет';
$lang['items'] = 'Предметы';
$lang['item_purchase_history'] = 'История покупки предметов';
$lang['last'] = 'Последний';
$lang['lastloot'] = 'Последний трофей';
$lang['lastraid'] = 'Последний RAID';
$lang['last_visit'] = 'Последний визит';
$lang['level'] = 'Уровень';
$lang['log_date_time'] = 'Дата/Время этого лога';
$lang['loot_factor'] = 'Фактор трофея';
$lang['loots'] = 'Трофеи';
$lang['manage'] = 'Управление';
$lang['member'] = 'Участник';
$lang['members'] = 'Участники';
$lang['members_present_at'] = "Участники показаны в %1\$s на %2\$s";
$lang['miscellaneous'] = 'Разное';
$lang['name'] = 'Название';
$lang['news'] = 'Новость';
$lang['note'] = 'Примечание';
$lang['online'] = 'Активен(Online)';
$lang['options'] = 'Настройки';
$lang['paste_log'] = 'Вставить лог ниже';
$lang['percent'] = 'Процент';
$lang['permissions'] = 'Права доступа';
$lang['per_day'] = 'За день';
$lang['per_raid'] = 'За RAID';
$lang['pct_earned_lost_to'] = '% потрачено';
$lang['preferences'] = 'Настройки';
$lang['purchase_history_for'] = "История покупок для предмета %1\$s";
$lang['quote'] = 'Цитата';
$lang['race'] = 'Раса';
$lang['raid'] = 'RAID';
$lang['raids'] = 'RAIDs';
$lang['raid_id'] = 'RAID ID';
$lang['raid_attendance_history'] = 'История участия в RAID(ах)';
$lang['raids_lifetime'] = "Активность: (%1\$s - %2\$s)";
$lang['raids_x_days'] = "Последние %1\$d дней";
$lang['rank_distribution'] = 'Распределение по рангам';
$lang['recorded_raid_history'] = "Запись истории RAIDов для локации %1\$s";
$lang['reason'] = 'Причина';
$lang['registration_information'] = 'Регистрационная информация';
$lang['result'] = 'Результат';
$lang['session_id'] = 'ID сессии';
$lang['settings'] = 'Настройки';
$lang['spent'] = 'Потрачено';
$lang['summary_dates'] = "Отчет по RAID: %1\$s to %2\$s";
$lang['themes'] = 'Темы';
$lang['time'] = 'Время';
$lang['total'] = 'Всего';
$lang['total_earned'] = 'Всего заработано';
$lang['total_items'] = 'Всего предметов';
$lang['total_raids'] = 'Всего RAIDы';
$lang['total_spent'] = 'Всего потрачено';
$lang['transfer_member_history'] = 'Переместить историю участника';
$lang['turn_ins'] = 'Передача предметов';
$lang['type'] = 'Тип';
$lang['update'] = 'Обновление';
$lang['updated_by'] = 'Обновил(а)';
$lang['user'] = 'Пользователь';
$lang['username'] = 'Имя Пользователя';
$lang['value'] = 'Стоимость';
$lang['view'] = 'Просмотр';
$lang['view_action'] = 'Просмотреть действие';
$lang['view_logs'] = 'Просмотреть логи';

// Page Foot Counts
$lang['listadj_footcount']               = "... найдено %1\$d изменений / %2\$d на странице";
$lang['listevents_footcount']            = "... найдено %1\$d событий / %2\$d на странице";
$lang['listiadj_footcount']              = "... найдено %1\$d индивидуальных изменений / %2\$d на странице";
$lang['listitems_footcount']             = "... найдено %1\$d уникальных предметов / %2\$d на странице";
$lang['listmembers_active_footcount']    = "... найдено %1\$d активных участников / %2\$sпоказать всех</a>";
$lang['listmembers_compare_footcount']   = "... сравнивается %1\$d участников";
$lang['listmembers_footcount']           = "... найдено %1\$d участников";
$lang['listnews_footcount']              = "... найдено %1\$d новостей / %2\$d на странице";
$lang['listpurchased_footcount']         = "... найдено %1\$d предмет(ов) / %2\$d на странице";
$lang['listraids_footcount']             = "... найдено %1\$d RAID(ов) / %2\$d на странице";
$lang['stats_active_footcount']          = "... найдено %1\$d активный(ых) участник(ов) / %2\$sпоказать всех</a>";
$lang['stats_footcount']                 = "... найдено %1\$d eчастников";
$lang['viewevent_footcount']             = "... найдено %1\$d RAID(ов)";
$lang['viewitem_footcount']              = "... найдено %1\$d Предмет(ов)";
$lang['viewmember_adjustment_footcount'] = "... найдено %1\$d Индивидуальных изменений";
$lang['viewmember_item_footcount']       = "... найдено %1\$d купленных предметов / %2\$d на странице";
$lang['viewmember_raid_footcount']       = "... найдено %1\$d проведенных RAID(ов) / %2\$d на странице";
$lang['viewraid_attendees_footcount']    = "... найдено %1\$d участников";
$lang['viewraid_drops_footcount']        = "... найдено %1\$d трофеев";

// Submit Buttons
$lang['close_window'] = 'Закрыть окно';
$lang['compare_members'] = 'Сравнить участников';
$lang['create_news_summary'] = 'Создать отчет по новостям';
$lang['login'] = 'Время начала сессии';
$lang['logout'] = 'Выход';
$lang['log_add_data'] = 'Добавить данные в форму';
$lang['lost_password'] = 'Забыл пароль';
$lang['no'] = 'Нет';
$lang['proceed'] = 'Продолжить';
$lang['reset'] = 'Сброс';
$lang['set_admin_perms'] = 'Назначить права администратора';
$lang['submit'] = 'Отправить';
$lang['upgrade'] = 'Обновить';
$lang['yes'] = 'Да';

// Form Element Descriptions
$lang['admin_login'] = 'Логин Администратора';
$lang['confirm_password'] = 'Подтвердить пароль';
$lang['confirm_password_note'] = 'Введите повторно ваш новый пароль';
$lang['current_password'] = 'Текущий пароль';
$lang['current_password_note'] = 'Укажите свой текущий пароль для его изменения';
$lang['email'] = 'Email';
$lang['email_address'] = 'Email адресс';
$lang['ending_date'] = 'Дата окончания';
$lang['from'] = 'От';
$lang['guild_tag'] = 'Название гильдии';
$lang['language'] = 'Язык';
$lang['new_password'] = 'Новый пароль';
$lang['new_password_note'] = 'Введите новый пароль, если желаете поменять ваш текущий пароль';
$lang['password'] = 'Пароль';
$lang['remember_password'] = 'Запомнить меня (cookie)';
$lang['starting_date'] = 'Дата начала';
$lang['style'] = 'Стиль';
$lang['to'] = 'Для';
$lang['username'] = 'Имя пользователя';
$lang['users'] = 'Пользователи';

// Pagination
$lang['next_page'] = 'Следующая страница';
$lang['page'] = 'Страница';
$lang['previous_page'] = 'Предыдущая страница';

// Permission Messages
$lang['noauth_default_title'] = 'Отказ в доступе';
$lang['noauth_u_event_list'] = 'У вас нет прав для списка событий.';
$lang['noauth_u_event_view'] = 'У вас нет прав для просмотра событий.';
$lang['noauth_u_item_list'] = 'У вас нет прав для списка предметов.';
$lang['noauth_u_item_view'] = 'У вас нет прав для просмотра предметов.';
$lang['noauth_u_member_list'] = 'У вас нет прав для просмотра положения участников.';
$lang['noauth_u_member_view'] = 'У вас нет прав для просмотра истории участников.';
$lang['noauth_u_raid_list'] = 'У вас нет прав для списка RAIDs.';
$lang['noauth_u_raid_view'] = 'У вас нет прав для просмотра RAIDs.';

// Submission Success Messages
$lang['add_itemvote_success'] = 'Ваш голос по предмету успешно зафиксирован.';
$lang['update_itemvote_success'] = 'Ваш голос по предмету успешно обновлен.';
$lang['update_settings_success'] = 'Ваши настройки пользователя успешно обновлены.';

// Form Validation Errors
$lang['fv_alpha_attendees'] = 'Имена персонажей в EverQuest могут содержать только буквы алфавита.';
$lang['fv_already_registered_email'] = 'Этот адрес e-mail уже зарегистрирован.';
$lang['fv_already_registered_username'] = 'Это имя пользователя уже зарегистрировано.';
$lang['fv_difference_transfer'] = 'Перенос истории должен производиться между двумя разными участниками.';
$lang['fv_difference_turnin'] = 'Покупка должна производиться между двумя разными участниками.';
$lang['fv_invalid_email'] = 'Адрес e-mail не действителен.';
$lang['fv_match_password'] = 'Поля пароля должны быть одинаковыми.';
$lang['fv_member_associated']  = "%1\$s уже ассоциирован с учётной записью другого участника.";
$lang['fv_number'] = 'Должно быть число.';
$lang['fv_number_adjustment'] = 'Поле количества изменения должно быть числом.';
$lang['fv_number_alimit'] = 'Поле предела изменения должно быть числом.';
$lang['fv_number_ilimit'] = 'Поле предела предметов должно быть числом.';
$lang['fv_number_inactivepd'] = 'Период неактивности должен быть числом.';
$lang['fv_number_pilimit'] = 'Предел купленных предметов должен быть числом.';
$lang['fv_number_rlimit'] = 'Предел RAIDов должен быть числом.';
$lang['fv_number_value'] = 'Поле стоимости должно быть числом.';
$lang['fv_number_vote'] = 'Поле голосования должно быть числом.';
$lang['fv_date'] = 'Пожалуйства выберите правильную дату в календаре.';
$lang['fv_range_day'] = 'Поле дня должно быть числом между 1 и 31.';
$lang['fv_range_hour'] = 'Поле часа должно быть числом между 0 и 23.';
$lang['fv_range_minute'] = 'Поле минуты должно быть числом между 0 и 59.';
$lang['fv_range_month'] = 'Поле месяца должно быть между 1 и 12.';
$lang['fv_range_second'] = 'Поле секунды должно быть числом между 0 и 59.';
$lang['fv_range_year'] = 'Поле года должно содержать число не меньше 1998.';
$lang['fv_required'] = 'Необходимое поле';
$lang['fv_required_acro'] = 'Поле акронима гильдии необходимо.';
$lang['fv_required_adjustment'] = 'Поле количества изменения необходимо.';
$lang['fv_required_attendees'] = 'Выберите участников RAIDа.';
$lang['fv_required_buyer'] = 'Выберите покупателя.';
$lang['fv_required_buyers'] = 'Хотябы один покупатель должен быть выбран.';
$lang['fv_required_email'] = 'Поле адреса e-mail необходимо.';
$lang['fv_required_event_name'] = 'Выберите событие.';
$lang['fv_required_guildtag'] = 'Укажите название гильдии.';
$lang['fv_required_headline'] = 'Укажите заголовок.';
$lang['fv_required_inactivepd'] = 'Если скрытие неактивных участников включено, должно быть введено значение неактивности.';
$lang['fv_required_item_name'] = 'Поле названия предмета должно быть заполнено или выбран существующий предмет.';
$lang['fv_required_member'] = 'Должен быть указан участник.';
$lang['fv_required_members'] = 'Должен быть выбран хотя бы один участник.';
$lang['fv_required_message'] = 'Не введено сообщение.';
$lang['fv_required_name'] = 'Заполните поле названия.';
$lang['fv_required_password'] = 'Заполните поле пароля.';
$lang['fv_required_raidid'] = 'Не выбран RAID.';
$lang['fv_required_user'] = 'Укажите имя пользователя.';
$lang['fv_required_value'] = 'Укажите значение.';
$lang['fv_required_vote'] = 'Необходимо проголосовать.';

// Miscellaneous
$lang['added'] = 'Добавлено';
$lang['additem_raidid_note'] = "Отображаются RAIDы за прошедшие две недели / %1\$sпоказать все</a>";
$lang['additem_raidid_showall_note'] = 'Показать все RAIDs';
$lang['addraid_datetime_note'] = 'Если вы передаёте лог на синтаксический анализ, дата и время будут определены автоматически.';
$lang['addraid_value_note'] = 'Для единовременного бонуса; если поле оставить пустым будет использоваться значение по умолчанию для этого события';
$lang['add_items_from_raid'] = 'Добавить предметы с этого RAIDа';
$lang['deleted'] = 'Удалено';
$lang['done'] = 'Готово';
$lang['enter_new'] = 'Ввести новый';
$lang['error'] = 'Ошибка';
$lang['head_admin'] = 'Главный администратор';
$lang['hold_ctrl_note'] = 'Зажмите CTRL чтобы выбрать несколько участников';
$lang['list'] = 'Список';
$lang['list_groupadj'] = 'Вывести список групповых изменений';
$lang['list_events'] = 'Вывести список событий';
$lang['list_indivadj'] = 'Вывести список индивидуальных изменений';
$lang['list_items'] = 'Вывести список предметов';
$lang['list_members'] = 'Вывести список участников';
$lang['list_news'] = 'Вывести список новостей';
$lang['list_raids'] = 'Вывести список RAIDов';
$lang['may_be_negative_note'] = 'может быть отрицательным';
$lang['not_available'] = 'Не доступно';
$lang['no_news'] = 'Ничего нового не найдено.';
$lang['of_raids'] = "%1\$d%% RAIDов";
$lang['or'] = 'Или';
$lang['powered_by'] = 'Поддерживается';
$lang['preview'] = 'Предпросмотр';
$lang['required_field_note'] = 'Все поля, помеченные * (звездочкой), обязательны для заполнения.';
$lang['select_1ofx_members'] = "Выбрать одного из %1\$d участников...";
$lang['select_existing'] = 'Выбрать использующихся';
$lang['select_version'] = 'Выберите версию EQdkp, которую вы хотите обновить:';
$lang['success'] = 'Успешно';
$lang['s_admin_note'] = 'Управление правами доступа доступно только для Администрации.';
$lang['transfer_member_history_description'] = 'Это перенесет всю историю участника (RAIDы, предметы, изменения) к другому участнику.';
$lang['updated'] = 'Обновлено';
$lang['upgrade_complete'] = 'Процесс обновления EQdkp успешно завершен.<br /><br /><b class="negative">Удалите данный файл в целях безопасности!</b>';

// Settings
$lang['account_settings'] = 'Настройки учётной записи';
$lang['adjustments_per_page'] = 'Укажите, сколько выводить изменений на страницу';
$lang['basic'] = 'Основные';
$lang['events_per_page'] = 'Укажите, сколько выводить событий на страницу';
$lang['items_per_page'] = 'Укажите, сколько выводить предметов на страницу';
$lang['news_per_page'] = 'Укажите, сколько выводить новостей на страницу';
$lang['raids_per_page'] = 'Укажите, сколько выводить RAIDов на страницу';
$lang['associated_members'] = 'Персоонажи пользователя';
$lang['guild_members'] = 'Участники гильдии';
$lang['default_locale'] = 'Локаль по умолчанию';



// Error messages
$lang['error_account_inactive'] = 'Ваша учётная запись неактивна.';
$lang['error_already_activated'] = 'Эта учётная запись уже активирована.';
$lang['error_invalid_email'] = 'Действительный адрес e-mail не был предоставлен.';
$lang['error_invalid_event_provided'] = 'Существующий id события не был предоставлен.';
$lang['error_invalid_item_provided'] = 'Существующий id предмета не был предоставлен.';
$lang['error_invalid_key'] = 'Вы предоставили неправильный ключ активации.';
$lang['error_invalid_name_provided'] = 'Существующее имя участника не было предоставлено.';
$lang['error_invalid_news_provided'] = 'Существующий id новости не был предоставлен.';
$lang['error_invalid_raid_provided'] = 'Существующий id RAIDа не был предоставлен.';
$lang['error_user_not_found'] = 'Существующее имя пользователя не было предоставлено';
$lang['incorrect_password'] = 'Неправильный пароль';
$lang['invalid_login'] = 'Вы предоставили неправильное имя пользователя или пароль';
$lang['not_admin'] = 'Вы не являетесь администратором';

// Registration
$lang['account_activated_admin']   = 'Учётная запись активирована. Письмо информирующее об этом изменении отправлено на email пользователя.';
$lang['account_activated_user']    = "Ваша учётная запись активирована и теперь вы можете %1\$sвойти%2\$s.";
$lang['password_sent'] = 'Новый пароль к вашей учётной записи отправлен на ваш e-mail.';
$lang['register_activation_self']  = "Ваша Учётная запись создана, но перед тем как её использовать вы должны активировать её.<br /><br />E-mail отправлен на адрес %1\$s с информацией как активировать вашу учётную запись.";
$lang['register_activation_admin'] = "Ваша Учётная запись создана, но перед тем как её использовать администратор должен активировать её.<br /><br />E-mail отправлен на адрес %1\$s с дополнительной информацией.";
$lang['register_activation_none']  = "Ваша Учётная запись создана и теперь вы можете %1\$sвойти%2\$s.<br /><br />E-mail отправлен на адрес %3\$s с дополнительной информацией.";

// lua
$lang['lua'] = "Импорт CT_RaidTracker";
$lang['lua_parse'] = "Импорт LUA";
$lang['import_lua_data'] = "Импорт данных CT_RaidTracker";
$lang['lua_step1_pagetitel'] = "Импорт CT_Raidtracker";
$lang['lua_step1_th'] = "Вставить лог ниже";
$lang['lua_step1_button_parselog'] = "Синтаксический анализ лога";
$lang['lua_step1_invalidstring_titel'] = "Неверная строка DKP";
$lang['lua_step1_invalidstring_msg'] = "Строка DKP заполнена неверно.";
$lang['lua_step1_button_parselog'] = "Синтаксический анализ лог";
$lang['lua_step2_pagetitel'] = "Импорт CT_Raidtracker";
$lang['lua_step2_foundraids'] = "Найдено RAIDов";
$lang['lua_step2_dkpvaluetip'] = "Стоимость/Претенденты предмета";
$lang['lua_step2_insertraids'] = "Вставить RAIDы";
$lang['lua_step2_raidsdropsdetails'] = "Детали RAIDа/шанса выпадения";
$lang['lua_step3_pagetitel'] = "Импорт CT_Raidtracker";
$lang['lua_step3_titel'] = "Лог действий<br>\n";
$lang['lua_step3_alreadyexist'] = "%s (%s, %s DKP) уже был добавлен, пропускаю шаг<br>\n";
$lang['lua_step3_raidadded'] = "%s (%s, %s DKP) добавлен<br>\n";
$lang['lua_step3_memberadded'] = "%s (раса: %s, класс: %s, уровень: %s, ранг: %s) был добавлен к участникам<br>\n";
$lang['lua_step3_attendeesadded'] = "%s заявленных добавлено<br>\n";
$lang['lua_step3_lootadded'] = "%s (%s DKP) был добавлен к %s<br>\n";

//plus
$lang['news_submitter'] = 'Отправил(а)';
$lang['news_submitat'] = 'На';
$lang['droprate_loottable'] = "Таблица троффев";
$lang['droprate_name'] = "Название предмета";
$lang['droprate_count'] = "Цена";
$lang['droprate_drop'] = "Шанс выпадения %";

$lang['Points_header'] = "Быстрый DKP";
$lang['Points_class'] = "Класс:";
$lang['Points_Char'] = "Имя:";
$lang['Points_DKP'] = "DKP:";
$lang['Points_CHAR'] = "Участник не установлен";

$lang['Itemsearch_link'] = "Предмет-поиск";
$lang['Itemsearch_search'] = "Поиск предметов :";
$lang['Itemsearch_searchby'] = "Найден(а) :";
$lang['Itemsearch_item'] = "Предмет ";
$lang['Itemsearch_buyer'] = "Покупатель ";
$lang['Itemsearch_raid'] = "RAID ";
$lang['Itemsearch_unique'] = "Результат среди уникальных предметов :";
$lang['Itemsearch_no'] = "Да";
$lang['Itemsearch_yes'] = "нет";

$lang['bosscount_player'] = "Игрок: ";
$lang['bosscount_raids'] = "RAIDs: ";
$lang['bosscount_items'] = "Предметы: ";
$lang['bosscount_dkptotal'] = "Текущий DKP: ";

//MultiDKP
$lang['Plus_menuentry'] 			= "EQDKP Plus";
$lang['Multi_entryheader'] 		= "MultiDKP - Добавить Pool";
$lang['Multi_pageheader'] 		= "MultiDKP - Показать Pools";
$lang['Multi_events'] 				= "События:";
$lang['Multi_eventname'] 				= "Название События";
$lang['Multi_discnottolong'] 	= "(Имя строки) - this one should not be too long, the table will get large,. Choose p.e MC, BWL, AQ etc. !";
$lang['Multi_kontoname_short']= "Имя Учётной записи:";
$lang['Multi_discr'] 					= "Отчёт:";
$lang['Multi_events'] 				= "События в этом Pool";


$lang['Multi_addkonto'] 			  = "Добавит MultiDKP Pool";
$lang['Multi_updatekonto'] 			= "Сменить Pool";
$lang['Multi_deletekonto'] 			= "Удалить Pool";
$lang['Multi_viewkonten']			  = "Показать MultiDKP Pools";
$lang['Multi_chooseevents']			= "Выбрать событие";
$lang['multi_footcount'] 				= "... %1\$d DKP Pools / %2\$d на страницу";
$lang['multi_error_invalid']    = "No Pools assigned....";

$lang['Multi_required_event']   = "Вы должны выбрать at least one event!";
$lang['Multi_required_name']    = "Вы должны ввести название(имя)!";
$lang['Multi_required_disc']    = "Вы должны ввести отчёт!";
$lang['Multi_admin_add_multi_success'] = "The Pool %1\$s ( %2\$s ) с событиями %3\$s был добавлен в базу данных.";
$lang['Multi_admin_update_multi_success'] = "The Pool %1\$s ( %2\$s ) с событиями %3\$s был изменён в базе данных.";
$lang['Multi_admin_delete_success']           = "The Pool %1\$s был удалён из базы данных.";
$lang['Multi_confirm_delete']    = 'Are you really sure you want to delete that Pool?';


##########

$lang['Multi_total_cost']   										= 'Сумма очков для this даной Pool';
$lang['Multi_Accs']    													= 'MultiDKP Pool';

//update

$lang['upd_eqdkp_status']    										= 'Обновить EQDKP статус';
$lang['upd_system_status']    									= 'Статус системы';
$lang['upd_template_status']    								= 'Статус шаблона';
$lang['upd_update_need']    										= 'Обновить необходимо!';
$lang['upd_update_need_link']    								= 'Установить все требуемые компоненты';
$lang['upd_no_update']    											= 'Обновление не требуется. Система содержит последнее обновление.';
$lang['upd_status']    													= 'Статус';
$lang['upd_state_error']    										= 'Ошибка';
$lang['upd_sql_string']    											= 'SQL команда';
$lang['upd_sql_status_done']    								= 'Сделать(Решить)';
$lang['upd_sql_error']    											= 'Ошибка';
$lang['upd_sql_footer']    											= 'SQL команда выполнена';
$lang['upd_sql_file_error']    									= 'Ошибка: Требуемый SQL файд %1\$s не был найден!';
$lang['upd_eqdkp_system_title']    							= 'Компонент EQDKP системы обновлён';
$lang['upd_plus_version']    										= 'Версия EQDKP Plus';
$lang['upd_plus_feature']    										= 'Функция';
$lang['upd_plus_detail']    										= 'Детали';
$lang['upd_update']    													= 'Обновить';
$lang['upd_eqdkp_template_title']    						= 'Шаблон EQDKP обновлён';
$lang['upd_template_name']    									= 'Имя шаблона';
$lang['upd_template_state']    									= 'Статус шаблона';
$lang['upd_template_filestate']    							= 'Папка шаблона';
$lang['upd_link_install']    										= 'Обновить';
$lang['upd_link_reinstall']    									= 'Установить';
$lang['upd_admin_need_update']    							= 'Ошибка базы данных была обнаружена. Система не up to date and needs to be updated.';
$lang['upd_admin_link_update']									= 'Кликните сюда чтобы решить проблемы.';
$lang['upd_backto']    													= 'Назад к анализу';

// Event Icon
$lang['event_icon_header']    								  = 'Выберите иконку события';

//update Itemstats
$lang['updi_header']    								    	= 'Обновить статистику предметов в базе';
$lang['updi_header2']    								    	= 'Информация о статистике Предметов';
$lang['updi_action']    								    	= 'Действие';
$lang['updi_notfound']    								    = 'Не найдено';
$lang['updi_writeable_ok']    							  = 'Файл перезаписан';
$lang['updi_writeable_no']    								= 'Файл не перезаписан';
$lang['updi_help']    								    		= 'Описание';
$lang['updi_footcount']    								    = 'Предмет обновлён';
$lang['updi_curl_bad']    								    = 'Требуемая функция PHP cURL не найдена. Возможно статистика предметов работает не правильно. Пожалуйста свяжитесь с администратором.';
$lang['updi_curl_ok']    								    	= 'cURL найден.';
$lang['updi_fopen_bad']    								    = 'Требуемая функция PHP fopen не найдена. Возможно статистика предметов работает не правильно. Пожалуйста свяжитесь с администратором.';
$lang['updi_fopen_ok']    								    = 'fopen найден.';
$lang['updi_nothing_found']						    		= 'Предметы не найдены';
$lang['updi_itemscount']  						    		= 'Входы кэша Предметов:';
$lang['updi_baditemscount']						    		= 'Плохой вход:';
$lang['updi_items']										    		= 'Предметы в базе данных:';
$lang['updi_items_duplicate']					    		= '{с двойными предметами}';
$lang['updi_show_all']    								    = 'Список всех предметов со статистикой';
$lang['updi_refresh_all']    								  = 'Удалить все предметы и обновить их.';
$lang['updi_refresh_bad']    								  = 'Обновить только неправильные предметы';
$lang['updi_refresh_raidbank']    						= 'Обновить Предметы Raidbanker(а)';
$lang['updi_refresh_tradeskill']   						= 'Обновить Предметы Tradeskill(а)';
$lang['updi_help_show_all']    								= 'Показать все предметы с их статистиками. Плохие статистики будут обновлены. (Рекомендуется)';
$lang['updi_help_refresh_all']  							= 'Удалённый текущий кэш предметов и tries to refresh all items that are listed in EQDKP. WARNING: If you share your Itemcache with a forum, the items from the forum cannot be refreshed. Depending on your webservers speed and the availability of Allakhazam.com this action could take several minutes. Possibly your webserver settings forbid a successful execution. In this case please contact your administrator.';
$lang['updi_help_refresh_bad']    						= 'Удалить все плохие предметы из кэша и обновить их.';
$lang['updi_help_refresh_raidbank']    				= 'Raidbanker установлен, Статистика Предметов uses the entered items of the banker.';
$lang['updi_help_refresh_Tradeskill']    			= 'Когда Tradeskill установлен, введённые предметы не будут обновлены в статистике Предметов.';

$lang['updi_active'] 					   							= 'Активировано';
$lang['updi_inactive']    										= 'Неактивировано';

$lang['fontcolor']    			  = 'Цвет шрифта';
$lang['Warrior']    					        = 'Воин';
$lang['Rogue']    						= 'Разбойник';
$lang['Hunter']    						= 'Охотник';
$lang['Paladin']    					        = 'Паладин';
$lang['Priest']    						= 'Жрец';
$lang['Druid']    						= 'Друид';
$lang['Shaman']    						= 'Шаман';
$lang['Warlock']    					        = 'Колдун';
$lang['Mage']    					        = 'Маг';

# Reset DB Feature
$lang['reset_header']    			= 'Сбросить дату EQDKP';
$lang['reset_infotext']  			= 'Предупреждение!!! Удалённая дата может быть сброшена!!! Сделайте последнюю копию. Подтвердите действие, нажмите УДАЛИТЬ в ящике редактирования.';
$lang['reset_type']    				= 'Тип даты';
$lang['reset_disc']    				= 'Описание';
$lang['reset_sec']    				= 'Сертификат';
$lang['reset_action']    			= 'Действие';

$lang['reset_news']					  = 'Новости';
$lang['reset_news_disc']		  = 'Удалить все новости из базы данных.';
$lang['reset_dkp'] 					  = 'DKP';
$lang['reset_dkp_disc']			  = 'Удалить все RAIDы и Предметы из базы данных и сбросить все DKP очки до 0.';
$lang['reset_ALL']   					= 'Все';
$lang['reset_ALL_DISC']				= 'Удалить любой RAID, Предмет на участников. Сброс данных звершён. (Не удаляет Пользователей).';

$lang['reset_confirm_text']	  = ' Нажмите сюда =>';
$lang['reset_confirm']			  = 'УДАЛИТЬ';

// Armory Menu
$lang['lm_armorylink1']				= 'Оружейная';
$lang['lm_armorylink2']				= 'Таланты';
$lang['lm_armorylink3']				= 'Гильдия';

$lang['updi_update_ready']			= 'Предметы были успешно обновлены. Вы можете увидеть <a href="#" onclick="javascript:parent.closeWindow()" >close</a> в этом окне.';
$lang['updi_update_alternative']= 'Альтернативный метод обновления был анулирован из-за времени ожидания.';
$lang['zero_sum']				= ' на нуль суммировано DKP';

//Hybrid
$lang['Hybrid']				= 'Гибрид';

$lang['Jump_to'] 				= 'Просмотреть видео на ';
$lang['News_vid_help'] 			= 'To embed videos just post the link to the video without [tags]. supported videosites: google video, youtube, myvideo, clipfish, sevenload, metacafe and streetfire. ';

$lang['SubmitNews'] 		   = 'Отправить новость';
$lang['SubmitNews_help'] 	   = 'У вас есть хорошая новость? Отправьте новость и поделитесь со всеми Eqdkp Plus Пользователями.';

$lang['MM_User_Confirm']	   = 'Выбрали вашу учётную запись Администратора? Если вы имеете права Администратора, это может быть только сброшено в базе данных';

$lang['beta_warning']	   	   = 'Внимание!! Данная версия EQDKP-Plus является Beta! Мы Настоятельно рекомендуем использовать последнюю стабильную версию. Кликните <a href="http://www.eqdkp-plus.com" >www.eqdkp-plus.com</a> для проверки обновления!';

$lang['news_comment']        = 'Комментарий';
$lang['news_comments']       = 'Комментарии';

$lang['comments_no_comments']	   = 'No entries';
$lang['comments_comments_raid']	   = 'Комментарии';
$lang['comments_write_comment']	   = 'Прочесть комментарий';
$lang['comments_send_comment']	   = 'Сохранить комментарий';
$lang['comments_save_wait']	   	   = 'Пожалуйста подождите, комментарий сохраняется...';


$lang['news_nocomments'] 	 		    = 'Комментарии запрещены';
$lang['news_ext_message']  			  	= 'Расширенные новости';
$lang['news_message'] 				  	= 'Текст новостей';
$lang['news_permissions']			  	= 'Права для просмотра';

$lang['news_permissions_text']			= 'Показывать новости для:';
$lang['news_permissions_guest']			= 'Только Гостей';
$lang['news_permissions_member']		= 'Гостей и Участников (только Администраторы могут видеть)';
$lang['news_permissions_all']			= 'Всех';
$lang['news_readmore'] 				  	= 'Прочесть больше...';

$lang['recruitment_open']				= 'Задействование открыто';
$lang['recruitment_contact']			= 'Контакт';

$lang['sig_conf']						= 'Кликните по изображению ,чтобы получить BB код';
$lang['sig_show']						= 'Показать WoW сигнатуру для вашего форума';

//Next Raids
$lang['next_raids_signoff']				= 'Unsigned';
$lang['next_raids_signon']				= 'Signed';
$lang['next_raids_total']				= 'Gesamt';
$lang['next_raids_confirmed']			= 'Confirmed';
$lang['next_raids_missing']				= 'Требуется';
$lang['next_raids_head']				= 'Следующие RAIDs';
$lang['next_raids_notsigned']			= 'Not Signed';

//Last items
$lang['last_items']					    = 'Последние Предметы';

$lang['service']					    = 'Сервис';
$lang['shirt_ad1']					    = 'Go to the Shirt-shop. <br> get your own shirt now!';
$lang['shirt_ad2']					    = 'Выбрите вашего персоонажа';
$lang['shirt_ad3']					    = 'Приветствую вас в магазине Гильдии ';
$lang['shirt_ad4']					    = 'Wдhle eines der vorgefertigten Produkte aus, oder erstell Dir mit dem Creator ein komplett eigenes Shirt.<br>
										   Du kannst jedes Shirt nach Deinen Bedьrfnissen anpassen und jeden Schriftzug verдndern.<br>
										   Unter Motive findest alle zur Verfьgung stehenden Motive!';

$lang['error_iframe']					= "Ваш браузер не поддерживает Frames!";
$lang['new_window']						= 'Открыть вкладку в новом окне';
$lang['your_name']						= 'Ваше имя';
$lang['your_guild']						= 'Ваша Гильдия';
$lang['your_server']					= 'Ваш сервер';

//Last Raids
$lang['last_raids']					    = 'Последние RAIDs';

$lang['voice_error']				    = 'Отсутствует соеденение с сервером.';



$lang['talents'] = array(
'Paladin'   => array('Свет','Защита','Возмездие'),
'Rogue'     => array('Убийство','Битва','Тонкость'),
'Warrior'   => array('Мастер по оружию','Ярость','Защита'),
'Hunter'    => array('Мастер зверей','Стрельба','Выживание'),
'Priest'    => array('Дисциплина','Свет','Тьма'),
'Warlock'   => array('Бедствие','Демонология','Уничтожение'),
'Druid'     => array('Равновесие','Дикость','Восстановление'),
'Mage'      => array('Аркан','Огонь','Холод'),
'Shaman'    => array('Елементаль','Усиление','Восстановление')
);
?>
