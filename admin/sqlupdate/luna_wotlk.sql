#m9wow3eq
DELETE FROM eqdkp_styles where style_id=36;
DELETE FROM eqdkp_style_config where style_id=36;
#luna_wotlk
INSERT INTO eqdkp_style_config (style_id, attendees_columns, logo_path) VALUES (36, '6', 'logo.png');
#luna_wotlk
INSERT INTO eqdkp_styles (style_id, style_name, template_path, body_background, body_link, body_link_style, body_hlink, body_hlink_style, header_link, header_link_style, header_hlink, header_hlink_style, tr_color1, tr_color2, th_color1, fontface1, fontface2, fontface3, fontsize1, fontsize2, fontsize3, fontcolor1, fontcolor2, fontcolor3, fontcolor_neg, fontcolor_pos, table_border_width, table_border_color, table_border_style, input_color, input_border_width, input_border_color, input_border_style) VALUES (36, 'luna_wotlk', 'luna_wotlk', '000000', '000000', 'underline', '000000', 'underline', 'ffffff', 'none', 'FFFFFF', 'none', 'FFFFFF', 'DDDDDD', 'CCCCCC', 'Verdana, Arial, Helvetica, sans-serif', 'Verdana, Arial, Helvetica, sans-serif', 'Verdana, Arial, Helvetica, sans-serif', 9, 9, 10, 'EEEEEE', 'EEEEEE', '000000', 'F80000', '008800', 1, '000000', 'none', 'e5e3e3', 1, '999999', 'solid');
