<?php
/******************************
 * EQdkp
 * Copyright 2002-2003
 * Licensed under the GNU GPL.  See COPYING for full terms.
 * ------------------
 * lang_main.php
 * begin: Wed December 18 2002
 *
 * $Id: lang_main.php 2016 2008-05-12 16:00:22Z osr-corgan $
*
 ******************************/

if ( !defined('EQDKP_INC') )
{
     die('Íåò äîñòóïà ê äàííîé ôàéëîâîé äèðåêòîðèè');
}

// %1\$<type> prevents a possible error in strings caused
//      by another language re-ordering the variables
// $s is a string, $d is an integer, $f is a float

$lang['XML_LANG'] = 'ru';
$lang['ISO_LANG_SHORT'] = 'ru_RU';
$lang['ISO_LANG_NAME'] = 'русский';

// Linknames
$lang['rp_link_name']   = "Raidplanner";

// Titles
$lang['admin_title_prefix']   = "%1\$s %2\$s Àäìèíèñòðèðîâàíèå";
$lang['listadj_title']        = 'Ñïèñîê ãðóïïîâûõ èçìåíåíèé';
$lang['listevents_title']     = 'Ñòîèìîñòü ñîáûòèé';
$lang['listiadj_title']       = 'Ñïèñîê èíäèâèäóàëüíûõ èçìåíåíèé';
$lang['listitems_title']      = 'Ñòîèìîñòü ïðåäìåòîâ';
$lang['listnews_title']       = 'Íîâîñòè';
$lang['listmembers_title']    = 'Ïðîñìîòð èíôîðìàöèè î DKP ó÷àñòíèêîâ';
$lang['listpurchased_title']  = 'Èñòîðèÿ ïðåäìåòîâ';
$lang['listraids_title']      = 'Ñïèñîê Ðåéäîâ';
$lang['login_title']          = 'Âõîä';
$lang['message_title']        = 'EQdkp: ñîîáùåíèå';
$lang['register_title']       = 'Ðåãèñòðàöèÿ';
$lang['settings_title']       = 'Íàñòðîéêè Ó÷åòíîé çàïèñè';
$lang['stats_title']          = "%1\$s Ñòàòèñòèêà";
$lang['summary_title']        = 'Îò÷åò ïî íîâîñòÿì';
$lang['title_prefix']         = "%1\$s %2\$s";
$lang['viewevent_title']      = "Ïðîñìîòð èñòîðèè Ðåéä(îâ) %1\$s";
$lang['viewitem_title']       = "Ïðîñìîòð èñòîðèè ïîêóïîê %1\$s";
$lang['viewmember_title']     = "Ïðîñìîòð èíôîðìàöèè îá ó÷àñòíèêå %1\$s";
$lang['viewraid_title']       = 'Îò÷åò ïî Ðåéäó';

// Main Menu
$lang['menu_admin_panel'] = 'Ïàíåëü àäìèíèñòðàòîðà';
$lang['menu_events'] = 'Ñîáûòèÿ';
$lang['menu_itemhist'] = 'Èñòîðèÿ ïðåäìåòîâ';
$lang['menu_itemval'] = 'Ñòîèìîñòü ïðåäìåòîâ';
$lang['menu_news'] = 'Íîâîñòè';
$lang['menu_raids'] = 'Ðåéäû';
$lang['menu_register'] = 'Ðåãèñòðàöèÿ';
$lang['menu_settings'] = 'Ëè÷íûå íàñòðîéêè';
$lang['menu_members'] = 'Characters';
$lang['menu_standings'] = 'Îáçîð ó÷àñòíèêîâ';
$lang['menu_stats'] = 'Ñòàòèñòèêà';
$lang['menu_summary'] = 'Îò÷åòû';

// Column Headers
$lang['account'] = 'Ó÷åòíàÿ çàïèñü';
$lang['action'] = 'Äåéñòâèå';
$lang['active'] = 'Àêòèâèðîâàí';
$lang['add'] = 'Äîáàâëåíèå';
$lang['added_by'] = 'Äîáàâèë(à)';
$lang['adjustment'] = 'Èçìåíåíèå ';
$lang['administration'] = 'Àäìèíèñòðèðîâàíèå';
$lang['administrative_options'] = 'Àäìèíèñòðàòèâíûå íàñòðîéêè';
$lang['admin_index'] = 'Ãëàâíàÿ Àäìèíöåíòðà';
$lang['attendance_by_event'] = 'Ïîñåùåíèå ñîáûòèé';
$lang['attended'] = 'Ïîñåùåíèå';
$lang['attendees'] = 'Ó÷àñòíèêè';
$lang['average'] = 'Ñðåäíèé';
$lang['buyer'] = 'Ïîêóïàòåëü';
$lang['buyers'] = 'Ïîêóïàòåëè';
$lang['class'] = 'Êëàññ';
$lang['armor'] = 'Áðîíÿ';
$lang['type'] = 'Áðîíÿ';
$lang['class_distribution'] = 'Ðàñïðåäåëåíèå ïî êëàññàì';
$lang['class_summary'] = "Îò÷åò ïî êëàññó: ñ %1\$s ïî %2\$s";
$lang['configuration'] = 'Îáùåå óïðàâëåíèå';
$lang['config_plus']	= 'PLUS íàñòðîéêè';
$lang['plus_vcheck']	= 'Ïðîâåðèòü îáíîâëåíèå';
$lang['current'] = 'Òåêóùèé ';
$lang['date'] = 'Äàòà';
$lang['delete'] = 'Óäàëèòü';
$lang['delete_confirmation'] = 'Ïîäòâåðæäåíèå îá óäàëåíèè';
$lang['dkp_value'] = "%1\$s Ñòîèìîñòü";
$lang['drops'] = 'Òðîôåè';
$lang['earned'] = 'Çàðàáîòàíî ';
$lang['enter_dates'] = 'Ââåñòè äàòû';
$lang['eqdkp_index'] = 'Ãëàâíàÿ EQdkp';
$lang['eqdkp_upgrade'] = 'EQdkp îáíîâëåíèå';
$lang['event'] = 'Ñîáûòèå';
$lang['events'] = 'Ñîáûòèÿ';
$lang['filter'] = 'Ôèëüòð';
$lang['first'] = 'Ïåðâûé';
$lang['rank'] = 'Ðàíã';
$lang['general_admin'] = 'Îáùåå óïðàâëåíèå';
$lang['get_new_password'] = 'Ïîëó÷èòü íîâûé ïàðîëü';
$lang['group_adj'] = 'Ãðóïïîâîå èçìåíåíèå';
$lang['group_adjustments'] = 'Ãðóïïîâûå èçìåíåíèÿ';
$lang['individual_adjustments'] = 'Èíäèâèäóàëüíûå èçìåíåíèÿ';
$lang['individual_adjustment_history'] = 'Èñòîðèÿ èíäèâèäóàëüíûõ èçìåíåíèé';
$lang['indiv_adj'] = 'Èíäèâèä. èçì.';
$lang['ip_address'] = 'IP-àäðåñ';
$lang['item'] = 'Ïðåäìåò';
$lang['items'] = 'Ïðåäìåòû';
$lang['item_purchase_history'] = 'Èñòîðèÿ ïîêóïêè ïðåäìåòîâ';
$lang['last'] = 'Ïîñëåäíèé';
$lang['lastloot'] = 'Ïîñëåäíèé òðîôåé';
$lang['lastraid'] = 'Ïîñëåäíèé Ðåéä';
$lang['last_visit'] = 'Ïîñëåäíèé âèçèò';
$lang['level'] = 'Óðîâåíü';
$lang['log_date_time'] = 'Äàòà/Âðåìÿ ýòîãî ëîãà';
$lang['loot_factor'] = 'Ôàêòîð òðîôåÿ';
$lang['loots'] = 'Òðîôåè';
$lang['manage'] = 'Óïðàâëåíèå';
$lang['member'] = 'Ó÷àñòíèê';
$lang['members'] = 'Ó÷àñòíèêè';
$lang['members_present_at'] = "Ó÷àñòíèêè ïîêàçàíû â %1\$s íà %2\$s";
$lang['miscellaneous'] = 'Ðàçíîå';
$lang['name'] = 'Èìÿ';
$lang['news'] = 'Íîâîñòü';
$lang['note'] = 'Ïðèìå÷àíèå';
$lang['online'] = 'Àêòèâåí(Online)';
$lang['options'] = 'Íàñòðîéêè';
$lang['paste_log'] = 'Âñòàâèòü ëîã íèæå';
$lang['percent'] = 'Ïðîöåíò';
$lang['permissions'] = 'Ïðàâà äîñòóïà';
$lang['per_day'] = 'Çà äåíü';
$lang['per_raid'] = 'Çà Ðåéä';
$lang['pct_earned_lost_to'] = '% ïîòðà÷åíî';
$lang['preferences'] = 'Íàñòðîéêè';
$lang['purchase_history_for'] = "Èñòîðèÿ ïîêóïîê äëÿ ïðåäìåòà %1\$s";
$lang['quote'] = 'Öèòàòà';
$lang['race'] = 'Ðàñà';
$lang['raid'] = 'Ðåéä';
$lang['raids'] = 'Ðåéäû';
$lang['raid_id'] = 'Ðåéä ID';
$lang['raid_attendance_history'] = 'Èñòîðèÿ ó÷àñòèÿ â Ðåéäå(àõ)';
$lang['raids_lifetime'] = "Àêòèâíîñòü: (%1\$s - %2\$s)";
$lang['raids_x_days'] = "Ïîñëåäíèõ %1\$d äíåé";
$lang['rank_distribution'] = 'Ðàñïðåäåëåíèå ïî ðàíãàì';
$lang['recorded_raid_history'] = "Çàïèñü èñòîðèè Ðåéäîâ äëÿ ëîêàöèè %1\$s";
$lang['reason'] = 'Ïðè÷èíà';
$lang['registration_information'] = 'Ðåãèñòðàöèîííàÿ èíôîðìàöèÿ';
$lang['result'] = 'Ðåçóëüòàò';
$lang['session_id'] = 'ID ñåññèè';
$lang['settings'] = 'Íàñòðîéêè';
$lang['spent'] = 'Ïîòðà÷åíî ';
$lang['summary_dates'] = "Îò÷åò ïî Ðåéäó: ñ %1\$s ïî %2\$s";
$lang['themes'] = 'Òåìû';
$lang['time'] = 'Âðåìÿ';
$lang['total'] = 'Âñåãî';
$lang['total_earned'] = 'Âñåãî çàðàáîòàíî';
$lang['total_items'] = 'Âñåãî ïðåäìåòîâ';
$lang['total_raids'] = 'Âñåãî Ðåéäîâ';
$lang['total_spent'] = 'Âñåãî ïîòðà÷åíî';
$lang['transfer_member_history'] = 'Ïåðåìåñòèòü èñòîðèþ ó÷àñòíèêà';
$lang['turn_ins'] = 'Ïåðåäà÷à ïðåäìåòîâ';
$lang['type'] = 'Òèï';
$lang['update'] = 'Îáíîâëåíèå';
$lang['updated_by'] = 'Îáíîâèë(à)';
$lang['user'] = 'Ïîëüçîâàòåëü';
$lang['username'] = 'Èìÿ Ïîëüçîâàòåëÿ';
$lang['value'] = 'Çíà÷åíèå';
$lang['view'] = 'Ïðîñìîòð';
$lang['view_action'] = 'Ïðîñìîòðåòü äåéñòâèå';
$lang['view_logs'] = 'Ïðîñìîòðåòü ëîãè';

// Page Foot Counts
$lang['listadj_footcount']               = "... íàéäåíî %1\$d èçìåíåíèé / %2\$d íà ñòðàíèöå";
$lang['listevents_footcount']            = "... íàéäåíî %1\$d ñîáûòèé / %2\$d íà ñòðàíèöå";
$lang['listiadj_footcount']              = "... íàéäåíî %1\$d èíäèâèäóàëüíûõ èçìåíåíèé / %2\$d íà ñòðàíèöå";
$lang['listitems_footcount']             = "... íàéäåíî %1\$d óíèêàëüíûõ ïðåäìåòîâ / %2\$d íà ñòðàíèöå";
$lang['listmembers_active_footcount']    = "... íàéäåíî %1\$d àêòèâíûõ ó÷àñòíèêîâ / %2\$sïîêàçàòü âñåõ</a>";
$lang['listmembers_compare_footcount']   = "... ñðàâíèâàåòñÿ %1\$d ó÷àñòíèêîâ";
$lang['listmembers_footcount']           = "... íàéäåíî %1\$d ó÷àñòíèêîâ";
$lang['listnews_footcount']              = "... íàéäåíî %1\$d íîâîñòåé / %2\$d íà ñòðàíèöå";
$lang['listpurchased_footcount']         = "... íàéäåíî %1\$d ïðåäìåò(îâ) / %2\$d íà ñòðàíèöå";
$lang['listraids_footcount']             = "... íàéäåíî %1\$d Ðåéä(îâ) / %2\$d íà ñòðàíèöå";
$lang['stats_active_footcount']          = "... íàéäåíî %1\$d àêòèâíûé(ûõ) ó÷àñòíèê(îâ) / %2\$sïîêàçàòü âñåõ</a>";
$lang['stats_footcount']                 = "... íàéäåíî %1\$d e÷àñòíèêîâ";
$lang['viewevent_footcount']             = "... íàéäåíî %1\$d Ðåéä(îâ)";
$lang['viewitem_footcount']              = "... íàéäåíî %1\$d Ïðåäìåò(îâ)";
$lang['viewmember_adjustment_footcount'] = "... íàéäåíî %1\$d Èíäèâèäóàëüíûõ èçìåíåíèé";
$lang['viewmember_item_footcount']       = "... íàéäåíî %1\$d êóïëåííûõ ïðåäìåòîâ / %2\$d íà ñòðàíèöå";
$lang['viewmember_raid_footcount']       = "... íàéäåíî %1\$d ïðîâåäåííûõ Ðåéä(îâ) / %2\$d íà ñòðàíèöå";
$lang['viewraid_attendees_footcount']    = "... íàéäåíî %1\$d ó÷àñòíèêîâ";
$lang['viewraid_drops_footcount']        = "... íàéäåíî %1\$d òðîôåÿ(åâ)";

// Submit Buttons
$lang['close_window'] = 'Çàêðûòü îêíî';
$lang['compare_members'] = 'Ñðàâíèòü ó÷àñòíèêîâ';
$lang['create_news_summary'] = 'Ñîçäàòü îò÷åò ïî íîâîñòÿì';
$lang['login'] = 'Âõîä';
$lang['logout'] = 'Âûõîä';
$lang['log_add_data'] = 'Äîáàâèòü äàííûå â ôîðìó';
$lang['lost_password'] = 'Çàáûë ïàðîëü';
$lang['no'] = 'Íåò';
$lang['proceed'] = 'Ïðîäîëæèòü';
$lang['reset'] = 'Ñáðîñ';
$lang['set_admin_perms'] = 'Íàçíà÷èòü ïðàâà àäìèíèñòðàòîðà';
$lang['submit'] = 'Îòïðàâèòü';
$lang['upgrade'] = 'Îáíîâèòü';
$lang['yes'] = 'Äà';

// Form Element Descriptions
$lang['admin_login'] = 'Ëîãèí Àäìèíèñòðàòîðà';
$lang['confirm_password'] = 'Ïîäòâåðäèòü ïàðîëü';
$lang['confirm_password_note'] = 'Ââåäèòå ïîâòîðíî íîâûé ïàðîëü';
$lang['current_password'] = 'Òåêóùèé ïàðîëü';
$lang['current_password_note'] = 'Óêàæèòå ñâîé òåêóùèé ïàðîëü äëÿ åãî èçìåíåíèÿ';
$lang['email'] = 'Email';
$lang['email_address'] = 'Email àäðåñ';
$lang['ending_date'] = 'Äàòà îêîí÷àíèÿ';
$lang['from'] = 'Îò';
$lang['guild_tag'] = 'Íàçâàíèå ãèëüäèè';
$lang['language'] = 'ßçûê';
$lang['new_password'] = 'Íîâûé ïàðîëü';
$lang['new_password_note'] = 'Ââåäèòå íîâûé ïàðîëü, åñëè æåëàåòå ïîìåíÿòü òåêóùèé ïàðîëü';
$lang['password'] = 'Ïàðîëü';
$lang['remember_password'] = 'Çàïîìíèòü ìåíÿ (cookie)';
$lang['starting_date'] = 'Äàòà íà÷àëà';
$lang['style'] = 'Ñòèëü';
$lang['to'] = 'Äëÿ';
$lang['username'] = 'Èìÿ ïîëüçîâàòåëÿ';
$lang['users'] = 'Ïîëüçîâàòåëè';

// Pagination
$lang['next_page'] = 'Ñëåäóþùàÿ ñòðàíèöà';
$lang['page'] = 'Ñòðàíèöà';
$lang['previous_page'] = 'Ïðåäûäóùàÿ ñòðàíèöà';

// Permission Messages
$lang['noauth_default_title'] = 'Îòêàç â äîñòóïå';
$lang['noauth_u_event_list'] = 'Ó âàñ íåò ïðàâ äëÿ ïðîñìîòðà ñïèñêà ñîáûòèé';
$lang['noauth_u_event_view'] = 'Ó âàñ íåò ïðàâ äëÿ ïðîñìîòðà ñîáûòèé';
$lang['noauth_u_item_list'] = 'Ó âàñ íåò ïðàâ äëÿ ïðîñìîòðà ñïèñêà ïðåäìåòîâ';
$lang['noauth_u_item_view'] = 'Ó âàñ íåò ïðàâ äëÿ ïðîñìîòðà ïðåäìåòîâ';
$lang['noauth_u_member_list'] = 'Ó âàñ íåò ïðàâ äëÿ ïðîñìîòðà ðåéòèíãà ó÷àñòíèêîâ';
$lang['noauth_u_member_view'] = 'Ó âàñ íåò ïðàâ äëÿ ïðîñìîòðà èñòîðèè ó÷àñòíèêîâ';
$lang['noauth_u_raid_list'] = 'Ó âàñ íåò ïðàâ äëÿ ïðîñìîòðà ñïèñêà Ðåéäîâ';
$lang['noauth_u_raid_view'] = 'Ó âàñ íåò ïðàâ äëÿ ïðîñìîòðà Ðåéäîâ';

// Submission Success Messages
$lang['add_itemvote_success'] = 'Âàø ãîëîñ ïî ïðåäìåòó óñïåøíî çàôèêñèðîâàí';
$lang['update_itemvote_success'] = 'Âàø ãîëîñ ïî ïðåäìåòó óñïåøíî îáíîâëåí';
$lang['update_settings_success'] = 'Íàñòðîéêè ïîëüçîâàòåëÿ óñïåøíî îáíîâëåíû';

// Form Validation Errors
$lang['fv_alpha_attendees'] = 'Èìåíà ïåðñîíàæåé â EverQuest ìîãóò ñîäåðæàòü òîëüêî áóêâû àëôàâèòà';
$lang['fv_already_registered_email'] = 'Ýòîò àäðåñ e-mail óæå çàðåãèñòðèðîâàí';
$lang['fv_already_registered_username'] = 'Ýòî èìÿ ïîëüçîâàòåëÿ óæå çàðåãèñòðèðîâàíî';
$lang['fv_difference_transfer'] = 'Ïåðåíîñ èñòîðèè äîëæåí ïðîèçâîäèòüñÿ ìåæäó äâóìÿ ðàçíûìè ó÷àñòíèêàìè';
$lang['fv_difference_turnin'] = 'Ïîêóïêà äîëæíà ïðîèçâîäèòüñÿ ìåæäó äâóìÿ ðàçíûìè ó÷àñòíèêàìè';
$lang['fv_invalid_email'] = 'Àäðåñ e-mail íå äåéñòâèòåëåí';
$lang['fv_match_password'] = 'Ïîëÿ ïàðîëÿ äîëæíû áûòü îäèíàêîâûìè';
$lang['fv_member_associated']  = "%1\$s óæå àññîöèèðîâàí ñ ó÷åòíîé çàïèñüþ äðóãîãî ó÷àñòíèêà";
$lang['fv_number'] = 'Äîëæíî áûòü ÷èñëî';
$lang['fv_number_adjustment'] = 'Ïîëå êîëè÷åñòâà èçìåíåíèÿ äîëæíî áûòü ÷èñëîì';
$lang['fv_number_alimit'] = 'Ïîëå ïðåäåëà èçìåíåíèÿ äîëæíî áûòü ÷èñëîì';
$lang['fv_number_ilimit'] = 'Ïîëå ïðåäåëà ïðåäìåòîâ äîëæíî áûòü ÷èñëîì';
$lang['fv_number_inactivepd'] = 'Ïåðèîä íåàêòèâíîñòè äîëæåí áûòü ÷èñëîì';
$lang['fv_number_pilimit'] = 'Ïðåäåë êóïëåííûõ ïðåäìåòîâ äîëæåí áûòü ÷èñëîì';
$lang['fv_number_rlimit'] = 'Ïðåäåë Ðåéäîâ äîëæåí áûòü ÷èñëîì';
$lang['fv_number_value'] = 'Ïîëå ñòîèìîñòè äîëæíî áûòü ÷èñëîì';
$lang['fv_number_vote'] = 'Ïîëå ãîëîñîâàíèÿ äîëæíî áûòü ÷èñëîì';
$lang['fv_date'] = 'Ïîæàëóéñòà, âûáåðèòå ïðàâèëüíóþ äàòó â êàëåíäàðå';
$lang['fv_range_day'] = 'Ïîëå äíÿ äîëæíî áûòü ÷èñëîì ìåæäó 1 è 31';
$lang['fv_range_hour'] = 'Ïîëå ÷àñà äîëæíî áûòü ÷èñëîì ìåæäó 0 è 23';
$lang['fv_range_minute'] = 'Ïîëå ìèíóòû äîëæíî áûòü ÷èñëîì ìåæäó 0 è 59';
$lang['fv_range_month'] = 'Ïîëå ìåñÿöà äîëæíî áûòü ìåæäó 1 è 12';
$lang['fv_range_second'] = 'Ïîëå ñåêóíäû äîëæíî áûòü ÷èñëîì ìåæäó 0 è 59';
$lang['fv_range_year'] = 'Ïîëå ãîäà äîëæíî ñîäåðæàòü ÷èñëî íå ìåíüøå 1998';
$lang['fv_required'] = 'Íåîáõîäèìîå ïîëå';
$lang['fv_required_acro'] = 'Ïîëå àêðîíèìà ãèëüäèè íåîáõîäèìî';
$lang['fv_required_adjustment'] = 'Ïîëå êîëè÷åñòâà èçìåíåíèÿ íåîáõîäèìî';
$lang['fv_required_attendees'] = 'Âûáåðèòå ó÷àñòíèêîâ Ðåéäà';
$lang['fv_required_buyer'] = 'Âûáåðèòå ïîêóïàòåëÿ';
$lang['fv_required_buyers'] = 'Õîòÿ áû îäèí ïîêóïàòåëü äîëæåí áûòü âûáðàí';
$lang['fv_required_email'] = 'Ïîëå àäðåñà e-mail íåîáõîäèìî';
$lang['fv_required_event_name'] = 'Âûáåðèòå ñîáûòèå';
$lang['fv_required_guildtag'] = 'Óêàæèòå íàçâàíèå ãèëüäèè';
$lang['fv_required_headline'] = 'Óêàæèòå çàãîëîâîê';
$lang['fv_required_inactivepd'] = 'Åñëè ñêðûòèå íåàêòèâíûõ ó÷àñòíèêîâ âêëþ÷åíî, äîëæíî áûòü ââåäåíî çíà÷åíèå íåàêòèâíîñòè';
$lang['fv_required_item_name'] = 'Ïîëå íàçâàíèÿ ïðåäìåòà äîëæíî áûòü çàïîëíåíî èëè âûáðàí ñóùåñòâóþùèé ïðåäìåò';
$lang['fv_required_member'] = 'Äîëæåí áûòü óêàçàí ó÷àñòíèê';
$lang['fv_required_members'] = 'Äîëæåí áûòü âûáðàí õîòÿ áû îäèí ó÷àñòíèê';
$lang['fv_required_message'] = 'Íå ââåäåíî ñîîáùåíèå';
$lang['fv_required_name'] = 'Çàïîëíèòå ïîëå íàçâàíèÿ';
$lang['fv_required_password'] = 'Çàïîëíèòå ïîëå ïàðîëÿ';
$lang['fv_required_raidid'] = 'Íå âûáðàí Ðåéä';
$lang['fv_required_user'] = 'Óêàæèòå èìÿ ïîëüçîâàòåëÿ';
$lang['fv_required_value'] = 'Óêàæèòå çíà÷åíèå';
$lang['fv_required_vote'] = 'Íåîáõîäèìî ïðîãîëîñîâàòü';

// Miscellaneous
$lang['added'] = 'Äîáàâëåíî';
$lang['additem_raidid_note'] = "Îòîáðàæàþòñÿ Ðåéäû çà ïðîøåäøèå äâå íåäåëè / %1\$s ïîêàçàòü âñå</a>";
$lang['additem_raidid_showall_note'] = 'Ïîêàçàòü âñå Ðåéäû';
$lang['addraid_datetime_note'] = 'Åñëè âû ïåðåäàåòå ëîã íà ñèíòàêñè÷åñêèé àíàëèç, äàòà è âðåìÿ áóäóò îïðåäåëåíû àâòîìàòè÷åñêè';
$lang['addraid_value_note'] = 'Äëÿ åäèíîâðåìåííîãî áîíóñà; åñëè ïîëå îñòàâèòü ïóñòûì áóäåò èñïîëüçîâàòüñÿ çíà÷åíèå ïî óìîë÷àíèþ äëÿ ýòîãî ñîáûòèÿ';
$lang['add_items_from_raid'] = 'Äîáàâèòü ïðåäìåòû ñ ýòîãî Ðåéäà';
$lang['deleted'] = 'Óäàëåíî';
$lang['done'] = 'Ãîòîâî';
$lang['enter_new'] = 'Ââåñòè íîâûé';
$lang['error'] = 'Îøèáêà';
$lang['head_admin'] = 'Ãëàâíûé àäìèíèñòðàòîð';
$lang['hold_ctrl_note'] = 'Çàæìèòå CTRL ÷òîáû âûáðàòü íåñêîëüêî ó÷àñòíèêîâ ëèáî ïóíêòîâ';
$lang['list'] = 'Ñïèñîê';
$lang['list_groupadj'] = 'Âûâåñòè ñïèñîê ãðóïïîâûõ èçìåíåíèé';
$lang['list_events'] = 'Âûâåñòè ñïèñîê ñîáûòèé';
$lang['list_indivadj'] = 'Âûâåñòè ñïèñîê èíäèâèäóàëüíûõ èçìåíåíèé';
$lang['list_items'] = 'Âûâåñòè ñïèñîê ïðåäìåòîâ';
$lang['list_members'] = 'Âûâåñòè ñïèñîê ó÷àñòíèêîâ';
$lang['list_news'] = 'Âûâåñòè ñïèñîê íîâîñòåé';
$lang['list_raids'] = 'Âûâåñòè ñïèñîê Ðåéäîâ';
$lang['may_be_negative_note'] = 'ìîæåò áûòü îòðèöàòåëüíûì';
$lang['not_available'] = 'Íå äîñòóïíî';
$lang['no_news'] = 'Íè÷åãî íîâîãî íå íàéäåíî';
$lang['of_raids'] = "%1\$d%% Ðåéäîâ";
$lang['or'] = 'Èëè';
$lang['powered_by'] = 'Ïîääåðæèâàåòñÿ';
$lang['preview'] = 'Ïðåäâàðèòåëüíûé Ïðîñìîòð';
$lang['required_field_note'] = 'Âñå ïîëÿ, ïîìå÷åííûå * (çâåçäî÷êîé), îáÿçàòåëüíû äëÿ çàïîëíåíèÿ';
$lang['select_1ofx_members'] = "Âûáðàòü îäíîãî èç %1\$d ó÷àñòíèêîâ...";
$lang['select_existing'] = 'Âûáðàòü ñðåäè ñóùåñòâóþùåãî';
$lang['select_version'] = 'Âûáåðèòå âåðñèþ EQdkp, êîòîðóþ âû õîòèòå îáíîâèòü:';
$lang['success'] = 'Óñïåøíî';
$lang['s_admin_note'] = 'Óïðàâëåíèå ïðàâàìè äîñòóïà äîñòóïíî òîëüêî äëÿ Àäìèíèñòðàöèè';
$lang['transfer_member_history_description'] = 'Ýòî ïåðåíåñåò âñþ èñòîðèþ ó÷àñòíèêà (Ðåéäû, ïðåäìåòû, èçìåíåíèÿ) ê äðóãîìó ó÷àñòíèêó';
$lang['updated'] = 'îáíîâëåíî ';
$lang['upgrade_complete'] = 'Ïðîöåññ îáíîâëåíèÿ EQdkp óñïåøíî çàâåðøåí.<br /><br /><b class="negative">Óäàëèòå äàííûé ôàéë â öåëÿõ áåçîïàñíîñòè!</b>';

// Settings
$lang['account_settings'] = 'Íàñòðîéêè ó÷åòíîé çàïèñè';
$lang['adjustments_per_page'] = 'Óêàæèòå, ñêîëüêî âûâîäèòü èçìåíåíèé íà ñòðàíèöó';
$lang['basic'] = 'Îñíîâíûå';
$lang['events_per_page'] = 'Óêàæèòå, ñêîëüêî âûâîäèòü ñîáûòèé íà ñòðàíèöó';
$lang['items_per_page'] = 'Óêàæèòå, ñêîëüêî âûâîäèòü ïðåäìåòîâ íà ñòðàíèöó';
$lang['news_per_page'] = 'Óêàæèòå, ñêîëüêî âûâîäèòü íîâîñòåé íà ñòðàíèöó';
$lang['raids_per_page'] = 'Óêàæèòå, ñêîëüêî âûâîäèòü Ðåéäîâ íà ñòðàíèöó';
$lang['associated_members'] = 'Ïåðñîíàæè ïîëüçîâàòåëÿ';
$lang['guild_members'] = 'Ó÷àñòíèêè ãèëüäèè';
$lang['default_locale'] = 'Ëîêàëü ïî óìîë÷àíèþ';


// Error messages
$lang['error_account_inactive'] = 'Âàøà ó÷åòíàÿ çàïèñü íåàêòèâíà';
$lang['error_already_activated'] = 'Ýòà ó÷åòíàÿ çàïèñü óæå àêòèâèðîâàíà';
$lang['error_invalid_email'] = 'Äåéñòâèòåëüíûé àäðåñ e-mail íå áûë ïðåäîñòàâëåí';
$lang['error_invalid_event_provided'] = 'Ñóùåñòâóþùèé id ñîáûòèÿ íå áûë ïðåäîñòàâëåí';
$lang['error_invalid_item_provided'] = 'Ñóùåñòâóþùèé id ïðåäìåòà íå áûë ïðåäîñòàâëåí';
$lang['error_invalid_key'] = 'Âû ïðåäîñòàâèëè íåïðàâèëüíûé êëþ÷ àêòèâàöèè';
$lang['error_invalid_name_provided'] = 'Ñóùåñòâóþùåå èìÿ ó÷àñòíèêà íå áûëî ïðåäîñòàâëåíî';
$lang['error_invalid_news_provided'] = 'Ñóùåñòâóþùèé id íîâîñòè íå áûë ïðåäîñòàâëåí';
$lang['error_invalid_raid_provided'] = 'Ñóùåñòâóþùèé id Ðåéäà íå áûë ïðåäîñòàâëåí';
$lang['error_user_not_found'] = 'Ñóùåñòâóþùåå èìÿ ïîëüçîâàòåëÿ íå áûëî ïðåäîñòàâëåíî';
$lang['incorrect_password'] = 'Íåïðàâèëüíûé ïàðîëü';
$lang['invalid_login'] = 'Âû ïðåäîñòàâèëè íåïðàâèëüíîå èìÿ ïîëüçîâàòåëÿ èëè ïàðîëü';
$lang['not_admin'] = 'Âû íå ÿâëÿåòåñü àäìèíèñòðàòîðîì';

// Registration
$lang['account_activated_admin']   = 'Ó÷åòíàÿ çàïèñü àêòèâèðîâàíà. Ïèñüìî, èíôîðìèðóþùåå îá ýòîì èçìåíåíèè îòïðàâëåíî íà ýëåêòðîííóþ ïî÷òó';
$lang['account_activated_user']    = "Âàøà ó÷åòíàÿ çàïèñü àêòèâèðîâàíà è òåïåðü âû ìîæåòå %1\$s âîéòè %2\$s.";
$lang['password_sent'] = 'Íîâûé ïàðîëü ê âàøåé ó÷åòíîé çàïèñè îòïðàâëåí íà âàøó ýëåêòðîííóþ ïî÷òó.';
$lang['register_activation_self']  = "Âàøà Ó÷åòíàÿ çàïèñü ñîçäàíà, íî ïåðåä òåì êàê åå èñïîëüçîâàòü âû äîëæíû àêòèâèðîâàòü åå.<br /><br />Íà âàøó ýëåòðîííóþ ïî÷òó %1\$s îòïðàâëåíî ïèñüìî ñ èíôîðìàöèåé î òîì, êàê àêòèâèðîâàòü âàøó ó÷åòíóþ çàïèñü";
$lang['register_activation_admin'] = "Âàøà Ó÷åòíàÿ çàïèñü ñîçäàíà, íî ïåðåä òåì êàê åå èñïîëüçîâàòü Àäìèíèñòðàòîð äîëæåí àêòèâèðîâàòü åå.<br /><br />Íà âàøó ýëåòðîííóþ ïî÷òó %1\$s îòïðàâëåíî ïèñüìî ñ äîïîëíèòåëüíîé èíôîðìàöèåé";
$lang['register_activation_none']  = "Âàøà Ó÷åòíàÿ çàïèñü ñîçäàíà è òåïåðü âû ìîæåòå %1\$s âîéòè %2\$s.<br /><br />Íà âàøó ýëåòðîííóþ ïî÷òó %3\$s îòïðàâëåíî ïèñüìî ñ äîïîëíèòåëüíîé èíôîðìàöèåé";

//plus
$lang['news_submitter'] = 'Îòïðàâèë(à)';
$lang['news_submitat'] = 'Íà';
$lang['droprate_loottable'] = "Òàáëèöà òðîôååâ";
$lang['droprate_name'] = "Íàçâàíèå ïðåäìåòà";
$lang['droprate_count'] = "Öåíà";
$lang['droprate_drop'] = "Øàíñ âûïàäåíèÿ %";

$lang['Itemsearch_link'] = "Ïðåäìåò-ïîèñê";
$lang['Itemsearch_search'] = "Ïîèñê ïðåäìåòîâ :";
$lang['Itemsearch_searchby'] = "Íàéäåí(à) :";
$lang['Itemsearch_item'] = "Ïðåäìåò ";
$lang['Itemsearch_buyer'] = "Ïîêóïàòåëü ";
$lang['Itemsearch_raid'] = "Ðåéä ";
$lang['Itemsearch_unique'] = "Ðåçóëüòàò ñðåäè óíèêàëüíûõ ïðåäìåòîâ :";
$lang['Itemsearch_no'] = "Äà";
$lang['Itemsearch_yes'] = "íåò";

$lang['bosscount_player'] = "Ó÷àñòíèêîâ: ";
$lang['bosscount_raids'] = "Ðåéäîâ: ";
$lang['bosscount_items'] = "Ïðåäìåòîâ: ";
$lang['bosscount_dkptotal'] = "Òåêóùèé DKP: ";

//MultiDKP
$lang['Plus_menuentry'] 			= "EQDKP Plus";
$lang['Multi_entryheader'] 		= "MultiDKP - Äîáàâèòü Pool";
$lang['Multi_pageheader'] 		= "MultiDKP - Ïîêàçàòü Pools";
$lang['Multi_events'] 				= "Ñîáûòèÿ:";
$lang['Multi_eventname'] 				= "Íàçâàíèå Ñîáûòèÿ";
$lang['Multi_discnottolong'] 	= "(Èìÿ ñòðîêè) -íå äîëæíî áûòü ñëèøêîì äëèííûì, èíà÷å ñòîë ñòàíåò áîëüøå,. Âûáåðèòå ò.å. MC, BWL, AQ etc. !";
$lang['Multi_kontoname_short']= "Ñîñòîÿíèå:";
$lang['Multi_discr'] 					= "Îò÷åò:";
$lang['Multi_events'] 				= "Ñîáûòèÿ â ýòîì Pool";

$lang['Multi_addkonto'] 			  = "Äîáàâèòü/Èçìåíèòü MultiDKP Pool";
$lang['Multi_updatekonto'] 			= "Èçìåíèòü Pool";
$lang['Multi_deletekonto'] 			= "Óäàëèòü Pool";
$lang['Multi_viewkonten']			  = "Ïîêàçàòü MultiDKP Pools";
$lang['Multi_chooseevents']			= "Âûáðàòü ñîáûòèå";
$lang['multi_footcount'] 				= "...íàéäåíî %1\$d DKP Pools / %2\$d íà ñòðàíèöå";
$lang['multi_error_invalid']    = "No Pools assigned....";
$lang['Multi_required_event']   = "Âû äîëæíû âûáðàòü, ïî êðàéíåé ìåðå, 1-íî ñîáûòèå!";
$lang['Multi_required_name']    = "Âû äîëæíû ââåñòè íàçâàíèå(èìÿ)!";
$lang['Multi_required_disc']    = "Âû äîëæíû ââåñòè îò÷åò!";
$lang['Multi_admin_add_multi_success'] = "The Pool %1\$s ( %2\$s ) ñ ñîáûòèÿìè %3\$s áûë äîáàâëåí â áàçó äàííûõ";
$lang['Multi_admin_update_multi_success'] = "The Pool %1\$s ( %2\$s ) ñ ñîáûòèÿìè %3\$s áûë èçìåíåí â áàçå äàííûõ";
$lang['Multi_admin_delete_success']           = "The Pool %1\$s áûë óäàëåí èç áàçû äàííûõ";
$lang['Multi_confirm_delete']    = 'Âû äåéñòâèòåëüíî óâåðåíû è õîòèòå óäàëèòü äàííûé Pool?';


$lang['Multi_total_cost']   										= 'Ñóììà î÷êîâ äëÿ äàííîé Pool';
$lang['Multi_Accs']    													= 'MultiDKP Pool';

//update
$lang['upd_eqdkp_status']    										= 'Îáíîâèòü EQDKP ñòàòóñ';
$lang['upd_system_status']    									= 'Ñòàòóñ ñèñòåìû';
$lang['upd_template_status']    								= 'Ñòàòóñ øàáëîíà';
$lang['upd_gamefile_status']                    = 'Game Status';
$lang['upd_update_need']    										= 'Îáíîâèòü íåîáõîäèìî!';
$lang['upd_update_need_link']    								= 'Óñòàíîâèòü âñå òðåáóåìûå êîìïîíåíòû';
$lang['upd_no_update']    											= 'Îáíîâëåíèå íå òðåáóåòñÿ. Ñèñòåìà ñîäåðæèò ïîñëåäíåå îáíîâëåíèå';
$lang['upd_status']    													= 'Ñòàòóñ';
$lang['upd_state_error']    										= 'Îøèáêà';
$lang['upd_sql_string']    											= 'SQL êîìàíäà';
$lang['upd_sql_status_done']    								= 'Ñäåëàòü(Ðåøèòü)';
$lang['upd_sql_error']    											= 'Îøèáêà';
$lang['upd_sql_footer']    											= 'SQL êîìàíäà âûïîëíåíà';
$lang['upd_sql_file_error']    									= 'Îøèáêà: Òðåáóåìûé SQL ôàéë %1\$s íå áûë íàéäåí!';
$lang['upd_eqdkp_system_title']    							= 'Êîìïîíåíò EQDKP ñèñòåìû îáíîâëåí';
$lang['upd_plus_version']    										= 'Âåðñèÿ EQDKP Plus';
$lang['upd_plus_feature']    										= 'Ôóíêöèÿ';
$lang['upd_plus_detail']    										= 'Äåòàëè';
$lang['upd_update']    													= 'Îáíîâèòü';
$lang['upd_eqdkp_template_title']    						= 'Øàáëîí EQDKP îáíîâëåí';
$lang['upd_eqdkp_gamefile_title']               = 'EQDKP game update';
$lang['upd_gamefile_availversion']              = 'Available Version';
$lang['upd_gamefile_instversion']               = 'Installed Version';
$lang['upd_template_name']    									= 'Èìÿ øàáëîíà';
$lang['upd_template_state']    									= 'Ñòàòóñ øàáëîíà';
$lang['upd_template_filestate']    							= 'Ïàïêà øàáëîíà';
$lang['upd_link_install']    										= 'Îáíîâèòü';
$lang['upd_link_reinstall']    									= 'Óñòàíîâèòü';
$lang['upd_admin_need_update']    							= 'Îøèáêà áàçû äàííûõ áûëà îáíàðóæåíà. Ñèñòåìà íå up to date and needs to be updated.';
$lang['upd_admin_link_update']									= 'Êëèêíèòå ñþäà, ÷òîáû ðåøèòü ïðîáëåìû';
$lang['upd_backto']    													= 'Íàçàä ê àíàëèçó';

// Event Icon
$lang['event_icon_header']    								  = 'Âûáåðèòå èêîíêó ñîáûòèÿ';

//update Itemstats
$lang['updi_header']    								    	= 'Îáíîâèòü ñòàòèñòèêó ïðåäìåòîâ â áàçå';
$lang['updi_header2']    								    	= 'Èíôîðìàöèÿ î ñòàòèñòèêå Ïðåäìåòîâ';
$lang['updi_action']    								    	= 'Äåéñòâèå';
$lang['updi_notfound']    								    = 'Íå íàéäåíî';
$lang['updi_writeable_ok']    							  = 'Ôàéë ïåðåçàïèñàí';
$lang['updi_writeable_no']    								= 'Ôàéë íå ïåðåçàïèñàí';
$lang['updi_help']    								    		= 'Îïèñàíèå';
$lang['updi_footcount']    								    = 'Ïðåäìåò îáíîâëåí';
$lang['updi_curl_bad']    								    = 'Òðåáóåìàÿ ôóíêöèÿ PHP cURL íå íàéäåíà. Âîçìîæíî, ñòàòèñòèêà ïðåäìåòîâ ðàáîòàåò íå ïðàâèëüíî. Ïîæàëóéñòà, ñâÿæèòåñü ñ Àäìèíèñòðàòîðîì';
$lang['updi_curl_ok']    								    	= 'cURL íàéäåí';
$lang['updi_fopen_bad']    								    = 'Òðåáóåìàÿ ôóíêöèÿ PHP fopen íå íàéäåíà. Âîçìîæíî, ñòàòèñòèêà ïðåäìåòîâ ðàáîòàåò íå ïðàâèëüíî. Ïîæàëóéñòà, ñâÿæèòåñü ñ Àäìèíèñòðàòîðîì';
$lang['updi_fopen_ok']    								    = 'fopen íàéäåí';
$lang['updi_nothing_found']						    		= 'Ïðåäìåòû íå íàéäåíû';
$lang['updi_itemscount']  						    		= 'Âõîäû ÊÝØà Ïðåäìåòîâ:';
$lang['updi_baditemscount']						    		= 'Ïëîõîé âõîä:';
$lang['updi_items']										    		= 'Ïðåäìåòû â áàçå äàííûõ:';
$lang['updi_items_duplicate']					    		= '{ñ äâîéíûìè ïðåäìåòàìè}';
$lang['updi_show_all']    								    = 'Ñïèñîê âñåõ ïðåäìåòîâ ñî ñòàòèñòèêîé';
$lang['updi_refresh_all']    								  = 'Óäàëèòü âñå ïðåäìåòû è îáíîâèòü èõ';
$lang['updi_refresh_bad']    								  = 'Îáíîâèòü òîëüêî íåïðàâèëüíûå ïðåäìåòû';
$lang['updi_refresh_raidbank']    						= 'Îáíîâèòü Ïðåäìåòû Raidbanker(à)';
$lang['updi_refresh_tradeskill']   						= 'Îáíîâèòü Ïðåäìåòû Tradeskill(à)';
$lang['updi_help_show_all']    								= 'Ïîêàçàòü âñå ïðåäìåòû ñ èõ ñòàòèñòèêàìè. Ïëîõèå ñòàòèñòèêè áóäóò îáíîâëåíû (Ðåêîìåíäóåòñÿ)';
$lang['updi_help_refresh_all']  							= 'Óäàëåííûé òåêóùèé êýø ïðåäìåòîâ è tries to refresh all items that are listed in EQDKP. WARNING: If you share your Itemcache with a forum, the items from the forum cannot be refreshed. Depending on your webservers speed and the availability of Allakhazam.com this action could take several minutes. Possibly your webserver settings forbid a successful execution. In this case please contact your administrator';
$lang['updi_help_refresh_bad']    						= 'Óäàëèòü âñå ïëîõèå ïðåäìåòû èç êýøà è îáíîâèòü èõ';
$lang['updi_help_refresh_raidbank']    				= 'Raidbanker óñòàíîâëåí, Ñòàòèñòèêà Ïðåäìåòîâ uses the entered items of the banker';
$lang['updi_help_refresh_Tradeskill']    			= 'Êîãäà Tradeskill óñòàíîâëåí, ââåäåííûå ïðåäìåòû áóäóò îáíîâëåíû â ñòàòèñòèêå Ïðåäìåòîâ';

$lang['updi_active'] 					   							= 'Àêòèâèðîâàíî';
$lang['updi_inactive']    										= 'Äåàêòèâèðîâàíî';

$lang['fontcolor']    			  = 'Öâåò øðèôòà';
$lang['Warrior']    					        = 'Âîèí';
$lang['Rogue']    						= 'Ðàçáîéíèê';
$lang['Hunter']    						= 'Îõîòíèê';
$lang['Paladin']    					        = 'Ïàëàäèí';
$lang['Priest']    						= 'Æðåö';
$lang['Druid']    						= 'Äðóèä';
$lang['Shaman']    						= 'Øàìàí';
$lang['Warlock']    					        = 'Êîëäóí';
$lang['Mage']    					        = 'Ìàã';

# Reset DB Feature
$lang['reset_header']    			= 'Ñáðîñèòü äàòó EQDKP';
$lang['reset_infotext']  			= 'Ïðåäóïðåæäåíèå!!! Óäàëåííûå äàííûå ìîãóò áûòü ñáðîøåíû!!! Ñäåëàéòå ïîñëåäíþþ êîïèþ. Ïîäòâåðäèòå äåéñòâèå, íàæìèòå ÓÄÀËÈÒÜ â ÿùèêå ðåäàêòèðîâàíèÿ';
$lang['reset_type']    				= 'Òèï äàòû';
$lang['reset_disc']    				= 'Îïèñàíèå';
$lang['reset_sec']    				= 'Ñåðòèôèêàò';
$lang['reset_action']    			= 'Äåéñòâèå';

$lang['reset_news']					  = 'Íîâîñòè';
$lang['reset_news_disc']		  = 'Óäàëèòü âñå íîâîñòè èç áàçû äàííûõ';
$lang['reset_dkp'] 					  = 'DKP';
$lang['reset_dkp_disc']			  = 'Óäàëèòü âñå Ðåéäû è Ïðåäìåòû èç áàçû äàííûõ è ñáðîñèòü âñå DKP î÷êè äî 0';
$lang['reset_ALL']   					= 'Âñå';
$lang['reset_ALL_DISC']				= 'Óäàëèòü ëþáîé Ðåéä, Ïðåäìåò íà ó÷àñòíèêîâ. Ñáðîñ äàííûõ çàâåðøåí. (Íå óäàëÿåò Ïîëüçîâàòåëåé)';

$lang['reset_confirm_text']	  = ' Íàæìèòå ñþäà =>';
$lang['reset_confirm']			  = 'ÓÄÀËÈÒÜ';

// Armory Menu
$lang['lm_armorylink1']				= 'Armory';
$lang['lm_armorylink2']				= 'Òàëàíòû';
$lang['lm_armorylink3']				= 'Ãèëüäèÿ';

$lang['updi_update_ready']			= 'Ïðåäìåòû áûëè óñïåøíî îáíîâëåíû. Âû ìîæåòå óâèäåòü <a href="#" onclick="javascript:parent.closeWindow()" >close</a> â ýòîì îêíå';
$lang['updi_update_alternative']= 'Àëüòåðíàòèâíûé ìåòîä îáíîâëåíèÿ áûë àííóëèðîâàí èç-çà âðåìåíè îæèäàíèÿ';
$lang['zero_sum']				= ' íà íóëü ñóììèðîâàíî DKP';

//Hybrid
$lang['Hybrid']				= 'Ãèáðèä';

$lang['Jump_to'] 				= 'Ïðîñìîòðåòü âèäåî íà ';
$lang['News_vid_help'] 			= 'To embed videos just post the link to the video without [tags]. Ïîääåðæèâàåìûé âèäåî ñàéòû: google video, youtube, myvideo, clipfish, sevenload, metacafe and streetfire ';

$lang['SubmitNews'] 		   = 'Îòïðàâèòü íîâîñòü';
$lang['SubmitNews_help'] 	   = 'Ó âàñ åñòü õîðîøàÿ íîâîñòü? Îòïðàâüòå íîâîñòü è ïîäåëèòåñü ñî âñåìè Eqdkp Plus Ïîëüçîâàòåëÿìè';

$lang['MM_User_Confirm']	   = 'Âûáðàëè âàøó ó÷åòíóþ çàïèñü Àäìèíèñòðàòîðà? Åñëè âû èìååòå ïðàâà Àäìèíèñòðàòîðà, ýòî ìîæåò áûòü òîëüêî ñáðîøåíî â áàçå äàííûõ';

$lang['beta_warning']	   	   = 'Âíèìàíèå!! Äàííàÿ âåðñèÿ EQDKP-Plus ÿâëÿåòñÿ Beta! Ìû Íàñòîÿòåëüíî ðåêîìåíäóåì èñïîëüçîâàòü ïîñëåäíþþ ñòàáèëüíóþ âåðñèþ. Êëèêíèòå <a href="http://www.eqdkp-plus.com" >www.eqdkp-plus.com</a> äëÿ ïðîâåðêè îáíîâëåíèÿ!';

$lang['news_comment']        = 'Êîììåíòàðèé';
$lang['news_comments']       = 'Êîììåíòàðèè';
$lang['comments_no_comments']	   = 'No entries';
$lang['comments_comments_raid']	   = 'Êîììåíòàðèè';
$lang['comments_write_comment']	   = 'Ïðî÷åñòü êîììåíòàðèé';
$lang['comments_send_comment']	   = 'Ñîõðàíèòü êîììåíòàðèé';
$lang['comments_save_wait']	   	   = 'Ïîæàëóéñòà, ïîäîæäèòå, êîììåíòàðèé ñîõðàíÿåòñÿ...';

$lang['news_nocomments'] 	 		    = 'Disallow Comments';
$lang['news_readmore_button']  			  	= 'Extend News';
$lang['news_readmore_button_help']  			  	= 'To use the extended Newstext, click here:';
$lang['news_message'] 				  	= 'Òåêñò íîâîñòåé';
$lang['news_permissions']			  	= 'Ïðàâà äëÿ ïðîñìîòðà';

$lang['news_permissions_text']			= 'Ïîêàçûâàòü íîâîñòè äëÿ:';
$lang['news_permissions_guest']			= 'Òîëüêî Ãîñòåé';
$lang['news_permissions_member']		= 'Ãîñòåé è Ó÷àñòíèêîâ (òîëüêî Àäìèíèñòðàòîðû ìîãóò âèäåòü)';
$lang['news_permissions_all']			= 'Âñåõ';
$lang['news_readmore'] 				  	= 'Ïðî÷åñòü áîëüøå...';

$lang['recruitment_open']				= 'Â ãèëüäèþ òðåáóþòñÿ:';
$lang['recruitment_contact']			= 'Êîíòàêò';

$lang['sig_conf']						= 'Êëèêíèòå ïî èçîáðàæåíèþ ,÷òîáû ïîëó÷èòü BB êîä';
$lang['sig_show']						= 'Ïîêàçàòü WoW ñèãíàòóðó äëÿ âàøåãî ôîðóìà';


//Shirtshop
$lang['service']					    = 'Ñåðâèñ';
$lang['shirt_ad1']					    = 'Go to the Shirt-shop. <br> get your own shirt now!';
$lang['shirt_ad2']					    = 'Âûáåðèòå âàøåãî ïåðñîíàæà';
$lang['shirt_ad3']					    = 'Ïðèâåòñòâóþ âàñ â ìàãàçèíå Ãèëüäèè ';
$lang['shirt_ad4']					    = 'Wähle eines der vorgefertigten Produkte aus, oder erstell Dir mit dem Creator ein komplett eigenes Shirt.<br>
										   Du kannst jedes Shirt nach Deinen Bedürfnissen anpassen und jeden Schriftzug verändern.<br>
										   Unter Motive findest alle zur Verfügung stehenden Motive!';
$lang['error_iframe']					= "Âàø áðàóçåð íå ïîääåðæèâàåò Frames!";
$lang['new_window']						= 'Îòêðûòü âêëàäêó â íîâîì îêíå';
$lang['your_name']						= 'Âàøå èìÿ';
$lang['your_guild']						= 'Âàøà Ãèëüäèÿ';
$lang['your_server']					= 'Âàø ñåðâåð';

//Last Raids
$lang['last_raids']					    = 'Ïîñëåäíèå Ðåéäû';

$lang['voice_error']				    = 'Îòñóòñòâóåò ñîåäèíåíèå ñ ñåðâåðîì';

$lang['login_bridge_notice']		    = 'Login - CMS-Brigde is active. Use your CSM/Board Data to login.';

$lang['ads_remove']		    			= 'support EQdkp-Plus';
$lang['ads_header']	    				= 'Support EQDKP-Plus';
$lang['ads_text']		    			= 'EQDKP-Plus is a hobby-project which was mainly developed and is kept updated by two private persons.
											At the beginning this wasnt a problem but after three years of constant programming and updating,
											the cost for this grows unfortunately over our heads. Only for the developer and the update-server we
											have to spend 600 per year now and there are also another 1000 in costs for an attorney, since there are
											some legal problems at this time. For the future we have also planned many more server-based features which will
											result in another needed server. Costs for our new forum and the designer of our new homepage add to this.
											All these named costs plus our more and more invested working time cannot be paid anymore by ourselves.
											For this reason and not wanting the project to die you will now sparely see ad-banners in EQDKP-Plus.
											These banners are very limited for content, so you will not see any pornographic banners or gold/item-selling vendors.

											You do have options to turn these banners off:
										  <ol>
										  <li> Log on to <a href="http://www.eqdkp-plus.com">www.eqdkp-plus.com</a> and donate any amount you want.
										  		Please think about it, how much is EQDKP-Plus worth for you.
										  		After a donation (Amazon or Paypal) you will get an eMail with a serial-key for the
										  		respective major or major-version..<br><br></li>
										  <li> Log on to <a href="http://www.eqdkp-plus.com">www.eqdkp-plus.com</a> and donate any amount exceeding 50.
										  		You will earn premium status and get a livetime-premium-account, making you eligible for
										  		free upgrades to new major-versions. </li><br><br>
										  <li> Log on to <a href="http://www.eqdkp-plus.com">www.eqdkp-plus.com</a> and donate any amount exceeding 100.
										  		You will earn gold status and get a livetime-premium-account,
										  		making you eligible for free upgrades to new major-versions + free personal
										  		support from the EQDKP-Plus developers.<br><br></li>
										  <li> All developers and translators ever contributed to EQDKP-Plus also get a free serial-key.<br><br></li>
										  <li> Deeply committed beta-testers also get a free serial-key. <br><br></li>
										  </ol>
										 All money generated with ad-banners and donations is solely spent to pay the costs coming up with the EQDKP-Plus project.
										 EQDKP-Plus is still a non-profit project! You dont have a Paypal or Amazon Account or have trouble with you key? Write me a <a href=mailto:corgan@eqdkp-plus.com>Email</a>.
										  ';


$lang['portalmanager'] = 'Manage Portal Modules';

$lang['air_img_resize_warning'] = 'Click this bar to view the full image. The original is %1$sx%2$s.';

$lang['guild_shop'] = 'Shop';

// LibLoader Language String
$lang['libloader_notfound'] = 'The Library Loader Class is not available. Please check if the folder  "eqdkp/libraries/" is propperly uploaded!<br/> Download: <a href="https://sourceforge.net/project/showfiles.php?group_id=167016&package_id=301378">Libraries Download</a>';
$lang['libloader_tooold']   = "The Library '%1\$s' is outdated. You have to upload Version %2\$s or higher.<br/> Download: <a href='%3\$s' target='blank'>Libraries Download</a><br/>Please download, and overwrite the existing 'eqdkp/libraries/' folder with the one you downloaded!";

$lang['more_plugins']   = "For more Plugins visit <a href=http://www.eqdkp-plus.com/download.php>www.eqdkp-plus.com</a>.";
$lang['more_moduls']   = "For more Modules visit <a href=http://www.eqdkp-plus.com/download.php>www.eqdkp-plus.com</a>.";
$lang['more_template']   = "For more Style visit <a href=http://www.eqdkp-plus.com/download.php>www.eqdkp-plus.com</a>";

// jQuery
$lang['cl_bttn_ok']      = 'Ok';
$lang['cl_bttn_cancel']  = 'Cancel';

// Update Available
$lang['upd_available_head']    = 'System Updated available';
$lang['upd_available_txt']     = 'The System is not up to date. There are updates available.';
$lang['upd_available_link']    = 'Click to show updates.';

$lang['menu_roster'] = 'Roster';

$lang['lib_cache_notwriteable'] = 'The folder "eqdkp/data" is not writable. Please chmod 777!';

//Sticky news
$lang['sticky_news_prefix'] = 'Sticky:';
$lang['news_sticky'] = 'Make it sticky?';

//pdh listmember
$lang['manage_members'] = "Manage members";
$lang['show_hidden_ranks'] = "Show hidden ranks";
$lang['show_inactive'] = "Show inactive";

//Libraries
$lang = array_merge($lang, array(
  
  // JS Short Language
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