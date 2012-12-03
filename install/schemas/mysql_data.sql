### Permission Options
### A = Admin / U = User
INSERT INTO eqdkp_auth_options (auth_value, auth_default) VALUES ('a_event_add','N');
INSERT INTO eqdkp_auth_options (auth_value, auth_default) VALUES ('a_event_upd','N');
INSERT INTO eqdkp_auth_options (auth_value, auth_default) VALUES ('a_event_del','N');
INSERT INTO eqdkp_auth_options (auth_value, auth_default) VALUES ('a_indivadj_add','N');
INSERT INTO eqdkp_auth_options (auth_value, auth_default) VALUES ('a_indivadj_upd','N');
INSERT INTO eqdkp_auth_options (auth_value, auth_default) VALUES ('a_indivadj_del','N');
INSERT INTO eqdkp_auth_options (auth_value, auth_default) VALUES ('a_item_add','N');
INSERT INTO eqdkp_auth_options (auth_value, auth_default) VALUES ('a_item_upd','N');
INSERT INTO eqdkp_auth_options (auth_value, auth_default) VALUES ('a_item_del','N');
INSERT INTO eqdkp_auth_options (auth_value, auth_default) VALUES ('a_news_add','N');
INSERT INTO eqdkp_auth_options (auth_value, auth_default) VALUES ('a_news_upd','N');
INSERT INTO eqdkp_auth_options (auth_value, auth_default) VALUES ('a_news_del','N');
INSERT INTO eqdkp_auth_options (auth_value, auth_default) VALUES ('a_raid_add','N');
INSERT INTO eqdkp_auth_options (auth_value, auth_default) VALUES ('a_raid_upd','N');
INSERT INTO eqdkp_auth_options (auth_value, auth_default) VALUES ('a_raid_del','N');
INSERT INTO eqdkp_auth_options (auth_value, auth_default) VALUES ('a_config_man','N');
INSERT INTO eqdkp_auth_options (auth_value, auth_default) VALUES ('a_members_man','N');
INSERT INTO eqdkp_auth_options (auth_value, auth_default) VALUES ('a_users_man','N');
INSERT INTO eqdkp_auth_options (auth_value, auth_default) VALUES ('a_users_comment_w','N');
INSERT INTO eqdkp_auth_options (auth_value, auth_default) VALUES ('a_logs_view','N');
INSERT INTO eqdkp_auth_options (auth_value, auth_default) VALUES ('a_logs_del','N');
INSERT INTO eqdkp_auth_options (auth_value, auth_default) VALUES ('a_plugins_man','N');
INSERT INTO eqdkp_auth_options (auth_value, auth_default) VALUES ('a_styles_man','N');
INSERT INTO eqdkp_auth_options (auth_value, auth_default) VALUES ('a_backup','N');
INSERT INTO eqdkp_auth_options (auth_value, auth_default) VALUES ('a_reset','N');
INSERT INTO eqdkp_auth_options (auth_value, auth_default) VALUES ('a_maintenance','N');
INSERT INTO eqdkp_auth_options (auth_value, auth_default) VALUES ('a_infopages_man','N');
INSERT INTO eqdkp_auth_options (auth_value, auth_default) VALUES ('a_sms_send','N');
INSERT INTO eqdkp_auth_options (auth_value, auth_default) VALUES ('a_files_man','N');

INSERT INTO eqdkp_auth_options (auth_value, auth_default) VALUES ('u_event_list','Y');
INSERT INTO eqdkp_auth_options (auth_value, auth_default) VALUES ('u_event_view','Y');
INSERT INTO eqdkp_auth_options (auth_value, auth_default) VALUES ('u_item_list','Y');
INSERT INTO eqdkp_auth_options (auth_value, auth_default) VALUES ('u_item_view','Y');
INSERT INTO eqdkp_auth_options (auth_value, auth_default) VALUES ('u_member_list','Y');
INSERT INTO eqdkp_auth_options (auth_value, auth_default) VALUES ('u_member_view','Y');
INSERT INTO eqdkp_auth_options (auth_value, auth_default) VALUES ('u_raid_list','Y');
INSERT INTO eqdkp_auth_options (auth_value, auth_default) VALUES ('u_raid_view','Y');
INSERT INTO eqdkp_auth_options (auth_value, auth_default) VALUES ('u_member_man','Y');
INSERT INTO eqdkp_auth_options (auth_value, auth_default) VALUES ('u_member_add','Y');
INSERT INTO eqdkp_auth_options (auth_value, auth_default) VALUES ('u_member_conn','Y');
INSERT INTO eqdkp_auth_options (auth_value, auth_default) VALUES ('u_member_del','Y');
INSERT INTO eqdkp_auth_options (auth_value, auth_default) VALUES ('u_userlist','N');
INSERT INTO eqdkp_auth_options (auth_value, auth_default) VALUES ('u_infopages_view','Y');
INSERT INTO eqdkp_auth_options (auth_value, auth_default) VALUES ('u_users_comment_r','N');


### Style presets

### Luna WotLK
INSERT INTO `eqdkp_styles` (`style_name`, `style_code`, `style_version`, `style_contact`, `style_author`, `enabled`, `template_path`, `body_background`, `body_link`, `body_link_style`, `body_hlink`, `body_hlink_style`, `header_link`, `header_link_style`, `header_hlink`, `header_hlink_style`, `tr_color1`, `tr_color2`, `th_color1`, `fontface1`, `fontface2`, `fontface3`, `fontsize1`, `fontsize2`, `fontsize3`, `fontcolor1`, `fontcolor2`, `fontcolor3`, `fontcolor_neg`, `fontcolor_pos`, `table_border_width`, `table_border_color`, `table_border_style`, `input_color`, `input_border_width`, `input_border_color`, `input_border_style`, `attendees_columns`, `logo_path`, `background_img`, `css_file`, `use_db_vars`) VALUES 
('Luna WotLK', 'luna_wotlk', '1.0.0', '', 'Lunary', '1', 'luna_wotlk', '2B577C', 'C5E5FF', 'none', 'EEEEEE', 'none', 'FFFFFF', 'none', 'C3E5FF', 'none', '14293B', '1D3953', '2B577C', 'Verdana, Arial, Helvetica, sans-serif', 'Verdana, Arial, Helvetica, sans-serif', 'Verdana, Arial, Helvetica, sans-serif', 10, 11, 12, 'EEEEEE', 'C3E5FF', '000000', 'FF0000', '008800', 1, '999999', 'solid', 'EEEEEE', 1, '2B577C', 'solid', '6', 'logo.png', '', '', 1);

### WoW Moonclaw01 Vertical
INSERT INTO `eqdkp_styles` (`style_name`, `style_code`, `style_version`, `style_contact`, `style_author`, `enabled`, `template_path`, `body_background`, `body_link`, `body_link_style`, `body_hlink`, `body_hlink_style`, `header_link`, `header_link_style`, `header_hlink`, `header_hlink_style`, `tr_color1`, `tr_color2`, `th_color1`, `fontface1`, `fontface2`, `fontface3`, `fontsize1`, `fontsize2`, `fontsize3`, `fontcolor1`, `fontcolor2`, `fontcolor3`, `fontcolor_neg`, `fontcolor_pos`, `table_border_width`, `table_border_color`, `table_border_style`, `input_color`, `input_border_width`, `input_border_color`, `input_border_style`, `attendees_columns`, `logo_path`, `background_img`, `css_file`, `use_db_vars`) VALUES 
('WoW Moonclaw01 Vertical', 'WoWMoonclaw01V', '1.0.0', '', 'EQdkp Plus', '1', 'WoWMoonclaw01V', '000000', 'CBA300', '0', 'E98219', '0', 'FFDD33', '0', 'E98219', '0', '454545', '202020', '020725', 'Verdana, Tahoma, Arial', 'Verdana, Tahoma, Arial', 'Verdana, Tahoma, Arial', 10, 11, 12, 'FFFFFF', '007799', 'FFFFFF', 'F80000', '008800', 0, '1', '0', '000000', 1, '202020', '0', '6', 'bc_header3.gif', '', '', 1);

### WoW MaevahEmpire Vertical
INSERT INTO `eqdkp_styles` (`style_name`, `style_code`, `style_version`, `style_contact`, `style_author`, `enabled`, `template_path`, `body_background`, `body_link`, `body_link_style`, `body_hlink`, `body_hlink_style`, `header_link`, `header_link_style`, `header_hlink`, `header_hlink_style`, `tr_color1`, `tr_color2`, `th_color1`, `fontface1`, `fontface2`, `fontface3`, `fontsize1`, `fontsize2`, `fontsize3`, `fontcolor1`, `fontcolor2`, `fontcolor3`, `fontcolor_neg`, `fontcolor_pos`, `table_border_width`, `table_border_color`, `table_border_style`, `input_color`, `input_border_width`, `input_border_color`, `input_border_style`, `attendees_columns`, `logo_path`, `background_img`, `css_file`, `use_db_vars`) VALUES 
('WoW MaevahEmpire Vertical', 'WoWMaevahEmpireV', '1.0.0', '', 'EQdkp Plus', '1', 'WoWMaevahEmpireV', '000000', 'CBA300', '0', 'E98219', '0', 'FFDD33', '0', 'E98219', '0', '454545', '202020', '020725', 'Verdana, Tahoma, Arial', 'Verdana, Tahoma, Arial', 'Verdana, Tahoma, Arial', 10, 11, 12, 'FFFFFF', '007799', 'FFFFFF', 'F80000', '008800', 0, '1', '0', '000000', 1, '202020', '0', '6', 'bc_header3.gif', '', '', 1);

### m9wotlk
INSERT INTO `eqdkp_styles` (`style_name`, `style_code`, `style_version`, `style_contact`, `style_author`, `enabled`, `template_path`, `body_background`, `body_link`, `body_link_style`, `body_hlink`, `body_hlink_style`, `header_link`, `header_link_style`, `header_hlink`, `header_hlink_style`, `tr_color1`, `tr_color2`, `th_color1`, `fontface1`, `fontface2`, `fontface3`, `fontsize1`, `fontsize2`, `fontsize3`, `fontcolor1`, `fontcolor2`, `fontcolor3`, `fontcolor_neg`, `fontcolor_pos`, `table_border_width`, `table_border_color`, `table_border_style`, `input_color`, `input_border_width`, `input_border_color`, `input_border_style`, `attendees_columns`, `logo_path`, `background_img`, `css_file`, `use_db_vars`) VALUES 
('m9wotlk', 'm9wotlk', '1.0.0', '', 'Cattiebrie', '1', 'm9wotlk', '000000', 'ffffff', '0', 'C60000', '0', 'ffffff', '0', 'C60000', '0', '252525', '161616', '', 'Verdana, Tahoma, Arial', 'Verdana, Tahoma, Arial', 'Verdana, Tahoma, Arial', 10, 11, 12, 'ffffff', 'CBA300', 'ffffff', 'F80000', '008800', 1, 'ffffff', '0', 'D1D1D1', 1, '000000', '0', '6', 'logo.gif', '', '', 1);


### Member ranks
INSERT INTO eqdkp_member_ranks (rank_id, rank_name) VALUES ('0', '');
INSERT INTO eqdkp_member_ranks (rank_id, rank_name) VALUES ('1', 'Member');


INSERT INTO eqdkp_plus_links (link_id, link_url, link_name, link_window, link_menu) VALUES (1, 'http://www.eqdkp-plus.com', 'EQDKP-Plus', '1', 3);
INSERT INTO eqdkp_plus_links (link_id, link_url, link_name, link_window, link_menu) VALUES (2, 'http://allvatar.com/', 'Allvatar', '1', 3);

INSERT INTO eqdkp_portal (id, name, enabled, settings, path, contact, url, autor, version, position, number, plugin, visibility, collapsable) VALUES(1, 'DKPInfo Module', '1', '0', 'dkpinfo', 'corgan@eqdkp-plus.com', NULL, NULL, '1.0.0', 'left2', 0, '', '0', '1');
INSERT INTO eqdkp_portal (id, name, enabled, settings, path, contact, url, autor, version, position, number, plugin, visibility, collapsable) VALUES(3, 'LastItems Module', '1', '1', 'lastitems', 'corgan@eqdkp-plus.com', NULL, NULL, '1.0.0', 'right', 2, '', '0', '1');
INSERT INTO eqdkp_portal (id, name, enabled, settings, path, contact, url, autor, version, position, number, plugin, visibility, collapsable) VALUES(4, 'LastRaids Module', '1', '1', 'lastraids', 'corgan@eqdkp-plus.com', NULL, NULL, '1.0.0', 'right', 3, '', '0', '1');
INSERT INTO eqdkp_portal (id, name, enabled, settings, path, contact, url, autor, version, position, number, plugin, visibility, collapsable) VALUES(5, 'QuickDKP Module', '1', '0', 'quickdkp', 'corgan@eqdkp-plus.com', NULL, NULL, '1.0.0', 'left1', 2, '', '0', '1');
INSERT INTO eqdkp_portal (id, name, enabled, settings, path, contact, url, autor, version, position, number, plugin, visibility, collapsable) VALUES(6, 'RankImage Module', '1', '1', 'rankimage', 'corgan@eqdkp-plus.com', NULL, NULL, '1.0.0', 'right', 4, '', '0', '1');
INSERT INTO eqdkp_portal (id, name, enabled, settings, path, contact, url, autor, version, position, number, plugin, visibility, collapsable) VALUES(7, 'Recruitment Module', '0', '0', 'recruitment', 'corgan@eqdkp-plus.com', NULL, NULL, '1.0.0', 'left1', 1, '', '0', '1');
INSERT INTO eqdkp_portal (id, name, enabled, settings, path, contact, url, autor, version, position, number, plugin, visibility, collapsable) VALUES(8, 'Teamspeak Module', '0', '1', 'teamspeak', 'corgan@eqdkp-plus.com', NULL, NULL, '1.0.0', 'right', 5, '', '0', '1');
INSERT INTO eqdkp_portal (id, name, enabled, settings, path, contact, url, autor, version, position, number, plugin, visibility, collapsable) VALUES(10, 'Advertising Modul', '0', '1', 'advertising', NULL, NULL, NULL, '', 'left2', 10, '', '0', '0');
INSERT INTO eqdkp_portal (id, name, enabled, settings, path, contact, url, autor, version, position, number, plugin, visibility, collapsable) VALUES(11, 'Bossguides', '1', '0', 'bossguides', NULL, NULL, NULL, '', 'right', 5, '', '0', '1');
INSERT INTO eqdkp_portal (id, name, enabled, settings, path, contact, url, autor, version, position, number, plugin, visibility, collapsable) VALUES(12, 'Realmstatus Module', '0', '1', 'realmstatus', NULL, NULL, NULL, '', 'right', 5, '', '0', '1');
INSERT INTO eqdkp_portal (id, name, enabled, settings, path, contact, url, autor, version, position, number, plugin, visibility, collapsable) VALUES(13, 'Online Module', '1', '1', 'whoisonline', NULL, NULL, NULL, '', 'right', 2, '', '0', '1');
INSERT INTO eqdkp_portal (id, name, enabled, settings, path, contact, url, autor, version, position, number, plugin, visibility, collapsable) VALUES(14, 'Latest Forum Posts', '0', '1', 'latestposts', NULL, NULL, NULL, '', 'middle', 1, '', '0', '1');
INSERT INTO eqdkp_portal (id, name, enabled, settings, path, contact, url, autor, version, position, number, plugin, visibility, collapsable) VALUES(15, 'Custom Content Module', '0', '1', 'mycontent', NULL, NULL, NULL, '', 'right', 7, '', '0', '1');
INSERT INTO eqdkp_portal (id, name, enabled, settings, path, contact, url, autor, version, position, number, plugin, visibility, collapsable) VALUES(16, 'Ventrilo Status', '0', '1', 'ventrilo', NULL, NULL, NULL, '', 'left1', 1, '', '0', '1');
INSERT INTO eqdkp_portal (id, name, enabled, settings, path, contact, url, autor, version, position, number, plugin, visibility, collapsable) VALUES(17, 'Quick Login', '1', '0', 'quicklogin', NULL, NULL, NULL, '', 'left2', 2, '', '0', '1');
INSERT INTO eqdkp_portal (id, name, enabled, settings, path, contact, url, autor, version, position, number, plugin, visibility, collapsable) VALUES(18, 'QuickSearch', '1', '1', 'quicksearch', NULL, NULL, NULL, '', 'left2', 4, '', '0', '1');
INSERT INTO eqdkp_portal (id, name, enabled, settings, path, contact, url, autor, version, position, number, plugin, visibility, collapsable) VALUES(19, 'Quicklinks & RSS', '1', '0', 'rsslinks', NULL, NULL, NULL, '', 'left2', 9, '', '0', '1');

### Portal-Modules that are neccessary for the Core:
INSERT INTO eqdkp_portal (name, enabled, settings, path, contact, url, autor, version, position, number, plugin, visibility, collapsable) VALUES('MMO-News', '1', '1', 'mmo_news', 'corgan@eqdkp-plus.com', NULL, NULL, '1.0.0', 'left2', 0, '', '0', '0');

INSERT INTO eqdkp_portal (name, enabled, settings, path, contact, url, autor, version, position, number, plugin, visibility, collapsable) VALUES('InfoPages Module', '0', '1', 'infopages', 'godmod@eqdkp-plus.com', NULL, NULL, '1.0.0', 'right', 0, '', '0', '1');

#itempools
INSERT INTO eqdkp_itempool (itempool_name, itempool_desc) VALUES ('default', 'Default itempool');

#newscategories
INSERT INTO eqdkp_news_categories (category_id, category_name) VALUES (1, 'Default');