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

if ( !defined('EQDKP_INC') ){
	header('HTTP/1.0 404 Not Found');exit;
}

if(!class_exists('wow')) {
	class wow extends game_generic {
		public static $shortcuts = array('pdh', 'game', 'pm', 'html', 'user', 'config', 'jquery', 'in', 'time');

		protected $this_game	= 'wow';
		protected $types		= array('classes', 'races', 'factions', 'filters', 'realmlist', 'roles', 'professions', 'chartooltip');	// which information are stored?
		public $icons			= array('classes', 'classes_big', 'races', 'roles', 'ranks', 'events', 'talents', '3dmodel');	// which icons do we have?
		protected $classes		= array();
		protected $roles		= array();
		protected $races		= array();															// for each type there must be the according var
		protected $factions		= array();															// and the according function: load_$type
		protected $filters		= array();
		protected $realmlist	= array();
		protected $professions	= array();
		public $objects			= array('bnet_armory');												// eventually there are some objects (php-classes) in this game
		public $no_reg_obj		= array('bnet_armory');												// a list with all objects, which dont need registry
		public $langs			= array('english', 'german');										// in which languages do we have information?
		public $importers 		= array(
			'char_import'		=> 'charimporter.php',						// filename of the character import
			'char_update'		=> 'charimporter.php',						// filename of the character update, member_id (POST) is passed
			'char_mupdate'		=> 'charimporter.php?massupdate=true',		// filename of the "update all characters" aka mass update
			'guild_import'		=> 'guildimporter.php',						// filename of the guild import
			'import_reseturl'	=> 'charimporter.php?resetcache=true',		// filename of the reset cache
			'guild_imp_rsn'		=> true,									// Guild import & Mass update requires server name
			'import_data_cache'	=> true,									// Is the data cached and requires a reset call?
		);
		protected $ArrInstanceCategories = array(
			'classic'	=> array(2717, 2677, 3429, 3428),
			'bc'		=> array(3457, 3836, 3923, 3607, 3845, 3606, 3959, 4075),
			'wotlk'		=> array(4603, 3456, 4493, 4500, 4273, 2159, 4722, 4812, 4987),
			'cataclysm'	=> array(5600, 5094, 5334, 5638, 5723, 5892),
			'mop'		=> array(6125, 6297, 6067, 6622, 6738)
		);

		protected $glang		= array();
		protected $lang_file	= array();
		protected $path			= '';
		public $lang			= false;
		public $version			= '5.4.4';

		public function __construct() {
			parent::__construct();
			$this->pdh->register_read_module($this->this_game, $this->path . 'pdh/read/'.$this->this_game);
		}
		
		public function chartooltip($intCharID){
			$template = $this->root_path.'games/'.$this->this_game.'/chartooltip/chartooltip.tpl';
			$content = file_get_contents($template);
			$charicon = $this->pdh->get('wow', 'charicon', array($intCharID));
			if ($charicon == '') {
				$charicon = $this->root_path.'images/no_pic.png';
			}
			$charhtml = '<b>'.$this->pdh->get('member', 'html_name', array($intCharID)).'</b><br />';
			$guild = $this->pdh->get('member', 'profile_field', array($intCharID, 'guild'));
			if (strlen($guild)) $charhtml .= '<br />&laquo;'.$guild.'&raquo;';
			
			$charhtml .= '<br />'.$this->pdh->get('member', 'html_racename', array($intCharID));
			$charhtml .= ' '.$this->pdh->get('member', 'html_classname', array($intCharID));
			$charhtml .= '<br />'.$this->user->lang('level').' '.$this->pdh->get('member', 'level', array($intCharID));
			
			
			$content = str_replace('{CHAR_ICON}', $charicon, $content);
			$content = str_replace('{CHAR_HTML}', $charhtml, $content);
			
			return $content;
		}
		
		
		/**
		 * Returns Information to change the game
		 *
		 * @param bool $install
		 * @return array
		 */
		public function get_OnChangeInfos($install=false){
			//classcolors
			$info['class_color'] = array(
				1	=> '#C41F3B',
				2	=> '#FF7C0A',
				3	=> '#AAD372',
				4	=> '#68CCEF',
				5	=> '#F48CBA',
				6	=> '#FFFFFF',
				7	=> '#FFF468',
				8	=> '#1a3caa',
				9	=> '#9382C9',
				10	=> '#C69B6D',
				11	=> '#00C77B',
			);

			//config-values
			$info['config'] = array();

			//lets do some tweak on the templates dependent on the game
			$info['aq'] = array();

			//Do this SQL Query NOT if the Eqdkp is installed -> only @ the first install
			if($install){

				// mop events
				array_push($info['aq'], 'INSERT INTO __events (event_id, event_name, event_value, event_added_by, event_updated_by, event_icon) VALUES (1, "'.$this->glang('mop_mogushan_10').'", 0.00, "default", NULL, "mv.png"); ');
				array_push($info['aq'], 'INSERT INTO __events (event_id, event_name, event_value, event_added_by, event_updated_by, event_icon) VALUES (2, "'.$this->glang('mop_mogushan_25').'", 0.00, "default", NULL, "mv.png"); ');
				array_push($info['aq'], 'INSERT INTO __events (event_id, event_name, event_value, event_added_by, event_updated_by, event_icon) VALUES (3, "'.$this->glang('mop_heartoffear_10').'", 0.00, "default", NULL, "hf.png"); ');
				array_push($info['aq'], 'INSERT INTO __events (event_id, event_name, event_value, event_added_by, event_updated_by, event_icon) VALUES (4, "'.$this->glang('mop_heartoffear_25').'", 0.00, "default", NULL, "hf.png"); ');
				array_push($info['aq'], 'INSERT INTO __events (event_id, event_name, event_value, event_added_by, event_updated_by, event_icon) VALUES (5, "'.$this->glang('mop_endlessspring_10').'", 0.00, "default", NULL, "tes.png"); ');
				array_push($info['aq'], 'INSERT INTO __events (event_id, event_name, event_value, event_added_by, event_updated_by, event_icon) VALUES (6, "'.$this->glang('mop_endlessspring_25').'", 0.00, "default", NULL, "tes.png"); ');
				array_push($info['aq'], 'INSERT INTO __events (event_id, event_name, event_value, event_added_by, event_updated_by, event_icon) VALUES (7, "'.$this->glang('mop_throneofthunder_10').'", 0.00, "default", NULL, "tot.png"); ');
				array_push($info['aq'], 'INSERT INTO __events (event_id, event_name, event_value, event_added_by, event_updated_by, event_icon) VALUES (8, "'.$this->glang('mop_throneofthunder_25').'", 0.00, "default", NULL, "tot.png"); ');
				array_push($info['aq'], 'INSERT INTO __events (event_id, event_name, event_value, event_added_by, event_updated_by, event_icon) VALUES (13, "'.$this->glang('mop_siegeoforgrimmar').'", 0.00, "default", NULL, "soo.png"); ');

				//Default Events
				array_push($info['aq'], 'INSERT INTO __events (event_id, event_name, event_value, event_added_by, event_updated_by, event_icon) VALUES(9, "'.$this->glang('wotlk').'", 0.00, "default", NULL, "wotlk.png"); ');
				array_push($info['aq'], 'INSERT INTO __events (event_id, event_name, event_value, event_added_by, event_updated_by, event_icon) VALUES(10, "'.$this->glang('cataclysm').'", 0.00, "default", NULL, "cata.png"); ');
				array_push($info['aq'], 'INSERT INTO __events (event_id, event_name, event_value, event_added_by, event_updated_by, event_icon) VALUES(11, "'.$this->glang('burning_crusade').'", 0.00, "default", NULL, "bc.png"); ');
				array_push($info['aq'], 'INSERT INTO __events (event_id, event_name, event_value, event_added_by, event_updated_by, event_icon) VALUES(12, "'.$this->glang('classic').'", 0.00, "default", NULL, "classic.png"); ');

				//Connect them to the Default-Multidkp-Pool
				array_push($info['aq'], 'INSERT INTO __multidkp (multidkp_id, multidkp_name, multidkp_desc) VALUES (2, "classic", "Classic-Pool");');
				array_push($info['aq'], 'INSERT INTO __multidkp2event (multidkp2event_multi_id, multidkp2event_event_id) VALUES (1, 1), (1, 2), (1, 3), (1, 4), (1, 5), (1, 6), (1, 7), (1, 8), (2, 9), (2, 10), (2, 11), (2, 12), (2, 13);');
				array_push($info['aq'], 'INSERT INTO __itempool (itempool_id, itempool_name, itempool_desc) VALUES (2, "classic", "Classic itempool");');
				array_push($info['aq'], 'INSERT INTO __multidkp2itempool (multidkp2itempool_itempool_id, multidkp2itempool_multi_id) VALUES (2, 2);');

				//default links
				array_push($info['aq'], "INSERT INTO __links (`link_url`, `link_name`, `link_window`, `link_menu`) VALUES ('http://eu.battle.net/wow/', 'WoW Battle.net', 1, 0);");
				
				$this->pdh->add_object_tablepreset($this->config->get('eqdkp_layout'), 'roster', 'hptt_roster',
					array('name' => 'wow_charicon', 'sort' => false, 'th_add' => 'width="52"', 'td_add' => '')
				);
				
				$this->pdh->add_object_tablepreset($this->config->get('eqdkp_layout'), 'roster', 'hptt_roster',
					array('name' => 'profile_guild', 'sort' => true, 'th_add' => 'width="160"', 'td_add' => '')
				);
				
				$this->pdh->add_object_tablepreset($this->config->get('eqdkp_layout'), 'roster', 'hptt_roster',
					array('name' => 'wow_achievementpoints', 'sort' => true, 'th_add' => 'width="160"', 'td_add' => '')
				);

			}
			return $info;
		}

		/**
		 * Initialises filters
		 *
		 * @param array $langs
		 */
		protected function load_filters($langs){
			if(!count($this->classes)) {
				$this->load_type('classes', $langs);
			}
			foreach($langs as $lang) {
				$names = $this->classes[$this->lang];
				$this->filters[$lang] = array(
					array('name' => '-----------', 'value' => false),
					array('name' => $names[0], 'value' => 'class:0'),
					array('name' => $names[1], 'value' => 'class:1'),
					array('name' => $names[2], 'value' => 'class:2'),
					array('name' => $names[3], 'value' => 'class:3'),
					array('name' => $names[4], 'value' => 'class:4'),
					array('name' => $names[5], 'value' => 'class:5'),
					array('name' => $names[6], 'value' => 'class:6'),
					array('name' => $names[7], 'value' => 'class:7'),
					array('name' => $names[8], 'value' => 'class:8'),
					array('name' => $names[9], 'value' => 'class:9'),
					array('name' => $names[10], 'value' => 'class:10'),
					array('name' => $names[11], 'value' => 'class:11'),
					array('name' => '-----------', 'value' => false),
					array('name' => $this->glang('plate', true, $lang), 'value' => 'class:1,5,10'),
					array('name' => $this->glang('mail', true, $lang), 'value' => 'class:3,8'),
					array('name' => $this->glang('leather', true, $lang), 'value' => 'class:2,7,11'),
					array('name' => $this->glang('cloth', true, $lang), 'value' => 'class:4,6,9'),
					array('name' => '-----------', 'value' => false),
					array('name' => $this->glang('tier_token', true, $lang).$names[3].', '.$names[10].', '.$names[8].', '.$names[11], 'value' => 'class:3,8,10,11'),
					array('name' => $this->glang('tier_token', true, $lang).$names[5].', '.$names[6].', '.$names[9], 'value' => 'class:5,6,9'),
					array('name' => $this->glang('tier_token', true, $lang).$names[1].', '.$names[2].', '.$names[4].', '.$names[7], 'value' => 'class:1,2,4,7'),
				);
			}
		}

		/*
		 * add professions to array
		 */
		public function get_profilefields(){
			$this->load_type('professions', array($this->lang));
			$professions = $this->professions[$this->lang];
			$xml_fields = parent::get_profilefields();
			$xml_fields['prof1_name']['options'] = $professions;
			$xml_fields['prof2_name']['options'] = $professions;
			return $xml_fields;
		}

		public function changeprofilefields(){
			$member_data	= $this->pdh->get('member', 'array', array($this->in->get('editid')));
			$talents_array = $this->jquery->dd_ajax_request('member_class_id', array('profilefields[skill_1]', 'profilefields[skill_2]'), $this->game->get('classes', array('id_0')), array('--------'), $member_data['class_id'], 'addcharacter.php?ajax=talents', '', array($member_data['skill_1'], $member_data['skill_2']));
			return array(
				'member_class_id'	=> array(
					'fieldtype'		=> 'direct',
					'category'		=> 'character',
					'name'			=> 'class_id',
					'language'		=> $this->user->lang('class'),
					'direct'		=> $talents_array[0],
					'directfield'	=> true,
					'visible'		=> true
				),
				'skill_1'	=> array(
					'fieldtype'		=> 'direct',
					'category'		=> 'character',
					'name'			=> 'skill_1',
					'language'		=> $this->game->glang('uc_skill1'),
					'direct'		=> $talents_array[1],
					'directfield'	=> true,
					'visible'		=> true
				),
				'skill_2'	=> array(
					'fieldtype'		=> 'direct',
					'category'		=> 'character',
					'name'			=> 'skill_2',
					'language'		=> $this->game->glang('uc_skill2'),
					'direct'		=> $talents_array[2],
					'directfield'	=> true,
					'visible'		=> true
				)
			);
		}

		public function gameprofile_talents($id){
			$talents = $this->game->glang('talents');
			return $this->jquery->dd_create_ajax($talents[$id]);
		}

		/*
		 * Per game data for the calendar Tooltip
		 */
		public function calendar_membertooltip($memberid){
			$talents		= $this->game->glang('talents');
			$member_data	= $this->pdh->get('member', 'array', array($memberid));
			$membertalents	= (isset($talents[$member_data['class_id']])) ? $talents[$member_data['class_id']] : array(0,0,0,0);
			return array(
				$this->game->glang('talents_tt_1').': '.((isset($membertalents[$member_data['skill_1']])) ? $membertalents[$member_data['skill_1']] : ' -- '),
				$this->game->glang('talents_tt_2').': '.((isset($membertalents[$member_data['skill_2']])) ? $membertalents[$member_data['skill_2']] : ' -- '),
			);
		}

		/*
		 * Parse the guild news of armory
		 */
		public function parseGuildnews($arrNews, $intCount = 50){
			$this->game->new_object('bnet_armory', 'armory', array($this->config->get('uc_server_loc'), $this->config->get('uc_data_lang')));

			$arrOut = array();

			$arrAchievementsData = $this->game->obj['armory']->getdata();
			$arrGuildAchievementsData = $this->game->obj['armory']->getdata('guild');

			if(is_array($arrNews)){
				$i = 0;
				foreach($arrNews as $val){
					if ($i == $intCount) break;
					
					switch($val['type']){
						case 'guildCreated':
						$arrOut[] = array(
							'text' => $this->glang('news_guildCreated'),
							'icon' => $this->root_path.'games/wow/roster/newsfeed_guild.png',
							'date' => substr($val['timestamp'], 0, -3),
						);
						break;
						case 'itemLoot':{
						$itemData = $this->game->obj['armory']->item($val['itemId']);
						$charID = register('pdh')->get('member', 'id', array(trim($val['character'])));
						if ($charID) {
							$charLink = register('pdh')->get('member', 'html_memberlink', array($charID, $this->root_path.'viewcharacter.php', '', false, false, true));
						} else {
							$charLink = $val['character'];
						}
						$arrOut[] = array(
							'text' => sprintf($this->glang('news_itemLoot'), $charLink, infotooltip($itemData['name'], $val['itemId'], false, false, false, true)),
							'icon' => "http://eu.media.blizzard.com/wow/icons/18/".$itemData['icon'].".jpg",
							'date' => substr($val['timestamp'], 0, -3),
						);
						}
						break;
						case 'itemPurchase':
						$itemData = $this->game->obj['armory']->item($val['itemId']);
						$charID = register('pdh')->get('member', 'id', array(trim($val['character'])));
						if ($charID) {
							$charLink = register('pdh')->get('member', 'html_memberlink', array($charID, $this->root_path.'viewcharacter.php', '', false, false, true));
						} else {
							$charLink = $val['character'];
						}
						$arrOut[] = array(
							'text' => sprintf($this->glang('news_itemPurchase'), $charLink, infotooltip($itemData['name'], $val['itemId'], false, false, false, true)),
							'icon' => "http://eu.media.blizzard.com/wow/icons/18/".$itemData['icon'].".jpg",
							'date' => substr($val['timestamp'], 0, -3),
						);
						break;
						case 'guildLevel':
						$arrOut[] = array(
							'text' => sprintf($this->glang('news_guildLevel'), $val['levelUp']),
							'icon' => $this->root_path.'games/wow/roster/newsfeed_guild.png',
							'date' => substr($val['timestamp'], 0, -3),
						);
						break;
						case 'guildAchievement':{
							$achievCat = $this->game->obj['armory']->getCategoryForAchievement((int)$val['achievement']['id'], $arrGuildAchievementsData);
							$bnetLink = $this->game->obj['armory']->bnlink($val['character'], $this->config->get('uc_servername'), 'guild-achievements', $this->config->get('guildtag')).'#'.$achievCat.':a'.$val['achievement']['id'];
						$arrOut[] = array(
							'text' => sprintf($this->glang('news_guildAchievement'), '<a href="'.$bnetLink.'">'.$val['achievement']['title'].'</a>', $val['achievement']['points']),
							'icon' => "http://eu.media.blizzard.com/wow/icons/18/".$val['achievement']['icon'].".jpg",
							'date' => substr($val['timestamp'], 0, -3),
						);
						}
						break;
						case 'playerAchievement':{
							$charID = register('pdh')->get('member', 'id', array(trim($val['character'])));
							if ($charID) {
								$charLink = register('pdh')->get('member', 'html_memberlink', array($charID, $this->root_path.'viewcharacter.php','', false, false, true));
							} else {
								$charLink = $val['character'];
							}
							$achievCat = $this->game->obj['armory']->getCategoryForAchievement((int)$val['achievement']['id'], $arrAchievementsData);
							$bnetLink = $this->game->obj['armory']->bnlink($val['character'], $this->config->get('uc_servername'), 'achievements').'#'.$achievCat.':a'.$val['achievement']['id'];
							$arrOut[] = array(
								'text' => sprintf($this->glang('news_playerAchievement'), $charLink, '<a href="'.$bnetLink.'">'.$val['achievement']['title'].'</a>', $val['achievement']['points']),
								'icon' => "http://eu.media.blizzard.com/wow/icons/18/".$val['achievement']['icon'].".jpg",
								'date' => substr($val['timestamp'], 0, -3),
							);
						}
						break;
					}
					$i++;
				}
			}
			return $arrOut;
		}

		/*
		 * parse the guild achievement overview of armory
		 */
		public function parseGuildAchievementOverview($arrAchievs){
			$this->game->new_object('bnet_armory', 'armory', array($this->config->get('uc_server_loc'), $this->config->get('uc_data_lang')));

			$arrGuildAchievementsData = $this->game->obj['armory']->getdata('guild', 'achievements');
			$arrOut = array();
			$done = array();
			$doneIDs = array();
			$arrOut['total'] = array(
				'total' => 0
			);
			foreach ($arrGuildAchievementsData['achievements'] as $arrCatAchievs){
				$completed = 0;
				$achievs = 0;

				foreach ($arrCatAchievs['achievements'] as $arrCatAchievs2){

					//if (isset($done[$arrCatAchievs2['title']])) continue;
					if (isset($doneIDs[$arrCatAchievs2['id']])) continue;
					$done[$arrCatAchievs2['title']] = true;
					$doneIDs[$arrCatAchievs2['id']] = true;

					if (in_array((int)$arrCatAchievs2['id'], $arrAchievs['achievementsCompleted'])) $completed++;
					$achievs++;
				}

				if (isset($arrCatAchievs['categories'])){
					foreach ($arrCatAchievs['categories'] as $arrCatAchievs2){

						foreach ($arrCatAchievs2['achievements'] as $arrCatAchievs3){
							//if (isset($done[$arrCatAchievs3['title']])) continue;
							if (isset($doneIDs[$arrCatAchievs3['id']])) continue;
							$done[$arrCatAchievs3['title']] = true;
							$doneIDs[$arrCatAchievs3['id']] = true;

							if (in_array((int)$arrCatAchievs3['id'], $arrAchievs['achievementsCompleted'])) $completed++;
							$achievs++;
						}
					}
				}

				$arrOut[$arrCatAchievs['id']] = array(
					'id'	=> $arrCatAchievs['id'],
					'name'	=> $arrCatAchievs['name'],
					'total' => $achievs,
					'completed' => $completed,
				);
			}

			//Now, let's cheat a bit
			$arrOut[15088]['total'] = $arrOut[15088]['total'] - 8;
			$arrOut[15078]['total'] = $arrOut[15078]['total'] - 13;
			$arrOut[15079]['total'] = $arrOut[15079]['total'] - 2;
			$arrOut[15089]['total'] = $arrOut[15089]['completed'];
			$arrOut[15093]['total'] = $arrOut[15093]['completed'];

			$total = 0;
			foreach ($arrOut as $val){
				$total += $val['total'];
			}

			$arrOut['total'] = array(
				'total' 	=> $total,
				'completed' => count($arrAchievs['achievementsCompleted']),
				'name' 		=> $this->glang('guildachievs_total_completed'),
			);

			return $arrOut;
		}

		/*
		 * parse the guild achievement overview of armory
		 */
		public function parseCharAchievementOverview($chardata){
			$this->game->new_object('bnet_armory', 'armory', array($this->config->get('uc_server_loc'), $this->config->get('uc_data_lang')));

			$arrAchievs = $chardata['achievements'];
			$arrCharAchievementsData = $this->game->obj['armory']->getdata('character', 'achievements');
			$arrOut = array();
			$done = array();
			$doneIDs = array();
			$arrOut['total'] = array(
				'total' => 0
			);
			foreach ($arrCharAchievementsData['achievements'] as $arrCatAchievs){
				$completed = 0;
				$achievs = 0;

				foreach ($arrCatAchievs['achievements'] as $arrCatAchievs2){

					//if (isset($done[$arrCatAchievs2['title']])) continue;
					if (isset($doneIDs[$arrCatAchievs2['id']])) continue;
					$done[$arrCatAchievs2['title']] = true;
					$doneIDs[$arrCatAchievs2['id']] = true;

					if (in_array((int)$arrCatAchievs2['id'], $arrAchievs['achievementsCompleted'])) $completed++;
					$achievs++;
				}

				if (isset($arrCatAchievs['categories'])){
					foreach ($arrCatAchievs['categories'] as $arrCatAchievs2){

						foreach ($arrCatAchievs2['achievements'] as $arrCatAchievs3){
							//if (isset($done[$arrCatAchievs3['title']])) continue;
							if (isset($doneIDs[$arrCatAchievs3['id']])) continue;
							$done[$arrCatAchievs3['title']] = true;
							$doneIDs[$arrCatAchievs3['id']] = true;

							if (in_array((int)$arrCatAchievs3['id'], $arrAchievs['achievementsCompleted'])) $completed++;
							$achievs++;
						}
					}
				}

				$arrOut[$arrCatAchievs['id']] = array(
					'id'	=> $arrCatAchievs['id'],
					'name'	=> $arrCatAchievs['name'],
					'total' => $achievs,
					'completed' => $completed,
				);
			}

			$total = 0;
			foreach ($arrOut as $val){
				$total += $val['total'];
			}

			$arrOut['total'] = array(
				'total' 	=> $total,
				'completed' => count($arrAchievs['achievementsCompleted']),
				'name' 		=> $this->glang('guildachievs_total_completed'),
			);

			return $arrOut;
		}

		/*
		 * parse the latest guild achievements of armory
		 */
		public function parseLatestGuildAchievements($arrAchievs, $intCount = 10){
			$this->game->new_object('bnet_armory', 'armory', array($this->config->get('uc_server_loc'), $this->config->get('uc_data_lang')));

			$arrAchieveTimes = $arrAchievs['achievementsCompletedTimestamp'];
			$arrAchievs		 = $arrAchievs['achievementsCompleted'];
			array_multisort($arrAchieveTimes, SORT_DESC, SORT_NUMERIC, $arrAchievs);
			$count = 0;
			$arrGuildAchievementsData = $this->game->obj['armory']->getdata('guild', 'achievements');

			$arrAchievsOut = array();
			foreach($arrAchievs as $key => $achievID){
				if ($count == $intCount) break;
				$count++;
				$achievData = $this->game->obj['armory']->achievement($achievID);
				if ($achievData){
					$arrAchievsOut[] = array(
						'name'	=> '<a href="'.$this->game->obj['armory']->bnlink('', $this->config->get('uc_servername'), 'guild-achievements', $this->config->get('guildtag')).'#'.$this->game->obj['armory']->getCategoryForAchievement($achievID, $arrGuildAchievementsData).':a'.$achievID.'">'.$achievData['title'].'</a>',
						'icon'	=> '<img src="http://eu.media.blizzard.com/wow/icons/18/'.$achievData['icon'].'.jpg" alt="" />',
						'desc'	=> $achievData['description'],
						'points'=> $achievData['points'],
						'date'	=> substr($arrAchieveTimes[$key], 0, -3),
					);
				}
			}
			return $arrAchievsOut;
		}

		/*
		 * parse the latest char achievements of armory
		 */
		public function parseLatestCharAchievements($chardata, $charname, $intCount = 10){
			$this->game->new_object('bnet_armory', 'armory', array($this->config->get('uc_server_loc'), $this->config->get('uc_data_lang')));

			$arrAchievs			= $chardata['achievements'];
			$arrAchieveTimes	= $arrAchievs['achievementsCompletedTimestamp'];
			$arrAchievs			= $arrAchievs['achievementsCompleted'];
			array_multisort($arrAchieveTimes, SORT_DESC, SORT_NUMERIC, $arrAchievs);
			$count = 0;
			$arrCharAchievementsData = $this->game->obj['armory']->getdata('character', 'achievements');

			$arrAchievsOut = array();
			foreach($arrAchievs as $key => $achievID){
				if ($count == $intCount) break;
				$count++;
				$achievData = $this->game->obj['armory']->achievement($achievID);
				if ($achievData){
					$class = ($achievData['accountWide'] == 1) ? 'accountwide' : '';
					$arrAchievsOut[] = array(
						'name'	=> '<a href="'.$this->game->obj['armory']->bnlink($charname, $this->config->get('uc_servername'), 'achievements').'#'.$this->game->obj['armory']->getCategoryForAchievement($achievID, $arrCharAchievementsData).':a'.$achievID.'" class="'.$class.'">'.$achievData['title'].'</a>',
						'icon'	=> '<img src="http://eu.media.blizzard.com/wow/icons/18/'.$achievData['icon'].'.jpg" alt="" />',
						'desc'	=> $achievData['description'],
						'points'=> $achievData['points'],
						'date'	=> substr($arrAchieveTimes[$key], 0, -3),

					);
				}
			}
			return $arrAchievsOut;
		}

		/*
		 * parse the guild challenges of armory
		 */
		public function parseGuildChallenge($arrInput){
			$arrChallengeOut	= array();
			foreach($arrInput['challenge'] as $a_values){
				$a_groupout = array();
				foreach($a_values['groups'] as $a_groupid => $a_groups){
					$a_membersout = array();
					foreach($a_groups['members'] as $a_memid => $a_members){
						if(isset($a_members['character']['name']) && $a_members['character']['name'] != ''){
							$memberid = $this->pdh->get('member', 'id', array($a_members['character']['name']));
							$a_membersout[] = array(
								'name'			=> $a_members['character']['name'],
								'realm'			=> $a_members['character']['realm'],
								'guild'			=> $a_members['character']['guild'],
								'class'			=> $this->game->obj['armory']->ConvertID($a_members['character']['class'], 'int', 'classes'),
								'off_realm'		=> ($this->config->get('uc_servername') != $a_members['character']['realm']) ? true : false,
								'memberid'		=> (isset($memberid) && $memberid > 0) ? $memberid : 0,
							);
						}
					}
					$a_groupout[] = array(
						'name'		=> $a_groups['ranking'],
						'medal'		=> $a_groups['medal'],
						'faction'	=> $a_groups['faction'],
						'date'		=> $this->time->user_date($this->time->fromformat($a_groups['date'], 1)),
						'time'		=> sprintf('%02d', $a_groups['time']['hours']).':'.sprintf('%02d', $a_groups['time']['minutes']).':'.sprintf('%02d', $a_groups['time']['seconds']),
						'members'	=> $a_membersout
					);
				}
				$arrChallengeOut[$a_values['map']['id']] = array(
					'name'		=> $a_values['map']['name'],
					'icon'		=> $a_values['map']['slug'],
					'time'		=> '',
					'group'		=> $a_groupout
				);
			}
			return $arrChallengeOut;
		}

		/*
		 * generate an array with the profession data
		 */
		public function professions($chardata){
			$professions = array();
			if (is_array($chardata['professions']['primary'])) {
				foreach ($chardata['professions']['primary'] as $k_profession => $v_profession){
					$akt = (int)$v_profession['rank'];
					$max = (int)$v_profession['max'];
	
					if($akt>$max){
						$max = $akt;
					}
	
					$professions[$k_profession] = array(
						'name'			=> $v_profession['name'],
						'icon'			=> "games/wow/profiles/professions/".(($v_profession['icon']) ? $v_profession['icon'] : '0').".jpg",
						'progressbar'	=> $this->html->bar($akt, $max, 110, "")
					);
				}
			}
			return $professions;
		}

		/*
		 * generate an array with the talent data
		 */
		public function talents($chardata){
			$talents = array();
			if (is_array($chardata['talents'])){
				$talents = array();
				foreach ($chardata['talents'] as $v_talents){

					// fetch the specialization and put it in an array
					$spezialisation = array();
					if(isset($v_talents['talents']) && is_array($v_talents['talents'])){
						foreach($v_talents['talents'] as $v_spezialisation){
							$spezialisation[$v_spezialisation['tier']] = array(
								'name'			=> $v_spezialisation['spell']['name'],
								'description'	=> $v_spezialisation['spell']['description'],
								'icon'			=> sprintf('http://eu.media.blizzard.com/wow/icons/18/%s.jpg', $v_spezialisation['spell']['icon'])
							);
						}
					}

					// glyphs
					$glyphs = array();
					if(isset($v_talents['glyphs']) && is_array($v_talents['glyphs'])){
						foreach($v_talents['glyphs'] as $id_glyphs => $v_glyphs){
							foreach($v_glyphs as $v_glyph){
								$glyphs[$id_glyphs][] = array(
									'name'		=> $v_glyph['name'],
									'item'		=> $v_glyph['item'],
									'icon'		=> sprintf('http://eu.media.blizzard.com/wow/icons/18/%s.jpg', $v_glyph['icon'])
								);
							}
						}
					}

					$talents[] = array(
						'selected'		=> (isset($v_talents['selected']) && $v_talents['selected'] == '1') ? '1' : '0',
						'name'			=> (isset($v_talents['spec']['name']) && $v_talents['spec']['name']) ? $v_talents['spec']['name'] : $this->game->glang('not_assigned'),
						'icon'			=> $this->game->obj['armory']->talentIcon(((isset($v_talents['spec']['icon']) && $v_talents['spec']['icon']) ? $v_talents['spec']['icon'] : 'inv_misc_questionmark')),
						'role'			=> $v_talents['spec']['role'],
						'desc'			=> $v_talents['spec']['description'],
						'calcTalent'	=> $v_talents['calcTalent'],
						'calcSpec'		=> $v_talents['calcSpec'],
						'calcGlyph'		=> $v_talents['calcGlyph'],
						'talents'		=> $spezialisation,
						'glyphs'		=> $glyphs
					);
				}
			}
			return $talents;
		}

		public function ParseCharNews($chardata, $amount=10){
			$charfeed = array();
			if(is_array($chardata['feed'])){
				$ii = 0;
				foreach($chardata['feed'] as $d_charfeed){
					switch ($d_charfeed['type']){
						case 'ACHIEVEMENT':
							$charfeed[] = array(
								'type'		=> 'achievement',
								'timestamp'	=> $d_charfeed['timestamp']/ 1000,
								'title'		=> $d_charfeed['achievement']['title'],
								'points'	=> $d_charfeed['achievement']['points'],
								'icon'		=> sprintf('http://eu.media.blizzard.com/wow/icons/18/%s.jpg', $d_charfeed['achievement']['icon']),
								'hero'		=> ($d_charfeed['featOfStrength'] == 1) ? true : false,
								'achievementID' => $d_charfeed['achievement']['id'],
								'accountWide'=> ($d_charfeed['achievement']['accountWide'] == 1) ? true : false,
							);
						break;
						case 'BOSSKILL':
							$charfeed[] = array(
								'type'		=> 'bosskill',
								'timestamp'	=> $d_charfeed['timestamp']/ 1000,
								'title'		=> $d_charfeed['achievement']['title'],
								'icon'		=> sprintf('http://eu.media.blizzard.com/wow/icons/18/%s.jpg', $d_charfeed['achievement']['icon']),
								'quantity'  => $d_charfeed['quantity'],
							);
						break;
						case 'CRITERIA':
							$charfeed[] = array(
								'type'		=> 'criteria',
								'timestamp'	=> $d_charfeed['timestamp']/ 1000,
								'criteria'	=> $d_charfeed['criteria']['description'],
								'title'		=> $d_charfeed['achievement']['title'],
								'achievementID' => $d_charfeed['achievement']['id'],
								'icon'		=> sprintf('http://eu.media.blizzard.com/wow/icons/18/%s.jpg', $d_charfeed['achievement']['icon'])
							);
						break;
						case 'LOOT':
							$charfeed[] = array(
								'type'		=> 'item',
								'timestamp'	=> $d_charfeed['timestamp']/ 1000,
								'itemid'	=> $d_charfeed['itemId']
							);
						break;
					}
					// end parse process when amount is reached
					$ii++;
					if($ii == $amount){ break; }
				}
			}
			return $charfeed;
		}

		/**
		 * Return an array(left,right,button) with the wow char icons
		 *
		 * @param array $data
		 * @param string $member_name
		 * @return array
		 */
		public function getItemArray($data, $member_name, $icons_size = 53){
			$d_itemoptions = array(
				'head'		=> array('position' => 'left',		'bnetid' => '0'),
				'neck'		=> array('position' => 'left',		'bnetid' => '1'),
				'shoulder'	=> array('position' => 'left',		'bnetid' => '2'),
				'back'		=> array('position' => 'left',		'bnetid' => '14'),
				'chest'		=> array('position' => 'left',		'bnetid' => '4'),
				'shirt'		=> array('position' => 'left',		'bnetid' => '3'),
				'tabard'	=> array('position' => 'left',		'bnetid' => '18'),
				'wrist'		=> array('position' => 'left',		'bnetid' => '8'),

				'hands'		=> array('position' => 'right',		'bnetid' => '9'),
				'waist'		=> array('position' => 'right',		'bnetid' => '5'),
				'legs'		=> array('position' => 'right',		'bnetid' => '6'),
				'feet'		=> array('position' => 'right',		'bnetid' => '7'),
				'finger1'	=> array('position' => 'right',		'bnetid' => '10'),
				'finger2'	=> array('position' => 'right',		'bnetid' => '11'),
				'trinket1'	=> array('position' => 'right',		'bnetid' => '12'),
				'trinket2'	=> array('position' => 'right',		'bnetid' => '13'),

				'mainHand'	=> array('position' => 'bottom',	'bnetid' => '15'),
				'offHand'	=> array('position' => 'bottom',	'bnetid' => '16')
			);

			// reset the array
			$a_items = array();

			// set the itemlevel
			$a_items['itemlevel'] = array(
				'averageItemLevel'			=> $data['averageItemLevel'],
				'averageItemLevelEquipped'	=> $data['averageItemLevelEquipped']
			);

			// fill the item slots with data
			foreach ($d_itemoptions as $slot=>$options){
				$a_items[$options['position']][] = (isset($data[$slot]['id']) && $data[$slot]['id'] > 0) ? infotooltip($data[$slot]['name'], $data[$slot]['id'], false, 0, $icons_size, false, $member_name, false, false, '', $slot) : "<img src='games/wow/profiles/slots/".$options['bnetid'].".png' height='$icons_size' width='$icons_size' alt='' />";
			}
			return $a_items;
		}

		public function ParseRaidProgression($chardata){
			$a_raidprogress = array();
			if(isset($chardata['progression']['raids']) && is_array($chardata['progression']['raids'])){
				foreach($chardata['progression']['raids'] as $v_progression){

					// parse the bosses
					$a_bosses = array('progress_normal' => 0, 'progress_heroic' => 0);
					if(isset($v_progression['bosses']) && is_array($v_progression['bosses'])){
						foreach($v_progression['bosses'] as $bosses){
							$a_bosses['bosses'] = $bosses;

							// progress count
							if($bosses['normalKills'] > 0){
								$a_bosses['progress_normal']++;
							}
							if($bosses['heroicKills'] > 0){
								$a_bosses['progress_heroic']++;
							}
						}
					}

					// put all together in an array
					$a_category		= array_keys(search_in_array($v_progression['id'], $this->ArrInstanceCategories));
					$v_progresscat	=  (isset($a_category[0])) ? $a_category[0] : 'default';
					$a_raidprogress[$v_progresscat][$v_progression['id']] = array(
						'id'			=> $v_progression['id'],
						'name'			=> $v_progression['name'],
						'icon'			=> 'games/wow/events/'.$v_progression['id'].'.png',
						'bosses'		=> $v_progression['bosses'],
						'bosses_max'	=> count($v_progression['bosses']),
						'bosses_normal'	=> $a_bosses['progress_normal'],
						'bosses_heroic'	=> $a_bosses['progress_heroic'],
						'runs_normal'	=> $v_progression['normal'],
						'runs_heroic'	=> $v_progression['heroic'],
						
					);
				}
			}
			return $a_raidprogress;
		}
	}#class
}
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_wow', wow::$shortcuts);
?>