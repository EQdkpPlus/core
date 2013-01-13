<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2012
 * Date:		$Date$
 * -----------------------------------------------------------------------
 * @author		$Author$
 * @copyright	2006-2012 EQdkp-Plus Developer Team
 * @link		http://eqdkp-plus.com
 * @package		eqdkp-plus
 * @version		$Rev$
 * 
 * $Id$
 */

if ( !defined('EQDKP_INC') ){
	header('HTTP/1.0 404 Not Found');exit;
}

if ( !class_exists( "inactive_crontask" ) ) {
	class inactive_crontask extends crontask {
		public static $shortcuts = array('pdh', 'config', 'time'
		);

		public function __construct(){
			$this->defaults['active']		= true;
			$this->defaults['repeat']		= true;
			$this->defaults['repeat_type']	= 'daily';
			$this->defaults['editable']		= false;
			$this->defaults['description']	= 'Update status of characters to inactive';
		}

		public function run() {
			if ((int)$this->config->get('inactive_period') == 0) return;
			$members = $this->pdh->aget('member_dates', 'last_raid', 0, array($this->pdh->get('member', 'id_list'), null, false));
			$crit_time = $this->time->time - 24*3600*$this->config->get('inactive_period');
			foreach($members as $member_id => $last_raid) {
				if($last_raid < $crit_time) $this->pdh->put('member', 'change_status', array($member_id, 0));
			}
			$this->pdh->process_hook_queue();
		}
	}
}
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_inactive_crontask', inactive_crontask::$shortcuts);
?>