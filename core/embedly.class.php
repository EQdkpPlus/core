<?php
/*	Project:	EQdkp-Plus
 *	Package:	EQdkp-plus
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
 
class embedly extends gen_class {

	public function __construct(){
		include_once($this->root_path.'libraries/embedly/embedly.php');
		$this->embedly = new libEmbedly(array('key' => $this->config->get('embedly_key')));
	}
	
	//Parse one single Link
	public function parseLink($link){
		if (strlen($link) == 0 || (!$this->config->get('enable_embedly'))) return '';
		$oembed = $this->getLinkDetails($link);
		
		
		if ($oembed && is_array($oembed) && count($oembed)){
			$out = $this->formatEmbedded($oembed[0]);
		}
		
		return $out;
	}
	
	//Get all embed.ly Information for an single Link, like Thumbnail, Size, ...
	public function getLinkDetails($link){
		if (strlen($link) == 0 || (!$this->config->get('enable_embedly'))) return false;

		$oembed = $this->embedly->oembed(array('url' => $link, 'wmode' => 'transparent'));
		if ($oembed->type == "error"){
			return false;
		}
		return $oembed;
	}
	
	//Parse an String for Hyperlinks and replace Videos and Images
	public function parseString($string, $maxwidth=false, $blnEncodeMediaTags=false){
		if (strlen($string) == 0) return '';
		if(!$this->config->get('enable_embedly')) return $string;
		

		$embedlyUrls = array();
		//First, get the links
		$arrLinks = array();
		$intLinks = preg_match_all('@((("|:)?)https?://([-\w\.]+)+(:\d+)?(/([\w/_\.]*(\?\S+)?)?)?)@', $string, $arrLinks);
		
		$arrDecodedLinks = array();
		if ($intLinks){
			$key = 0;
			foreach ($arrLinks[0] as $link){
				$orig_link = $link;
				$link = html_entity_decode($link);
				if (substr($link, 0, 1) != '"' && substr($link, 0, 1) != ':') {
					$embedlyUrls[$key] = strip_tags($link);
					$arrDecodedLinks[$key] = $orig_link;
					$key++;
				}
			}	
		}

		//Now let's get the information from embedly
		$config = array('urls' => $embedlyUrls, 'wmode' => 'transparent');
		if ($maxwidth) $config['maxwidth'] = intval($maxwidth);
		$oembeds = $this->embedly->oembed($config);
		
		//And now let's replace the Links with the Videos or pictures
		foreach ($oembeds as $key => $oembed){
			$out = $this->formatEmbedded($oembed);
			if (strlen($out)){
				$out = ($blnEncodeMediaTags) ? htmlspecialchars($out) : $out;
				$string = str_replace($arrDecodedLinks[$key], $out, $string);
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
	
}

?>