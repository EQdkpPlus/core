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

if(!defined('EQDKP_INC')) {
	die('Do not access this file directly.');
}

if(!class_exists('pdh_w_cronjobs')) {
	class pdh_w_cronjobs extends pdh_w_generic {

		public function add($cronjobID, $intStartTime, $blnRepeat, $strRepeatType, $intRepeatInterval, $blnExtern, $blnAjax, $blnDelay, $blnMultiple, $blnActive, $blnEditable, $strPath, $arrParams, $strDescription){
			$objQuery = $this->db->prepare("INSERT INTO __cronjobs :p")->set(array(
					'id'				=> $cronjobID,
					'start_time'		=> $intStartTime,
					'`repeat`'			=> ($blnRepeat) ? 1 : 0,
					'repeat_type'		=> $strRepeatType,
					'repeat_interval'	=> $intRepeatInterval,
					'extern'			=> ($blnExtern) ? 1 : 0,
					'ajax'				=> ($blnAjax) ? 1 : 0,
					'delay'				=> ($blnDelay) ? 1 : 0,
					'multiple'			=> ($blnMultiple) ? 1 : 0,
					'active'			=> ($blnActive) ? 1 : 0,
					'editable'			=> ($blnEditable) ? 1 : 0,
					'`path`'			=> $strPath,
					'params'			=> serialize($arrParams),
					'description'		=> $strDescription
			))->execute();

			if($objQuery){
				$this->pdh->enqueue_hook('cronjobs_update', $cronjobID);
				return $cronjobID;
			}
			return false;
		}

		public function update($cronjobID, $intStartTime, $blnRepeat, $strRepeatType, $intRepeatInterval, $blnExtern, $blnAjax, $blnDelay, $blnMultiple, $blnActive, $blnEditable, $strPath, $arrParams, $strDescription){
			$objQuery = $this->db->prepare("UPDATE __cronjobs :p WHERE id=?")->set(array(
					'start_time'		=> $intStartTime,
					'`repeat`'			=> ($blnRepeat) ? 1 : 0,
					'repeat_type'		=> $strRepeatType,
					'repeat_interval'	=> $intRepeatInterval,
					'extern'			=> ($blnExtern) ? 1 : 0,
					'ajax'				=> ($blnAjax) ? 1 : 0,
					'delay'				=> ($blnDelay) ? 1 : 0,
					'multiple'			=> ($blnMultiple) ? 1 : 0,
					'active'			=> ($blnActive) ? 1 : 0,
					'editable'			=> ($blnEditable) ? 1 : 0,
					'`path`'			=> $strPath,
					'params'			=> serialize($arrParams),
					'description'		=> $strDescription
			))->execute($cronjobID);

			if($objQuery){
				$this->pdh->enqueue_hook('cronjobs_update', $cronjobID);
				return $cronjobID;
			}
			return false;
		}


		public function delete($cronjobID){
			$objQuery = $this->db->prepare("DELETE FROM __cronjobs WHERE id=?")->execute($cronjobID);
			$this->pdh->enqueue_hook('cronjobs_update', array($cronjobID));
			return true;
		}

		public function setActive($cronjobID, $arrAdditionalData=array()){
			$arrToSet = array(
				'active' => 1,
			);

			$arrData = $this->pdh->get('cronjobs', 'data', array($cronjobID));

			foreach($arrAdditionalData as $key => $val){
				if(!isset($arrData[$key])) continue;
				$arrToSet[$key] = $val;
			}

			$objQuery = $this->db->prepare("UPDATE __cronjobs :p WHERE id=?")->set($arrToSet)->execute($cronjobID);

			if($objQuery){
				$this->pdh->enqueue_hook('cronjobs_update', $cronjobID);
				return $cronjobID;
			}
			return false;
		}

		public function setInactive($cronjobID){
			$objQuery = $this->db->prepare("UPDATE __cronjobs :p WHERE id=?")->set(array(
				'active' => 0,
			))->execute($cronjobID);

			if($objQuery){
				$this->pdh->enqueue_hook('cronjobs_update', $cronjobID);
				return $cronjobID;
			}
			return false;
		}

		public function setNextRun($cronjobID,$intNextRun){
			$objQuery = $this->db->prepare("UPDATE __cronjobs :p WHERE id=?")->set(array(
					'next_run' => $intNextRun,
			))->execute($cronjobID);

			if($objQuery){
				$this->pdh->enqueue_hook('cronjobs_update', $cronjobID);
				return $cronjobID;
			}
			return false;
		}

		public function setLastRun($cronjobID,$intLastRun){
			$objQuery = $this->db->prepare("UPDATE __cronjobs :p WHERE id=?")->set(array(
					'last_run' => $intLastRun,
			))->execute($cronjobID);

			if($objQuery){
				$this->pdh->enqueue_hook('cronjobs_update', $cronjobID);
				return $cronjobID;
			}
			return false;
		}

		public function setLastAndNextRun($cronjobID, $intLastRun, $intNextRun){
			$objQuery = $this->db->prepare("UPDATE __cronjobs :p WHERE id=?")->set(array(
					'next_run' => $intNextRun,
					'last_run' => $intLastRun,
			))->execute($cronjobID);

			if($objQuery){
				$this->pdh->enqueue_hook('cronjobs_update', $cronjobID);
				return $cronjobID;
			}
			return false;
		}


	}
}
