<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2010
 * Date:		$Date: 2013-08-12 10:07:19 +0200 (Mo, 12 Aug 2013) $
 * -----------------------------------------------------------------------
 * @author		$Author: godmod $
 * @copyright	2006-2011 EQdkp-Plus Developer Team
 * @link		http://eqdkp-plus.com
 * @package		eqdkp-plus
 * @version		$Rev: 13468 $
 * 
 * $Id: game_crontask.class.php 13468 2013-08-12 08:07:19Z godmod $
 */

if ( !defined('EQDKP_INC') ){
	header('HTTP/1.0 404 Not Found');exit;
}

if ( !class_exists( "game_crontask" ) ) {
	class game_crontask extends crontask {
		
		public $options = array();

		public function __construct(){
			$this->defaults['repeat']		= true;
			$this->defaults['repeat_type']	= 'daily';
			$this->defaults['editable']		= true;
			$this->defaults['delay']		= true;
			$this->defaults['ajax']			= true;
			$this->defaults['description']	= 'Game-specific Update Tasks';
			
			$this->options = $this->game->cronjobOptions();
		}
		
		public function run(){
			$crons		= $this->timekeeper->list_crons();
			$params		= $crons['game']['params'];
			$this->game->cronjob($params);
		}
	}
}
?>