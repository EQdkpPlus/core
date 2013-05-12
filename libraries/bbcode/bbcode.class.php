<?php
 /*
 * Project:		eqdkpPLUS Libraries: bbCode
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

if ( !defined('EQDKP_INC') ){
	header('HTTP/1.0 404 Not Found');exit;
}

if (!class_exists("bbcode")) {
	class bbcode extends gen_class {
		public static $shortcuts = array('core', 'user', 'pdh', 'pm', 'config', 'pfh', 'puf'=>'urlfetcher', 'hooks');

		private $smiliepath = '';
		private $arrImageExtensions = array('jpg', 'png', 'gif', 'jpeg');
		private $strImageCacheFolder = '';
		private $strImageThumbFolder = '';

		public function __construct(){
			$this->strImageCacheFolder = $this->pfh->FolderPath('images', 'eqdkp');
			$this->strImageThumbFolder = $this->pfh->FolderPath('images/thumb', 'eqdkp');
		}


		public function SetSmiliePath($path){
			$this->smiliepath = $path;
		}

		private function escape($s) {
			global $text;
			$text = strip_tags($text);
			return '<pre><code>'.htmlspecialchars($s[1]).'</code></pre>';
		}

		// clean some tags to remain strict
		// not very elegant, but it works. No time to do better ;)
		private function removeBr($s) {
			return str_replace("<br />", "", $s[0]);
		}

		public function MyEmoticons($text){
			// Smileys to find...
			$in = array(
				':)',
				':D',
				':o',
				':p',
				':(',
				';)'
			);

			$out = array(
				'<img alt=":)" src="'.$this->smiliepath.'/happy.png" />',
				'<img alt=":D" src="'.$this->smiliepath.'/smile.png" />',
				'<img alt=":o" src="'.$this->smiliepath.'/surprised.png" />',
				'<img alt=":p" src="'.$this->smiliepath.'/tongue.png" />',
				'<img alt=":(" src="'.$this->smiliepath.'/unhappy.png" />',
				'<img alt=";)" src="'.$this->smiliepath.'/wink.png" />'
			);

			$text = preg_replace('/\<img(.*?)alt=\"(\W.*?)\"(.*?)\>/si' , '$2' , $text);
			$text = str_replace($in, $out, $text);
			return $text;
		}

		public function toHTML($text, $skip_lbox=false, $bbrss=false){
			$text = trim($text);
			$text = preg_replace_callback('/\[code\](.*?)\[\/code\]/ms', array($this,"escape"), $text);

			// BBCode to find...
			$in = array(
				'/&(?![a-z0-9#]*;)/msi', //no bbcode but needed for valid html (& -> &amp;)
				'/\[b\](.*?)\[\/b\]/msi',
				'/\[i\](.*?)\[\/i\]/msi',
				'/\[u\](.*?)\[\/u\]/msi',
				'/\[quote](.*?)\[\/quote\]/msi',
				'/\[center](.*?)\[\/center\]/msi',
				'/\[left](.*?)\[\/left\]/msi',
				'/\[right](.*?)\[\/right\]/msi',
				'/\[list\](.*?)\[\/list\]/msi',
				'/\[\*\]\s?(.*?)\n/msi',
				'/\[br\]/msi',
				'/&#10;/msi'
			);

			// And replace them by...
			if($bbrss){
				$out = array(
					'&amp;',
					'<strong>\1</strong>',
					'<em>\1</em>',
					'<u>\1</u>',
					'\1',
					'\1',
					'\1',
					'\1',
					'<ul>\1</ul>',
					'<li>\1</li>',
					'<br/>',
					'<br/>',
				);
			}else{
				$out = array(
					'&amp;',
					'<strong>\1</strong>',
					'<em>\1</em>',
					'<u>\1</u>',
					'<blockquote>\1</blockquote>',
					'<div style="text-align:center;">\1</div>',
					'<div style="text-align:left;">\1</div>',
					'<div style="text-align:right;">\1</div>',
					'<ul>\1</ul>',
					'<li>\1</li>',
					'<br/>',
					'<br/>',
				);
			}
			$text = preg_replace($in, $out, $text);

			if($bbrss){
				$text = preg_replace('/\[img\](.*?)\[\/img\]/msi', '\1', $text);
			}else{
				$text = preg_replace_callback('/\[img\](.*?)\[\/img\]/msi', array($this,"sanatizeIMG"), $text);
			}
			//Replace urls
			$text = preg_replace_callback('/\[url\="?(.*?)"?\](.*?)\[\/url\]/msi', array($this,"sanatizeURLs"), $text);
			//Replace font color
			$text = preg_replace_callback('/\[color\="?(.*?)"?\](.*?)\[\/color\]/msi', array($this,"sanatizeFontcolor"), $text);
			//Replace font size
			$text = preg_replace_callback('/\[size\="?(.*?)"?\](.*?)\[\/size\]/msi', array($this,"sanatizeFontsize"), $text);
			//Replace ordered list
			$text = preg_replace_callback('/\[list\=(.*?)\](.*?)\[\/list\]/msi', array($this,"sanatizeOrderedList"), $text);

			// paragraphs
			$text = str_replace("\r", "", $text);
			$text = nl2br($text);

			$text = preg_replace_callback('/<pre>(.*?)<\/pre>/msi', array($this,"removeBr"), $text);
			$text = preg_replace('/<p><pre>(.*?)<\/pre><\/p>/msi', "<pre>\\1</pre>", $text);

			$text = preg_replace_callback('/<ul>(.*?)<\/ul>/msi', array($this,"removeBr"), $text);
			$text = preg_replace('/<p><ul>(.*?)<\/ul><\/p>/msi', "<ul>\\1</ul>", $text);

			//Do Hooks - Heavy BB-Code Replacements
			if(is_object($this->pm)){
				$arrHooks = $this->hooks->process('bbcodes', array('text' => $text, 'state'=>'toHTML'), true);
				$text = $arrHooks['text'];
			}

			return $text;
		}

		function sanatizeFontcolor($arrMatches){
			if (preg_match('/#[a-zA-Z0-9]{3,6}/', $arrMatches[1])){
				return '<div style="color: '.$arrMatches[1].';">'.$arrMatches[2].'</div>';
			}
			return '';
		}
		
		function sanatizeFontsize($arrMatches){
			return '<div style="font-size: '.intval($arrMatches[1]).'px">'.$arrMatches[2].'</div>';
		}
		
		function sanatizeOrderedList($arrMatches){
			return '<ol start="'.intval($arrMatches[1]).'">'.$arrMatches[2].'</ol>';
		}
		
		function sanatizeURLs($arrURL){d($arrURL);
			$text = str_replace(array('"', "'"), array("",""), $arrURL[1]);
			if (!filter_var($text, FILTER_VALIDATE_URL)) return '';
			
			return '<a href="'.filter_var($text, FILTER_SANITIZE_URL).'">'.$arrURL[2].'</a>';
		}

		// Download the Image to eqdkp
		function DownloadImage($img){
				//If its an dynamic image...
				$path_parts = pathinfo($img);
				if (!in_array(strtolower($path_parts['extension']), $this->arrImageExtensions)){
					return false;
				}

				// Load it...
				$tmp_name = md5(rand());
				$this->pfh->CheckCreateFile($this->strImageCacheFolder.$tmp_name);
				$this->pfh->putContent($this->strImageCacheFolder.$tmp_name, $this->puf->fetch($img));
				$i = getimagesize($this->strImageCacheFolder.$tmp_name);

				// Image is no image, lets remove it
				if (!$i) {
					$this->pfh->Delete($this->strImageCacheFolder.$tmp_name);
					return false;
				}

				$myFileName = $this->strImageCacheFolder.md5($img).'_'.$path_parts['filename'].'.'.$path_parts['extension'];
				$this->pfh->rename($this->strImageCacheFolder.$tmp_name, $myFileName);
				return $myFileName;
		}

		// Clean Up Image Tags to prevent CSRF and create Thumbnails
		function sanatizeIMG($arrImage){

			$strImage = $arrImage[1];
			$path_parts = pathinfo($strImage);

			//If extension is accepted
			if (in_array(strtolower($path_parts['extension']), $this->arrImageExtensions)){

				$strDataFolderAbsolute = $this->pfh->FileLink('', '', 'absolute');
				$strDataFolderRelative = $this->pfh->FileLink('', '');

				$strImageURL = '';

				//Is EQdkp Images?
				if (strpos($strImage, $strDataFolderAbsolute) === 0){
					$strImageURL = $strImage;
				} elseif(strpos($strImage, $strDataFolderRelative) === 0){
					$strImageURL = $this->root_path.$strImage;
				}

				//Its not an EQdkp Image, its an external image
				if ($strImageURL == ''){
					//External Image already downloaded?
					$strFilename = md5($strImage).'_'.$path_parts['filename'].'.'.$path_parts['extension'];
					if (is_file($this->strImageCacheFolder.$strFilename)){
						$strImageURL = $this->strImageCacheFolder.$strFilename;
					} else {
						//Download external image
						$strDownloadedImage = $this->DownloadImage($strImage);
						if (strlen($strDownloadedImage)){
							//Download successful
							$strImageURL = $strDownloadedImage;
						}
					}
				}

				//Now lets check image width
				if ($strImageURL != ''){
					$arrImageSize = getimagesize($strImageURL);
					$intImageWidth = $arrImageSize[0];
					if ($intImageWidth > (int)$this->config->get('thumbnail_defaultsize')){
						//Check if Thumb is available
						$strThumbFilename = 'thumb'.(int)$this->config->get('thumbnail_defaultsize').'_'.md5($strImage).'_'.$path_parts['filename'].'.'.$path_parts['extension'];
						if (is_file($this->strImageThumbFolder.$strThumbFilename)){
							$strThumbImage = $this->strImageThumbFolder.$strThumbFilename;
						} else {
							//Create thumbnail
							$this->pfh->thumbnail($strImageURL, $this->strImageThumbFolder, $strThumbFilename, (int)$this->config->get('thumbnail_defaultsize'));
							$strThumbImage = $this->strImageThumbFolder.$strThumbFilename;
						}

						return '<a href="'.$strImageURL.'" rel="lightbox"><img src="'.$strThumbImage.'" alt="image" /></a>';

					} else {
						//No Thumbnail required, return image
						return '<img src="'.$strImageURL.'" alt="image" />';
					}
				}

			}

			//Show error message becaue image is not available
			$langBits = ($this->user->check_auth('a_news_', false)) ? $this->user->lang('images_not_available_admin') : $this->user->lang('images_not_available');
			return '<div class="errorbox roundbox">
						<div class="icon_brokenimage">'.$langBits.'</div>
					</div>';

			return '<br/><table class="errortable" width="100%"><tr><td width="120px" align="center"><img src="images/brokenimg.png" /></td><td>'.$langBits.'</td></tr></table>';
		}

		//Parse shorttags
		public function parse_shorttags($text, $filter = array()){
			$tags = preg_split('/{{([^}]+)}}/', $text, -1, PREG_SPLIT_DELIM_CAPTURE);

			$strBuffer = '';

			for($rit = 0; $rit<(count($tags)); $rit=$rit+2) {
				$strBuffer .= $tags[$rit];
				if (!isset($tags[$rit+1])) break;
				$strTag = $tags[$rit+1];
				$arrCache = array();

				// Load value from cache array
				if (isset($arrCache[$strTag])) {
					$strBuffer .= $arrCache[$strTag];
					continue;
				}
				$elements = explode('::', $strTag);
				//Check Filter
				if (is_array($filter) && count($filter) > 0 && !in_array(strtolower($elements[0]), $filter)){
					continue;
				}

				// Replace tag
				switch (strtolower($elements[0])) {
					// Date
					case 'page':
						$arrCache[$strTag] = ($this->pdh->get('pages', 'page_exists', array($elements[1]))) ? '<a href="'.$this->pdh->get('pages', 'url', array($elements[1])).'">'.$this->pdh->get('pages', 'title', array($elements[1])).'</a>': '';
						break;

					case 'page_url':
						$arrCache[$strTag] = ($this->pdh->get('pages', 'page_exists', array($elements[1]))) ? $this->pdh->get('pages', 'url', array($elements[1])) : '';
						break;

					case 'server':
						switch($elements[1]){
							case 'name':
								$arrCache[$strTag] = $this->config->get('uc_servername');
								break;

							case 'location':
								$arrCache[$strTag] = $this->config->get('uc_server_loc');
								break;
						}
						break;

					case 'user':
						switch($elements[1]){
							case 'name':
								$this->username = (!$this->user->is_signedin()) ? $this->user->lang('guest') : $this->pdh->get('user', 'name', array($this->user->data['user_id']));
								$arrCache[$strTag] = $this->username;
								break;
						}
						break;

					case 'guild':
						switch($elements[1]){
							case 'name':
								$arrCache[$strTag] = $this->config->get('guildtag');
								break;
						}
						break;

					case 'char':	$member_id = $this->pdh->get('member', 'id', array($elements[1]));
									if ($member_id){
										$arrCache[$strTag] = $this->pdh->get('member', 'html_memberlink', array($member_id, 'viewcharacter.php', '', false, false, true));
									}
						break;

				}
				//Infotooltips
				if (strpos('item', strtolower($elements[0])) === 0){
					infotooltip_js();
					$item = strip_tags($elements[1]);

					$game_id = (is_numeric($item)) ? intval($item) : 0;

					$str =  infotooltip($item, $game_id);

					$arrCache[$strTag] = $str;
				}

				$strBuffer .= $arrCache[$strTag];
			}

			return $strBuffer;
		}

		//Replace Shorttags before saving in Database - usefull for Shorttags that need external connections like embed.ly
		public function replace_shorttags($text){
			$tags = preg_split('/{{([^}]+)}}/', $text, -1, PREG_SPLIT_DELIM_CAPTURE);

			$strBuffer = '';

			for($rit = 0; $rit<(count($tags)); $rit=$rit+2) {
				$strBuffer .= $tags[$rit];
				if (!isset($tags[$rit+1])) break;
				$strTag = $tags[$rit+1];
				$arrCache = array();

				// Load value from cache array
				if (isset($arrCache[$strTag])) {
					$strBuffer .= $arrCache[$strTag];
					continue;
				}
				$elements = explode('::', $strTag);
				// Replace tag
				$embedly = registry::register('embedly');
				switch (strtolower($elements[0])) {

					case 'embed':
					case 'video':
					case 'media':
						$arrCache[$strTag] = $embedly->parseLink($elements[1]);
						break;

					default:
						if (strlen($strTag)) $arrCache[$strTag] = '{{'.$strTag.'}}';
						break;
				}
				$strBuffer .= $arrCache[$strTag];
			}

			return $strBuffer;
		}

		//Removes all Shorttags
		public function remove_shorttags($text, $blnDisplayItemnames = false){
			if ($blnDisplayItemnames){
				$text = preg_replace('/{{item::([^}]+)}}/', '$1', $text);
				$text = preg_replace('/{{char::([^}]+)}}/', '$1', $text);
			}
			$text = preg_replace('/{{([^}]+)}}/', '', $text);
			return $text;
		}

		//Removed all objects and embed-Tags
		public function remove_embeddedMedia($text){
			$text = preg_replace('{<object[^>]*>(.*?)</object>}', '', $text);
			$text = preg_replace('{<embed[^>]*>(.*?)</embed>}', '', $text);

			return $text;
		}

	}
}
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_bbcode', bbcode::$shortcuts);
?>