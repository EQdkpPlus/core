<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2011
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
if(!defined('EQDKP_INC')) {
	header('HTTP/1.0 404 Not Found');exit;
}
class php_check extends install_generic {
	public static $shortcuts = array('pdl');
	public static $before 		= 'licence';

	public static function before() {
		return self::$before;
	}

	private function getCheckParams(){
		return array(
			'php'		=> array(
				'required'		=> VERSION_PHP_RQ.'+',
				'installed'		=> phpversion(),
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
				'passfail'		=> (ini_get('safe_mode') != '1') ? true : false,
				'adviced_fail'	=> (ini_get('safe_mode') == '1') ? true : false,
				'ignore'		=> true
			),
			'memory'	=> array(
				'required'		=> '60M',
				'installed'		=> (intval(ini_get('memory_limit')) == -1) ? "Unlimited" : ini_get('memory_limit'),
				'passfail'		=> $this->check_php_limit(),
			),
			'curl'		=> array(
				'required'		=> $this->lang['yes'],
				'installed'		=> (function_exists('curl_version')) ? $this->lang['yes'] : $this->lang['no'],
				'passfail'		=> (function_exists('curl_version')) ? true : false,
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
		);
	}
	
	private function check_php_limit($size){
		$installed = ini_get('memory_limit');
		$needed = REQ_PHP_MEMORY;
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
			$allmatched_opt = ($fdata['adviced_fail']) ? true : $allmatched_opt;
		}
		return $allmatched_opt;
	}

	public function get_output() {
		$content = '';
		$phpcheckdata	= $this->getCheckParams();

		// show a message if safemode is on, as we can install eqdkp+ with ftp handler
		if(!$phpcheckdata['safemode']['passfail']){
			$content .='<div style="margin-top: 10px; padding: 0pt 0.7em;" class="ui-state-highlight ui-corner-all">
					<p>'.$this->lang['safemode_warning'].'</p>
				</div>';
		}

		// show a warning if one of the optional steps does not match
		if($this->do_match_opt()){
			$content .='<div style="margin-top: 10px; padding: 0pt 0.7em;" class="ui-state-highlight ui-corner-all">
					<p>'.$this->lang['do_match_opt_failed'].'</p>
				</div>';
		}

		$content .= '<br/>
		<table class="ui-widget" style="border-collapse: collapse;">
			<thead class="ui-state-default">
			<tr>
				<th width="54%">'.$this->lang['table_pcheck_name'].'</th>
				<th width="19%">'.$this->lang['table_pcheck_installed'].'</th>
				<th width="19%">'.$this->lang['table_pcheck_required'].'</th>
				<th width="6%"></th>
			</tr>
			</thead>
			<tbody class="ui-widget-content">';

		foreach($phpcheckdata as $fname=>$fdata){
			if(isset($fdata['adviced_fail'])){
				$passfail_color	= ($fdata['adviced_fail']) ? 'neutral' : (($fdata['passfail']) ? 'positive' : 'negative');
				$passfail_icon	= ($fdata['adviced_fail']) ? 'style/warn.png' : (($fdata['passfail']) ? 'style/ok.png' : 'style/failed.png');
			}else{
				$passfail_color	= ($fdata['passfail']) ? 'positive' : 'negative';
				$passfail_icon	= (($fdata['passfail']) ? 'style/ok.png' : 'style/failed.png');
			}
			$content .= '<tr>
				<td>'.(($this->lang['module_'.$fname]) ? $this->lang['module_'.$fname] : $fname).'</td>
				<td class="'.$passfail_color.'">'.$fdata['installed'].'</td>
				<td class="positive">'.$fdata['required'].'</td>
				<td><img src="'.$passfail_icon.'" alt="passfail" /></td>
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
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_php_check', php_check::$shortcuts);
?>