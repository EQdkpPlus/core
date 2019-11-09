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

 if (!class_exists("myemojione")) {
	class myemojione extends gen_class {

		public function __construct(){

			//Load required files
			require_once('RulesetInterface.php');
			require_once('ClientInterface.php');
			require_once('Client.php');
			require_once('Ruleset.php');
			require_once('Emojione.php');
		}

		public function textToShortcode($strText){
			$client = new Emojione\Client(new Emojione\Ruleset());

			$client->imageType = 'svg';
			$client->imagePathSVG = $this->server_path.'libraries/emojione/svg/';

			return $client->toShort($strText);
		}

		public function textToImage($strText){
			$client = new Emojione\Client(new Emojione\Ruleset());

			$client->imageType = 'svg';
			$client->imagePathSVG = $this->server_path.'libraries/emojione/svg/';
			return $client->unicodeToImage($strText);
		}

		public function shortcodeToImage($strText){
			$client = new Emojione\Client(new Emojione\Ruleset());

			$client->imageType = 'svg';
			$client->imagePathSVG = $this->server_path.'libraries/emojione/svg/';

			return $client->shortnameToImage($strText);
		}
	}
 }
