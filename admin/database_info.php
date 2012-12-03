<?php
/******************************
 * EQdkp
 * Copyright 2002-2003
 * Licensed under the GNU GPL.  See COPYING for full terms.
 * ------------------
 * mysql_info.php
 * Began: Sat April 5 2003
 *
 * $Id$
 *
 ******************************/

define('EQDKP_INC', true);
define('IN_ADMIN', true);
$eqdkp_root_path = './../';
include_once($eqdkp_root_path . 'common.php');

class MySQL_Info extends EQdkp_Admin
{
		
    function MySQL_Info()
    {
        global $db, $core,$SID;

        $this->assoc_buttons(array(
            'form' => array(
                'name'    => '',
                'process' => 'display_form',
                'check'   => 'a_config_man'),
						'optimize' => array(
                'name'    => 'optimize',
                'process' => 'process_optimize',
                'check'   => 'a_config_man'),
						'repair' => array(
                'name'    => 'repair',
                'process' => 'process_repair',
                'check'   => 'a_config_man')
				));

    }

    // ---------------------------------------------------------
    // Display form
    // ---------------------------------------------------------
    function display_form()
    {
        global $db, $core, $user, $tpl, $pm;
        global $SID, $dbname, $table_prefix, $dbtype;
				
        $sql = "SHOW TABLE STATUS FROM `" . $dbname."`";
        $result = $db->query($sql);
				
				//Set some default-values
				$table_count = 0;
				$table_size = 0;
				$index_size = 0;
				
				while ($row = $db->fetch_record($result)) {
        	if ($table_prefix != '' && $row['Type'] != 'MRG_MyISAM') {
					 if (strstr($row['Name'], $table_prefix) or (item_cache_table == $row['Name'])){	
							$tpl->assign_block_vars('table_row', array(
									'ROW_CLASS'  => $core->switch_row_class(),
									'TABLE_NAME' => $row['Name'],
									'ROWS'       => $row['Rows'],
									'COLLATION'	 => $row['Collation'],
									'ENGINE'     => $row['Engine'],
									'TABLE_SIZE' => $this->convert_db_size($row['Data_length']),
									'INDEX_SIZE' => $this->convert_db_size($row['Index_length']))
							);

							$table_count++;
							$table_size += $row['Data_length'];
							$index_size += $row['Index_length'];
            } // close match
					} //close if eqdkp-table
				} //close while
				
				$tpl->assign_vars(array(
						'NUM_TABLES'       => sprintf($user->lang['num_tables'], $table_count),
						'TOTAL_TABLE_SIZE' => $this->convert_db_size($table_size),
						'TOTAL_INDEX_SIZE' => $this->convert_db_size($index_size),
						'TOTAL_SIZE'       => $this->convert_db_size($table_size + $index_size),
						
						'DB_ENGINE'			=> $dbtype,
						'DB_NAME'			=> $dbname,
						'DB_PREFIX'		=> $table_prefix,
						'DB_VERSION'	=> 'Client ('.mysql_get_client_info().')<br/>Server ('.mysql_get_server_info().')',

						'L_DB_INFOS' => $user->lang['mysql_info'],
						'L_EQDKP_TABLES' => $user->lang['eqdkp_tables'],
						'L_TABLE_NAME'   => $user->lang['table_name'],
						'L_ROWS'         => $user->lang['rows'],
						'L_TABLE_SIZE'   => $user->lang['table_size'],
						'L_INDEX_SIZE'   => $user->lang['index_size'],
						'L_TOTALS'       => $user->lang['totals'],
						'L_OPTIMIZE'     => $user->lang['optimize'],
						'L_REPAIR'     		=> $user->lang['repair_tables'],
						'L_DB_ENGINE'    => $user->lang['db_type'],
						'L_DB_NAME'    	=> $user->lang['db_name'],
						'L_DB_PREFIX'    => $user->lang['db_prefix'],
						'L_DB_VERSION'    => $user->lang['db_version'],
						'L_ENGINE'    => $user->lang['db_engine'],
						'L_COLLATION'    => $user->lang['db_collation'],
				));

				$core->set_vars(array(
						'page_title'    => $user->lang['title_mysqlinfo'],
						'template_file' => 'admin/database_info.html',
						'display'       => true)
				);
    }
		
		// ---------------------------------------------------------
    // Optimize tables
    // ---------------------------------------------------------
    function process_optimize()
    {
        global $db, $core, $user, $tpl, $pm;
        global $SID, $dbname, $table_prefix;
				
				$sql = "SHOW TABLE STATUS FROM `" . $dbname."`";
        $result = $db->query($sql);
				
				while ($row = $db->fetch_record($result)) {
        	if ($table_prefix != '' && $row['Type'] != 'MRG_MyISAM') {
						$db->query("OPTIMIZE TABLE ".$row['Name']);
					}
				}
				$this->display_form();
		}
		
		// ---------------------------------------------------------
    // Optimize tables
    // ---------------------------------------------------------
    function process_repair()
    {
        global $db, $core, $user, $tpl, $pm;
        global $SID, $dbname, $table_prefix;
				
				$sql = "SHOW TABLE STATUS FROM `" . $dbname."`";
        $result = $db->query($sql);
				
				while ($row = $db->fetch_record($result)) {

						$db->query("REPAIR TABLE ".$row['Name']);
				}
				$this->display_form();
		}
		
		// ---------------------------------------------------------
    // Process Helper
    // ---------------------------------------------------------
    function convert_db_size($bytes){
			if ( $bytes <= 1024 ){
					return $bytes.' B';
			} elseif ( $bytes <= 1048576 ) {
					return (round($bytes/1024, 2)).' KB';
			} else {
					return (round($bytes/1048576, 2)).' MB';
			}
		}

}

$info = new MySQL_Info;
$info->process();
?>
