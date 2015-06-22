<?php
/*	Project:	EQdkp-Plus
 *	Package:	Livestreams Portal Module
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

class livestreams_portal extends portal_generic {

	protected static $path		= 'livestreams';
	protected static $data		= array(
		'name'			=> 'Livestreams Module',
		'version'		=> '0.1.0',
		'author'		=> 'GodMod',
		'contact'		=> EQDKP_PROJECT_URL,
		'icon'			=> 'fa-video-camera',
		'description'	=> 'Shows status of the users\' livestreams',
		'lang_prefix'	=> 'ls_',
		'multiple'		=> true,
	);
	
	public $template_file = 'livestreams.html';
	
	protected $settings	= array(
	);
	
	protected static $install	= array(
		'autoenable'		=> '0',
		'defaultposition'	=> 'left',
		'defaultnumber'		=> '2',
	);
	
	protected static $apiLevel = 20;
	
	public function get_settings($state){
		
		return $this->settings;
	}

	public function output() {
		$arrUserIDs = $this->pdh->sort($this->pdh->get('user', 'id_list'), 'user', 'name', 'asc');
		
		$this->tpl->add_js('
			 $(".ls_twitch").each(function (index, streamName) {
				myStreamName = $(this).data("streamname");
				
	            $.ajax({
	                url: "https://api.twitch.tv/kraken/streams/" + myStreamName,
	                dataType: "jsonp",
	                type: "get",
	                complete: function (jqXHR, textStatus) {
	                    twitchStreamComplete(myStreamName, jqXHR);
	                }
	            });
	        });
				
			function twitchStreamComplete(streamName, resp) {				
	            if (resp.responseJSON.stream != null) {
	               $("." + streamName + "_status").html("<i class=\"eqdkp-icon-offline blink_me\" style=\"color:red;\"></i> '.$this->jquery->sanitize($this->user->lang('online')).'");
	            } else {
	                $("." + streamName + "_status").html("<i class=\"fa fa-close\" style=\"color:red;\"></i> '.$this->jquery->sanitize($this->user->lang('offline')).'");
	            }
	        }
				
			$(".ls_hb").each(function (index, streamName) {
				myStreamName = $(this).data("streamname");
				
	             $.ajax({
	                url: "https://api.hitbox.tv/media/status/" + myStreamName + ".json",
					dataType: "json",
	                type: "get",
	                complete: function (jqXHR, textStatus) {
	                    hbStreamComplete(myStreamName, jqXHR);
	                }
	            });
	        });
				
			function twitchStreamComplete(streamName, resp) {				
	            if (resp.responseJSON.stream != null) {
	               $("." + streamName + "_status").html("<i class=\"eqdkp-icon-offline blink_me\" style=\"color:red;\"></i> '.$this->jquery->sanitize($this->user->lang('online')).'");
	            } else {
	                $("." + streamName + "_status").html("<i class=\"fa fa-close\" style=\"color:red;\"></i> '.$this->jquery->sanitize($this->user->lang('offline')).'");
	            }
	        }
				
			function hbStreamComplete(streamName, resp) {				
	            if (resp.responseJSON.media_is_live == "1") {
	               $("." + streamName + "_status").html("<i class=\"eqdkp-icon-offline blink_me\" style=\"color:red;\"></i> '.$this->jquery->sanitize($this->user->lang('online')).'");
	            } else {
	                $("." + streamName + "_status").html("<i class=\"fa fa-close\" style=\"color:red;\"></i> '.$this->jquery->sanitize($this->user->lang('offline')).'");
	            }
	        }	
				
		', 'docready');
		
		$this->tpl->add_css(".blink_me {
    -webkit-animation-name: blinker;
    -webkit-animation-duration: 3s;
    -webkit-animation-timing-function: linear;
    -webkit-animation-iteration-count: infinite;

    -moz-animation-name: blinker;
    -moz-animation-duration: 3s;
    -moz-animation-timing-function: linear;
    -moz-animation-iteration-count: infinite;

    animation-name: blinker;
    animation-duration: 3s;
    animation-timing-function: linear;
    animation-iteration-count: infinite;
}

@-moz-keyframes blinker {  
    0% { opacity: 1.0; }
    50% { opacity: 0.0; }
    100% { opacity: 1.0; }
}

@-webkit-keyframes blinker {  
    0% { opacity: 1.0; }
    50% { opacity: 0.0; }
    100% { opacity: 1.0; }
}

@keyframes blinker {  
    0% { opacity: 1.0; }
    50% { opacity: 0.0; }
    100% { opacity: 1.0; }
}");

		foreach($arrUserIDs as $intUserID){
			$strTwitch = $this->pdh->get('user', 'profilefield_by_name', array($intUserID, 'twitch'));
			if($strTwitch && $strTwitch != ""){
					
				$strUsername = $this->pdh->get('user', 'name', array($intUserID));
			
				$this->tpl->assign_block_vars('ls_user_row', array(
					'USERNAME' 		=> $strUsername,
					'USERLINK' 		=> $this->routing->build('user', $strUsername, 'u'.$intUserID),
					'STREAM_TYPE'	=> 'twitch',
					'STREAM_NAME'	=> '<i class="fa fa-twitch" title="Twitch"></i>',
					'STREAM_LINK'	=> 'http://www.twitch.tv/'.utf8_strtolower($strTwitch),
					'STREAM_USERNAME' => sanitize(utf8_strtolower($strTwitch)),
				));
			}
			
			$strHitbox = $this->pdh->get('user', 'profilefield_by_name', array($intUserID, 'hitbox'));
			
			if($strHitbox && strlen($strHitbox)){
			
				$strUsername = $this->pdh->get('user', 'name', array($intUserID));
			
				$this->tpl->assign_block_vars('ls_user_row', array(
					'USERNAME' 		=> $strUsername,
					'USERLINK' 		=> $this->routing->build('user', $strUsername, 'u'.$intUserID),
					'STREAM_TYPE'	=> 'hb',
					'STREAM_NAME'	=> 'Hitbox',
					'STREAM_LINK'	=> 'http://www.hitbox.tv/'.utf8_strtolower($strHitbox),
					'STREAM_USERNAME' => sanitize(utf8_strtolower($strHitbox)),
				));
			}
		}
	}
}
?>