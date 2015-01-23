<?php
/*	Project:	EQdkp-Plus
 *	Package:	EQdkp-plus
 *	Link:		http://eqdkp-plus.eu
 *
 *	Copyright (C) 2006-2015 EQdkp-Plus Developer Team
 *
 *	This program is free software: you can redistribute it and/or modify
 *	it under the terms of the GNU Affero General Public License as published
 *	by the Free Software Foundation, either version 3 of the License, or
 *	(at your option) any later version.
 *
 *	This program is distributed in the hope that it will be useful,
 *	but WITHOUT ANY WARRANTY; without even the implied warranty of
 *	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *	GNU Affero General Public License for more details.
 *
 *	You should have received a copy of the GNU Affero General Public License
 *	along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

if(!defined('EQDKP_INC')){
	die('Do not access this file directly.');
}

if(!class_exists('pageobject')){
	class pageobject extends page_generic {
		private $arrVars = array();
		public $strPath = '';
		public $strPathPlain = '';
		public $strPage = '';
		
		protected function set_vars($arrVars){
			if (!isset($this->arrVars['display']) || $this->arrVars['display'] = false) $this->arrVars = $arrVars;
		}
		
		public function get_vars(){
			return $this->arrVars;
		}
		
		public function __construct($pre_check=false, $handler=false, $pdh_call=array(), $params=null, $cb_name='', $url_id='') {
			parent::__construct($pre_check, $handler, $pdh_call, $params, $cb_name, $url_id);
			if (registry::isset_const('url_id')){
				$this->set_url_id((($url_id != "") ? $url_id  : registry::get_const('url_id')), registry::get_const('url_id'));
			}
			
			//Build Path
			$strPath = $this->server_path;
			if (!intval($this->config->get('seo_remove_index'))) $strPath .= 'index.php/';
			$strPagePath = (strlen($this->page_path))  ? $this->page_path : $this->env->path;
			if (strpos($strPagePath, "/") === 0) $strPagePath = substr($strPagePath, 1);
			
			$strPath .= $strPagePath;
			
			$this->strPage = $this->page;
			$this->strPath = $strPath;
			$this->strPathPlain = str_replace($this->server_path, "", $strPath);
			
			$this->action = $strPath.$this->SID.$this->simple_head_url.$this->url_id_ext;
			$this->tpl->assign_vars(array(
				'ACTION'	=> $this->action,
				'PATH'		=> $strPath,
			));
		}
		
		
		//@Override
		protected function process() {
			foreach($this->handler as $key => $process) {
				if($this->in->exists($key) AND !is_array(current($process))) {
					if($this->pre_check && $process['check'] !== false) $this->user->check_auth($process['check']);
						
					if(isset($process['csrf']) && $process['csrf']) {
						$blnResult = $this->checkCSRF($key);
						if (!$blnResult) break;
					}
		
					if(method_exists($this, $process['process'])) $this->$process['process']();
					break;
				} elseif($this->in->get($key) AND is_array(current($process))) {
					foreach($process as $subprocess) {
						if($subprocess['value'] == $this->in->get($key)) {
							if($this->pre_check && $subprocess['check'] !== false) $this->user->check_auth($subprocess['check']);
								
							if(isset($subprocess['csrf']) && $subprocess['csrf']) {
								$blnResult = $this->checkCSRF($key);
								if (!$blnResult) break;
							}
								
							$this->$subprocess['process']();
							break 2;
						}
					}
				}
			}
			if($this->pre_check) $this->user->check_auth($this->pre_check);
			if (!isset($this->arrVars['display']) || $this->arrVars['display'] == false) $this->display();
		}
				
	}
}
?>