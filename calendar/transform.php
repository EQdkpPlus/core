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

define('EQDKP_INC', true);
$eqdkp_root_path = '../';
include_once($eqdkp_root_path . 'common.php');

class calraids_transform extends page_generic {
	public static $shortcuts = array('user', 'tpl', 'in', 'pdh', 'core', 'time');

	public function __construct() {
		$handler = array(
			'transformraid'			=> array('process' => 'transform_raid'),
		);
		$this->user->check_auth('u_cal_event_add');
		parent::__construct(false, $handler, array(), null, '', 'eventid');
		$this->process();
	}

	function transform_raid(){
		if($this->in->get('confirmed')){
			$statarray[] = '0';
		}
		if($this->in->get('signedin')){
			$statarray[] = '1';
		}
		if($this->in->get('notsure')){
			$statarray[] = '3';
		}
		if($this->in->get('unsigned')){
			$statarray[] = '4';
		}

		$attendees	= $this->pdh->get('calendar_raids_attendees', 'attendee_stats', array($this->url_id, $statarray));
		$raidext	= $this->pdh->get('calendar_events', 'extension', array($this->url_id));

		// add the raidleaders to the attendees
		if($this->in->get('raidleaders')){
			if(is_array($raidext['raidleader'])){
				$attendees	= array_merge($attendees, $raidext['raidleader']);
				$attendees = array_unique($attendees);
			}
		}

		$htmlcode	= '<html>
			<body onload="document.transform.submit();">
			<form method="post" action="'.$this->root_path.'admin/manage_raids.php'.$this->SID.'&upd=true&dataimport=true" name="transform" target="_parent">
			<input name="event" value="'.$raidext['raid_eventid'].'" type="hidden">
			<input name="value" value="'.(($raidext['raid_value'] > 0) ? $raidext['raid_value'] : 0).'" type="hidden">
			<input name="date" value="'.$this->time->user_date($this->pdh->get('calendar_events', 'time_start', array($this->url_id)), true, false, false, function_exists('date_create_from_format')).'" type="hidden">
			<input name="rnote" value="'.$this->pdh->get('calendar_events', 'notes', array($this->url_id)).'" type="hidden">
			<input name="'.$this->user->csrfPostToken().'" value="'.$this->user->csrfPostToken().'" type="hidden" />
			';
		foreach($attendees as $mattids){
			$htmlcode	.= '<input name="attendees[]" value="'.$mattids.'" type="hidden">';
		}
		$htmlcode	.= '</form>
			</body>
			</html>';

		echo $htmlcode;die();
	}

	public function display(){
		$this->tpl->assign_vars(array(
			'EVENTID'		=> $this->in->get('eventid', 0),
		));

		$this->core->set_vars(array(
			'page_title'		=> $this->user->lang('rp_transform'),
			'template_file'		=> 'calendar/transform.html',
			'header_format'		=> 'simple',
			'display'			=> true)
		);
	}
}
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_calraids_transform', calraids_transform::$shortcuts);
registry::register('calraids_transform');
?>