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

if(!class_exists('itt_parser')) {
	abstract class itt_parser extends gen_class {
		public $config			= array();

		public $supported_games	= array();
		public $av_langs		= array();

		public function __construct($init=false, $config=false) {
			$this->config = $config;
			if(!$init) {
				return true;
			}
		}

		public function __destruct(){
			unset($this->config);
			parent::__destruct();
		}

		/*
		 * searches an Item-ID for an itemname
		 * @string $itemname
		 * @string $lang
		 * return @array array(0 => $item_id, 1 => $type)
		 */
		abstract protected function searchItemID($itemname, $lang);
		/*
		 * return $item array, with all information about an item
		 * @int $itemid
		 * @string $lang
		 * @string $itemname (optional, only if available)
		 * @string $type (optional, for e.g. spells, npcs, default: items; not for all parsers needed)
		 * return @array $item
		 */
		abstract protected function getItemData($item_id, $lang, $itemname='', $type='items');

		/*
		 * fetch $item array
		 * @string	$item_name
		 * @string	$lang
		 * @int		$game_id
		 * @string	$type (optional, see above)
		 */
		final public function getitem($item_name='', $lang=false, $game_id=false, $data=array()){
			$lang = ($lang) ? $lang : $this->config['game_language'];
			$name = trim($item_name);
			if(empty($name) && !$game_id) return null;
	
			if(!$game_id) {
				$item_id = $this->searchItemID($name, $lang);
			} else {
				$item_id[0] = $game_id;
				$item_id[1] = isset($data['type']) ? $data['type'] : 'items';
			}
			return $this->getItemData($item_id[0], $lang, $name, $item_id[1], $data);
		}
	}
}
?>