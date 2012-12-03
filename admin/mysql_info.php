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
include_once($eqdkp_root_path . 'itemstats/config.php');

class MySQL_Info extends EQdkp_Admin
{
    var $mysql_version = '';
    var $table_size    = 0;
    var $index_size    = 0;
    var $num_tables    = 0;

    function mysql_info()
    {
        global $db, $eqdkp, $user, $tpl, $pm;
        global $SID;

        parent::eqdkp_admin();

        $this->assoc_buttons(array(
            'form' => array(
                'name'    => '',
                'process' => 'display_form',
                'check'   => 'a_config_man'))
        );

        $result = $db->query('SELECT VERSION() AS mysql_version');
        if ( $row = $db->fetch_record($result) )
        {
            $this->mysql_version = $row['mysql_version'];
        }
    }

    // ---------------------------------------------------------
    // Display form
    // ---------------------------------------------------------
    function display_form()
    {
        global $db, $eqdkp, $user, $tpl, $pm;
        global $SID, $dbname, $table_prefix;

        if ( preg_match('/^(3\.23|4\.|5\.)/', $this->mysql_version) )
        {
            // Get table status
            $sql = "SHOW TABLE STATUS
                    FROM `" . $dbname."`";
            $result = $db->query($sql);

            $dbsize = 0;
            while ( $row = $db->fetch_record($result) )
            {
                if ( $row['Type'] != 'MRG_MyISAM' )
                {
                    if ( $table_prefix != '' )
                    {
                        // Current row is an EQdkp table, get info for it
                        if ( strstr($row['Name'], $table_prefix) or (item_cache_table == $row['Name']))
                        {
                            $tpl->assign_block_vars('table_row', array(
                                'ROW_CLASS'  => $eqdkp->switch_row_class(),
                                'TABLE_NAME' => $row['Name'],
                                'ROWS'       => number_format($row['Rows'], ','),
                                'TABLE_SIZE' => $this->db_size($row['Data_length']),
                                'INDEX_SIZE' => $this->db_size($row['Index_length']))
                            );

                            $this->num_tables++;
                            $this->table_size += $row['Data_length'];
                            $this->index_size += $row['Index_length'];
                        } // name match
                    } // table_prefix != ''
                } // type != MRG_MyISAM
            } // while

            $tpl->assign_vars(array(
                'NUM_TABLES'       => sprintf($user->lang['num_tables'], $this->num_tables),
                'TOTAL_TABLE_SIZE' => $this->db_size($this->table_size),
                'TOTAL_INDEX_SIZE' => $this->db_size($this->index_size),
                'TOTAL_SIZE'       => $this->db_size($this->table_size + $this->index_size),

                'L_EQDKP_TABLES' => $user->lang['eqdkp_tables'],
                'L_TABLE_NAME'   => $user->lang['table_name'],
                'L_ROWS'         => $user->lang['rows'],
                'L_TABLE_SIZE'   => $user->lang['table_size'],
                'L_INDEX_SIZE'   => $user->lang['index_size'],
                'L_TOTALS'       => $user->lang['totals'])
            );

            $eqdkp->set_vars(array(
                'page_title'    => 'MySQL Info',
                'template_file' => 'admin/mysql_info.html',
                'display'       => true)
            );
        } // version match
    }

    function db_size($size)
    {
        if ( $size >= 1048576 )
        {
            return sprintf('%.2f MB', ($size / 1048576));
        }
        elseif ( $size >= 1024 )
        {
            return sprintf('%.2f KB', ($size / 1024));
        }
        else
        {
            return sprintf('%.2f B', $size);
        }
    }
}

$info = new MySQL_Info;
$info->process();
?>
