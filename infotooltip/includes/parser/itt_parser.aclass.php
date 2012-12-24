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
		final public function getitem($item_name='', $lang=false, $game_id=false, $type=false, $data=array()){
			$lang = ($lang) ? $lang : $this->config['game_language'];
			$name = trim($item_name);
			if(empty($name) && !$game_id) return null;
	
			if(!$game_id) {
				$item_id = $this->searchItemID($name, $lang);
			} else {
				$item_id[0] = $game_id;
				$item_id[1] = ($type) ? $type : 'items';
			}
			return $this->getItemData($item_id[0], $lang, $name, $item_id[1], $data);
		}
	}
}
?>