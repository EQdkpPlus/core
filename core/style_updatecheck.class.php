<?php
 /*
 * Project:     EQdkp-Plus
 * License:     Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:       2010
 * Date:        $Date$
 * -----------------------------------------------------------------------
 * @author      $Author$
 * @copyright   2006-2010 EQdkp-Plus Developer Team
 * @link        http://eqdkp-plus.com
 * @package     eqdkp-plus
 * @version     $Rev$
 * 
 * $Id$
 */

if ( !defined('EQDKP_INC') ){
    header('HTTP/1.0 404 Not Found');exit;
}

if (!class_exists("style_updatecheck"))
{
  class style_updatecheck
  {
	
		var $update_styles = array();
	
		function check_local_updates(){
			global $db, $pcache, $eqdkp_root_path;
			
			$styles = $db->query("SELECT style_code, style_name, style_version FROM __styles");
			while ($row = $db->fetch_record($styles)){
				$tpl_index = $eqdkp_root_path . 'templates/' . $row['style_code'].'/package.xml';
				if (file_exists($tpl_index)){
						$xml = simplexml_load_file($tpl_index);
						
						$result = compareVersion($xml->version, $row['style_version']);
						if ($result == 1){
							$row['new_version'] = $xml->version;
							$this->update_styles[] = $row;
						}
				}
				
			}
			if (count($this->update_styles) >0){
				return true;
			}
			return false;
		} //close function
		
		function text_output(){
			global $user, $eqdkp_root_path;
			$output = $user->lang['style_update_warning'];
			$output .= '<ul>';
			foreach ($this->update_styles as $style){
					$output .= '<li><b>'.$style['style_name'].'</b> '.sprintf($user->lang['style_update_versions'], $style['style_version'], $style['new_version']).' - <b><a href="'.$eqdkp_root_path.'admin/styles.php">'.$user->lang['puc_solve_dbissues'].'</a></b></li>';
			}
			$output .= '</ul>';
			
			return $output;
		}
		
	}
}

?>