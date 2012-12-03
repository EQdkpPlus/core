<?php
 /*
 * Project:     EQdkp-Plus
 * License:     Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:       2008
 * Date:        $Date$
 * -----------------------------------------------------------------------
 * @author      $Author$
 * @copyright   2006-2008 Corgan - Stefan Knaak | Wallenium & the EQdkp-Plus Developer Team
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
  var $positions = array('left1', 'left2', 'right', 'middle');
  var $THIRD_C = false;
  
	function portal()
	{
		global $eqdkp_root_path, $db, $tpl, $user, $eqdkp, $pluslang,$conf_plus;
		$plang = (is_array($plang)) ? $plang : array();
		$this->EnablePortalModules();   // Update the Portal Modules...
		
		// Site Display Thing
		require_once($eqdkp_root_path . 'pluskernel/include/siteDisplay.class.php');
		$siteDisplay = new siteDisplay();
		
		//RSS Parser TEMP INPUT... moved to a module some day.
		if(!$conf_plus['pk_showRss'] == 1  )
		{
				include_once($eqdkp_root_path . 'pluskernel/include/rss.class.php');
				$rss = new rss();
		}
		// end of TEMP INPUT
		
		foreach($this->positions as $wherevalue)
		{
		  	$output = $portalpluglist = '';
			$sql = "SELECT path, plugin FROM __portal WHERE enabled='1' AND position='".strtolower($wherevalue)."' ORDER BY number";
			if ($plugin_result = $db->query($sql)) 
			{ 
				while($plugg = $db->fetch_record($plugin_result)) 
				{
		  			// Load the module language
		  			if($this->CheckInstall($plugg['plugin'], $plugg['path']))
		  			{
		    			$portalpluglist[$plugg['path']] = $plugg['plugin'];
		  			}
				}
			}
		
			$plang = $pluslang->PortalLanguage($portalpluglist);
			if (is_array($portalpluglist))
			{
				foreach($portalpluglist as $portiplugg=>$isplugin)
				{
				    // Load the Module
				    if($isplugin)
				    {
			    		$modulfile = $eqdkp_root_path . 'plugins/'.$isplugin.'/portal/' . $portiplugg .'.php';
			  		}else
			  		{
			    		$modulfile = $eqdkp_root_path . 'portal/' . $portiplugg .'/module.php';
			  		}
				    if (file_exists($modulfile)) 
				    {
				        include_once($modulfile);
				        $modulefunction = $portiplugg.'_module';
				        $jsouttmpl[]    = "animatedcollapse.addDiv('".$portiplugg."', 'persist=1,hide=0');";
				        $modtitlelang   = ($isplugin) ? $user->lang[$portiplugg] : $plang[$portiplugg];
				        $portmoduleperm = ($portal_module[$portiplugg]['signedin'] == '1') ? true : false;
				        $output .= $this->ModuleStyle($modulefunction(), $portiplugg, $modtitlelang, $portmoduleperm);			        
				    }	        	        
				}      	
			}
		
			//Dynamic show the third column if we have output for it		
			if ( ($wherevalue == 'right') and (strlen($output)>1)) 
			{
				$this->THIRD_C = true ; 
			}			
					
			if ( ($wherevalue == 'left2')) 
			{				
				$output.= $rss->output_left;
				$output.= $siteDisplay->left;
			}
			
			$tpl->assign_var('PORTAL_'.strtoupper($wherevalue),$output);
		} // end for each
		
		$jsout = '<script type="text/javascript">';
		if (is_array($jsouttmpl)) 
		{
			foreach($jsouttmpl as $pthings)
		    {
		      $jsout .= $pthings;
		    }    	
		}
		
		$jsout .= 'animatedcollapse.init();</script>';
		$tpl->assign_var('PORTAL_JAVASCRIPT',$jsout);
				
	}  //end function
  
  
  // The Module Style
  function ModuleStyle($htmlout, $ccid, $name, $perm)
  {
    global $eqdkp_root_path, $user;
    $out = '<table width="100%" border="0" cellspacing="1" cellpadding="2" class="forumline">
              <tr>
                <th class="smalltitle" align="left">
                  <a href="javascript:animatedcollapse.toggle(\''.$ccid.'\')"><img id="img'.$ccid.'" src="'.$eqdkp_root_path.'pluskernel/images/toggleportal.png" /></a>
                  <span align="center" id="txt'.$ccid.'">'.$name.'</span>
                </th>
              </tr>
              <tr class=row1>
                <td>
                  <div id="'.$ccid.'" style="display:show">'.$htmlout.'</div>
                </td>
              </tr>
            </table><br/>';
  	
  	// if the user is not signed in:	
    return ($perm && $user->data['username']=="") ? '' : $out;
  }
  
  function InstallModule($d_plugin_code, $portal_module, $plugin='')
  {
    global $db;
    if ($portal_module[$d_plugin_code] && isset($portal_module[$d_plugin_code])){
      $install_sett = $portal_module[$d_plugin_code]['install'];
      $query = $db->build_query('INSERT', array(
              'name'        => $portal_module[$d_plugin_code]['name'],
              'path'        => $portal_module[$d_plugin_code]['path'],
              'settings'    => ($portal_module[$d_plugin_code]['settings']) ? $portal_module[$d_plugin_code]['settings'] : 0,
              'plugin'      => $plugin,
              'enabled'     => ($install_sett) ? $install_sett['autoenable'] : 0,
              'position'    => ($install_sett) ? $install_sett['defaultposition'] : '',
              'number'      => ($install_sett) ? $install_sett['defaultnumber'] : '',
            )
          );
      $db->query('INSERT INTO __portal ' . $query);
    }
  }
  
  function EnablePortalModules()
  {
    global $db;
    if (isset($_POST['processid'])){
      foreach($_POST['processid'] as $plugID){
        $enabled_value = ($_POST['enabled'][$plugID]) ? 1 : 0;
        $sql = "UPDATE __portal SET enabled='".$enabled_value."', position='".$_POST['position'][$plugID]."', number='".$_POST['number'][$plugID]."' WHERE id='".$plugID."';";
        $db->query($sql);
      }
    }
  }
  
  // Check if the Portal Module is still installed
  // Checks plugin if plugin bundled module
  function CheckInstall($plugin, $path)
  {
    global $db, $pm, $eqdkp_root_path;
    $cwd = $eqdkp_root_path . 'portal/' . $path .'/module.php';
    
    // normal portal module
    $uninstall  = (!is_file($cwd) && !$plugin) ? true : false;
    $uninstall  = ($plugin && !$pm->check(PLUGIN_INSTALLED, $plugin)) ? true : $uninstall;
    if ($uninstall){
      // uninstall addon
      $sql = "DELETE FROM __portal WHERE path='".$path."';";
      $db->query($sql);
      return 0;
    }else{
      return ($plugin) ? $eqdkp_root_path . 'plugins/'.$plugin.'/portal/'.$path.'.php' : $cwd;
    }
  }
  
  function InstallIfRequired()
  {
    global $db, $pm, $eqdkp_root_path;
    $plugin = array();
    
    $sql = "SELECT * FROM __portal";
    if ($plugin_result = $db->query($sql)) { 
      while($plugg = $db->fetch_record($plugin_result)) {
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
    if ($dir = @opendir($eqdkp_root_path . 'portal/') ){
      while ($d_plugin_code = @readdir($dir) ) {
        $cwd = $eqdkp_root_path . 'portal/' . $d_plugin_code;
        if (valid_folder($cwd)){
          if (in_array($d_plugin_code, $plugin)){
            continue;
          }else{
            include($eqdkp_root_path.'portal/'.$d_plugin_code.'/module.php');
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
    if ( $dir = @opendir($eqdkp_root_path . 'plugins/') ){
      while ( $d_plugin_code = @readdir($dir) ){
        $cwd = $eqdkp_root_path . 'plugins/' . $d_plugin_code;
        if ( valid_folder($cwd)){
          if($pm->check(PLUGIN_INSTALLED, $d_plugin_code)){
            $plugmoduleslist = $eqdkp_root_path . 'plugins/'.$d_plugin_code.'/portal/modules.php';
            if(is_file($plugmoduleslist)){
              include($plugmoduleslist);
      
              foreach($pluginmodules as $modulenames){
                if(!in_array($modulenames,$plugin)){
                  include($eqdkp_root_path . 'plugins/'.$d_plugin_code.'/portal/'.$modulenames.'.php');
                  $portal_module[$d_plugin_code]['settings']  = (sizeof($portal_settings[$d_plugin_code]) > 0 ) ? '1' : '0';
                  $this->InstallModule($modulenames, $portal_module, $d_plugin_code);
                }
              }
            }
          }
        }
      }
    }
    return $portal_module;
  }
  	
}	
?>
