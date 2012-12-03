<?php
/******************************
 * EQdkp RaidBanker Plugin
 * Copyright 2005 - 2006
 * Licensed under the GNU GPL.  See COPYING for full terms.
 * ------------------
 * lang_main.php
 ******************************/

// General Shit
$lang['raidbanker'] 						      = "Raid Banker";
$lang['raidbanker_title'] 			      = "Raid Banker";
$lang['rb_date_format']               = "%A, %d-%m-%Y, %H:%M";
$lang['rb_local_format']              = "en_US";

// Buttons
$lang['rb_import']							      = "Import";
$lang['rb_add']                       = "Add";
$lang['rb_edit']							        = "Edit";
$lang['rb_delete']							      = "Delete";
$lang['rb_view']                      = "View";
$lang['rb_config']                    = "Settings";
$lang['lang_couldnt_info']            = "Could not obtain item information";
$lang['lang_couldnt_char']						= "Could not obtain Character information";
$lang['rb_close']                     = "Close";

// User Menu
$lang['rb_usermenu_raidbanker']				= "Raid Banker";

// Admin Menu
$lang['rb_adminmenu_raidbanker']			= "Raid Banker";
$lang["rb_step1_pagetitle"]					  = "Raid Banker - Import Log";
$lang["rb_step1_th"]						      = "Paste Raid Banker log below";
$lang["rb_step1_button_parselog"]			= "Parse Log";
$lang["rb_step2_pagetitle"]					  = "Raid Banker - Parsed Log";
$lang["rb_edit_pagetitle"]					  = "Raid Banker - Edit Banker";

// output
$lang['rb_Bank_Items']                = "Banked Items";
$lang['rb_Banker']                    = "All Bankers";
$lang['rb_all_Banker']                = "All Bankers";
$lang['rb_not_avail']                 = "N/A";
$lang['rb_Item_Name']                 = "Item";
$lang['rb_Bank_Type']                 = "Type";
$lang['rb_Bank_QTY']                  = "Qty";
$lang['rb_Bank_Quality']              = "Quality";
$lang['rb_Update']                    = "Last Updated";
$lang['rb_AllBankers']                = "All Bankers";
$lang['rb_TotBankers']                = "Total bank holdings";
$lang['rb_mainchar_out']              = "Mainchar";
$lang['rb_note_out']                  = "Note";

//import
$lang['Character_Data']               = "Character Data";
$lang['lang_with']                    = "with";
$lang['lang_g']                       = "g";
$lang['lang_s']                       = "s";
$lang['lang_c']                       = "c";
$lang['lang_gold']                    = "gold";
$lang['lang_silver']                  = "silver";
$lang['lang_copper']                  = "copper";
$lang['lang_amount']                  = "amount";
$lang['lang_name']                    = "name";
$lang['lang_itemid']                  = "item ID";
$lang['lang_quality']                 = "quality";
$lang['lang_skip']                    = "skip";
$lang['lang_update_data']             = "Update Bank Data";
$lang['lang_found_log']               = "Items found in log";
$lang['lang_skipped_items']           = "<b>skipped</b> item";
$lang['lang_cleared_data']            = "cleared all data for";
$lang['lang_added_data']              = "added char data for";
$lang['lang_adding_item']             = "adding item";
$lang['lang_deleting_item']           = "deleting item";
$lang['rb_add_item']                  = "Add Item";
$lang['rb_insert']                    = "Insert Item Data";
$lang['rb_insert_banker']             = "Insert Banker";
$lang['rb_add_banker_l']              = "Add Banker";
$lang['rb_money_val']                 = "Cost: Money";
$lang['rb_dkp_val']                   = "Cost: DKP";
$lang['rb_mainchar']                  = "Mainchar Name";
$lang['rb_note']                      = "Note for this banker";

// Result page
$lang['rb_user_link']                 = "Back to previous page";
$lang['Lang_actions_performed']       = "Actions Performed";

// acl shit
$lang['rb_add_acl']                   = "Add Item Relation";
$lang['rb_acl_action']                = "Action";
$lang['rb_ac_spent']                  = "Donated";
$lang['rb_ac_got']                    = "Received";
$lang['rb_item_name']                 = "Item name";
$lang['rb_acl_save']                  = "Save Item Relations";
$lang['rb_list_acl']                  = "List Item Relation Data";
$lang['rb_char_name']                 = "Character Name";
$lang['rb_id']                        = "ID";
$lang['rb_acl']                       = "Item Relations";
$lang['rb_banker']                    = "Banker";
$lang['rb_char_data']                 = "Character Data";
$lang['itemcost_money']               = "Itemcost (Money)";
$lang['itemcost_dkp']                 = "Itemcost (DKP)";
$lang['rb_adjust_reason']             = "received from RaidBank";

// Log things
$lang['action_rbacl_added']           = "Item Relation added";
$lang['action_rbacl_del']             = "Item Relation deleted";
$lang['action_rb_imported']           = "RaidBanker Log imported";
$lang['action_rbbank_del']            = "Banker deleted";

// Proprity
$lang['rb_priority']                  = "Priority";
$lang['rb_prio_4']                    = "very high";
$lang['rb_prio_3']                    = "high";
$lang['rb_prio_2']                    = "medium";
$lang['rb_prio_1']                    = "small";
$lang['rb_prio_0']                    = "no";

//edit
$lang['admin_delete_bank_success']    = "Banker successfully deleted";

// configuration
$lang['rb_header_global']             = "RaidBanker Settings";
$lang['rb_use_itemstats']             = "Use Itemstats";
$lang['rb_hide_banker']               = "Hide other Bankers (after selecting one)";
$lang['rb_hide_money']                = "Show Bank Holding";
$lang['rb_no_banker']                 = "Merge all Bankers into one";
$lang['rb_is_cache']                  = "Itemstats Cache: if true, items will be loaded on click @item.";
$lang['rb_is_path']                   = "Path to Itemstats";
$lang['rb_saved']                     = "The settings were succesfully saved";
$lang['rb_failed']                    = "The settings failed being saved";
$lang['rb_info_box']                  = "Information";
$lang['rb_list_lang']                 = "Item Language";
$lang['rb_locale_de']                 = "German";
$lang['rb_locale_en']                 = "English";
$lang['rb_show_tooltip']              = "Show Information Tooltips<br />(longer executon times!)";
$lang['rb_auto_adjust']               = "Automatic DKP Adjustments on item-receive";
$lang['rb_is_oldstyle']								= "OldStyle Layout: Show every Banker's item (do not group by Item Name)";
  
//filter translations
$lang['rb_filter_banker']             = "Choose banker";
$lang['rb_filter_type']               = "Choose item type";
$lang['rb_filter_prio']               = "Choose priority";

// View Item PopUP
$lang['rb_char_got']                  = "Item bought by";
$lang['rb_char_spent']                = "Item donated by";
$lang['rb_gold_value']                = "Costs in Money";
$lang['rb_dkp_value']                 = "Costs in DKP";
$lang['rb_total_amount']              = "Total amount";
$lang['rb_dkp']                       = "DKP";

// About dialog
$lang['rb_created by']              = "Created by";
$lang['rb_contact_info']            = "Contact Information";
$lang['rb_url_personal']            = "Privat";
$lang['rb_url_web']                 = "Web";
$lang['rb_sponsors']                = "Donators";
$lang['rb_dialog_header']						= "About RaidBanker";
$lang['rb_additions']               = "Submissions";
$lang['rb_loading']                 = "Loading";

// Update Checker Part
$lang['rb_changelog_url']							= 'Changelog';
$lang['rb_updated_date']							= 'Released at';
$lang['rb_timeformat']								= 'm/d/Y';
$lang['rb_release_level']							= 'release';
$lang['rb_noserver']                  = 'An error ocurred while trying to contact the update server, either your host do not allow outbound connections
                                          or the error was caused by a network problem.
                                          Please visit the eqdkp-plugin-forum to make sure you are running the latest plugin version.';
$lang['rb_update_available_p1']       = 'Please update the Raidbanker Plugin.';
$lang['rb_update_available_p2']       = 'Your current version is';
$lang['rb_update_available_p3']       = 'and the latest version is';
$lang['rb_update_url']                = 'To the Download Page';
?>