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
		global $eqdkp_root_path, $db, $tpl, $user, $eqdkp, $pluslang, $conf_plus, $wherevalue, $sql_db, $in;
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
		  	// Do not load the thing for the middle & right column if not the start page...
  			if(($wherevalue == 'middle' || $wherevalue == 'right') && basename($_SERVER['PHP_SELF']) != basename($eqdkp->config['start_page']))
  			{
  		  		$tpl->assign_var('PORTAL_'.strtoupper($wherevalue),'');
  			}else
  			{
        		$output = $portalpluglist = $module_collapsable = $rightsmanagement = '';
  				$sql = "SELECT path, plugin, visibility, collapsable FROM __portal WHERE enabled='1' AND position='".strtolower($wherevalue)."' ORDER BY number";
  				if ($plugin_result = $db->query($sql))
  				{
  					while($plugg = $db->fetch_record($plugin_result))
  					{
  						// Load the module language
	  		  			if($this->CheckInstall($plugg['plugin'], $plugg['path']))
	  		  			{
	  		    			$portalpluglist[$plugg['path']]      = $plugg['plugin'];
	  		    			$rightsmanagement[$plugg['path']]    = $plugg['visibility'];
	  		    			$module_collapsable[$plugg['path']]  = $plugg['collapsable'];
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
	  				        $jsouttmpl[]    = ($module_collapsable[$portiplugg] == '1') ? "animatedcollapse.addDiv('".$portiplugg."', 'persist=1,hide=0');" : '';
	  				        $modtitlelang   = ($isplugin) ? $user->lang[$portiplugg] : $plang[$portiplugg];

	  				        // Rights system
	  				        if($rightsmanagement[$portiplugg] == '0')
	  				        {
	                    		$portmoduleperm = true;
	                  		}elseif($rightsmanagement[$portiplugg] == '1')
	                  		{
	                    		$portmoduleperm = ($user->data['username']=="") ? true : false;
	                  		}elseif($rightsmanagement[$portiplugg] == '2')
	                  		{
	                    		$portmoduleperm = ($user->data['username']) ? true : false;
	                  		}

	  				        // Generate the Output
	  				        $output .= $this->ModuleStyle($modulefunction(), $portiplugg, $modtitlelang, $portmoduleperm, $module_collapsable[$portiplugg]);
	  				    }
	  				}
	  			}

	  			//Dynamic show the third column if we have output for it
	  			if ( ($wherevalue == 'right') and (strlen($output)>1))
	  			{
	  				$this->THIRD_C = true ;
	  				$output = $siteDisplay->right.$output;
	  			}

	  			if ( ($wherevalue == 'left1'))
	  			{
	  				$output.= $siteDisplay->content;
	  			}

	  			if ( ($wherevalue == 'left2'))
	  			{
	  				$output.= $rss->output_left;
	  				$output.= $siteDisplay->left;
	  			}

	  			$tpl->assign_var('PORTAL_'.strtoupper($wherevalue),$output);
	  	}	// end of if right collumn
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
  function ModuleStyle($htmlout, $ccid, $name, $perm=false, $collapsable='1')
  {
    global $eqdkp_root_path, $user;
    $out = '<table width="100%"border="0" cellspacing="1" cellpadding="2" class="forumline">
              <tr>
                <th class="smalltitle" align="left">';
    $out .= ($collapsable == '1') ? '<a href="javascript:animatedcollapse.toggle(\''.$ccid.'\')"><img id="img'.$ccid.'" src="'.$eqdkp_root_path.'pluskernel/images/toggleportal.png" /></a>' : '';
    $out .= '      <span align="center" id="txt'.$ccid.'">'.$name.'</span>
                </th>
              </tr>
              <tr class=row1>
                <td>
                  <div id="'.$ccid.'" style="display:show">'.$htmlout.'</div>
                </td>
              </tr>
            </table><br/>';

  	// if the user is not signed in:
    return ($perm) ? $out : '';
  }

  function InstallModule($d_plugin_code, $portal_module, $plugin='')
  {
    global $db, $in;
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

      $db->query($query);

      // now, do the custom code...
      if(is_array($install_sett['customsql']) && count($install_sett['customsql'])>0){
        foreach($install_sett['customsql'] as $DOsql){
          $db->query($DOsql);
        }
      }
    }
  }

  function EnablePortalModules()
  {
    global $db;
    if (isset($_POST['processid'])){
      foreach($_POST['processid'] as $plugID){
        $enabled_value = ($_POST['enabled'][$plugID]) ? 1 : 0;
        $collaps_value = ($_POST['collapsable'][$plugID]) ? 1 : 0;
        $sql = "UPDATE __portal SET enabled='".$enabled_value."', position='".$_POST['position'][$plugID]."', visibility='".$_POST['rights'][$plugID]."', number='".$_POST['number'][$plugID]."', collapsable='".$collaps_value."' WHERE id='".$plugID."';";
        $db->query($sql);
      }
    }
  }

  // Check if the Portal Module is still installed
  // Checks plugin if plugin bundled module
  function CheckInstall($plugin, $path)
  {
    global $db, $pm, $eqdkp_root_path, $in;

    // checks if its a plugin bundle or not:
    $cwd = ($plugin) ? $eqdkp_root_path . 'plugins/'.$plugin.'/portal/'.$path.'.php' : $eqdkp_root_path . 'portal/' . $path .'/module.php';

    // normal portal module
    $uninstall  = (!is_file($cwd) || ($plugin && !$pm->check(PLUGIN_INSTALLED, $plugin))) ? true : false;

    // Delete it!
    if ($uninstall){
      // uninstall addon
      $sql = "DELETE FROM __portal WHERE path='".$path."';";
      $db->query($sql);
      return 0;
    }else{
      return $cwd;
    }
  }

  function InstallIfRequired()
  {
    global $db, $pm, $eqdkp_root_path, $in;
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
              $pluginmodules = array();
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