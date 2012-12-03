<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2010
 * Date:		$Date$
 * -----------------------------------------------------------------------
 * @author		$Author$
 * @copyright	2006-2010 EQdkp-Plus Developer Team
 * @link		http://eqdkp-plus.com
 * @package		eqdkp-plus
 * @version		$Rev$
 * 
 * $Id$
 */

if ( !defined('EQDKP_INC') )
{
	header('HTTP/1.0 404 Not Found');exit;
}
class mmocms_config
{

		protected $blnIsModified	= false;
		protected $config			= array();

	public function __construct($pcache=false, $db=false)	{
		global $eqdkp_root_path;

		// Some strange things for include in ITT...
		if(!$pcache)	{ global $pcache; }
		if(!$db) 		{ global $db; }

		$this->pcache		= $pcache;
		$this->db			= $db;
		$this->root_path	= $eqdkp_root_path;
	}

	public function __destruct(){
		if (!$this->blnIsModified){
			return;
		}

		// check if the pcache is already initiated, if not, load it!
		if(!is_object($this->pcache)){
			require_once($this->root_path . 'core/file_handler/file_handler.class.php');
			$this->pcache	= new file_handler;
		}
		$this->config_save();
	}

	public function set_config($config_name, $config_value='', $plugin=''){
		$this->blnIsModified = true;
		if(is_array($config_name)){
			foreach($config_name as $d_name => $d_value){
				$this->set_config($d_name, $d_value, $plugin);
			}
		}else{
			if($plugin){
				$this->config[$plugin][$config_name]	= $config_value;
			}else{
				$this->config[$config_name]						= $config_value;
			}
		}
	}

	public function get_config($plugin=''){
		if(count($this->config) < 1){
			$file = $this->pcache->FolderPath('config', 'eqdkp')."localconf.php";
			if(is_file($file)){
				include($file);
				$this->config = $localconf;
			}else{
				// Load from Database..
				$this->get_dbconfig();
			}
		}
		return ($plugin) ? $this->config[$plugin] : $this->config;
	}

	public function del_config($config_name, $plugin=''){
		$this->blnIsModified = true;
		if(is_array($config_name)) {
			foreach($config_name as $d_name) {
				$this->config_del($d_name, $plugin);
			}
		}else{
			if($plugin){
				if($config_name){
					unset($this->config[$plugin][$config_name]);
				}else{
					unset($this->config[$plugin]);
				}
			}else{
				unset($this->config[$config_name]);
			}
			return true;
		}
		return false;
		}

	public function get_dbconfig(){
		if(!is_object($this->db)){return true;}
		$this->blnIsModified = true;
		$result = $this->db->query("SELECT * FROM __backup_cnf;");
		while($row = $this->db->fetch_record($result) ){
			if($row['config_plugin']){
				$this->config[$row['config_plugin']][$row['config_name']] = $row['config_value'];
			}else{
				$this->config[$row['config_name']] = $row['config_value'];
			}
		}
		$this->db->free_result($result);
		return $this->config;
	}

	public function install_set($array){
		$this->config_save($array);
	}

	private function save_backup($array){

		if(is_array($array)){
			foreach($array as $name=>$value){
				if(is_array($value)){
					// Plugin..
					foreach($value as $pname=>$pvalue){

						$this->db->query("REPLACE INTO __backup_cnf :params", array(
							'config_name'	=> $pname,
							'config_value'	=> $pvalue,
							'config_plugin'	=> $name
						));
					}
				}else{

					$this->db->query("REPLACE INTO __backup_cnf :params", array(
						'config_name'	=>	$name,
						'config_value'	=>	$value
					));
				}
			}
		}
	}

	private function config_save($manual=false){
		$this->config = (is_array($manual) ? $manual : $this->config);

		// add the database backup
		$this->save_backup($this->config);

		// Build the plain file config
		$file = $this->pcache->FolderPath('config', 'eqdkp')."localconf.php";
		$data = "<?php\n";
		$data .= "if (!defined('EQDKP_INC')){\n\tdie('You cannot access this file directly.');\n}\n";
		$data .= '$localconf = ';
		$data .= var_export($this->config, true);
		$data .= "\n?";
		$data .= ">";
		$this->pcache->putContent($data, $file);    
	}
}	
?>