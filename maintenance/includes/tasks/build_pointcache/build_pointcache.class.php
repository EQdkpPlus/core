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

if ( !defined('EQDKP_INC') ){
	header('HTTP/1.0 404 Not Found');exit;
}

class build_pointcache extends task {
	public $author = 'GodMod';
	public $version = '1.0.0';
	public $form_method = 'post';
	public $name = 'Build Pointcache';
	public $type = 'worker';

	public function is_applicable() {
		return true;
	}

	public function is_necessary() {
		return ($this->is_applicable() && $this->config->get('build_pointcache')) ? true : false;
	}

	public function get_form_content() {
		@set_time_limit(0);

		$arrTasks = array();

		//Raids
		$arrRaids = $this->pdh->get('raid', 'id_list');
		foreach($arrRaids as $intRaidID){
			$intEventID = $this->pdh->get('raid', 'event', array($intRaidID));
			$arrMultiPools = $this->pdh->get('event', 'multidkppools', array($intEventID));
			foreach($arrMultiPools as $intPools){
				$arrTasks[] = array($intRaidID, $intPools, 'raid');
			}
		}

		//Items
		$arrItems =  $this->pdh->get('item', 'id_list');
		foreach($arrItems as $intItemID){
			$intItemPool = $this->pdh->get('item', 'itempool_id', array($intItemID));

			$arrPools = $this->pdh->get('multidkp', 'mdkpids4itempoolid', array($intItemPool));

			foreach($arrPools as $intPoolID){
				$arrTasks[] = array($intItemID, $intPoolID, 'item');
			}
		}

		//Adjustments
		$arrAdjustments = $this->pdh->get('adjustment', 'id_list');
		foreach($arrAdjustments as $intAdjID){
			$arrTasks[] = array($intAdjID, 0, 'adjustment');
		}

		//Points
		$arrMemberIDs = $this->pdh->get('member', 'id_list');

		$arrMultidkpPools = $this->pdh->get('multidkp', 'id_list');
		foreach($arrMemberIDs as $val){
			foreach($arrMultidkpPools as $intPoolID){
				$arrTasks[] = array($val, $intPoolID, 'points');
			}
		}

		//Execute Task
		if($this->in->exists('primaryID')){
			$strInputPrimary = $this->in->get('primaryID');
			$strInputSecondary = $this->in->get('secondaryID');
			$strInputType = $this->in->get('type');

			$arrPrimary = explode(',', $strInputPrimary);
			$arrSecondary = explode(',', $strInputSecondary);
			$arrType = explode(',', $strInputType);

			foreach($arrPrimary as $key => $strVal){
				$intPrimary = intval($strVal);
				$intSecondary = intval($arrSecondary[$key]);
				$strType = $arrType[$key];

				if($strType == 'points'){
					$this->pdh->get('points', 'current', array($intPrimary, $intSecondary));
					$this->pdh->get('points', 'current', array($intPrimary, $intSecondary, 0, 0, true, false));
					echo "points ";
				} elseif($strType == 'raid'){
					$this->pdh->get('raid', 'value', array($intPrimary, $intSecondary));
					echo "raid ";
				} elseif($strType == 'item'){
					$this->pdh->get('item', 'value', array($intPrimary, $intSecondary));
					echo "item ";
				} elseif($strType == 'adjustment'){
					$arrMultidkpPools = $this->pdh->get('multidkp', 'id_list');
					foreach($arrMultidkpPools as $intPoolID){
						$this->pdh->get('adjustment', 'value', array($intPrimary, $intPoolID));
					}

					echo "adjustment ";
				}

				echo sanitize($intPrimary).'-'.sanitize($intSecondary).'; ';

			}

			exit;
		} elseif($this->in->exists('finished')) {

			//Clear Cache
			$this->pdc->flush();

			echo "finished";
			exit;
		} else {
			$this->config->del('build_pointcache');

			//Clear Tables
			$this->db->query("UPDATE __members SET points = NULL");
			$this->db->query("UPDATE __members SET points_apa = NULL");
			$this->db->query("TRUNCATE __member_points");
			$this->db->query("UPDATE __items SET item_apa_value = NULL");
			$this->db->query("UPDATE __raids SET raid_apa_value = NULL");
			$this->db->query("UPDATE __adjustments SET adjustment_apa_value = NULL");


			//Clear Cache
			$this->pdc->flush();
		}

		//Javascript for XHTML Requests
		$out = $this->lang['build_pointcache_info']."<br /><br />".sprintf($this->lang['execute_step'], "<span id='stepnumber'>0</span>", count($arrTasks))."


<br /><br />
		<div id=\"progressbar\">
		  <div id='progressbar-inner'></div>
		</div>

		<style>
			#progressbar {
  background-color: #ddd;
  border-radius: 2px; /* (height of inner div) / 2 + padding */
  padding: 3px;
}

#progressbar > div {
   background-color: green;
   width: 0%; /* Adjust with JavaScript */
   height: 20px;
   border-radius: 2px;
}
		</style>

		<script>
		var tasks = ".json_encode($arrTasks).";

		var firstElement = tasks[0];

		var current_item = 0;
		var cookie = getCookie('pointcache_step');
		if(cookie != null && cookie != '') current_item = parseInt(cookie);

		var max_item = tasks.length-1;
		if(tasks.length == 0){
			document.getElementById('progressbar-inner').style.width = 100+'%';
		} else {
			do_request();
		}

		function setCookie(name,value,days) {
		    var expires = '';
		    if (days) {
		        var date = new Date();
		        date.setTime(date.getTime() + (days*24*60*60*1000));
		        expires = '; expires=' + date.toUTCString();
		    }
		    document.cookie = name + '=' + (value || '')  + expires + '; path=/';
		}
		function getCookie(name) {
		    var nameEQ = name + '=';
		    var ca = document.cookie.split(';');
		    for(var i=0;i < ca.length;i++) {
		        var c = ca[i];
		        while (c.charAt(0)==' ') c = c.substring(1,c.length);
		        if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
		    }
		    return null;
		}

		function do_request(){
			if(current_item > max_item) {
				console.log('finished');
				document.getElementById('stepnumber').innerHTML = max_item+1;
				document.getElementById('progressbar-inner').style.width = 100+'%';

				var xhttp = new XMLHttpRequest();
			  	xhttp.onreadystatechange = function() {};
			  	xhttp.open('GET', 'task.php".$this->SID."&task=build_pointcache&finished=true', true);
			  	xhttp.send();

                alert('All tasks finished.');
				setCookie('pointcache_step', 0, 0.1);
				return;
			}

			var i;
			var combined_primary = '';
			var combined_secondary = '';
			var combined_type = '';
			var combineCount = ".((defined('POINTCACHE_COMBINE')) ? POINTCACHE_COMBINE : 5).";

			for (i = 0; i < combineCount; i++) {
				var arrElement = tasks[current_item];
				if (typeof arrElement == 'undefined' || arrElement == null) break;

				var primary = arrElement[0];
				var secondary = arrElement[1];
				var type = arrElement[2];
				combined_primary = combined_primary + ',' + primary;
				combined_secondary = combined_secondary  + ',' + secondary;
				combined_type = combined_type + ',' + type;
				current_item = current_item + 1;
			}

			document.getElementById('stepnumber').innerHTML = current_item;

			console.log(combined_primary);
			console.log(combined_secondary);
			console.log(combined_type);

			setCookie('pointcache_step', current_item, 14);

			var xhttp = new XMLHttpRequest();
			  xhttp.onreadystatechange = function() {
			    if (this.readyState == 4 && this.status == 200) {
					current_display = current_item;
					if(current_item > max_item) current_display = max_item;
					document.getElementById('progressbar-inner').style.width = ((current_display+1) / (max_item+1))*100+'%';
					do_request();
			    } else if(this.readyState == 4) {
					console.log('Request failed');
					document.getElementById('progressbar-inner').style.backgroundColor = 'orange';
				}
			  };
			  xhttp.open('GET', 'task.php".$this->SID."&task=build_pointcache&primaryID='+combined_primary.substring(1)+'&secondaryID='+combined_secondary.substring(1)+'&type='+combined_type.substring(1), true);
			  xhttp.send();
		}

		</script>

		<br /><a href='".$this->root_path."maintenance/".$this->SID."'><button type=\"button\"><i class=\"fa fa-chevron-right\"></i> ".$this->user->lang('task_manager')."</button></a>
		";

		//JavaScript for Progressbar

		return $out;
	}
}
