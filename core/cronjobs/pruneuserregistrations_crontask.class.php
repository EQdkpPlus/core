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

if ( !class_exists( "pruneuserregistrations_crontask" ) ) {
	class pruneuserregistrations_crontask extends crontask {
		public function __construct(){
			$this->defaults['repeat']		= true;
			$this->defaults['repeat_type']	= 'daily';
			$this->defaults['editable']		= true;
			$this->defaults['ajax']			= true;
			$this->defaults['description']	= 'Prune Userregistrations';
			$this->defaults['active']		= true;
		}

		public $options = array(
		);

		public function run(){
			$crons		= $this->cronjobs->list_crons();
			$params		= $crons['pruneuserregistrations']['params'];

			$intRegistrationType = (int)$this->config->get('account_activation');
			if($intRegistrationType == 0) return;

			if($intRegistrationType == 1){
				//User
				$intDays = 14;
			}elseif($intRegistrationType == 2){
				//Admin
				$intDays = 30;
			}

			$objQuery = $this->db->prepare("SELECT * FROM __users WHERE user_email_confirmed = -1 AND timestampdiff(DAY, FROM_UNIXTIME(user_registered), now()) > ?")->execute($intDays);
			$intCount = 0;
			if($objQuery){
				while($row = $objQuery->fetchAssoc()){
					$intUserID = $row['user_id'];
					$this->pdh->put('user', 'delete_user', array($intUserID, 0));
					$intCount++;
				}
			}

			echo "Deleted ".$intCount." inactive user registrations.";

			$this->pdh->process_hook_queue();

		}
	}
}
