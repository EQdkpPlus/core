<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2011
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
 
define('EQDKP_INC', true);
$eqdkp_root_path = './../../../';
include_once ($eqdkp_root_path . 'common.php');

class guildImporter extends page_generic {
	public function __construct() {
		$handler = array();
		parent::__construct(false, $handler, array());
		$this->user->check_auth('a_members_man');
		$this->game->new_object('bnet_armory', 'armory', array($this->config->get('uc_server_loc'), $this->config->get('uc_data_lang')));
		$this->process();
	}

	public function perform_step0(){

		// quit if there is not a server….
		if($this->config->get('uc_servername') == ''){
			return '<fieldset class="settings mediumsettings">
							<dl>
								<dt><label>'.$this->game->glang('uc_error_head').'</label></dt>
								<dd>'.$this->game->glang('uc_error_noserver').'</dd>
							</dl>
						</fieldset>';
		}

		// classes array
		$classfilter	= $this->game->get('classes');
		$classfilter[0]	= $this->game->glang('uc_class_nofilter');

		// generate output
		$hmtlout = '<fieldset class="settings mediumsettings">
			<dl>
				<dt><label>'.$this->game->glang('uc_guild_name').'</label></dt>
				<dd>'.new htext('guildname', array('value' => $this->config->get('guildtag'), 'size' => '40')).'</dd>
			</dl>
			<dl>
				<dt><label>'.$this->game->glang('uc_delete_chars_onimport').'</label></dt>
				<dd>'.new hradio('delete_old_chars').'</dd>
			</dl>
			<dl>
				<dt><label>'.$this->game->glang('uc_sync_ranks').'</label></dt>
				<dd>'.new hradio('sync_ranks').'</dd>
			</dl>
			</fieldset>
			<fieldset class="settings mediumsettings">
				<legend>'.$this->game->glang('uc_filter_name').'</legend>

				<dl>
					<dt><label>'.$this->game->glang('uc_class_filter').'</label></dt>
					<dd>'.new hdropdown('filter_class', array('options' => $classfilter)).'</dd>
				</dl>
				<dl>
					<dt><label>'.$this->game->glang('uc_level_filter').'</label></dt>
					<dd>'.new htext('filter_level', array('value' => 0, 'size' => '5')).'</dd>
				</dl>
				<dl>
					<dt><label>'.$this->game->glang('uc_rank_filter').'</label></dt>
					<dd>'.new hdropdown('rank_sort', array('options' => array(1=>$this->game->glang('uc_rank_filter1a'), 2=>$this->game->glang('uc_rank_filter1b')))).' '.new htext('filter_rank', array('value' => 0, 'size' => '5')).'</dd>
				</dl>
			</fieldset>';
		$hmtlout .= '<br/><input type="submit" name="submiti" value="'.$this->game->glang('uc_import_forw').'" class="mainoption bi_ok" />';
		return $hmtlout;
	}

	public function perform_step1(){
		if($this->in->get('guildname', '') == ''){
			return '<div class="infobox infobox-large infobox-red clearfix"><i class="fa fa-exclamation-triangle fa-4x pull-left"></i> <span id="error_message_txt>'.$this->game->glang('uc_imp_noguildname').'</span></div>';
		}
		
		//Suspend all Chars
		if ($this->in->get('delete_old_chars',0)){
			$this->pdh->put('member', 'suspend', array('all'));
		}
		
		// generate output
		$guilddata	= $this->game->obj['armory']->guild($this->in->get('guildname', ''), $this->config->get('uc_servername'), true);

		if(!isset($guilddata['status'])){
			$hmtlout = '<div id="guildimport_dataset">
							<div id="controlbox">
								<fieldset class="settings">
									<dl>
										'.$this->game->glang('uc_gimp_loading').'
									</dl>
									<div id="progressbar"></div>
									</dl>
								</fieldset>
							</div>
							<fieldset class="settings data">
							</fieldset>
						</div>';

			$jsondata = array();
			foreach($guilddata['members'] as $guildchars){

				// filter: class
				if($this->in->get('filter_class', 0) > 0 && $this->game->obj['armory']->ConvertID($guildchars['character']['class'], 'int', 'classes') != $this->in->get('filter_class', 0)){
					continue;
				}

				// filter: level
				if($this->in->get('filter_level', 0) > 0 && $guildchars['character']['level'] < $this->in->get('filter_level', 0)){
					continue;
				}

				// filter: rank
				if($this->in->get('filter_rank', 0) > 0 && (($this->in->get('rank_sort', 0) == 2 && $guildchars['rank'] != $this->in->get('filter_rank', 0)) || ($this->in->get('rank_sort', 0) == 1 && $guildchars['rank'] >= $this->in->get('filter_rank', 0)))){
					continue;
				}

				// Build the array
				$jsondata[] = array(
					'thumbnail'		=> $guildchars['character']['thumbnail'],
					'name'			=> $guildchars['character']['name'],
					'class'			=> $guildchars['character']['class'],
					'race'			=> $guildchars['character']['race'],
					'level'			=> $guildchars['character']['level'],
					'gender'		=> $guildchars['character']['gender'],
					'rank'			=> $guildchars['rank'],
					'servername'	=> $guildchars['character']['realm'],
					'guild'			=> $guildchars['character']['guild'],
				);
			}

			$this->tpl->add_js('
				$( "#progressbar" ).progressbar({
					value: 0
				});
				getData();', 'docready');
			$this->tpl->add_js('
			var guilddataArry = $.parseJSON(\''.json_encode($jsondata, JSON_HEX_APOS).'\');
			function getData(i){
				if (!i)
					i=0;
	
				if (guilddataArry.length >= i){
					$.post("guildimporter.php'.$this->SID.'&del='.(($this->in->get('delete_old_chars',0)) ? 'true' : 'false').'&sync_rank='.(($this->in->get('sync_ranks',0)) ? 'true' : 'false').'&step=2&totalcount="+guilddataArry.length+"&actcount="+i, guilddataArry[i], function(data){
						guilddata = $.parseJSON(data);
						if(guilddata.success == "available"){
							successdata = "<span style=\"color:orange;\">'.$this->game->glang('uc_armory_impduplex').'</span>";
						}else if(guilddata.success == "imported"){
							successdata = "<span style=\"color:green;\">'.$this->game->glang('uc_armory_imported').'</span>";
						}else{
							successdata = "<span style=\"color:red;\">'.$this->game->glang('uc_armory_impfailed').'</span>";
						}
						$("#guildimport_dataset fieldset.data").prepend("<dl><dt><label><img src=\""+ guilddata.image +"\" alt=\"charicon\" height=\"84\" width=\"84\" /></label></dt><dd>"+ guilddata.name+"<br/>"+ successdata +"</dd></dl>").children(":first").hide().fadeIn("slow");
						$("#progressbar").progressbar({ value: ((i/guilddataArry.length)*100) })
						if(guilddataArry.length > i+1){
							getData(i+1);
						}else{
							$("#controlbox").html("<dl><div class=\"infobox infobox-large infobox-green clearfix\"><i class=\"fa fa-check fa-4x pull-left\"></i> '.$this->game->glang('uc_gimp_header_fnsh').'</div></dl>").fadeIn("slow");
							return;
						}
					});
				}
			}');
		}else{
			$hmtlout .= '<div class="infobox infobox-large infobox-red clearfix"><i class="fa fa-exclamation-triangle fa-4x pull-left"></i> <span id="error_message_txt">'.$guilddata['reason'].'</span></div>';
		}
		return $hmtlout;
	}

	public function perform_step2(){
		//Build Rank ID
		$intRankID = $this->pdh->get('rank', 'default', array());
		if ($this->in->get('sync_rank') == 'true'){
			$arrRanks = $this->pdh->get('rank', 'id_list');
			$inRankID = $this->in->get('rank', 0);
			if (isset($arrRanks[$inRankID])) $intRankID = $arrRanks[$inRankID];
		}

		if(in_array($this->in->get('name', ''), $this->pdh->get('member', 'names', array()))){
			$successmsg = 'available';

			// Fix for connected realms....
			$member_id = $this->pdh->get('member', 'id', array($this->in->get('name', '')));
			
			$server_name = $this->in->get('servername', '');
			if(isset($server_name) && $server_name != ''){
				$this->pdh->put('member', 'update_profilefield', array($member_id, array('servername'=>$server_name)));
			}

			//Revoke Char
			if ($this->in->get('del', '') == 'true'){
				if ($member_id) {
					$this->pdh->put('member', 'revoke', array($member_id));
					$this->pdh->process_hook_queue();
				}
			}
			
			//Sync Rank
			if ($this->in->get('sync_rank') == 'true'){
				$member_id = $this->pdh->get('member', 'id', array($this->in->get('name', '')));
				if ($member_id) {
					$dataarry = array(
						'rankid'	=> $intRankID,
					);
					$myStatus = $this->pdh->put('member', 'addorupdate_member', array($member_id, $dataarry));
				}
			}
			
		}else{

			//Create new char
			$dataarry = array(
				'name'			=> $this->in->get('name',''),
				'level'			=> $this->in->get('level', 0),
				'class'			=> $this->game->obj['armory']->ConvertID($this->in->get('class', 0), 'int', 'classes'),
				'race'			=> $this->game->obj['armory']->ConvertID($this->in->get('race', 0), 'int', 'races'),
				'guild'			=> $this->in->get('guild', ''),
				'servername'	=> $this->in->get('servername', ''),
				'gender'		=> $this->in->get('gender', 0),
				'rankid'		=> $intRankID,
			);
			$myStatus = $this->pdh->put('member', 'addorupdate_member', array(0, $dataarry));
			
			$successmsg = ($myStatus) ? 'imported' : 'failed';

			// reset the cache
			$this->pdh->process_hook_queue();
		}

		// show the charimage & the name
		$chararray	= array('thumbnail'=>$this->in->get('thumbnail', ''), 'race'=>$this->in->get('race', 0), 'gender'=>$this->in->get('gender', 0));
		$charicon = $this->game->obj['armory']->characterIcon($chararray);
		if ($charicon == "") $charicon = $this->server_path.'images/global/avatar-default.svg';
		
		die(json_encode(array(
			'image'		=> $charicon,
			'name'		=> $this->in->get('name', ''),
			'success'	=> $successmsg
		)));
	}

	public function display(){
		$funcname = 'perform_step'.$this->in->get('step',0);
		$this->tpl->assign_vars(array(
			'DATA'		=> $this->$funcname(),
			'STEP'		=> ($this->in->get('step',0)+1)
		));

		$this->core->set_vars(array(
			'page_title'		=> $this->user->lang('uc_bttn_import'),
			'header_format'		=> 'simple',
			'template_file'		=> 'importer.html',
			'display'			=> true
		));
	}
}
registry::register('guildImporter');
?>