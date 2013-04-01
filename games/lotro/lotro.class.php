<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2009
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

if(!class_exists('lotro')) {
	class lotro extends game_generic {
		public static $shortcuts = array('db');
		protected $this_game	= 'lotro';
		protected $types		= array('classes', 'races', 'factions', 'filters', 'roles');
		public $icons			= array('3dmodel', 'classes', 'classes_big', 'events', 'races');
		protected $classes		= array();
		protected $races		= array();
		protected $factions		= array();
		protected $filters		= array();
		public  $langs			= array('english', 'german');
		public $objects			= array('lotro_data');
		public $no_reg_obj		= array('lotro_data');	
		
		public $importers 		= array(
			'char_import'		=> 'charimporter.php',						// filename of the character import
			'char_update'		=> 'charimporter.php',						// filename of the character update, member_id (POST) is passed
			'char_mupdate'		=> 'charimporter.php?massupdate=true',		// filename of the "update all characters" aka mass update
			'guild_import'		=> 'guildimporter.php',						// filename of the guild import
			'import_reseturl'	=> 'charimporter.php?resetcache=true',		// filename of the reset cache
			'guild_imp_rsn'		=> true,									// Guild import & Mass update requires server name
			'import_data_cache'	=> true,									// Is the data cached and requires a reset call?
		);
		

		protected $glang		= array();
		protected $lang_file	= array();
		protected $path			= '';
		public  $lang			= false;
		public $version	= '2.1.2';

		/**
		* Initialises filters
		*
		* @param array $langs
		*/
		protected function load_filters($langs){
			if(empty($this->classes)) {
				$this->load_type('classes', $langs);
			}
			foreach($langs as $lang) {
				$names = $this->classes[$lang];
				$this->filters[$lang][] = array('name' => '-----------', 'value' => false);
				foreach($names as $id => $name) {
					$this->filters[$lang][] = array('name' => $name, 'value' => array($id => 'class'));
				}
				$this->filters[$lang] = array_merge($this->filters[$lang], array(
					array('name' => '-----------', 'value' => false),
                    array('name' => $this->glang('heavy', true, $lang), 'value' => array(2 => 'class', 6 => 'class', 7 => 'class')),
                    array('name' => $this->glang('medium', true, $lang), 'value' => array(3 => 'class', 5 => 'class', 9 => 'class')),
                    array('name' => $this->glang('light', true, $lang), 'value' => array(1 => 'class', 4 => 'class', 8 => 'class')),
				));
			}
		}

		public function get_OnChangeInfos($install=false){
			//classcolors
			$info['class_color'] = array(
				1 => '#FFCC33',
				2 => '#0033CC',
				3 => '#006600',
				4 => '#00CCFF',
				5 => '#444444',
				6 => '#990000',
				7 => '#CC3300',
				8 => '#1A3CAA',
				9 => '#FFF468'
			);

			//Do this SQL Query NOT if the Eqdkp is installed -> only @ the first install
			$info['aq'] = array();
			if($install){
				
				array_push($info['aq'], "INSERT INTO `__events` (`event_id`, `event_name`, `event_value`, `event_added_by`, `event_updated_by`, `event_icon`) VALUES
(1, '".$this->glang('event1')."', 0.00, '', '', 'annuminas_glinghant.jpg'),
(2, '".$this->glang('event2')."', 0.00, '', '', 'annuminas_feste_elendil.jpg'),
(3, '".$this->glang('event3')."', 0.00, '', '', 'annuminas_haudh_valabdil.jpg'),
(4, '".$this->glang('event4')."', 0.00, '', '', 'fornost.jpg'),
(5, '".$this->glang('event5')."', 0.00, '', '', 'fornost.jpg'),
(6, '".$this->glang('event6')."', 0.00, '', '', 'fornost.jpg'),
(7, '".$this->glang('event7')."', 0.00, '', '', 'fornost.jpg'),
(8, '".$this->glang('event8')."h', 0.00, '', '', 'huegelgrab.jpg'),
(9, '".$this->glang('event9')."', 0.00, '', '', 'huegelgrab.jpg'),
(10, '".$this->glang('event10')."', 0.00, '', '', 'huegelgrab.jpg'),
(11, '".$this->glang('event11')."', 0.00, '', '', 'helegrod.jpg'),
(12, '".$this->glang('event12')."', 0.00, '', '', 'helegrod.jpg'),
(13, '".$this->glang('event13')."', 0.00, '', '', 'helegrod.jpg'),
(14, '".$this->glang('event14')."', 0.00, '', '', 'helegrod.jpg'),
(15, '".$this->glang('event15')."', 0.00, '', '', 'sonstiges_halle_der_nacht.jpg'),
(16, '".$this->glang('event16')."', 0.00, '', '', 'sonstiges_herberge_der_verlassenen.jpg'),
(17, '".$this->glang('event17')."', 0.00, '', '', 'tham_mirdain_bibliothek.jpg'),
(18, '".$this->glang('event18')."', 0.00, '', '', 'tham_mirdain_schule.jpg'),
(19, '".$this->glang('event19')."', 0.00, '', '', 'isengard_turm_orthanc.jpg'),
(20, '".$this->glang('event20')."', 0.00, '', '', 'isengard_giesserei.jpg'),
(21, '".$this->glang('event21')."', 0.00, '', '', 'isengard_dargnakh.jpg'),
(22, '".$this->glang('event22')."', 0.00, '', '', 'isengard_grube.jpg'),
(23, '".$this->db->escape($this->glang('event23'))."', 0.00, '', '', 'isengard_rande_des_fangorn.jpg'),
(24, '".$this->db->escape($this->glang('event24'))."', 0.00, '', '', 'isengard_draigoch.jpg'),
(25, '".$this->glang('event25')."', 0.00, '', '', 'isengard_wdf.jpg'),
(26, '".$this->glang('event26')."', 0.00, '', '', 'skirmish_bruinenfurt.jpg'),
(27, '".$this->glang('event27')."', 0.00, '', '', 'skirmish_gondamon.jpg'),
(28, '".$this->glang('event28')."', 0.00, '', '', 'skirmish_amon_sul.jpg'),
(29, '".$this->glang('event29')."', 0.00, '', '', 'skirmish.jpg'),
(30, '".$this->glang('event30')."', 0.00, '', '', 'skirmish_thangulhad.jpg'),
(31, '".$this->glang('event31')."', 0.00, '', '', 'skirmish_tiefweg.jpg'),
(32, '".$this->glang('event32')."', 0.00, '', '', 'skirmish_schmiedeweg.jpg'),
(33, '".$this->glang('event33')."', 0.00, '', '', 'skirmish_21_halle.jpg'),
(34, '".$this->glang('event34')."', 0.00, '', '', 'skirmish_buckelstadt.jpg'),
(35, '".$this->glang('event35')."', 0.00, '', '', 'skirmish_dannenglor.jpg'),
(36, '".$this->db->escape($this->glang('event36'))."', 0.00, '', '', 'skirmish_geisterbeschwoerer.jpg'),
(37, '".$this->db->escape($this->glang('event37'))."', 0.00, '', '', 'skirmish_ringgeister.jpg'),
(38, '".$this->glang('event38')."', 0.00, '', '', 'skirmish_kamp_im_turm.jpg'),
(39, '".$this->glang('event39')."', 0.00, '', '', 'skirmish_dieberei_und_unheil.jpg'),
(40, '".$this->glang('event40')."', 0.00, '', '', 'skirmish_nurz_gashu.jpg'),
(41, '".$this->glang('event41')."', 0.00, '', '', 'skirmish_eisige_kluft.jpg'),
(42, '".$this->glang('event42')."', 0.00, '', '', 'skirmish_morgengrauen.jpg'),
(43, '".$this->glang('event43')."', 0.00, '', '', 'skirmish_methedras.jpg'),
(44, '".$this->glang('event44')."', 0.00, '', '', 'skirmish_huegelgraeberhoehen.jpg'),
(45, '".$this->glang('event45')."', 0.00, '', '', 'angmar_carnd_dum.jpg'),
(46, '".$this->glang('event46')."', 0.00, '', '', 'angmar_urugarth.jpg'),
(47, '".$this->glang('event47')."', 0.00, '', '', 'angmar_barad_gularan.jpg'),
(48, '".$this->glang('event48')."', 0.00, '', '', 'angmar_nurz_gashu.jpg'),
(49, '".$this->glang('event49')."', 0.00, '', '', 'dol_guldur_barad_guldur.jpg'),
(50, '".$this->glang('event50')."', 0.00, '', '', 'dol_guldur_sammath_gul.jpg'),
(51, '".$this->glang('event51')."', 0.00, '', '', 'dol_guldur_warggehege.jpg'),
(52, '".$this->glang('event52')."', 0.00, '', '', 'dol_guldur_verliese.jpg'),
(53, '".$this->glang('event53')."', 0.00, '', '', 'dol_guldur_schwerthalle.jpg'),
(54, '".$this->glang('event54')."', 0.00, '', '', 'garth_agarwen.jpg'),
(55, '".$this->glang('event55')."', 0.00, '', '', 'garth_agarwen.jpg'),
(56, '".$this->glang('event56')."', 0.00, '', '', 'garth_agarwen.jpg'),
(57, '".$this->glang('event57')."', 0.00, '', '', 'in_ihrer_abw_feste_dunoth.jpg'),
(58, '".$this->glang('event58')."', 0.00, '', '', 'in_ihrer_abw_sari_surma.jpg'),
(59, '".$this->glang('event59')."', 0.00, '', '', 'in_ihrer_abw_verlorener_tempel.jpg'),
(60, '".$this->glang('event60')."', 0.00, '', '', 'in_ihrer_abw_nhh.jpg'),
(61, '".$this->glang('event61')."', 0.00, '', '', 'in_ihrer_abw_steinhoehe.jpg'),
(62, '".$this->glang('event62')."', 0.00, '', '', 'lothlorien_dar_nurbugud.jpg'),
(63, '".$this->glang('event63')."', 0.00, '', '', 'lothlorien_hallen_der_handwerkskunst.jpg'),
(64, '".$this->glang('event64')."', 0.00, '', '', 'lothlorien_spiegelhallen.jpg'),
(65, '".$this->glang('event65')."', 0.00, '', '', 'lothlorien_wasserraeder.jpg'),
(66, '".$this->glang('event66')."', 0.00, '', '', 'moria_abscheulicher_schlund.jpg'),
(67, '".$this->glang('event67')."', 0.00, '', '', 'moria_filikul.jpg'),
(68, '".$this->glang('event68')."', 0.00, '', '', 'moria_die_grosse_treppe.jpg'),
(69, '".$this->glang('event69')."', 0.00, '', '', 'moria_skumfil.jpg'),
(70, '".$this->glang('event70')."', 0.00, '', '', 'moria_die_schmieden_von_khazad_dum.jpg'),
(71, '".$this->glang('event71')."', 0.00, '', '', 'moria_fil_gashan.jpg'),
(72, '".$this->glang('event72')."', 0.00, '', '', 'moria_schattenbinge.jpg'),
(73, '".$this->glang('event73')."', 0.00, '', '', 'moria_sechzehnte_halle.jpg'),
(74, '".$this->glang('event74')."', 0.00, '', '', 'moria_der_vergessene_schatz.jpg'),
(75, '".$this->glang('event75')."', 0.00, '', '', 'bilwissdorf_thronsaal.jpg'),
(76, '".$this->glang('event76')."', 0.00, '', '', 'skirmish.jpg'),
(77, '".$this->glang('event77')."', 0.00, '', '', 'skirmish.jpg');");


array_push($info['aq'], "INSERT INTO `__multidkp2event` (`multidkp2event_multi_id`, `multidkp2event_event_id`, `multidkp2event_no_attendance`) VALUES
	(1, 1, 0),
	(1, 2, 0),
	(1, 3, 0),
	(1, 4, 0),
	(1, 5, 0),
	(1, 6, 0),
	(1, 77, 0),
	(1, 76, 0),
	(1, 75, 0),
	(1, 74, 0),
	(1, 73, 0),
	(1, 71, 0),
	(1, 72, 0),
	(1, 70, 0),
	(1, 69, 0),
	(1, 68, 0),
	(1, 67, 0),
	(1, 66, 0),
	(1, 65, 0),
	(1, 64, 0),
	(1, 62, 0),
	(1, 63, 0),
	(1, 61, 0),
	(1, 59, 0),
	(1, 60, 0),
	(1, 57, 0),
	(1, 58, 0),
	(1, 54, 0),
	(1, 55, 0),
	(1, 56, 0),
	(1, 53, 0),
	(1, 52, 0),
	(1, 51, 0),
	(1, 50, 0),
	(1, 49, 0),
	(1, 46, 0),
	(1, 47, 0),
	(1, 48, 0),
	(1, 45, 0),
	(1, 43, 0),
	(1, 44, 0),
	(1, 42, 0),
	(1, 40, 0),
	(1, 41, 0),
	(1, 38, 0),
	(1, 39, 0),
	(1, 37, 0),
	(1, 36, 0),
	(1, 35, 0),
	(1, 33, 0),
	(1, 34, 0),
	(1, 32, 0),
	(1, 31, 0),
	(1, 30, 0),
	(1, 29, 0),
	(1, 28, 0),
	(1, 26, 0),
	(1, 27, 0),
	(1, 25, 0),
	(1, 24, 0),
	(1, 23, 0),
	(1, 22, 0),
	(1, 21, 0),
	(1, 20, 0),
	(1, 19, 0),
	(1, 18, 0),
	(1, 17, 0),
	(1, 16, 0),
	(1, 15, 0),
	(1, 14, 0),
	(1, 12, 0),
	(1, 13, 0),
	(1, 11, 0),
	(1, 10, 0),
	(1, 9, 0),
	(1, 8, 0),
	(1, 7, 0);");
	}
			return $info;
		}
	}
}
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_lotro', lotro::$shortcuts);
?>