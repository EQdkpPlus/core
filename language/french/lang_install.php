<?php
/******************************
 * EQdkp
 * Copyright 2002-2003
 * Licensed under the GNU GPL.  See COPYING for full terms.
 * ------------------
 * file.php
 * Began: Day January 1 2003
 *
 * $Id: lang_install.php 3875 2009-02-19 17:42:26Z Lightstalker $
 *
 ******************************/

// Do not remove. Security Option!
if ( !defined('EQDKP_INC') ){
    header('HTTP/1.0 404 Not Found');exit;
}

$lang = array(
'inst_header'               => 'Installation',

// ===========================================================
//	Per Language default settings
// ===========================================================
'game_language'             => 'fr',
'default_lang'              => 'french',
'default_locale'            => 'fr_FR',

// ===========================================================
//	Prepare Installation
// ===========================================================
'installation_message'      => 'Note d\'installation',
'installation_messages'     => 'Notes d\'installation',
'error'                     => 'Erreur',
'errors'                    => 'Erreurs',
'lerror'                    => 'ERREUR',
'notice'                    => 'NOTICE',
'install_error'             => 'Erreur d\'installation',
'inst_step'                 => 'Etape',
'error_nostructure'         => 'N\'a pas pu obtenir la structure de données SQL.',
'error_template'            => "N\'a pas pu inclure '%s' includes/class_template.php - vérifiez que le fichier existe.",

// ===========================================================
//	Stepnames
// ===========================================================
'stepname_1'                => 'Informations',
'stepname_2'                => 'Base de données',
'stepname_3'                => 'Vérification de la base',
'stepname_4'                => 'Infos serveur',
'stepname_5'                => 'Compte',
'stepname_6'                => 'Terminer',

// ===========================================================
//	Step 1: PHP / Mysql Environment
// ===========================================================
'language_selector'         => 'Sélectionnez la langue souhaité',
'install_language'          => 'Langue d\'installation',
'already_installed'         => 'EQdkp est déjà installé - supprimer le dossier <b>install/</b> de ce répertoire.',
'conf_not_write'            => 'Le fichier <b>config.php</b> n\'existe pas et n\'a pas pu être créé dans la racine du répertoire EQdkp<br />
                                Vous devez créer un fichier vide config.php sur votre serveur avant de poursuivre.',
'conf_written'              => 'Le fichier <b>config.php</b> a été créé dans la racine du répertoire EQdkp<br />
                                La suppression de ce fichier provoque une mauvaise installation de EQdkp',
'conf_chmod'                => 'Le fichier <b>config.php</b> n\'est pas modifiable ou accessible, et ne peut être modifié automatiquement.
                                <br />Veuillez configurer les permissions à 0666 manuellement avec la commande <b>chmod 0666 config.php</b> sur votre serveur.',
'conf_writable'             => '<b>config.php</b> a été rendu modifiable et accessible afin de permettre l\'installation automatique.',
'templcache_created'        => "The directory '%1\$s' was created, removing this directory could interfere with the operation of your EQdkp installation.",
'templcache_notcreated'     => "The directory '%1\$s' could not be created, please create one manually in the templates directory.
                                <br />You can do this by changing to the EQdkp root directory and typing <b>mkdir -p %1\$s</b>",
'templcache_notwritable'    => "The directory '%1\$s' exists, but is not set to be writeable and could not be changed automatically.
                                <br />Please change the permissions to 0777 manually by executing <b>chmod 0777 %1\$s</b> on your server.",
'templatecache_ok'          => "The '%1\$s' directory has been set to be writeable in order to let EQDKP-PLUS create files in these folders.",
'cachefolder_out'           => "The folder '%1\$s' has been %2\$s and is %3\$s",
'connection_failed'         => 'La connexion à EQdkp-PLUS.com à échouée..',
'curl_notavailable'         => 'cURL n\'est pas disponible. Itemstats ne fonctionnera probablement pas.',
'fopen_notavailable'        => 'fopen n\'est pas disponible. Itemstats ne fonctionnera probablement pas.',
'safemode_on'               => 'PHP Safe Mode is enabled. EQDKP-PLUS will not run in Safe Mode because of the Data write operations.',

'minimal_requ_notfilled'    => 'Désolé, votre serveur ne satisfait pas aux caractérisques minimales pour EQdkp',
'minimal_requ_filled'       => 'EQdkp a analysé votre serveur et il satisfait aux caractéristiques minimales pour l\'installation.',

'inst_unknown'              => 'Inconnu',
'eqdkp_name'                => 'EQdkp PLUS',
'inst_eqdkpv'               => 'Version EQDKP Plus',
'inst_latest'               => 'Dernière stable',

'inst_php'                  => 'PHP',
'inst_view'                 => 'Voir phpinfo()',
'inst_version'              => 'Version',
'inst_required'             => 'Requise',
'inst_available'            => 'Disponible',
'inst_enabled'              => 'Enabled',
'inst_using'                => 'Utilise',
'inst_yes'                  => 'Oui',
'inst_no'                   => 'Non',

'inst_mysqlmodule'          => 'Module MySQL',
'inst_zlibmodule'           => 'Module zLib',
'inst_curlmodule'           => 'Module cURL',
'inst_fopen'                => 'fopen',
'inst_safemode'             => 'Safe Mode',

'inst_php_modules'          => 'Modules PHP',
'inst_Supported'            => 'Supporté',

'inst_found'                => 'Found',
'inst_writable'             => 'Writable',
'inst_notfound'             => 'Not Found',
'inst_unwritable'           => 'Unwritable',

'inst_button1'              => 'Démarrer l\'installation',

// ===========================================================
//	Step 2: Database
// ===========================================================
'inst_database_conf'        => 'Configuration de la base de données',
'inst_dbtype'               => 'Type de la base de données',
'inst_dbhost'               => 'Serveur de la base de données',
'inst_dbname'               => 'Nom de la base de données',
'inst_dbuser'               => 'Utilisateur de la base de données',
'inst_dbpass'               => 'Mot de passe de la base de données',
'inst_table_prefix'         => 'Prefix des tables EQdkp',
'inst_button2'              => 'Test de la base de données',

// ===========================================================
//	Step 3: Database cofirmation
// ===========================================================
'inst_error_nodbname'       => 'Aucun nom de base de données !',
'inst_error_prefix'         => 'Pas de préfixe de table. Revenez en arrière.',
'inst_error_prefix_inval'   => 'Prefixe invalide',
'inst_error_prefix_toolong' => 'prefixe trop long !',
'inserror_dbconnect'        => 'Echec de la connexion à la base',
'insterror_no_mysql'        => 'La base de données n\'est pas MySQL!',
'inst_redoit'               => 'Recommencer l\'instalation',
'db_warning'                => 'Alerte',
'db_information'            => 'Informations',
'inst_sqlheaderbox'         => 'Informations SQL',
'inst_mysqlinfo'            => "Le client MySQL <b>et</b> la version du serveurr 4.0.4 ou plus ainsi que le support des tables InnoDB sont nécessaires pour EQdkp.<br>
                                <b><br>Vous utilisez la version serveur <ul>%s</ul> et la version client <ul>%s.</ul></b><br>
                                les versions MySQL antérieures à 4.0.4 ne fonctionnent pas et ne sont pas supportées. Les versions antérieures à 4.0.4<br>
                                posent des problèmes de corruption de données, et nous ne fourniront aucun support sur ces installations.<br><br>",
'inst_button3'              => 'Continuer',
'inst_button_back'          => 'Retour',
'inst_sql_error'            => "Erreur ! Echec d'\'exécution de la requête SQL : <br><br><ul>%1\$s</ul><br>Erreur : %2\$s [%3\$s]",
'insinfo_dbready'           => 'La connexion à la base a été vérifiée et aucune erreur n\'a été détectée. Vous pouvez continuer l\'installation.',

// Errors
'INST_ERR'                  => 'Erreur d\'installation',
'INST_ERR_PREFIX'           => 'Le préfixe de la base existe déjà. Veuillez revenir en arrière et choisissez un autre préfixe ou vos données seront perdues !',
'INST_ERR_DB_CONNECT'       => 'Impossible de se connecter à la base, voir le message d\'erreur ci-dessous.',
'INST_ERR_DB_NO_ERROR'      => 'Pas de message d\'erreur indiqué.',
'INST_ERR_DB_NO_MYSQLI'     => 'La version de MySQL installée sur cette machine est incompatible avec l\'option “MySQL with MySQLi Extension” choisie. Veuillez essayer l\'option “MySQL” à la place.',
'INST_ERR_DB_NO_NAME'       => 'Pas de nom de base indiqué.',
'INST_ERR_PREFIX_INVALID'   => 'Le préfixe de table indiqué n\'est pas valide pour votre base de données. Veuillez en choisir un autre, en ôtant les caractères comme hyphénation, apostrophe ou les barres obliques.',
'INST_ERR_PREFIX_TOO_LONG'  => 'Le préfixe de tabe indiqué est trop long. La longueur maximale est de %d caractères.',

// ===========================================================
//	Step 4: Server
// ===========================================================
'inst_language_config'      => 'Configuration de la langue',
'inst_default_lang'         => 'Langue par défaut',
'inst_default_locale'       => 'Local par défaut',

'inst_game_config'          => 'Configuration du jeu',
'inst_default_game'         => 'Jeu par défaut',

'inst_server_config'        => 'Configuration du serveur',
'inst_server_name'          => 'Nom de domaine',
'inst_server_port'          => 'Port du serveur',
'inst_server_path'          => 'Chemin du script',

'inst_button4'              => 'Installer la base',

// ===========================================================
//	Step 5: Accounts
// ===========================================================
'inst_administrator_config' => 'Configuration du compte administrateur',
'inst_username'             => 'Nom de l\'administrateur',
'inst_user_password'        => 'Mot de passe de l\'administrateur',
'inst_user_pw_confirm'      => 'Confirmer le mot de passe',
'inst_user_email'           => 'Adresse email de l\'administrateur',

'inst_button5'              => 'Installer les comptes',

'inst_writerr_confile'      => 'le fichier <b>config.php</b> n\'a pas pu être ouvert pour l\'écriture. Copier-coller les lignes suivantes dans le fichier config.php
                                et sauvegarder pour continuer:',
'inst_confwritten'          => 'Votre fichier de configuration a été enregistré avec les valeurs initiales, maisn l\installation ne pourra pas se termniner avant
                                de créer un compte administrateur à la prochaine étape.',
'inst_checkifdbexists'      => 'Avant de continuer, veuillez vérifier que la base de données avec le nom indiqué est déjà créée et que l\'utilisateur précisé à les permission pour créer des tables.',
'inst_wrong_dbtype'         => "Impossible de trouver la couche d\'abstraction de la base pour <b>%s</b>, vérifiez que %s existe.",
'inst_failedconhost'        => "Echec de la connexion à la base <b>%s</b> comme <b>%s@%s</b>
                                <br /><br /><a href='index.php'>Recommencer l\'installation</a>",
'inst_failedversioninfo'    => "Impossible d\'obtenir les informatiosn de version pour la base database <b>%s</b> comme <b>%s@%s</b>
                                <br /><br /><a href='index.php'>Recommencer l\'installation</a>",

// ===========================================================
//	Step 5: Finish
// ===========================================================
'login'                     => 'Connexion',
'username'                  => 'Utilisateur',
'password'                  => 'Mot de passe',
'remember_password'         => 'Se souvenir de moi (cookie)',

'login_button'              => 'Connexion',

'inst_passwordnotmatch'     => 'Votre mot de passe est incorrect, il a donc été réinitialisé avec <b>admin</b>.  Vous pouvez le changer en accédant à vos paramètres de compte après votre connexion.',
'inst_admin_created'        => 'Votre compte administrateur a été créé, veuillez vous connecter pour accéder à la page de configuration.',
);
?>
