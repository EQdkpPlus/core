<?php 
 /*
 * Project:		eqdkpPLUS Libraries: embedly
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2008
 * Date:		$Date$
 * -----------------------------------------------------------------------
 * @author		$Author$
 * @copyright	2006-2011 EQdkp-Plus Developer Team
 * @link		http://eqdkp-plus.com
 * @package		libraries:bbCode
 * @version		$Rev$
 * 
 * $Id$
 */
 
class embedly extends gen_class {

	public function __construct(){
		include_once($this->root_path.'libraries/embedly/embedly.php');
		$this->embedly = new libEmbedly();
	}
	
	//Parse one single Link
	public function parseLink($link){
		if (strlen($link) == 0) return '';
		$oembed = $this->getLinkDetails($link);
		
		
		if ($oembed && is_array($oembed) && count($oembed)){
			$out = $this->formatEmbedded($oembed[0]);
		}
		
		return $out;
	}
	
	//Get all embed.ly Information for an single Link, like Thumbnail, Size, ...
	public function getLinkDetails($link){
		if (strlen($link) == 0) return false;

		$oembed = $this->embedly->oembed(array('url' => $link, 'wmode' => 'transparent'));
		if ($oembed->type == "error"){
			return false;
		}
		return $oembed;
	}
	
	//Parse an String for Hyperlinks and replace Videos and Images
	public function parseString($string){
		if (strlen($string) == 0) return '';
		
		$string = html_entity_decode($string);
		$embedlyUrls = array();
		//First, get the links
		$arrLinks = array();
		$intLinks = preg_match_all('@((("|:)?)https?://([-\w\.]+)+(:\d+)?(/([\w/_\.]*(\?\S+)?)?)?)@', $string, $arrLinks);
		if ($intLinks){
			foreach ($arrLinks[0] as $link){
				if (substr($link, 0, 1) != '"' && substr($link, 0, 1) != ':') $embedlyUrls[] =  strip_tags($link);
			}	
		}

		//Now let's get the information from embedly
		$oembeds = $this->embedly->oembed(array('urls' => $embedlyUrls, 'wmode' => 'transparent')); //, 'maxwidth' => 200));
		
		//And now let's replace the Links with the Videos or pictures
		foreach ($oembeds as $key => $oembed){
			$out = $this->formatEmbedded($oembed);

			if (strlen($out)){
				$string = str_replace($arrLinks[0][$key], $out, $string);
			}
		}
		return htmlspecialchars($string);	
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