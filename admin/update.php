<?php
/******************************
 * EQdkp
 * Copyright 2002-2003
 * Licensed under the GNU GPL.  See COPYING for full terms.
 * ------------------
 * update.php
 * Began: 16.11.2006
 *
 * corgan
 *
 ******************************/

define('EQDKP_INC', true);
define('IN_ADMIN', true);
$eqdkp_root_path = './../';
include_once($eqdkp_root_path . 'common.php');
include_once('sqlupdate/backup_data.php');

class update_eqdkp extends EQdkp_Admin
{

    function update_eqdkp()
    {
        global $db, $eqdkp, $user, $tpl, $pm;
        global $SID, $a_system, $a_styles;;

        parent::eqdkp_admin();

        $this->assoc_buttons(array(
            'form' => array(
                'name'    => '',
                'process' => 'display_form',
                'check'   => 'a_'))
        );

        if(isset($_GET['update']))
        {
        	$update_type = $_GET['update'] ;
        	switch ($update_type)
        	{
        		case "item_id"									: $sql_count = $this->update_database($update_type.'.sql'); 		break;
						case "newsloot"							: $sql_count = $this->update_database($update_type.'.sql'); 		break;
						case "pk_config"						: $sql_count = $this->update_database($update_type.'.sql'); 		break;
						case "pk_update"						: $sql_count = $this->update_database($update_type.'.sql'); 		break;
						case "pk_links"							: $sql_count = $this->update_database($update_type.'.sql'); 		break;
						case "multidkp"							: $sql_count = $this->update_database($update_type.'.sql'); 		break;
						case "multidkp_events"					: $sql_count = $this->update_database($update_type.'.sql'); 		break;
						case "adjustment"						: $sql_count = $this->update_database($update_type.'.sql'); 		break;
						case "event_icon"						: $sql_count = $this->update_database($update_type.'.sql'); 		break;
						case "mod_game"							: $sql_count = $this->update_database($update_type.'.sql'); 		break;
						#case "wow3theme"						: $sql_count = $this->update_database($update_type.'.sql'); 		break;
						case "wow_Vert"							: $sql_count = $this->update_database($update_type.'.sql'); 		break;
						case "wow_style"						: $sql_count = $this->update_database($update_type.'.sql'); 		break;
						case "wow_style_Vert"					: $sql_count = $this->update_database($update_type.'.sql'); 		break;
						case "WoWMoonclaw01"					: $sql_count = $this->update_database($update_type.'.sql'); 		break;
						case "WoWMoonclaw01_Vert"				: $sql_count = $this->update_database($update_type.'.sql'); 		break;
						case "WoWMaevahEmpire"					: $sql_count = $this->update_database($update_type.'.sql'); 		break;
						case "WoWMaevahEmpire_Vert"				: $sql_count = $this->update_database($update_type.'.sql'); 		break;
						case "dkpUA_Vert"						: $sql_count = $this->update_database($update_type.'.sql'); 		break;
						case "EQCPS_Vert"						: $sql_count = $this->update_database($update_type.'.sql'); 		break;
						case "Collab_Vert"						: $sql_count = $this->update_database($update_type.'.sql'); 		break;
						case "Blueish_Vert"						: $sql_count = $this->update_database($update_type.'.sql'); 		break;
						case "Penguin_Vert"						: $sql_count = $this->update_database($update_type.'.sql'); 		break;
						case "Default_Vert"						: $sql_count = $this->update_database($update_type.'.sql'); 		break;
						case "EQdkp VB_Vert"					: $sql_count = $this->update_database($update_type.'.sql'); 		break;
						case "subSilver_Vert"					: $sql_count = $this->update_database($update_type.'.sql'); 		break;
						case "EQdkp VB2_Vert"					: $sql_count = $this->update_database($update_type.'.sql'); 		break;
						case "Old_School_Vert"					: $sql_count = $this->update_database($update_type.'.sql'); 		break;
						case "EQdkp Items_Vert"					: $sql_count = $this->update_database($update_type.'.sql'); 		break;
						case "aallix Silver_Vert"				: $sql_count = $this->update_database($update_type.'.sql'); 		break;
						case "EQdkp Invision_Vert"				: $sql_count = $this->update_database($update_type.'.sql'); 		break;
						case "classcolor_style"					: $sql_count = $this->update_database($update_type.'.sql'); 		break;
						case "multidpk_fix"						: $sql_count = $this->update_database($update_type.'.sql'); 		break;
						case "m9wow3eq"							: $sql_count = $this->update_database($update_type.'.sql'); 		break;
						case "rss"								: $sql_count = $this->update_database($update_type.'.sql'); 		break;
						case "comments"							: $sql_count = $this->update_database($update_type.'.sql'); 		break;
						case "advanced_news"					: $sql_count = $this->update_database($update_type.'.sql'); 		break;
						case "511"								: $update_type = (strpos($eqdkp->config['default_game'],'german') >0) ? '511_de' : '511_eng' ; $sql_count = $this->update_database($update_type.'.sql'); 		break;
						case "513"								: $sql_count = $this->update_database($update_type.'.sql'); 		break;
					}

					# System
					########
					if($update_type=='system')
					{
						foreach($a_system as $key => $value)
						{
							if($value['state']==0)
							{
								if($key == '511')
								{
									$update_type = (strpos($eqdkp->config['default_game'],'german') >0) ? '511_de' : '511_eng' ;
									$sql_count += $this->update_database($update_type.'.sql');
								}else
								{
									$sql_count += $this->update_database($key.'.sql');
								}

							}
						}
					}

					# Styles
					########
					if($update_type=='template')
					{
						foreach($a_styles as $key => $value)
						{
							if($value['state']==0)
							{
								$sql_count += $this->update_database($key.'.sql');
							}
						}
					}

					$tpl->assign_vars(array(
														'QUERYS_DONE' 							=> $sql_count
														));

		 	  }# end ifset
    } # end function

		function update_database($sql_file)
		{
    	global $db, $eqdkp, $user, $tpl, $pm;
     	global $SID, $table_prefix;

			$sql_file = 'sqlupdate/'.$sql_file;

			if(file_exists($sql_file))
			{
		    $sql = @fread(@fopen($sql_file, 'r'), @filesize($sql_file));
		    $sql = preg_replace('#eqdkp\_(\S+?)([\s\.,]|$)#', $table_prefix . '\\1\\2', $sql);
		    $sql = $this->remove_remarks($sql);
		    $sql = $this->parse_sql($sql,';');
		    $sql_count = count($sql);
		    $i = 0;

		  	while ( $i < $sql_count )
		  	{
					if (isset($sql[$i]) && $sql[$i] != "")
					{
						if ( !($db->query($sql[$i]) ))
						{
		        			$log[] = "<td><img src='../images/glyphs/status_red.gif'></td><td>".$user->lang['upd_sql_error']."</td><td>".sql_highlight($sql[$i]).'</td>';
						}
						else
						{
							$log[] = "<td><img src='../images/glyphs/status_green.gif'></td><td>".$user->lang['upd_sql_status_done']."</td><td>".sql_highlight($sql[$i]).'</td>';
						}
					}
					$i++;
		  	}
		    unset($sql);

		    foreach($log as $logstring)
		    {
					$tpl->assign_block_vars('logs_row', array(
																	'ROW_CLASS'  => $eqdkp->switch_row_class(),
	                     					  'LOG_STRING'  => $logstring
	                     					  ));
		    }

				$tpl->assign_vars(array(
				'UPDATE_DONE'	 							=> true,
				'SQL_FOOTER' 								=> $user->lang['upd_sql_footer'],
				'L_BACK_TO'									=> '<a href=update.php>'.$user->lang['upd_backto'].'</a>'
				));
				return $sql_count-1 ;
		}
		else
		{
			$log[] = "<td><img src='../images/false.png'></td>
								<td>".$user->lang['upd_sql_error']."</td>
								<td>".sprintf($user->lang['upd_sql_file_error'],"/admin/".$sql_file ).'</td>';

		  foreach($log as $logstring)
	    {
	    	$tpl->assign_block_vars('logs_row', array(
																'ROW_CLASS'  => $eqdkp->switch_row_class(),
                     					  'LOG_STRING'  => $logstring
			               					  ));
			}

			$tpl->assign_vars(array(
			'UPDATE_DONE'	 							=> true,
			'L_BACK_TO'									=> '<a href=update.php>'.$user->lang['upd_backto'].'</a>'
			));

		}


		} // end function

    // ---------------------------------------------------------
    // Display form
    // ---------------------------------------------------------
    function display_form()
    {
        global $db, $eqdkp, $user, $tpl, $pm;
        global $SID, $dbname, $table_prefix, $a_system, $a_styles;

				#System STATUS
				###############
				$need_system_update=0;
				if(isset($a_system))
				{
					foreach($a_system as $key => $value)
					{
						$tpl->assign_block_vars('update_row',array(
																	'ROW_CLASS'  				=> $eqdkp->switch_row_class(),
												          'VERSION'					 	=> 'EQDKP Plus '.$value['version'],
												          'NAME'					 		=> $value['name'],
												          'DETAIL'					 	=> $value['detail'],
												          'STATE'					 		=> $this->create_state($value['state']),
												          'LINK'					 		=> $this->create_link($value['state'],$key)
	                    					 ));

	          if($value['state']==0){$need_system_update=1;}
					}
				}

				#Style STATUS
				#############
				if(isset($a_system))
				{
					foreach($a_styles as $key => $value)
					{
						$tpl->assign_block_vars('styles_row', array(
	                                'ROW_CLASS'  				=> $eqdkp->switch_row_class(),
	                                'STYLE_VERSION'  		=> $value['version'],
	                                'STYLE_NAME'  			=> $key,
	                                'STYLE_STATE' 			=> $this->create_state($value['state']),
	                                'STYLE_FILESTATE' 	=> $this->create_state($value['filestate']),
	                                'STYLE_LINK' 				=> $this->create_link($value['state'],$key)
	                                ));

	         if($value['state']==0){$need_template_update=1;}
					}# end foreach styles
				}# end if isset styles array


        $tpl->assign_vars(array(
        		'SYSTEM_STATUS'    			=> $this->create_update_link($need_system_update,'system'),
        		'TEMPLATE_STATUS'  			=> $this->create_update_link($need_template_update,'template'),
        		'L_EQDKP_STATUS'				=>	$user->lang['upd_eqdkp_status'],
        		'L_SYSTEM_STATUS' 			=>	$user->lang['upd_system_status'],
        		'L_TEMPLATE_STATUS' 		=>  $user->lang['upd_template_status'],
        		'L_UPDATE_NEED' 				=>	$user->lang['upd_update_need'],
        		'L_UPDATE_NEED_LINK' 		=>  $user->lang['upd_update_need_link'],
        		'L_NO_UPDATE' 					=>	$user->lang['upd_no_update'],
        		'L_STATUS'							=>	$user->lang['upd_status'],
        		'L_SQL_STRING' 					=>	$user->lang['upd_sql_string'],
        		'L_SQL_STATUS_DONE' 		=>  $user->lang['upd_sql_status_done'],

        		'L_SQL_ERROR' 					=>	$user->lang['upd_sql_error'],
        		'L_EQDKP_SYSTEM_TITLE' 	=>  $user->lang['upd_eqdkp_system_title'],
        		'L_PLUS_VERSION' 				=>	$user->lang['upd_plus_version'],
        		'L_PLUS_FEATURE' 				=>	$user->lang['upd_plus_feature'],
        		'L_PLUS_DETAIL' 				=>	$user->lang['upd_plus_detail'],

        		'L_UPDATE' 							=>	$user->lang['upd_update'],
        		'L_EQDKP_TEMPLATE_TITLE'=>  $user->lang['upd_eqdkp_template_title'],
        		'L_TEMPLATE_NAME' 			=>	$user->lang['upd_template_name'],
        		'L_TEMPLATE_STATE' 			=>	$user->lang['upd_template_state'],
        		'L_TEMPLATE_FILESTATE' 	=>  $user->lang['upd_template_filestate'],
        		'L_LINK_INSTALL' 				=>	$user->lang['upd_link_install'],
        		'L_LINK_REINSTALL' 			=>	$user->lang['upd_link_reinstall']

        		));

        $eqdkp->set_vars(array(
            'page_title'    => 'Update EQDKP Plus',
            'template_file' => 'admin/update.html',
            'display'       => true)
        );
    }

    function create_state($value)
    {
    	global $user ;
        if($value == 1)
        {
        	return "<img src='../images/glyphs/status_green.gif'>".' OK' ;
        }
        elseif($value==0)
        {
        	return "<img src='../images/glyphs/status_red.gif'> ".$user->lang['upd_state_error'] ;
        }
    }

    function create_link($value,$update_type)
    {
    	global $user ;
        if($value == 1)
        {
        	return "<a href='update.php?update=".$update_type."'>".$user->lang['upd_link_reinstall']."</a>" ;
        }
        elseif($value==0)
        {
        	return "<a href='update.php?update=".$update_type."'>".$user->lang['upd_link_install']."</a>" ;
        }
    }

    function create_update_link($value,$update_type)
    {
    	global $user ;
        if($value == 1)
        {
        	return "<td><img src='../images/false.png'></td><td>".$user->lang['upd_admin_need_update']."<br><br><a href='update.php?update=".$update_type."'>".$user->lang['upd_admin_link_update']."</a></td>" ;
        }
        elseif($value==0)
        {
        	return "<td><img src='../images/ok.png'></td><td>".$user->lang['upd_no_update']."</td>" ;
        }
    }

		function remove_remarks($sql)
		{
			if ( $sql == '' )
			{
			    die('Could not obtain SQL structure/data - function remove_remarks($sql)');
			}

			$retval = '';
			$lines  = explode("\n", $sql);
			unset($sql);

			foreach ( $lines as $line )
			{
			    // Only parse this line if there's something on it, and we're not on the last line
			    if ( strlen($line) > 0 )
			    {
			        // If '#' is the first character, strip the line
			        $retval .= ( substr($line, 0, 1) != '#' ) ? $line . "\n" : "\n";
			    }
			}
			unset($lines, $line);

			return $retval;
		}

		/**
		* Parse multi-line SQL statements into a single line
		*
		* @param    string  $sql    SQL file contents
		* @param    char    $delim  End-of-statement SQL delimiter
		* @return   array
		*/
		function parse_sql($sql, $delim)
		{
			if ( $sql == '' )
			{
			    die('Could not obtain SQL structure/data - function parse_sql($sql, $delim) ');
			}

			$retval     = array();
			$statements = explode($delim, $sql);
			unset($sql);

			$linecount = count($statements);
			for ( $i = 0; $i < $linecount; $i++ )
			{
			    if ( ($i != $linecount - 1) || (strlen($statements[$i]) > 0) )
			    {
			        $statements[$i] = trim($statements[$i]);
			        $statements[$i] = str_replace("\r\n", '', $statements[$i]) . "\n";

			        // Remove 2 or more spaces
			        $statements[$i] = preg_replace('#\s{2,}#', ' ', $statements[$i]);

			        $retval[] = trim($statements[$i]);
			    }
			}
			unset($statements);

			return $retval;
		}

}

$info = new update_eqdkp;
$info->process();
?>