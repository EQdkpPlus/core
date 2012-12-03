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

if ( !defined('EQDKP_INC') )
{
    header('HTTP/1.0 404 Not Found');exit;
}
class portal
{
  var $positions = array('left1', 'left2', 'right', 'middle', 'bottom');
  var $THIRD_C = false;
  
	function __construct(){
		global $eqdkp_root_path, $db, $tpl, $user, $core, $pluslang, $wherevalue, $sql_db, $jquery, $game, $in;
		//$this->EnablePortalModules();   // Update the Portal Modules...
		
		// init the global variables...
		$this->isAdmin		= $user->check_auth('a_config_man', false);
		$this->jquery			= $jquery;
		$this->db					= $db;
		$this->in					= $in;
		$this->tpl				= $tpl;
		$this->user				= $user;
		$this->root_path	= $eqdkp_root_path;
		$this->core				= $core;
		$this->game				= $game;
		$this->modules		= array();
		
		// Site Display Thing
		require_once($this->root_path . 'core/sitedisplay.class.php');
		$siteDisplay = new siteDisplay();
		
		//get plugin status with one query
		$sql = "SELECT id, position, path, plugin, visibility, collapsable FROM __portal WHERE enabled='1' AND position IN ('".implode("', '", $this->positions)."') ORDER BY number";
		if ($plugin_result = $this->db->query($sql)) {
			
			while($plugg = $this->db->fetch_record($plugin_result)) {
	  		// Load the module language
	  		if($this->CheckInstall($plugg['plugin'], $plugg['path'])){
					$plug_list[$plugg['position']][$plugg['id']]['path'] = $plugg['path'];
					$plug_list[$plugg['position']][$plugg['id']]['plugin'] = $plugg['plugin'];
					$plug_list[$plugg['position']][$plugg['id']]['visibility'] = $plugg['visibility'];
					$plug_list[$plugg['position']][$plugg['id']]['collapsable'] = $plugg['collapsable'];
	  		}
	  		$this->modules[$plugg['path']]['id'] = $plugg['id'];
			}
		}
		$this->db->free_result();

		$selected_portal_pos = (unserialize(stripslashes($core->config['pk_permanent_portal']))) ? unserialize(stripslashes($core->config['pk_permanent_portal'])) : array();
		
		foreach($this->positions as $wherevalue){
			

		$scriptname = explode('?', $core->config['start_page']);


			$is_start_page = (basename($_SERVER['PHP_SELF']) == basename($scriptname[0])) ? true : false;

		  // Do not load the thing for the middle & right column if not the start page...
  		if(!($is_start_page || $wherevalue == 'left1' || $wherevalue == 'left2'|| (in_array($wherevalue, $selected_portal_pos) && !defined('IN_ADMIN')))){
  		  $this->tpl->assign_var('PORTAL_'.strtoupper($wherevalue),'');
  		}else{
        $output = $portalpluglist = $module_collapsable = $rightsmanagement = '';
	  		if(is_array($plug_list[$wherevalue])){
	  			foreach($plug_list[$wherevalue] as $id => $module_info){
	          $portalpluglist[$module_info['path']]      = $module_info['plugin'];
	          $rightsmanagement[$module_info['path']]    = $module_info['visibility'];
	          $module_collapsable[$module_info['path']]  = $module_info['collapsable'];
	        }
  			}
  		
  			$this->user->lang = array_merge($this->user->lang, $pluslang->PortalLanguage($portalpluglist));
  			if (is_array($portalpluglist)){
  				foreach($portalpluglist as $portiplugg=>$isplugin){
						// Load the Module
						if($isplugin){
							$modulfile = $this->root_path . 'plugins/'.$isplugin.'/portal/' . $portiplugg .'.php';
						}else{
							$modulfile = $this->root_path . 'portal/' . $portiplugg .'/module.php';
						}
						if (file_exists($modulfile)){
							require($modulfile);
							$modulefunction = $portiplugg.'_module';
							$modtitlelang   = ($isplugin) ? $this->user->lang[$portiplugg] : $this->user->lang[$portiplugg];
							$this->modules[$portiplugg]['settings'] = $portal_module[$portiplugg]['settings'];
  				        
							// Rights system
							if($rightsmanagement[$portiplugg] == '0'){
								$portmoduleperm = true;
							}elseif($rightsmanagement[$portiplugg] == '1'){
								$portmoduleperm = ($this->user->data['username']=="") ? true : false;
							}elseif($rightsmanagement[$portiplugg] == '2'){
								$portmoduleperm = ($this->user->data['username']) ? true : false;
							}
  
							// Generate the Output
							$output .= $this->ModuleStyle($modulefunction(), $portiplugg, $modtitlelang, $portmoduleperm, $module_collapsable[$portiplugg]);			        
						}	        	        
  				}
  			}
  		
  			//Dynamic show the third column if we have output for it		
  			if ( ($wherevalue == 'right') and (strlen($output)>1)) {
  				$this->THIRD_C = true ; 
  				$output = $siteDisplay->right.$output;
  			}		
  			
  			if ( ($wherevalue == 'left1')) {				
  				$output.= $siteDisplay->content;
  			}			
  					
  			if ( ($wherevalue == 'left2')) {				
  				$output.= $siteDisplay->left;
  			}
  			$this->tpl->assign_var('PORTAL_'.strtoupper($wherevalue),$output);
  			}
		  } // end for each				
	}  //end function
  
  
  // The Module Style
  function ModuleStyle($htmlout, $ccid, $name, $perm=false, $collapsable='1'){
    if($collapsable == '1'){
    	$this->jquery->Collapse('portalbox'.$ccid);
    }
    
    $out = '<span id="portalbox'.$ccid.'"><table width="100%" border="0" cellspacing="1" cellpadding="2" class="forumline">
              <tr>
                <th class="smalltitle" align="left">';
    $out .= '<span class="toggle_button" style="display:inline-block;width:16px;height:16px;vertical-align:middle;"></span>&nbsp;';
    if($this->isAdmin && $this->modules[$ccid]['id'] > 0 && $this->modules[$ccid]['settings'] == 1){
    	$out .= '<span id="portal_fe_edit" onclick="fe_portalsettings(\''.$this->modules[$ccid]['id'].'\')"><img src="'.$this->root_path.'images/global/edit.png" alt="'.$this->user->lang['portalplugin_settings'].'"></span> ';
    }
    $out .= '<span align="center" id="txt'.$ccid.'">'.$name.'</span>
                </th>
              </tr>
              <tr class=row1>
                <td>
                  <div class="toggle_container">'.$htmlout.'</div>
                </td>
              </tr>
            </table><br/></span>';
  	
  	// if the user is not signed in:	
    return ($perm) ? $out : '';
  }
  
  function InstallModule($d_plugin_code, $portal_module, $plugin=''){
    if ($portal_module[$d_plugin_code] && isset($portal_module[$d_plugin_code])){
      $install_sett = $portal_module[$d_plugin_code]['install'];
      $myValues     = $portal_module[$d_plugin_code];
      $query = "INSERT INTO __portal 
                (name, path, settings, plugin, enabled, position, 
                number, visibility, collapsable) 
                VALUES 
                ('".$myValues['name']."', '".$myValues['path']."', 
                '".(($myValues['settings']) ? $myValues['settings'] : 0)."', '".$plugin."',
                '".(($install_sett['autoenable']) ? $install_sett['autoenable'] : 0)."',
                '".(($install_sett['defaultposition']) ? $install_sett['defaultposition'] : '')."',
                '".(($install_sett['defaultnumber']) ? $install_sett['defaultnumber'] : '')."',
                '".(($install_sett['visibility']) ? $install_sett['visibility'] : 0)."',
                '".(($install_sett['collapsable']) ? $install_sett['collapsable'] : 1)."')";

      $this->db->query($query);
      
      // now, do the custom code...
      if(is_array($install_sett['customsql']) && count($install_sett['customsql'])>0){
        foreach($install_sett['customsql'] as $DOsql){
          $this->db->query($DOsql);
        }
      }
    }
  }
  
  function EnablePortalModules(){
    if (isset($_POST['processid'])){
      foreach($_POST['processid'] as $plugID){
				$pos = $this->in->getArray('pos', 'string');
				$numbers = $this->in->getArray('sort', 'int');
				foreach ($numbers as $key=>$value){
;
					if (isset($value[$plugID])){
						$number = $key;
					}
				}
				
        $enabled_value = ($_POST['enabled'][$plugID]) ? 1 : 0;
        $collaps_value = ($_POST['collapsable'][$plugID]) ? 1 : 0;
        $sql = "UPDATE __portal SET enabled='".$enabled_value."', position='".$this->db->escape($pos[$plugID])."', visibility='".$_POST['rights'][$plugID]."', number='".$this->db->escape($number)."', collapsable='".$collaps_value."' WHERE id='".$plugID."';";
        $this->db->query($sql);
      }
    }
  }
  
  // Check if the Portal Module is still installed
  // Checks plugin if plugin bundled module
  function CheckInstall($plugin, $path)
  {
    global $pm, $game, $eqdkp_root_path;
    
    // checks if its a plugin bundle or not:
    $cwd = ($plugin) ? $this->root_path . 'plugins/'.$plugin.'/portal/'.$path.'.php' : $this->root_path . 'portal/' . $path .'/module.php';
    
    // normal portal module
    $uninstall  = (!is_file($cwd) || ($plugin && !$pm->check(PLUGIN_INSTALLED, $plugin))) ? true : false;
    
    // Delete it!
    if ($uninstall){
      // uninstall addon
      $sql = "DELETE FROM __portal WHERE path='".$path."';";
      $this->db->query($sql);
      return 0;
    }else{
      return $cwd;
    }
  }
  
  function InstallIfRequired()
  {
    global $pm, $game, $eqdkp_root_path;
    $plugin = array();
    
    $sql = "SELECT * FROM __portal";
    if ($plugin_result = $this->db->query($sql)) { 
      while($plugg = $this->db->fetch_record($plugin_result)) {
        $plugin[$plugg['path']] = $plugg['path'];
        $cwd = $this->CheckInstall($plugg['plugin'],$plugg['path']);
        if (file_exists($cwd)) 
        {
        	include($cwd);	
        }        
        $portal_module[$plugg['path']]['settings']  = (sizeof($portal_settings[$plugg['path']]) > 0 ) ? '1' : '0';
      }
    }
    
    //EQDKP PORTAL MODULES
    // Search for plugins and make sure they are registered
    if ($dir = @opendir($this->root_path . 'portal/') ){
      while ($d_plugin_code = @readdir($dir) ) {
        $cwd = $this->root_path . 'portal/' . $d_plugin_code;
        if (valid_folder($cwd)){
          if (in_array($d_plugin_code, $plugin)){
            continue;
          }else{
            include($this->root_path.'portal/'.$d_plugin_code.'/module.php');
            $portal_module[$d_plugin_code]['settings']  = (sizeof($portal_settings[$d_plugin_code]) > 0 ) ? '1' : '0';
            $this->InstallModule($d_plugin_code, $portal_module);
          }
        }
        unset($d_plugin_code, $cwd);
      } // readdir
    } // opendir
    else {
    	print "opendir didn't work.<br>";
    }
    
    // EQDKP PLUGIN PORTAL MODULES
    foreach ( $pm->get_plugins(PLUGIN_INSTALLED) as $plugin_code => $plugin_object ){
      $plugin_path = ( $plugin_object->get_data('path') !== false ) ? $plugin_object->get_data('path') : $plugin_code;
      foreach($plugin_object->get_portal_modules() as $module_name){
        if(!in_array($module_name, $plugin)){
          include($this->root_path . 'plugins/'.$plugin_path.'/portal/'.$module_name.'.php');
          $portal_module[$plugin_path]['settings']  = (sizeof($portal_settings[$plugin_path]) > 0 ) ? '1' : '0';
          $this->InstallModule($module_name, $portal_module, $plugin_path);
        }
      }
    }

    return $portal_module;
  }
  	
}	
?>
