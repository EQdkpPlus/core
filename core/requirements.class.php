<?php
/*	Project:	EQdkp-Plus
 *	Package:	EQdkp-plus
 *	Link:		http://eqdkp-plus.eu
 *
 *	Copyright (C) 2006-2016 EQdkp-Plus Developer Team
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
if(!class_exists('requirements')) {
	class requirements extends gen_class {
		private $checks = array();
		private $blnWritable = NULL;

		public function __construct(){
			$this->checks = array(
				'php'		=> array(
					'required'		=> VERSION_PHP_RQ.'+',
					'installed'		=> phpversion(),
					'recommended'	=> VERSION_PHP_REC,
					'passfail'		=> (version_compare(PHP_VERSION, VERSION_PHP_RQ, '>=')) ? true : false,
					'adviced_fail'	=> (version_compare(PHP_VERSION, VERSION_PHP_REC, '<=')) ? true : false
				),
				'mysql'		=> array(
					'required'		=> $this->user->lang('yes'),
					'installed'		=> (extension_loaded('mysqli') || extension_loaded('pdo_mysql')) ? $this->user->lang('yes') : $this->user->lang('no'),
					'passfail'		=> (extension_loaded('mysqli') || extension_loaded('pdo_mysql')) ? true : false
				),
				'zLib'		=> array(
					'required'		=> $this->user->lang('yes'),
					'installed'		=> (extension_loaded('zlib')) ? $this->user->lang('yes') : $this->user->lang('no'),
					'passfail'		=> (extension_loaded('zlib')) ? true : false
				),
				'Zip'		=> array(
						'required'		=> $this->user->lang('yes'),
						'installed'		=> (class_exists('ZipArchive')) ? $this->user->lang('yes') : $this->user->lang('no'),
						'passfail'		=> (class_exists('ZipArchive')) ? true : false
				),
				'memory'	=> array(
					'required'		=> REQ_PHP_MEMORY,
					'installed'		=> (intval(ini_get('memory_limit')) == -1) ? "Unlimited" : ini_get('memory_limit'),
					'passfail'		=> $this->check_php_limit(REQ_PHP_MEMORY),
					'adviced_fail'	=> ($this->check_php_limit(REQ_PHP_MEMORY_REC) ? false :true),
					'recommended'	=> REQ_PHP_MEMORY_REC,
				),
				'curl'		=> array(
					'required'		=> $this->user->lang('yes'),
					'installed'		=> (function_exists('curl_version')) ? $this->user->lang('yes') : $this->user->lang('no'),
					'passfail'		=> (function_exists('curl_version')) ? true : false,
					'adviced_fail'	=> (!function_exists('curl_version')) ? true : false,
					'ignore'		=> true
				),
				'fopen'		=> array(
					'required'		=> $this->user->lang('yes'),
					'installed'		=> (function_exists('fopen')) ? $this->user->lang('yes') : $this->user->lang('no'),
					'passfail'		=> (function_exists('fopen')) ? true : false
				),
				'hash'		=> array(
					'required'		=> $this->user->lang('yes'),
					'installed'		=> (function_exists('hash')) ? $this->user->lang('yes') : $this->user->lang('no'),
					'passfail'		=> (function_exists('hash')) ? true : false
				),
				'xml'	=> array(
					'required'		=> $this->user->lang('yes'),
					'installed'		=> (function_exists('simplexml_load_string')) ? $this->user->lang('yes') : $this->user->lang('no'),
					'passfail'		=> (function_exists('simplexml_load_string')) ? true : false
				),
				'autoload'	=> array(
					'required'		=> $this->user->lang('yes'),
					'installed'		=> (function_exists('spl_autoload_register')) ? $this->user->lang('yes') : $this->user->lang('no'),
					'passfail'		=> (function_exists('spl_autoload_register')) ? true : false
				),
				'json'		=> array(
					'required'		=> $this->user->lang('yes'),
					'installed'		=> (extension_loaded('json')) ? $this->user->lang('yes') : $this->user->lang('no'),
					'passfail'		=> (extension_loaded('json')) ? true : false
				),
				'gd'		=> array(
					'required'		=> $this->user->lang('yes'),
					'installed'		=> (extension_loaded('gd') && function_exists('gd_info')) ? $this->user->lang('yes') : $this->user->lang('no'),
					'passfail'		=> (extension_loaded('gd') && function_exists('gd_info')) ? true : false
				),
				'mb'		=> array(
					'required'		=> $this->user->lang('yes'),
					'installed'		=> (function_exists('mb_strtolower')) ? $this->user->lang('yes') : $this->user->lang('no'),
					'passfail'		=> (function_exists('mb_strtolower')) ? true : false,
				),
				'data-folder' => array(
					'required' => 'Writable',
					'installed' => ($this->checkDataFolder()) ? 'Writable' : 'Folder '.$this->pfh->FolderPath('templates', 'eqdkp').' not writable',
					'passfail' => $this->checkDataFolder(),
				),
			);
		}

		private function checkDataFolder(){
			if($this->blnWritable != NULL) return $this->blnWritable;
			$blnIsWritable = register('pfh')->is_writable($this->pfh->FolderPath('templates', 'eqdkp').'testfile.txt', true);
			$this->blnWritable = $blnIsWritable;
			return $blnIsWritable;
		}

		/**
		*	get the count
		*/
		public function getCounts($type='required'){
			$arrCount	= array();
			foreach($this->checks as $fname=>$fdata){
				if(
					($type == 'required' && (!$fdata['passfail'] || (isset($fdata['ignore']) && $fdata['ignore']))) ||
					($type == 'optional' && (isset($fdata['adviced_fail']) && $fdata['adviced_fail'])) ||
					($type == 'both' && ((isset($fdata['adviced_fail']) && $fdata['adviced_fail']) || (isset($fdata['adviced_fail']) && $fdata['adviced_fail'])))
				){
					if(!isset($fdata['ignore']) || !$fdata['ignore']){
						$arrCount[]	= $fname;
					}

				}
			}
			return count($arrCount);
		}

		/**
		*	Return the array with the results
		*/
		public function getRequirements(){
			return $this->checks;
		}

		/**
		*	private helper functions
		*/
		private function check_php_limit($needed){
			$installed = ini_get('memory_limit');
			if (intval($installed) == -1) return true;
			return ($this->convert_hr_to_bytes($installed) >= $this->convert_hr_to_bytes($needed)) ? true : false;
		}

		private function convert_hr_to_bytes( $size ) {
			( $bytes = (float) $size )
			&& ( $last = strtolower( substr( $size, -1 ) ) )
			&& ( $pos = strpos( ' kmg', $last , 1 ) )
			&& $bytes *= pow( 1024, $pos )
			;
			return round( $bytes );
		}

		/*public function get_output() {
			$content = '';
			$phpcheckdata	= $this->checks;

			//Check for Apache on Windows System, because of ThreadStackSize
			//https://eqdkp-plus.eu/wiki/Versionsaktualisierung#EQdkp_Plus_2.1_l.C3.A4uft_nicht_mehr_auf_Windows-Servern
			$output_array = array();
			if(preg_match("/Apache\/(.*)\(Win(.*)\)/", $_SERVER['SERVER_SOFTWARE'], $output_array)){
				$content .='<div class="infobox infobox-large infobox-red clearfix">
				<i class="fa fa-exclamation-triangle fa-4x pull-left"></i> <strong>'.$this->lang['windows_apache_hint'].'</strong>
			</div>';
			}

			if($this->getCounts() > 0){
				$content .='<div class="infobox infobox-large infobox-red clearfix">
				<i class="fa fa-exclamation-triangle fa-4x pull-left"></i> <strong>'.$this->lang['phpcheck_failed'].'</strong>
			</div>';
			} else {

				// show a warning if one of the optional steps does not match
				if($this->getCounts('optional') > 0){
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
			if($this->getCounts() > 0){
				$this->pdl->log('install_success', $this->lang['phpcheck_success']);
			}else{
				$this->pdl->log('install_error', $this->lang['phpcheck_failed']);
			}

			//JavaScript check pathinfo
			$content .= '<script>$(function(){
				$.get( "index.php/pathinfotest", function( data ) {
					if($.trim(data) != "/pathinfotest"){
						pathinfotest_failed();
					}

				}).fail(function() {
				    pathinfotest_failed();
				});

				function pathinfotest_failed(){
					var myFirstColumn = $(".colorswitch tr:last td:nth-child(2)");
					var myLastColum = $(".colorswitch tr:last td:nth-child(5)");
					myFirstColumn.html("'.$this->lang['no'].'");
					myFirstColumn.removeClass("positive");
					myFirstColumn.addClass("negative");
					myLastColum.html("<i class=\"fa fa-times-circle fa-2x negative\"></i>");
					$(".buttonbar button[name=\"next\"]").hide();
				}
			})</script>';

			return $content;
		} */
	}
}
