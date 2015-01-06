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
	$this->jquery->Tab_header('char1_tabs');

	// init infotooltip
	infotooltip_js();

	// Add css & JS Code
	$this->tpl->add_css("
		.profession-name { padding-left: 4px; }
		.profession-icon { margin-left: 4px; }
		.profession-row { height: 40px !important; }

		.sw-tab {
			outline: none;
			border-color: #E2D872;
			border-radius: 10px; 
			box-shadow: 0 0 10px #E2D872;
			-webkit-box-shadow: 0 0px 10px #E2D872;
			-moz-box-shadow: 0 0px 10px #E2D872;
			}
		.sw-charname {
			color:#E0BD49;
			font: small-caps 48px/60px Berling,Times New Roman,serif;
			text-shadow: 1px 1px 2px #000;
			filter: dropshadow(color=#000, offx=1, offy=1);
			border: none;
		}

		.sw-guilde {
			color:#E0BD49;
			font: small-caps 25px/30px Berling,Times New Roman,serif;
			text-shadow: 1px 1px 2px #000;
			filter: dropshadow(color=#000, offx=1, offy=1);
			border: none;
		}
		.sw-picture {
			    margin-left: auto;
				margin-right: auto;
			}

		.sw-picture img {
			max-width: 700px;
			}
		.sw-wrapper {
			width: 700px;
			margin: 0 auto;
			}
		.sw-header {
			width: 700;
			height: 150px;
			font-size: 40px;
			color:#E0BD49
			}
		.sw-middle {
			position: relative;
			}
		.sw-middle:after {
			display: table;
			clear: both;
			fa-content: '';
			}
		.sw-container {
			width: 100%;
			float: left;
			overflow: hidden;
			}
		.sw-content {
			padding: 0 320px 0 170px;
			}
		.sw-left {
			float: left;
			width: 150px;
			height: 150px;
			margin-left: -100%;
			position: relative;
			}
		.sw-right {
			float: left;
			width: 300px;
			height: 150px;
			margin-left: -300px;
			position: relative;
			}
		.sw-footer {
			width: 700px;
			}

	");
		$swtoravatar = $this->server_path."games/swtor/profiles/avatar/swtor.png";
		$this->tpl->assign_vars(array(
			'CHARDATA_NAME'			=> $chardata['name'],
			'SWTOR_AVATAR'			=> $swtoravatar,
			'IMG_CLASSICON_BIG'   => $this->game->decorate('primary', $member[$this->game->get_primary_class(true)].'_b', $this->pdh->get('member', 'profiledata', array($this->url_id)), 200),
		));
	
		$a_lang_profession = $this->game->get('professions');
		$a_professions = array(
			0	=> array(
				'icon'			=> $this->server_path."games/swtor/profiles/professions/".(($member['prof1_name']) ? $member['prof1_name'] : '0').".png",
				'name'			=> $a_lang_profession[$member['prof1_name']],
				'progressbar'	=> $this->jquery->progressbar('profession1', 0, array('completed' => $member['prof1_value'], 'total' => 500, 'text' => '%progress%'))
			),
			1	=> array(
				'icon'			=> $this->server_path."games/swtor/profiles/professions/".(($member['prof2_name']) ? $member['prof2_name'] : '0').".png",
				'name'			=> $a_lang_profession[$member['prof2_name']],
				'progressbar'	=> $this->jquery->progressbar('profession2', 0, array('completed' => $member['prof2_value'], 'total' => 500, 'text' => '%progress%'))
			),
			2	=> array(
				'icon'			=> $this->server_path."games/swtor/profiles/professions/".(($member['prof3_name']) ? $member['prof3_name'] : '0').".png",
				'name'			=> $a_lang_profession[$member['prof3_name']],
				'progressbar'	=> $this->jquery->progressbar('profession3', 0, array('completed' => $member['prof3_value'], 'total' => 500, 'text' => '%progress%'))
			)
		);
				foreach ($a_professions as $v_professions){
			$this->tpl->assign_block_vars('professions', array(
				'ICON'			=> $v_professions['icon'],
				'NAME'			=> $v_professions['name'],
				'BAR'			=> $v_professions['progressbar'],
			));
		}
		//faction
		$swfaction = $this->pdh->get('member', 'profile_field', array($this->url_id, 'faction'));
		$this->tpl->assign_vars(array(
		'SW_FACTION'			=> $this->server_path."games/swtor/profiles/faction/".$swfaction,
		));
		
		//reputation
		$max_wert = 70000;
		for ($i=1; $i<=20; $i++){
			$meinWert	= (int)$this->pdh->get('member', 'profile_field', array($this->url_id, 'ruf'.$i.'_value'));
			$ba			= $this->jquery->progressbar('reputationbar_'.$i, 0, array('completed' => $meinWert, 'total' => $max_wert, 'text' => '%progress% (%percentage%)'));

			if ($meinWert <2500){
				$repicon = $this->server_path."games/swtor/profiles/reputation/icon0.png";
				$r = 0;
			}elseif ($meinWert >= 2500 && $meinWert < 7500){
				$repicon = $this->server_path."games/swtor/profiles/reputation/icon1.png";
				$r = 1;
			}elseif ($meinWert >= 7500 && $meinWert < 15000){
				$repicon = $this->server_path."games/swtor/profiles/reputation/icon2.png";
				$r = 2;
			}elseif ($meinWert >= 15000 && $meinWert < 25000){
				$repicon = $this->server_path."games/swtor/profiles/reputation/icon3.png";
				$r = 3;
			}elseif ($meinWert >= 25000 && $meinWert < 40000){
				$repicon = $this->server_path."games/swtor/profiles/reputation/icon4.png";
				$r = 4;
			}elseif ($meinWert >= 40000 && $meinWert < 70000){
				$repicon = $this->server_path."games/swtor/profiles/reputation/icon5.png";
				$r = 5;
			}elseif ($meinWert >= 70000){
				$repicon = $this->server_path."games/swtor/profiles/reputation/icon6.png";
				$r = 6;
			}
			
			$this->tpl->assign_block_vars(
				'reputation', array(
								'BAR' => $ba,
								'NAME' => $this->game->glang('ruf'.$i),
								'REPUTATIONICON' => $repicon,
								'TOOL' => $this->game->glang('repuname'.$r)
								)
							);
			}

?>