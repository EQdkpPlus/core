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

### EQdkp Default
INSERT INTO `__styles` (`style_name`, `style_version`, `style_contact`, `style_author`, `enabled`, `template_path`, `body_background`, `body_link`, `body_link_style`, `body_hlink`, `body_hlink_style`, `header_link`, `header_link_style`, `header_hlink`, `header_hlink_style`, `tr_color1`, `tr_color2`, `th_color1`, `fontface1`, `fontface2`, `fontface3`, `fontsize1`, `fontsize2`, `fontsize3`, `fontcolor1`, `fontcolor2`, `fontcolor3`, `fontcolor_neg`, `fontcolor_pos`, `table_border_width`, `table_border_color`, `table_border_style`, `input_color`, `input_border_width`, `input_border_color`, `input_border_style`, `attendees_columns`, `logo_position`, `background_img`, `css_file`, `use_db_vars`) VALUES
('EQdkp Default', '0.0.1', '', 'GodMod', '1', 'eqdkp_default', '2B577C', 'C5E5FF', 'none', 'EEEEEE', 'none', 'FFFFFF', 'none', 'C3E5FF', 'none', '14293B', '1D3953', '2B577C', 'Verdana, Arial, Helvetica, sans-serif', 'Verdana, Arial, Helvetica, sans-serif', 'Verdana, Arial, Helvetica, sans-serif', 10, 11, 12, 'EEEEEE', 'C3E5FF', '000000', 'FF0000', '008800', 1, '999999', 'solid', 'EEEEEE', 1, '2B577C', 'solid', '6', 'center', '', '', 1);

### Default calendars
INSERT INTO __calendars (id,name,color,private,feed,system, type) VALUES ('1','Raids','00628c','0',NULL,'1', '1');
INSERT INTO __calendars (id,name,color,private,feed,system, type) VALUES ('2','Standard','ba1e1e','0',NULL,'0', '2');

### Member ranks
INSERT INTO __member_ranks (rank_id, rank_name) VALUES ('0', '');
INSERT INTO __member_ranks (rank_id, rank_name) VALUES ('1', 'Member');

## Links
INSERT INTO __links (link_id, link_url, link_name, link_window, link_menu) VALUES (1, 'http://www.eqdkp-plus.eu', 'EQDKP-Plus', '1', 3);

#multidkp-pool
INSERT INTO __multidkp (multidkp_id, multidkp_name, multidkp_desc) VALUES ('1', 'def', 'Default-Pool');

#itempools
INSERT INTO __itempool (itempool_name, itempool_desc) VALUES ('default', 'Default itempool');

#multidkp2itempool
INSERT INTO __multidkp2itempool (multidkp2itempool_itempool_id, multidkp2itempool_multi_id) VALUES (1, 1);

#newscategories
INSERT INTO __news_categories (category_id, category_name) VALUES (1, 'Default');