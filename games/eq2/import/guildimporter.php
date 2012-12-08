<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2011
 * Date:		$Date: 2011-11-25 16:34:26 +0100 (Fr, 25. Nov 2011) $
 * -----------------------------------------------------------------------
 * @author		$Author: hoofy $
 * @copyright	2006-2011 EQdkp-Plus Developer Team
 * @link		http://eqdkp-plus.com
 * @package		eqdkp-plus
 * @version		$Rev: 11442 $
 * 
 * $Id: guildimporter.php 11442 2011-11-25 15:34:26Z hoofy $
 */
 
define('EQDKP_INC', true);
$eqdkp_root_path = './../../../';
include_once ($eqdkp_root_path . 'common.php');

class guildImporter extends page_generic {
	public static function __shortcuts() {
		$shortcuts = array('user', 'tpl', 'in', 'pdh', 'game', 'core', 'html', 'config');
		return array_merge(parent::$shortcuts, $shortcuts);
	}

	public function __construct() {
		$handler = array();
		parent::__construct(false, $handler, array());
		$this->user->check_auth('a_members_man');
		$this->game->new_object('eq2_soe', 'soe', array());
		$this->process();
	}

	public function perform_step0(){
		// classes array
		$classfilter	= $this->game->get('classes');
		$classfilter[0]	= $this->game->glang('uc_class_nofilter');

		// generate output
		$hmtlout = '<fieldset class="settings mediumsettings">
			<dl>
				<dt><label>'.$this->game->glang('uc_guild_name').'</label></dt>
				<dd>'.$this->html->widget(array('fieldtype'=>'text','name'=>'guildname','value'=> $this->config->get('guildtag'), 'size'=>'40')).'</dd>
			</dl>
			<dl>
				<dt><label>'.$this->game->glang('uc_delete_chars_onimport').'</label></dt>
				<dd>'.$this->html->widget(array('fieldtype'=>'boolean','name'=>'delete_old_chars')).'</dd>
			</dl>
			</fieldset>
			<fieldset class="settings mediumsettings">
				<legend>'.$this->game->glang('uc_filter_name').'</legend>

				<dl>
					<dt><label>'.$this->game->glang('uc_class_filter').'</label></dt>
					<dd>'.$this->html->widget(array('fieldtype'=>'dropdown','name'=>'filter_class','value'=> '', 'options'=>$classfilter)).'</dd>
				</dl>
				<dl>
					<dt><label>'.$this->game->glang('uc_level_filter').'</label></dt>
					<dd>'.$this->html->widget(array('fieldtype'=>'text','name'=>'filter_level','value'=> 0, 'size'=>'5')).'</dd>
				</dl>
			</fieldset>';
		$hmtlout .= '<br/><input type="submit" name="submiti" value="'.$this->game->glang('uc_import_forw').'" class="mainoption bi_ok" />';
		return $hmtlout;
	}

	public function perform_step1(){
		if($this->in->get('guildname', '') == ''){
			return '<div class="errorbox roundbox"><div class="icon_ok" id="error_message_txt">'.$this->game->glang('uc_imp_noguildname').'</div></div>';
		}
		
		//Suspend all Chars
		if ($this->in->get('delete_old_chars',0)){
			$this->pdh->put('member', 'suspend', array('all'));
		}

		// generate output
		$guilddata	= $this->game->obj['soe']->guild($this->in->get('guildname', ''), $this->config->get('uc_servername'), true);
		$this->config->set('uc_guildid', $this->in->get('guildname', ''));
		//d($guilddata);

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
			foreach($guilddata['character_list'] as $guildchars){
				// filter: class
				if($this->in->get('filter_class', 0) > 0 && $this->game->obj['soe']->ConvertID((int)$guildchars['type']['classid'], 'int', 'classes') != $this->in->get('filter_class', 0)){
					continue;
				}

				// filter: level
				if($this->in->get('filter_level', 0) > 0 && (int)$guildchars['type']['level'] < $this->in->get('filter_level', 0)){
					continue;
				}
				
				// Build the array
				$jsondata[] = array(
					'name'		=> $guildchars['name']['first'],
					'class'		=> (int)$guildchars['type']['classid'],
					'race'		=> (int)$guildchars['type']['raceid'],
					'level'		=> (int)$guildchars['type']['level'],
					'gamecharid'=> (string)$guildchars['id'],
				);
			}

			$this->tpl->add_js('
				$( "#progressbar" ).progressbar({
					value: 0
				});
				getData();', 'docready');
			$this->tpl->add_js('
			var guilddataArry = $.parseJSON(\''.json_encode($jsondata).'\');
			function getData(i){
				if (!i)
					i=0;
	
				if (guilddataArry.length >= i){
					$.post("guildimporter.php'.$this->SID.'&del='.(($this->in->get('delete_old_chars',0)) ? 'true' : 'false').'&step=2&totalcount="+guilddataArry.length+"&actcount="+i, guilddataArry[i], function(data){
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
							$("#controlbox").html("<dl><div class=\"greenbox roundbox\"><div class=\"icon_ok\" id=\"error_message_txt\">'.$this->game->glang('uc_gimp_header_fnsh').'</div></div></dl>").fadeIn("slow");
							return;
						}
					});
				}
			}');
		}else{
			$hmtlout .= '<div class="errorbox roundbox"><div class="icon_ok" id="error_message_txt">'.$guilddata['reason'].'</div></div>';
		}
		return $hmtlout;
	}

	public function perform_step2(){
		if(in_array($this->in->get('name', ''), $this->pdh->get('member', 'names', array()))){
			$successmsg = 'available';
			$charid = $this->pdh->get('member', 'id', array($this->in->get('name', '')));
			if ($charid) {
				$gamecharid = $this->pdh->get('member', 'picture', array($charid));
				$charicon = $this->game->obj['soe']->characterIcon($gamecharid, true);
				
				//Revoke Char
				if ($this->in->get('del', '') == 'true'){
					$this->pdh->put('member', 'revoke', array($charid));
					$this->pdh->process_hook_queue();
				}
			} else {
				$charicon = $this->root_path.'images/no_pic.png';
			}
		}else{
			$dataarry = array(
				'name'		=> $this->in->get('name',''),
				'lvl'		=> $this->in->get('level', 0),
				'classid'	=> $this->game->obj['soe']->ConvertID($this->in->get('class', 0), 'int', 'classes'),
				'raceid'	=> $this->game->obj['soe']->ConvertID($this->in->get('race', 0), 'int', 'races'),
				'picture'	=> $this->in->get('gamecharid', ''),
			);
			$charicon = $this->game->obj['soe']->characterIcon($this->in->get('gamecharid', ''), true);
			$myStatus = $this->pdh->put('member', 'addorupdate_member', array(0, $dataarry));
			$successmsg = ($myStatus) ? 'imported' : 'failed';

			// reset the cache
			$this->pdh->process_hook_queue();
		}

		die(json_encode(array(
			'image'		=> ($charicon == '') ? $this->root_path.'images/no_pic.png' : $charicon,
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