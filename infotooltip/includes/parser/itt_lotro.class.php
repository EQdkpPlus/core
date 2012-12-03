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

include_once('itt_parser.aclass.php');

if(!class_exists('itt_lotro')) {
	class itt_lotro extends itt_parser {
		public static $shortcuts = array('pdl', 'puf' => 'urlfetcher');

		public $supported_games = array('lotro');
		public $av_langs = array('en' => 'en_US');#, 'de' => 'de_DE', 'fr' => 'fr_FR', 'ru' => 'ru_RU', 'jp' => 'ja_JP');

		public $settings = array(
			'itt_icon_loc' => array('name' => 'itt_icon_loc',
									'language' => 'pk_itt_icon_loc',
									'fieldtype' => 'text',
									'size' => false,
									'options' => false,
									'default' => 'http://content.turbine.com/sites/lorebook.lotro.com/images/icons/'),
			'itt_icon_ext' => array('name' => 'itt_icon_ext',
									'language' => 'pk_itt_icon_ext',
									'fieldtype' => 'text',
									'size' => false,
									'options' => false,
									'default' => '.png'),
			'itt_default_icon' => array('name' => 'itt_default_icon',
										'language' => 'pk_itt_default_icon',
										'fieldtype' => 'text',
										'size' => false,
										'options' => false,
										'default' => 'skill/default')
		);

		protected function u_construct() {}
		protected function u_destruct() {}

		protected function searchItemID($itemname, $lang, $searchagain=0){
			# http://lorebook.lotro.com/index.php?title=Armour:Hardened_Elven_Officer%27s_Vibrant_Helm&action=edit
			# http://lorebook.lotro.com/index.php?title=Item:Ancient_Signet_Pendant&action=edit
			# http://lorebook.lotro.com/index.php?title=Weapon:Alrekur%27s_Spear&action=edit
			$this->pdl->log('infotooltip', 'itt_lotro->searchItemID called: itemname: '.$itemname.', lang: '.$lang.', searchagain: '.$searchagain);
			$types = array('armour', 'item', 'weapon'); #for the url
			$id_id = array('armor', 'item', 'weapon'); #identifier for ID in code
			while($searchagain < count($types)) {
				$encoded_name = urlencode(str_replace(' ','_',$itemname));
				$link = 'http://lorebook.lotro.com/index.php?title='.$types[$searchagain].':'.$encoded_name.'&action=edit';
				$this->pdl->log('infotooltip', 'Search for ItemID at '.$link);
				$data = $this->puf->fetch($link);
				if($start = strpos($data, 'textarea')) {
					$text = str_replace("\n", '', substr($data, $start, 1000));
					preg_match_all('#'.$id_id[$searchagain].' id=&quot;([0-9]*)&quot; name=#', $text, $matches);
					if($matches[1][0] > 0) {
						$this->pdl->log('infotooltip', 'Item-ID found: '.$matches[1][0]);
						return array($matches[1][0], $types[$searchagain]);
					}
				}
				if($searchagain < (count($types)-1)) $this->pdl->log('Nothing found. Search again in another category');
				$searchagain++;
			}
			$this->pdl->log('No Item-ID found.');
			return false;
		}

		protected function getItemData($item_id, $lang, $itemname='', $type='item'){
			$this->pdl->log('infotooltip', 'itt_lotro->getItemData called: item_id: '.$item_id.', lang: '.$lang.', itemname: '.$itemname.', type: '.$type);
			if($item_id < 1) {
				$this->pdl->log('infotooltip', 'No Item-ID given. Set baditem to true.');
				return array('name' => $itemname, 'html' => '{DEBUG}', 'baditem' => true);
			}
			# http://lorebook.lotro.com/wiki/index.php?action=ajax&rs=efLotroResourceAjaxWrapper&rsargs[]=rulingring&rsargs[]=Special:LotroResource?id=1879213025
			/* <?xml version="1.0" encoding="UTF-8"?>
<apiresponse>
 <item id="1879213025" name="Herdsman's Scarred Blade" level="66" type="One-handed Sword" quality="Uncommon" unique="1" isItemAdvancement="0" stackSize="1" bindOnAcquire="1" bindOnEquip="0" consumedOnUse="0" cooldown="" iconUrl="http://content.turbine.com/sites/lorebook.lotro.com/images/icons/item/weapon_sword/eq_trumdreng_1hsword_01.png" decoration="" instrument="" slotShortName="Weapon_Sword" slotName="Swords">
  <requirements>
    <level/>
    <gloryRank/>
    <traits/>
    <races/>
    <classes/>
    <factions/>

  </requirements>
  <value baseCopperValue="644" gold="0" silver="6" copper="44"/>
  <durability points="100" type="Normal"/>
  <damage dps="61" minimum="87" maximum="Max: 145" type="Common"/>
  <armour/>
  <effects>
<effect><![CDATA[+45 Might]]></effect>
<effect><![CDATA[+45 Agility]]></effect>
<effect><![CDATA[+264 Physical Mastery Rating]]></effect>
</effects>
  <set/>

  <description></description>
</item><cache_info cached_until_gmt="2012-03-04 14:53"/></apiresponse>
*/
	/*		$RulingRing.registerItem('Special:LotroResource?id=1879213025', 'lorebook', {
  name_enus: 'Special:LotroResource?id=1879213025',
  icon: '',
  tooltip_enus: '<table class="tooltip" cellspacing="0" cellpadding="0">  <tr class="tt_tr">    <td><img src="http://content.turbine.com/sites/lorebook.lotro.com/images/skins/moria/tc_tl.gif" /></td>    <td class="tbt"></td>    <td><img src="http://content.turbine.com/sites/lorebook.lotro.com/images/skins/moria/tc_tr.gif" /></td>  </tr>  <tr>    <td class="tbl"></td>    <td class="tooltipbody"><div class="itemicon"><img src="http://content.turbine.com/sites/lorebook.lotro.com/images/icons/item/weapon_sword/eq_trumdreng_1hsword_01.png" /></div><div class="itemname uncommon">Herdsman\'s Scarred Blade</div><div class="visualClear"></div><div><table width="100%"><tr><td style="text-align: left;"><div class="itembind">Bind on Acquire</div></td><td style="text-align: right;"><div class="itemunique">Unique</div></td></tr></table></div><div class="itemtype">One-handed Sword</div><div class="itemdi">  <div class="itemdamage">87 - 145 Common Damage</div>  <table width="100%">    <tr>      <td style="text-align: left;"><div class="itemdps">61 DPS</div></td>    </tr>  </table></div><div class="itemes">    <div class="iteme">        <div>+45 Might</div>  </div>    <div class="iteme">        <div>+45 Agility</div>  </div>    <div class="iteme">        <div>+264 Physical Mastery Rating</div>  </div>  </div><table width="100%" class="itemdur"><tr><td style="text-align: left;"><div class="itemdurability">Durability 100 / 100</div></td><td style="text-align: right;"><div class="itemsturdiness">Normal</div></td></tr></table><div class="itemsw"><div class="itemworth">Worth&nbsp;&nbsp;&nbsp;6<img class="coin" src="http://content.turbine.com/sites/lorebook.lotro.com/images/icons/currency/silver.gif" />&nbsp;44<img class="coin" src="http://content.turbine.com/sites/lorebook.lotro.com/images/icons/currency/copper.gif" /></div></div></td>    <td class="tbr"></td>  </tr>  <tr class="tt_br">    <td><img src="http://content.turbine.com/sites/lorebook.lotro.com/images/skins/moria/tc_bl.gif" /></td>    <td class="tbb"></td>    <td><img src="http://content.turbine.com/sites/lorebook.lotro.com/images/skins/moria/tc_br.gif" /></td>  </tr></table>',
  buff_enus: ''
});*/

			$data_link = 'http://lorebook.lotro.com/wiki/index.php?action=ajax&rs=efLotroResourceAjaxWrapper&rsargs[]=rulingring&rsargs[]=Special:LotroResource?id='.$item_id;
			$this->pdl->log('infotooltip', 'Fetch Item-Info from '.$data_link);
			$data = $this->puf->fetch($data_link);
			preg_match_all('#tooltip_enus:\s\'(.*?)\',#', $data, $matches);
			preg_match_all('#<div class="itemname ([a-z]*)">#', $matches[1][0], $matches1);
			preg_match_all('#<div class="itemicon"><img src="http://content\.turbine\.com/sites/lorebook\.lotro\.com/images/icons/([a-z0-9_/]*)\.png" \/>#', $data, $matches2);
			

			$encoded_name = urlencode(str_replace(' ','_',$itemname));
			$item['link'] = 'http://lorebook.lotro.com/wiki/'.$type.':'.$encoded_name;
			$item['id'] = $item_id;
			$item['name'] = $itemname;
			$item['icon'] = $matches2[1][0];
			$item['lang'] = $lang;
			$item['color'] = $matches1[1][0];
			//filter baditems
			if(strlen($matches[1][0]) < 15 || strpos($matches[1][0], 'No tooltip available')) {
				$this->pdl->log('infotooltip', 'No Tooltip could be found. Set baditem to true.');
				$item['baditem'] = true;
				$item['html'] = '{DEBUG}';
			} else {
				//inject {DEBUG}
				$html = stripslashes($matches[1][0]);
				$html = preg_replace('#<div class="itemworth">(.*?)</div>#', '<div class="itemworth">\1</div>{DEBUG}', $html);
				$item['html'] = $html;
			}
			if(strpos($item['html'], '{DEBUG}') === false) $item['html'] .= '<br />{DEBUG}';

			$this->pdl->log('infotooltip', 'Item succesfully fetched.');
			return $item;
		}
	}
}
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_itt_lotro', itt_lotro::$shortcuts);
?>