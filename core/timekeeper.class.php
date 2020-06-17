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

class timekeeper extends gen_class {
	public static $dependencies = array('pfh');

	private $times = array();
	private $time_file = 'times.php';
	private $save_necessary = false;

	public function __construct(){
		$this->init_timekeeper();
		$this->pfh->secure_folder('timekeeper', 'eqdkp');
	}

	private function init_timekeeper(){
		$this->time_file  = $this->pfh->FolderPath('timekeeper', 'eqdkp').'times.php';
		$result = @file_get_contents($this->time_file);
		if($result !== false){
			$this->times = unserialize_noclasses($result);
		}else{
			$this->pfh->putContent($this->time_file, serialize(array()));
			//file_put_contents($this->time_file, serialize(array()));
		}
	}

	public function put($class, $event, $time = null, $force_write = false){
		if($time == null){
			$time = $this->time->time;
		}
		$this->times[$class][$event] = $time;
		if($force_write){
			$this->saveToFile();
		}else{
			$this->save_necessary = true;
		}
	}

	public function get($class, $event=null){
		if($event == null){
			if(is_array($this->times[$class])){
				$max_time = 0;
				foreach($this->times[$class] as $event => $time){
					if($time > $max_time)
						$max_time = $time;
				}
			}
		}else{
			$max_time = (isset($this->times[$class][$event])) ? $this->times[$class][$event] : 0;
		}
		return $max_time;
	}

	public function del($class, $event=null){
		if($event == null){
			unset($this->times[$class]);
		}else{
			unset($this->times[$class][$event]);
		}
		$this->save_necessary = true;
	}

	public function clear(){
		$this->times = array();
		$this->save_necessary = true;
	}

	public function saveToFile(){
		$this->pfh->putContent($this->time_file, serialize($this->times));
		//file_put_contents($this->time_file, serialize($this->times));
		$this->save_necessary = false;
	}

	public function __destruct(){
		if($this->save_necessary)
			$this->saveToFile();
	}

}
