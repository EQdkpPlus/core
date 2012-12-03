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
$lang['noauth_a_event_add']    = 'Usted no tiene permisos para añadir eventos.';
$lang['noauth_a_event_upd']    = 'Usted no tiene permisos para actualizar eventos.';
$lang['noauth_a_event_del']    = 'Usted no tiene permisos para borrar eventos.';
$lang['noauth_a_groupadj_add'] = 'Usted no tiene permisos para añadir ajustes de grupo.';
$lang['noauth_a_groupadj_upd'] = 'Usted no tiene permisos para actualizar ajuste de grupo.';
$lang['noauth_a_groupadj_del'] = 'Usted no tiene permisos para borrar ajustes de grupo.';
$lang['noauth_a_indivadj_add'] = 'Usted no tiene permisos para añadir ajustes individuales.';
$lang['noauth_a_indivadj_upd'] = 'Usted no tiene permisos para actualizar ajustes individuales.';
$lang['noauth_a_indivadj_del'] = 'Usted no tiene permisos para borrar ajustes individuales.';
$lang['noauth_a_item_add']     = 'Usted no tiene permisos para añadir objeto.';
$lang['noauth_a_item_upd']     = 'Usted no tiene permisos para actualizar objeto.';
$lang['noauth_a_item_del']     = 'Usted no tiene permisos para borrar objeto.';
$lang['noauth_a_news_add']     = 'Usted no tiene permisos para añadir noticias.';
$lang['noauth_a_news_upd']     = 'Usted no tiene permisos para actualizar noticias.';
$lang['noauth_a_news_del']     = 'Usted no tiene permisos para borrar noticias.';
$lang['noauth_a_raid_add']     = 'Usted no tiene permisos para añadir raids.';
$lang['noauth_a_raid_upd']     = 'Usted no tiene permisos para actualizar raids.';
$lang['noauth_a_raid_del']     = 'Usted no tiene permisos para borrar raids.';
$lang['noauth_a_turnin_add']   = 'Usted no tiene permisos para añadir una Transferencia de Objeto.';
$lang['noauth_a_config_man']   = 'Usted no tiene permisos para administrar ajustes de configuracion EQDKP';
$lang['noauth_a_members_man']  = 'Usted no tiene permisos para administrar miembros de hermandad.';
$lang['noauth_a_plugins_man']  = 'Usted no tiene permisos para administrar plugins de EQDKP.';
$lang['noauth_a_styles_man']   = 'Usted no tiene permisos para administrar estilos de EQDKP.';
$lang['noauth_a_users_man']    = 'Usted no tiene permisos para administrar la configuracion de cuentas de usuario.';
$lang['noauth_a_logs_view']    = 'Usted no tiene permisos para ver registros de EQDKPK.';

// Submission Success Messages
$lang['admin_add_adj_success']               = "Un %1\$s ajuste de %2\$.2f se ha añadido a la base de datos.";
$lang['admin_add_admin_success']             = "Un e-mail ha sido enviado a %1\$s con su informacion administrativa.";
$lang['admin_add_event_success']             = "Un valor predeterminado de %1\$s para las raids en el evento %2\$s se ha añadido a la base de datos.";
$lang['admin_add_iadj_success']              = "Un ajuste individual de %2\$.2f %1\$s para %3\$s ha sido añadido a la base de datos.";
$lang['admin_add_item_success']              = "Una compra de objeto de %1\$s, comprado por %2\$s con el coste de %3\$.2f DKP ha sido añadido a la base de datos.";
$lang['admin_add_member_success']            = "%1\$s ha sido añadido como un miembro de su Hermandad.";
$lang['admin_add_news_success']              = 'La noticia ha sido añadida a la base de datos.';
$lang['admin_add_raid_success']              = "La raid del %1\$d/%2\$d/%3\$d en %4\$s ha sido añadida a la base de datos.";
$lang['admin_add_style_success']             = 'El nuevo estilo se ha añadido con exito.';
$lang['admin_add_turnin_success']            = "%1\$s ha sido transferido de %2\$s a %3\$s.";
$lang['admin_delete_adj_success']            = "El ajuste %1\$s de %2\$.2f ha sido borrado de la base de datos.";
$lang['admin_delete_admins_success']         = "Los administradores seleccionados han sido borrados.";
$lang['admin_delete_event_success']          = "El evento %2\$s ha sido borrado de la base de datos.";
$lang['admin_delete_iadj_success']           = "Un ajuste individual de %2\$.2f %1\$s para %3\$s ha sido borrado de la base de datos.";
$lang['admin_delete_item_success']           = "Una compra de objeto de %1\$s, comprado por %2\$s con el coste de %3\$.2f DKP ha sido borrado de la base de datos.";
$lang['admin_delete_members_success']        = "El miembro %1\$s junto a todo su historial, ha sido borrado de la base de datos.";
$lang['admin_delete_news_success']           = 'La noticia ha sido borrada de la base de datos.';
$lang['admin_delete_raid_success']           = 'La raid y los objetos asociados a ella han sido borrados de la base de datos.';
$lang['admin_delete_style_success']          = 'El estilo a sido borrado con exito.';
$lang['admin_delete_user_success']           = "La cuenta con nombre de usuario %1\$s ha sido borrada.";
$lang['admin_set_perms_success']             = "Todos los permisos administrativos han sido actualizados en los miembros seleccionados.";
$lang['admin_transfer_history_success']      = "Todo el historial de %1\$s ha sido transferido a %2\$s y %1\$s ha sido borrado de la base de datos.";
$lang['admin_update_account_success']        = "Su configuración de cuenta ha sido actualizada en la base de datos.";
$lang['admin_update_adj_success']            = "El ajuste %1\$s de %2\$.2f ha sido actualizado en la base de datos.";
$lang['admin_update_event_success']          = "El valor predeterminado de %1\$s para las raids en el evento %2\$s ha sido actualizado en la base de datos.";
$lang['admin_update_iadj_success']           = "Un ajuste individual de %2\$.2f %1\$s para %3\$s ha sido actualizado en la base de datos.";
$lang['admin_update_item_success']           = "Una compra de objeto de %1\$s, comprado por %2\$s con el coste de %3\$.2f DKP ha sido actualizado en la base de datos.";
$lang['admin_update_member_success']         = "La configuracion del miembro %1\$s ha sido actualizada.";
$lang['admin_update_news_success']           = 'La noticia ha sido actualizada en la base de datos.';
$lang['admin_update_raid_success']           = "La raid del %1\$d/%2\$d/%3\$d en %4\$s ha sido actualizada en la base de datos.";
$lang['admin_update_style_success']          = 'El estilo se ha actualizado con exito.';

$lang['admin_raid_success_hideinactive']     = 'Actualizando estado de jugador activo/inactivo ...';

// Delete Confirmation Texts
$lang['confirm_delete_adj']     = 'Esta seguro de que desea borrar este ajuste de grupo?';
$lang['confirm_delete_admins']  = 'Esta seguro de que desea borrar el administrador seleccionado?';
$lang['confirm_delete_event']   = 'Esta seguro de que desea borrar este evento?';
$lang['confirm_delete_iadj']    = 'Esta seguro de que desea borrar este ajuste individual?';
$lang['confirm_delete_item']    = 'Esta seguro de que desea borrar este objeto?';
$lang['confirm_delete_members'] = 'Esta seguro de que desea borrar los siguientes miembros?';
$lang['confirm_delete_news']    = 'Esta seguro de que desea borrar esta noticia?';
$lang['confirm_delete_raid']    = 'Esta seguro de que desea borrar esta raid?';
$lang['confirm_delete_style']   = 'Esta seguro de que desea borrar este estilo?';
$lang['confirm_delete_users']   = 'Esta seguro de que desea borrar las siguientes cuentas de usuario?';

// Log Actions
$lang['action_event_added']      = 'Evento Añadido';
$lang['action_event_deleted']    = 'Evento Borrado';
$lang['action_event_updated']    = 'Evento Actualizado';
$lang['action_groupadj_added']   = 'Ajuste de Grupo Añadido';
$lang['action_groupadj_deleted'] = 'Ajuste de Grupo Borrado';
$lang['action_groupadj_updated'] = 'Ajuste de Grupo Actualizado';
$lang['action_history_transfer'] = 'Historial de miembro transferido';
$lang['action_indivadj_added']   = 'Ajuste Individual Añadido';
$lang['action_indivadj_deleted'] = 'Ajuste Individual Borrado';
$lang['action_indivadj_updated'] = 'Ajuste Individual Actualizado';
$lang['action_item_added']       = 'Objeto Añadido';
$lang['action_item_deleted']     = 'Objeto Borrado';
$lang['action_item_updated']     = 'Objeto Actualizado';
$lang['action_member_added']     = 'Miembro Añadido';
$lang['action_member_deleted']   = 'Miembro Borrado';
$lang['action_member_updated']   = 'Miembro Actualizado';
$lang['action_news_added']       = 'Noticia Añadida';
$lang['action_news_deleted']     = 'Noticia Borrada';
$lang['action_news_updated']     = 'Noticia Actualizada';
$lang['action_raid_added']       = 'Raid Añadida';
$lang['action_raid_deleted']     = 'Raid Borrada';
$lang['action_raid_updated']     = 'Raid Actualizada';
$lang['action_turnin_added']     = 'Transferencia de Objeto Añadida';

// Before/After
$lang['adjustment_after']  = 'Ajuste Siguiente';
$lang['adjustment_before'] = 'Ajuste Anterior';
$lang['attendees_after']   = 'Asistentes Siguiente';
$lang['attendees_before']  = 'Asistentes Anterior';
$lang['buyers_after']      = 'Comprador Siguiente';
$lang['buyers_before']     = 'Comprador Anterior';
$lang['class_after']       = 'Clase Siguiente';
$lang['class_before']      = 'Clase Anterior';
$lang['earned_after']      = 'Ganado Siguiente';
$lang['earned_before']     = 'Ganado Anterior';
$lang['event_after']       = 'Evento Siguiente';
$lang['event_before']      = 'Evento Anterior';
$lang['headline_after']    = 'Encabezamiento Siguiente';
$lang['headline_before']   = 'Encabezamiento Anterior';
$lang['level_after']       = 'Nivel Siguiente';
$lang['level_before']      = 'Nivel Anterior';
$lang['members_after']     = 'Miembros Siguiente';
$lang['members_before']    = 'Miembros Anterior';
$lang['message_after']     = 'Mensaje Siguiente';
$lang['message_before']    = 'Mensaje Anterior';
$lang['name_after']        = 'Nombre Siguiente';
$lang['name_before']       = 'Nombre Anterior';
$lang['note_after']        = 'Nota Siguiente';
$lang['note_before']       = 'Nota Anterior';
$lang['race_after']        = 'Raza Siguiente';
$lang['race_before']       = 'Raza Anterior';
$lang['raid_id_after']     = 'Raid ID Siguiente';
$lang['raid_id_before']    = 'Raid ID Anterior';
$lang['reason_after']      = 'Razón Siguiente';
$lang['reason_before']     = 'Razón Anterior';
$lang['spent_after']       = 'Gastado Siguiente';
$lang['spent_before']      = 'Gastado Anterior';
$lang['value_after']       = 'Valor Siguiente';
$lang['value_before']      = 'Valor Anterior';

// Configuration
$lang['general_settings'] = 'Configuracion General';
$lang['guildtag'] = 'Nombre de Hermandad';
$lang['guildtag_note'] = 'Usada en el titulo de casi todas las paginas';
$lang['parsetags'] = 'Analizar etiqueta';
$lang['parsetags_note'] = 'Aquellos puestos en la lista estarán disponibles como opciones a la hora de analizar registros de raid.';
$lang['domain_name'] = 'Nombre Dominio';
$lang['server_port'] = 'Puerto del Servidor';
$lang['server_port_note'] = 'Su puerto del Servidor. Normalmente, 80.';
$lang['script_path'] = 'Ruta de Script';
$lang['script_path_note'] = 'Ruta donde se encuentra EQDKP-Plus, en relacion con el nombre de dominio.';
$lang['site_name'] = 'Nombre del Sitio';
$lang['site_description'] = 'Descripcion del Sitio';
$lang['point_name'] = 'Nombre de Puntos';
$lang['point_name_note'] = 'Ejem: DKP, RP, etc.';
$lang['enable_account_activation'] = 'Permitir activacion de Cuenta';
$lang['none'] = 'Ninguno';
$lang['admin'] = 'Administrador';
$lang['default_language'] = 'Lenguaje por defecto';
$lang['default_locale'] = 'Lugar por Defecto (sólo juego de caracteres; no afecta el lenguaje)';
$lang['default_game'] = 'Juego por Defecto';
$lang['default_game_warn'] = 'El cambio del juego por defecto puede anular otros cambios de esta sesión.';
$lang['default_style'] = 'Estilo por Defecto';
$lang['default_page'] = 'Pagina de Inicio por Defecto';
$lang['hide_inactive'] = 'Esconder Miembros Inactivos';
$lang['hide_inactive_note'] = 'Ocultar los miembros que no han asistido a una raid en el [periodo de inactividad] dias?';
$lang['inactive_period'] = 'Periodo de Inactividad';
$lang['inactive_period_note'] = 'Número de días que un miembro puede no asistir a una raid y todavía considerarse activo';
$lang['inactive_point_adj'] = 'Ajuste de puntos de Inactividad';
$lang['inactive_point_adj_note'] = 'Puntos de ajuste para hacer que un miembro pase a ser inactivo.';
$lang['active_point_adj'] = 'Ajuste de puntos de Activo';
$lang['active_point_adj_note'] = 'Puntos de ajuste para hacer que un miembro pase a ser activo.';
$lang['enable_gzip'] = 'Activar Compresion GZip';
$lang['show_item_stats'] = 'Mostrar estadisticas de Objeto';
$lang['show_item_stats_note'] = 'Trata de coger las estadisticas del objeto de Internet. Puede afectar la velocidad de determinadas paginas.';
$lang['default_permissions'] = 'Permisos por Defecto';
$lang['default_permissions_note'] = 'Éstos son los permisos para usuarios que no se han identificado, y son dados a nuevos usuarios cuando se registran. Las opciones en <b>Negrita</b> son permisos administrativos,
                                     se recomienda no marcar estas opciones por defecto. Las opciones en <i>Cursiva</i> son utilizadas exclusivamente por los plugin. Usted puede cambiar mas adelante los permisos de cada usuario individualmente, solo tiene que ir a "Administrar Usuarios" en el menu "Administracion General".';
$lang['plugins'] = 'Plugins';
$lang['no_plugins'] = 'La carpeta Plugin esta vacia (./plugins/).';
$lang['cookie_settings'] = 'Configuración de la Cookie';
$lang['cookie_domain'] = 'Dominio de Cookie';
$lang['cookie_name'] = 'Nombre de Cookie';
$lang['cookie_path'] = 'Ruta de Cookie';
$lang['session_length'] = 'Longitud de Sesion (segundos)';
$lang['email_settings'] = 'Configuración de E-Mail';
$lang['admin_email'] = 'E-Mail del Administrador';

// Admin Index
$lang['anonymous'] = 'Anónimo';
$lang['database_size'] = 'Tamaño de la Base de datos';
$lang['eqdkp_started'] = 'EQdkp se inició el';
$lang['ip_address'] = 'Dirección IP';
$lang['items_per_day'] = 'Objetos por Dia';
$lang['last_update'] = 'Última Actualización';
$lang['location'] = 'Ubicación';
$lang['new_version_notice'] = "Version EQdkp %1\$s esta disponible para su <a href=\"http://sourceforge.net/project/showfiles.php?group_id=69529\" target=\"_blank\">descarga</a>.";
$lang['number_of_items'] = 'Total de Objetos';
$lang['number_of_logs'] = 'Total de entradas de registro';
$lang['number_of_members'] = 'Total de Miembros (Activos/Inactivos)';
$lang['number_of_raids'] = 'Total de Raids';
$lang['raids_per_day'] = 'Raids por Dia';
$lang['statistics'] = 'Estadísticas de la Web';
$lang['totals'] = 'Totales';
$lang['version_update'] = 'Actualización de Versión';
$lang['who_online'] = 'Quien esta en linea';

// Style Management
$lang['style_settings'] = 'Configuración de Estilos';
$lang['style_name'] = 'Nombre de Estilo';
$lang['template'] = 'Plantilla';
$lang['element'] = 'Elemento';
$lang['background_color'] = 'Color de Fondo';
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
$lang['fontcolor2_note'] = 'Color utilizado fuera de las tablas (menus, titulos, copyright)';
$lang['fontcolor3'] = 'Color de Fuente 3';
$lang['fontcolor3_note'] = 'Color de fuente en campo de entrada';
$lang['fontcolor_neg'] = 'Color de Fuente Negativo';
$lang['fontcolor_neg_note'] = 'Color para números negativos';
$lang['fontcolor_pos'] = 'Color de Fuente Positivo';
$lang['fontcolor_pos_note'] = 'Color para números positivos';
$lang['body_link'] = 'Color de Vinculos';
$lang['body_link_style'] = 'Estilo de Vinculos';
$lang['body_hlink'] = 'Color de Vinculos Activos';
$lang['body_hlink_style'] = 'Estilo de Vinculos Activos';
$lang['header_link'] = 'Cabezera del Vinculo';
$lang['header_link_style'] = 'Estilo Cabezera del Vinculo';
$lang['header_hlink'] = 'Cabezera del Vinculo Activo';
$lang['header_hlink_style'] = 'Estilo Cabezera del Vinculo Activo';
$lang['tr_color1'] = 'Color de Fila de Tabla 1';
$lang['tr_color2'] = 'Color de Fila de Tabla 2';
$lang['th_color1'] = 'Color Cabezera de Tabla';
$lang['table_border_width'] = 'Ancho del Borde de la Tabla';
$lang['table_border_color'] = 'Color del Borde de la Tabla';
$lang['table_border_style'] = 'Estilo del Borde de la Tabla';
$lang['input_color'] = 'Color del Fondo de Campo de Entrada';
$lang['input_border_width'] = 'Ancho del Borde del Campo de Entrada';
$lang['input_border_color'] = 'Color del Borde del Campo de Entrada';
$lang['input_border_style'] = 'Estilo del Borde del Campo de Entrada';
$lang['style_configuration'] = 'Configuracion de Estilo';
$lang['style_date_note'] = 'Para los campos Fecha/Hora, la sistaxis usada es identica a la de PHP: función <a href="http://www.php.net/manual/en/function.date.php" target="_blank">date()</a>.';
$lang['attendees_columns'] = 'Columnas de Asistentes';
$lang['attendees_columns_note'] = 'Número de columnas a usar para asistentes viendo una raid';
$lang['date_notime_long'] = 'Fecha (largo)';
$lang['date_notime_short'] = 'Fecha (corto)';
$lang['date_time'] = 'Fecha y Hora';
$lang['logo_path'] = 'Nombre de archivo del Logo :';
$lang['logo_path_note'] = 'Elija una imagen de /templates/template/images/o inserte URL completa de una imagen en internet. Por favor insertar la URL empezando con http://  )';
$lang['logo_path_config'] = 'Seleccione un archivo de su disco duro y suba su nuevo logotipo aquí';

// Errors
$lang['error_invalid_adjustment'] = 'No fue proporcionado un ajuste válido.';
$lang['error_invalid_plugin']     = 'No fue proporcionado un plugin válido.';
$lang['error_invalid_style']      = 'No fue proporcionado un estilo válido.';

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
$lang['vlog_raid_added']       = "%1\$s añade una raid en '%2\$s'.";
$lang['vlog_raid_updated']     = "%1\$s actualiza una raid en '%2\$s'.";
$lang['vlog_raid_deleted']     = "%1\$s borra una raid en '%2\$s'.";
$lang['vlog_turnin_added']     = "%1\$s añade una transferencia del objeto '%4\$s' de %2\$s a %3\$s.";

// Location messages
$lang['adding_groupadj'] = 'Añadir Ajuste de Grupo';
$lang['adding_indivadj'] = 'Añadir Ajuste Individual';
$lang['adding_item'] = 'Añadir Objeto';
$lang['adding_news'] = 'Añadir Noticia';
$lang['adding_raid'] = 'Añadir Raid';
$lang['adding_turnin'] = 'Añadir Transferencia de Objeto';
$lang['editing_groupadj'] = 'Editar Ajuste de Grupo';
$lang['editing_indivadj'] = 'Editar Ajuste Individual';
$lang['editing_item'] = 'Editar Objeto';
$lang['editing_news'] = 'Editar Noticia';
$lang['editing_raid'] = 'Editar Raid';
$lang['listing_events'] = 'Listado de Eventos';
$lang['listing_groupadj'] = 'Listado de Ajustes de Grupo';
$lang['listing_indivadj'] = 'Listado de Ajustes Individuales';
$lang['listing_itemhist'] = 'Listado de Historial de Objetos';
$lang['listing_itemvals'] = 'Listado de Valor de Objetos';
$lang['listing_members'] = 'Listado de Miembros';
$lang['listing_raids'] = 'Listado de Raids';
$lang['managing_config'] = 'Gestión de Configuracion EQdkp';
$lang['managing_members'] = 'Gestión de Miembros de Hermandad';
$lang['managing_plugins'] = 'Gestión de Plugins';
$lang['managing_styles'] = 'Gestión de Estilos';
$lang['managing_users'] = 'Gestión de Cuentas de Usuario';
$lang['parsing_log'] = 'Analisis de un Registro';
$lang['viewing_admin_index'] = 'Viendo Indice de Admin';
$lang['viewing_event'] = 'Viendo Evento';
$lang['viewing_item'] = 'Viendo Objeto';
$lang['viewing_logs'] = 'Viendo Registros';
$lang['viewing_member'] = 'Viendo Miembros';
$lang['viewing_mysql_info'] = 'Viendo Informacion SQL';
$lang['viewing_news'] = 'Viendo Noticia';
$lang['viewing_raid'] = 'Viendo Raid';
$lang['viewing_stats'] = 'Viendo Estadisticas';
$lang['viewing_summary'] = 'Viendo Resumen';

// Help lines
$lang['b_help'] = 'Texto en Negrita: [b]texto[/b] (shift+alt+b)';
$lang['i_help'] = 'Texto en Cursiva: [i]texto[/i] (shift+alt+i)';
$lang['u_help'] = 'Texto Subrayado: [u]texto[/u] (shift+alt+u)';
$lang['q_help'] = 'Texto de Cotización: [quote]texto[/quote] (shift+alt+q)';
$lang['c_help'] = 'Texto Centrado: [center]texto[/center] (shift+alt+c)';
$lang['p_help'] = 'Insertar Imagen: [img]http://url_de_imagen[/img] (shift+alt+p)';
$lang['w_help'] = 'Insertar URL: [url]http://URL[/url] or [url=http://url]text[/url] (shift+alt+w)';
$lang['it_help'] = 'Insertar Objeto: ejem. [item]Coraza de Sentencia[/item] (shift+alt+t)';
$lang['ii_help'] = 'Insertar Icono Objeto: ejem. [itemicon]Coraza de Sentencia[/itemicon] (shift+alt+o)';

// Manage Members Menu (yes, MMM)
$lang['add_member'] = 'Añadir nuevo Miembro';
$lang['list_edit_del_member'] = 'Listar, Editar o Borrar Miembros';
$lang['edit_ranks'] = 'Editar Rangos de los Miembros';
$lang['transfer_history'] = 'Transferir Historial de Miembro';

// MySQL info
$lang['mysql'] = 'MySQL';
$lang['mysql_info'] = 'Informacion MySQL';
$lang['eqdkp_tables'] = 'Tablas EQdkp';
$lang['table_name'] = 'Nombre Tabla';
$lang['rows'] = 'Filas';
$lang['table_size'] = 'Tamaño Tabla';
$lang['index_size'] = 'Tamaño Index';
$lang['num_tables'] = "%d tablas";

//Backup
$lang['backup']            = 'Copia de Seguridad';
$lang['backup_database']   = 'Copia de Seguridad de la Base de Datos';
$lang['backup_title']      = 'Crear copia de Seguridad de la Base de Datos';
$lang['backup_type']       = 'Formato Copia de Seguridad :';
$lang['create_table']      = 'Añadir declaraciones \'CREATE TABLE\'? :';
$lang['skip_nonessential'] = 'Saltar datos no esenciales? <br/> no producirá filas de encarte para eqdkp_sessions.';
$lang['gzip_content']      = 'Comprimir en GZIP?<br />Producirá un archivo más pequeño si GZIP esta habilitado.';
$lang['backup_no_table_prefix']    = '<strong>ADVERTENCIA:</strong> Su instalación de EQdkp no tiene un prefijo de tabla para sus tablas de la base de datos. Cualquier tabla para plugins que usted puede tener no será sostenida.';

// plus
$lang['in_database']  = 'Guardado en la base de datos';

//Log Users Actions
$lang['action_user_added']     = 'Usuario Añadido';
$lang['action_user_deleted']   = 'Usuario Borrado';
$lang['action_user_updated']   = 'Usuario Actualizado';

$lang['vlog_user_added']     = "%1\$s añade el usuario %2\$s.";
$lang['vlog_user_updated']   = "%1\$s actualiza el usuario %2\$s.";
$lang['vlog_user_deleted']   = "%1\$s borra el usuario %2\$s.";

//MultiDKP
$lang['action_multidkp_added']     = "MultiDKP: Tabla-DKP Añadida";
$lang['action_multidkp_deleted']   = "MultiDKP: Tabla-DKP Borrada";
$lang['action_multidkp_updated']   = "MultiDKP: Tabla-DKP Actualizada";
$lang['action_multidkp_header']    = "MultiDKP";

$lang['vlog_multidkp_added']     = "%1\$s añade la Tabla-DKP %2\$s.";
$lang['vlog_multidkp_updated']   = "%1\$s actualiza la Tabla-DKP %2\$s.";
$lang['vlog_multidkp_deleted']   = "%1\$s borra la Tabla-DKP %2\$s.";

$lang['default_style_overwrite']   = "Sobrescribir la configuracion de estilo del usuario (todos los usuarios utilizaran el Estilo por defecto)";
$lang['class_colors']              = "Colores de Clase";

#Plugins
$lang['description'] = 'Descripcion';
$lang['manual'] = 'Manual';
$lang['homepage'] = 'Página Principal';
$lang['readme'] = 'Léame';
$lang['link'] = 'Enlace';
$lang['infos'] = 'Infos';

// Plugin Install / Uninstall
$lang['plugin_inst_success']  = 'Éxito';
$lang['plugin_inst_error']  = 'Error';
$lang['plugin_inst_message']  = "El plugin <i>%1\$s</i> a sido %2\$s con éxito.";
$lang['plugin_inst_installed'] = 'instalado';
$lang['plugin_inst_uninstalled'] = 'desinstalado';
$lang['plugin_inst_errormsg1'] = "Se detectaron errores en el proceso: %1\$s  %2\$s";
$lang['plugin_inst_errormsg2']  = "El %1\$s puede no tener %2\$s correctamente.";

$lang['background_image'] = 'Imagen de Fondo ( 1000x1000px) [opcional]';
$lang['css_file'] = 'Archivo CSS - ignorado la mayor parte del ajuste de color en este sitio. [opcional]';

$lang['plugin_inst_sql_note'] = 'Un error SQL durante la instalacion no implica necesariamente una mala instalacion del Plugin. Trate de usar el Plugin, si se producen errores, por favor reinstale el Plugin.';

// Plugin Update Warn Class
$lang['puc_perform_intro']          = 'Los siguientes Plugins necesitan actualizar la estructura de su base de datos. Por favor haga clic en el enlace "Solucionar" para realizar los cambios en la base de datos para cada plugin.<br/>Las tablas de la base de datos siguientes estan obsoletas:';
$lang['puc_pluginneedupdate']       = "<b>%1\$s</b>: (Requiere actualizaciones de base de datos del %2\$s al %3\$s)";
$lang['puc_solve_dbissues']         = 'Solucionar';
$lang['puc_unknown']                = '[desconocido]';
?>
