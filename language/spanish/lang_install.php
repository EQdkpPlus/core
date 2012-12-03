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
'inst_header'               => 'Instalación',

// ===========================================================
//	Per Language default settings
// ===========================================================
'game_language'             => 'es',
'default_lang'              => 'spanish',
'default_locale'            => 'es_ES',

// ===========================================================
//	Prepare Installation
// ===========================================================
'installation_message'      => 'Nota de Instalación',
'installation_messages'     => 'Notas de Instalación',
'error'                     => 'Error',
'errors'                    => 'Errores',
'lerror'                    => 'ERROR',
'notice'                    => 'ATENCIÓN',
'install_error'             => 'Error de Instalación',
'inst_step'                 => 'Paso',
'error_nostructure'         => 'No se pudo obtener la estructura/datos SQL',
'error_template'            => "No se pudo incluir '%s' includes/class_template.php - asegúrese de que el archivo existe!",

// ===========================================================
//	Stepnames
// ===========================================================
'stepname_1'                => 'Información',
'stepname_2'                => 'Base de Datos',
'stepname_3'                => 'Comprobar Base de Datos',
'stepname_4'                => 'Información de Servidor',
'stepname_5'                => 'Cuenta',
'stepname_6'                => 'Finalizar',

// ===========================================================
//	Step 1: PHP / Mysql Environment
// ===========================================================
'language_selector'         => 'Por favor, seleccione el idioma preferido',
'install_language'          => 'Lenguaje de Instalación',
'already_installed'         => 'EQdkp ya está instalado - por favor elimina el directorio <b>install/</b>.',
'conf_not_write'            => 'El archivo <b>config.php</b> no existe y no puede ser creado en el directorio EQdkp.<br />
                                Debe crear un archivo config.php en su carpeta raiz del servidor antes de continuar.',
'conf_written'              => 'Se ha creado el archivo <b>config.php</b> en el directorio raiz de EQdkp.<br />
                                Eliminar este archivo interfiere con el funcionamiento de la instalación de EQdkp.',
'conf_chmod'                => 'El archivo <b>config.php</b> no tiene permisos de Lectura/Escritura y no ha podido ser cambiado automaticamente.
                                <br />Por favor, cambie los permisos a 0666 manualmente ejecutando <b>chmod 0666 config.php</b> en su servidor.',
'conf_writable'             => 'El archivo <b>config.php</b> ha sido cambiado a Lectura/Escritura a fin de dejar a este instalador escribir su archivo de 
                                configuracion automaticamente.',
'templcache_notcreated'     => 'El directorio "Cache" en "Templates" no ha podido ser creado, por favor cree uno manualmente en el directorio "Templates".
                                <br />Usted puede hacer esto cambiando al directorio raiz de EQdkp y escribiendo esto: <b>mkdir -p templates/cache/</b> con eso obtendra la carpeta "Cache" dentro del directorio "Templates".',
'templcache_created'     => 'El directorio "Cache" ha sido creado dentro del directorio "Templates", borrar este archivo puede interferir
                                con el funcionamiento de la instalación EQdkp.',
'templcache_notwritable'       => 'El directorio %1\$s existe, pero no tiene permisos de escritura y tampoco puede cambiarse automaticamente.
                                <br />Por favor cambie los permisos a 0777 manualmente ejecutando <b>chmod 0777 templates/cache</b> en su servidor.',
'templatecache_ok'          => 'Al directorio "Cache" en "Templates" se le han puesto permisos de escritura a fin de dejar al motor de las "Templates" crear
                                versiones compiladas para acelerar la visualización de EQdkp.',
'cachefolder_out'           => "La carpeta '%1\$s' ha sido %2\$s y es %3\$s",

'connection_failed'         => 'Conexión a EQdkp-PLUS.com fallada.',
'curl_notavailable'         => 'cURL no esta activo. Posiblemente no funcione correctamente Itemstats.',
'fopen_notavailable'        => 'fopen no esta activo. Posiblemente no funcione correctamente Itemstats.',

'minimal_requ_notfilled'    => 'Lo sentimos, su servidor no cumple los requisitos mínimos para EQdkp',
'minimal_requ_filled'       => 'EQdkp ha escaneado su servidor y cumple con los requisitos mínimos para la instalación.',

'inst_unknown'              => 'Desconocido',
'eqdkp_name'                => 'EQdkp PLUS',
'inst_eqdkpv'               => 'Versión EQDKP Plus',
'inst_latest'               => 'Ultima Estable',

'inst_php'                  => 'PHP',
'inst_view'                 => 'Ver phpinfo()',
'inst_version'              => 'Versión',
'inst_required'             => 'Requerido',
'inst_available'            => 'Disponible',
'inst_enabled'              => 'Activado',
'inst_using'                => 'Usada',
'inst_yes'                  => 'Si',
'inst_no'                   => 'No',

'inst_mysqlmodule'          => 'Modulo MySQL',
'inst_zlibmodule'           => 'Modulo zLib',
'inst_curlmodule'           => 'Modulo cURL',
'inst_fopen'                => 'fopen',
'inst_safemode'             => 'Modo a prueba de Errores',

'inst_php_modules'          => 'Modulos PHP',
'inst_Supported'            => 'Soportado',

'inst_found'                => 'Encontrada',
'inst_writable'             => 'Escribible',
'inst_notfound'             => 'No Encontrado',
'inst_unwritable'           => 'No Escribible',

'inst_button1'              => 'Iniciar Instalación',

// ===========================================================
//	Step 2: Database
// ===========================================================
'inst_database_conf'        => 'Configuración de la Base de datos',
'inst_dbtype'               => 'Tipo de la Base de datos',
'inst_dbhost'               => 'Host de la Base de datos',
'inst_dbname'               => 'Nombre de la Base de datos',
'inst_dbuser'               => 'Nombre Usuario de la Base de datos',
'inst_dbpass'               => 'Contraseña de la Base de datos',
'inst_table_prefix'         => 'Prefijo de las Tablas EQdkp',
'inst_button2'              => 'Probar la Base de datos',

// ===========================================================
//	Step 3: Database cofirmation
// ===========================================================
'inst_error_nodbname'       => 'No puso un nombre en la Base de datos!',
'inst_error_prefix'         => 'No puso un prefijo de Tabla EQdkp! Por favor, vuelva atras y escriba un prefijo.',
'inst_error_prefix_inval'   => 'Prefijo Invalido',
'inst_error_prefix_toolong' => 'Prefijo muy largo!',
'inserror_dbconnect'        => 'Error al conectar con la Base de datos',
'insterror_no_mysql'        => 'La Base de datos no es MySQL!',
'inst_redoit'               => 'Reiniciar Instalación',
'db_warning'                => 'Advertencia',
'db_information'            => 'Información',
'inst_sqlheaderbox'         => 'Información SQL',
'inst_mysqlinfo'            => "El cliente MySQL y la version de servidor deben ser superior a la 4.0.4 ya que el soporte para la tabla InnoDB es requerida para EQdkp.<br>
                                <b><br>Su version del servidor es <ul>%s</ul> y su version de cliente <ul>%s.</ul></b><br>
                                Versiones inferiores a la 4.0.4 no funcionan y no son soportadas. Con versiones inferiores a la 4.0.4
                                esperimentaran la corrupcion de datos, y no proporcionaremos ayuda en estas instalaciones.<br><br>",
'inst_button3'              => 'Seguir',
'inst_button_back'          => 'Atras',
'inst_sql_error'            => "Error! No se ha podido ejecutar la sentencia SQL: <br><br><ul>%1\$s</ul><br>Error: %2\$s [%3\$s]",
'insinfo_dbready'           => 'Se comprobo la conexión con la base de datos y no hubo errores. Puede serguir con la instalación.',

// Errors
'INST_ERR'                  => 'Error de Instalación',
'INST_ERR_PREFIX'           => 'El prefijo de la base de datos ya existe. Por favor, vuelva atras y cambielo por otro o se sobrescribiran los datos.',
'INST_ERR_DB_CONNECT'       => 'No se ha podido conectar a la base de datos, vea el siguiente mensaje de error.',
'INST_ERR_DB_NO_ERROR'      => 'No dio ningún mensaje de error.',
'INST_ERR_DB_NO_MYSQLI'     => 'La versión de MySQL instalada en esta maquina es incompatible con la “MySQL con extension MySQLi” opcion que ha seleccionado. Por favor, intente en su luegar la opcion “MySQL”.',
'INST_ERR_DB_NO_NAME'       => 'No se especifico ningun nombre de la base de datos.',
'INST_ERR_PREFIX_INVALID'   => 'El prefijo de tabla que ha especificado no es válido para su base de datos. Por favor, pruebe con otro, eliminando caracteres como el guion, apostrofe o barras invertidas.',
'INST_ERR_PREFIX_TOO_LONG'  => 'El prfijo de tabla que ha especificado es muy largo. La longitud maxima es de %d caracteres.',

// ===========================================================
//	Step 4: Server
// ===========================================================
'inst_language_config'      => 'Configuración de Idioma',
'inst_default_lang'         => 'Idioma por defecto',
'inst_default_locale'       => 'Lugar por defecto',

'inst_game_config'          => 'Configuración de Juego',
'inst_default_game'         => 'Juego por defecto',

'inst_server_config'        => 'Configuración del Servidor',
'inst_server_name'          => 'Nombre de dominio',
'inst_server_port'          => 'Puerto del Servidor',
'inst_server_path'          => 'Ruta del Script',

'inst_button4'              => 'Instalar Base de datos',

// ===========================================================
//	Step 5: Accounts
// ===========================================================
'inst_administrator_config' => 'Configuración de la Cuenta del Administrador',
'inst_username'             => 'Nombre de Usuario del Administrador',
'inst_user_password'        => 'Contraseña',
'inst_user_pw_confirm'      => 'Confirmar Contraseña',
'inst_user_email'           => 'Email del Administrador',

'inst_button5'              => 'Instalar Cuentas',

'inst_writerr_confile'      => 'El archivo <b>config.php</b> no pudo abrirse para la Escritura.  Pegue el siguiente texto en el archivo config.php y guardelo
                                para continuar:',
'inst_confwritten'          => 'Su archivo de configuración ha sido escrito con los valores iniciales, pero la instalación no será completa hasta que usted 
                                cree una cuenta de administrador en el siguiente paso.',
'inst_checkifdbexists'      => 'Antes de proceder, por favor, compruebe que el nombre de base de datos que ha facilitado ya está creada y que el usuario tiene permisos para crear tablas en la base de datos.',
'inst_wrong_dbtype'         => "Incapaz de encontrar la capa de abstracción de base de datos para <b> %s </b>, compruebelo para asegurarse que %s existe.",
'inst_failedconhost'        => "Error al conectar a la base de datos <b>%s</b> como <b>%s@%s</b>
                                <br /><br /><a href='index.php'>Reinicie la Instalación</a>",
'inst_failedversioninfo'    => "No se ha podido obtener la información de version de la base de datos <b>%s</b> como <b>%s@%s</b>
                                <br /><br /><a href='index.php'>Reinicie la Instalación</a>",

// ===========================================================
//	Step 5: Finish
// ===========================================================
'login'                     => 'Inicio de Sesión',
'username'                  => 'Usuario',
'password'                  => 'Contraseña',
'remember_password'         => 'Recuérdeme (cookie)',

'login_button'              => 'Inicio de Sesión',

'inst_passwordnotmatch'     => 'Las contraseñas no coinciden, por lo que se ha restablecido a <b>admin</b>.  Usted puede cambiarlo accediendo a sus ajustes de cuenta.',
'inst_admin_created'        => 'Su cuenta de administrador ha sido creada, inicie sesion arriba para ir a la pagina de configuracion de EQdkp.',
);
?>
