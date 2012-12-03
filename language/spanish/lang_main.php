<?php
/******************************
 * EQdkp
 * Copyright 2002-2003
 * Licensed under the GNU GPL.  See COPYING for full terms.
 * ------------------
 * lang_main.php
 * begin: Wed December 18 2002
 *
 * $Id: lang_main.php 4184 2009-03-11 01:29:03Z osr-corgan $
*
 ******************************/

if ( !defined('EQDKP_INC') )
{
     die('Do not access this file directly.');
}

// %1\$<type> prevents a possible error in strings caused
//      by another language re-ordering the variables
// $s is a string, $d is an integer, $f is a float

$lang['ENCODING'] = 'iso-8859-1';
$lang['XML_LANG'] = 'es';

// Linknames
$lang['rp_link_name']   = "Calendario";

// Titles
$lang['admin_title_prefix']   = "%1\$s %2\$s Admin";
$lang['listadj_title']        = 'Listado de Ajustes de Grupo';
$lang['listevents_title']     = 'Valores de Eventos';
$lang['listiadj_title']       = 'Listado de Ajuste Individual';
$lang['listitems_title']      = 'Valores de Objeto';
$lang['listnews_title']       = 'Entrada de noticias';
$lang['listmembers_title']    = 'Posiciones de Miembro';
$lang['listpurchased_title']  = 'Historial de Objeto';
$lang['listraids_title']      = 'Listado de Raids';
$lang['login_title']          = 'Incio de Sesion';
$lang['message_title']        = 'Web: Mensaje';
$lang['register_title']       = 'Registro';
$lang['settings_title']       = 'Configuracion de Cuenta';
$lang['stats_title']          = "%1\$s Estadísticas";
$lang['summary_title']        = 'Resumen de Noticias';
$lang['title_prefix']         = "%1\$s %2\$s";
$lang['viewevent_title']      = "Ver historial de raid registrada para %1\$s";
$lang['viewitem_title']       = "Ver historial de compra para %1\$s";
$lang['viewmember_title']     = "Historial para %1\$s";
$lang['viewraid_title']       = 'Resumen de Raid';

// Main Menu
$lang['menu_admin_panel'] = 'Panel de Administracion';
$lang['menu_events'] = 'Eventos';
$lang['menu_itemhist'] = 'Historial de Objetos';
$lang['menu_itemval'] = 'Valor de Objetos';
$lang['menu_news'] = 'Inicio';
$lang['menu_raids'] = 'Raids';
$lang['menu_register'] = 'Registro';
$lang['menu_settings'] = 'Configuracion';
$lang['menu_members'] = 'Personajes';
$lang['menu_standings'] = 'DKPs';
$lang['menu_stats'] = 'Estadisticas';
$lang['menu_summary'] = 'Resumen';

// Column Headers
$lang['account'] = 'Cuenta';
$lang['action'] = 'Accion';
$lang['active'] = 'Activo';
$lang['add'] = 'Añadir';
$lang['added_by'] = 'Añadido por';
$lang['adjustment'] = 'Ajuste';
$lang['administration'] = 'Administracion';
$lang['administrative_options'] = 'Opciones Administrativas';
$lang['admin_index'] = 'Indice Admin';
$lang['attendance_by_event'] = 'Asistencia por Evento';
$lang['attended'] = 'Asistió';
$lang['attendees'] = 'Asistentes';
$lang['average'] = 'Promedio';
$lang['buyer'] = 'Comprador';
$lang['buyers'] = 'Compradores';
$lang['class'] = 'Clase';
$lang['armor'] = 'Armadura';
$lang['type'] = 'Armadura';
$lang['class_distribution'] = 'Estadisticas por Clases';
$lang['class_summary'] = "Resumen de Clase: %1\$s en %2\$s";
$lang['configuration'] = 'Configuracion';
$lang['config_plus']	= 'Configuraciones PLUS';
$lang['plus_vcheck']	= 'Buscar Actualizaciónes';
$lang['current'] = 'Actual DKP';
$lang['date'] = 'Fecha';
$lang['delete'] = 'Borrar';
$lang['delete_confirmation'] = 'Borrar Confirmacion';
$lang['dkp_value'] = "%1\$s Valor";
$lang['drops'] = 'Drops';
$lang['earned'] = 'Ganado';
$lang['enter_dates'] = 'Introduzca Fechas';
$lang['eqdkp_index'] = 'Indice Web';
$lang['eqdkp_upgrade'] = 'Actualización de la Web';
$lang['event'] = 'Evento';
$lang['events'] = 'Eventos';
$lang['filter'] = 'Filtro';
$lang['first'] = 'Primero';
$lang['rank'] = 'Rango';
$lang['general_admin'] = 'Administración General';
$lang['get_new_password'] = 'Obtenga una nueva contraseña';
$lang['group_adj'] = 'Ajust. Grupo';
$lang['group_adjustments'] = 'Ajustes de Grupo';
$lang['individual_adjustments'] = 'Ajustes Individuales';
$lang['individual_adjustment_history'] = 'Historial de ajustes individuales';
$lang['indiv_adj'] = 'Ajust. Indiv.';
$lang['ip_address'] = 'Direccion IP';
$lang['item'] = 'Objeto';
$lang['items'] = 'Objetos';
$lang['item_purchase_history'] = 'Historial de compra de Objetos';
$lang['last'] = 'Último';
$lang['lastloot'] = 'Último Loot';
$lang['lastraid'] = 'Última Raid';
$lang['last_visit'] = 'Última Visita';
$lang['level'] = 'Nivel';
$lang['log_date_time'] = 'Fecha/Hora de este Log';
$lang['loot_factor'] = 'Factor de Loot';
$lang['loots'] = 'Loots';
$lang['manage'] = 'Administrar';
$lang['member'] = 'Miembro';
$lang['members'] = 'Miembros';
$lang['members_present_at'] = "Miembros presentes en %1\$s en %2\$s";
$lang['miscellaneous'] = 'Miscelaneo';
$lang['name'] = 'Nombre';
$lang['news'] = 'Noticias';
$lang['note'] = 'Nota';
$lang['online'] = 'En Linea';
$lang['options'] = 'Opciones';
$lang['paste_log'] = 'Pegue un registro debajo';
$lang['percent'] = 'Por ciento';
$lang['permissions'] = 'Permisos';
$lang['per_day'] = 'Por Dia';
$lang['per_raid'] = 'Por Raid';
$lang['pct_earned_lost_to'] = '% Ganado Perdido a';
$lang['preferences'] = 'Preferencias';
$lang['purchase_history_for'] = "Historial de compras para %1\$s";
$lang['quote'] = 'Citar';
$lang['race'] = 'Raza';
$lang['raid'] = 'Raid';
$lang['raids'] = 'Raids';
$lang['raid_id'] = 'Raid ID';
$lang['raid_attendance_history'] = 'Historial de asistencia en Raids';
$lang['raids_lifetime'] = "Mucho mas tiempo (%1\$s - %2\$s)";
$lang['raids_x_days'] = "Ultimos %1\$d dias";
$lang['rank_distribution'] = 'Distribucion de Rangos';
$lang['recorded_raid_history'] = "Historial de raids registradas para %1\$s";
$lang['reason'] = 'Razón';
$lang['registration_information'] = 'Informacion de Registro';
$lang['result'] = 'Resultado';
$lang['session_id'] = 'ID de Sesion';
$lang['settings'] = 'Configuracion';
$lang['spent'] = 'Gastado';
$lang['summary_dates'] = "Resumen Raid: %1\$s en %2\$s";
$lang['themes'] = 'Temas';
$lang['time'] = 'Tiempo';
$lang['total'] = 'Total';
$lang['total_earned'] = 'Total Ganado';
$lang['total_items'] = 'Total Objetos';
$lang['total_raids'] = 'Total Raids';
$lang['total_spent'] = 'Total Gastado';
$lang['transfer_member_history'] = 'Transfiera Historial de Miembro';
$lang['turn_ins'] = 'Transferir Objetos';
$lang['type'] = 'Tipo';
$lang['update'] = 'Actualizar';
$lang['updated_by'] = 'Actualizado por';
$lang['user'] = 'Usuario';
$lang['username'] = 'Nombre de Usuario';
$lang['value'] = 'Valor';
$lang['view'] = 'Ver';
$lang['view_action'] = 'Ver Accion';
$lang['view_logs'] = 'Ver Logs';

// Page Foot Counts
$lang['listadj_footcount']               = "... encontrado(s) %1\$d ajuste(s) / %2\$d por pagina";
$lang['listevents_footcount']            = "... encontrado(s) %1\$d evento(s) / %2\$d por pagina";
$lang['listiadj_footcount']              = "... encontrado(s) %1\$d ajuste(s) individuales / %2\$d por pagina";
$lang['listitems_footcount']             = "... encontrado(s) %1\$d objeto(s) unico(s) / %2\$d por pagina";
$lang['listmembers_active_footcount']    = "... encontrado(s) %1\$d miembro(s) activo(s) / %2\$sMostrar todos</a>";
$lang['listmembers_compare_footcount']   = "... comparar %1\$d miembros";
$lang['listmembers_footcount']           = "... encontrado(s) %1\$d miembro(s)";
$lang['listnews_footcount']              = "... encontrada(s) %1\$d entrada de noticias / %2\$d por pagina";
$lang['listpurchased_footcount']         = "... encontrado(s) %1\$d objeto(s) / %2\$d por pagina";
$lang['listraids_footcount']             = "... encontrada(s) %1\$d raid(s) / %2\$d por pagina";
$lang['stats_active_footcount']          = "... encontrado(s) %1\$d miembro(s) activo(s) / %2\$sMostrar todos</a>";
$lang['stats_footcount']                 = "... encontrado(s) %1\$d miembro(s)";
$lang['viewevent_footcount']             = "... encontrada(s) %1\$d raid(s)";
$lang['viewitem_footcount']              = "... encontrado(s) %1\$d objeto(s)";
$lang['viewmember_adjustment_footcount'] = "... encontrado(s) %1\$d ajuste(s) individual";
$lang['viewmember_item_footcount']       = "... encontrado(s) %1\$d objeto(s) comprado(s) / %2\$d por pagina";
$lang['viewmember_raid_footcount']       = "... encontrado(s) %1\$d asistencia en raid(s) / %2\$d por pagina";
$lang['viewraid_attendees_footcount']    = "... encontrado(s) %1\$d asistente(s)";
$lang['viewraid_drops_footcount']        = "... encontrado(s) %1\$d drop(s)";

// Submit Buttons
$lang['close_window'] = 'Cerrar Ventana';
$lang['compare_members'] = 'Comparar Miembros';
$lang['create_news_summary'] = 'Crear resumen de noticias';
$lang['login'] = 'Inicio de Sesion';
$lang['logout'] = 'Cerrar Sesion';
$lang['log_add_data'] = 'Añada datos al formulario';
$lang['lost_password'] = 'Rescuperar Contraseña';
$lang['no'] = 'No';
$lang['proceed'] = 'Proceder';
$lang['reset'] = 'Restablecer';
$lang['set_admin_perms'] = 'Establecer permisos administrativos';
$lang['submit'] = 'Enviar';
$lang['upgrade'] = 'Actualizar';
$lang['yes'] = 'Si';

// Form Element Descriptions
$lang['admin_login'] = 'Inicio sesion de Administrador';
$lang['confirm_password'] = 'Confirmar Contraseña';
$lang['confirm_password_note'] = 'Usted sólo tendrá que confirmar su nueva contraseña si la has cambiado anteriormente';
$lang['current_password'] = 'Contraseña Actual';
$lang['current_password_note'] = 'Deberá confirmar su contraseña actual si desea cambiar su nombre de usuario o contraseña';
$lang['email'] = 'Email';
$lang['email_address'] = 'Direccion de Email';
$lang['ending_date'] = 'Fecha final';
$lang['from'] = 'Desde';
$lang['guild_tag'] = 'Etiqueta de Hermandad';
$lang['language'] = 'Lenguaje';
$lang['new_password'] = 'Nueva Contraseña';
$lang['new_password_note'] = 'Solo es necesario poner una nueva contraseña si desea cambiarla';
$lang['password'] = 'Contraseña';
$lang['remember_password'] = 'Recuérdeme (cookie)';
$lang['starting_date'] = 'Fecha de Inicio';
$lang['style'] = 'Estilo';
$lang['to'] = 'Para';
$lang['username'] = 'Usuario';
$lang['users'] = 'Usuarios';

// Pagination
$lang['next_page'] = 'Pagina Siguiente';
$lang['page'] = 'Pagina';
$lang['previous_page'] = 'Pagina Anterior';

// Permission Messages
$lang['noauth_default_title'] = 'Permiso Denegado';
$lang['noauth_u_event_list'] = 'Usted no tiene permisos para listar los eventos.';
$lang['noauth_u_event_view'] = 'Usted no tiene permisos para ver los eventos.';
$lang['noauth_u_item_list'] = 'Usted no tiene permisos para listar los objetos.';
$lang['noauth_u_item_view'] = 'Usted no tiene permisos para ver los objetos.';
$lang['noauth_u_member_list'] = 'Usted no tiene permisos para ver las clasificaciones de los miembros.';
$lang['noauth_u_member_view'] = 'Usted no tiene permisos para ver el historial de miembros.';
$lang['noauth_u_raid_list'] = 'Usted no tiene permisos para listar las raids.';
$lang['noauth_u_raid_view'] = 'Usted no tiene permisos para ver las raids.';

// Submission Success Messages
$lang['add_itemvote_success'] = 'Su voto por el objeto a sido registrado.';
$lang['update_itemvote_success'] = 'Su voto por el objeto a sido actualizado.';
$lang['update_settings_success'] = 'La configuracion de usuario a sido actualizada.';

// Form Validation Errors
$lang['fv_alpha_attendees'] = 'Personajes\' en los nombres de EverQuest contienen sólo caracteres Alfabéticos.';
$lang['fv_already_registered_email'] = 'La direccion de email ya esta registrada.';
$lang['fv_already_registered_username'] = 'El nombre de usuario ya esta registrado.';
$lang['fv_difference_transfer'] = 'Una transferencia de historial debe hacerse entre dos personas diferentes.';
$lang['fv_difference_turnin'] = 'Un turn-in debe hacerse entre dos personas diferentes.';
$lang['fv_invalid_email'] = 'La direccion de email no parece ser valida.';
$lang['fv_match_password'] = 'Los campos de Contraseña deben coincidir.';
$lang['fv_member_associated']  = "%1\$s ya esta asociado a otra cuenta de usuario.";
$lang['fv_number'] = 'Debe ser un numero.';
$lang['fv_number_adjustment'] = 'El ajuste de valor de campo debe ser un numero.';
$lang['fv_number_alimit'] = 'El campo limite de los ajustes debe ser un numero.';
$lang['fv_number_ilimit'] = 'El campo limite de los objetos debe ser un numero.';
$lang['fv_number_inactivepd'] = 'El periodo de inactividad debe ser un numero.';
$lang['fv_number_pilimit'] = 'El limite de los objetos adquiridos debe ser un numero.';
$lang['fv_number_rlimit'] = 'El limite de Raids debe ser un numero.';
$lang['fv_number_value'] = 'El valor del campo debe ser un numero.';
$lang['fv_number_vote'] = 'La campo votacion debe ser un numero.';
$lang['fv_date'] = 'Por favor elija una fecha valida del calendario.';
$lang['fv_range_day'] = 'El campo de Día debe ser un numero entre 1 y 31.';
$lang['fv_range_hour'] = 'El campo de Hora debe ser un numero entre 0 y 23.';
$lang['fv_range_minute'] = 'El campo minutos debe ser un numero entre 0 y 59.';
$lang['fv_range_month'] = 'El campo Mes debe ser un numero entre 1 y 12.';
$lang['fv_range_second'] = 'El segundo campo debe ser un numero entre 0 y 59.';
$lang['fv_range_year'] = 'El campo Año debe ser un numero con un valor de al menos 1998.';
$lang['fv_required'] = 'Campo Obligatorio';
$lang['fv_required_acro'] = 'El campo de sigla de hermandad es obligatorio.';
$lang['fv_required_adjustment'] = 'El campo de valor de ajuste es obligatorio.';
$lang['fv_required_attendees'] = 'Debe haber al menos un participante en esta raid.';
$lang['fv_required_buyer'] = 'El comprador debe ser seleccionado.';
$lang['fv_required_buyers'] = 'Al menos un comprador debe ser seleccionado.';
$lang['fv_required_email'] = 'El campo dirección de email es obligatorio.';
$lang['fv_required_event_name'] = 'Debe seleccionar un evento.';
$lang['fv_required_guildtag'] = 'El campo eiqueta de hermandad es obligatorio.';
$lang['fv_required_headline'] = 'El campo Título es obligatorio.';
$lang['fv_required_inactivepd'] = 'Si el campo esconder miembros inactivos esta definido como SI, debe tambien poner un valor para el periodo de inactividad.';
$lang['fv_required_item_name'] = 'El campo de nombre del objeto debe ser llenado o un objeto existente debe ser seleccionado.';
$lang['fv_required_member'] = 'Un miembro debe ser seleccionado.';
$lang['fv_required_members'] = 'Al menos un miembro debe ser seleccionado.';
$lang['fv_required_message'] = 'El campo Mensaje es obligatorio.';
$lang['fv_required_name'] = 'El campo Nombre es obligatorio.';
$lang['fv_required_password'] = 'El campo Contraseña es obligatorio.';
$lang['fv_required_raidid'] = 'Una raid debe ser seleccionada.';
$lang['fv_required_user'] = 'El campo Nombre de Usuario es obligatorio.';
$lang['fv_required_value'] = 'El campo Valor es obligatorio.';
$lang['fv_required_vote'] = 'El campo Votación es obligatorio.';

// Miscellaneous
$lang['added'] = 'Añadido';
$lang['additem_raidid_note'] = "Se mostraran solo las raids de menos de dos semanas / %1\$sMostrar todo</a>";
$lang['additem_raidid_showall_note'] = 'Se muestran todas las raids';
$lang['addraid_datetime_note'] = 'Si usted analiza un registro, la fecha y hora sera encontrada automaticamente.';
$lang['addraid_value_note'] = 'Dar un Bonus por una sola vez; Si se deja en blanco se utulizara el valor preestablecido en el evento';
$lang['add_items_from_raid'] = 'Añadir los objetos de esta Raid';
$lang['deleted'] = 'Borrado';
$lang['done'] = 'Hecho';
$lang['enter_new'] = 'Introduzca uno nuevo';
$lang['error'] = 'Error';
$lang['head_admin'] = 'Admin principal';
$lang['hold_ctrl_note'] = 'Mantenga CTRL para seleccionar varios';
$lang['list'] = 'Listar';
$lang['list_groupadj'] = 'Lista de ajustes de grupo';
$lang['list_events'] = 'Lista de eventos';
$lang['list_indivadj'] = 'Lista de ajustes individuales';
$lang['list_items'] = 'Lista de objetos';
$lang['list_members'] = 'Lista de miembros';
$lang['list_news'] = 'Lista de noticias';
$lang['list_raids'] = 'Lista de raids';
$lang['may_be_negative_note'] = 'puede ser negativo';
$lang['not_available'] = 'No disponible';
$lang['no_news'] = 'Ninguna entrada de noticias encontrada.';
$lang['of_raids'] = "%1\$d%% de raids";
$lang['or'] = 'O';
$lang['powered_by'] = 'Powered by';
$lang['preview'] = 'Vista previa';
$lang['required_field_note'] = 'Los asuntos marcados con un * son campos obligatorios.';
$lang['select_1ofx_members'] = "Selecciona 1 de %1\$d miembros...";
$lang['select_existing'] = 'Seleccione existentes';
$lang['select_version'] = 'Seleccione la version de EQdkp desde la cual actualiza';
$lang['success'] = 'Éxito';
$lang['s_admin_note'] = 'Estos campos no pueden ser modificados por los usuarios.';
$lang['transfer_member_history_description'] = 'Esto transfiere el historial del miembro(s) (raids, objetos, ajustes) a otro miembro.';
$lang['updated'] = 'Actualizado';
$lang['upgrade_complete'] = 'Su instalación EQdkp se ha actualizado.<br /><br /><b class="negative">Para mayor seguridad, elimine este archivo!</b>';

// Settings
$lang['account_settings'] = 'Configurar cuenta';
$lang['adjustments_per_page'] = 'Ajustes por pagina';
$lang['basic'] = 'Básico';
$lang['events_per_page'] = 'Eventos por Pagina';
$lang['items_per_page'] = 'Objetos por Pagina';
$lang['news_per_page'] = 'Noticias por Pagina';
$lang['raids_per_page'] = 'Raids por Pagina';
$lang['associated_members'] = 'Miembros Asociados';
$lang['guild_members'] = 'Miembro(s) Hermandad';
$lang['default_locale'] = 'Lugar por defecto';


// Error messages
$lang['error_account_inactive'] = 'Tu cuenta está inactiva.';
$lang['error_already_activated'] = 'La cuenta ya ha sido activada.';
$lang['error_invalid_email'] = 'No se proporcionó una direccion de email valida.';
$lang['error_invalid_event_provided'] = 'No se proporcionó un ID de evento valido.';
$lang['error_invalid_item_provided'] = 'No se proporcionó un ID de objeto valido.';
$lang['error_invalid_key'] = 'Usted a proporcionado una clave de activacion invalida.';
$lang['error_invalid_name_provided'] = 'No se proporcionó un nombre de usuario valido.';
$lang['error_invalid_news_provided'] = 'No se proporcionó un ID de noticia valido.';
$lang['error_invalid_raid_provided'] = 'No se proporcionó un ID de raid valido.';
$lang['error_user_not_found'] = 'No se proporcionó un nombre de usuario valido';
$lang['incorrect_password'] = 'Contraseña Incorrecta';
$lang['invalid_login'] = 'Usted a proporcionado un nombre de usuario o contraseña incorrecto';
$lang['not_admin'] = 'Usted no es un administrador';

// Registration
$lang['account_activated_admin']   = 'La cuenta ha sido activada. Un e-mail ha sido enviado al usuario para informarle de este cambio.';
$lang['account_activated_user']    = "Su cuenta ha sido activada y ahora puede %1\$siniciar sesion en%2\$s.";
$lang['password_sent'] = 'Su nueva contraseña le ha sido enviada por email.';
$lang['register_activation_self']  = "Su cuenta ha sido creada, pero antes de poder usarla es necesario la activacion.<br /><br />Un email ha sido enviado a %1\$s con la informacion para activar su cuenta.";
$lang['register_activation_admin'] = "Su cuenta a sido creada, pero antes de poder usarla debe ser activada por el administrador.<br /><br />Un email ha sido enviado a %1\$s con mas informacion.";
$lang['register_activation_none']  = "Su cuenta ha sido creada y ahora puede %1\$siniciar sesion en%2\$s.<br /><br />Un email a sido enviado a %3\$s con mas informacion.";

//plus
$lang['news_submitter'] = 'escrito por';
$lang['news_submitat'] = 'a las';
$lang['droprate_loottable'] = "Tabla de loot";
$lang['droprate_name'] = "Nombre objeto";
$lang['droprate_count'] = "Cuenta";
$lang['droprate_drop'] = "Drop %";

$lang['Itemsearch_link'] = "Buscar Objeto";
$lang['Itemsearch_search'] = "Buscar objeto :";
$lang['Itemsearch_searchby'] = "Buscar por :";
$lang['Itemsearch_item'] = "Objeto ";
$lang['Itemsearch_buyer'] = "Comprador ";
$lang['Itemsearch_raid'] = "Raid ";
$lang['Itemsearch_unique'] = "Resultados de tema unico :";
$lang['Itemsearch_no'] = "No";
$lang['Itemsearch_yes'] = "Si";

$lang['bosscount_player'] = "Jugador: ";
$lang['bosscount_raids'] = "Raids: ";
$lang['bosscount_items'] = "Objetos: ";
$lang['bosscount_dkptotal'] = "Total DKP: ";

//MultiDKP
$lang['Plus_menuentry'] 			= "EQdkp Plus";
$lang['Multi_entryheader'] 		= "MultiDKP - Añadir Tabla-DKP";
$lang['Multi_pageheader'] 		= "MultiDKP - Mostrar Tablas-DKP";
$lang['Multi_events'] 				= "Eventos:";
$lang['Multi_eventname'] 				= "Nombre evento";
$lang['Multi_discnottolong'] 	= "(Nombre de la tabla-DKP) - esto no deberia ser demasiado largo, la tabla-DKP se hará grande. Elija p.e NAX, BWL, AQ etc. !";
$lang['Multi_kontoname_short']= "Tabla-DKP:";
$lang['Multi_discr'] 					= "Descripcion:";
$lang['Multi_events'] 				= "Eventos de esta tabla-DKP";

$lang['Multi_addkonto'] 			  = "Añadir Tabla-DKP";
$lang['Multi_updatekonto'] 			= "Cambiar Tabla-DKP";
$lang['Multi_deletekonto'] 			= "Borrar Tabla-DKP";
$lang['Multi_viewkonten']			  = "Mostrar Tablas-DKP";
$lang['Multi_chooseevents']			= "Elija Evento(s)";
$lang['multi_footcount'] 				= "...encontrada(s) %1\$d Tablas-DKP  / %2\$d por pagina";
$lang['multi_error_invalid']    = "Tablas-DKP no asignadas....";
$lang['Multi_required_event']   = "Debe elegir al menos un evento!";
$lang['Multi_required_name']    = "Debe insertar un nombre!";
$lang['Multi_required_disc']    = "Debe insertar una descripcion!";
$lang['Multi_admin_add_multi_success'] = "La Tabla-DKP %1\$s ( %2\$s ) con los eventos %3\$s se ha añadido a la base de datos.";
$lang['Multi_admin_update_multi_success'] = "La Tabla-DKP %1\$s ( %2\$s ) con los eventos %3\$s se ha cambiado en la base de datos.";
$lang['Multi_admin_delete_success']           = "La Tabla-DKP %1\$s se ha eliminado de la base de datos.";
$lang['Multi_confirm_delete']    = 'Seguro que desea eliminar la Tabla-DKP?';

$lang['Multi_total_cost']   										= 'Total dkps para esta Tabla-DKP';
$lang['Multi_Accs']    													= 'Tabla(s)-DKP';

//update
$lang['upd_eqdkp_status']    										= 'Estado de actualización de EQdkp';
$lang['upd_system_status']    									= 'Estado de sistema';
$lang['upd_template_status']    								= 'Estado de plantilla';
$lang['upd_gamefile_status']                    = 'Estado de juego';
$lang['upd_update_need']    										= 'Necesita actualizar!';
$lang['upd_update_need_link']    								= 'Instalar todos los componentes necesarios';
$lang['upd_no_update']    											= 'Ninguna actualizacion necesaria. El sistema ya esta actualizado.';
$lang['upd_status']    													= 'Estado';
$lang['upd_state_error']    										= 'Error';
$lang['upd_sql_string']    											= 'Comando SQL';
$lang['upd_sql_status_done']    								= 'conseguido';
$lang['upd_sql_error']    											= 'Error';
$lang['upd_sql_footer']    											= 'Ejecutado comando SQL';
$lang['upd_sql_file_error']    									= 'Error: El archivo SQL requerido %1\$s no ha podido ser encontrado!';
$lang['upd_eqdkp_system_title']    							= 'Sistema de actualización de componentes EQdkp';
$lang['upd_plus_version']    										= 'Version EQdkp Plus';
$lang['upd_plus_feature']    										= 'Caracteristica';
$lang['upd_plus_detail']    										= 'Detalles';
$lang['upd_update']    													= 'Actualizar';
$lang['upd_eqdkp_template_title']    						= 'Sistema de actualización de Plantillas EQdkp';
$lang['upd_eqdkp_gamefile_title']               = 'Sistema de actualización de juegos EQdkp';
$lang['upd_gamefile_availversion']              = 'Versión disponible';
$lang['upd_gamefile_instversion']               = 'Versión Instalada';
$lang['upd_template_name']    									= 'Nombre de Plantilla';
$lang['upd_template_state']    									= 'Estado de Plantilla';
$lang['upd_template_filestate']    							= 'Carpeta de plantillas disponibles';
$lang['upd_link_install']    										= 'Actualizar';
$lang['upd_link_reinstall']    									= 're-instalar';
$lang['upd_admin_need_update']    							= 'Ha sido detectado un error en la base de datos. El sistema no está al dia y necesita ser actualizado.';
$lang['upd_admin_link_update']									= 'Click aqui para resolver el problema.';
$lang['upd_backto']    													= 'Volver a la vista general';

// Event Icon
$lang['event_icon_header']    								  = 'Selecciona un icono para el evento';

//update Itemstats
$lang['updi_header']    								    	= 'Actualizar datos Itemstats';
$lang['updi_header2']    								    	= 'Informacion Itemstats';
$lang['updi_action']    								    	= 'Acción';
$lang['updi_notfound']    								    = 'No encontrado';
$lang['updi_writeable_ok']    							  = 'El archivo es escribible';
$lang['updi_writeable_no']    								= 'El archivo no es escribible';
$lang['updi_help']    								    		= 'Descripcion';
$lang['updi_footcount']    								    = 'Objeto refrescado';
$lang['updi_curl_bad']    								    = 'La funcion PHP necesaria (cURL) no se ha encontrado. Es posible que Itemstats no funcione bien. Por favor, pongase en contacto con su administrador.';
$lang['updi_curl_ok']    								    	= 'cURL encontrado.';
$lang['updi_fopen_bad']    								    = 'La funcion PHP necesaria (fopen) no se ha encontrado. Es posible que Itemstats no funcione bien. Por favor, pongase en contacto con su administrador.';
$lang['updi_fopen_ok']    								    = 'fopen encontrado.';
$lang['updi_nothing_found']						    		= 'Ningunos objetos encontrados';
$lang['updi_itemscount']  						    		= 'Entradas Itemcache:';
$lang['updi_baditemscount']						    		= 'Malas Entradas:';
$lang['updi_items']										    		= 'Objetos en base de datos:';
$lang['updi_items_duplicate']					    		= '{Con objetos duplicados}';
$lang['updi_show_all']    								    = 'Listar todos los objetos con Itemstats';
$lang['updi_refresh_all']    								  = 'Eliminar todos los objetos y refrésquelos.';
$lang['updi_refresh_bad']    								  = 'Refrescar solo objetos malos';
$lang['updi_refresh_raidbank']    						= 'Refrescar objetos de Raidbanker';
$lang['updi_refresh_tradeskill']   						= 'Refrescar objetos Tradeskill';
$lang['updi_help_show_all']    								= 'Todos los objetos se muestran con sus estadisticas. Refrescar las malas estadisticas. (recomendado)';
$lang['updi_help_refresh_all']  							= 'Elimina el Itemcache actual y trata de actualizar todos los objetos que se muestran en EQDKP. ADVERTENCIA: Si usted comparte su Itemcache con un foro, los objetos del foro no pueden ser refrescados. Segun la velocidad de su servidor web y la disponibilidad de Allakhazam.com esta accion podria tardar varios minutos. Posiblemente los ajustes de su servidor web prohiben una buena ejecucion. En este caso pongase en contacto con su administrador.';
$lang['updi_help_refresh_bad']    						= 'Borra todos los Objetos malos de la cache y los refresca.';
$lang['updi_help_refresh_raidbank']    				= 'Si Raidbanker esta instalado, Itemstats utiliza los obejtos entrados en el banco.';
$lang['updi_help_refresh_Tradeskill']    			= 'Cuando Tradeskill esta instalado, los objetos entrados seran actualizados por Itemstats.';

$lang['updi_active'] 					   							= 'activado';
$lang['updi_inactive']    										= 'desactivado';

$lang['fontcolor']    			  = 'Color fuente';
$lang['Warrior']    					= 'Guerrero';
$lang['Rogue']    						= 'Picaro';
$lang['Hunter']    						= 'Cazador';
$lang['Paladin']    					= 'Paladin';
$lang['Priest']    						= 'Sacerdote';
$lang['Druid']    						= 'Druida';
$lang['Shaman']    						= 'Chaman';
$lang['Warlock']    					= 'Brujo';
$lang['Mage']    							= 'Mago';

# Reset DB Feature
$lang['reset_header']    			= 'Resetear datos de la web';
$lang['reset_infotext']  			= 'Peligro!!! Si borra los datos no podran recuperarse!!! Realice una copia de seguridad primero. Para confirmar la accion, escribe DELETE en el editbox.';
$lang['reset_type']    				= 'Tipo de datos';
$lang['reset_disc']    				= 'Descripcion';
$lang['reset_sec']    				= 'Certificado';
$lang['reset_action']    			= 'Accion';

$lang['reset_news']					  = 'Noticias';
$lang['reset_news_disc']		  = 'Eliminar todas las noticias de la base de datos.';
$lang['reset_dkp'] 					  = 'DKP';
$lang['reset_dkp_disc']			  = 'Eliminar todas las raids y objetos de la base de datos y reiniciar todos los puntos DKP a cero.';
$lang['reset_ALL']   					= 'Todos';
$lang['reset_ALL_DISC']				= 'Eliminar todas las raids, objetos y miembros. Reiniciar datos por completo. (No borra a los usuarios).';

$lang['reset_confirm_text']	  = 'aqui (DELETE) =>';
$lang['reset_confirm']			  = 'Escribir';

// Armory Menu
$lang['lm_armorylink1']				= 'Armeria';
$lang['lm_armorylink2']				= 'Talentos';
$lang['lm_armorylink3']				= 'Hermandad';

$lang['updi_update_ready']			= 'Los objetos se han actualizado. Puede <a href="#" onclick="javascript:parent.closeWindow()" >cerrar</a> esta ventana.';
$lang['updi_update_alternative']= 'Metodo de actualizacion alternativo para evitar intervalos de espera.';
$lang['zero_sum']				= ' Encender Zero SUM DKP';

//Hybrid
$lang['Hybrid']				= 'Híbrido';

$lang['Jump_to'] 				= 'ver el video en ';
$lang['News_vid_help'] 			= 'Para incrustar videos solo ponga el enlace al video sin [etiquetas]. Soporta estos sitios de Video: google video, youtube, myvideo, clipfish, sevenload, metacafe and streetfire. ';

$lang['SubmitNews'] 		   = 'Enviar Noticias';
$lang['SubmitNews_help'] 	   = 'Usted tiene una buena noticia? Presente la noticia y compartala con todos los Usuarios de EQDKP Plus.';

$lang['MM_User_Confirm']	   = 'Seleccione su cuenta de admin? Si usted sume permisos de admin, esto solo se podra restarurar en la base de datos';

$lang['beta_warning']	   	   = 'Advertencia, esta version beta de eqdkp-plus no debe utilizarse en un sistema online! Esta version dejara de funciona si una version estable esta disponible. Compruebe <a href="http://www.eqdkp-plus.com" >www.eqdkp-plus.com</a> para actualizaciones!';

$lang['news_comment']        = 'Comentario';
$lang['news_comments']       = 'Comentarios';
$lang['comments_no_comments']	   = 'No hay entradas';
$lang['comments_comments_raid']	   = 'Comentarios';
$lang['comments_write_comment']	   = 'escribe un comentario';
$lang['comments_send_comment']	   = 'guardar comentario';
$lang['comments_save_wait']	   	   = 'Por favor espere, guardando comentario...';

$lang['news_nocomments'] 	 		    = 'Desactivar Comentarios';
$lang['news_readmore_button']  			  	= 'Extender Noticia';
$lang['news_readmore_button_help']  			  	= 'Se usa para extender la noticia con un boton LEER MAS, click aqui:';
$lang['news_message'] 				  	= 'Texto de la Noticia';
$lang['news_permissions']			  	= 'Permisos';

$lang['news_permissions_text']			= 'La noticia no se muestra para';
$lang['news_permissions_guest']			= 'Invitado';
$lang['news_permissions_member']		= 'Invitado y Miembros (solo Admins pueden ver)';
$lang['news_permissions_all']			= 'Libre para todos';
$lang['news_readmore'] 				  	= 'Leer mas...';

$lang['recruitment_open']				= 'Reclutamiento Abierto';
$lang['recruitment_contact']			= 'contactar';

$lang['sig_conf']						= 'Haga Click en la imagen para obtener el codigo BB';
$lang['sig_show']						= 'mostrar firma WoW de su foro';

//Shirtshop
$lang['service']					    = 'Firmas';
$lang['shirt_ad1']					    = 'Vaya al taller de firmas. <br> Obtenga su Firma ahora!';
$lang['shirt_ad2']					    = 'Elija su caracter';
$lang['shirt_ad3']					    = 'Bienvenido a su taller de hermandad ';
$lang['shirt_ad4']					    = 'Elija una de los productos o haga su propia Firma con nuestro creador.<br>
										   Puede personalizar cada Firma y cada cambio de letra.<br>
										   En la pestaña "Motivos" encontrará todos los motivos!';
$lang['error_iframe']					= "Tu navegador no soporta frames!";
$lang['new_window']						= 'Abrir taller en nueva ventana';
$lang['your_name']						= 'TU NOMBRE';
$lang['your_guild']						= 'TU HERMANDAD';
$lang['your_server']					= 'TU SERVIDOR';

//Last Raids
$lang['last_raids']					    = 'Ultimas Raids';

$lang['voice_error']				    = 'No hay conexion con el servidor.';

$lang['login_bridge_notice']		    = 'Incio de sesion - CMS-Puente esta activo. Use su datos CMS/Board para entrar al sistema.';

$lang['ads_remove']		    			= 'soporte EQdkp-Plus';
$lang['ads_header']	    				= 'Soporte EQdkp-Plus';
$lang['ads_text']		    			= 'EQDKP-Plus es un proyecto/afición que fue principalmente desarrollado y es actualizado por dos personas privadas.
											Al principio esto no era un problema, pero después de tres años de programación constante y actualización,
											los gastos para esto se ponen lamentablemente demasiado rápidos para manejarlos nosotros. Sólo para el revelador y el servidor de la actualización tenemos
												que gastar 600€ por año ahora y también hay otros 1000€ de gastos para un abogado, ya que se han producido
											algunos problemas juridicos no hace mucho. Para el futuro también hemos planeado muchos mas caracteristicas basadas en el servidor
											resultado necesidad de otro servidor. Añadir los gastos para nuestro nuevo foro y el diseñador de nuestra nueva página de inicio.
											Todos estos gastos citados más nuestro tiempo de trabajo cada vez más invertido no pueden ser pagados por nosotros más.
											Por esta razón y no queriendo dejar el proyecto usted verá de repuesto ahora carteles de anuncios en EQDKP-Plus.
											Estos banners son muy limitados para el contenido, entonces usted no verá ningún cartel pornográfico o vendedores oro/objetos/leveling.

											Usted realmente tiene opciones para apagar estos banners:
										  <ol>
										  <li> Inicie sesion en <a href="http://www.eqdkp-plus.com">www.eqdkp-plus.com</a> y Donar la cantidad que desee.
										  		Por favor piense en cuanto vale EQDKP-Plus para usted.
										  		Después de una donación (Amazon o Paypal) usted recivira un email con un serial-key para su
										  		respectiva version o una version superior (e.g. 0.6 or 0.7).<br><br></li>
										  <li> Inicie sesion en <a href="http://www.eqdkp-plus.com">www.eqdkp-plus.com</a> y donando cualquier cantidad superior a 50€.
										  		Usted ganara un estado superior y conseguira una cuenta Premium, por lo que puede optar a
										  		libres actualizaciones a nuevas version. </li><br><br>
										  <li> Inicie sesion en <a href="http://www.eqdkp-plus.com">www.eqdkp-plus.com</a> y donando cualquier cantidad superior 100€.
										  		Usted ganará el estado de oro y conseguirá una cuenta premium,
										  		por lo que optara gratis a nuevas actualizaciones y nuevas versiones + soporte personal
										  		de los desarrolladores de EQDKP-Plus.<br><br></li>
										  <li> Todos los desarroladores y los traductores que alguna vez contribuyeron a EQDKP-Plus también consiguen un serial key libre.<br><br></li>
										  <li> Los probadores de la beta comprometidos también consiguen un serial key libre. <br><br></li>
										  </ol>
										 Todo el dinero generado con banners de anuncios y donaciones es únicamente gastado para pagar los gastos que ocasionan el proyecto EQDKP-Plus.
										 EQDKP-Plus todavía es un proyecto no lucrativo!  No tienes una cuenta de Paypal o Amazon o tienes problemas con la key? Escribeme un <a href=mailto:corgan@eqdkp-plus.com>Email</a>.
										  ';


$lang['talents'] = array(
'Paladin'   	=> array('Sagrado','Proteccion','Retri'),
'Picaro'     	=> array('Asesinato','Combate','Sutileza'),
'Guerrero'   	=> array('Armas','Furia','Proteccion'),
'Cazador'    	=> array('Bestias','Punteria','Supervivencia'),
'Sacerdote'    	=> array('Disciplina','Sagrado','Sombra'),
'Brujo'  		=> array('Affliccion','Demonologia','Destruccion'),
'Druida'     	=> array('Equilibrio','Combate Feral','Restauracion'),
'Mago'      	=> array('Arcano','Fuego','Escarcha'),
'Chaman'    	=> array('Elemental','Mejora','Restauracion'),
'Caballero de la Muerte'   => array('Sangre','Escarcha','Profano')
);

$lang['portalmanager'] = 'Gestionar modulos del Portal';

$lang['air_img_resize_warning'] = 'Haga clic en esta barra para ver la imagen completa. La original es %1$sx%2$s.';

$lang['guild_shop'] = 'Tienda';

// LibLoader Language String
$lang['libloader_notfound'] = 'La biblioteca del Cargador de Clase no está disponible. Por favor compruebe si la carpeta "eqdkp/libraries/" esta correctamente subida!<br/> Descarga: <a href="https://sourceforge.net/project/showfiles.php?group_id=167016&package_id=301378">Descargar Librerias</a>';
$lang['libloader_tooold']   = "La Libreria '%1\$s' esta obsoleta. Usted tiene que subir la version %2\$s o superior.<br/> Descarga: <a href='%3\$s' target='blank'>Descargar Librerias</a><br/>Por favor descargar, y sobreescribir la existente carpeta 'eqdkp/libraries/' con la que ha descargado!";
$lang['libloader_tooold_plug']  = "El modulo de la libreria '%1\$s' esta obsoleto. Se requiere un version %2\$s o superior.
                                  Estas se incluyen en las librerias %4\$s o superior. Su version de las librerias es %5\$s<br/>
                                  Descargar: <a href='%3\$s' target='blank'>Librerias</a><br/>
                                  Por favor, descarguelas , y sobrescriba las existentes en la carpeta 'eqdkp/libraries/' por las que acaba de descagar!";

$lang['more_plugins']   = "Para obtener mas Plugins visita <a href=http://www.eqdkp-plus.com/download.php>www.eqdkp-plus.com</a>.";
$lang['more_moduls']   = "Para obtener mas modulos visita <a href=http://www.eqdkp-plus.com/download.php>www.eqdkp-plus.com</a>.";
$lang['more_template']   = "Para obtener mas plantillas visita <a href=http://www.eqdkp-plus.com/download.php>www.eqdkp-plus.com</a>";

// jQuery
$lang['cl_bttn_ok']      = 'Aceptar';
$lang['cl_bttn_cancel']  = 'Cancelar';

// Update Available
$lang['upd_available_head']    = 'Actualizacion de sistema disponible';
$lang['upd_available_txt']     = '¡ Advertencia ! El Sistema no esta actualizado. Hay actualizaciones disponibles.';
$lang['upd_available_link']    = 'Click para mostrar actualizaciones.';

$lang['menu_roster'] = 'Lista por Clases';


$lang['adduser_first_name'] = 'Nombre';
$lang['adduser_last_name'] = 'Apellido';
$lang['adduser_addinfos'] = 'Información de perfil';
$lang['adduser_country'] = 'País';
$lang['adduser_town'] = 'Ciudad';
$lang['adduser_state'] = 'Provincia';
$lang['adduser_ZIP_code'] = 'Codigo Postal';
$lang['adduser_phone'] = 'Telefono';
$lang['adduser_cellphone'] = 'Telefono Movil';
$lang['adduser_foneinfo'] = 'Los números de teléfono serán guardados anónimamente y sólo los admins son capaces de verlos. Con el número del teléfono movil dado usted puede enviar el uno al otro mensajes de texto anónimamente, p.ej en caso de nuevos acontecimientos de raids o raids canceladas.';
$lang['adduser_address'] = 'Calle';
$lang['adduser_allvatar_nick'] = 'Nick de Allvatar';
$lang['adduser_icq'] = 'ICQ';
$lang['adduser_skype'] = 'Skype';
$lang['adduser_msn'] = 'MSN';
$lang['adduser_irq'] = 'IRC Servidor y Canal';
$lang['adduser_gender'] = 'Género';
$lang['adduser_birthday'] = 'Cumpleaños';
$lang['adduser_gender_m'] = 'Hombre';
$lang['adduser_gender_f'] = 'Mujer';
$lang['fv_required'] = 'Campo Requerido!';
$lang['lib_cache_notwriteable'] = 'La carpeta "eqdkp/data" no es escribible. Por favor ponga permisos chmod 777!';
$lang['pcache_safemode_error']  = 'Modo a prueba de errores activo. EQDKP-PLUS no funciona asi, no puede escribir en la carpeta cache en modo a prueba de errores.';

// Ajax Image Uploader
$lang['aiupload_wrong_format']  = "Las dimensiones de la imagen esta fuera de límites (valores maximos: %1\$spx x %2\$spx).<br/>Por favor cambie el tamaño a la imagen.";
$lang['aiupload_wrong_type']    = 'Tipo de archivo inválido! Solo archivos de imagen (*.jpg, *.gif, *.png) estan permitidas.';
$lang['aiupload_upload_again']  = 'Erneut hochladen';

//Sticky news
$lang['sticky_news_prefix'] = '';
$lang['news_sticky'] = 'Hacerlo Fijo?';

$lang['menu_eqdkp'] = 'Menú';
$lang['menu_user'] = 'Menú de Usuario';

//Usersettings
$lang['user_list'] = 'Userlist';
$lang['user_priv'] = 'Privacy settings';
$lang['user_priv_set_global'] = 'Who should be allowed to see profile data like name, Skype-Account, ICQ… ?';
$lang['user_priv_set'] = 'Visible for ';
$lang['user_priv_all'] = 'all';
$lang['user_priv_user'] = 'Registered users';
$lang['user_priv_admin'] = 'Admin only';
$lang['user_priv_rl'] = 'Raidplaner admins';
$lang['user_priv_no'] = 'Nobody';
$lang['user_priv_tel_all'] = 'Should phone numbers be visible to all registered users instead of being visible only for admins? ';
$lang['user_priv_tel_cript'] = 'Should phone numbers be completely invisible, even for admins? (SMS/Text message sending still possible) ';
$lang['user_priv_tel_sms'] = 'Disable receiving SMS/Text messages from admins. (Receiving of raid-invitations via SMS/Text message not possible)';

// Image & BBCode Handling
$lang['images_not_available']	= 'This image is not longer available';
$lang['images_userposted']		= 'User Posted Image';


//SMS
$lang['sms_header'] = 'Send Message';
$lang['sms_header'] = 'Send text message/SMS';
$lang['sms_info'] = 'Send text message/SMS to users, e.g. when a raid was cancelled or you need extra players on short notice.';
$lang['sms_info_account'] = "You don't have a text message/SMS account, yet? Then get your text message/contingent now.";
$lang['sms_info_account_link'] = '<a href=http://www.eqdkp-plus.com target=_blank> --> Link</a>';
$lang['sms_send_info'] = 'In order to be able to send text messages/SMS, at least one user with a valid cell phone number has to be selected and a text has to be entered.';
$lang['sms_success'] = 'Text message successfully forwarded to SMS-Server. It may take a few minutes till messages will be sent.';
$lang['sms_error'] = 'An error occurred while sending text message.';
$lang['sms_error_badpw'] = 'An error occurred while sending text message. Username oder password incorrect.';
$lang['sms_error_bad'] = 'An error occurred while sending text message. No more text message credit on your account.';
$lang['sms_error_fopen'] = "An error occurred while sending text message. Server couldn't establish a fopen connection to the sms-relay. Either the sms-server is not available at this moment or your server doesn't accept fopen-connections. In such a case please contact your hoster/administrator. (don't contact the EQdkpPlus Team/Forum!!)";
$lang['sms_error_159'] = 'An error occurred while sending text message. Service-ID unknown.';
$lang['sms_error_160'] = 'An error occurred while sending text message. Message not found!';
$lang['sms_error_200'] = 'An error occurred while sending text message. Fatal eception error. / XML Script incomplete';
$lang['sms_error_254'] = 'An error occurred while sending text message. Message was deleted!';

// Libraries
$lang = array_merge($lang, array(
    'cl_shortlangtag'           => 'es',

  // Update Check
  'cl_update_box'             => 'New Version available',
  'cl_changelog_url'          => 'Changelog',
  'cl_timeformat'             => 'd/m/Y',
  'cl_noserver'               => 'Se ha producido un error al intentar ponerse en contacto con el servidor de actualización, ya sea que su servidor no permite conexiones salientes
                                  o el error fue causado por un problema de red..
                                  Por favor visite el foro de plugins en la web de EQdkp plus para asegurarse de que está ejecutando la última versión de plugin.',
  'cl_update_available'       => "Por favor, actualice el Plugin <i>%1\$s</i> .
                                  Su versión actual es <b>%2\$s</b> y la ultima versión es <b>%3\$s (Publicado en: %4\$s)</b>.<br/><br/>
                                  [fecha: %5\$s]%6\$s%7\$s",
  'cl_update_url'             => 'A la página de descarga',

  // Plugin Updater
  'cl_update_box'             => 'Actualización de la base de datos necesaria',
  'cl_upd_wversion'           => "La actual base de datos ( Versión %1\$s ) no se ajusta a la version instalada del plugin %2\$s.
                                  Por favor, utilice el botón de 'Actualizar base de datos' para realizar las actualizaciones automáticamente.",
  'cl_upd_woversion'          => 'Una instalación anterior fue encontrada. Los Datos de versión fallan.
                                  Por favor, elija la anterior versión instalada de la lista desplegable, para realizar todos los cambios en la base de datos.',
  'cl_upd_bttn'               => 'Actualizar base de datos',
  'cl_upd_no_file'            => 'El archivo de actualización falla',
  'cl_upd_glob_error'         => 'Se ha producido un error durante el proceso de actualización.',
  'cl_upd_ok'                 => 'La actualización de la base de datos se ha realizado correctamente.',
  'cl_upd_step'               => 'Paso',
  'cl_upd_step_ok'            => 'Conseguido',
  'cl_upd_step_false'         => 'Fallado',
  'cl_upd_reload_txt'         => 'Recargando ajustes, por favor espere...',
  'cl_upd_pls_choose'         => 'Por favor, elija...',
  'cl_upd_prev_version'       => 'Versión anterior',

  // HTML Class
  'cl_on'                     => 'On',
  'cl_off'                    => 'Off',

  // ReCaptcha Library
	'lib_captcha_head'					=> 'confirmation Code',
	'lib_captcha_insertword'		=> 'Enter the words written below',
	'lib_captcha_insertnumbers' => 'Enter the spoken Numbers',
	'lib_captcha_send'					=> 'Send confirmation Code',
));

#$lang['']    								  = '';
?>