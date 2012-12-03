### Configuration values
INSERT INTO eqdkp_config (config_name, config_value) VALUES ('default_lang', 'english');
INSERT INTO eqdkp_config (config_name, config_value) VALUES ('default_game', 'WoW');
INSERT INTO eqdkp_config (config_name, config_value) VALUES ('game_language', 'de');
INSERT INTO eqdkp_config (config_name, config_value) VALUES ('default_style', '36');
INSERT INTO eqdkp_config (config_name, config_value) VALUES ('default_alimit', '100');
INSERT INTO eqdkp_config (config_name, config_value) VALUES ('default_elimit', '100');
INSERT INTO eqdkp_config (config_name, config_value) VALUES ('default_ilimit', '100');
INSERT INTO eqdkp_config (config_name, config_value) VALUES ('default_nlimit', '10');
INSERT INTO eqdkp_config (config_name, config_value) VALUES ('default_rlimit', '100');
INSERT INTO eqdkp_config (config_name, config_value) VALUES ('guildtag', 'My Guild');
INSERT INTO eqdkp_config (config_name, config_value) VALUES ('parsetags', '');
INSERT INTO eqdkp_config (config_name, config_value) VALUES ('dkp_name', 'DKP');
INSERT INTO eqdkp_config (config_name, config_value) VALUES ('hide_inactive', '0');
INSERT INTO eqdkp_config (config_name, config_value) VALUES ('inactive_period', '99');
INSERT INTO eqdkp_config (config_name, config_value) VALUES ('active_point_adj', '0.00');
INSERT INTO eqdkp_config (config_name, config_value) VALUES ('inactive_point_adj', '0.00');
INSERT INTO eqdkp_config (config_name, config_value) VALUES ('main_title', '');
INSERT INTO eqdkp_config (config_name, config_value) VALUES ('sub_title', '');
INSERT INTO eqdkp_config (config_name, config_value) VALUES ('start_page', 'viewnews.php');
INSERT INTO eqdkp_config (config_name, config_value) VALUES ('cookie_domain', '');
INSERT INTO eqdkp_config (config_name, config_value) VALUES ('cookie_name', 'eqdkp');
INSERT INTO eqdkp_config (config_name, config_value) VALUES ('cookie_path', '/');
INSERT INTO eqdkp_config (config_name, config_value) VALUES ('session_length', '3600');
INSERT INTO eqdkp_config (config_name, config_value) VALUES ('session_cleanup', '0');
INSERT INTO eqdkp_config (config_name, config_value) VALUES ('session_last_cleanup', '0');
INSERT INTO eqdkp_config (config_name, config_value) VALUES ('server_name', '');
INSERT INTO eqdkp_config (config_name, config_value) VALUES ('server_path', '/eqdkp/');
INSERT INTO eqdkp_config (config_name, config_value) VALUES ('server_port', '80');
INSERT INTO eqdkp_config (config_name, config_value) VALUES ('enable_gzip', '0');
INSERT INTO eqdkp_config (config_name, config_value) VALUES ('admin_email', '');
INSERT INTO eqdkp_config (config_name, config_value) VALUES ('account_activation', '1');
INSERT INTO eqdkp_config (config_name, config_value) VALUES ('eqdkp_start', UNIX_TIMESTAMP());
INSERT INTO eqdkp_config (config_name, config_value) VALUES ('default_locale', 'en_US');
INSERT INTO eqdkp_config (config_name, config_value) VALUES ('plus_version', '0.6.3.0');
INSERT INTO eqdkp_config (config_name, config_value) VALUES ('default_game_overwrite', '0');


### Permission Options
### A = Admin / U = User
INSERT INTO eqdkp_auth_options (auth_id, auth_value, auth_default) VALUES ('1','a_event_add','N');
INSERT INTO eqdkp_auth_options (auth_id, auth_value, auth_default) VALUES ('2','a_event_upd','N');
INSERT INTO eqdkp_auth_options (auth_id, auth_value, auth_default) VALUES ('3','a_event_del','N');
INSERT INTO eqdkp_auth_options (auth_id, auth_value, auth_default) VALUES ('4','a_groupadj_add','N');
INSERT INTO eqdkp_auth_options (auth_id, auth_value, auth_default) VALUES ('5','a_groupadj_upd','N');
INSERT INTO eqdkp_auth_options (auth_id, auth_value, auth_default) VALUES ('6','a_groupadj_del','N');
INSERT INTO eqdkp_auth_options (auth_id, auth_value, auth_default) VALUES ('7','a_indivadj_add','N');
INSERT INTO eqdkp_auth_options (auth_id, auth_value, auth_default) VALUES ('8','a_indivadj_upd','N');
INSERT INTO eqdkp_auth_options (auth_id, auth_value, auth_default) VALUES ('9','a_indivadj_del','N');
INSERT INTO eqdkp_auth_options (auth_id, auth_value, auth_default) VALUES ('10','a_item_add','N');
INSERT INTO eqdkp_auth_options (auth_id, auth_value, auth_default) VALUES ('11','a_item_upd','N');
INSERT INTO eqdkp_auth_options (auth_id, auth_value, auth_default) VALUES ('12','a_item_del','N');
INSERT INTO eqdkp_auth_options (auth_id, auth_value, auth_default) VALUES ('13','a_news_add','N');
INSERT INTO eqdkp_auth_options (auth_id, auth_value, auth_default) VALUES ('14','a_news_upd','N');
INSERT INTO eqdkp_auth_options (auth_id, auth_value, auth_default) VALUES ('15','a_news_del','N');
INSERT INTO eqdkp_auth_options (auth_id, auth_value, auth_default) VALUES ('16','a_raid_add','N');
INSERT INTO eqdkp_auth_options (auth_id, auth_value, auth_default) VALUES ('17','a_raid_upd','N');
INSERT INTO eqdkp_auth_options (auth_id, auth_value, auth_default) VALUES ('18','a_raid_del','N');
INSERT INTO eqdkp_auth_options (auth_id, auth_value, auth_default) VALUES ('19','a_turnin_add','N');
INSERT INTO eqdkp_auth_options (auth_id, auth_value, auth_default) VALUES ('20','a_config_man','N');
INSERT INTO eqdkp_auth_options (auth_id, auth_value, auth_default) VALUES ('21','a_members_man','N');
INSERT INTO eqdkp_auth_options (auth_id, auth_value, auth_default) VALUES ('22','a_users_man','N');
INSERT INTO eqdkp_auth_options (auth_id, auth_value, auth_default) VALUES ('23','a_logs_view','N');
INSERT INTO eqdkp_auth_options (auth_id, auth_value, auth_default) VALUES ('24','u_event_list','Y');
INSERT INTO eqdkp_auth_options (auth_id, auth_value, auth_default) VALUES ('25','u_event_view','Y');
INSERT INTO eqdkp_auth_options (auth_id, auth_value, auth_default) VALUES ('26','u_item_list','Y');
INSERT INTO eqdkp_auth_options (auth_id, auth_value, auth_default) VALUES ('27','u_item_view','Y');
INSERT INTO eqdkp_auth_options (auth_id, auth_value, auth_default) VALUES ('28','u_member_list','Y');
INSERT INTO eqdkp_auth_options (auth_id, auth_value, auth_default) VALUES ('29','u_member_view','Y');
INSERT INTO eqdkp_auth_options (auth_id, auth_value, auth_default) VALUES ('30','u_raid_list','Y');
INSERT INTO eqdkp_auth_options (auth_id, auth_value, auth_default) VALUES ('31','u_raid_view','Y');
INSERT INTO eqdkp_auth_options (auth_id, auth_value, auth_default) VALUES ('32','a_plugins_man','N');
INSERT INTO eqdkp_auth_options (auth_id, auth_value, auth_default) VALUES ('33','a_styles_man','N');
INSERT INTO eqdkp_auth_options (auth_id, auth_value, auth_default) VALUES ('36','a_backup','N');

### Style presets
INSERT INTO eqdkp_styles (style_id, style_name, template_path, body_background, body_link, body_link_style, body_hlink, body_hlink_style, header_link, header_link_style, header_hlink, header_hlink_style, tr_color1, tr_color2, th_color1, fontface1, fontface2, fontface3, fontsize1, fontsize2, fontsize3, fontcolor1, fontcolor2, fontcolor3, fontcolor_neg, fontcolor_pos, table_border_width, table_border_color, table_border_style, input_color, input_border_width, input_border_color, input_border_style) VALUES (1, 'Default', 'default', '212042', '31308C', 'underline', '000084', 'underline', 'CECFEF', 'underline', 'E6E6F5', 'underline', 'EFEFEF', 'FFFFFF', '424563', 'Verdana, Tahoma, Arial', 'Verdana, Tahoma, Arial', 'Verdana, Tahoma, Arial', 10, 11, 12, '000000', 'CECFEF', '000000', 'F80000', '008800', 1, '7B819A', 'solid', 'FFFFFF', 1, '000000', 'solid');
INSERT INTO eqdkp_styles (style_id, style_name, template_path, body_background, body_link, body_link_style, body_hlink, body_hlink_style, header_link, header_link_style, header_hlink, header_hlink_style, tr_color1, tr_color2, th_color1, fontface1, fontface2, fontface3, fontsize1, fontsize2, fontsize3, fontcolor1, fontcolor2, fontcolor3, fontcolor_neg, fontcolor_pos, table_border_width, table_border_color, table_border_style, input_color, input_border_width, input_border_color, input_border_style) VALUES (2, 'Old School', 'default', 'FFFFFF', '000000', 'underline', '000000', 'underline', '000000', 'underline', '000000', 'underline', 'BCBFCB', 'D3D7DE', '7B819A', 'Verdana, Tahoma, Arial', 'Verdana, Tahoma, Arial', 'Verdana, Tahoma, Arial', 10, 11, 12, '000000', '000000', '000000', 'F80000', '008800', 0, '000000', 'solid', 'FFFFFF', 1, '000000', 'solid');
INSERT INTO eqdkp_styles (style_id, style_name, template_path, body_background, body_link, body_link_style, body_hlink, body_hlink_style, header_link, header_link_style, header_hlink, header_hlink_style, tr_color1, tr_color2, th_color1, fontface1, fontface2, fontface3, fontsize1, fontsize2, fontsize3, fontcolor1, fontcolor2, fontcolor3, fontcolor_neg, fontcolor_pos, table_border_width, table_border_color, table_border_style, input_color, input_border_width, input_border_color, input_border_style) VALUES (3, 'EQdkp VB', 'default', '8080A6', '000000', 'underline', 'FB4400', 'underline', 'FFF788', 'none', 'FFF788', 'underline', 'F1F1F1', 'DFDFDF', '606096', 'Verdana, Tahoma, Arial', 'Verdana, Tahoma, Arial', 'Verdana, Tahoma, Arial', 10, 11, 12, '000000', '000000', '000000', 'F80000', '008800', 0, '000000', 'none', 'FFFFFF', 1, '000000', 'solid');
INSERT INTO eqdkp_styles (style_id, style_name, template_path, body_background, body_link, body_link_style, body_hlink, body_hlink_style, header_link, header_link_style, header_hlink, header_hlink_style, tr_color1, tr_color2, th_color1, fontface1, fontface2, fontface3, fontsize1, fontsize2, fontsize3, fontcolor1, fontcolor2, fontcolor3, fontcolor_neg, fontcolor_pos, table_border_width, table_border_color, table_border_style, input_color, input_border_width, input_border_color, input_border_style) VALUES (4, 'Blueish', 'default', '939EAF', '586273', 'underline', 'FFF9F3', 'underline', 'FFFFFF', 'none', 'FFFFFF', 'none', 'D6DAE6', 'BFC5D3', '828FA2', 'Verdana, Tahoma, Arial', 'Verdana, Tahoma, Arial', 'Verdana, Tahoma, Arial', 10, 11, 12, '000000', 'FFFFFF', 'FFFFFF', 'F80000', '008800', 1, '455164', 'solid', '727E93', 1, 'FFFFFF', 'solid');
INSERT INTO eqdkp_styles (style_id, style_name, template_path, body_background, body_link, body_link_style, body_hlink, body_hlink_style, header_link, header_link_style, header_hlink, header_hlink_style, tr_color1, tr_color2, th_color1, fontface1, fontface2, fontface3, fontsize1, fontsize2, fontsize3, fontcolor1, fontcolor2, fontcolor3, fontcolor_neg, fontcolor_pos, table_border_width, table_border_color, table_border_style, input_color, input_border_width, input_border_color, input_border_style) VALUES (5, 'EQdkp Items', 'default', 'FFFFFF', '2F415D', 'underline', 'E98219', 'underline', 'FFFFFF', 'underline', 'E98219', 'underline', 'F1F1F3', 'E3E4E8', '263B58', 'Verdana, Tahoma, Arial', 'Verdana, Tahoma, Arial', 'Verdana, Tahoma, Arial', 10, 11, 12, '000000', '000000', '000000', 'F80000', '008800', 1, '1D2846', 'solid', 'FFFFFF', 1, '000000', 'solid');
INSERT INTO eqdkp_styles (style_id, style_name, template_path, body_background, body_link, body_link_style, body_hlink, body_hlink_style, header_link, header_link_style, header_hlink, header_hlink_style, tr_color1, tr_color2, th_color1, fontface1, fontface2, fontface3, fontsize1, fontsize2, fontsize3, fontcolor1, fontcolor2, fontcolor3, fontcolor_neg, fontcolor_pos, table_border_width, table_border_color, table_border_style, input_color, input_border_width, input_border_color, input_border_style) VALUES (6, 'aallix Silver', 'default', 'FFFFFF', '555555', 'none', 'EC8500', 'underline', '555555', 'none', 'EC8500', 'underline', 'F2F2F2', 'FAFAFA', 'E0E0E0', 'Verdana, Tahoma, Arial', 'Verdana, Tahoma, Arial', 'Verdana, Tahoma, Arial', 10, 11, 12, '000000', '000000', '000000', 'F80000', '008800', 1, '999999', 'solid', 'D1D1D1', 1, '000000', 'solid');
INSERT INTO eqdkp_styles (style_id, style_name, template_path, body_background, body_link, body_link_style, body_hlink, body_hlink_style, header_link, header_link_style, header_hlink, header_hlink_style, tr_color1, tr_color2, th_color1, fontface1, fontface2, fontface3, fontsize1, fontsize2, fontsize3, fontcolor1, fontcolor2, fontcolor3, fontcolor_neg, fontcolor_pos, table_border_width, table_border_color, table_border_style, input_color, input_border_width, input_border_color, input_border_style) VALUES (7, 'Penguin', 'default', 'FFFFFF', '000000', 'underline', '000000', 'underline', '000000', 'underline', '000000', 'underline', 'EEEEEE', 'FFFFFF', 'FFCC00', 'Verdana, Tahoma, Arial', 'Verdana, Tahoma, Arial', 'Verdana, Tahoma, Arial', 10, 11, 12, '000000', '000000', '000000', 'F80000', '008800', 1, 'FFCC00', 'solid', 'FFFFFF', 1, '000000', 'solid');
INSERT INTO eqdkp_styles (style_id, style_name, template_path, body_background, body_link, body_link_style, body_hlink, body_hlink_style, header_link, header_link_style, header_hlink, header_hlink_style, tr_color1, tr_color2, th_color1, fontface1, fontface2, fontface3, fontsize1, fontsize2, fontsize3, fontcolor1, fontcolor2, fontcolor3, fontcolor_neg, fontcolor_pos, table_border_width, table_border_color, table_border_style, input_color, input_border_width, input_border_color, input_border_style) VALUES (8, 'Collab', 'default', 'FFFFFF', '354A55', 'underline', '5B7F93', 'underline', '5B7F93', 'underline', '354A55', 'underline', 'F5F5F5', 'DEE7EB', 'C4D3DB', 'Verdana, Tahoma, Arial', 'Verdana, Tahoma, Arial', 'Verdana, Tahoma, Arial', 10, 11, 12, '000000', '000000', '000000', 'F80000', '008800', 1, '999999', 'solid', 'FFFFFF', 1, '000000', 'solid');
INSERT INTO eqdkp_styles (style_id, style_name, template_path, body_background, body_link, body_link_style, body_hlink, body_hlink_style, header_link, header_link_style, header_hlink, header_hlink_style, tr_color1, tr_color2, th_color1, fontface1, fontface2, fontface3, fontsize1, fontsize2, fontsize3, fontcolor1, fontcolor2, fontcolor3, fontcolor_neg, fontcolor_pos, table_border_width, table_border_color, table_border_style, input_color, input_border_width, input_border_color, input_border_style) VALUES (9, 'EQdkp Invision', 'default', 'FFFFFF', '000000', 'underline', '465584', 'underline', '3A4F6C', 'none', '3A4F6C', 'underline', 'E4EAF2', 'DFE6EF', 'BCD0ED', 'Verdana, Tahoma, Arial', 'Verdana, Tahoma, Arial', 'Verdana, Tahoma, Arial', 10, 11, 12, '000000', '000000', '000000', 'F80000', '008800', 1, '345487', 'solid', 'FFFFFF', 1, '000000', 'solid');
INSERT INTO eqdkp_styles (style_id, style_name, template_path, body_background, body_link, body_link_style, body_hlink, body_hlink_style, header_link, header_link_style, header_hlink, header_hlink_style, tr_color1, tr_color2, th_color1, fontface1, fontface2, fontface3, fontsize1, fontsize2, fontsize3, fontcolor1, fontcolor2, fontcolor3, fontcolor_neg, fontcolor_pos, table_border_width, table_border_color, table_border_style, input_color, input_border_width, input_border_color, input_border_style) VALUES (10, 'dkpUA', 'default', '253546', 'C6C6C6', 'underline', '576695', 'underline', 'C6C6C6', 'none', 'C6C6C6', 'underline', '39495A', '283846', '1F2F3D', 'Verdana', 'Verdana', 'Verdana', 10, 11, 12, 'C6C6C6', 'C6C6C6', '000000', 'FF0000', '00C000', 1, '60707E', 'solid', 'FFFFFF', 1, '60707E', 'solid');
INSERT INTO eqdkp_styles (style_id, style_name, template_path, body_background, body_link, body_link_style, body_hlink, body_hlink_style, header_link, header_link_style, header_hlink, header_hlink_style, tr_color1, tr_color2, th_color1, fontface1, fontface2, fontface3, fontsize1, fontsize2, fontsize3, fontcolor1, fontcolor2, fontcolor3, fontcolor_neg, fontcolor_pos, table_border_width, table_border_color, table_border_style, input_color, input_border_width, input_border_color, input_border_style) VALUES (11, 'subSilver', 'default', 'FFFFFF', '006699', 'underline', 'DD6900', 'underline', 'FFA34F', 'none', 'FFA34F', 'underline', 'DEE3E7', 'EFEFEF', '1073A5', 'Verdana, Arial', 'Verdana, Arial', 'Verdana, Arial', 10, 11, 12, '000000', '000000', '000000', 'F80000', '008800', 1, '006699', 'solid', 'FFFFFF', 1, '000000', 'solid');
INSERT INTO eqdkp_styles (style_id, style_name, template_path, body_background, body_link, body_link_style, body_hlink, body_hlink_style, header_link, header_link_style, header_hlink, header_hlink_style, tr_color1, tr_color2, th_color1, fontface1, fontface2, fontface3, fontsize1, fontsize2, fontsize3, fontcolor1, fontcolor2, fontcolor3, fontcolor_neg, fontcolor_pos, table_border_width, table_border_color, table_border_style, input_color, input_border_width, input_border_color, input_border_style) VALUES (12, 'EQdkp VB2', 'default', 'FFFFFF', '000000', 'underline', 'FF4400', 'underline', 'FFF788', 'none', 'FFF788', 'underline', 'F1F1F1', 'DFDFDF', '8080A6', 'Verdana, Arial', 'Verdana, Arial', 'Verdana, Arial', 10, 11, 12, '000000', '000000', '000000', 'F80000', '008800', 1, '555576', 'solid', 'FFFFFF', 1, '000000', 'solid');
INSERT INTO eqdkp_styles (style_id, style_name, template_path, body_background, body_link, body_link_style, body_hlink, body_hlink_style, header_link, header_link_style, header_hlink, header_hlink_style, tr_color1, tr_color2, th_color1, fontface1, fontface2, fontface3, fontsize1, fontsize2, fontsize3, fontcolor1, fontcolor2, fontcolor3, fontcolor_neg, fontcolor_pos, table_border_width, table_border_color, table_border_style, input_color, input_border_width, input_border_color, input_border_style) VALUES (13, 'EQCPS', 'default', '7B7984', '151F41', 'underline', '800000', 'underline', 'FFFFFF', 'none', 'FFFFFF', 'none', 'CECBCE', 'BDBABD', '424952', 'Verdana, Arial', 'Verdana, Arial', 'Verdana, Arial', 10, 11, 12, '000000', '000000', '000000', '800000', '008000', 1, '000000', 'solid', 'C0C0C0', 1, '000000', 'solid');

#WOW
INSERT INTO eqdkp_styles (style_id, style_name, template_path, body_background, body_link, body_link_style, body_hlink, body_hlink_style, header_link, header_link_style, header_hlink, header_hlink_style, tr_color1, tr_color2, th_color1, fontface1, fontface2, fontface3, fontsize1, fontsize2, fontsize3, fontcolor1, fontcolor2, fontcolor3, fontcolor_neg, fontcolor_pos, table_border_width, table_border_color, table_border_style, input_color, input_border_width, input_border_color, input_border_style) VALUES (14, 'wow_style', 'wow_style', '000000', 'CBA300', 'underline', 'C60000', 'underline', '003366', 'underline', 'C60000', 'underline', 'F2F2F2', 'FAFAFA', '', 'Verdana, Tahoma, Arial', 'Verdana, Tahoma, Arial', 'Verdana, Tahoma, Arial', 10, 11, 12, '003366', 'CBA300', '003366', 'F80000', '008800', 1, '000000', 'solid', 'D1D1D1', 1, '000000', 'solid');

#Moon
INSERT INTO eqdkp_styles (style_id, style_name, template_path, body_background, body_link, body_link_style, body_hlink, body_hlink_style, header_link, header_link_style, header_hlink, header_hlink_style, tr_color1, tr_color2, th_color1, fontface1, fontface2, fontface3, fontsize1, fontsize2, fontsize3, fontcolor1, fontcolor2, fontcolor3, fontcolor_neg, fontcolor_pos, table_border_width, table_border_color, table_border_style, input_color, input_border_width, input_border_color, input_border_style) VALUES (15, 'WoWMoonclaw01', 'WoWMoonclaw01', '000000', 'CBA300', 'none', 'E98219', 'underline', 'FFDD33', 'none', 'E98219', 'none', '454545', '202020', '020725', 'Verdana, Tahoma, Arial', 'Verdana, Tahoma, Arial', 'Verdana, Tahoma, Arial', 10, 11, 12, 'FFFFFF', '007799', 'FFFFFF', 'F80000', '008800', 0, '1', 'solid', '000000', 1, '202020', 'solid');

#empire
INSERT INTO eqdkp_styles (style_id, style_name, template_path, body_background, body_link, body_link_style, body_hlink, body_hlink_style, header_link, header_link_style, header_hlink, header_hlink_style, tr_color1, tr_color2, th_color1, fontface1, fontface2, fontface3, fontsize1, fontsize2, fontsize3, fontcolor1, fontcolor2, fontcolor3, fontcolor_neg, fontcolor_pos, table_border_width, table_border_color, table_border_style, input_color, input_border_width, input_border_color, input_border_style) VALUES (16, ' WoWMaevahEmpire', 'WoWMaevahEmpire', '000000', 'CBA300', 'none', 'E98219', 'underline', 'FFDD33', 'none', 'E98219', 'none', '454545', '202020', '020725', 'Verdana, Tahoma, Arial', 'Verdana, Tahoma, Arial', 'Verdana, Tahoma, Arial', 10, 11, 12, 'FFFFFF', '007799', 'FFFFFF', 'F80000', '008800', 0, '1', 'solid', '000000', 1, '202020', 'solid');

#DefaultV
INSERT INTO eqdkp_styles (style_id, style_name, template_path, body_background, body_link, body_link_style, body_hlink, body_hlink_style, header_link, header_link_style, header_hlink, header_hlink_style, tr_color1, tr_color2, th_color1, fontface1, fontface2, fontface3, fontsize1, fontsize2, fontsize3, fontcolor1, fontcolor2, fontcolor3, fontcolor_neg, fontcolor_pos, table_border_width, table_border_color, table_border_style, input_color, input_border_width, input_border_color, input_border_style) VALUES (17, 'Default_Vert', 'defaultV', '212042', '31308C', 'underline', '000084', 'underline', 'CECFEF', 'underline', 'E6E6F5', 'underline', 'EFEFEF', 'FFFFFF', '424563', 'Verdana, Tahoma, Arial', 'Verdana, Tahoma, Arial', 'Verdana, Tahoma, Arial', 10, 11, 12, '000000', 'CECFEF', '000000', 'F80000', '008800', 1, '7B819A', 'solid', 'FFFFFF', 1, '000000', 'solid');
INSERT INTO eqdkp_styles (style_id, style_name, template_path, body_background, body_link, body_link_style, body_hlink, body_hlink_style, header_link, header_link_style, header_hlink, header_hlink_style, tr_color1, tr_color2, th_color1, fontface1, fontface2, fontface3, fontsize1, fontsize2, fontsize3, fontcolor1, fontcolor2, fontcolor3, fontcolor_neg, fontcolor_pos, table_border_width, table_border_color, table_border_style, input_color, input_border_width, input_border_color, input_border_style) VALUES (18, 'Old_School_Vert', 'defaultV', 'FFFFFF', '000000', 'underline', '000000', 'underline', '000000', 'underline', '000000', 'underline', 'BCBFCB', 'D3D7DE', '7B819A', 'Verdana, Tahoma, Arial', 'Verdana, Tahoma, Arial', 'Verdana, Tahoma, Arial', 10, 11, 12, '000000', '000000', '000000', 'F80000', '008800', 0, '000000', 'solid', 'FFFFFF', 1, '000000', 'solid');
INSERT INTO eqdkp_styles (style_id, style_name, template_path, body_background, body_link, body_link_style, body_hlink, body_hlink_style, header_link, header_link_style, header_hlink, header_hlink_style, tr_color1, tr_color2, th_color1, fontface1, fontface2, fontface3, fontsize1, fontsize2, fontsize3, fontcolor1, fontcolor2, fontcolor3, fontcolor_neg, fontcolor_pos, table_border_width, table_border_color, table_border_style, input_color, input_border_width, input_border_color, input_border_style) VALUES (19, 'EQdkp VB_Vert', 'defaultV', '8080A6', '000000', 'underline', 'FB4400', 'underline', 'FFF788', 'none', 'FFF788', 'underline', 'F1F1F1', 'DFDFDF', '606096', 'Verdana, Tahoma, Arial', 'Verdana, Tahoma, Arial', 'Verdana, Tahoma, Arial', 10, 11, 12, '000000', '000000', '000000', 'F80000', '008800', 0, '000000', 'none', 'FFFFFF', 1, '000000', 'solid');
INSERT INTO eqdkp_styles (style_id, style_name, template_path, body_background, body_link, body_link_style, body_hlink, body_hlink_style, header_link, header_link_style, header_hlink, header_hlink_style, tr_color1, tr_color2, th_color1, fontface1, fontface2, fontface3, fontsize1, fontsize2, fontsize3, fontcolor1, fontcolor2, fontcolor3, fontcolor_neg, fontcolor_pos, table_border_width, table_border_color, table_border_style, input_color, input_border_width, input_border_color, input_border_style) VALUES (20, 'Blueish_Vert', 'defaultV', '939EAF', '586273', 'underline', 'FFF9F3', 'underline', 'FFFFFF', 'none', 'FFFFFF', 'none', 'D6DAE6', 'BFC5D3', '828FA2', 'Verdana, Tahoma, Arial', 'Verdana, Tahoma, Arial', 'Verdana, Tahoma, Arial', 10, 11, 12, '000000', 'FFFFFF', 'FFFFFF', 'F80000', '008800', 1, '455164', 'solid', '727E93', 1, 'FFFFFF', 'solid');
INSERT INTO eqdkp_styles (style_id, style_name, template_path, body_background, body_link, body_link_style, body_hlink, body_hlink_style, header_link, header_link_style, header_hlink, header_hlink_style, tr_color1, tr_color2, th_color1, fontface1, fontface2, fontface3, fontsize1, fontsize2, fontsize3, fontcolor1, fontcolor2, fontcolor3, fontcolor_neg, fontcolor_pos, table_border_width, table_border_color, table_border_style, input_color, input_border_width, input_border_color, input_border_style) VALUES (21, 'EQdkp Items_Vert', 'defaultV', 'FFFFFF', '2F415D', 'underline', 'E98219', 'underline', 'FFFFFF', 'underline', 'E98219', 'underline', 'F1F1F3', 'E3E4E8', '263B58', 'Verdana, Tahoma, Arial', 'Verdana, Tahoma, Arial', 'Verdana, Tahoma, Arial', 10, 11, 12, '000000', '000000', '000000', 'F80000', '008800', 1, '1D2846', 'solid', 'FFFFFF', 1, '000000', 'solid');
INSERT INTO eqdkp_styles (style_id, style_name, template_path, body_background, body_link, body_link_style, body_hlink, body_hlink_style, header_link, header_link_style, header_hlink, header_hlink_style, tr_color1, tr_color2, th_color1, fontface1, fontface2, fontface3, fontsize1, fontsize2, fontsize3, fontcolor1, fontcolor2, fontcolor3, fontcolor_neg, fontcolor_pos, table_border_width, table_border_color, table_border_style, input_color, input_border_width, input_border_color, input_border_style) VALUES (22, 'aallix Silver_Vert', 'defaultV', 'FFFFFF', '555555', 'none', 'EC8500', 'underline', '555555', 'none', 'EC8500', 'underline', 'F2F2F2', 'FAFAFA', 'E0E0E0', 'Verdana, Tahoma, Arial', 'Verdana, Tahoma, Arial', 'Verdana, Tahoma, Arial', 10, 11, 12, '000000', '000000', '000000', 'F80000', '008800', 1, '999999', 'solid', 'D1D1D1', 1, '000000', 'solid');
INSERT INTO eqdkp_styles (style_id, style_name, template_path, body_background, body_link, body_link_style, body_hlink, body_hlink_style, header_link, header_link_style, header_hlink, header_hlink_style, tr_color1, tr_color2, th_color1, fontface1, fontface2, fontface3, fontsize1, fontsize2, fontsize3, fontcolor1, fontcolor2, fontcolor3, fontcolor_neg, fontcolor_pos, table_border_width, table_border_color, table_border_style, input_color, input_border_width, input_border_color, input_border_style) VALUES (23, 'Penguin_Vert', 'defaultV', 'FFFFFF', '000000', 'underline', '000000', 'underline', '000000', 'underline', '000000', 'underline', 'EEEEEE', 'FFFFFF', 'FFCC00', 'Verdana, Tahoma, Arial', 'Verdana, Tahoma, Arial', 'Verdana, Tahoma, Arial', 10, 11, 12, '000000', '000000', '000000', 'F80000', '008800', 1, 'FFCC00', 'solid', 'FFFFFF', 1, '000000', 'solid');
INSERT INTO eqdkp_styles (style_id, style_name, template_path, body_background, body_link, body_link_style, body_hlink, body_hlink_style, header_link, header_link_style, header_hlink, header_hlink_style, tr_color1, tr_color2, th_color1, fontface1, fontface2, fontface3, fontsize1, fontsize2, fontsize3, fontcolor1, fontcolor2, fontcolor3, fontcolor_neg, fontcolor_pos, table_border_width, table_border_color, table_border_style, input_color, input_border_width, input_border_color, input_border_style) VALUES (24, 'Collab_Vert', 'defaultV', 'FFFFFF', '354A55', 'underline', '5B7F93', 'underline', '5B7F93', 'underline', '354A55', 'underline', 'F5F5F5', 'DEE7EB', 'C4D3DB', 'Verdana, Tahoma, Arial', 'Verdana, Tahoma, Arial', 'Verdana, Tahoma, Arial', 10, 11, 12, '000000', '000000', '000000', 'F80000', '008800', 1, '999999', 'solid', 'FFFFFF', 1, '000000', 'solid');
INSERT INTO eqdkp_styles (style_id, style_name, template_path, body_background, body_link, body_link_style, body_hlink, body_hlink_style, header_link, header_link_style, header_hlink, header_hlink_style, tr_color1, tr_color2, th_color1, fontface1, fontface2, fontface3, fontsize1, fontsize2, fontsize3, fontcolor1, fontcolor2, fontcolor3, fontcolor_neg, fontcolor_pos, table_border_width, table_border_color, table_border_style, input_color, input_border_width, input_border_color, input_border_style) VALUES (25, 'EQdkp Invision_Vert', 'defaultV', 'FFFFFF', '000000', 'underline', '465584', 'underline', '3A4F6C', 'none', '3A4F6C', 'underline', 'E4EAF2', 'DFE6EF', 'BCD0ED', 'Verdana, Tahoma, Arial', 'Verdana, Tahoma, Arial', 'Verdana, Tahoma, Arial', 10, 11, 12, '000000', '000000', '000000', 'F80000', '008800', 1, '345487', 'solid', 'FFFFFF', 1, '000000', 'solid');
INSERT INTO eqdkp_styles (style_id, style_name, template_path, body_background, body_link, body_link_style, body_hlink, body_hlink_style, header_link, header_link_style, header_hlink, header_hlink_style, tr_color1, tr_color2, th_color1, fontface1, fontface2, fontface3, fontsize1, fontsize2, fontsize3, fontcolor1, fontcolor2, fontcolor3, fontcolor_neg, fontcolor_pos, table_border_width, table_border_color, table_border_style, input_color, input_border_width, input_border_color, input_border_style) VALUES (26, 'dkpUA_Vert', 'defaultV', '253546', 'C6C6C6', 'underline', '576695', 'underline', 'C6C6C6', 'none', 'C6C6C6', 'underline', '39495A', '283846', '1F2F3D', 'Verdana', 'Verdana', 'Verdana', 10, 11, 12, 'C6C6C6', 'C6C6C6', '000000', 'FF0000', '00C000', 1, '60707E', 'solid', 'FFFFFF', 1, '60707E', 'solid');
INSERT INTO eqdkp_styles (style_id, style_name, template_path, body_background, body_link, body_link_style, body_hlink, body_hlink_style, header_link, header_link_style, header_hlink, header_hlink_style, tr_color1, tr_color2, th_color1, fontface1, fontface2, fontface3, fontsize1, fontsize2, fontsize3, fontcolor1, fontcolor2, fontcolor3, fontcolor_neg, fontcolor_pos, table_border_width, table_border_color, table_border_style, input_color, input_border_width, input_border_color, input_border_style) VALUES (27, 'subSilver_Vert', 'defaultV', 'FFFFFF', '006699', 'underline', 'DD6900', 'underline', 'FFA34F', 'none', 'FFA34F', 'underline', 'DEE3E7', 'EFEFEF', '1073A5', 'Verdana, Arial', 'Verdana, Arial', 'Verdana, Arial', 10, 11, 12, '000000', '000000', '000000', 'F80000', '008800', 1, '006699', 'solid', 'FFFFFF', 1, '000000', 'solid');
INSERT INTO eqdkp_styles (style_id, style_name, template_path, body_background, body_link, body_link_style, body_hlink, body_hlink_style, header_link, header_link_style, header_hlink, header_hlink_style, tr_color1, tr_color2, th_color1, fontface1, fontface2, fontface3, fontsize1, fontsize2, fontsize3, fontcolor1, fontcolor2, fontcolor3, fontcolor_neg, fontcolor_pos, table_border_width, table_border_color, table_border_style, input_color, input_border_width, input_border_color, input_border_style) VALUES (28, 'EQdkp VB2_Vert', 'defaultV', 'FFFFFF', '000000', 'underline', 'FF4400', 'underline', 'FFF788', 'none', 'FFF788', 'underline', 'F1F1F1', 'DFDFDF', '8080A6', 'Verdana, Arial', 'Verdana, Arial', 'Verdana, Arial', 10, 11, 12, '000000', '000000', '000000', 'F80000', '008800', 1, '555576', 'solid', 'FFFFFF', 1, '000000', 'solid');
INSERT INTO eqdkp_styles (style_id, style_name, template_path, body_background, body_link, body_link_style, body_hlink, body_hlink_style, header_link, header_link_style, header_hlink, header_hlink_style, tr_color1, tr_color2, th_color1, fontface1, fontface2, fontface3, fontsize1, fontsize2, fontsize3, fontcolor1, fontcolor2, fontcolor3, fontcolor_neg, fontcolor_pos, table_border_width, table_border_color, table_border_style, input_color, input_border_width, input_border_color, input_border_style) VALUES (29, 'EQCPS_Vert', 'defaultV', '7B7984', '151F41', 'underline', '800000', 'underline', 'FFFFFF', 'none', 'FFFFFF', 'none', 'CECBCE', 'BDBABD', '424952', 'Verdana, Arial', 'Verdana, Arial', 'Verdana, Arial', 10, 11, 12, '000000', '000000', '000000', '800000', '008000', 1, '000000', 'solid', 'C0C0C0', 1, '000000', 'solid');


#WOWV new by Urox
INSERT INTO eqdkp_styles (style_id, style_name, template_path, body_background, body_link, body_link_style, body_hlink, body_hlink_style, header_link, header_link_style, header_hlink, header_hlink_style, tr_color1, tr_color2, th_color1, fontface1, fontface2, fontface3, fontsize1, fontsize2, fontsize3, fontcolor1, fontcolor2, fontcolor3, fontcolor_neg, fontcolor_pos, table_border_width, table_border_color, table_border_style, input_color, input_border_width, input_border_color, input_border_style) VALUES (30, 'wow_style_Vert', 'wow_styleV', '000000', 'CBA300', 'underline', 'C60000', 'underline', '003366', 'underline', 'C60000', 'underline', 'F2F2F2', 'FAFAFA', '', 'Verdana, Tahoma, Arial', 'Verdana, Tahoma, Arial', 'Verdana, Tahoma, Arial', 10, 11, 12, '003366', 'CBA300', '003366', 'F80000', '008800', 1, '000000', 'solid', 'D1D1D1', 1, '000000', 'solid');

#MoonV
INSERT INTO eqdkp_styles (style_id, style_name, template_path, body_background, body_link, body_link_style, body_hlink, body_hlink_style, header_link, header_link_style, header_hlink, header_hlink_style, tr_color1, tr_color2, th_color1, fontface1, fontface2, fontface3, fontsize1, fontsize2, fontsize3, fontcolor1, fontcolor2, fontcolor3, fontcolor_neg, fontcolor_pos, table_border_width, table_border_color, table_border_style, input_color, input_border_width, input_border_color, input_border_style) VALUES (31, 'WoWMoonclaw01_Vert', 'WoWMoonclaw01V', '000000', 'CBA300', 'none', 'E98219', 'underline', 'FFDD33', 'none', 'E98219', 'none', '454545', '202020', '020725', 'Verdana, Tahoma, Arial', 'Verdana, Tahoma, Arial', 'Verdana, Tahoma, Arial', 10, 11, 12, 'FFFFFF', '007799', 'FFFFFF', 'F80000', '008800', 0, '1', 'solid', '000000', 1, '202020', 'solid');

#empireV
INSERT INTO eqdkp_styles (style_id, style_name, template_path, body_background, body_link, body_link_style, body_hlink, body_hlink_style, header_link, header_link_style, header_hlink, header_hlink_style, tr_color1, tr_color2, th_color1, fontface1, fontface2, fontface3, fontsize1, fontsize2, fontsize3, fontcolor1, fontcolor2, fontcolor3, fontcolor_neg, fontcolor_pos, table_border_width, table_border_color, table_border_style, input_color, input_border_width, input_border_color, input_border_style) VALUES (32, 'WoWMaevahEmpire_Vert', 'WoWMaevahEmpireV', '000000', 'CBA300', 'none', 'E98219', 'underline', 'FFDD33', 'none', 'E98219', 'none', '454545', '202020', '020725', 'Verdana, Tahoma, Arial', 'Verdana, Tahoma, Arial', 'Verdana, Tahoma, Arial', 10, 11, 12, 'FFFFFF', '007799', 'FFFFFF', 'F80000', '008800', 0, '1', 'solid', '000000', 1, '202020', 'solid');

#WoWV by Urox
INSERT INTO eqdkp_styles (style_id, style_name, template_path, body_background, body_link, body_link_style, body_hlink, body_hlink_style, header_link, header_link_style, header_hlink, header_hlink_style, tr_color1, tr_color2, th_color1, fontface1, fontface2, fontface3, fontsize1, fontsize2, fontsize3, fontcolor1, fontcolor2, fontcolor3, fontcolor_neg, fontcolor_pos, table_border_width, table_border_color, table_border_style, input_color, input_border_width, input_border_color, input_border_style) VALUES (33, 'wow_Vert', 'wowV', '000000', 'CBA300', 'none', 'CBA300', 'none', 'D7CEA4', 'none', 'C60000', 'none', '2F2F2F', '202020', '2D2D2D', 'Verdana, Tahoma, Arial', 'Verdana, Tahoma, Arial', 'Verdana, Tahoma, Arial', 10, 11, 12, 'D7CEA4', 'CBA300', '000000', 'F80000', '008800', 1, '000000', 'solid', 'D1D1D1', 1, '000000', 'solid');

#m9wow3eq
INSERT INTO eqdkp_styles (style_id, style_name, template_path, body_background, body_link, body_link_style, body_hlink, body_hlink_style, header_link, header_link_style, header_hlink, header_hlink_style, tr_color1, tr_color2, th_color1, fontface1, fontface2, fontface3, fontsize1, fontsize2, fontsize3, fontcolor1, fontcolor2, fontcolor3, fontcolor_neg, fontcolor_pos, table_border_width, table_border_color, table_border_style, input_color, input_border_width, input_border_color, input_border_style) VALUES (35, 'm9wow3eq', 'm9wow3eq', '000000', 'ffffff', 'underline', 'C60000', 'underline', 'ffffff', 'underline', 'C60000', 'underline', '252525', '161616', '', 'Verdana, Tahoma, Arial', 'Verdana, Tahoma, Arial', 'Verdana, Tahoma, Arial', 10, 11, 12, 'ffffff', 'CBA300', 'ffffff', 'F80000', '008800', 0, 'ffffff', 'solid', '0B0B0B', 1, '766B37', 'solid');

#luna_wotlk
INSERT INTO eqdkp_styles (style_id, style_name, template_path, body_background, body_link, body_link_style, body_hlink, body_hlink_style, header_link, header_link_style, header_hlink, header_hlink_style, tr_color1, tr_color2, th_color1, fontface1, fontface2, fontface3, fontsize1, fontsize2, fontsize3, fontcolor1, fontcolor2, fontcolor3, fontcolor_neg, fontcolor_pos, table_border_width, table_border_color, table_border_style, input_color, input_border_width, input_border_color, input_border_style) VALUES (36, 'luna_wotlk', 'luna_wotlk', '000000', '000000', 'underline', '000000', 'underline', '000000', 'none', '000000', 'none', 'FFFFFF', 'DDDDDD', 'CCCCCC', 'Verdana, Arial, Helvetica, sans-serif', 'Verdana, Arial, Helvetica, sans-serif', 'Verdana, Arial, Helvetica, sans-serif', 9, 9, 10, 'EEEEEE', 'EEEEEE', '000000', '008800', '000000', 1, '000000', 'none', 'e5e3e3', 1, '999999', 'solid');

#m9wow
INSERT INTO eqdkp_styles (style_id, style_name, template_path, body_background, body_link, body_link_style, body_hlink, body_hlink_style, header_link,header_link_style, header_hlink, header_hlink_style,tr_color1,tr_color2,th_color1, fontface1, fontface2, fontface3, fontsize1,fontsize2, fontsize3, fontcolor1, fontcolor2, fontcolor3, fontcolor_neg, fontcolor_pos, table_border_width, table_border_color,table_border_style, input_color, input_border_width, input_border_color, input_border_style)VALUES (37, 'm9wotlk', 'm9wotlk', '000000', 'ffffff', 'underline', 'C60000', 'underline', 'ffffff','underline', 'C60000', 'underline','252525','161616','', 'Verdana, Tahoma, Arial', 'Verdana, Tahoma, Arial', 'Verdana, Tahoma, Arial', 10,11, 12, 'ffffff', 'CBA300', 'ffffff', 'F80000', '008800', 1, 'ffffff','solid', 'D1D1D1', 1, '000000', 'solid');



### Style configurations
#default
INSERT INTO eqdkp_style_config (style_id, attendees_columns, logo_path) VALUES (1, '6',  'logo_plus.gif');

#Old School
INSERT INTO eqdkp_style_config (style_id, attendees_columns, logo_path) VALUES (2, '4',  'logo_plus.gif');

#EQdkp VB
INSERT INTO eqdkp_style_config (style_id, attendees_columns, logo_path) VALUES (3, '6',  'logo_plus.gif');

#Blueish
INSERT INTO eqdkp_style_config (style_id, attendees_columns, logo_path) VALUES (4, '6',  'logo_plus.gif');

#EQdkp Items
INSERT INTO eqdkp_style_config (style_id, attendees_columns, logo_path) VALUES (5, '6',  'logo_plus.gif');

#aallix Silver
INSERT INTO eqdkp_style_config (style_id, attendees_columns, logo_path) VALUES (6, '6',  'logo_plus.gif');

#Penguin
INSERT INTO eqdkp_style_config (style_id, attendees_columns, logo_path) VALUES (7, '6',  'logo_plus.gif');

#Collab
INSERT INTO eqdkp_style_config (style_id, attendees_columns, logo_path) VALUES (8, '6',  'logo_plus.gif');

#EQdkp Invision
INSERT INTO eqdkp_style_config (style_id, attendees_columns, logo_path) VALUES (9, '6',  'logo_plus.gif');

#dkpUA
INSERT INTO eqdkp_style_config (style_id, attendees_columns, logo_path) VALUES (10, '8', 'logo_plus.gif');

#subSilver
INSERT INTO eqdkp_style_config (style_id, attendees_columns, logo_path) VALUES (11, '8', 'logo_plus.gif');

#EQdkp VB2
INSERT INTO eqdkp_style_config (style_id, attendees_columns, logo_path) VALUES (12, '8', 'logo_plus.gif');

#EQCPS
INSERT INTO eqdkp_style_config (style_id, attendees_columns, logo_path) VALUES (13, '8', 'logo_plus.gif');

#wow_style
INSERT INTO eqdkp_style_config (style_id, attendees_columns, logo_path) VALUES (14, '6', '/logo/logo_wow.gif');

#WoWMoonclaw01
INSERT INTO eqdkp_style_config (style_id, attendees_columns, logo_path) VALUES (15, '8', 'logo_wow.gif');

#WoWMaevahEmpire
INSERT INTO eqdkp_style_config (style_id, attendees_columns, logo_path) VALUES (16, '6', 'logo_wow.gif');

#Default_Vert
INSERT INTO eqdkp_style_config (style_id, attendees_columns, logo_path) VALUES (17, '6', 'bc_header3.gif');

#Old_School_Vert
INSERT INTO eqdkp_style_config (style_id, attendees_columns, logo_path) VALUES (18, '4', 'bc_header3.gif');

#EQdkp VB_Vert
INSERT INTO eqdkp_style_config (style_id, attendees_columns, logo_path) VALUES (19, '6', 'bc_header3.gif');

#Blueish_Vert
INSERT INTO eqdkp_style_config (style_id, attendees_columns, logo_path) VALUES (20, '6', 'bc_header3.gif');

#EQdkp Items_Vert
INSERT INTO eqdkp_style_config (style_id, attendees_columns, logo_path) VALUES (21, '6', 'bc_header3.gif');

#aallix Silver_Vert
INSERT INTO eqdkp_style_config (style_id, attendees_columns, logo_path) VALUES (22, '6', 'bc_header3.gif');

#Penguin_Vert
INSERT INTO eqdkp_style_config (style_id, attendees_columns, logo_path) VALUES (23, '6', 'bc_header3.gif');

#Collab_Vert
INSERT INTO eqdkp_style_config (style_id, attendees_columns, logo_path) VALUES (24, '6', 'bc_header3.gif');

#EQdkp Invision_Vert
INSERT INTO eqdkp_style_config (style_id, attendees_columns, logo_path) VALUES (25, '6', 'bc_header3.gif');

#dkpUA_Vert
INSERT INTO eqdkp_style_config (style_id, attendees_columns, logo_path) VALUES (26, '8', 'bc_header3.gif');

#subSilver_Vert
INSERT INTO eqdkp_style_config (style_id, attendees_columns, logo_path) VALUES (27, '8', 'bc_header3.gif');

#EQdkp VB2_Vert
INSERT INTO eqdkp_style_config (style_id, attendees_columns, logo_path) VALUES (28, '8', 'bc_header3.gif');

#EQCPS_Vert
INSERT INTO eqdkp_style_config (style_id, attendees_columns, logo_path) VALUES (29, '8', 'bc_header3.gif');

#wow_style_Vert
INSERT INTO eqdkp_style_config (style_id, attendees_columns, logo_path) VALUES (30, '6', '/logo/logo_wow.gif');

#WoWMoonclaw01_Vert
INSERT INTO eqdkp_style_config (style_id, attendees_columns, logo_path) VALUES (31, '8', 'bc_header3.gif');

#WoWMaevahEmpire_Vert
INSERT INTO eqdkp_style_config (style_id, attendees_columns, logo_path) VALUES (32, '6', 'bc_header3.gif');

#wow_Vert
INSERT INTO eqdkp_style_config (style_id, attendees_columns, logo_path) VALUES (33, '6', '/logo/logo_wow.gif');

#m9wow
INSERT INTO eqdkp_style_config (style_id, attendees_columns, logo_path) VALUES (35, '6', 'wowlogo3.png');

#luna_wotlk
INSERT INTO eqdkp_style_config (style_id, attendees_columns, logo_path) VALUES (36, '6', 'logo.png');

#m9wow
INSERT INTO eqdkp_style_config (style_id, attendees_columns, logo_path) VALUES (37, '6', 'wowlogo3.png');



### Default user
INSERT INTO eqdkp_users (user_id, username, user_password, user_style, user_lang, user_active) VALUES ('1','admin',md5('admin'),'36','english','0');
INSERT INTO eqdkp_auth_users (auth_id, user_id, auth_setting) VALUES ('1','1','Y');
INSERT INTO eqdkp_auth_users (auth_id, user_id, auth_setting) VALUES ('2','1','Y');
INSERT INTO eqdkp_auth_users (auth_id, user_id, auth_setting) VALUES ('3','1','Y');
INSERT INTO eqdkp_auth_users (auth_id, user_id, auth_setting) VALUES ('4','1','Y');
INSERT INTO eqdkp_auth_users (auth_id, user_id, auth_setting) VALUES ('5','1','Y');
INSERT INTO eqdkp_auth_users (auth_id, user_id, auth_setting) VALUES ('6','1','Y');
INSERT INTO eqdkp_auth_users (auth_id, user_id, auth_setting) VALUES ('7','1','Y');
INSERT INTO eqdkp_auth_users (auth_id, user_id, auth_setting) VALUES ('8','1','Y');
INSERT INTO eqdkp_auth_users (auth_id, user_id, auth_setting) VALUES ('9','1','Y');
INSERT INTO eqdkp_auth_users (auth_id, user_id, auth_setting) VALUES ('10','1','Y');
INSERT INTO eqdkp_auth_users (auth_id, user_id, auth_setting) VALUES ('11','1','Y');
INSERT INTO eqdkp_auth_users (auth_id, user_id, auth_setting) VALUES ('12','1','Y');
INSERT INTO eqdkp_auth_users (auth_id, user_id, auth_setting) VALUES ('13','1','Y');
INSERT INTO eqdkp_auth_users (auth_id, user_id, auth_setting) VALUES ('14','1','Y');
INSERT INTO eqdkp_auth_users (auth_id, user_id, auth_setting) VALUES ('15','1','Y');
INSERT INTO eqdkp_auth_users (auth_id, user_id, auth_setting) VALUES ('16','1','Y');
INSERT INTO eqdkp_auth_users (auth_id, user_id, auth_setting) VALUES ('17','1','Y');
INSERT INTO eqdkp_auth_users (auth_id, user_id, auth_setting) VALUES ('18','1','Y');
INSERT INTO eqdkp_auth_users (auth_id, user_id, auth_setting) VALUES ('19','1','Y');
INSERT INTO eqdkp_auth_users (auth_id, user_id, auth_setting) VALUES ('20','1','Y');
INSERT INTO eqdkp_auth_users (auth_id, user_id, auth_setting) VALUES ('21','1','Y');
INSERT INTO eqdkp_auth_users (auth_id, user_id, auth_setting) VALUES ('22','1','Y');
INSERT INTO eqdkp_auth_users (auth_id, user_id, auth_setting) VALUES ('23','1','Y');
INSERT INTO eqdkp_auth_users (auth_id, user_id, auth_setting) VALUES ('24','1','Y');
INSERT INTO eqdkp_auth_users (auth_id, user_id, auth_setting) VALUES ('25','1','Y');
INSERT INTO eqdkp_auth_users (auth_id, user_id, auth_setting) VALUES ('26','1','Y');
INSERT INTO eqdkp_auth_users (auth_id, user_id, auth_setting) VALUES ('27','1','Y');
INSERT INTO eqdkp_auth_users (auth_id, user_id, auth_setting) VALUES ('28','1','Y');
INSERT INTO eqdkp_auth_users (auth_id, user_id, auth_setting) VALUES ('29','1','Y');
INSERT INTO eqdkp_auth_users (auth_id, user_id, auth_setting) VALUES ('30','1','Y');
INSERT INTO eqdkp_auth_users (auth_id, user_id, auth_setting) VALUES ('31','1','Y');
INSERT INTO eqdkp_auth_users (auth_id, user_id, auth_setting) VALUES ('32','1','Y');
INSERT INTO eqdkp_auth_users (auth_id, user_id, auth_setting) VALUES ('33','1','Y');
INSERT INTO eqdkp_auth_users (auth_id, user_id, auth_setting) VALUES ('34','1','Y');
INSERT INTO eqdkp_auth_users (auth_id, user_id, auth_setting) VALUES ('35','1','Y');
INSERT INTO eqdkp_auth_users (auth_id, user_id, auth_setting) VALUES ('36','1','Y');

### Member ranks
INSERT INTO eqdkp_member_ranks (rank_id, rank_name) VALUES ('0', '');
INSERT INTO eqdkp_member_ranks (rank_id, rank_name) VALUES ('1', 'Member');


INSERT INTO eqdkp_plus_links (link_id, link_url, link_name, link_window) VALUES (1, 'http://www.eqdkp-plus.com', 'EQDKP-Plus', '1');
INSERT INTO eqdkp_plus_links (link_id, link_url, link_name, link_window) VALUES (2, 'http://allvatar.com/', 'Allvatar', '1');

INSERT INTO eqdkp_plus_config VALUES ('pk_updatecheck', '1');
INSERT INTO eqdkp_plus_config VALUES ('pk_windowtime', '10');
INSERT INTO eqdkp_plus_config VALUES ('pk_links', '1');
INSERT INTO eqdkp_plus_config VALUES ('pk_bosscount', '1');
INSERT INTO eqdkp_plus_config VALUES ('pk_itemstats', '1');
INSERT INTO eqdkp_plus_config VALUES ('pk_is_icon_loc', 'http://www.buffed.de/images/wow/32/');
INSERT INTO eqdkp_plus_config VALUES ('pk_is_icon_ext', '.png');
INSERT INTO eqdkp_plus_config VALUES ('pk_is_webdb', 'armory_wowhead');
INSERT INTO eqdkp_plus_config VALUES ('pk_attendance90', '1');
INSERT INTO eqdkp_plus_config VALUES ('pk_lastraid', '1');
INSERT INTO eqdkp_plus_config VALUES ('pk_class_color', '1');
INSERT INTO eqdkp_plus_config VALUES ('pk_quickdkp', '1');
INSERT INTO eqdkp_plus_config VALUES ('pk_newsloot_limit', 'all');
INSERT INTO eqdkp_plus_config VALUES ('pk_multiTooltip', '1');
INSERT INTO eqdkp_plus_config VALUES ('pk_multiSmarttip', '1');
INSERT INTO eqdkp_plus_config VALUES ('pk_leaderboard', '1');
INSERT INTO eqdkp_plus_config VALUES ('pk_rank_icon', '1');
INSERT INTO eqdkp_plus_config VALUES ('pk_showclasscolumn', '1');
INSERT INTO eqdkp_plus_config VALUES ('pk_show_skill', '1');
INSERT INTO eqdkp_plus_config VALUES ('pk_is_prio_first', 'armory');
INSERT INTO eqdkp_plus_config VALUES ('pk_is_prio_second', 'wowhead');
INSERT INTO eqdkp_plus_config VALUES ('pk_is_prio_third', 'allakhazam');
INSERT INTO eqdkp_plus_config VALUES ('pk_is_prio_fourth', 'allakhazam');
INSERT INTO eqdkp_plus_config VALUES ('pk_is_itemlanguage', 'de');
INSERT INTO eqdkp_plus_config VALUES ('pk_is_itemlanguage_alla', 'deDE');
INSERT INTO eqdkp_plus_config VALUES ('pk_is_autosearch', '1');
INSERT INTO eqdkp_plus_config VALUES ('pk_showRss', '');
INSERT INTO eqdkp_plus_config VALUES ('pk_Rss_Style', '1');
INSERT INTO eqdkp_plus_config VALUES ('pk_Rss_count', '10');
INSERT INTO eqdkp_plus_config VALUES ('pk_debug', '0');
INSERT INTO eqdkp_plus_config VALUES ('pk_air_enable', '1');
INSERT INTO eqdkp_plus_config VALUES ('pk_Rss_checkURL', '1');

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








