<?php
/******************************
 * EQdkp
 * Copyright 2002-2007
 * Licensed under the GNU GPL.  See COPYING for full terms.
 * ------------------
 * reset.php
 * Began: 13.05.2007
 *
 * corgan
 *
 ******************************/

define('EQDKP_INC', true);
define('IN_ADMIN', true);
$eqdkp_root_path = './../';
include_once($eqdkp_root_path . 'common.php');

class reset_eqdkp extends EQdkp_Admin
{
 
    function reset_eqdkp()
    {
        global $db, $eqdkp, $user, $tpl, $pm;
        global $SID ;

        parent::eqdkp_admin();

          
        $this->reset_data = array(
            'confirm_box'  => post_or_db('confirm_box')
        );
         

        $this->assoc_buttons(array(
            'form' => array(
                'name'    => '',
                'process' => 'display_form',
                'check'   => 'a_'),
        		'news' => array(
                'name'    => 'news',
                'process' => 'process_news',
                'check'   => 'a_'),
        		'dkp' => array(
                'name'    => 'dkp',
                'process' => 'process_dkp',
                'check'   => 'a_'),						
        		'all' => array(
                'name'    => 'all',
                'process' => 'process_all',
                'check'   => 'a_'))						
        										
        										);       
		 	  
				
		 	  
    } # end function
    
    function process_news()
    {
    	  global $user;
    	
    	if($this->reset_data['confirm_box'] == $user->lang['reset_confirm']) {
    		$this->update_database('reset_news.sql');
    	}
    	$this->display_form();
    }

    function process_dkp()
    {
    	  global $user;
    	
    	if($this->reset_data['confirm_box'] == $user->lang['reset_confirm']) {
    		$this->update_database('reset_dkp.sql');
    	}
    	$this->display_form();
    }   
    
    function process_all()
    {
    	  global $user;
    	
    	if($this->reset_data['confirm_box'] == $user->lang['reset_confirm']) {
    		$this->update_database('reset_all.sql');
    	}
    	$this->display_form();
    }       
    
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
		        	$log[] = "<td><img src='../images/glyphs/status_red.gif'></td><td>".$user->lang['upd_sql_error']."</td><td>".$sql[$i].'</td>';
						}
						else
						{
							$log[] = "<td><img src='../images/glyphs/status_green.gif'></td><td>".$user->lang['upd_sql_status_done']."</td><td>".$sql[$i].'</td>';
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
				'L_BACK_TO'									=> '<a href=index.php>'.$user->lang['upd_backto'].'</a>' 
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
     			
				
				// News
				$tpl->assign_block_vars('reset_row', array(
                      'ROW_CLASS'  				=> $eqdkp->switch_row_class(),
                      'TYPE'  						=> $user->lang['reset_news'],
                      'DISC'  						=> $user->lang['reset_news_disc'],
                      'VAL_NAME' 					=> 'news'
                      ));
				
				// DKP
				$tpl->assign_block_vars('reset_row', array(
                      'ROW_CLASS'  				=> $eqdkp->switch_row_class(),
                      'TYPE'  						=> $user->lang['reset_dkp'],
                      'DISC'  						=> $user->lang['reset_dkp_disc'],
                      'VAL_NAME' 					=> 'dkp'
                      ));							
        
        // All
        $tpl->assign_block_vars('reset_row', array(
                      'ROW_CLASS'  				=> $eqdkp->switch_row_class(),
                      'TYPE'  						=> $user->lang['reset_ALL'],
                      'DISC'  						=> $user->lang['reset_ALL_DISC'],
                      'VAL_NAME' 					=> 'all'
                      ));                      
        
        $tpl->assign_vars(array(
        		'L_RESET_HEADER' 			  =>	$user->lang['reset_header'],  
        		'L_RESET_INFO'  			  =>	$user->lang['reset_infotext'],  
        		'L_RESET_TYPE' 			    =>	$user->lang['reset_type'],  
        		'L_RESET_DISC' 			    =>	$user->lang['reset_disc'],  
        		'L_RESET_SEC' 			    =>	$user->lang['reset_sec'],  
        		'L_RESET_ACTION' 			  =>	$user->lang['reset_action'],   		
        		'L_RESET_CONFIRM'			  =>	$user->lang['reset_confirm'],
        		'L_RESET_CONFIRM_TEXT'  =>	$user->lang['reset_confirm_text'],
        		
        		'L_STATUS'							=>	$user->lang['upd_status'],    		
        		'L_SQL_STRING' 					=>	$user->lang['upd_sql_string'],    	
        		'L_SQL_STATUS_DONE' 		=>  $user->lang['upd_sql_status_done'],  

        		'L_SQL_ERROR' 					=>	$user->lang['upd_sql_error'],    	
        		'L_EQDKP_SYSTEM_TITLE' 	=>  $user->lang['upd_eqdkp_system_title'],
        		'L_EQDKP_STATUS'				=>	$user->lang['upd_eqdkp_status'],    	
        		        		
        		));
        		

				
        $eqdkp->set_vars(array(
            'page_title'    => 'Reset EQDKP Plus',
            'template_file' => 'admin/reset.html',
            'display'       => true)
        		);
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

$info = new reset_eqdkp;
$info->process();
?>