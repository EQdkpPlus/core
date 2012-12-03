<?php
/******************************
 * EQdkp
 * Copyright 2002-2003
 * Licensed under the GNU GPL.  See COPYING for full terms.
 * ------------------
 * file.php
 * Began: Day January 1 2003
 *
 * $Id: lang_install.php 1701 2008-03-16 15:04:04Z osr-corgan $
 *
 ******************************/

// Do not remove. Security Option!
if ( !defined('EQDKP_INC') ){
    header('HTTP/1.0 404 Not Found');exit;
}

$lang = array(
'inst_header'               => 'Установка EQdkp',

// ===========================================================
//	Per Language default settings
// ===========================================================
'game_language'             => 'ru',
'default_lang'              => 'russian',
'default_locale'            => 'ru_RU',

// ===========================================================
//	Prepare Installation
// ===========================================================
'installation_message'      => 'Installation Note',
'installation_messages'     => 'Installation Notes',
'error'                     => 'Ошибка',
'errors'                    => 'Ошибки',
'lerror'                    => 'ОШИБКА',
'notice'                    => 'Извещение',
'install_error'             => 'Ошибка установки',
'inst_step'                 => 'Шаг',
'error_nostructure'         => 'Не получена SQL структура/дата',
'error_template'            => "Не включено '%s' includes/class_template.php - проверьте что файл существует!",

// ===========================================================
//	Stepnames
// ===========================================================
'stepname_1'                => 'Информация',
'stepname_2'                => 'База данных',
'stepname_3'                => 'Проверка DB',
'stepname_4'                => 'Информация о сервере',
'stepname_5'                => 'Учетная запись(Account)',
'stepname_6'                => 'Завершение (Finish)',

// ===========================================================
//	Step 1: PHP / Mysql Environment
// ===========================================================
'language_selector'         => 'Пожалуйста, выберите язык',
'install_language'          => 'Язык установки',
'already_installed'         => 'EQdkp уже установлена - удалите <b>install/</b> папку в вашей директории.',
'conf_not_write'            => 'The <b>config.php</b> file does not exist and could not be created in EQdkp\'s root folder.<br />
                                You must create an empty config.php file on your server before proceeding.',
'conf_written'              => 'The <b>config.php</b> file has been created in EQdkp\'s root folder<br />
                                Deleting this file will interfere with the operation of your EQdkp installation.',
'conf_chmod'                => 'The file <b>config.php</b> is not set to be readable/writeable and could not be changed automatically.
                                <br />Please change the permissions to 0666 manually by executing <b>chmod 0666 config.php</b> on your server.',
'conf_writable'             => '<b>config.php</b> has been set to be readable/writeable in order to let this installer write your configuration
                                file automatically.',
'templcache_created'        => "The directory '%1\$s' was created, removing this directory could interfere with the operation of your EQdkp installation.",
'templcache_notcreated'     => "The directory '%1\$s' could not be created, please create one manually in the templates directory.
                                <br />You can do this by changing to the EQdkp root directory and typing <b>mkdir -p %1\$s</b>",
'templcache_notwritable'    => "The directory '%1\$s' exists, but is not set to be writeable and could not be changed automatically.
                                <br />Please change the permissions to 0777 manually by executing <b>chmod 0777 %1\$s</b> on your server.",
'templatecache_ok'          => "The '%1\$s' directory has been set to be writeable in order to let EQDKP-PLUS create files in these folders.",
'cachefolder_out'           => "The folder '%1\$s' has been %2\$s and is %3\$s",
'connection_failed'         => 'Connection to EQdkp-PLUS.com failed.',
'curl_notavailable'         => 'cURL is not available. Itemstats possibly will not work correct.',
'fopen_notavailable'        => 'fopen is not available. Itemstats possibly will not work correct.',
'safemode_on'               => 'PHP Safe Mode is enabled. EQDKP-PLUS will not run in Safe Mode because of the Data write operations.',

'minimal_requ_notfilled'    => 'Извините, ваш сервер не соответствует минимальным требованиям EQdkp',
'minimal_requ_filled'       => 'EQdkp просканировало вашу систему и выяснило, что она соответствует минимальным требованиям.',

'inst_unknown'              => 'Неизвестно',
'eqdkp_name'                => 'EQdkp PLUS',
'inst_eqdkpv'               => 'EQDKP Plus версия',
'inst_latest'               => 'Последняя устойчивая',

'inst_php'                  => 'PHP',
'inst_view'                 => 'Показать php информацию()',
'inst_version'              => 'Версия',
'inst_required'             => 'Требуется',
'inst_available'            => 'Доступно',
'inst_enabled'              => 'Enabled',
'inst_using'                => 'Используется',
'inst_yes'                  => 'Да',
'inst_no'                   => 'Нет',

'inst_mysqlmodule'          => 'MySQL модуль',
'inst_zlibmodule'           => 'zLib модуль',
'inst_curlmodule'           => 'cURL модуль',
'inst_fopen'                => 'fopen',
'inst_safemode'             => 'Safe Mode',

'inst_php_modules'          => 'PHP модули',
'inst_Supported'            => 'Поддерживается',

'inst_found'                => 'Found',
'inst_writable'             => 'Writable',
'inst_notfound'             => 'Not Found',
'inst_unwritable'           => 'Unwritable',

'inst_button1'              => 'Запуск установки',

// ===========================================================
//	Step 2: Database
// ===========================================================
'inst_database_conf'        => 'Конфигурация базы данных',
'inst_dbtype'               => 'Тип базы данных',
'inst_dbhost'               => 'Хост базы данных',
'inst_dbname'               => 'Название базы данных',
'inst_dbuser'               => 'Логин базы данных',
'inst_dbpass'               => 'Пароль базы данных',
'inst_table_prefix'         => 'Префикс для таблиц EQdkp',
'inst_button2'              => 'Установить базу данных',

// ===========================================================
//	Step 3: Database cofirmation
// ===========================================================
'inst_error_nodbname'       => 'База данных не получила название!',
'inst_error_prefix'         => 'No Table prefix set! Please go back and enter a prefix.',
'inst_error_prefix_inval'   => 'Invalid prefix',
'inst_error_prefix_toolong' => 'prefix too long!',
'inserror_dbconnect'        => 'Failed to connect to database',
'insterror_no_mysql'        => 'База данных не MySQL!',
'inst_redoit'               => 'Restart instalation',
'db_warning'                => 'Опасность',
'db_information'            => 'Информация',
'inst_sqlheaderbox'         => 'SQL информация',
'inst_mysqlinfo'            => "MySQL клиент <b>and</b> версия сервера 4.0.4 или выше и InnoDB таблица поддерживаются EQdkp.<br>
                                <b><br>Вы запустили версию сервера <ul>%s</ul> и версию клиента <ul>%s.</ul></b><br>
                                MySQL версии ниже 4.0.4 не будут работать и поддерживаться. Версии ниже 4.0.4<br>
                                will experience data corruption, and we will not provide support for these installations.<br><br>",
'inst_button3'              => 'Процесс',
'inst_button_back'          => 'Back',
'inst_sql_error'            => 'Ошибка! Неудалось выполнить следующее SQL Утверждение: <br><br><ul>%s</ul> <br>',
'insinfo_dbready'           => 'Соединение с базой дынных было проверено и никаких ошибок не было обнаружено. Вы можете продолжить процесс установки.',

// Errors
'INST_ERR'                  => 'Installation error',
'INST_ERR_PREFIX'           => 'The Database Prefix already exists. Please go back and use another one or your data will be flushed!',
'INST_ERR_DB_CONNECT'       => 'Could not connect to the database, see error message below.',
'INST_ERR_DB_NO_ERROR'      => 'No error message given.',
'INST_ERR_DB_NO_MYSQLI'     => 'The version of MySQL installed on this machine is incompatible with the “MySQL with MySQLi Extension” option you have selected. Please try the “MySQL” option instead.',
'INST_ERR_DB_NO_NAME'       => 'No database name specified.',
'INST_ERR_PREFIX_INVALID'   => 'The table prefix you have specified is invalid for your database. Please try another, removing characters such as hyphen, apostrophe or forward- or back-slashes.',
'INST_ERR_PREFIX_TOO_LONG'  => 'The table prefix you have specified is too long. The maximum length is %d characters.',

// ===========================================================
//	Step 4: Server
// ===========================================================
'inst_language_config'      => 'Конфигурация языка',
'inst_default_lang'         => 'Язык по умолчанию',
'inst_default_locale'       => 'Локализация по умолчанию',

'inst_game_config'          => 'Игровая конфигурация',
'inst_default_game'         => 'Стандартная игра',

'inst_server_config'        => 'Конфигурация сервера',
'inst_server_name'          => 'Имя домена',
'inst_server_port'          => 'Порт сервера',
'inst_server_path'          => 'Путь скрипта',

'inst_button4'              => 'Установить',

// ===========================================================
//	Step 5: Accounts
// ===========================================================
'inst_administrator_config' => 'Конфигурация учетной записи Администратора',
'inst_username'             => 'Логин администратора',
'inst_user_password'        => 'Пароль Администратора',
'inst_user_pw_confirm'      => 'Подтвердите пароль администратора',
'inst_user_email'           => 'Адрес Email администратора',

'inst_button5'              => 'Установка учетных записей',

'inst_writerr_confile'      => 'The <b>config.php</b> file couldn\'t be opened for writing.  Paste the following in to config.php and save the
                                file to continue:',
'inst_confwritten'          => 'Your configuration file has been written with the initial values, but installation will not be complete until
                                you create an administrator account in the next step.',
'inst_checkifdbexists'      => 'Before proceeding, please verify that the database name you provided is already created and that the user you provided has permission to create tables in that database',
'inst_wrong_dbtype'         => "Unable to find the database abstraction layer for <b>%s</b>, check to make sure %s exists.",
'inst_failedconhost'        => "Failed to connect to database <b>%s</b> as <b>%s@%s</b>
                                <br /><br /><a href='index.php'>Restart Installation</a>",
'inst_failedversioninfo'    => "Failed to get version information for database <b>%s</b> as <b>%s@%s</b>
                                <br /><br /><a href='index.php'>Restart Installation</a>",

// ===========================================================
//	Step 5: Finish
// ===========================================================
'login'                     => 'Учетная запись',
'username'                  => 'Имя пользователя',
'password'                  => 'Пароль',
'remember_password'         => 'Запомнить меня (cookie)',

'login_button'              => 'Логин',

'inst_passwordnotmatch'     => 'Ваши пароли не совпадают, таким образом пароли были сброшены <b>admin</b>.  Вы можете сменить его, зайдя на сайт и перейти к настройкам вашей учетной записи.',
'inst_admin_created'        => 'Ваша учетная запись администратора была успешно создана, log in above to be taken to the EQdkp configuration page.',
);
?>
