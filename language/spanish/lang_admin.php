<?php
/******************************
 * EQdkp
 * Copyright 2002-2003
 * Licensed under the GNU GPL.  See COPYING for full terms.
 * ------------------
 * lang_admin.php
 * Began: Fri January 3 2003
 *
 * $Id: lang_admin.php 3975 2009-02-24 15:54:48Z osr-corgan $
 *
 ******************************/

if ( !defined('EQDKP_INC') )
{
     die('Do not access this file directly.');
}

// %1\$<type> prevents a possible error in strings caused
//      by another language re-ordering the variables
// $s is a string, $d is an integer, $f is a float

// Titles
$lang['addadj_title']         = 'Añadir un ajuste de Grupo';
$lang['addevent_title']       = 'Añadir un Evento';
$lang['addiadj_title']        = 'Añadir un ajuste individual';
$lang['additem_title']        = 'Añadir una compra de Objeto';
$lang['addmember_title']      = 'Añadir un miembro de Hermandad';
$lang['addnews_title']        = 'Añadir una Noticia';
$lang['addraid_title']        = 'Añadir una Raid';
$lang['addturnin_title']      = "Transferencia de Objetos - Paso %1\$d";
$lang['admin_index_title']    = 'Menu Administracion';
$lang['config_title']         = 'Script Configuracion';
$lang['manage_members_title'] = 'Administrar Miembros Hermandad';
$lang['manage_users_title']   = 'Cuentad de usuario y permisos';
$lang['parselog_title']       = 'Analizar un archivo de registro';
$lang['plugins_title']        = 'Administrar Plugins';
$lang['styles_title']         = 'Administrar Estilos';
$lang['viewlogs_title']       = 'Visor de Registro';

// Page Foot Counts
$lang['listusers_footcount']             = "... encontrado(s) %1\$d usuario(s) / %2\$d por pagina";
$lang['manage_members_footcount']        = "... encontrado(s) %1\$d miembro(s)";
$lang['online_footcount']                = "... %1\$d usuario(s) en linea";
$lang['viewlogs_footcount']              = "... encontrado(s) %1\$d registro(s) / %2\$d por pagina";

// Submit Buttons
$lang['add_adjustment'] = 'Añadir Ajuste';
$lang['add_account'] = 'Añadir Cuenta';
$lang['add_event'] = 'Añadir Evento';
$lang['add_item'] = 'Añadir Objeto';
$lang['add_member'] = 'Añadir Miembro';
$lang['add_news'] = 'Añadir Noticia';
$lang['add_raid'] = 'Añadir Raid';
$lang['add_style'] = 'Añadir Estilo';
$lang['add_turnin'] = 'Añadir Transferencia de Objeto';
$lang['delete_adjustment'] = 'Borrar Ajuste';
$lang['delete_event'] = 'Borrar Evento';
$lang['delete_item'] = 'Borrar Objeto';
$lang['delete_member'] = 'Borrar Miembro';
$lang['delete_news'] = 'Borrar Noticia';
$lang['delete_raid'] = 'Borrar Raid';
$lang['delete_selected_members'] = 'Borrar Miembro(s) selecionado(s)';
$lang['delete_style'] = 'Borrar Estilo';
$lang['mass_delete'] = 'Borrar en masa';
$lang['mass_update'] = 'Actualizar en masa';
$lang['parse_log'] = 'Analizar Registro';
$lang['search_existing'] = 'Buscar Existencia';
$lang['select'] = 'Seleccionar';
$lang['transfer_history'] = 'Transferir Historial';
$lang['update_adjustment'] = 'Actualizar Ajuste';
$lang['update_event'] = 'Actualizar Evento';
$lang['update_item'] = 'Actualizar Objeto';
$lang['update_member'] = 'Actualizar Miembro';
$lang['update_news'] = 'Actualizar Noticia';
$lang['update_raid'] = 'Actualizar Raid';
$lang['update_style'] = 'Actualizar Estilo';

// Misc
$lang['account_enabled'] = 'Cuenta Activa';
$lang['adjustment_value'] = 'Valor de Ajuste';
$lang['adjustment_value_note'] = 'Puede ser Negativo';
$lang['code'] = 'Código';
$lang['contact'] = 'Contacto';
$lang['create'] = 'Crear';
$lang['found_members'] = "Analizadas %1\$d lineas, encontrados %2\$d miembros";
$lang['headline'] = 'Titulo';
$lang['hide'] = 'Esconder?';
$lang['install'] = 'Instalar';
$lang['item_search'] = 'Buscar Objeto';
$lang['list_prefix'] = 'Prefijo de Lista';
$lang['list_suffix'] = 'Sufijo de Lista';
$lang['logs'] = 'Registros';
$lang['log_find_all'] = 'Buscar todos (incluidos los anonimos)';
$lang['manage_members'] = 'Administrar Miembros';
$lang['manage_plugins'] = 'Administrar Plugins';
$lang['manage_users'] = 'Administrar Usuarios';
$lang['mass_update_note'] = 'Si usted desea aplicar cambios en todos los artículos seleccionados anteriormente, use estos controles para cambiar sus propiedades y haga clic en "Actualizar en masa".
                             Para borrar las cuentas seleccionadas, sólo haga clic en "Borrar en masa".';
$lang['members'] = 'Miembros';
$lang['member_rank'] = 'Rango Miembro';
$lang['message_body'] = 'Cuerpo del Mensaje';
$lang['message_show_loot_raid'] = 'Mostrar loot de Raid:';
$lang['results'] = "%1\$d Resultados (\"%2\$s\")";
$lang['search'] = 'Buscar';
$lang['search_members'] = 'Buscar Miembros';
$lang['should_be'] = 'En caso de';
$lang['styles'] = 'Estilos';
$lang['title'] = 'Titulo';
$lang['uninstall'] = 'Desinstalar';
$lang['enable']		= 'Habilitar';
$lang['update_date_to'] = "Actualizar fecha de<br />%1\$s?";
$lang['version'] = 'Version';
$lang['x_members_s'] = "%1\$d miembro";
$lang['x_members_p'] = "%1\$d miembros";

// Permission Messages
$lang['noauth_a_event_add']    = 'No tiene permisos para añadir eventos.';
$lang['noauth_a_event_upd']    = 'No tiene permisos para actualizar eventos.';
$lang['noauth_a_event_del']    = 'No tiene permisos para borrar eventos.';
$lang['noauth_a_groupadj_add'] = 'No tiene permisos para añadir ajustes de grupo.';
$lang['noauth_a_groupadj_upd'] = 'No tiene permisos para actualizar ajuste de grupo.';
$lang['noauth_a_groupadj_del'] = 'No tiene permisos para borrar ajustes de grupo.';
$lang['noauth_a_indivadj_add'] = 'No tiene permisos para añadir ajustes individuales.';
$lang['noauth_a_indivadj_upd'] = 'No tiene permisos para actualizar ajustes individuales.';
$lang['noauth_a_indivadj_del'] = 'No tiene permisos para borrar ajustes individuales.';
$lang['noauth_a_item_add']     = 'No tiene permisos para añadir objeto.';
$lang['noauth_a_item_upd']     = 'No tiene permisos para actualizar objeto.';
$lang['noauth_a_item_del']     = 'No tiene permisos para borrar objeto.';
$lang['noauth_a_news_add']     = 'No tiene permisos para añadir noticias.';
$lang['noauth_a_news_upd']     = 'No tiene permisos para actualizar noticias.';
$lang['noauth_a_news_del']     = 'No tiene permisos para borrar noticias.';
$lang['noauth_a_raid_add']     = 'No tiene permisos para añadir raids.';
$lang['noauth_a_raid_upd']     = 'No tiene permisos para actualizar raids.';
$lang['noauth_a_raid_del']     = 'No tiene permisos para borrar raids.';
$lang['noauth_a_turnin_add']   = 'No tiene permisos para añadir una Transferencia de Objeto.';
$lang['noauth_a_config_man']   = 'No tiene permisos para administrar ajustes de configuracion EQDKP';
$lang['noauth_a_members_man']  = 'No tiene permisos para administrar miembros de hermandad.';
$lang['noauth_a_plugins_man']  = 'No tiene permisos para administrar plugins de EQDKP.';
$lang['noauth_a_styles_man']   = 'No tiene permisos para administrar estilos de EQDKP.';
$lang['noauth_a_users_man']    = 'No tiene permisos para administrar la configuracion de cuentas de usuario.';
$lang['noauth_a_logs_view']    = 'No tiene permisos para ver registros de EQDKPK.';

// Submission Success Messages
$lang['admin_add_adj_success']               = "Un %1\$s ajuste de %2\$.2f se ha añadido a la base de datos.";
$lang['admin_add_admin_success']             = "Se ha enviado un e-mail a %1\$s con su información administrativa.";
$lang['admin_add_event_success']             = "Se ha añadido un valor predeterminado de %1\$s para las bandas en el evento %2\$s a la base de datos.";
$lang['admin_add_iadj_success']              = "Se ha añadido un ajuste individual de %2\$.2f %1\$s para %3\$s a la base de datos.";
$lang['admin_add_item_success']              = "Se ha añadido una compra de objeto %1\$s, por %2\$s con el coste de %3\$.2f DKP a la base de datos.";
$lang['admin_add_member_success']            = "%1\$s se ha añadido como miembro de su hermandad.";
$lang['admin_add_news_success']              = 'La noticia se añadió a la base de datos.';
$lang['admin_add_raid_success']              = "La banda del %1\$d/%2\$d/%3\$d en %4\$s ha sido añadida a la base de datos.";
$lang['admin_add_style_success']             = 'El nuevo estilo se añadió con éxito.';
$lang['admin_add_turnin_success']            = "%1\$s ha sido transferido de %2\$s a %3\$s.";
$lang['admin_delete_adj_success']            = "Se ha borrado el ajuste %1\$s de %2\$.2f de la base de datos.";
$lang['admin_delete_admins_success']         = "Se han borrado los administradores seleccionados.";
$lang['admin_delete_event_success']          = "Se ha eliminado el evento %2\$s de la base de datos.";
$lang['admin_delete_iadj_success']           = "Se ha borrado el ajuste individual de %2\$.2f %1\$s para %3\$s de la base de datos.";
$lang['admin_delete_item_success']           = "La compra de %1\$s, por %2\$s con el coste de %3\$.2f DKP se ha borrado de la base de datos.";
$lang['admin_delete_members_success']        = "Se ha borrado a %1\$s junto a todo su historial de la base de datos.";
$lang['admin_delete_news_success']           = 'Se borró la noticia de la base de datos.';
$lang['admin_delete_raid_success']           = 'La banda y los objetos asociados a ella han sido borrados de la base de datos.';
$lang['admin_delete_style_success']          = 'Se ha borrado el estilo de la base de datos.';
$lang['admin_delete_user_success']           = "Se ha borrado la cuenta del usuario %1\$s.";
$lang['admin_set_perms_success']             = "Todos los permisos administrativos han sido actualizados en los miembros seleccionados.";
$lang['admin_transfer_history_success']      = "Todo el historial de %1\$s ha sido transferido a %2\$s y %1\$s ha sido borrado de la base de datos.";
$lang['admin_update_account_success']        = "Se ha actualizado la configuración de su cuenta en la base de datos.";
$lang['admin_update_adj_success']            = "Se ha actualizado el ajuste %1\$s de %2\$.2f en la base de datos.";
$lang['admin_update_event_success']          = "El valor predeterminado de %1\$s para las bandas en el evento %2\$s ha sido actualizado en la base de datos.";
$lang['admin_update_iadj_success']           = "Un ajuste individual de %2\$.2f %1\$s para %3\$s ha sido actualizado en la base de datos.";
$lang['admin_update_item_success']           = "Se actualizó la compra de %1\$s, por %2\$s con el coste de %3\$.2f DKP en la base de datos.";
$lang['admin_update_member_success']         = "Se ha actualizado la configuración de %1\$s.";
$lang['admin_update_news_success']           = 'Se ha actualizado la noticia en la base de datos.';
$lang['admin_update_raid_success']           = "La banda del %1\$d/%2\$d/%3\$d en %4\$s se actualizó en la base de datos.";
$lang['admin_update_style_success']          = 'El estilo se ha actualizado con éxito.';

$lang['admin_raid_success_hideinactive']     = 'Actualizando estado de jugador activo/inactivo ...';

// Delete Confirmation Texts
$lang['confirm_delete_adj']     = '¿Está seguro de que desea borrar este ajuste de grupo?';
$lang['confirm_delete_admins']  = '¿Está seguro de que desea borrar el administrador seleccionado?';
$lang['confirm_delete_event']   = '¿Está seguro de que desea borrar este evento?';
$lang['confirm_delete_iadj']    = '¿Está seguro de que desea borrar este ajuste individual?';
$lang['confirm_delete_item']    = '¿Está seguro de que desea borrar este objeto?';
$lang['confirm_delete_members'] = '¿Está seguro de que desea borrar los siguientes miembros?';
$lang['confirm_delete_news']    = '¿Está seguro de que desea borrar esta noticia?';
$lang['confirm_delete_raid']    = '¿Está seguro de que desea borrar esta banda?';
$lang['confirm_delete_style']   = '¿Está seguro de que desea borrar este estilo?';
$lang['confirm_delete_users']   = '¿Está seguro de que desea borrar las siguientes cuentas de usuario?';

// Log Actions
$lang['action_event_added']      = 'Evento añadido';
$lang['action_event_deleted']    = 'Evento borrado';
$lang['action_event_updated']    = 'Evento actualizado';
$lang['action_groupadj_added']   = 'Ajuste de Grupo añadido';
$lang['action_groupadj_deleted'] = 'Ajuste de Grupo borrado';
$lang['action_groupadj_updated'] = 'Ajuste de Grupo actualizado';
$lang['action_history_transfer'] = 'Historial de miembro transferido';
$lang['action_indivadj_added']   = 'Ajuste individual añadido';
$lang['action_indivadj_deleted'] = 'Ajuste individual borrado';
$lang['action_indivadj_updated'] = 'Ajuste individual actualizado';
$lang['action_item_added']       = 'Objeto añadido';
$lang['action_item_deleted']     = 'Objeto borrado';
$lang['action_item_updated']     = 'Objeto actualizado';
$lang['action_member_added']     = 'Miembro añadido';
$lang['action_member_deleted']   = 'Miembro aorrado';
$lang['action_member_updated']   = 'Miembro actualizado';
$lang['action_news_added']       = 'Noticia añadida';
$lang['action_news_deleted']     = 'Noticia borrada';
$lang['action_news_updated']     = 'Noticia actualizada';
$lang['action_raid_added']       = 'Banda añadida';
$lang['action_raid_deleted']     = 'Banda borrada';
$lang['action_raid_updated']     = 'Banda actualizada';
$lang['action_turnin_added']     = 'Transferencia de objeto añadida';

// Before/After
$lang['adjustment_after']  = 'Ajuste siguiente';
$lang['adjustment_before'] = 'Ajuste anterior';
$lang['attendees_after']   = 'Asistentes siguiente';
$lang['attendees_before']  = 'Asistentes anterior';
$lang['buyers_after']      = 'Comprador siguiente';
$lang['buyers_before']     = 'Comprador anterior';
$lang['class_after']       = 'Clase siguiente';
$lang['class_before']      = 'Clase anterior';
$lang['earned_after']      = 'Ganado siguiente';
$lang['earned_before']     = 'Ganado anterior';
$lang['event_after']       = 'Evento siguiente';
$lang['event_before']      = 'Evento anterior';
$lang['headline_after']    = 'Encabezamiento siguiente';
$lang['headline_before']   = 'Encabezamiento anterior';
$lang['level_after']       = 'Nivel siguiente';
$lang['level_before']      = 'Nivel anterior';
$lang['members_after']     = 'Miembros siguiente';
$lang['members_before']    = 'Miembros anterior';
$lang['message_after']     = 'Mensaje siguiente';
$lang['message_before']    = 'Mensaje anterior';
$lang['name_after']        = 'Nombre siguiente';
$lang['name_before']       = 'Nombre anterior';
$lang['note_after']        = 'Nota siguiente';
$lang['note_before']       = 'Nota anterior';
$lang['race_after']        = 'Raza siguiente';
$lang['race_before']       = 'Raza anterior';
$lang['raid_id_after']     = 'ID de banda siguiente';
$lang['raid_id_before']    = 'ID de banda anterior';
$lang['reason_after']      = 'Siguiente razón';
$lang['reason_before']     = 'Razón anterior';
$lang['spent_after']       = 'Gastado siguiente';
$lang['spent_before']      = 'Gastado anterior';
$lang['value_after']       = 'Valor siguiente';
$lang['value_before']      = 'Valor anterior';

// Configuration
$lang['general_settings'] = 'Configuración General';
$lang['guildtag'] = 'Nombre de Hermandad';
$lang['guildtag_note'] = 'Usada en el titulo de casi todas las paginas';
$lang['parsetags'] = 'Analizar etiqueta';
$lang['parsetags_note'] = 'Aquellos puestos en la lista estarán disponibles como opciones a la hora de analizar registros de banda.';
$lang['domain_name'] = 'Nombre de dominio';
$lang['server_port'] = 'Puerto del servidor';
$lang['server_port_note'] = 'Su puerto del servidor. Normalmente, 80.';
$lang['script_path'] = 'Ruta de Script';
$lang['script_path_note'] = 'Ruta donde se encuentra EQDKP-Plus, en relación con el nombre de dominio.';
$lang['site_name'] = 'Nombre del sitio';
$lang['site_description'] = 'Descripción del sitio';
$lang['point_name'] = 'Nombre de Puntos';
$lang['point_name_note'] = 'Ejem: DKP, RP, etc.';
$lang['enable_account_activation'] = 'Permitir activación de cuenta';
$lang['none'] = 'Ninguno';
$lang['admin'] = 'Administrador';
$lang['default_language'] = 'Lenguaje por defecto';
$lang['default_locale'] = 'Lugar por defecto (sólo juego de caracteres; no afecta el lenguaje)';
$lang['default_game'] = 'Juego por defecto';
$lang['default_game_warn'] = 'El cambio del juego por defecto puede anular otros cambios de esta sesión.';
$lang['default_style'] = 'Estilo por defecto';
$lang['default_page'] = 'Pagina de inicio por defecto';
$lang['hide_inactive'] = 'Esconder miembros inactivos';
$lang['hide_inactive_note'] = '¿Ocultar los miembros que no han asistido a una banda en el [periodo de inactividad] dias?';
$lang['inactive_period'] = 'Periodo de inactividad';
$lang['inactive_period_note'] = 'Número de días que un miembro puede no asistir a una banda y todavía considerarse activo';
$lang['inactive_point_adj'] = 'Ajuste de puntos de inactividad';
$lang['inactive_point_adj_note'] = 'Puntos de ajuste para hacer que un miembro pase a ser inactivo.';
$lang['active_point_adj'] = 'Ajuste de puntos de activo';
$lang['active_point_adj_note'] = 'Puntos de ajuste para hacer que un miembro pase a ser activo.';
$lang['enable_gzip'] = 'Activar compresión GZip';
$lang['show_item_stats'] = 'Mostrar estadísticas de objeto';
$lang['show_item_stats_note'] = 'Trata de coger las estadísticas del objeto de Internet. Puede afectar la velocidad de determinadas paginas.';
$lang['default_permissions'] = 'Permisos por defecto';
$lang['default_permissions_note'] = 'Éstos son los permisos para usuarios que no se han identificado, y son dados a nuevos usuarios cuando se registran. Las opciones en <b>Negrita</b> son permisos administrativos, se recomienda no marcar estas opciones por defecto. Las opciones en <i>Cursiva</i> son utilizadas exclusivamente por los plugin. Puedes cambiar más adelante los permisos de cada usuario individualmente, solo tiene que ir a "Administrar Usuarios" en el menú "Administración General".';
$lang['plugins'] = 'Plugins';
$lang['no_plugins'] = 'La carpeta plugin esta vacía (./plugins/).';
$lang['cookie_settings'] = 'Configuración de la Cookie';
$lang['cookie_domain'] = 'Dominio de Cookie';
$lang['cookie_name'] = 'Nombre de Cookie';
$lang['cookie_path'] = 'Ruta de Cookie';
$lang['session_length'] = 'Duración de la sesión (segundos)';
$lang['email_settings'] = 'Configuración de E-Mail';
$lang['admin_email'] = 'E-Mail del administrador';

// Admin Index
$lang['anonymous'] = 'Anónimo';
$lang['database_size'] = 'Tamaño de la base de datos';
$lang['eqdkp_started'] = 'EQdkp se inició el';
$lang['ip_address'] = 'Dirección IP';
$lang['items_per_day'] = 'Objetos por dia';
$lang['last_update'] = 'Última actualización';
$lang['location'] = 'Ubicación';
$lang['new_version_notice'] = "La versión de EQdkp %1\$s está disponible para su <a href=\"http://sourceforge.net/project/showfiles.php?group_id=69529\" target=\"_blank\">descarga</a>.";
$lang['number_of_items'] = 'Total de objetos';
$lang['number_of_logs'] = 'Total de entradas de registro';
$lang['number_of_members'] = 'Total de miembros (Activos/Inactivos)';
$lang['number_of_raids'] = 'Total de bandas';
$lang['raids_per_day'] = 'Bandas por día';
$lang['statistics'] = 'Estadísticas de la web';
$lang['totals'] = 'Totales';
$lang['version_update'] = 'Actualización de versión';
$lang['who_online'] = 'Quien está en linea';

// Style Management
$lang['style_settings'] = 'Configuración de estilos';
$lang['style_name'] = 'Nombre de estilo';
$lang['template'] = 'Plantilla';
$lang['element'] = 'Elemento';
$lang['background_color'] = 'Color de fondo';
$lang['fontface1'] = 'Fuente Cara 1';
$lang['fontface1_note'] = 'Fuente por defecto en Cara';
$lang['fontface2'] = 'Fuente Cara 2';
$lang['fontface2_note'] = 'Fuente en campo de entrada de Cara';
$lang['fontface3'] = 'Fuente Cara 3';
$lang['fontface3_note'] = 'No se utilizan actualmente';
$lang['fontsize1'] = 'Tamaño de Fuente 1';
$lang['fontsize1_note'] = 'Pequeña';
$lang['fontsize2'] = 'Tamaño de Fuente 2';
$lang['fontsize2_note'] = 'Mediana';
$lang['fontsize3'] = 'Tamaño de Fuente 3';
$lang['fontsize3_note'] = 'Grande';
$lang['fontcolor1'] = 'Color de Fuente 1';
$lang['fontcolor1_note'] = 'Color por Defecto';
$lang['fontcolor2'] = 'Color de Fuente 2';
$lang['fontcolor2_note'] = 'Color utilizado fuera de las tablas (menús, títulos, copyright)';
$lang['fontcolor3'] = 'Color de Fuente 3';
$lang['fontcolor3_note'] = 'Color de fuente en campo de entrada';
$lang['fontcolor_neg'] = 'Color de Fuente Negativo';
$lang['fontcolor_neg_note'] = 'Color para números negativos';
$lang['fontcolor_pos'] = 'Color de Fuente Positivo';
$lang['fontcolor_pos_note'] = 'Color para números positivos';
$lang['body_link'] = 'Color de vínculos';
$lang['body_link_style'] = 'Estilo de vínculos';
$lang['body_hlink'] = 'Color de vínculos Activos';
$lang['body_hlink_style'] = 'Estilo de vínculos activos';
$lang['header_link'] = 'Cabecera del vínculo';
$lang['header_link_style'] = 'Estilo Cabecera del Vinculo';
$lang['header_hlink'] = 'Cabecera del vínculo activo';
$lang['header_hlink_style'] = 'Estilo Cabecera del Vínculo Activo';
$lang['tr_color1'] = 'Color de Fila de Tabla 1';
$lang['tr_color2'] = 'Color de Fila de Tabla 2';
$lang['th_color1'] = 'Color Cabecera de Tabla';
$lang['table_border_width'] = 'Ancho del borde de la tabla';
$lang['table_border_color'] = 'Color del borde de la tabla';
$lang['table_border_style'] = 'Estilo del borde de la tabla';
$lang['input_color'] = 'Color del fondo de campo de entrada';
$lang['input_border_width'] = 'Ancho del borde del campo de entrada';
$lang['input_border_color'] = 'Color del borde del campo de entrada';
$lang['input_border_style'] = 'Estilo del borde del campo de entrada';
$lang['style_configuration'] = 'Configuración de estilo';
$lang['style_date_note'] = 'Para los campos Fecha/Hora, la sintaxis usada es idéntica a la de PHP: función <a href="http://www.php.net/manual/en/function.date.php" target="_blank">date()</a>.';
$lang['attendees_columns'] = 'Columnas de asistentes';
$lang['attendees_columns_note'] = 'Número de columnas a usar para asistentes viendo una banda';
$lang['date_notime_long'] = 'Fecha (largo)';
$lang['date_notime_short'] = 'Fecha (corto)';
$lang['date_time'] = 'Fecha y Hora';
$lang['logo_path'] = 'Nombre de archivo del logo:';
$lang['logo_path_note'] = 'Elija una imagen de /templates/template/images/o inserte URL completa de una imagen en internet. Por favor inserta la URL con http:// al inicio )';
$lang['logo_path_config'] = 'Seleccione un archivo de su disco duro y suba su nuevo logotipo aquí';

// Errors
$lang['error_invalid_adjustment'] = 'No se proporcionó un ajuste válido.';
$lang['error_invalid_plugin']     = 'No se proporcionó un plugin válido.';
$lang['error_invalid_style']      = 'No se proporcionó un estilo válido.';

// Verbose log entry lines
$lang['new_actions']           = 'Ultimas acciones de los Administradores';
$lang['vlog_event_added']      = "%1\$s añade el evento '%2\$s' por valor de %3\$.2f puntos.";
$lang['vlog_event_updated']    = "%1\$s actualiza el evento '%2\$s'.";
$lang['vlog_event_deleted']    = "%1\$s borra el evento '%2\$s'.";
$lang['vlog_groupadj_added']   = "%1\$s añade un ajuste de grupo de %2\$.2f puntos.";
$lang['vlog_groupadj_updated'] = "%1\$s actualiza un ajuste de grupo de %2\$.2f puntos.";
$lang['vlog_groupadj_deleted'] = "%1\$s borra un ajuste de grupo de %2\$.2f puntos.";
$lang['vlog_history_transfer'] = "%1\$s transfirió el historial de %2\$s's a %3\$s.";
$lang['vlog_indivadj_added']   = "%1\$s añade un ajuste individual de %2\$.2f a %3\$d miembro(s).";
$lang['vlog_indivadj_updated'] = "%1\$s actualiza un ajuste individual de %2\$.2f a %3\$s.";
$lang['vlog_indivadj_deleted'] = "%1\$s borra un ajuste individual de %2\$.2f a %3\$s.";
$lang['vlog_item_added']       = "%1\$s añade el objeto '%2\$s' cobrado a %3\$d miembro(s) por %4\$.2f puntos.";
$lang['vlog_item_updated']     = "%1\$s actualiza el objeto '%2\$s' cobrado a %3\$d miembro(s).";
$lang['vlog_item_deleted']     = "%1\$s borrado el objeto '%2\$s' cobrado a %3\$d miembro(s).";
$lang['vlog_member_added']     = "%1\$s añade el miembro %2\$s.";
$lang['vlog_member_updated']   = "%1\$s actualiza el miembro %2\$s.";
$lang['vlog_member_deleted']   = "%1\$s borra el miembro %2\$s.";
$lang['vlog_news_added']       = "%1\$s añade la noticia '%2\$s'.";
$lang['vlog_news_updated']     = "%1\$s actualiza la noticia '%2\$s'.";
$lang['vlog_news_deleted']     = "%1\$s borra la noticia '%2\$s'.";
$lang['vlog_raid_added']       = "%1\$s añade una banda en '%2\$s'.";
$lang['vlog_raid_updated']     = "%1\$s actualiza una banda en '%2\$s'.";
$lang['vlog_raid_deleted']     = "%1\$s borra una banda en '%2\$s'.";
$lang['vlog_turnin_added']     = "%1\$s añade una transferencia del objeto '%4\$s' de %2\$s a %3\$s.";

// Location messages
$lang['adding_groupadj'] = 'Añadir ajuste de grupo';
$lang['adding_indivadj'] = 'Añadir ajuste individual';
$lang['adding_item'] = 'Añadir objeto';
$lang['adding_news'] = 'Añadir noticia';
$lang['adding_raid'] = 'Añadir banda';
$lang['adding_turnin'] = 'Añadir transferencia de objeto';
$lang['editing_groupadj'] = 'Editar ajuste de grupo';
$lang['editing_indivadj'] = 'Editar ajuste individual';
$lang['editing_item'] = 'Editar objeto';
$lang['editing_news'] = 'Editar noticia';
$lang['editing_raid'] = 'Editar banda';
$lang['listing_events'] = 'Listado de eventos';
$lang['listing_groupadj'] = 'Listado de ajustes de grupo';
$lang['listing_indivadj'] = 'Listado de ajustes individuales';
$lang['listing_itemhist'] = 'Listado de historial de objetos';
$lang['listing_itemvals'] = 'Listado de valor de objetos';
$lang['listing_members'] = 'Listado de miembros';
$lang['listing_raids'] = 'Listado de bandas';
$lang['managing_config'] = 'Gestión de configuración EQdkp';
$lang['managing_members'] = 'Gestión de miembros de hermandad';
$lang['managing_plugins'] = 'Gestión de plugins';
$lang['managing_styles'] = 'Gestión de estilos';
$lang['managing_users'] = 'Gestión de cuentas de usuario';
$lang['parsing_log'] = 'Análisis de un registro';
$lang['viewing_admin_index'] = 'Viendo índice de administración';
$lang['viewing_event'] = 'Viendo evento';
$lang['viewing_item'] = 'Viendo objeto';
$lang['viewing_logs'] = 'Viendo registros';
$lang['viewing_member'] = 'Viendo miembros';
$lang['viewing_mysql_info'] = 'Viendo información SQL';
$lang['viewing_news'] = 'Viendo noticia';
$lang['viewing_raid'] = 'Viendo banda';
$lang['viewing_stats'] = 'Viendo estadísticas';
$lang['viewing_summary'] = 'Viendo resumen';

// Help lines
$lang['b_help'] = 'Texto en negrita: [b]texto[/b] (shift+alt+b)';
$lang['i_help'] = 'Texto en cursiva: [i]texto[/i] (shift+alt+i)';
$lang['u_help'] = 'Texto subrayado: [u]texto[/u] (shift+alt+u)';
$lang['q_help'] = 'Texto de cotización: [quote]texto[/quote] (shift+alt+q)';
$lang['c_help'] = 'Texto centrado: [center]texto[/center] (shift+alt+c)';
$lang['p_help'] = 'Insertar imagen: [img]http://url_de_imagen[/img] (shift+alt+p)';
$lang['w_help'] = 'Insertar URL: [url]http://URL[/url] o [url=http://url]texto[/url] (shift+alt+w)';
$lang['it_help'] = 'Insertar objeto: ejem. [item]Coraza de Sentencia[/item] (shift+alt+t)';
$lang['ii_help'] = 'Insertar icono objeto: ejem. [itemicon]Coraza de Sentencia[/itemicon] (shift+alt+o)';

// Manage Members Menu (yes, MMM)
$lang['add_member'] = 'Añadir nuevo miembro';
$lang['list_edit_del_member'] = 'Listar, editar o borrar miembros';
$lang['edit_ranks'] = 'Editar rangos de los miembros';
$lang['transfer_history'] = 'Transferir historial de miembro';

// MySQL info
$lang['mysql'] = 'MySQL';
$lang['mysql_info'] = 'Información MySQL';
$lang['eqdkp_tables'] = 'Tablas EQdkp';
$lang['table_name'] = 'Nombre de tabla';
$lang['rows'] = 'Filas';
$lang['table_size'] = 'Tamaño de tabla';
$lang['index_size'] = 'Tamaño del índice';
$lang['num_tables'] = "%d tablas";

//Backup
$lang['backup']            = 'Copia de seguridad';
$lang['backup_database']   = 'Copia de seguridad de la base de datos';
$lang['backup_title']      = 'Crear copia de seguridad de la base de datos';
$lang['backup_type']       = 'Formato de copia de seguridad :';
$lang['create_table']      = 'Añadir declaraciones \'CREATE TABLE\'? :';
$lang['skip_nonessential'] = '¿Saltar datos no esenciales? <br/> no producirá filas de encarte para eqdkp_sessions.';
$lang['gzip_content']      = '¿Comprimir en GZIP?<br />Producirá un archivo más pequeño si GZIP esta habilitado.';
$lang['backup_no_table_prefix']    = '<strong>ADVERTENCIA:</strong> Su instalación de EQdkp no tiene un prefijo de tabla para sus tablas de la base de datos. Cualquier tabla para plugins que pueda tener no será sostenida.';

// plus
$lang['in_database']  = 'Guardado en la base de datos';

//Log Users Actions
$lang['action_user_added']     = 'Usuario añadido';
$lang['action_user_deleted']   = 'Usuario borrado';
$lang['action_user_updated']   = 'Usuario actualizado';

$lang['vlog_user_added']     = "%1\$s añade el usuario %2\$s.";
$lang['vlog_user_updated']   = "%1\$s actualiza el usuario %2\$s.";
$lang['vlog_user_deleted']   = "%1\$s borra el usuario %2\$s.";

//MultiDKP
$lang['action_multidkp_added']     = "MultiDKP: Tabla-DKP añadida";
$lang['action_multidkp_deleted']   = "MultiDKP: Tabla-DKP borrada";
$lang['action_multidkp_updated']   = "MultiDKP: Tabla-DKP actualizada";
$lang['action_multidkp_header']    = "MultiDKP";

$lang['vlog_multidkp_added']     = "%1\$s añade la tabla-DKP %2\$s.";
$lang['vlog_multidkp_updated']   = "%1\$s actualiza la tabla-DKP %2\$s.";
$lang['vlog_multidkp_deleted']   = "%1\$s borra la tabla-DKP %2\$s.";

$lang['default_style_overwrite']   = "Sobreescribir la configuración de estilo del usuario (todos los usuarios utilizaran el estilo por defecto)";
$lang['class_colors']              = "Colores de clase";

#Plugins
$lang['description'] = 'Descripción';
$lang['manual'] = 'Manual';
$lang['homepage'] = 'Página principal';
$lang['readme'] = 'Léeme';
$lang['link'] = 'Enlace';
$lang['infos'] = 'Infos';

// Plugin Install / Uninstall
$lang['plugin_inst_success']  = 'Éxito';
$lang['plugin_inst_error']  = 'Error';
$lang['plugin_inst_message']  = "El plugin <i>%1\$s</i> ha sido %2\$s con éxito.";
$lang['plugin_inst_installed'] = 'instalado';
$lang['plugin_inst_uninstalled'] = 'desinstalado';
$lang['plugin_inst_errormsg1'] = "Se detectaron errores en el proceso: %1\$s  %2\$s";
$lang['plugin_inst_errormsg2']  = "El %1\$s puede no tener %2\$s correctamente.";

$lang['background_image'] = 'Imagen de Fondo ( 1000x1000px) [opcional]';
$lang['css_file'] = 'Archivo CSS - ignorado la mayor parte del ajuste de color en este sitio. [opcional]';

$lang['plugin_inst_sql_note'] = 'Un error SQL durante la instalación no implica necesariamente una mala instalación del Plugin. Trate de usar el Plugin, si se producen errores, por favor reinstale el Plugin.';

// Plugin Update Warn Class
$lang['puc_perform_intro']          = 'Los siguientes Plugins necesitan actualizar la estructura de su base de datos. Por favor haga click en el enlace "Solucionar" para realizar los cambios en la base de datos para cada plugin.<br/>Las tablas de la base de datos siguientes están obsoletas:';
$lang['puc_pluginneedupdate']       = "<b>%1\$s</b>: (Requiere actualizaciones de base de datos del %2\$s al %3\$s)";
$lang['puc_solve_dbissues']         = 'Solucionar';
$lang['puc_unknown']                = '[desconocido]';

$lang['plus_cache_reset_done']      = '¡Restauración completada!';
$lang['plus_cache_reset_name']      = 'Caché de datos plus';

// Update Check PLUS
$lang['lib_pupd_intro']             = 'Hay nuevas versiones disponibles:';
$lang['lib_pupd_updtxt']            = "<b>%1\$s</b>: %2\$s (instalado: %3\$s), apareció el %4\$s";
$lang['lib_pupd_noupdates']         = 'EQDKP-PLUS y todos los plugins están actualizados. No hay actualizaciones disponibles. Compruébalo de nuevo más tarde...';
$lang['lib_pupd_nodbupdates']       = '¡Todas las tablas de la Base de Datos están actualizadas! No hacen falta actualizaciones.';
$lang['lib_pupd_changelog']         = 'Log de cambios';
$lang['lib_pupd_nochangelog']       = 'No hay información de cambios disponible';
$lang['lib_pupd_download']          = 'Descargar';
$lang['lib_pupd_checknow']          = '¡Revisa ahora las actualizaciones!';

// RSS News
$lang['rssadmin_head1']             = 'Notificaciones';
$lang['rssadmin_head2']             = 'Noticias de Eqdkp-Plus';

// Admin Info Center
$lang['adminc_information']         = 'Información';
$lang['adminc_news']                = 'Noticias';
$lang['adminc_updtcheck']           = 'Actualizaciones';
$lang['adminc_statistics']          = 'Estadísticas';
$lang['adminc_server']              = 'Información PHP';
$lang['adminc_support']             = 'Soporte';
$lang['adminc_phpvalue']            = 'Valor';
$lang['adminc_phpname']             = 'Nombre de configuración PHP';
$lang['adminc_info_dbupdates_av']   = 'Hay tablas de los plugins que necesitan actualizarse. Por favor, visita la pestaña de "Actualizaciones" para más información.';
$lang['adminc_info_updates_av']     = 'Hay nuevas actualizaciones disponibles. Por favor, visita la pestaña de "Actualizaciones" para más información.';
$lang['adminc_support_intro']       = "Si tienes preguntas sobre EQDKP-PLUS, asegúrate de haber visitado una de las siguientes fuentes.";
$lang['adminc_support_wiki']        = "Una WIKI es un sistema de documentación online. Hay varias respuestas, preguntas frecuentes y otras cosas útiles. Se anima a los usuarios a escribir sus propios artículos para ayudar a la comunidad.<br><a href='http://wiki.eqdkp-plus.com' target='blank'>A la Wiki</a>";
$lang['adminc_support_bugtracker']  = "¿Has encontrado un fallo? Ayúdanos buscando primero en la herramienta y abre un nuevo reporte si es necesario<br><a href='http://bugtracker.eqdkp-plus.com' target='blank'>Al Bugtracker</a>";
$lang['adminc_support_forums']      = "¿Tienes preguntas? ¿Necesitas ayuda? ¿Has buscado en la WIKI sin encontra respuesta? Visita los foros oficiales para encontrar ayuda.<br><a href='http://www.eqdkp-plus.com/forum' target='blank'>¡A los foros!</a>"
?>
