<?php
/*	Project:	EQdkp-Plus
 *	Package:	Star Wars - The Old Republic game package
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

if ( !defined('EQDKP_INC') ){
	header('HTTP/1.0 404 Not Found');exit;
}

if(!class_exists('swtor')) {
	class swtor extends game_generic {
		protected static $apiLevel	= 20;
		public $version				= '2.0.1b';
		protected $this_game		= 'swtor';
		public $author				= "Anykan (Reapers T3-M4)";
		protected $types			= array('classes', 'races', 'factions', 'roles', 'skills','filters', 'realmlist', 'professions');
		protected $classes			= array();
		protected $races			= array();
		protected $roles			= array();
		protected $factions			= array();
		protected $filters			= array();
		public $langs				= array('english', 'german');

		protected $glang			= array();
		protected $lang_file		= array();
		protected $path				= false;
		public $lang				= false;

		protected $class_dependencies = array(
			array(
				'name'		=> 'faction',
				'type'		=> 'factions',
				'admin' 	=> false,
				'decorate'	=> false,
				'parent'	=> false,
			),
			array(
				'name'		=> 'race',
				'type'		=> 'races',
				'admin'		=> false,
				'decorate'	=> true,
				'parent'	=> array(
					'faction' => array(
						'republic'	=> array(0,1,2,3,4,5,6,7,8,9,10),
						'imperial'	=> array(0,1,2,3,4,5,6,7,8,9,10),
					),
				),
			),
			array(
				'name'		=> 'class',
				'type'		=> 'classes',
				'admin'		=> false,
				'decorate'	=> true,
				'primary'	=> true,
				'colorize'	=> true,
				'roster'	=> true,
				'recruitment' => true,
				'parent'	=> array(
					'faction' => array(
						'republic' 	=> array(0,1,2,3,4,5,6,7,8),	// 
						'imperial' 	=> array(0,9,10,11,12,13,14,15,16),	// 
					),
				),
			),
			array(
				'name'		=> 'skill',
				'type'		=> 'skills',
				'admin'		=> false,
				'decorate'	=> false,
				'recruitment' => true,
				'parent'	=> array(
					'class' => array(
						0 	=> array(20),			// Unbekannt
						1 	=> array(0,1,48),		// Frontkämpfer
						2 	=> array(2,3,4),		// Kommando
						3 	=> array(15,16,45),		// Schurke
						4 	=> array(17,18,19),		// Revolverheld
						5 	=> array(5,6,7),		// Gelehrter
						6 	=> array(8, 9, 43),		// Schatten
						7 	=> array(10,11,12),		// Wächter
						8 	=> array(13,14,42),		// Hüter
						9	=> array(23,24,25),		// Powertech
						10	=> array(21,22,47),		// Soeldner
						11	=> array(37,38,39),		// Saboteur
						12	=> array(35,36,46),		// Scharfschuetze
						13	=> array(30,31,32),		// Hexer
						14	=> array(33,34,44),		// Attentaeter
						15	=> array(26,27,28),		// Marodeur
						16	=> array(29,40,41),		// Juggernaut
					),
				),
			),
		);

		public $default_roles = array( 
			1 => array(2, 3, 5, 10, 13),
			2 => array(1, 6, 8, 9, 14, 16),
			3 => array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16),
			4 => array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16)
		);
		protected $class_colors = array(
			1	=> '#6ce31c',
			2	=> '#38a450',
			3	=> '#af6b1d',
			4	=> '#ebed5c',
			5	=> '#308fa1',
			6	=> '#284fb8',
			7	=> '#c72d35',
			8	=> '#896ccb',
			9	=> '#6ce31c',
			10	=> '#38a450',
			11	=> '#af6b1d',
			12	=> '#ebed5c',
			13	=> '#308fa1',
			14	=> '#284fb8',
			15	=> '#c72d35',
			16	=> '#896ccb',
		);

		public function install($blnEQdkpInstall=false){

			$arrClassicEventIDs = array();
			//Operation Swtor 1.0
			$arrClassicEventIDs[] = $this->game->addEvent($this->glang('sm_ewigekammer'), 0, "s.png");
			$arrClassicEventIDs[] = $this->game->addEvent($this->glang('hc_ewigekammer'), 0, "h.png");
			$arrClassicEventIDs[] = $this->game->addEvent($this->glang('nm_ewigekammer'), 0, "n.png");
			$arrClassicEventIDs[] = $this->game->addEvent($this->glang('sm_karaggaspalast'), 0, "s.png");
			$arrClassicEventIDs[] = $this->game->addEvent($this->glang('hc_karaggaspalast'), 0, "h.png");
			$arrClassicEventIDs[] = $this->game->addEvent($this->glang('nm_karaggaspalast'), 0, "n.png");
			$arrClassicEventIDs[] = $this->game->addEvent($this->glang('sm_explosivkonflikt'), 0, "s.png");
			$arrClassicEventIDs[] = $this->game->addEvent($this->glang('hc_explosivkonflikt'), 0, "h.png");
			$arrClassicEventIDs[] = $this->game->addEvent($this->glang('nm_explosivkonflikt'), 0, "m.png");

			//Operation Swtor 2.0
			$arrEventIDs = array();
			$arrEventIDs[] = $this->game->addEvent($this->glang('sm_abschaum'), 0, "s.png");
			$arrEventIDs[] = $this->game->addEvent($this->glang('hc_abschaum'), 0, "h.png");
			$arrEventIDs[] = $this->game->addEvent($this->glang('nm_abschaum'), 0, "n.png");
			$arrEventIDs[] = $this->game->addEvent($this->glang('sm_schrecken'), 0, "s.png");
			$arrEventIDs[] = $this->game->addEvent($this->glang('hc_schrecken'), 0, "h.png");
			$arrEventIDs[] = $this->game->addEvent($this->glang('nm_schrecken'), 0, "n.png");
			$arrEventIDs[] = $this->game->addEvent($this->glang('sm_s_festung'), 0, "s.png");
			$arrEventIDs[] = $this->game->addEvent($this->glang('hc_s_festung'), 0, "h.png");
			$arrEventIDs[] = $this->game->addEvent($this->glang('nm_s_festung'), 0, "n.png");
			$arrEventIDs[] = $this->game->addEvent($this->glang('sm_s_palast'), 0, "s.png");
			$arrEventIDs[] = $this->game->addEvent($this->glang('hc_s_palast'), 0, "h.png");
			$arrEventIDs[] = $this->game->addEvent($this->glang('nm_s_palast'), 0, "n.png");
			$arrEventIDs[] = $this->game->addEvent($this->glang('sm_tbh'), 0, "s.png");
			$arrEventIDs[] = $this->game->addEvent($this->glang('hc_tbh'), 0, "h.png");
			$arrEventIDs[] = $this->game->addEvent($this->glang('nm_tbh'), 0, "n.png");
			
			//Operation Swtor 3.0
			$arrRevanEventIDs = array();
			$arrRevanEventIDs[] = $this->game->addEvent($this->glang('sm_wueter'), 0, "s.png");
			$arrRevanEventIDs[] = $this->game->addEvent($this->glang('hc_wueter'), 0, "h.png");
			$arrRevanEventIDs[] = $this->game->addEvent($this->glang('nm_wueter'), 0, "n.png");
			$arrRevanEventIDs[] = $this->game->addEvent($this->glang('sm_tempel'), 0, "s.png");
			$arrRevanEventIDs[] = $this->game->addEvent($this->glang('hc_tempel'), 0, "h.png");
			$arrRevanEventIDs[] = $this->game->addEvent($this->glang('nm_tempel'), 0, "n.png");
			
			//itempools
			$intItempoolClassic = $this->game->addItempool("SWtoR 1.0", "SWtoR 1.0 Itempool");
			$intItempoolGalactic = $this->game->addItempool("SWtoR 2.0", "SWtoR 2.0 Itempool");
			$intItempoolRevan = $this->game->addItempool("SWtoR 3.0", "SWtoR 3.0 Itempool");

			
			$this->game->addMultiDKPPool("SWtoR 1.0", "SWtoR MultiDKPPool", $arrClassicEventIDs, array($intItempoolClassic));
			$this->game->addMultiDKPPool("SWtoR 2.0", "SWtoR MultiDKPPool", $arrEventIDs, array($intItempoolGalactic));
			$this->game->addMultiDKPPool("SWtoR 3.0", "SWtoR MultiDKPPool", $arrRevanEventIDs, array($intItempoolRevan));

			
			//Ranks
			$this->game->addRank(0, "Guildmaster");
			$this->game->addRank(1, "Officer");
			$this->game->addRank(2, "Veteran");
			$this->game->addRank(3, "Member");
			$this->game->addRank(4, "Initiate", true);
			
			//Raidgroups
			$this->game->addRaidgroup("Gold","#E0BD49", "Team Gold", 0, 1, 0);
			$this->game->addRaidgroup("Blue","#000093", "Team Blue", 0, 2, 0);
			$this->game->addRaidgroup("Red","#930000", "Team Red", 0, 3, 0);
			
		}
			public function uninstall(){
			}
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
					array('name' => $names[12], 'value' => 'class:12'),
					array('name' => $names[13], 'value' => 'class:13'),
					array('name' => $names[14], 'value' => 'class:14'),
					array('name' => $names[15], 'value' => 'class:15'),
					array('name' => $names[16], 'value' => 'class:16'),
				);
			}
		}

		public function profilefields() {
			$this->load_type('professions', array($this->lang));
			$this->load_type('realmlist', array($this->lang));
			$fields = array(
				'level'	=> array(
					'type'			=> 'spinner',
					'category'		=> 'character',
					'lang'			=> 'uc_level',
					'max'			=> 60,
					'min'			=> 1,
					'undeletable'	=> true,
					'sort'			=> 1,
				),
				'gender'	=> array(
					'type'			=> 'dropdown',
					'category'		=> 'character',
					'lang'			=> 'uc_gender',
					'options'		=> array('male' => 'uc_male', 'female' => 'uc_female'),
					'undeletable'	=> true,
					'visible'		=> true,
					'tolang'		=> true,
					'sort'			=> 3,
				),
				'guild'	=> array(
					'type'			=> 'text',
					'category'		=> 'character',
					'lang'			=> 'uc_guild',
					'size'			=> 32,
					'undeletable'	=> true,
					'visible'		=> true,
					'sort'			=> 4,
				),
				'servername'	=> array(
					'type'			=> 'dropdown',
					'category'		=> 'character',
					'lang'			=> 'uc_servername',
					'edecode'		=> true,
					'options'		=> $this->realmlist[$this->lang],
					'undeletable'	=> true,
					'options_lang'	=> "realmlist",
					'sort'			=> 2,
				),
				'prof1_name'	=> array(
					'type'			=> 'dropdown',
					'category'		=> 'profession',
					'lang'			=> 'uc_prof1_name',
					'options'		=> $this->professions[$this->lang],
					'undeletable'	=> true,
					'image'			=> "games/swtor/profiles/professions/{VALUE}.png",
					'options_lang'	=> "professions",
					'sort'			=> 5,
				),
				'prof1_value'	=> array(
					'type'			=> 'int',
					'category'		=> 'profession',
					'lang'			=> 'uc_prof1_value',
					'size'			=> 4,
					'undeletable'	=> true,
					'sort'			=> 6,
				),
				'prof2_name'	=> array(
					'type'			=> 'dropdown',
					'category'		=> 'profession',
					'lang'			=> 'uc_prof2_name',
					'options'		=> $this->professions[$this->lang],
					'undeletable'	=> true,
					'image'			=> "games/swtor/profiles/professions/{VALUE}.png",
					'options_lang'	=> "professions",
					'sort'			=> 7,
				),
				'prof2_value'	=> array(
					'type'			=> 'int',
					'category'		=> 'profession',
					'lang'			=> 'uc_prof2_value',
					'size'			=> 4,
					'undeletable'	=> true,
					'sort'			=> 8,
				),
				'prof3_name'	=> array(
					'type'			=> 'dropdown',
					'category'		=> 'profession',
					'lang'			=> 'uc_prof3_name',
					'options'		=> $this->professions[$this->lang],
					'undeletable'	=> true,
					'image'			=> "games/swtor/profiles/professions/{VALUE}.png",
					'options_lang'	=> "professions",
					'sort'			=> 9,
				),
				'prof3_value'	=> array(
					'type'			=> 'int',
					'category'		=> 'profession',
					'lang'			=> 'uc_prof3_value',
					'size'			=> 4,
					'undeletable'	=> true,
					'sort'			=> 10
				),
					'ruf1_value'	=> array(
					'type'			=> 'int',
					'category'		=> 'reputation',
					'lang'			=> 'ruf1',
					'size'			=> 4,
					'undeletable'	=> true,
					'sort'			=> 11
				),
					'ruf2_value'	=> array(
					'type'			=> 'int',
					'category'		=> 'reputation',
					'lang'			=> 'ruf2',
					'size'			=> 4,
					'undeletable'	=> true,
					'sort'			=> 12
				),
					'ruf3_value'	=> array(
					'type'			=> 'int',
					'category'		=> 'reputation',
					'lang'			=> 'ruf3',
					'size'			=> 4,
					'undeletable'	=> true,
					'sort'			=> 13
				),
					'ruf4_value'	=> array(
					'type'			=> 'int',
					'category'		=> 'reputation',
					'lang'			=> 'ruf4',
					'size'			=> 4,
					'undeletable'	=> true,
					'sort'			=> 14
				),
					'ruf5_value'	=> array(
					'type'			=> 'int',
					'category'		=> 'reputation',
					'lang'			=> 'ruf5',
					'size'			=> 4,
					'undeletable'	=> true,
					'sort'			=> 15
				),
					'ruf6_value'	=> array(
					'type'			=> 'int',
					'category'		=> 'reputation',
					'lang'			=> 'ruf6',
					'size'			=> 4,
					'undeletable'	=> true,
					'sort'			=> 16
				),
					'ruf7_value'	=> array(
					'type'			=> 'int',
					'category'		=> 'reputation',
					'lang'			=> 'ruf7',
					'size'			=> 4,
					'undeletable'	=> true,
					'sort'			=> 17
				),
					'ruf8_value'	=> array(
					'type'			=> 'int',
					'category'		=> 'reputation',
					'lang'			=> 'ruf8',
					'size'			=> 4,
					'undeletable'	=> true,
					'sort'			=> 18
				),
					'ruf9_value'	=> array(
					'type'			=> 'int',
					'category'		=> 'reputation',
					'lang'			=> 'ruf9',
					'size'			=> 4,
					'undeletable'	=> true,
					'sort'			=> 19
				),
					'ruf10_value'	=> array(
					'type'			=> 'int',
					'category'		=> 'reputation',
					'lang'			=> 'ruf10',
					'size'			=> 4,
					'undeletable'	=> true,
					'sort'			=> 20
				),
					'ruf11_value'	=> array(
					'type'			=> 'int',
					'category'		=> 'reputation',
					'lang'			=> 'ruf11',
					'size'			=> 4,
					'undeletable'	=> true,
					'sort'			=> 21
				),
					'ruf12_value'	=> array(
					'type'			=> 'int',
					'category'		=> 'reputation',
					'lang'			=> 'ruf12',
					'size'			=> 4,
					'undeletable'	=> true,
					'sort'			=> 22
				),
					'ruf13_value'	=> array(
					'type'			=> 'int',
					'category'		=> 'reputation',
					'lang'			=> 'ruf13',
					'size'			=> 4,
					'undeletable'	=> true,
					'sort'			=> 23
				),
					'ruf14_value'	=> array(
					'type'			=> 'int',
					'category'		=> 'reputation',
					'lang'			=> 'ruf14',
					'size'			=> 4,
					'undeletable'	=> true,
					'sort'			=> 24
				),
					'ruf15_value'	=> array(
					'type'			=> 'int',
					'category'		=> 'reputation',
					'lang'			=> 'ruf15',
					'size'			=> 4,
					'undeletable'	=> true,
					'sort'			=> 25
				),

			);
			return $fields;
		}

		public function get_class_dependencies() {
			$pf_faction = $this->pdh->get('profile_fields', 'fields', array('faction'));
			if($this->config->get('uc_one_faction')) {
				$this->class_dependencies[0]['admin'] = true;
				// hide faction-field in profile-settings
				if($pf_faction['type'] != 'hidden') {
					$this->db->query("UPDATE __member_profilefields SET type = 'hidden' WHERE name='faction';");
					$this->pdh->enqueue_hook('game_update');
					$this->pdh->process_hook_queue();
				}
			} else {
				// set type of faction-field back to dropdown
				if($pf_faction['type'] != 'dropdown') {	
					$this->db->query("UPDATE __member_profilefields SET type = 'dropdown' WHERE name='faction';");
					$this->pdh->enqueue_hook('game_update');
					$this->pdh->process_hook_queue();
				}
			}
			return $this->class_dependencies;
		}

		public function admin_settings() {
			return array(
				'uc_one_faction' => array(
					'type'	=> 'radio',
					'lang'	=> 'uc_one_faction',
				)
			);
		}
	}
}
?>
