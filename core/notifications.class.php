<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2009
 * Date:		$Date$
 * -----------------------------------------------------------------------
 * @author		$Author$
 * @copyright	2006-2011 EQdkp-Plus Developer Team
 * @link		http://eqdkp-plus.com
 * @package		eqdkp-plus
 * @version		$Rev$
 * 
 * $Id$
 */

if ( !defined('EQDKP_INC') ){
	header('HTTP/1.0 404 Not Found');exit;
} 

class notifications extends gen_class {

	public static $dependencies = array('pm');
	
	private $notifications = array();
	//$strType = 'red', 'green', 'yellow'
	public function add($strType, $strPlugin, $strMessage, $strLink, $intCount = 1){
		if (!isset($this->notifications[$strType])) $this->notifications[$strType] = array();
		if (!isset($this->notifications[$strType][$strPlugin])) $this->notifications[$strType][$strPlugin] = array();
		$this->notifications[$strType][$strPlugin][] = array(
			'message'	=> $strMessage,
			'link'		=> $strLink,
			'count'		=> $intCount,
		);
	}
	
	public function get($strType){
		if (isset($this->notifications[$strType])){
			$html = '';
			$intCount = 0;
			foreach($this->notifications[$strType] as $pluginname => $entrys){
				
				$tmp_html = '';
				$intPluginCount = 0;
				foreach($entrys as $data){
					$tmp_html .= '<li><a href="'.$data['link'].'">'.$data['message'].'</a></li>';
					$intCount += (int)$data['count'];
					$intPluginCount += (int)$data['count'];
				}
				$html .= '<li class="nav-header">'.$pluginname.' ('.$intPluginCount.')</li>'.$tmp_html;
			}
		} else {
			$html = '';
			$intCount = 0;
		}
		return array('count' => $intCount, 'html' => $html);
	}

}
?>