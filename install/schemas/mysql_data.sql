### Permission Options
### A = Admin / U = User
INSERT INTO __auth_options (auth_value, auth_default) VALUES ('a_event_add','N');
INSERT INTO __auth_options (auth_value, auth_default) VALUES ('a_event_upd','N');
INSERT INTO __auth_options (auth_value, auth_default) VALUES ('a_event_del','N');
INSERT INTO __auth_options (auth_value, auth_default) VALUES ('a_indivadj_add','N');
INSERT INTO __auth_options (auth_value, auth_default) VALUES ('a_indivadj_upd','N');
INSERT INTO __auth_options (auth_value, auth_default) VALUES ('a_indivadj_del','N');
INSERT INTO __auth_options (auth_value, auth_default) VALUES ('a_item_add','N');
INSERT INTO __auth_options (auth_value, auth_default) VALUES ('a_item_upd','N');
INSERT INTO __auth_options (auth_value, auth_default) VALUES ('a_item_del','N');
INSERT INTO __auth_options (auth_value, auth_default) VALUES ('a_news_add','N');
INSERT INTO __auth_options (auth_value, auth_default) VALUES ('a_news_upd','N');
INSERT INTO __auth_options (auth_value, auth_default) VALUES ('a_news_del','N');
INSERT INTO __auth_options (auth_value, auth_default) VALUES ('a_raid_add','N');
INSERT INTO __auth_options (auth_value, auth_default) VALUES ('a_raid_upd','N');
INSERT INTO __auth_options (auth_value, auth_default) VALUES ('a_raid_del','N');
INSERT INTO __auth_options (auth_value, auth_default) VALUES ('a_config_man','N');
INSERT INTO __auth_options (auth_value, auth_default) VALUES ('a_members_man','N');
INSERT INTO __auth_options (auth_value, auth_default) VALUES ('a_users_man','N');
INSERT INTO __auth_options (auth_value, auth_default) VALUES ('a_logs_view','N');
INSERT INTO __auth_options (auth_value, auth_default) VALUES ('a_logs_del','N');
INSERT INTO __auth_options (auth_value, auth_default) VALUES ('a_extensions_man','N');
INSERT INTO __auth_options (auth_value, auth_default) VALUES ('a_backup','N');
INSERT INTO __auth_options (auth_value, auth_default) VALUES ('a_reset','N');
INSERT INTO __auth_options (auth_value, auth_default) VALUES ('a_maintenance','N');
INSERT INTO __auth_options (auth_value, auth_default) VALUES ('a_pages_man','N');
INSERT INTO __auth_options (auth_value, auth_default) VALUES ('a_sms_send','N');
INSERT INTO __auth_options (auth_value, auth_default) VALUES ('a_files_man','N');
INSERT INTO __auth_options (auth_value, auth_default) VALUES ('a_cal_event_man','N');
INSERT INTO __auth_options (auth_value, auth_default) VALUES ('a_calendars_man','N');
INSERT INTO __auth_options (auth_value, auth_default) VALUES ('a_cal_revent_conf','N');
INSERT INTO __auth_options (auth_value, auth_default) VALUES ('a_users_massmail','N');
INSERT INTO __auth_options (auth_value, auth_default) VALUES ('a_usergroups_man','N');
INSERT INTO __auth_options (auth_value, auth_default) VALUES ('a_usergroups_grpleader','N');

INSERT INTO __auth_options (auth_value, auth_default) VALUES ('u_event_view','Y');
INSERT INTO __auth_options (auth_value, auth_default) VALUES ('u_item_view','Y');
INSERT INTO __auth_options (auth_value, auth_default) VALUES ('u_member_view','Y');
INSERT INTO __auth_options (auth_value, auth_default) VALUES ('u_raid_view','Y');
INSERT INTO __auth_options (auth_value, auth_default) VALUES ('u_member_man','Y');
INSERT INTO __auth_options (auth_value, auth_default) VALUES ('u_member_add','Y');
INSERT INTO __auth_options (auth_value, auth_default) VALUES ('u_member_conn','Y');
INSERT INTO __auth_options (auth_value, auth_default) VALUES ('u_member_del','Y');
INSERT INTO __auth_options (auth_value, auth_default) VALUES ('u_userlist','N');
INSERT INTO __auth_options (auth_value, auth_default) VALUES ('u_cal_event_add','Y');
INSERT INTO __auth_options (auth_value, auth_default) VALUES ('u_calendar_view','Y');
INSERT INTO __auth_options (auth_value, auth_default) VALUES ('u_news_view','Y');
INSERT INTO __auth_options (auth_value, auth_default) VALUES ('u_search','Y');
INSERT INTO __auth_options (auth_value, auth_default) VALUES ('u_roster_list','Y');
INSERT INTO __auth_options (auth_value, auth_default) VALUES ('u_usermailer','Y');


### Style presets

### EQdkp modern style
INSERT INTO `__styles` (`style_id`, `style_name`, `style_version`, `style_contact`, `style_author`, `enabled`, `template_path`, `body_background`, `body_link`, `body_link_style`, `body_hlink`, `body_hlink_style`, `header_link`, `header_link_style`, `header_hlink`, `header_hlink_style`, `tr_color1`, `tr_color2`, `th_color1`, `fontface1`, `fontface2`, `fontface3`, `fontsize1`, `fontsize2`, `fontsize3`, `fontcolor1`, `fontcolor2`, `fontcolor3`, `fontcolor_neg`, `fontcolor_pos`, `table_border_width`, `table_border_color`, `table_border_style`, `input_color`, `input_border_width`, `input_border_color`, `input_border_style`, `attendees_columns`, `logo_position`, `background_img`, `css_file`, `use_db_vars`, `column_right_width`, `column_left_width`, `portal_width`) VALUES (1, 'EQdkp Modern', '0.0.1', '', 'GodMod', '1', 'eqdkp_modern', '#2B577C', '#C5E5FF', 'none', '#EEEEEE', 'none', '#FFFFFF', 'none', '#C3E5FF', 'none', '#14293B', '#1D3953', '#2B577C', 'Verdana, Arial, Helvetica, sans-serif', 'Verdana, Arial, Helvetica, sans-serif', 'Verdana, Arial, Helvetica, sans-serif', 10, 11, 12, '#EEEEEE', '#C3E5FF', '#000000', '#FF0000', '#008800', 1, '#999999', 'solid', '#EEEEEE', 1, '#2B577C', 'solid', '6', 'left', '', '', 1, '0px', '0px', '0px');

### Default calendars
INSERT INTO __calendars (id,name,color,private,feed,system, type, restricted) VALUES ('1','Raids','#00628c','0',NULL,'1', '1', '1');
INSERT INTO __calendars (id,name,color,private,feed,system, type, restricted) VALUES ('2','Userraids','#0cb20f','0',NULL,'1', '1', '0');
INSERT INTO __calendars (id,name,color,private,feed,system, type, restricted) VALUES ('3','Standard','#ba1e1e','0',NULL,'0', '2', '0');

### Member ranks
INSERT INTO __member_ranks (rank_id, rank_name) VALUES ('0', '');
INSERT INTO __member_ranks (rank_id, rank_name) VALUES ('1', 'Member');

## Links
INSERT INTO `__links` (`link_id`, `link_url`, `link_name`, `link_window`, `link_menu`, `link_sortid`, `link_visibility`, `link_height`) VALUES (1, '#', 'Guild', '0', 0, 0, 0, 4024);
INSERT INTO `__links` (`link_id`, `link_url`, `link_name`, `link_window`, `link_menu`, `link_sortid`, `link_visibility`, `link_height`) VALUES (2, '#', 'Links', '0', 0, 0, 0, 4024);
INSERT INTO `__links` (`link_id`, `link_url`, `link_name`, `link_window`, `link_menu`, `link_sortid`, `link_visibility`, `link_height`) VALUES (3, 'http://eqdkp-plus.eu', 'EQdkp-Plus', '1', 0, 0, 0, 4024);
INSERT INTO `__links` (`link_id`, `link_url`, `link_name`, `link_window`, `link_menu`, `link_sortid`, `link_visibility`, `link_height`) VALUES (4, '#', 'DKP-System', '0', 0, 0, 0, 4024);


#multidkp-pool
INSERT INTO __multidkp (multidkp_id, multidkp_name, multidkp_desc) VALUES ('1', 'def', 'Default-Pool');

#itempools
INSERT INTO __itempool (itempool_name, itempool_desc) VALUES ('default', 'Default itempool');

#multidkp2itempool
INSERT INTO __multidkp2itempool (multidkp2itempool_itempool_id, multidkp2itempool_multi_id) VALUES (1, 1);

#newscategories
INSERT INTO __news_categories (category_id, category_name) VALUES (1, 'Default');