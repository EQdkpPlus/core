<?php
/*	Project:	EQdkp-Plus
 *	Package:	EQdkp-plus
 *	Link:		http://eqdkp-plus.eu
 *
 *	Copyright (C) 2006-2016 EQdkp-Plus Developer Team
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
 
class embedly extends gen_class {

	public function __construct(){}
	
	public $arrServices = array(
				'youtube' => array(
					'regex' => ["https?://(?:[^\.]+\.)?youtube\.com/watch/?\?(?:.+&)?v=([^&]+)","https?://(?:[^\.]+\.)?(?:youtu\.be|youtube\.com/embed)/([a-zA-Z0-9_-]+)"],
					'format'=> 'json',
					'oembed'=> 'http://www.youtube.com/oembed?url=URL',
				),
				'twitch' => array(
					'regex' => ["https?://clips\.twitch\.tv/.*","https?://clips\.twitch\.tv/.*","https?://www\.twitch\.tv/.*","https?://www\.twitch\.tv/.*","https?://twitch\.tv/.*","https?://twitch\.tv/.*"],
					'format' => 'json',
					'oembed' => 'https://api.twitch.tv/v4/oembed?url=URL',
				),
				'vidme' => array(
					'regex' => ['https://vidd\.me/.*', 'https?://vid\.me/.*'],
					'format'=> 'json',
					'oembed'=> 'https://vid.me/api/videos/oembed.json?url=URL'
				),
				'facebook_video' => array(
					'regex' => ['https?://www\.facebook\.com/video\.php.*', 'https?://www\.facebook\.com/.*/videos/.*'],
					'format' => 'json',
					'oembed'=> 'https://www.facebook.com/plugins/video/oembed.json/?url=URL'
				),
				'facebook_posts' => array(
					'regex' => ['https?://www\.facebook\.com/.*/posts/.*', 'https?://www\.facebook\.com/.*/activity/.*', 'https?://www\.facebook\.com/photo\.php.*', 'https?://www\.facebook\.com/photos/.*', 'https?://www\.facebook\.com/permalink\.php.*', 'https?://www\.facebook\.com/media/set?set=.*', 'https?://www\.facebook\.com/questions/.*', 'https?://www\.facebook\.com/notes/.*'],
					'format' => 'json',
					'oembed'=> 'https://www.facebook.com/plugins/post/oembed.json/?url=URL'
				),
				'instagram' => array(
					'regex' => ["https?://(www\.)?instagram\.com/p/.*","https?://(www\.)?instagr\.am/p/.*","https?://(www\.)?instagram\.com/p/.*","https?://instagr\.am/p/.*"],
					'format' => 'json',
					'oembed'=> 'https://api.instagram.com/oembed?url=URL'
				),
				'twitter' => array(
					'regex' => ['https?://twitter\.com/.*/status/.*'],
					'format'=> 'json',
					'oembed'=> 'https://publish.twitter.com/oembed?url=URL'
				),
				'flickr' => array(
					'regex' => ['https?://.*\.flickr\.com/photos/.*', 'https?://flic\.kr/p/.*'],
					'format'=> 'json',
					'oembed'=> 'https://www.flickr.com/services/oembed.json?url=URL'
				),
				'vimeo' => array(
					'regex' => ["https?://vimeo\.com/.*","https?://vimeo\.com/album/.*/video/.*","https?://vimeo\.com/channels/.*/.*","https?://vimeo\.com/groups/.*/videos/.*","https?://vimeo\.com/ondemand/.*/.*","https?://player\.vimeo\.com/video/.*"],
					'format'=> 'json',
					'oembed'=> 'https://vimeo.com/api/oembed.json?url=URL',
				),
				'soundcloud' => array(
					'regex' => ["https?://soundcloud.com/.*"],
					'format'=> 'json',
					'oembed'=> 'https://soundcloud.com/oembed.json?url=URL',
				),
		);
	
	//Parse one single Link
	public function parseLink($link){
		if (strlen($link) == 0) return '';
		$oembed = $this->getLinkDetails($link);
		
		
		if ($oembed && is_array($oembed) && count($oembed)){
			$out = $this->formatEmbedded($oembed[0]);
		}
		
		return $out;
	}
	
	//Get all Information for an single Link, like Thumbnail, Size, ...
	public function getLinkDetails($link){
		if (strlen($link) == 0) return false;

		$oembed = $this->getMeta($link);
		if ($oembed->type == "error" || $oembed === false){
			return false;
		}
		return $oembed;
	}
	
	//Parse an String for Hyperlinks and replace Videos and Images
	public function parseString($string, $maxwidth=false, $blnEncodeMediaTags=false){
		if (strlen($string) == 0) return '';		

		$embedlyUrls = array();
		//First, get the links
		$arrLinks = $arrAreas = array();
		
		//Detect Areas
		$intAreas = preg_match_all("/(.*)<!-- NO_EMBEDLY -->(.*)<!-- END_NO_EMBEDLY -->(.*)/misU", $string, $arrAreas);
		$strIgnore = "";
		if(isset($arrAreas[2]) && count($arrAreas[2])){
			$strIgnore = implode(' ', $arrAreas[2]);
		}

		//Build Array for Links to ignore
		$arrIgnoreLinksMatches = $arrIgnoreLinks = array();
		if($strIgnore != ""){
			$intIgnoreLinks = preg_match_all('@((("|:|])?)https?:\/\/([-\w\.]+)+(:\d+)?(\/([\w\/_\-\.]*(\?[^<\s]+)?)?)?)@', $strIgnore, $arrIgnoreLinksMatches);
			if ($intIgnoreLinks){
				foreach ($arrIgnoreLinksMatches[0] as $link){
					$link = html_entity_decode($link);
					$strFirstChar = substr($link, 0, 1);
					if ($strFirstChar != '"' && $strFirstChar != ':' && $strFirstChar != ']') {
						$arrIgnoreLinks[] = strip_tags($link);
					}
				}
			}
		}	
		
		$intLinks = preg_match_all('@((("|:|])?)https?:\/\/([-\w\.]+)+(:\d+)?(\/([\w\/_\-\.]*(\?[^<\s]+)?)?)?)@', $string, $arrLinks);
		
		$arrDecodedLinks = array();
		if ($intLinks){
			$key = 0;
			foreach ($arrLinks[0] as $link){
				$orig_link = $link;
				$link = html_entity_decode($link);
				$strFirstChar = substr($link, 0, 1);
				if ($strFirstChar != '"' && $strFirstChar != ':' && $strFirstChar != ']') {
					$strMyLink = strip_tags($link);
					if(in_array($strMyLink, $arrIgnoreLinks)) continue;
					
					$embedlyUrls[$key] = $strMyLink;
					$arrDecodedLinks[$key] = $orig_link;
					$key++;
				}
			}	
		}

		//Now let's get the information from embedly
		$oembeds = $this->getMeta($embedlyUrls);
		
		//And now let's replace the Links with the Videos or pictures
		foreach ($oembeds as $key => $oembed){
			if($oembed === false) continue;
			
			$out = $this->formatEmbedded($oembed);
			if (strlen($out)){
				$out = ($blnEncodeMediaTags) ? htmlspecialchars($out) : $out;
				
				$string = preg_replace("#([^\"':])".preg_quote($arrDecodedLinks[$key], '#')."#i", "$1".$out, $string);
			}
		}

		return $string;
	}
		
		
	//Styling for Embedded Images/Objects
	private function formatEmbedded($objEmbedly){
		$out = '';
		switch($objEmbedly->type) {
				case 'photo':
					$out = '<div class="embed-content"><div class="embed-'.$objEmbedly->type.'">';
					if (isset($objEmbedly->title)) {
						$title = $objEmbedly->title;
					} else {
						$title = 'Image';
					}
					$out .= '<img src="'.$objEmbedly->url.'" alt="'.$title.'" />';

					$out .= '</div></div>';
					break;
				case 'link':
					return $out;
				case 'rich':
				case 'video':
					$out = '<div class="embed-content"><div class="embed-media">';
					$out .= $objEmbedly->html;
					$out .= '</div></div>';
					break;
				case 'error':
				default:
		}
		return $out;
	}
	
	private function getMeta($mixLink){
		if(is_array($mixLink)){
			$arrOut = array();
			foreach($mixLink as $key => $strLink){
				$arrOut[$key] = $this->getMeta($strLink);
			}
			return $arrOut;
		} else {
			//Check link
			$mixResult = $this->checkLink($mixLink);
			if($mixResult !== false){
				$arrMyService = $this->arrServices[$mixResult];
				
				$strUrl = str_replace('URL', urlencode($mixLink), $arrMyService['oembed']);
				
				$strResult = register('urlfetcher')->fetch($strUrl);
				
				if($strResult){
					if($arrMyService['format'] == 'xml'){
						$arrResult = simplexml_load_string($strResult);
					} else{
						$arrResult = json_decode($strResult);
					}
					
					return ($arrResult) ? $arrResult : false;
				} else {
					return false;
				}
			}
		}
		return false;
	}
	
	public function checkLink($strLink){
		foreach($this->arrServices as $strServicename => $arrServiceDetails){
			$arrRegex = $arrServiceDetails['regex'];
			foreach($arrRegex as $strRegex){
				if(preg_match('#'.$strRegex.'#', $strLink)) return $strServicename;
			}
		}
		return false;
	}
}

?>