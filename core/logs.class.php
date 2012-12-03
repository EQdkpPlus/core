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
if (!class_exists("mmocms_logs")) {
  class mmocms_logs
  {
    var $version  		= '0.0.0';
    var $build    		= '17072008a';
    var $pluginname 	= 'core';
	var $plugins		= array();
		
		function mmocms_logs(){
			global $pm;
			//Create Plugin List
			$this->plugins[] = 'core';
			foreach ($pm->get_plugins() as $key=>$object){
				$this->plugins[] = $key;
			}		
		}
  
    function Clean($timestamp){
      global $db, $user;
      $log_date = time()-($timestamp*24*60*60);
      $sql = 'DELETE FROM __logs WHERE log_date < '.$log_date;
      $db->query($sql);
			return $db->affected_rows();
    }
    
		function Truncate(){
			global $db;
			$db->query("DELETE FROM __logs");
			return $db->affected_rows();
		}
		
		function DeleteLog($log_id){
			global $db;
			$db->query("DELETE FROM __logs WHERE log_id = ".$db->escape($log_id));
			return $db->affected_rows();
		}
		
    public function ChangePlugin($name){
    	$this->pluginname 	= $name;
    }
    
    public function add($tag, $value, $admin_action = true, $plugin = '', $result='{L_SUCCESS}'){
      global $db, $user;
			
			$plugin = ($plugin != '') ? $plugin : $this->pluginname;
			$db->query('INSERT INTO __logs :params', array(
				'log_value'			=> $this->make_log_action($value),
				'log_result'		=> $result,
				'log_tag'			=> $tag,
				'log_date'			=> time(),
				'log_ipaddress'		=> $user->ip_address,
				'log_sid'			=> $user->sid,
				'user_id'			=> $user->data['user_id'],
				'log_plugin'		=> $plugin,
				'log_flag'			=> ($admin_action) ? 1 : 0,
			));
    }
    
    function GetList($log_id='', $plugin=false, $limit=false){
      global $db, $current_order, $pm, $in, $pdh;
			if (!$plugin){
				$plugin = implode("', '", $this->plugins);
			}elseif ($plugin === true){
				$plugin = $this->pluginname;
			}
			
      $start = ( $in->exists('start') ) ? $in->get('start', 0) : 0;
      $sql = 'SELECT * FROM __logs';
			if($log_id){
        $sql .= " WHERE log_id='".$log_id."'";
			 } else {
				$sql .= " WHERE log_plugin IN ('".$plugin."')";
			}

      $current_order['sql'] = ($current_order['sql']) ? $current_order['sql'] : 'log_date desc';
			$limit = ($limit) ? $limit : 100;
      $readysql = $sql.' ORDER BY '.$current_order['sql'].' LIMIT '.$start.','.$limit;
      $result = $db->query($readysql);
      
      while ( $log = $db->fetch_record($result) ){
        $data_out[$log['log_id']] = array(
                'id'        => $log['log_id'],
                'date'      => $log['log_date'],
                'sid'       => $log['log_sid'],
                'tag'       => $this->lang_replace($log['log_tag']), 
                'value'     => $this->lang_replace($log['log_value']),
                'ip'        => $log['log_ipaddress'],
                'result'    => $this->lang_replace($log['log_result']),
                'user'      => $pdh->get('user', 'name', array($log['user_id'])),
                'attached'  => $log['log_attachto'],
								'plugin'		=> $log['log_plugin'],
								'flag'			=> $log['log_flag'],
								'raw'				=> $log,
        );
      }
      return $data_out;
    }
  
    // Language Replacement
    function lang_replace($variable){
      global $user;

      preg_match("/\{L_(.+)\}/", $variable, $to_replace);
      if ( (isset($to_replace[1])) && (isset($user->lang[strtolower($to_replace[1])]))){
        $variable = str_replace('{L_'.$to_replace[1].'}', $user->lang[strtolower($to_replace[1])], $variable);
      }
      return $variable;
    }
    
    function make_log_action($action = array()){
        $str_action = "\$log_action = array(";
        foreach ( $action as $k => $v ){
            $str_action .= "'" . $k . "' => '" . addslashes($v) . "',";
        }
        $action = substr($str_action, 0, strlen($str_action)- 1) . ");";

        // Take the newlines and tabs (or spaces > 1) out of the action
        $action = preg_replace("/[[:space:]]{2,}/", '', $action);
        $action = str_replace("\t", '', $action);
        $action = str_replace("\n", '', $action);
        $action = preg_replace("#(\\\){1,}#", "\\", $action);

        return $action;
    }
  }
}
?>
