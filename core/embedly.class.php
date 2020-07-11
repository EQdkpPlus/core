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
	public static $shortcuts = array('puf'=>'urlfetcher');

	private $strImageCacheFolder = "";
	private $arrImageExtensions = array('jpg', 'png', 'gif', 'jpeg');
	private $arrCache = array();


	public function __construct(){
		$this->strImageCacheFolder = $this->pfh->FolderPath('embedd', 'eqdkp');
	}

	protected $arrServices = array(
				'youtube' => array(
					'regex' => ["https?://(?:[^\.]+\.)?youtube\.com/watch/?\?(?:.+&)?v=([^&]+)","https?://(?:[^\.]+\.)?(?:youtu\.be|youtube\.com/embed)/([a-zA-Z0-9_-]+)"],
					'format'=> 'json',
					'oembed'=> 'https://www.youtube.com/oembed?url=URL',
				),
				'twitch' => array(
					'regex' => ["https?://clips\.twitch\.tv/.*","https?://www\.twitch\.tv/.*","https?://twitch\.tv/.*"],
					'function' => 'embedTwitch',
				),
				'vidme' => array(
					'regex' => ['https://vidd\.me/.*', 'https?://vid\.me/.*'],
					'format'=> 'json',
					'oembed'=> 'https://vid.me/api/videos/oembed.json?url=URL'
				),
				'facebook_video' => array(
					'regex' => ['https://www\.facebook\.com/video\.php.*', 'https://www\.facebook\.com/.*/videos/.*', 'https://www.facebook.com/watch/?v=.*'],
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
				'spotify' => array(
					'regex' =>["https://open\.spotify\.com/album/.*", "https://open\.spotify\.com/track/.*", "https://open\.spotify\.com/artist/.*"],
					'format' => 'json',
					'oembed' => 'https://open.spotify.com/oembed?url=URL',
				),
				'playstv' => array(
						'regex' =>["https://plays\.tv/video/.*"],
						'format' => 'json',
						'oembed' => 'https://plays.tv/oembed?url=URL&format=json',
				),
		);

	//Parse one single Link
	public function parseLink($link){
		if (strlen($link) == 0) return '';
		$oembed = $this->getLinkDetails($link);

		if ($oembed && (is_array($oembed) || is_object($oembed)) && count($oembed)){
			$out = $this->formatEmbedded($oembed, $link);
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
			$intIgnoreLinks = preg_match_all('@((("|:|])?)https?:\/\/([-\w\.]+)+(:\d+)?(\/([\w\/_\-\.]*(\?[^<\s"]+)?)?)?)@', $strIgnore, $arrIgnoreLinksMatches);
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

		$intLinks = preg_match_all('@((("|:|])?)https?:\/\/([-\w\.]+)+(:\d+)?(\/([\w\/_\-\.]*(\?[^<\s"]+)?)?)?)@', $string, $arrLinks);

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


		$embedlyUrls = array_unique($embedlyUrls);

		//Now let's get the information from embedly
		$oembeds = $this->getMeta($embedlyUrls);
		
		//And now let's replace the Links with the Videos or pictures
		foreach ($oembeds as $key => $oembed){
			if($oembed === false) continue;

			$out = $this->formatEmbedded($oembed, $arrDecodedLinks[$key]);
			if (strlen($out)){
				$out = ($blnEncodeMediaTags) ? htmlspecialchars($out) : $out;

				$string = str_replace('<a href="'.$arrDecodedLinks[$key].'">'.$arrDecodedLinks[$key].'</a>', $out, $string);

				$string = preg_replace("~(^|\s)".preg_quote($arrDecodedLinks[$key], '~')."~", $out, $string);
			}
		}

		return $string;
	}


	//Styling for Embedded Images/Objects
	private function formatEmbedded($objEmbedly, $link){
		$out = '';
		switch($objEmbedly->type) {
				case 'photo':
					$out = '<div class="embed-content"><div class="embed-'.$objEmbedly->type.'">';
					if (isset($objEmbedly->title)) {
						$title = $objEmbedly->title;
					} else {
						$title = 'Image';
					}
					if($this->config->get('embedly_gdpr')){
						$image = $this->DownloadPreviewImage($objEmbedly->url);
					} else {
						$image = $objEmbedly->url;
					}

					$out .= '<img src="'.$image.'" alt="'.$title.'" loading="lazy"/>';

					$out .= '</div></div>';
					break;
				case 'link':
					return $out;
				case 'rich':
				case 'video':
					$out = '<div class="embed-content"><div class="embed-media">';

					if($this->config->get('embedly_gdpr')){
						//Get Thumbnail
						$strPreviewImage = "";

						if($objEmbedly->thumbnail_url){
							$strPreviewImage = sanitize($objEmbedly->thumbnail_url);
							$intPreviewWidth = (int)$objEmbedly->thumbnail_width;
							$intPreviewHeigh = (int)$objEmbedly->thumbnail_height;

							$strPreviewImage = $this->DownloadPreviewImage($objEmbedly->thumbnail_url);
						}

						if(!$strPreviewImage || $strPreviewImage == ""){
							$intPreviewWidth = 600;
							$intPreviewHeigh = 400;
							$strPreviewImage = $this->server_path.'images/global/placeholder-media.png';
						}

						if($objEmbedly->provider_url == 'https://www.youtube.com/'){
							$html = str_replace('youtube.com/em', 'youtube-nocookie.com/em', $objEmbedly->html);
						} else {
							$html = $objEmbedly->html;
						}


						$out .= '<div class="embed-consent">';
						$out .= '<div class="embed-consent-container" style=" height:'.$intPreviewHeigh.'px; width:'.$intPreviewWidth.'px">';

						$out .= '<div class="embed-consent-background" style="background:url(\''.$strPreviewImage.'\'); height:'.$intPreviewHeigh.'px; width:'.$intPreviewWidth.'px">';
						$out .= '</div>';

						$out .= '<div class="embed-consent-foreground" style="height:'.$intPreviewHeigh.'px; width:'.$intPreviewWidth.'px">';
						$out .= '<div class="embed-consent-message" onclick="show_embedded_content(this)">Load external Media of '.sanitize((string)$objEmbedly->provider_name).'</div>';
						$out .= '</div>';
						$out .= '</div>';

						$out .= '<div class="embed-consent-content" style="display:none;">';
						$out .= htmlentities($html);
						$out .= '</div>';

						$out .= '<div class="embed-consent-provider" style="display:none;">';
						$out .= sanitize((string)$objEmbedly->provider_name);
						$out .= '</div>';

						$out .= '<div class="embed-consent-link">';
						$out .= sanitize($objEmbedly->provider_name).': <a href="'.$link.'">'.$link.'</a>';
						$out .= '</div>';

						$out .= '</div>';
					} else {

						if($objEmbedly->provider_url == 'https://www.youtube.com/'){
							$out .= str_replace('youtube.com/em', 'youtube-nocookie.com/em', $objEmbedly->html);
						} else {
							$out .= $objEmbedly->html;
						}

					}
					$out .= '</div></div>';
					break;
				case 'error':
				default:
		}

		return $out;
	}


	private function DownloadPreviewImage($img){
		//If its an dynamic image...
		$url_parts = parse_url($img);

		$path_parts = pathinfo($url_parts['path']);

		if (!in_array(strtolower($path_parts['extension']), $this->arrImageExtensions)){
			return false;
		}

		//Does it already exist?
		$myFileName = $this->strImageCacheFolder.md5($img).'_'.$path_parts['filename'].'.'.$path_parts['extension'];

		if(file_exists($myFileName) && (filemtime($myFileName) > (time()-86400))){
			return $this->env->root_to_serverpath($myFileName);
		}

		// Load it...
		$tmp_name = md5(generateRandomBytes());
		$this->pfh->CheckCreateFile($this->strImageCacheFolder.$tmp_name);
		$this->pfh->putContent($this->strImageCacheFolder.$tmp_name, $this->puf->fetch($img));
		$i = getimagesize($this->strImageCacheFolder.$tmp_name);

		// Image is no image, lets remove it
		if (!$i) {
			$this->pfh->Delete($this->strImageCacheFolder.$tmp_name);
			return false;
		}

		$this->pfh->rename($this->strImageCacheFolder.$tmp_name, $myFileName);
		return $this->env->root_to_serverpath($myFileName);
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
				$arrMyService = $this->getServices($mixResult);
				
				if(isset($arrMyService['function'])){
					$function = $arrMyService['function'];
					$arrResult = $this->{$function}($mixLink);
					$this->arrCache[$mixLink] = ($arrResult) ? $arrResult : false;
					
					return $this->arrCache[$mixLink];
					
				} else {
					$strUrl = str_replace('URL', urlencode($mixLink), $arrMyService['oembed']);
	
					//Local cache
					if(isset($this->arrCache[$strUrl])) return $this->arrCache[$strUrl];
	
					//File cache
					if(file_exists($this->strImageCacheFolder.md5($strUrl).'.txt') && (filemtime($this->strImageCacheFolder.md5($strUrl).'.txt') > (time()-86400)) ){
						$strResult = file_get_contents($this->strImageCacheFolder.md5($strUrl).'.txt');
					} else {
						$strResult = register('urlfetcher')->fetch($strUrl);
					}
	
					if($strResult){
						$this->pfh->putContent($this->strImageCacheFolder.md5($strUrl).'.txt', $strResult);
	
						if($arrMyService['format'] == 'xml'){
							$arrResult = simplexml_load_string($strResult);
						} else{
							$arrResult = json_decode($strResult);
						}
	
						$this->arrCache[$strUrl] = ($arrResult) ? $arrResult : false;
	
						return $this->arrCache[$strUrl];
					} else {
						return false;
					}
				
				}
			}
		}
		return false;
	}

	private function getServices($strService=false){
		$arrServices = $this->arrServices;
		if($this->hooks->isRegistered('embedly_services')){
			$arrHooks = $this->hooks->process('embedly_services');

			if (count($arrHooks) > 0){
				foreach($arrHooks as $arrHook){
					if(is_array($arrHook)) $arrServices = array_merge($arrServices, $arrHook);
				}
			}
		}

		if($strService !== false) return $arrServices[$strService];

		return $arrServices;
	}

	public function checkLink($strLink){
		foreach($this->getServices() as $strServicename => $arrServiceDetails){
			$arrRegex = $arrServiceDetails['regex'];
			foreach($arrRegex as $strRegex){
				if(preg_match('#'.$strRegex.'#', $strLink)) return $strServicename;
			}
		}
		return false;
	}
	
	public function embedTwitch($url){
		$arrRegex = array('https?://www.twitch.tv/(?<AUTHOR>[a-zA-Z0-9_]+)/clip/(?<CLIP>[a-zA-Z0-9_]+)', 'https?://www.twitch.tv/(?!videos)(?!.*/v/)(?<CHANNEL>[a-zA-Z0-9_]+)',
'https?://www.twitch.tv/videos/(?<VIDEO>[0-9]+)', 'https?://www.twitch.tv/[a-zA-Z0-9]+/v/(?<VIDEO>[0-9]+)');
		
		$arrMatches = array();
		foreach($arrRegex as $val){
			$a = preg_match('~'.$val.'~', $url, $arrMatches);
			if($a > 0) break;
		}
		
		$src = '';
		if (!empty($arrMatches['CLIP'])) {
			$src = 'https://clips.twitch.tv/embed?clip=' . $arrMatches['CLIP'];
		}
		
		if (!empty($arrMatches['CHANNEL'])) {
			$src = 'https://player.twitch.tv/?channel=' . $arrMatches['CHANNEL'];
		}
		
		if (!empty($arrMatches['VIDEO'])) {
			$src = 'https://player.twitch.tv/?video=' . $arrMatches['VIDEO'];
		}
		
		if (!empty($src)) {
			$parent = parse_url($this->env->buildlink());
			$iframe = '<iframe src="' . $src . '&parent=' . $parent['host'] . '&autoplay=false" height="500" width="850" allowfullscreen></iframe>';	
			
			return (object)array('html' => $iframe, 'width'=> 850, 'height'=> 500, 'type'=>'video', 'provider_url' => 'https://twitch.tv', 'provider_name' => 'Twitch');			
		}
		return false;
	}
}
