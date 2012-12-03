<?php
 /*
 * Project:     EQdkp-Plus
 * License:     Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:				http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:       2007
 * Date:        $Date$
 * -----------------------------------------------------------------------
 * @author      $Author$
 * @copyright   2006-2010 EQdkp-Plus Developer Team
 * @link        http://eqdkp-plus.com
 * @package     eqdkp-plus
 * @version     $Rev$
 *
 * $Id$
 */

if ( !defined('EQDKP_INC') ){
    header('HTTP/1.0 404 Not Found');exit;
}

// Category 'character' is a fix one! All others are created dynamically!

$profession_data = array(
		'alchemy'					=> 'uc_prof_alchemy',
		'mining'					=> 'uc_prof_mining',
		'engineering'			=> 'uc_prof_engineering',
		'skinning'				=> 'uc_prof_skinning',
		'herbalism'				=> 'uc_prof_herbalism',
		'leatherworking'	=> 'uc_prof_leatherworking',
		'blacksmithing'		=> 'uc_prof_blacksmithing',
		'tailoring'				=> 'uc_prof_tailoring',
		'enchanting'			=> 'uc_prof_enchanting',
		'jewelcrafting'		=> 'uc_prof_jewelcrafting',
		'inscription'			=> 'uc_prof_inscription'
);

$xml_fields = array(
  'fire'	=> array(
  		'type'				=> 'int',
  		'category'		=> 'skills',
  		'name'				=> 'fire',
  		'size'				=> 4,
  		'visible'			=> true,
  		'undeletable'	=> true,
  		'image'				=> 'fire.gif'
  ),
  'nature'	=> array(
  		'type'				=> 'int',
  		'category'		=> 'skills',
  		'name'				=> 'nature',
  		'size'				=> 4,
  		'visible'			=> true,
  		'undeletable'	=> true,
  		'image'				=> 'nature.gif'
  ),
  'shadow'	=> array(
  		'type'				=> 'int',
  		'category'		=> 'skills',
  		'name'				=> 'shadow',
  		'size'				=> 4,
  		'visible'			=> true,
  		'undeletable'	=> true,
  		'image'				=> 'shadow.gif'
  ),
  'arcane'	=> array(
  		'type'				=> 'int',
  		'category'		=> 'skills',
  		'name'				=> 'arcane',
  		'size'				=> 4,
  		'visible'			=> true,
  		'undeletable'	=> true,
  		'image'				=> 'arcane.gif'
  ),
  'frost'           => array(
  		'type'				=> 'int',
  		'category'		=> 'skills',
  		'name'				=> 'frost',
  		'size'				=> 4,
  		'visible'			=> true,
  		'undeletable'	=> true,
  		'image'				=> 'frost.gif'
  ),
  'skill_1'	=> array(
  		'type'				=> 'int',
  		'category'		=> 'skills',
  		'name'				=> 'uc_skill1',
  		'size'				=> 4,
  		'undeletable'	=> true,
  		'visible'			=> true
  ),
  'skill_2'	=> array(
  		'type'				=> 'int',
  		'category'		=> 'skills',
  		'name'				=> 'uc_skill2',
  		'size'				=> 4,
  		'undeletable'	=> true,
  		'visible'			=> true
  ),
  'skill_3'	=> array(
  		'type'				=> 'int',
  		'category'		=> 'skills',
  		'name'				=> 'uc_skill3',
  		'size'				=> 4,
  		'undeletable'	=> true,
  		'visible'			=> true
  ),
  'skill_2_1'	=> array(
  		'type'				=> 'int',
  		'category'		=> 'skills',
  		'name'				=> 'uc_skill2_1',
  		'size'				=> 4,
  		'undeletable'	=> true,
  		'visible'			=> true
  ),
  'skill_2_2'	=> array(
  		'type'				=> 'int',
  		'category'		=> 'skills',
  		'name'				=> 'uc_skill2_2',
  		'size'				=> 4,
  		'undeletable'	=> true,
  		'visible'			=> true
  ),
  'skill_2_3' => array(
  		'type'				=> 'int',
  		'category'		=> 'skills',
  		'name'				=> 'uc_skill2_3',
  		'size'				=> 4,
  		'undeletable'	=> true,
  		'visible'			=> true
  ),
  'gender'	=> array(
  		'type'				=> 'dropdown',
  		'category'		=> 'character',
  		'name'				=> 'uc_gender',
  		'option'			=> array('Male' => 'uc_male', 'Female' => 'uc_female'),
  		'undeletable'	=> true,
  		'visible'			=> true
  ),
  'guild'	=> array(
  		'type'				=> 'text',
  		'category'		=> 'character',
  		'name'				=> 'uc_guild',
  		'size'				=> 40,
  		'undeletable'	=> true,
  		'visible'			=> true
  ),
  'faction'	=> array(
  		'type'				=> 'dropdown',
  		'category'		=> 'character',
  		'name'				=> 'uc_faction',
  		'option'			=> array('Horde' => 'uc_fact_horde', 'Alliance' => 'uc_fact_alliance'),
  		'undeletable'	=> true,
  		'visible'			=> true
  ),
  'prof1_value'	=> array(
  		'type'				=> 'int',
  		'category'		=> 'profession',
  		'name'				=> 'uc_prof1_value',
  		'size'				=> 4,
  		'undeletable'	=> true,
  		'visible'			=> true
  ),
  'prof1_name'	=> array(
  		'type'				=> 'dropdown',
  		'category'		=> 'profession',
  		'name'				=> 'uc_prof1_name',
  		'option'			=> $profession_data,
  		'undeletable'	=> true,
  		'visible'			=> true
  ),
  'prof2_value'	=> array(
  		'type'				=> 'int',
  		'category'		=> 'profession',
  		'name'				=> 'uc_prof2_value',
  		'size'				=> 4,
  		'undeletable'	=> true,
  		'visible'			=> true
  ),
  'prof2_name'	=> array(
  		'type'				=> 'dropdown',
  		'category'		=> 'profession',
  		'name'				=> 'uc_prof2_name',
  		'option'			=> $profession_data,
  		'undeletable'	=> true,
  		'visible'			=> true
  ),
  'health_bar'	=> array(
  		'type'				=> 'int',
  		'category'		=> 'character',
  		'name'				=> 'uc_bar_health',
  		'undeletable'	=> true,
  		'size'				=> 4
  ),
  'second_bar'	=> array(
  		'type'				=> 'int',
  		'category'		=> 'character',
  		'name'				=> 'uc_bar_2value',
  		'size'				=> 4,
  		'undeletable'	=> true,
  ),
  'second_name'	=> array(
  		'type'				=> 'text',
  		'category'		=> 'character',
  		'name'				=> 'uc_bar_2name',
  		'size'				=> 40,
  		'undeletable'	=> true,
  ),
  'blasc_id'	=> array(
  		'type'				=> 'int',
  		'category'		=> 'profiler',
  		'name'				=> 'uc_profile_blasc',
  		'size'				=> 40,
  		'visible'			=> true,
  		'undeletable'	=> false,
  		'image'				=> 'blasc_icon.png'
  ),
  'ct_profile'	 => array(
  		'type'				=> 'text',
  		'category'		=> 'profiler',
  		'name'				=> 'uc_profile_ct',
  		'size'				=> 40,
  		'visible'			=> true,
  		'undeletable'	=> false,
  		'image'				=> 'ctprofile_icon.png'
  ),
  'curse_profiler'  => array(
  		'type'				=> 'text',
  		'category'		=> 'profiler',
  		'name'				=> 'uc_profile_curse',
  		'size' 				=> 40,
  		'visible'			=> true,
  		'undeletable'	=> false,
  		'image'				=> false
  ),
  'allakhazam'		=> array(
  		'type'				=> 'text',
  		'category'		=> 'profiler',
  		'name'				=> 'uc_profile_alla',
  		'size'				=> 40,
  		'visible'			=> true,
  		'undeletable'	=> false,
  		'image'				=> 'alla_icon.png'
  ),
  'talentplaner'	=> array(
  		'type'				=> 'text',
  		'category'		=> 'profiler',
  		'name'				=> 'uc_profile_talent',
  		'size'				=> 40,
  		'visible'			=> true,
  		'undeletable'	=> false,
  		'image'				=> 'talent.jpg'
  ),
);

?>
