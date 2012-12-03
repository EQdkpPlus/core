<?php
 /*
 * Project:     EQdkp-Plus Infotooltip
 * License:     Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:       2009
 * Date:        $Date: 2008-12-02 11:54:02 +0100 (Di, 02 Dez 2008) $
 * -----------------------------------------------------------------------
 * @author      $Author: hoofy_leon $
 * @copyright   2009-2010 hoofy_leon
 * @link        http://eqdkp-plus.com
 * @package     infotooltip
 * @version     $Rev: 3293 $
 *
 * $Id:   $
 */

if(!class_exists('itt_parser')) {
  abstract class itt_parser {
	protected $root_path = '';
	public $config = array();
	protected $cache = false;
	protected $urlreader = false;
	protected $pdl = false;

	public $supported_games = array();
	public $av_langs = array();

	public function __construct($init=false, $config=false, $root_path=false, $cache=false, $urlreader=false, $pdl=false) {
		$this->config = $config;
		if(!$init) {
			return true;
		}
		$this->root_path = $root_path;
		$this->cache = $cache;
		$this->urlreader = $urlreader;
		$this->pdl = $pdl;
	}

	public function __destruct()
	{
		unset($this->cache);
		unset($this->urlreader);
		unset($this->config);
		unset($this->root_path);
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
	 * @string/@int $item_name
	 * @string $lang
	 * @bool $game_id (true: $item_name ist @int und die Item-ID)
	 * @string $type (optional, see above)
	 */
	public function getitem($item_name, $lang=false, $game_id=false, $type=false, $data=array())
	{
		$lang = ($lang) ? $lang : $this->config['game_language'];
		$name = trim($item_name);
		if(empty($name)) return null;

		if(!$game_id) {
			$item_id = $this->searchItemID($name, $lang);
		} else {
			$item_id[0] = $item_name;
			$item_id[1] = ($type) ? $type : 'items';
		}
		return $this->getItemData($item_id[0], $lang, $name, $item_id[1], $data);
	}
  }
}

?>