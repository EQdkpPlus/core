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

if(!defined('EQDKP_INC')) {
	header('HTTP/1.0 404 Not Found');exit;
}
class php_check extends install_generic {
	public static $before 		= 'licence';

	public static function before() {
		return self::$before;
	}

	private function getCheckParams(){
		return array(
			'php'		=> array(
				'required'		=> VERSION_PHP_RQ.'+',
				'installed'		=> phpversion(),
				'recommended'	=> VERSION_PHP_REC,
				'passfail'		=> (version_compare(PHP_VERSION, VERSION_PHP_RQ, '>=')) ? true : false,
				'adviced_fail'	=> (version_compare(PHP_VERSION, VERSION_PHP_REC, '<=')) ? true : false
			),
			'mysql'		=> array(
				'required'		=> $this->lang['yes'],
				'installed'		=> (extension_loaded('mysql')) ? $this->lang['yes'] : $this->lang['no'],
				'passfail'		=> (extension_loaded('mysql')) ? true : false
			),
			'zLib'		=> array(
				'required'		=> $this->lang['yes'],
				'installed'		=> (extension_loaded('zlib')) ? $this->lang['yes'] : $this->lang['no'],
				'passfail'		=> (extension_loaded('zlib')) ? true : false
			),
			'safemode'	=> array(
				'required'		=> $this->lang['no'],
				'installed'		=> (ini_get('safe_mode') != '1') ? $this->lang['no'] : $this->lang['yes'],
				'passfail'		=> true,
				'adviced_fail'	=> (ini_get('safe_mode') == '1') ? true : false,
				'ignore'		=> true,
			),
			'memory'	=> array(
				'required'		=> REQ_PHP_MEMORY,
				'installed'		=> (intval(ini_get('memory_limit')) == -1) ? "Unlimited" : ini_get('memory_limit'),
				'passfail'		=> $this->check_php_limit(REQ_PHP_MEMORY),
				'adviced_fail'	=> ($this->check_php_limit(REQ_PHP_MEMORY_REC) ? false :true),
				'recommended'	=> REQ_PHP_MEMORY_REC,
			),
			'curl'		=> array(
				'required'		=> $this->lang['yes'],
				'installed'		=> (function_exists('curl_version')) ? $this->lang['yes'] : $this->lang['no'],
				'passfail'		=> true,
				'adviced_fail'	=> (!function_exists('curl_version')) ? true : false,
				'ignore'		=> true
			),
			'fopen'		=> array(
				'required'		=> $this->lang['yes'],
				'installed'		=> (function_exists('fopen')) ? $this->lang['yes'] : $this->lang['no'],
				'passfail'		=> (function_exists('fopen')) ? true : false
			),
			'hash'		=> array(
				'required'		=> $this->lang['yes'],
				'installed'		=> (function_exists('hash')) ? $this->lang['yes'] : $this->lang['no'],
				'passfail'		=> (function_exists('hash')) ? true : false
			),
			'autoload'	=> array(
				'required'		=> $this->lang['yes'],
				'installed'		=> (function_exists('spl_autoload_register')) ? $this->lang['yes'] : $this->lang['no'],
				'passfail'		=> (function_exists('spl_autoload_register')) ? true : false
			),
			'json'		=> array(
				'required'		=> $this->lang['yes'],
				'installed'		=> (extension_loaded('json')) ? $this->lang['yes'] : $this->lang['no'],
				'passfail'		=> (extension_loaded('json')) ? true : false
			),
		);
	}
	
	private function check_php_limit($needed){
		$installed = ini_get('memory_limit');
		if (intval($installed) == -1) return true;
		return ($this->convert_hr_to_bytes($installed) >= $this->convert_hr_to_bytes($needed)) ? true : false;
	}
	
	function convert_hr_to_bytes( $size ) {
		( $bytes = (float) $size )
		&& ( $last = strtolower( substr( $size, -1 ) ) )
		&& ( $pos = strpos( ' kmg', $last , 1 ) )
		&& $bytes *= pow( 1024, $pos )
		;
		return round( $bytes );
	}

	private function do_match_req(){
		$allmatched_req		= true;
		foreach($this->getCheckParams() as $fname=>$fdata){
			$allmatched_req = ($fdata['passfail'] || $fdata['ignore']) ? $allmatched_req : false;
		}
		return $allmatched_req;
	}

	private function do_match_opt(){
		$allmatched_opt		= false;
		foreach($this->getCheckParams() as $fname=>$fdata){
			$allmatched_opt = (isset($fdata['adviced_fail']) && $fdata['adviced_fail']) ? true : $allmatched_opt;
		}
		return $allmatched_opt;
	}

	public function get_output() {
		$content = '';
		$phpcheckdata	= $this->getCheckParams();
		
		if(!$this->do_match_req()){
			$content .='<div class="infobox infobox-large infobox-red clearfix">
			<i class="fa fa-exclamation-triangle fa-4x pull-left"></i> <strong>'.$this->lang['phpcheck_failed'].'</strong>
		</div>';
		} else {

			// show a message if safemode is on, as we can install eqdkp+ with ftp handler
			if(!$phpcheckdata['safemode']['passfail']){
				$content .='<div style="margin-top: 10px; padding: 0pt 0.7em;" class="ui-state-highlight ui-corner-all">
						<p>'.$this->lang['safemode_warning'].'</p>
					</div>';
			}
	
			// show a warning if one of the optional steps does not match
			if($this->do_match_opt()){
				$content .='<div class="infobox infobox-large infobox-orange clearfix">
			<i class="fa fa-exclamation-triangle fa-4x pull-left"></i> <strong>'.$this->lang['do_match_opt_failed'].'</strong>
		</div>';
			}
		}

		$content .= '<br/>
		<table class="colorswitch" style="border-collapse: collapse;">
			<thead>
			<tr>
				<th width="54%">'.$this->lang['table_pcheck_name'].'</th>
				<th width="13%">'.$this->lang['table_pcheck_installed'].'</th>
				<th width="13%">'.$this->lang['table_pcheck_rec'].'</th>
				<th width="13%">'.$this->lang['table_pcheck_required'].'</th>
				<th width="6%"></th>
			</tr>
			</thead>
			<tbody>';

		foreach($phpcheckdata as $fname=>$fdata){
			if(isset($fdata['adviced_fail']) && $fdata['passfail']){
				$passfail_color	= ($fdata['adviced_fail']) ? 'neutral' : (($fdata['passfail']) ? 'positive' : 'negative');
				$passfail_icon	= ($fdata['adviced_fail']) ? 'fa-exclamation-triangle' : (($fdata['passfail']) ? 'fa-check-circle' : 'fa-times-circle');
			}else{
				$passfail_color	= ($fdata['passfail']) ? 'positive' : 'negative';
				$passfail_icon	= (($fdata['passfail']) ? 'fa-check-circle' : 'fa-times-circle');
			}
			$content .= '<tr>
				<td>'.(($this->lang['module_'.$fname]) ? $this->lang['module_'.$fname] : $fname).'</td>
				<td class="'.$passfail_color.'">'.$fdata['installed'].'</td>
				<td class="positive">'.((isset($fdata['recommended'])) ? $fdata['recommended'] : $fdata['required']).'</td>		
				<td class="positive">'.$fdata['required'].'</td>
				<td><i class="fa '.$passfail_icon.' fa-2x '.$passfail_color.'"></i></td>
			</tr>';
		}
		$content .='</tbody>
				</table>';

		// if do_match_req is true, all functions matched. if not, we failed..
		if($this->do_match_req()){
			$this->pdl->log('install_success', $this->lang['phpcheck_success']);
		}else{
			$this->pdl->log('install_error', $this->lang['phpcheck_failed']);
		}

		return $content;
	}

	public function get_filled_output() {
		return $this->get_output();
	}

	public function parse_input() {
		return $this->do_match_req();
	}
}
?>