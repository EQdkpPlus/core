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

include('itt_parser.aclass.php');

if(!class_exists('lotro_lidb')) {
	class lotro_lidb extends itt_parser {
		public $supported_games = array('lotro');
		public $av_langs = array('de' => 'de_DE');#, 'en' => 'en_US', 'fr' => 'fr_FR', 'ru' => 'ru_RU', 'jp' => 'ja_JP');

		public $settings = array(
			'itt_icon_loc' => array('name' => 'itt_icon_loc',
									'language' => 'pk_itt_icon_loc',
									'fieldtype' => 'text',
									'size' => false,
									'options' => false,
									'default' => 'http://www.lidb.de/eqdkp_ico.php?name='),
			'itt_icon_ext' => array('name' => 'itt_icon_ext',
									'language' => 'pk_itt_icon_ext',
									'fieldtype' => 'text',
									'size' => false,
									'options' => false,
									'default' => ''),
			'itt_default_icon' => array('name' => 'itt_default_icon',
										'language' => 'pk_itt_default_icon',
										'fieldtype' => 'text',
										'size' => false,
										'options' => false,
										'default' => 'unknown')
		);

		protected function searchItemID($name, $lang){
			return array($name, 'items');
		}

		protected function getItemData($name, $lang, $itemname='', $type='items') {
			if(empty($name)) return array('baditem' => true);

			$item = array('name' => $name);
			$item['color'] = 'whitename';
			$item['lang'] = '';
			$item['icon'] = $name;
			$item['link'] = 'http://www.lidb.de/index.php?itemsuchen=1&name='.urlencode($name);
			$item['html'] = '<img src="http://www.lidb.de/eqdkp_stat.php?name='.$name.'" alt="'.$name.'" />';
			return $item;
		}
	}
}
?>