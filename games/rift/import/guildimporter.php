<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2011
 * Date:		$Date: 2011-08-09 12:51:58 +0200 (Di, 09. Aug 2011) $
 * -----------------------------------------------------------------------
 * @author		$Author: wallenium $
 * @copyright	2006-2011 EQdkp-Plus Developer Team
 * @link		http://eqdkp-plus.com
 * @package		eqdkp-plus
 * @version		$Rev: 10957 $
 * 
 * $Id: guildimporter.php 10957 2011-08-09 10:51:58Z wallenium $
 */
 
define('EQDKP_INC', true);
$eqdkp_root_path = './../../../';
include_once ($eqdkp_root_path . 'common.php');

class guildImporter extends page_generic {
	public static function __shortcuts() {
		$shortcuts = array('user', 'tpl', 'in', 'pdh', 'game', 'core', 'config');
		return array_merge(parent::$shortcuts, $shortcuts);
	}

	public function __construct() {
		$handler = array();
		parent::__construct(false, $handler, array());
		$this->user->check_auth('a_members_man');
		$this->process();
	}

	public function perform_step0(){
		$arrLangsRaw = $this->game->get_available_langs();
		foreach ($arrLangsRaw as $lang){
			$arrLangs[$lang] = ucfirst($lang);
		}
		
		// generate output
		$hmtlout = '<fieldset class="settings mediumsettings">
			<dl>
				<dt><label>'.$this->game->glang('import_ranks').'</label></dt>
				<dd>'.$this->html->widget(array('fieldtype'=>'boolean','name'=>'ranks')).'</dd>
			</dl>
			<dl>
				<dt><label>'.$this->game->glang('uc_delete_chars_onimport').'</label></dt>
				<dd>'.$this->html->widget(array('fieldtype'=>'boolean','name'=>'delete_old_chars')).'</dd>
			</dl>
			<dl>
				<dt><label>'.$this->game->glang('guild_xml_lang').'</label></dt>
				<dd>'.$this->html->widget(array('fieldtype'=>'dropdown','name'=>'xmllang', 'options' => $arrLangs, 'selected' => $this->config->get('game_language'))).'</dd>
			</dl>
			<dl>
				<dt><label>'.$this->game->glang('guild_xml').'</label></dt>
				<dd>'.$this->html->widget(array('fieldtype'=>'textarea','name'=>'guildxml','rows'=>20, 'size'=>100)).'</dd>
			</dl>
			</fieldset>';
		$hmtlout .= '<br/><input type="submit" name="submiti" value="'.$this->game->glang('uc_import_forw').'" class="mainoption bi_ok" />';
		return $hmtlout;
	}

	public function perform_step1(){
		$xml = simplexml_load_string(trim($this->in->get('guildxml', '', 'raw')));
		if (!$xml){
			$hmtlout = '<div class="errorbox roundbox"><div class="icon_false" id="error_message_txt">'.$this->game->glang('guild_xml_error').'</div></div>';
		} else {
			//Import Ranks
			$arrRankList = array();
			if ((int)$this->in->get('ranks') == 1){
				$objRanks = $xml->Ranks->Rank;				
				$arrRanks = $this->pdh->aget('rank', 'name', 0, array($this->pdh->get('rank', 'id_list')));
				foreach ($objRanks as $objRank){
					$rankid = array_search((string)$objRank->Name, $arrRanks);
					if (!$rankid){
						$rank_id	= max(array_keys($arrRanks)) + 1;
						$this->pdh->put('rank', 'add_rank', array($rank_id, (string)$objRank->Name, 0));
						$arrRankList[(int)$objRank->Id] = $rankid;
						$arrRanks[$rank_id] = (string)$objRank->Name;
					} else {
						$arrRankList[(int)$objRank->Id] = $rankid;
					}
				}
			}
			
			//Suspend all Chars
			if ($this->in->get('delete_old_chars',0)){
				$this->pdh->put('member', 'suspend', array('all'));
			}
			
			//Import Chars
			foreach ($xml->Members->Member as $objMember) {	
				$dataarry = array(
					'name'		=> (string)$objMember->Name,
					'lvl'		=> (int)$objMember->Level,
					'classid'	=> $this->game->get_id('classes', (string)$objMember->Calling),
					'raceid'	=> 0,
				);
				
				if ((int)$this->in->get('ranks') == 1){
					$dataarry['rankid'] = (int)$arrRankList[(int)$objMember->Rank];
				}

				$myStatus = $this->pdh->put('member', 'addorupdate_member', array(0, $dataarry));
				if ($myStatus){
					//Revoke Char
					if ($this->in->get('delete_old_chars',0)){
						$this->pdh->put('member', 'revoke', array($myStatus));
					}
				}
				
				$hmtlout .= "<dl><dt><label><img src=\"".$this->root_path.'images/no_pic.png'."\" alt=\"charicon\" height=\"84\" width=\"84\" /></label></dt><dd>".(string)$objMember->Name."<br/>".(($myStatus) ? '<span class="positive">'.$this->game->glang('import_status_true').'</span>' : '<span class="negative">'.$this->game->glang('import_status_false').'</span>')."</dd></dl>";
			}
			
			$hmtlout = "<dl><div class=\"greenbox roundbox\"><div class=\"icon_ok\" id=\"error_message_txt\">".$this->game->glang('uc_gimp_header_fnsh')."</div></div></dl>".$hmtlout;			
			
			// reset the cache
			$this->pdh->process_hook_queue();
		}

		return '<fieldset class="settings">'.$hmtlout.'</fieldset>';
	}

	public function display(){
		$funcname = 'perform_step'.$this->in->get('step',0);
		$this->tpl->assign_vars(array(
			'DATA'		=> $this->$funcname(),
			'STEP'		=> ($this->in->get('step',0)+1)
		));

		$this->core->set_vars(array(
			'page_title'		=> $this->user->lang('raidevent_raid_guests'),
			'header_format'		=> 'simple',
			'template_file'		=> 'importer.html',
			'display'			=> true
		));
	}
}
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_guildImporter', guildImporter::__shortcuts());
registry::register('guildImporter');
?>