/******************************
 * EQdkp Raid Planner V2
 * Copyright 2005 by A.Stranger
 * Continued 2006 by Urox and Wallenium 
 * ------------------
 * config.php
 * Began: Tue June 1, 2006
 * Changed: Tue June 18, 2006
 * 
 ******************************/

README
--------------------------------------------------------------------------------------------------

README FIRST
---------------------------------------------
The content of the zip-file must be uploaded to the EQDKP-ROOT.

New Install of Raidplaner
---------------------------------------------
1. upload the new raidplan dir & all the other stuff
2. Go to the AdminPanel, Plugins. Install the RaidPlan Plugin.
3. Login as Administrator, go to Global DKP Configuration. There you can setup the Guest Permissions
4. Setup the Permissions for every single user of the DKP
5. chmod 777 ./lua_dl/ and ./lua_dl/AutoInvite.lua
6. You're done!

Update from an existing raidplaner
---------------------------------------------
1. Delete the old raidplan dir
2. upload the new raidplan dir & all the other stuff
3. go to PHPMyAdmin and execute the following SQL (Plese edit to your Table Prefix)


ALTER TABLE `eqdkp_raidplan_raids` ADD raid_date_invite int(11) NOT NULL default '0';
ALTER TABLE `eqdkp_raidplan_raid_attendees` ADD attendees_note text default NULL;
ALTER TABLE `eqdkp_raidplan_raid_attendees` ADD attendees_signup_time int(11) NOT NULL default '0';

INSERT INTO `eqdkp_auth_options` VALUES (506, 'a_raidplan_config', 'N');

CREATE TABLE `eqdkp_raidplan_config` (
  `config_name` varchar(255) NOT NULL default '',
  `config_value` varchar(255) default NULL,
  PRIMARY KEY  (`config_name`)
) ENGINE=MyISAM;

-- 
-- Daten für Tabelle `eqdkp_raidplan_config`
-- 

INSERT INTO `eqdkp_raidplan_config` VALUES ('rp_show_ranks', '1');
INSERT INTO `eqdkp_raidplan_config` VALUES ('rp_short_rank', '1');
INSERT INTO `eqdkp_raidplan_config` VALUES ('rp_send_email', '0');
INSERT INTO `eqdkp_raidplan_config` VALUES ('rp_roll_systm', '1');
INSERT INTO `eqdkp_raidplan_config` VALUES ('rp_wildcard', '1');
INSERT INTO `eqdkp_raidplan_config` VALUES ('rp_use_css', '1');
INSERT INTO `eqdkp_raidplan_config` VALUES ('rp_last_days', '7');
INSERT INTO `eqdkp_raidplan_config` VALUES ('rp_auto_hash', 'dgt_is_kewl');
INSERT INTO `eqdkp_raidplan_config` VALUES ('rp_auto_path', './lua_dl/');

4. Login as Administrator, go to Global DKP Configuration. There you can setup the Guest Permissions
5. Setup the Permissions for every single user of the DKP
6. You're done!


Bugfix on EQDKP Plugin API
---------------------------------------------
Changes on original files:

file:"/eqdkp/admin/index.php"
line:579-627
old: ... => array('link' => ' ... .php' .$SID
new: ... => array('link' => $eqdkp_root_path . 'admin/' . ' ... .php' .$SID

Do this for every single line!


---------------------------------------------
includes/eqdkp.php

search:
        if ( defined('PLUGIN') )
        {
            $script_path = 'plugins/' . PLUGIN . '/';
        }

replace:

        if ( defined('PLUGIN') )
        {
            $script_path = 'plugins/' . PLUGIN . '/';
            if ( defined('IN_ADMIN') ) { $script_path = 'plugins/' . PLUGIN . '/admin/'; }
        }


---------------------------------------------        
Wildcardsystem Integration:

settings.html in your EQDKP Template Folder

Search:

  <!-- END permissions_row -->
  <!-- ENDIF -->


After that:

<!-- IF S_MU_TABLE -->
</table>
<br />
<table width="100%" border="0" cellspacing="1" cellpadding="2">
<tr>
<th align="center" colspan="2">{L_WILDCARD}</th>
</tr>
<tr>
<td width="40%" class="row2">{L_WILDCARD}</td>
<td width="60%" class="row1">
<input type="checkbox" name="wildcard_set" value="Y"{WILDCARD_CHECKED} />
</td>
</tr>
</table>
<br />
<table width="100%" border="0" cellspacing="1" cellpadding="2">
  <tr>
    <th align="center" colspan="2">{L_ASSOCIATED_MEMBERS}</th>
  </tr>
  <tr>
    <td width="40%" class="row2">{L_MEMBERS}</td>
    <td width="60%" class="row1">
      <select name="member_id[]" multiple="multiple" size="10" class="input">
        <!-- BEGIN member_row -->
        <option value="{member_row.VALUE}"{member_row.SELECTED}>{member_row.OPTION}</option>
        <!-- END member_row -->
      </select>
      {FV_MEMBER_ID}
    </td>
  </tr>
  <!-- ENDIF -->