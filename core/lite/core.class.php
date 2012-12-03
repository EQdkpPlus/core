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

class core extends gen_class {
	public static $shortcuts = array('user', 'pdl', 'config', 'tpl', 'in', 'time');

	public $error_message			= array();			// Array of errors		@public $error_message
	public $header_inc				= false;			// Printed header?		@public $header_inc
	public $tail_inc				= false;			// Printed footer?		@public $tail_inc
	public $template_file			= '';				// Template filename	@public $template_file
	public $error_template_file		= '';				// Error Tp filename	@public $template_file
	public $default_game			= '';				// Defaultgame			@public $default_game
	public $game_language			= '';				// Defaultgame			@public $default_game
	public $icon_error				= '<img src="../templates/maintenance/images/failed.png" alt="" class="absmiddle"/>';
	public $icon_ok					= '<img src="../templates/maintenance/images/ok.png" alt="" class="absmiddle" />';

	public function StatusIcon($mystat= 'ok') {
		return ($mystat=='ok') ? $this->icon_ok : $this->icon_error;
	}

	public function __construct($template, $template_file, $error_template_file){
		$this->template_file = $template_file;
		$this->error_template_file = $error_template_file;

		$this->tpl->set_template($template);
		$this->tpl->assign_vars(array(
			'TYEAR'				=> $this->time->date('Y', time()),
			'MSG_TITLE'			=> '',
			'MSG_TEXT'			=> '')
		);

		$this->tpl->set_filenames(array(
			'body' => $this->template_file)
		);
	}

	public function message_die($text = '', $title = ''){
		$this->tpl->set_filenames(array(
			'body'			=> $this->error_template_file
		));
		$this->tpl->assign_vars(array(
			'MSG_TITLE'		=> ( $title != '' ) ? $title : '&nbsp;',
			'MSG_TEXT'		=> ( $text  != '' ) ? $text  : '&nbsp;',
		));
		if ( !$this->header_inc ){
			$this->page_header();
		}
		$this->page_tail();
	}

	private function httpHost(){
		$protocol = ($_SERVER['SSL_SESSION_ID'] || $_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1) ? 'https://' : 'http://';
		$xhost		= preg_replace('/[^A-Za-z0-9\.:-]/', '', $_SERVER['HTTP_X_FORWARDED_HOST']);
		$host			= $_SERVER['HTTP_HOST'];
		if (empty($host)){
			$host  = $_SERVER['SERVER_NAME'];
			$host .= ($_SERVER['SERVER_PORT'] != 80) ? ':' . $_SERVER['SERVER_PORT'] : '';
		}
		return $protocol.(!empty($xhost) ? $xhost . '/' : '').preg_replace('/[^A-Za-z0-9\.:-]/', '', $host);
	}

	public function check_auth(){
		if (!$this->user->check_auth('a_maintenance', false)){
			if ($this->config->get('pk_maintenance_mode') == '1'){
				redirect('maintenance/maintenance.php');
			} else {
				redirect('index.php');
			}
		}
	}

	public function create_breadcrump($name, $url = false) {
		$this->tpl->assign_block_vars('breadcrumps', array (
			'BREADCRUMP'	=> (($url) ? '<a href="'.$url.'">'.$name.'</a>' : $name)
		));
	}

	public function error_out($die = false){
		$error_message = $this->pdl->get_html_log(3);
		$error_count = $this->pdl->get_log_size(3);

		if ( $die ){
			$this->message_die($error_message, (( $error_count == 1 ) ? $this->user->lang['error'] : $this->user->lang['errors']));
		}else{
			$log = $this->pdl->get_log();
			$cc = 0;
			foreach($log as $type => $entries) {
				$cc++;
				$this->tpl->assign_block_vars('debug_types', array(
					'ID' => $cc,
					'TYPE' => $type)
				);
				foreach($entries as $entry) {
					$this->tpl->assign_block_vars('debug_types.debug_messages', array('MESSAGE' => $this->pdl->html_format_log_entry($type, $entry)));
				}
			}
			$this->tpl->assign_vars(array(
				'MAX_ID'	=> $cc,
				'L_CLICK'	=> $this->user->lang('click2show'))
			);
		}
	}

	public function page_header(){
		@header('Last-Modified: ' . gmdate('D, d M Y H:i:s', time()) . ' GMT');
		@header('Content-Type: text/html; charset=utf-8');
		//Disable Browser Cache
		@header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
		@header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Datum in der Vergangenheit
	
		$this->header_inc = true;
		$this->tpl->assign_vars(array(
			'L_ADMIN_PANEL' => $this->user->lang('admin_acp'),
			'U_ACP' => $this->root_path.'admin/index.php'.$this->SID,
			'L_ADMIN_PANEL' => $this->user->lang('admin_acp'),
			'L_MMODE' => $this->user->lang('maintenance_mode'),
			'L_TASK_MANAGER' => $this->user->lang('task_manager'),
			'U_MMODE' => $this->root_path.'maintenance/task_manager.php'.$this->SID,
			'L_ACTIVATE_INFO' => $this->user->lang('activate_info'),
			'L_ACTIVATE_MMODE' => $this->user->lang('activate_mmode'),
			'L_LEAVE_MMODE' => $this->user->lang('leave_mmode'),
			'L_DEACTIVATE_MMODE' => $this->user->lang('deactivate_mmode'),
			'S_MMODE_ACTIVE' => ($this->config->get('pk_maintenance_mode') == 1) ?  true : false,
			'MAINTENANCE_MESSAGE' => $this->config->get('pk_maintenance_message'),
			'S_SPLASH' => ($this->in->get('splash') == 'true') ? true : false,
			'SID'	=> $this->SID,
		));
		if($this->in->get('splash') == 'true') {
			$this->tpl->assign_vars(array(
				'L_SPLASH_WELCOME' => $this->user->lang('splash_welcome'),
				'L_SPLASH_DESC' => $this->user->lang('splash_desc'),
				'L_SPLASH_NEW' => $this->user->lang('splash_new'),
				'L_TOUR_START' => $this->user->lang('start_tour'),
				'L_JUMP_TOUR' => $this->user->lang('jump_tour'),
				'L_06_IMPORT' => $this->user->lang('06_import'),
				'L_GUILD_IMPORT' => $this->user->lang('guild_import'),
				'L_GUILD_IMPORT_INFO' => $this->user->lang('guild_import_info'),
			));
		}
	}

	public function page_tail() {
		$this->tpl->assign_var('S_SHOW_BUTTON', true);
		if($this->pdl->get_log_size(3) > 0){
			$this->tpl->assign_var('S_SHOW_BUTTON', false);
			$this->error_out(false);
		}

		$this->tpl->assign_var('EQDKP_VERSION', $this->config->get('plus_version'));
		$this->tpl->display();
	}
}
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_core', core::$shortcuts);
?>