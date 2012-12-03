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
class tour extends gen_class {
	public static $shortcuts = array('in', 'config', 'jquery', 'user', 'tpl', 'core');

	private $cookie			= array();
	private $cookie_time	= 0;
	private $lang			= array();
	private $steps			= array();

	public function init(){
		if ($this->in->get('tour') == 'cancel' || !$this->load_language()){
			$this->cancel();
			return;
		}

		if ($this->in->get('tour') == 'start'){
			$this->start();
		};

		$this->cookie = unserialize(get_cookie('tour'));
		$this->cookie_time = time() + 3600;

		if (strlen(get_cookie('tour')) && $this->user->is_signedin()){
			$this->init_steps();

			$step = $this->cookie['step'];
			$step_id = $step;

			if ($this->in->get('tour') == 'next'){
				$step_id = ($step + 1);
				$force = true;
			} elseif($this->in->get('tour') == 'reload'){
				$force = true;
			}

			if (isset($this->steps[$step_id])){
				$show = ($this->in->get('tour') == 'show') ? true : false;
				if ($this->cookie['shown'] !== true || $force || $show || $step == 0){
					$this->execute_step($step_id, $show);
				}
			} else {
				$this->cancel();
				return;
			}

			if ($step > 0 && $step < count($this->steps)-1){
				$this->core->message($this->lang['navi'], $this->lang['navi_title'].' - '.sprintf($this->lang['steps'], $step, count($this->steps)-1));
			}
		}
	}

	public function load_language(){
		if (file_exists($this->root_path.'language/'.sanitize($this->user->data['user_lang']).'/lang_tour.php')){
			include_once($this->root_path.'language/'.sanitize($this->user->data['user_lang']).'/lang_tour.php');
			$this->lang = $lang;
			return true;
		}
		return false;
	}

	public function cancel(){
		set_cookie('tour', '', 0);
	}

	public function start(){
		set_cookie('tour', serialize(array('step'	=> 0)), $this->cookie_time);
		redirect('index.php');
	}

	public function init_steps(){
		$steps = array(
			0	=> array(
				'url'	=> '',
				'type'	=> 'both',
			),
			1	=> array(
				'url'	=> 'admin/manage_settings.php',
				'type'	=> 'admin',
			),
			2	=> array(
				'url'	=> 'admin/manage_extensions.php',
				'type'	=> 'admin',
			),
			3	=> array(
				'url'	=> 'admin/manage_portal.php',
				'type'	=> 'admin',
			),
			4	=> array(
				'url'	=> 'admin/manage_users.php',
				'type'	=> 'admin',
			),
			5	=> array(
				'url'	=> 'admin/manage_raids.php',
				'type'	=> 'admin',
			),
			6	=> array(
				'url'	=> 'admin/manage_pagelayouts.php',
				'type'	=> 'admin',
			),
			7	=> array(
				'url'	=> 'admin/manage_pages.php',
				'type'	=> 'admin',
			),
			8	=> array(
				'url'	=> 'admin/manage_backup.php',
				'type'	=> 'admin',
			),
			9	=> array(
				'url'	=> 'admin/index.php',
				'type'	=> 'both',
			),
		);

		foreach ($steps as $key => $value){
			if ($value['type'] == 'admin' || $value['type'] == 'both'){
				if ($this->user->check_auth('a_', false)){
					$this->steps[] = $value;
				}
			} else {
				$this->steps[] = $value;
			}
		}
	}

	public function execute_step($step, $show){
		if (!$show && $this->steps[$step]['url'] != ''){
			set_cookie('tour', serialize(array('step'	=> $step)), $this->cookie_time);
			redirect($this->steps[$step]['url'].'?tour=show');
		} else {

			$custom_js = "var a = 1;";
			if ($step == 0){
				$custom_js = "window.location='?tour=next'";
			}

			$this->jquery->Dialog('tour_step', $this->lang['navi_title'], array('message'	=> '<b>'.$this->lang['navi_title'].' - '.$this->lang['step_'.$step.'_title'].'</b><br/><br/>'.$this->lang['step_'.$step], 'width' => 300, 'height'	=> 300, 'custom_js' => $custom_js, 'cancel_js'	=> "window.location='?tour=cancel'"), 'confirm');
			$this->tpl->add_js(
				"$(document).ready(function () {
					tour_step();
				});"
			);
			set_cookie('tour', serialize(array('step'	=> $step, 'shown' => true)), $this->cookie_time);
		}
	}
}
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_tour', tour::$shortcuts);
?>