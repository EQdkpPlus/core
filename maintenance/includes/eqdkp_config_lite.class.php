<?php
/******************************
 * EQdkp
 * Copyright 2009
 * Licensed under the GNU GPL.  See COPYING for full terms.
 * ------------------
 * eqdkp_config_lite.class.php
 * begin: 2009
 *
 * $Id$
 *
 ******************************/

if ( !defined('EQDKP_INC') )
{
    header('HTTP/1.0 404 Not Found');
    exit;
}

class eqdkp_config_lite{
  public $config = array();
	public $row_class = 'row1';

  public function __construct()
  {
    global $db, $settings, $pcache;
		$this->settings		= $settings;
		$this->db			= $db;
		$this->config		= $this->settings->get_config();
		return true;
  }
	
  public function config_set($config_name, $config_value='', $plugin=''){
		$this->settings->set_config($config_name, $config_value, $plugin);
		if(!is_array($config_name)) {
			if($plugin) {
				$this->config[$config_name][$plugin] = $config_value;
			} else {
				$this->config[$config_name] = $config_value;
			}
		} else {
			$this->config = $this->settings->get_config();
		}
	}
  
  public function config_del($config_name)
  {
  	return $this->settings->del_config($config_name, $plugin='');
  }
  
  function BuildLink(){
		$script_name = preg_replace('/^\/?(.*?)\/?$/', '\1', trim($this->config['server_path']));
		$script_name = ( $script_name != '' ) ? $script_name . '/' : '';
		return $this->httpHost().'/'.$script_name;
	}
	
	protected function httpHost(){
		$protocol = ($_SERVER['SSL_SESSION_ID'] || $_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1) ? 'https://' : 'http://';
		$xhost		= preg_replace('/[^A-Za-z0-9\.:-]/', '', $_SERVER['HTTP_X_FORWARDED_HOST']);
		$host			= $_SERVER['HTTP_HOST'];
		if (empty($host)){
			$host  = $_SERVER['SERVER_NAME'];
			$host .= ($_SERVER['SERVER_PORT'] != 80) ? ':' . $_SERVER['SERVER_PORT'] : '';
		}
		return $protocol.(!empty($xhost) ? $xhost . '/' : '').preg_replace('/[^A-Za-z0-9\.:-]/', '', $host);
	}
	
	
	  function switch_row_class($set_new = true){
			$row_class = ($this->row_class == 'row1') ? 'row2' : 'row1';

			if ($set_new){
				$this->row_class = $row_class;
			}
			return $row_class;
    }
		
		function check_auth(){
			global $user, $core;
			
			if (!$user->check_auth('a_maintenance', false)){
				if ($core->config['pk_maintenance_mode'] == '1'){
					redirect('maintenance/maintenance.php');
				} else {
					redirect('index.php');
				}
			}
		}
	
	function create_breadcrump($name, $url = false){
		global $user, $core, $tpl;
			$tpl->assign_block_vars('breadcrumps', array (
				'BREADCRUMP'	=> (($url) ? '<a href="'.$url.'">'.$name.'</a>' : $name)
			
			));
		
	}
	
}

?>