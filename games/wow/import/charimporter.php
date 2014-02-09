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

class charImporter extends page_generic {
	public function __construct() {
		$handler = array(
			'massupdate'		=> array('process' => 'perform_massupdate'),
			'resetcache'		=> array('process' => 'perform_resetcache'),
			'ajax_massupdate'	=> array('process' => 'ajax_massupdate'),
			'ajax_mudate'		=> array('process' => 'ajax_massupdatedate'),
		);
		parent::__construct(false, $handler, array());
		$this->user->check_auth('u_member_man');
		$this->user->check_auth('u_member_add');
		$this->game->new_object('bnet_armory', 'armory', array($this->config->get('uc_server_loc'), $this->config->get('uc_data_lang')));
		$this->process();
	}

	public function perform_resetcache(){
		// delete the cache folder
		$this->game->obj['armory']-> DeleteCache();

		// Output the success message
		$hmtlout = '<div id="guildimport_dataset">
						<div id="controlbox">
							<fieldset class="settings">
								<dl>
									'.$this->game->glang('uc_importcache_cleared').'
								</dl>
							</fieldset>
						</div>
					</div>';

		$this->tpl->assign_vars(array(
			'DATA'		=> $hmtlout,
			'STEP'		=> ''
		));

		$this->core->set_vars(array(
			'page_title'		=> $this->user->lang('raidevent_raid_guests'),
			'header_format'		=> 'simple',
			'template_file'		=> 'importer.html',
			'display'			=> true
		));
	}

	public function perform_massupdate(){
		// check permission again, cause this is for admins only
		$this->user->check_auth('a_members_man');

		// quit if there is not a server….
		if($this->config->get('uc_servername') == ''){
			return '<fieldset class="settings mediumsettings">
							<dl>
								<dt><label>'.$this->game->glang('uc_error_head').'</label></dt>
								<dd>'.$this->game->glang('uc_error_noserver').'</dd>
							</dl>
						</fieldset>';
		}

		$memberArry	= array();
		$members	= $this->pdh->get('member', 'names', array());
		if(is_array($members)){
			asort($members);
			foreach($members as $membernames){
				if($membernames != ''){
					$charid = $this->pdh->get('member', 'id', array($membernames));
					if($charid){
						$memberArry[] = array(
							'charname'	=> $membernames,
							'charid'	=> $charid,
						);
					}
				}
			}
		}
		$hmtlout = '<div id="guildimport_dataset">
						<div id="controlbox">
							<fieldset class="settings">
								<dl>
									'.$this->game->glang('uc_massupd_loading').'
									<div id="progressbar"></div>
								</dl>
							</fieldset>
						</div>
						<fieldset class="settings data">
						</fieldset>
					</div>';

		$this->tpl->add_js('$( "#progressbar" ).progressbar({ value: 0 }); getData();', 'docready');
		$this->tpl->add_js('
			var chardataArry = $.parseJSON(\''.json_encode($memberArry).'\');
			function getData(i){
				if (!i)
					i=0;
	
				if (chardataArry.length >= i){
					$.post("charimporter.php'.$this->SID.'&ajax_massupdate=true&totalcount="+chardataArry.length+"&actcount="+i, chardataArry[i], function(data){
						chardata = $.parseJSON(data);
						if(chardata.success == "imported"){
							successdata = "<span style=\"color:green;\">'.$this->game->glang('uc_armory_updated').'</span>";
						}else{
							successdata = "<span style=\"color:red;\">'.$this->game->glang('uc_armory_updfailed').'<br/>"+
							((chardata.error) ? "'.$this->game->glang('uc_armory_impfail_reason').' "+chardata.error : "")+"</span>";
						}
						$("#guildimport_dataset fieldset.data").prepend("<dl><dt><label><img src=\""+ chardata.image +"\" alt=\"charicon\" height=\"84\" width=\"84\" /></label></dt><dd>"+ chardata.name+"<br/>"+ successdata +"</dd></dl>").children(":first").hide().fadeIn("slow");
						$("#progressbar").progressbar({ value: ((i/chardataArry.length)*100) })
						if(chardataArry.length > i+1){
							getData(i+1);
						}else{
							$.post("charimporter.php'.$this->SID.'&ajax_mudate=true");
							$("#controlbox").html("<dl><div class=\"infobox infobox-large infobox-green clearfix\"><i class=\"fa fa-check fa-4x pull-left\"></i> '.$this->game->glang('uc_cupdt_header_fnsh').'</div></dl>").fadeIn("slow");
							return;
						}
					});
				}
			}');

		$this->tpl->assign_vars(array(
			'DATA'		=> $hmtlout,
			'STEP'		=> ''
		));

		$this->core->set_vars(array(
			'page_title'		=> $this->user->lang('raidevent_raid_guests'),
			'header_format'		=> 'simple',
			'template_file'		=> 'importer.html',
			'display'			=> true
		));
	}

	public function ajax_massupdatedate(){
		$this->config->set(array('uc_profileimported'=> $this->time->time));
	}

	public function ajax_massupdate(){
		// due to connected/virtual realms, check for a servername of the char
		$char_server	= $this->pdh->get('member', 'profile_field', array($this->in->get('charid', 0), 'servername'));
		$servername		= ($char_server != '') ? $char_server : $this->config->get('uc_servername');
		$chardata		= $this->game->obj['armory']->character($this->in->get('charname', ''), $servername, true);

		if(!isset($chardata['status'])){
			$errormsg	= '';
			$charname	= $chardata['name'];
			$charicon	= $this->game->obj['armory']->characterIcon($chardata);

			// insert into database
			$info		= $this->pdh->put('member', 'addorupdate_member', array($this->in->get('charid', 0), array(
				'name'				=> $this->in->get('charname', ''),
				'level'				=> $chardata['level'],
				'gender'			=> $this->game->obj['armory']->ConvertID($chardata['gender'], 'int', 'gender'),
				'race'				=> $this->game->obj['armory']->ConvertID($chardata['race'], 'int', 'races'),
				'class'				=> $this->game->obj['armory']->ConvertID($chardata['class'], 'int', 'classes'),
				'guild'				=> $chardata['guild']['name'],
				'last_update'		=> ($chardata['lastModified']/1000),
				'prof1_name'		=> $this->game->get_id('professions', $chardata['professions']['primary'][0]['name']),
				'prof1_value'		=> $chardata['professions']['primary'][0]['rank'],
				'prof2_name'		=> $this->game->get_id('professions', $chardata['professions']['primary'][1]['name']),
				'prof2_value'		=> $chardata['professions']['primary'][1]['rank'],
				'skill_1'			=> $this->game->obj['armory']->ConvertTalent($chardata['talents'][0]['spec']['icon']),
				'skill_2'			=> $this->game->obj['armory']->ConvertTalent($chardata['talents'][1]['spec']['icon']),
				'health_bar'		=> $chardata['stats']['health'],
				'second_bar'		=> $chardata['stats']['power'],
				'second_name'		=> $chardata['stats']['powerType'],
			), $this->in->get('overtakeuser')));

			$this->pdh->process_hook_queue();
			$successmsg	= ($info) ? 'imported' : 'error';
		}else{
			$successmsg	= 'error';
			$errormsg	= $chardata['reason'];
			$charname	= $this->in->get('charname', '');
			$charicon	= $this->server_path.'images/global/avatar-default.svg';
		}

		die(json_encode(array(
			'image'		=> $charicon,
			'name'		=> $charname,
			'success'	=> $successmsg,
			'error'		=> $errormsg
		)));
	}

	public function perform_step0(){

		$tmpmemname = '';
		if($this->in->get('member_id', 0) > 0){
			$tmpmemname = $this->pdh->get('member', 'name', array($this->in->get('member_id', 0)));
		}

		// generate output
		$hmtlout = '<fieldset class="settings mediumsettings">
			<dl>
				<dt><label>'.$this->game->glang('uc_charname').'</label></dt>
				<dd>'.new htext('charname', array('value' => (($tmpmemname) ? $tmpmemname : ''), 'size' => '25')).'</dd>
			</dl>';
		
		// Server Name
		$hmtlout .= '<dl>
				<dt><label>'.$this->game->glang('uc_servername').'</label></dt>
				<dd>';
		$hmtlout .= new htext('servername', array('value' => (($this->config->get('uc_servername')) ? stripslashes($this->config->get('uc_servername')) : ''), 'size' => '25', 'autocomplete' => $this->game->get('realmlist')));
		
		$hmtlout .= '</dd>
			</dl>
			<dl>
				<dt><label>'.$this->game->glang('uc_server_loc').'</label></dt>
				<dd>';
		if($this->config->get('uc_server_loc')){
			$hmtlout .= $this->config->get('uc_server_loc');
			
			$hmtlout .= new hhidden('server_loc', array('value' => $this->config->get('uc_server_loc')));
		}else{
			$hmtlout .= new hdropdown('server_loc', array('options' => $this->game->obj['armory']->getServerLoc()));
		}
		$hmtlout .= '</dd>
			</dl>';
		
		$hmtlout .= '</fieldset>';
		$hmtlout .= '<br/><input type="submit" name="submiti" value="'.$this->game->glang('uc_import_forw').'" class="mainoption" />';
		return $hmtlout;
	}

	public function perform_step1(){
		$hmtlout = '';
		if($this->in->get('member_id', 0) > 0){
			// We'll update an existing one...
			$isindatabase	= $this->in->get('member_id', 0);
			$isMemberName	= $this->pdh->get('member', 'name', array($isindatabase));
			$isServerName	= $this->config->get('uc_servername');
			$isServerLoc	= $this->config->get('uc_server_loc');
			$is_mine		= ($this->pdh->get('member', 'userid', array($isindatabase)) == $this->user->data['user_id']) ? true : false;
		}else{
			// Check for existing member name
			$isindatabase	= $this->pdh->get('member', 'id', array($this->in->get('charname')));
			$hasuserid		= ($isindatabase > 0) ? $this->pdh->get('member', 'userid', array($isindatabase)) : 0;
			$isMemberName	= $this->in->get('charname');
			$isServerName	= $this->in->get('servername');
			$isServerLoc	= $this->in->get('server_loc');
			if($this->user->check_auth('a_charmanager_config', false)){
				$is_mine	= true;			// We are an administrator, its always mine..
			}else{
				$is_mine	= (($hasuserid > 0) ? (($hasuserid == $this->user->data['user_id']) ? true : false) : true);	// we are a normal user
			}
		}

		if($is_mine){
			// Load the Armory Data
			$this->game->obj['armory']->setSettings(array('loc'=>$isServerLoc));
			$chardata	= $this->game->obj['armory']->character($isMemberName, $isServerName, true);
			//new hhidden('server_loc', array('value' => $this->config->get('uc_server_loc')))
			// Basics
			$hmtlout	.= new hhidden('member_id', array('value' => $isindatabase));
			$hmtlout	.= new hhidden('member_name', array('value' => $isMemberName));
			$hmtlout	.= new hhidden('member_level', array('value' => $chardata['level']));
			$hmtlout	.= new hhidden('gender', array('value' => $this->game->obj['armory']->ConvertID($chardata['gender'], 'int', 'gender')));
			$hmtlout	.= new hhidden('member_race_id', array('value' => $this->game->obj['armory']->ConvertID($chardata['race'], 'int', 'races')));
			$hmtlout	.= new hhidden('member_class_id', array('value' => $this->game->obj['armory']->ConvertID($chardata['class'], 'int', 'classes')));
			$hmtlout	.= new hhidden('guild', array('value' => $chardata['guild']['name']));
			$hmtlout	.= new hhidden('servername', array('value' => $isServerName));
			$hmtlout	.= new hhidden('last_update', array('value' => ($chardata['lastModified']/1000)));

			// primary professions
			$hmtlout	.= new hhidden('prof1_name', array('value' => $chardata['professions']['primary'][0]['name']));
			$hmtlout	.= new hhidden('prof1_value', array('value' => $chardata['professions']['primary'][0]['rank']));
			$hmtlout	.= new hhidden('prof2_name', array('value' => $chardata['professions']['primary'][1]['name']));
			$hmtlout	.= new hhidden('prof2_value', array('value' => $chardata['professions']['primary'][1]['rank']));

			// talents
			$hmtlout	.= new hhidden('skill_1', array('value' => $this->game->obj['armory']->ConvertTalent($chardata['talents'][0]['spec']['icon'])));
			$hmtlout	.= new hhidden('skill_2', array('value' => $this->game->obj['armory']->ConvertTalent($chardata['talents'][1]['spec']['icon'])));
			

			// health/power bar
			$hmtlout	.= new hhidden('health_bar', array('value' => $chardata['stats']['health']));
			$hmtlout	.= new hhidden('second_bar', array('value' => $chardata['stats']['power']));
			$hmtlout	.= new hhidden('second_name', array('value' => $chardata['stats']['powerType']));

			// viewable Output
			if(!isset($chardata['status'])){
				$hmtlout	.= '
				<div class="infobox infobox-large infobox-red clearfix">
					<i class="fa fa-exclamation-triangle fa-4x pull-left"></i> '.$this->game->glang('uc_charfound3').'
				</div>

				<fieldset class="settings mediumsettings">
					<dl>
						<dt><label><img src="'.$this->game->obj['armory']->characterIcon($chardata).'" name="char_icon" alt="icon" width="44px" height="44px" align="middle" /></label></dt>
						<dd>
							'.sprintf($this->game->glang('uc_charfound'), $isMemberName).'<br />
							'.sprintf($this->game->glang('uc_charfound2'), $this->time->user_date(($chardata['lastModified']/1000))).'
						</dd>
					</dl>
					<dl>';
				if(!$isindatabase){
					if($this->user->check_auth('u_member_conn', false)){
						$hmtlout	.= $this->user->lang('overtake_char').' '.new hradio('overtakeuser', array('value' => 1));
					}else{
						$hmtlout	.= $this->user->lang('overtake_char').' '.new hradio('overtakeuser', array('value' => 1, 'disabled' => true));
						$hmtlout	.= new hhidden('overtakeuser', array('value' => '1'));
					}
				}
				$hmtlout	.= '
					</dl>
					</fieldset>';
				$hmtlout		.= '<center><input type="submit" name="submiti" value="'.$this->game->glang('uc_prof_import').'" class="mainoption" /></center>';
			}else{
				$hmtlout		.= '<div class="infobox infobox-large infobox-red clearfix">
										<i class="fa fa-exclamation-triangle fa-4x pull-left"></i> <b>WARNING: </b> '.$chardata['reason'].'
									</div>';
			}
		}else{
			$hmtlout	.= '<div class="infobox infobox-large infobox-red clearfix">
								<i class="fa fa-exclamation-triangle fa-4x pull-left"></i> '.$this->game->glang('uc_notyourchar').'
							</div>';
		}
		return $hmtlout;
	}

	public function perform_step2(){
		$data = array(
			'name'				=> $this->in->get('member_name'),
			'level'				=> $this->in->get('member_level', 0),
			'gender'			=> $this->in->get('gender', 'Male'),
			'race'				=> $this->in->get('member_race_id', 0),
			'class'				=> $this->in->get('member_class_id', 0),
			'guild'				=> $this->in->get('guild',''),
			'last_update'		=> $this->in->get('last_update', 0),
			'prof1_name'		=> $this->game->get_id('professions', $this->in->get('prof1_name', '')),
			'prof1_value'		=> $this->in->get('prof1_value', 0),
			'prof2_name'		=> $this->game->get_id('professions', $this->in->get('prof2_name', '')),
			'prof2_value'		=> $this->in->get('prof2_value', 0),
			'skill_1'			=> $this->in->get('skill_1', 0),
			'skill_2'			=> $this->in->get('skill_2', 0),
			'health_bar'		=> $this->in->get('health_bar', 0),
			'second_bar'		=> $this->in->get('second_bar', 0),
			'second_name'		=> $this->in->get('second_name', ''),
			'servername'		=> $this->in->get('servername', ''),
		);

		$info		= $this->pdh->put('member', 'addorupdate_member', array($this->in->get('member_id', 0), $data, $this->in->get('overtakeuser', 0)));
		$this->pdh->process_hook_queue();
		if($info){
			$hmtlout	= '<div class="infobox infobox-large infobox-green clearfix">
								<i class="fa fa-check fa-4x pull-left"></i> '.$this->game->glang('uc_armory_updated').'
							</div>';
		}else{
			$hmtlout	= '<div class="infobox infobox-large infobox-red clearfix">
								<i class="fa fa-exclamation-triangle fa-4x pull-left"></i> '.$this->game->glang('uc_armory_updfailed').'
							</div>';
		}
		return $hmtlout;
	}

	public function display(){

		// quit if there is not a server….
		if($this->config->get('uc_servername') == ''){
			$this->tpl->assign_vars(array(
				'DATA'		=> '<fieldset class="settings mediumsettings">
							<dl>
								<dt><label>'.$this->game->glang('uc_error_head').'</label></dt>
								<dd>'.$this->game->glang('uc_error_noserver').'</dd>
							</dl>
						</fieldset>'
			));
		}else{
			$stepnumber		= ($this->config->get('uc_servername') && $this->config->get('uc_server_loc') && $this->in->get('member_id',0) > 0 && $this->in->get('step',0) == 0) ? 1 : $this->in->get('step',0);
			$urladdition	 = ($this->in->get('member_id',0)) ? '&amp;member_id='.$this->in->get('member_id',0) : '';
			$funcname		 = 'perform_step'.$stepnumber;
			$this->tpl->assign_vars(array(
				'DATA'		=> $this->$funcname(),
				'STEP'		=> ($stepnumber+1).$urladdition
			));
		}
		$this->core->set_vars(array(
			'page_title'		=> $this->user->lang('raidevent_raid_guests'),
			'header_format'		=> 'simple',
			'template_file'		=> 'importer.html',
			'display'			=> true
		));
	}
}
registry::register('charImporter');
?>