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

class calendareventtransform_pageobject extends pageobject {

	public function __construct() {
		$handler = array(
			'transformraid'			=> array('process' => 'transform_raid'),
		);
		$this->user->check_auth('a_raid_add');
		
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
		if($this->in->get('signedoff')){
			$statarray[] = '2';
		}
		if($this->in->get('notsure')){
			$statarray[] = '3';
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
		
		//Additional Data like Roles
		if($this->in->get('roleinfo', 0) && $raidext['raidmode'] != 'class'){
			$additional_data = "";
			foreach($attendees as $attendee_id){
				$additional_data .= $this->pdh->get('member', 'name', array($attendee_id)).': ';
				$roleid = $this->pdh->get('calendar_raids_attendees', 'role', array($this->url_id, $attendee_id));
				$additional_data .= $this->pdh->get('roles', 'name', array($roleid))."\n";
			}
		} else $additional_data = "";

		$htmlcode	= '<html>
			<body onload="document.transform.submit();">
			<form method="post" action="'.$this->server_path.'admin/manage_raids.php'.$this->SID.'&upd=true&dataimport=true" name="transform" target="_parent">
			<input name="event" value="'.$raidext['raid_eventid'].'" type="hidden">
			<input name="value" value="'.(($raidext['raid_value'] > 0) ? $raidext['raid_value'] : 0).'" type="hidden">
			<input name="date" value="'.$this->time->user_date($this->pdh->get('calendar_events', 'time_start', array($this->url_id)), true, false, false, function_exists('date_create_from_format')).'" type="hidden">
			<input name="rnote" value="'.$this->pdh->get('calendar_events', 'notes', array($this->url_id)).'" type="hidden">
			<input name="'.$this->user->csrfPostToken().'" value="'.$this->user->csrfPostToken().'" type="hidden" />
			<textarea name="additional_data">'.$additional_data.'</textarea>
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
?>