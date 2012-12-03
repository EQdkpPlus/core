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

if ( !defined('EQDKP_INC') )
{
    header('HTTP/1.0 404 Not Found');exit;
}
class mmocms_tour
{
	
	var $cookie = array();
	var $cookie_time = 0;
	var $lang = array();
	var $steps = array();
	
	function init(){
		global $in, $core;
		
		if ($in->get('tour') == 'cancel' || !$this->load_language()){
			$this->cancel();
			return;
		}		
		
		if ($in->get('tour') == 'start'){
			$this->start();
		};
		
		
		$this->cookie = unserialize(get_cookie('tour'));
		$this->cookie_time = time() + 3600;
		
		
		if (strlen(get_cookie('tour')) && $user->data['user_id'] != ANONYMOUS){
			$this->init_steps();
			
			
			$step = $this->cookie['step'];
			$step_id = $step;
			
			if ($in->get('tour') == 'next'){
				$step_id = ($step + 1);
				$force = true;
			} elseif($in->get('tour') == 'reload'){
				$force = true;
			}
			
			if (isset($this->steps[$step_id])){
				$show = ($in->get('tour') == 'show') ? true : false;
				if ($this->cookie['shown'] !== true || $force || $show || $step == 0){
					$this->execute_step($step_id, $show);
				}
			} else {
				$this->cancel();
				return;
			}
			
			if ($step > 0 && $step < count($this->steps)-1){
				$core->message($this->lang['navi'], $this->lang['navi_title'].' - '.sprintf($this->lang['steps'], $step, count($this->steps)-1));
			}
			
		}
	}
	
	function load_language(){
		global $core, $eqdkp_root_path, $user;
		if (file_exists($eqdkp_root_path.'language/'.sanitize($user->data['user_lang']).'/lang_tour.php')){
			include_once($eqdkp_root_path.'language/'.sanitize($user->data['user_lang']).'/lang_tour.php');
			$this->lang = $lang;
			return true;
		}
		return false;
		
	}
	
	function cancel(){
		set_cookie('tour', '', 0);
	}
	
	function start(){
		set_cookie('tour', serialize(array('step'	=> 0)), $this->cookie_time);
		redirect('index.php');
	}
	
	function init_steps(){
		global $user;
		
		$steps = array(
			0	=> array(
				'url'	=> '',
				'type'	=> 'both',
			),
			1	=> array(
				'url'	=> 'admin/settings.php',
				'type'	=> 'admin',
			),
			2	=> array(
				'url'	=> 'admin/manage_plugins.php',
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
				'url'	=> 'admin/manage_infopages.php',
				'type'	=> 'admin',
			),
			8	=> array(
				'url'	=> 'admin/backup.php',
				'type'	=> 'admin',
			),
			9	=> array(
				'url'	=> 'admin/index.php',
				'type'	=> 'both',
			),
		
		);
		
		foreach ($steps as $key => $value){
			if ($value['type'] == 'admin' || $value['type'] == 'both'){
				if ($user->check_auth('a_', false)){
					$this->steps[] = $value;
				}
				
			} else {
				$this->steps[] = $value;
			}

		}
	
	}
	
	function execute_step($step, $show){
		global $in, $jquery, $core, $tpl;

		if (!$show && $this->steps[$step]['url'] != ''){
			set_cookie('tour', serialize(array('step'	=> $step)), $this->cookie_time);
			redirect($this->steps[$step]['url'].'?tour=show');
		
		} else {

			$custom_js = "var a = 1;";
			if ($step == 0){
				$custom_js = "window.location='?tour=next'";
			}
			
			$jquery->Dialog('tour_step', $this->lang['navi_title'], array('message'	=> '<b>'.$this->lang['navi_title'].' - '.$this->lang['step_'.$step.'_title'].'</b><br/><br/>'.$this->lang['step_'.$step], 'width' => 300, 'height'	=> 300, 'custom_js' => $custom_js, 'cancel_js'	=> "window.location='?tour=cancel'"), 'confirm');
			$tpl->add_js(
				"$(document).ready(function () {
					tour_step();
				});"
			);
			
			set_cookie('tour', serialize(array('step'	=> $step, 'shown' => true)), $this->cookie_time);

		}
	}

}	
?>