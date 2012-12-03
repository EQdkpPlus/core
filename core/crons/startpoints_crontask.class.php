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

if ( !class_exists( "startpoints_crontask" ) ) {
	class startpoints_crontask extends crontask {
		public static $shortcuts = array('pdh', 'apa' => 'auto_point_adjustments',
			'timekeeper'	=> 'timekeeper',
		);

		public function __construct(){
			$this->defaults['repeat']		= true;
			$this->defaults['repeat_type']	= 'daily';
			$this->defaults['editable']		= false;
			$this->defaults['description']	= 'Give startpoints to characters';
		}

		public function run() {
			$cron = $this->timekeeper->list_crons('startpoints');
			$apa_ids = $this->apa->get_apa_idsbytype('startpoints');
			foreach($apa_ids as $apa_id) {
				$this->apa->get_apa_type('startpoints')->update_startdkp($apa_id, $cron['last_run']);
			}
			$this->pdh->process_hook_queue();
		}
	}
}
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_startpoints_crontask', startpoints_crontask::$shortcuts);
?>