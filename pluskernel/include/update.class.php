<?php
 /*
 * Project:     EQdkp-Plus
 * License:     Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:       2006
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


if ( !defined('EQDKP_INC') ){
    header('HTTP/1.0 404 Not Found');exit;
}

class UpdateCheck EXTENDS EQdkp_Plugin
{

	/*
	http://vcheck.eqdkp-plus.com/version/pluskernel
	http://vcheck.eqdkp-plus.com/version/charmanager
	http://vcheck.eqdkp-plus.com/version/raidplan
	http://vcheck.eqdkp-plus.com/version/itemspecials
	http://vcheck.eqdkp-plus.com/version/raidbanker
	*/


	// Plugin Version
	var $PlusUpdateClassVersion  = '1.1.0';
	var $PlusUpdateClassBuild    = '1511200701';

	function UpdateCheck($url1, $pluglist, $coltable, $version)
	{
		$this->vcurl        = $url1;
		$this->vcserver     = substr($this->vcurl, 7);
		$this->pluginlist   = $pluglist;
		$this->colortable   = $coltable;
		$this->plusversion  = $version;
	}

	function PlusVersion()
	{
		return $this->plusversion;
	}

	function CheckLink($url)
	{
	  	if($url)
	  	{
	  		$dat = @fsockopen ($this->vcserver, 80, $errno, $errstr, 4);
	    	//$dat = @fopen ($url, "r");
	  	}
	  	if($dat)
	  	{
	    	return true;
	    	fclose($dat);
	  	} else
	  	{
	    	return false;
	  	}
	}

	function BuildVersionArray()
	{
		global $conf_plus, $db, $eqdkp,$urlreader;
		$plusdb = new dbPlus();
		if ($this->CheckLink($this->vcurl))
		{

			if ($conf_plus['pk_updatetime'] && $conf_plus['pk_updatetime'] > (time()-(24*60*60)))
			{
				$sqlplus = 'SELECT * FROM __plus_update';
				$upd_result = $db->query($sqlplus);
				while ( $ddd = $db->fetch_record($upd_result) )
				{
					$versions[$ddd['name']]['name'] = $ddd['realname'];
					$versions[$ddd['name']]['version'] = $ddd['version'];
					$versions[$ddd['name']]['level'] = $ddd['level'];
					$versions[$ddd['name']]['changelog'] = $ddd['changelog'];
					$versions[$ddd['name']]['download'] = $ddd['download'];
					$versions[$ddd['name']]['release'] = $ddd['release'];
				}
			}
			else
			{
				foreach ($this->pluginlist as $value)
				{
					$value2  = $value.'|'.$eqdkp->config['server_name'].$eqdkp->config['server_path'].'|'.EQDKPPLUS_VERSION;
					$getdata = $urlreader->GetURL($this->vcurl.'/version/'.$value2) ;
					$ddarray = $plusdb->CheckDBFields('plus_update', 'name');

					if(!in_array(strtolower($value), $ddarray))
					{
						// insert the new row
						$plusdb->InsertUpdateCache($value);
					}
					$parse = explode('|' ,$getdata);
					$plusdb->UpdateUpdateCache('realname' , $value, $parse[6]);
					$plusdb->UpdateUpdateCache('version' , $value, $parse[0]);
					$plusdb->UpdateUpdateCache('level' , $value, $parse[1]);
					$plusdb->UpdateUpdateCache('changelog' , $value, $parse[2]);
					$plusdb->UpdateUpdateCache('download' , $value, $parse[3]);
					$plusdb->UpdateUpdateCache('release' , $value, $parse[5]);

					// set the versions array:
					$versions[$value]['name']	= $parse[6];
					$versions[$value]['version']	= $parse[0];
					$versions[$value]['level']	= $parse[1];
					$versions[$value]['changelog']	= $parse[2];
					$versions[$value]['download']	= $parse[3];
					$versions[$value]['release']	= $parse[5];

				} // end foreach
				$isplusconfarray = $plusdb->CheckDBFields('plus_config', 'config_name');
				$plusdb->UpdateConfig('pk_updatetime', time(), $isplusconfarray);
			} // end

			return $versions;
		}else
		{
			return false;
		}
	} #end function

	function buildPluginArray()
	{
		global $pm, $user;
		foreach ($this->pluginlist as $value)
		{
			if (isset($pm))
			{
				if($pm->check(PLUGIN_INSTALLED, $value))
				{
					$pluginlist[$value] = 1;
				}
				$pluginlist['pluskernel'] = 1;
			}
		}
		return $pluginlist;
	}

	function ResetUpdater($check)
	{
		global $db;
		if ($check == 'true')
		{
			$sql = "DELETE FROM __plus_config WHERE `config_name` = 'pk_updatetime' LIMIT 1;";
			$db->query($sql);
			return "<script>window.location.reload();</script>";
		}else
		{
			return false;
		}
	}

	function checkPlugins()
	{
		global $db, $user, $pm;
    $pluginscheck['update_available'] = false;
		$arr      = $this->buildPluginArray();
		$versions = $this->BuildVersionArray();
		if ($versions)
		{
			foreach ($arr as $key => $value)
			{
				if ($value == 1 && $key != 'pluskernel')
				{
					if(trim($versions[$key]['version']) == $pm->get_data($key, 'version') || trim($versions[$key]['version']) < $pm->get_data($key, 'version')){
    				}else
    				{
	    				$pluginscheck[$key]['plugin']      = $pm->get_data($key, 'version');
		        	$pluginscheck[$key]['server']      = $versions[$key]['version'];
		        	$pluginscheck[$key]['changelog']   = $versions[$key]['changelog'];
		        	$pluginscheck[$key]['level']       = $versions[$key]['level'];
		        	$pluginscheck[$key]['release']     = $versions[$key]['release'];
		        	$pluginscheck[$key]['download']    = $versions[$key]['download'];
		        	$pluginscheck[$key]['name']        = $versions[$key]['name'];
	    				$pluginscheck['update_available']  = true;
		    		}
				}
				elseif ($key == 'pluskernel')
				{
					if(trim($versions[$key]['version']) == $this->plusversion || trim($versions[$key]['version']) < $this->plusversion)
					{
					}else
					{
            $pluginscheck[$key]['plugin']     = $this->plusversion;
		        $pluginscheck[$key]['server']     = $versions[$key]['version'];
		        $pluginscheck[$key]['changelog']  = $versions[$key]['changelog'];
		        $pluginscheck[$key]['level']      = $versions[$key]['level'];
            $pluginscheck[$key]['release']    = $versions[$key]['release'];
            $pluginscheck[$key]['download']   = $versions[$key]['download'];
            $pluginscheck[$key]['name']       = $versions[$key]['name'];
            $pluginscheck['update_available'] = true;
    				}
				} // if value = 1
			} // foreach
			return $pluginscheck;
		}else
		{
			return false;
		}
	} // function

	function UpdateChecker()
	{
		global $db, $user, $pm, $plang, $updatelevel;
		$toupdate = $this->checkPlugins();
		$plusdb = new dbPlus();
		$conf_plus = $plusdb->InitConfig();
		$text = "<center><table width ='90%'>";
		if($toupdate)
		{
			if ($toupdate['update_available'])
			{
					$text .= "<tr><td width='90px'><img src='images/attention.png' class='reflect' style='float: left;' alt='".$plang['pk_alt_attention']."' \/></td>
								<td><div class='warning'>".$plang['pk_updates_avail']."</div></td>
								</tr><tr><td></td></tr><table><br/>
								<table class='updatetable'><tr><th width='160px'>".$plang['pk_module_name']."</th>
								<th width='70px'>".$plang['pk_inst_version']."</th>
								<th width='70px'>".$plang['pk_act_version']."</th>
								<th width='70px'>".$plang['pk_plugin_level']."</th>
								<th width='70px'>".$plang['pk_release_date']."</th>
								<th width='60px'></th></tr>";
				}else
				{
					$text .= "<tr><td width='90px'><img src='images/ok.png' class='reflect' style='float: left;' alt='".$plang['pk_alt_ok']."' \/></td>
								<td><div class='warning'>".$plang['pk_updates_navail']."</div></td>
								</tr><tr><td></td></tr><table><br/>";
				}

				foreach ($toupdate as $key => $value)
				{
					if ($toupdate['update_available'])
					{
						if ($key != 'update_available' && !empty($key))
						{
							$colortag = (isset($this->colortable[$toupdate[$key]['level']])) ? 'bgcolor="'.$this->colortable[$toupdate[$key]['level']].'"' : '';
							$level_txt = ( $updatelevel[$toupdate[$key]['level']]) ? $updatelevel[$toupdate[$key]['level']] : $plang['pk_level_other'];
							$text .= '<tr '.$colortag.'><td><div class="pluginname">'.$toupdate[$key]['name'].'</div></td>';
							$text .= '<td align="center">'.$toupdate[$key]['plugin'].'</td><td align="center">'.$toupdate[$key]['server'].'</td>
												<td align="center">'.$level_txt.'</td>
												<td align="center">'.date($plang['pk_date_settings'] ,$toupdate[$key]['release']).'</td>
												<td align="center">
													<a href="'.$toupdate[$key]['changelog'].'" target="_blank"><img src="images/changelog.png" border="0" alt="'.$plang['pk_changelog'].'"/></a>
													<a href="'.$toupdate[$key]['download'].'" target="_blank"><img src="images/download.png" border="0" alt="'.$plang['pk_download'].'"/></a>
												</td></tr>';
						} // end if window
					}else
					{
						$upd_status = ($conf_plus['pk_updatecheck'] == 1) ? '<b><font color="green">'.$plang['pk_enabled'].'</font></b>!' : '<b><font color="red">'.$plang['pk_disabled'].'</font></b> '.$plang['pk_auto_updates2'];
						$text .= $plang['pk_no_updates'].'<br/>';

						// Info Box
						$text .= '<br/><center><table width="80%" border="0" cellspacing="1" cellpadding="2">
		  								<tr>
		    							 <td class="row1" width ="54px" ><img class="reflect" src="images/info.png" width ="48px" height="48px" /></td>
		    							 <td class="row1">'.$plang['pk_auto_updates1'].' '.$upd_status.'</td>
		  								</tr>
		  								<tr>
										</table></center>';
					}
				}
			}else
			{
			$text .= "<tr><td width='90px'><img src='images/connect_no.png' class='reflect' style='float: left;' alt='".$plang['pk_alt_error']."' \/></td>
								<td><div class='warning'>".$plang['pk_no_conn_header']."</div></td>
								</tr><tr><td></td></tr><table><br/>";
			$text .= $plang['pk_no_server_conn'].'<br/>';
		}
		$text .= "</table>
		</center>";
		$text .= "<br/><center><a href='updates.php?reset=true'>".$plang['pk_reset_warning']."</a></center>";
		return $text;
	}

	function UpdateAvailable()
	{
		global $user, $conf_plus;
		if ($conf_plus['pk_updatecheck'] == 1)
		{
			$plusdb = new dbPlus();
			$conf_plus = $plusdb->InitConfig();
			if($user->check_auth('a_config_man', false))
			{
				$onloadcheck = $this->checkPlugins();
				return ($onloadcheck['update_available']) ? true : false;
			} // end perm check
		} // end enabled
	}

}// end of class
?>