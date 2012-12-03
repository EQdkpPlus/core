<?php
/******************************
 * EQdkp
 * Copyright 2002-2005
 * Licensed under the GNU GPL.  See COPYING for full terms.
 * ------------------
 * upgrade.php
 * Began: Tue July 1 2003
 *
 * $Id: upgrade.php 8 2006-05-08 17:15:20Z tsigo $
 *
 ******************************/

define('EQDKP_INC', true);
$eqdkp_root_path = './';
include_once($eqdkp_root_path . 'common.php');

    // I require MySQL version 4.0.4 minimum.
    $version = mysql_get_server_info();

    if ( ! ($version >= "4.0") ){

       printf("MySQL server version is not sufficient for EQdkp. ");
       printf("EQdkp requires MySQL version > 4.0 - you are running \n");
       printf("version %s .\n", $version);
       die;
    }

class Upgrade
{
    var $db = null;
    var $versions = array('1.0.0','1.1.0','1.2.0B1','1.2.0B2','1.2.0RC1','1.2.0RC2','1.2.0', '1.3.X');


    function upgrade()
    {
        global $db, $eqdkp, $user, $tpl, $pm;
        global $SID;

        $db->error_die(false);

        if ( isset($_POST['upgrade']) )
        {
            // Find out what version we're upgrading from
            $version_from = $_POST['version'];
            foreach ( $this->versions as $index => $version )
            {
                if ( str_replace('.', '', $version) == $version_from )
                {
                    $method = 'upgrade_' . $version_from;
                    $this->$method($index);
                }
            }
        }
        else
        {
            $this->display_form();
        }
    }

    function finalize($index)
    {
        global $user;

        if ( isset($this->versions[$index + 1]) )
        {
            $method = 'upgrade_' . str_replace('.', '', $this->versions[$index + 1]);
            $this->$method($index + 1);
        }
        else
        {
            message_die($user->lang['upgrade_complete'], $user->lang['success']);
        }
    }

    //--------------------------------------------------------------
    // Upgrade methods
    //--------------------------------------------------------------

    function upgrade_100($index)
    {
        global $db, $eqdkp, $user, $tpl, $pm;
        global $SID, $table_prefix;

        $queries = array(
            "CREATE TABLE IF NOT EXISTS " . $table_prefix . "member_flags (
               flag_id smallint(3) unsigned NOT NULL UNIQUE,
               flag_name varchar(50) NOT NULL));",
            "INSERT INTO " . $table_prefix . "member_flags (flag_id, flag_name) VALUES ('0', '');",
            "INSERT INTO " . $table_prefix . "member_flags (flag_id, flag_name) VALUES ('1', 'Member');",
            "ALTER TABLE " . $table_prefix . "members ADD member_flag smallint(3) NOT NULL default '0' AFTER member_class;",
            "INSERT INTO " . $table_prefix . "config (config_name, config_value) VALUES ('parsetags', '');",
            "INSERT INTO " . $table_prefix . "styles (style_id, style_name, template_path, body_background, body_link, body_link_style, body_hlink, body_hlink_style, header_link, header_link_style, header_hlink, header_hlink_style, tr_color1, tr_color2, th_color1, fontface1, fontface2, fontface3, fontsize1, fontsize2, fontsize3, fontcolor1, fontcolor2, fontcolor3, fontcolor_neg, fontcolor_pos, table_border_width, table_border_color, table_border_style, input_color, input_border_width, input_border_color, input_border_style) VALUES (10, 'dkpUA', 'default', '253546', 'C6C6C6', 'underline', '576695', 'underline', 'C6C6C6', 'none', 'C6C6C6', 'underline', '39495A', '283846', '1F2F3D', 'Verdana', 'Verdana', 'Verdana', 10, 11, 12, 'C6C6C6', 'C6C6C6', '000000', 'FF0000', '00C000', 1, '60707E', 'solid', 'FFFFFF', 1, '60707E', 'solid');",
            "INSERT INTO " . $table_prefix . "styles (style_id, style_name, template_path, body_background, body_link, body_link_style, body_hlink, body_hlink_style, header_link, header_link_style, header_hlink, header_hlink_style, tr_color1, tr_color2, th_color1, fontface1, fontface2, fontface3, fontsize1, fontsize2, fontsize3, fontcolor1, fontcolor2, fontcolor3, fontcolor_neg, fontcolor_pos, table_border_width, table_border_color, table_border_style, input_color, input_border_width, input_border_color, input_border_style) VALUES (11, 'subSilver', 'default', 'FFFFFF', '006699', 'underline', 'DD6900', 'underline', 'FFA34F', 'none', 'FFA34F', 'underline', 'DEE3E7', 'EFEFEF', '1073A5', 'Verdana, Arial', 'Verdana, Arial', 'Verdana, Arial', 10, 11, 12, '000000', '000000', '000000', 'F80000', '008800', 1, '006699', 'solid', 'FFFFFF', 1, '000000', 'solid');",
            "INSERT INTO " . $table_prefix . "styles (style_id, style_name, template_path, body_background, body_link, body_link_style, body_hlink, body_hlink_style, header_link, header_link_style, header_hlink, header_hlink_style, tr_color1, tr_color2, th_color1, fontface1, fontface2, fontface3, fontsize1, fontsize2, fontsize3, fontcolor1, fontcolor2, fontcolor3, fontcolor_neg, fontcolor_pos, table_border_width, table_border_color, table_border_style, input_color, input_border_width, input_border_color, input_border_style) VALUES (12, 'EQdkp VB2', 'default', 'FFFFFF', '000000', 'underline', 'FF4400', 'underline', 'FFF788', 'none', 'FFF788', 'underline', 'F1F1F1', 'DFDFDF', '8080A6', 'Verdana, Arial', 'Verdana, Arial', 'Verdana, Arial', 10, 11, 12, '000000', '000000', '000000', 'F80000', '008800', 1, '555576', 'solid', 'FFFFFF', 1, '000000', 'solid');",
            "INSERT INTO " . $table_prefix . "styles (style_id, style_name, template_path, body_background, body_link, body_link_style, body_hlink, body_hlink_style, header_link, header_link_style, header_hlink, header_hlink_style, tr_color1, tr_color2, th_color1, fontface1, fontface2, fontface3, fontsize1, fontsize2, fontsize3, fontcolor1, fontcolor2, fontcolor3, fontcolor_neg, fontcolor_pos, table_border_width, table_border_color, table_border_style, input_color, input_border_width, input_border_color, input_border_style) VALUES (13, 'EQCPS', 'default', '7B7984', '151F41', 'underline', '800000', 'underline', 'FFFFFF', 'none', 'FFFFFF', 'none', 'CECBCE', 'BDBABD', '424952', 'Verdana, Arial', 'Verdana, Arial', 'Verdana, Arial', 10, 11, 12, '000000', '000000', '000000', '800000', '008000', 1, '000000', 'solid', 'C0C0C0', 1, '000000', 'solid');",
            "INSERT INTO " . $table_prefix . "style_config (style_id, attendees_columns, logo_path) VALUES (10, '8', 'dkpua_logo.gif');",
            "INSERT INTO " . $table_prefix . "style_config (style_id, attendees_columns, logo_path) VALUES (11, '8', 'subsilver_logo.gif');",
            "INSERT INTO " . $table_prefix . "style_config (style_id, attendees_columns, logo_path) VALUES (12, '8', 'logo.gif');",
            "INSERT INTO " . $table_prefix . "style_config (style_id, attendees_columns, logo_path) VALUES (13, '8', 'logo.gif');"
        );

        foreach ( $queries as $sql )
        {
            $db->query($sql);
        }

        $this->finalize($index);
    }

    function upgrade_110($index)
    {
        global $db, $eqdkp, $user, $tpl, $pm;
        global $SID, $table_prefix;

        $queries = array(
            "ALTER TABLE " . $table_prefix . "users ADD user_lastpage varchar(100) default '' AFTER user_lastvisit;",
            "INSERT INTO " . $table_prefix . "config (config_name, config_value) VALUES ('start_page', 'viewnews.php');",
            "ALTER TABLE " . $table_prefix . "plugins ADD plugin_version varchar(7);",
            "CREATE TABLE IF NOT EXISTS " . $table_prefix . "member_user (
               member_id smallint(5) unsigned NOT NULL,
               user_id smallint(5) unsigned NOT NULL,
               KEY member_id (member_id),
               KEY user_id (user_id));",
            "RENAME TABLE " . $table_prefix . "member_flags TO " . $table_prefix . "member_ranks;",
            "ALTER TABLE " . $table_prefix . "member_ranks CHANGE flag_id rank_id smallint(3) unsigned NOT NULL UNIQUE;",
            "ALTER TABLE " . $table_prefix . "member_ranks CHANGE flag_name rank_name varchar(50) NOT NULL;",
            "ALTER TABLE " . $table_prefix . "member_ranks ADD rank_hide enum('0','1') NOT NULL DEFAULT '0';",
            "ALTER TABLE " . $table_prefix . "member_ranks ADD rank_prefix varchar(75) NOT NULL default '';",
            "ALTER TABLE " . $table_prefix . "member_ranks ADD rank_suffix varchar(75) NOT NULL default '';",
            "ALTER TABLE " . $table_prefix . "members CHANGE member_flag member_rank_id smallint(3) NOT NULL default '0';"
        );

        foreach ( $queries as $sql )
        {
            $db->query($sql);
        }

        $this->finalize($index);
    }

    function upgrade_120B1($index)
    {
        global $db, $eqdkp, $user, $tpl, $pm;
        global $SID, $table_prefix;

        // Re-run some of the 1.1.0 queries, since 1.2.0B1 screwed them up a bit
        $queries = array(
            "ALTER TABLE " . $table_prefix . "users ADD user_lastpage varchar(100) default '' AFTER user_lastvisit;",
            "ALTER TABLE " . $table_prefix . "plugins ADD plugin_version varchar(7);",
            "CREATE TABLE IF NOT EXISTS " . $table_prefix . "member_user (
               member_id smallint(5) unsigned NOT NULL,
               user_id smallint(5) unsigned NOT NULL,
               KEY member_id (member_id),
               KEY user_id (user_id));",
        );

        foreach ( $queries as $sql )
        {
            $db->query($sql);
        }

        $this->finalize($index);
    }

    function upgrade_120B2($index)
    {
        global $db, $eqdkp, $user, $tpl, $pm;
        global $SID, $table_prefix;

        $queries = array(
            "ALTER TABLE " . $table_prefix . "config ADD PRIMARY KEY(config_name);"
        );

        foreach ( $queries as $sql )
        {
            $db->query($sql);
        }

        $this->finalize($index);
    }


    function upgrade_13X($index)
    {
        global $db, $eqdkp, $user, $tpl, $pm;
        global $SID, $table_prefix;

	$sql = 'SELECT config_value FROM ' . $table_prefix .'config WHERE config_name = "default_game"';
	$result = $db->query_first($sql);
	
	if ( $result == "WoW" ) {
	
		$sql = 'UPDATE ' . $table_prefix .'classes 
			SET class_armor_type = "Mail" 
			WHERE (class_armor_type = "Chain" 
			    OR class_armor_type = "chain")';
		$db->query($sql);
	}

	$sql = 'CREATE UNIQUE INDEX member_idx ON ' . $table_prefix .'members (member_name)';
	$result = $db->query_first($sql);

        $this->finalize($index);
    }

    function upgrade_120RC1($index)
    {
        $this->finalize($index);
    }

    function upgrade_120RC2($index)
    {
        $this->finalize($index);
    }

    // New for 1.3.0
    function upgrade_120($index)
    {
        global $db, $eqdkp, $user, $tpl, $pm;
        global $SID, $table_prefix;

        $queries = array(

	"DROP TABLE IF EXISTS " . $table_prefix ."classes",
	"DROP TABLE IF EXISTS " . $table_prefix ."races",
	"DROP TABLE IF EXISTS " . $table_prefix ."factions",
	"CREATE TABLE " . $table_prefix ."classes ( c_index smallint(3) unsigned NOT NULL auto_increment, class_id smallint(3) unsigned NOT NULL, class_name varchar(50) NOT NULL, class_armor_type varchar(50) NOT NULL, class_hide enum('0','1') NOT NULL DEFAULT '0', class_min_level smallint(3) unsigned NOT NULL default '0', class_max_level smallint(3) unsigned NOT NULL DEFAULT '999', PRIMARY KEY (c_index));",

	"CREATE TABLE " . $table_prefix ."races ( race_id smallint(3) unsigned NOT NULL UNIQUE, race_name varchar(50) NOT NULL, race_faction_id smallint(3) NOT NULL, race_hide enum('0','1') NOT NULL DEFAULT '0', PRIMARY KEY (race_id));",

	"CREATE TABLE " . $table_prefix ."factions ( faction_id smallint(3) unsigned NOT NULL UNIQUE, faction_name varchar(50) NOT NULL, faction_hide enum('0','1') NOT NULL DEFAULT '0', PRIMARY KEY (faction_id));",

        "ALTER TABLE " . $table_prefix . "member_ranks MODIFY rank_id smallint(6) NOT NULL default '0';",

        "ALTER TABLE " . $table_prefix . "members MODIFY member_level tinyint(2) NOT NULL default '70';",

        "ALTER TABLE " . $table_prefix . "members ADD member_class_id smallint(3) NOT NULL default '0';",
        "ALTER TABLE " . $table_prefix . "members ADD member_race_id smallint(3) NOT NULL default '0';",

	"ALTER TABLE " . $table_prefix . "items MODIFY item_value float (6,2);",

	"INSERT IGNORE INTO ". $table_prefix ."classes (class_id, class_name, class_armor_type) VALUES (0, 'Unknown', 'Plate');",
	"INSERT IGNORE INTO ". $table_prefix ."classes (class_id, class_name, class_armor_type) VALUES (1, 'Warrior', 'Plate');",
	"INSERT IGNORE INTO ". $table_prefix ."classes (class_id, class_name, class_armor_type) VALUES (2, 'Rogue', 'Chain');",
	"INSERT IGNORE INTO ". $table_prefix ."classes (class_id, class_name, class_armor_type) VALUES (3, 'Monk', 'Leather');",
	"INSERT IGNORE INTO ". $table_prefix ."classes (class_id, class_name, class_armor_type) VALUES (4, 'Ranger', 'Chain');",
	"INSERT IGNORE INTO ". $table_prefix ."classes (class_id, class_name, class_armor_type) VALUES (5, 'Paladin', 'Plate');",
	"INSERT IGNORE INTO ". $table_prefix ."classes (class_id, class_name, class_armor_type) VALUES (6, 'Shadow Knight', 'Plate');",
	"INSERT IGNORE INTO ". $table_prefix ."classes (class_id, class_name, class_armor_type) VALUES (7, 'Bard', 'Plate');",
	"INSERT IGNORE INTO ". $table_prefix ."classes (class_id, class_name, class_armor_type) VALUES (8, 'Beastlord', 'Leather');",
	"INSERT IGNORE INTO ". $table_prefix ."classes (class_id, class_name, class_armor_type) VALUES (9, 'Cleric', 'Plate');",
	"INSERT IGNORE INTO ". $table_prefix ."classes (class_id, class_name, class_armor_type) VALUES (10, 'Druid', 'Leather');",
	"INSERT IGNORE INTO ". $table_prefix ."classes (class_id, class_name, class_armor_type) VALUES (11, 'Shaman', 'Chain');",
	"INSERT IGNORE INTO ". $table_prefix ."classes (class_id, class_name, class_armor_type) VALUES (12, 'Enchanter', 'Silk');",
	"INSERT IGNORE INTO ". $table_prefix ."classes (class_id, class_name, class_armor_type) VALUES (13, 'Wizard', 'Silk');",
	"INSERT IGNORE INTO ". $table_prefix ."classes (class_id, class_name, class_armor_type) VALUES (14, 'Necromancer', 'Silk');",
	"INSERT IGNORE INTO ". $table_prefix ."classes (class_id, class_name, class_armor_type) VALUES (15, 'Magician', 'Silk');",
	"INSERT IGNORE INTO ". $table_prefix ."classes (class_id, class_name, class_armor_type) VALUES (16, 'Berserker', 'Leather');",

	"INSERT IGNORE INTO ". $table_prefix ."races (race_id, race_name) VALUES (0, 'Unknown');",
	"INSERT IGNORE INTO ". $table_prefix ."races (race_id, race_name) VALUES (1, 'Gnome');",
	"INSERT IGNORE INTO ". $table_prefix ."races (race_id, race_name) VALUES (2, 'Human');",
	"INSERT IGNORE INTO ". $table_prefix ."races (race_id, race_name) VALUES (3, 'Barbarian');",
	"INSERT IGNORE INTO ". $table_prefix ."races (race_id, race_name) VALUES (4, 'Dwarf');",
	"INSERT IGNORE INTO ". $table_prefix ."races (race_id, race_name) VALUES (5, 'High Elf');",
	"INSERT IGNORE INTO ". $table_prefix ."races (race_id, race_name) VALUES (6, 'Dark Elf');",
	"INSERT IGNORE INTO ". $table_prefix ."races (race_id, race_name) VALUES (7, 'Wood Elf');",
	"INSERT IGNORE INTO ". $table_prefix ."races (race_id, race_name) VALUES (8, 'Half Elf');",
	"INSERT IGNORE INTO ". $table_prefix ."races (race_id, race_name) VALUES (9, 'Vah Shir');",
	"INSERT IGNORE INTO ". $table_prefix ."races (race_id, race_name) VALUES (10, 'Troll');",
	"INSERT IGNORE INTO ". $table_prefix ."races (race_id, race_name) VALUES (11, 'Ogre');",
	"INSERT IGNORE INTO ". $table_prefix ."races (race_id, race_name) VALUES (12, 'Frog');",
	"INSERT IGNORE INTO ". $table_prefix ."races (race_id, race_name) VALUES (13, 'Iksar');",
	"INSERT IGNORE INTO ". $table_prefix ."races (race_id, race_name) VALUES (14, 'Erudite');",
	"INSERT IGNORE INTO ". $table_prefix ."races (race_id, race_name) VALUES (15, 'Halfling');",

	"INSERT IGNORE INTO ". $table_prefix ."factions (faction_id, faction_name) VALUES (1, 'Good');",
	"INSERT IGNORE INTO ". $table_prefix ."factions (faction_id, faction_name) VALUES (2, 'Evil');",

	"INSERT INTO ". $table_prefix ."config (config_name, config_value) VALUES ('default_game', 'Everquest');",
	"INSERT INTO ". $table_prefix ."config (config_name, config_value) VALUES ('default_locale', 'en_US');",

	"UPDATE ". $table_prefix ."members m, ". $table_prefix ."classes c SET m.member_class_id = c.class_id WHERE m.member_class = c.class_name;",
	"UPDATE ". $table_prefix ."members m, ". $table_prefix ."races r SET m.member_race_id = r.race_id WHERE m.member_race = r.race_name;",

        "ALTER TABLE " . $table_prefix . "members DROP member_class;",
        "ALTER TABLE " . $table_prefix . "members DROP member_race;",

		# queries for upgrade go here
        );

        foreach ( $queries as $sql )
        {
            $db->query($sql);
        }


        $this->finalize($index);
    }

    function display_form()
    {
        global $db, $eqdkp, $user, $tpl, $pm;
        global $SID;

        foreach ( $this->versions as $version )
        {
            // This will never happen if common.php's been upgraded already; I'm a re-re!
            $selected = ( $version == EQDKP_VERSION ) ? ' selected="selected"' : '';

            $tpl->assign_block_vars('version_row', array(
                'VALUE'    => str_replace('.', '', $version),
                'SELECTED' => $selected,
                'OPTION'   => 'EQdkp ' . $version)
            );
        }

        $tpl->assign_vars(array(
            'L_EQDKP_UPGRADE'  => $user->lang['eqdkp_upgrade'],
            'L_SELECT_VERSION' => $user->lang['select_version'],
            'L_UPGRADE'        => $user->lang['upgrade'])
        );

        $eqdkp->set_vars(array(
            'page_title'    => $user->lang['eqdkp_upgrade'],
            'template_file' => 'upgrade.html',
            'display'       => true)
        );
    }
}

$upgrade = new Upgrade();

// And the upgrade-o-matic 5000 takes care of the rest.
?>
