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

if ( !defined('EQDKP_INC') ){
	header('HTTP/1.0 404 Not Found');exit;
}

if (!class_exists("bbcode")) {
	class bbcode extends gen_class {
		public static $shortcuts = array('puf'=>'urlfetcher');

		private $smiliepath = '';
		private $arrImageExtensions = array('jpg', 'png', 'gif', 'jpeg');
		private $strImageCacheFolder = '';
		private $strImageThumbFolder = '';

		public function __construct(){
			$this->strImageCacheFolder = $this->pfh->FolderPath('images', 'eqdkp');
			$this->strImageThumbFolder = $this->pfh->FolderPath('images/thumb', 'eqdkp');
			$this->smiliepath = $this->server_path.'images/smilies/';
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
				':-)',
				':D',
				':o',
				':p',
				':P',
				':(',
				':-(',
				';)',
				';-)'
			);

			$out = array(
				' <img alt=":)" src="'.$this->smiliepath.'/smile.svg" class="smilies" />',
				' <img alt=":)" src="'.$this->smiliepath.'/smile.svg" class="smilies" />',
				' <img alt=":D" src="'.$this->smiliepath.'/happy.svg" class="smilies" />',
				' <img alt=":o" src="'.$this->smiliepath.'/surprised.svg" class="smilies" />',
				' <img alt=":p" src="'.$this->smiliepath.'/tongue.svg" class="smilies" />',
				' <img alt=":p" src="'.$this->smiliepath.'/tongue.svg" class="smilies" />',
				' <img alt=":(" src="'.$this->smiliepath.'/unhappy.svg" class="smilies" />',
				' <img alt=":(" src="'.$this->smiliepath.'/unhappy.svg" class="smilies" />',
				' <img alt=";)" src="'.$this->smiliepath.'/wink.svg" class="smilies" />',
				' <img alt=";)" src="'.$this->smiliepath.'/wink.svg" class="smilies" />'
			);

			$text = preg_replace('/<img(.*?)alt=\"(.*?)\" src=\"(.*?)\" class=\"smilies\" \/>/Ui' , '$2' , $text);

			foreach($in as $key => $val){
				$text = preg_replace('/(^'.preg_quote($val, '/').'|\s'.preg_quote($val, '/').')/', $out[$key], $text);
			}

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
				'/\[\*\]\s?(.*?)(\s|&#10;)/msi',
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
				return '<div style="color: '.$arrMatches[1].';display:inline;">'.$arrMatches[2].'</div>';
			}
			return '';
		}

		function sanatizeFontsize($arrMatches){
			return '<div style="font-size: '.intval($arrMatches[1]).'px;display:inline;">'.$arrMatches[2].'</div>';
		}

		function sanatizeOrderedList($arrMatches){
			return '<ol start="'.intval($arrMatches[1]).'">'.$arrMatches[2].'</ol>';
		}

		function sanatizeURLs($arrURL){
			$text = str_replace(array('"', "'"), array("",""), $arrURL[1]);
			if (!filter_var($text, FILTER_VALIDATE_URL)) return '';

			return '<a href="'.filter_var($text, FILTER_SANITIZE_URL).'" rel="nofollow">'.$arrURL[2].'</a>';
		}

		// Download the Image to eqdkp
		function DownloadImage($img){
				//If its an dynamic image...
				$path_parts = pathinfo($img);
				if (!in_array(strtolower($path_parts['extension']), $this->arrImageExtensions)){
					return false;
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
					$strImageURL = $this->server_path.$strImage;
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

						return '<a href="'.str_replace($this->root_path, $this->server_path, $strImageURL).'" class="lightbox"><img src="'.str_replace($this->root_path, $this->server_path, $strThumbImage).'" alt="image" /></a>';

					} else {
						//No Thumbnail required, return image
						return '<img src="'.str_replace($this->root_path, $this->server_path, $strImageURL).'" alt="image" />';
					}
				}

			}

			//Show error message becaue image is not available
			$langBits = ($this->user->check_auth('a_news_', false)) ? $this->user->lang('images_not_available_admin') : $this->user->lang('images_not_available');
			return '<div class="infobox infobox-large infobox-red clearfix">
								<i class="fa fa-meh-o fa-4x pull-left"></i> '.$langBits.'
							</div>';
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

					case 'article_url':
						$strPath = $this->controller_path.$this->pdh->get('articles', 'path', array($elements[1]));
						$arrCache[$strTag] = ($strPath) ? $strPath : '';
						break;

					case 'article_title':
						$strTitle = $this->pdh->get('articles', 'title', array((int)$elements[1]));
						$arrCache[$strTag] = ($strTitle) ? $strTitle : '';
						break;

					case 'article_url_plain':
						$strPath = $this->controller_path_plain.$this->pdh->get('articles', 'path', array($elements[1]));
						$arrCache[$strTag] = ($strPath) ? $strPath : '';
						break;

					case 'category_url':
						$strPath = $this->controller_path.$this->pdh->get('article_categories', 'path', array($elements[1]));
						$arrCache[$strTag] = ($strPath) ? $strPath : '';
						break;

					case 'category_title':
						$strTitle = $this->pdh->get('article_categories', 'name', array((int)$elements[1]));
						$arrCache[$strTag] = ($strTitle) ? $strTitle : '';
						break;

					case 'category_url_plain':
						$strPath = $this->controller_path_plain.$this->pdh->get('article_categories', 'path', array($elements[1]));
						$arrCache[$strTag] = ($strPath) ? $strPath : '';
						break;

					case 'server':
						switch($elements[1]){
							case 'name':
								$arrCache[$strTag] = $this->config->get('servername');
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

							case 'id': $arrCache[$strTag] = $this->user->id;
								break;
								
							default:
								if (is_numeric($elements[1])){
									$userID = intval($elements[1]);
								} else $userID = $this->pdh->get('user', 'userid', array($elements[1]));
								if ($userID){
									$arrCache[$strTag] = $this->pdh->geth('user', 'avatarimglink', array($userID)).' '.$this->pdh->geth('user', 'name', array($userID, '', '', true));
								}
						}
						break;

					case 'guild':
						switch($elements[1]){
							case 'name':
								$arrCache[$strTag] = $this->config->get('guildtag');
								break;
						}
						break;

					case 'char':	if (is_numeric($elements[1])){
										$member_id = intval($elements[1]);
									} else $member_id = $this->pdh->get('member', 'id', array($elements[1]));
									if ($member_id){
										$arrCache[$strTag] = $this->pdh->get('member', 'memberlink_decorated', array($member_id, $this->routing->simpleBuild('character'), '', true));
									}
						break;

					case 'itemid': infotooltip_js();
									$item = "";
									$game_id = strip_tags($elements[1]);
									$str =  infotooltip($item, $game_id);
									$arrCache[$strTag] = $str;
						break;

					case 'item':	infotooltip_js();
									$item = strip_tags($elements[1]);
									if(strpos('id:', $item) === 0){
										$game_id = substr($item, 3);
										$str =  infotooltip("", $game_id);
										$arrCache[$strTag] = $str;
									} else {
										$game_id = (is_numeric($item)) ? intval($item) : 0;
										$str =  infotooltip($item, $game_id);
										$arrCache[$strTag] = $str;
									}
						break;

					case 'event':	$intEventID = intval($elements[1]);
									include_once($this->root_path.'core/article.class.php');
									$objArticleHelper = registry::register('article');
									$str = $objArticleHelper->buildCalendarevent($intEventID);
									$arrCache[$strTag] = $str;
						break;


					case 'iflang':
						if ($elements[1] != '' && $elements[1] != $this->user->lang_name)
						{
							for (; $rit<$_cnt; $rit+=2)
							{
								if ($tags[$rit+1] == 'iflang' || $tags[$rit+1] == 'iflang::' . $this->user->lang_name)
								{
									break;
								}
							}
						}

						unset($arrCache[$strTag]);
						break;
					case 'ifnlang':
						if ($elements[1] != '')
						{
							$langs = explode(',', $elements[1]);
							if (in_array($this->user->lang_name, $langs))
							{
								for (; $rit<$_cnt; $rit+=2)
								{
									if ($tags[$rit+1] == 'ifnlang')
									{
										break;
									}
								}
							}
						}
						unset($arrCache[$strTag]);
						break;

					case 'env':
						switch($elements[1]){
							case 'controller_path':
								$arrCache[$strTag] = $this->controller_path;
								break;

							case 'controller_path_plain':
								$arrCache[$strTag] = $this->controller_path_plain;
								break;
							case 'server_path':
								$arrCache[$strTag] = $this->server_path;
								break;
						}
						break;


					default:	if($this->hooks->isRegistered('parse_shorttags')){
									$strText = $this->hooks->process('parse_shorttags', array('tag'=> $elements[0], 'complete_tag' => $strTag, 'param' => $elements[1],'elements' => $elements), true);
									if($strText !== false){
										$arrCache[$strTag] = $strText;
									}
								}
				}

				$strBuffer .= (isset($arrCache[$strTag])) ? $arrCache[$strTag] : "";
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
				$text = preg_replace('/{{itemid::([^}]+)}}/', '$1', $text);
				$text = preg_replace('/{{char::([^}]+)}}/', '$1', $text);
			}
			$text = preg_replace('/{{([^}]+)}}/', '', $text);
			return $text;
		}

		//Removed all objects and embed-Tags
		public function remove_embeddedMedia($text){
			$text = preg_replace('#<script(.*?)>(.*?)</script>#mis', '', $text);
			$text = preg_replace('#<style(.*?)>(.*?)</style>#mis', '', $text);
			$text = preg_replace('#<iframe(.*?)>(.*?)</iframe>#mis', '', $text);
			$text = preg_replace('#<embed(.*?)>(.*?)</embed>#mis', '', $text);
			$text = preg_replace('#<object(.*?)>(.*?)</object>#mis', '', $text);
			return $text;
		}

		public function remove_bbcode($text){
			$text = trim($text);

			// BBCode to find...
			$in = array(
					'/\[code\](.*?)\[\/code\]/ms',
					'/\[b\](.*?)\[\/b\]/msi',
					'/\[i\](.*?)\[\/i\]/msi',
					'/\[u\](.*?)\[\/u\]/msi',
					'/\[quote](.*?)\[\/quote\]/msi',
					'/\[center](.*?)\[\/center\]/msi',
					'/\[left](.*?)\[\/left\]/msi',
					'/\[right](.*?)\[\/right\]/msi',
					'/\[list\](.*?)\[\/list\]/msi',
					'/\[\*\]\s?(.*?)\[br\]/msi',
					'/\[br\]/msi',
					'/&#10;/msi',
					'/\[img\](.*?)\[\/img\]/msi',
					'/\[url\="?(.*?)"?\](.*?)\[\/url\]/msi',
					'/\[color\="?(.*?)"?\](.*?)\[\/color\]/msi',
					'/\[size\="?(.*?)"?\](.*?)\[\/size\]/msi',
					'/\[list\=(.*?)\](.*?)\[\/list\]/msi'
			);

			$out = array(
					'\1',
					'\1',
					'\1',
					'\1',
					'\1',
					'\1',
					'\1',
					'\1',
					'\1',
					'\1',
					'',
					'',
					'\1',
					'\2',
					'\2',
					'\2',

			);

			$text = preg_replace($in, $out, $text);


			return $text;
		}
		
		/**
		 * Converts Links to URL BBCode and shortens the length of the Links
		 * 
		 * @param string $strBBCode
		 * @return string BBCode
		 */
		public function autolink($strBBCode) {
			$str = ' ' . $strBBCode;
			$str = preg_replace_callback(
					"/\[url\s*+=\s*+([^]\s]++)]([^[]++)\[\/url]/im",
					function ($matches) {
						$url = strlen($matches[1]) ? $matches[1] : $matches[2];
						$text = $matches[2];
						if(mb_strlen($text) > 45){
							$text = mb_substr($text, 0, 20) .'...' . mb_substr($text, -20);
						}
						return '[url='.$url.']'.$text.'[/url]';
					},
					$str
					);
			
			$str = preg_replace_callback(
					"/\[url]([^[]++)\[\/url]/im",
					function ($matches) {
						$url = $matches[1];
						$text = $url;
						if(mb_strlen($text) > 45){
							$text = mb_substr($text, 0, 20) .'...' . mb_substr($text, -20);
						}
						return '[url='.$url.']'.$text.'[/url]';
					},
					$str
					);
			
			//Normal
			$str = preg_replace_callback(
					"/(^|[^=\]\"])((((http|https|ftp):\/\/|www.)\S++))/im",
					function ($matches) {
						$url = $matches[2];
						$text = $url;
						if(mb_strlen($text) > 45){
							$text = mb_substr($text, 0, 20) .'...' . mb_substr($text, -20);
						}
						return $matches[1].'[url='.$url.']'.$text.'[/url]';
					},
					$str
					);
			
			$str = substr($str, 1);
			$str = preg_replace('`url=\"www`','url="http://www',$str);
			$str = preg_replace('`url=www`','url=http://www',$str);
			
			// fügt http:// hinzu, wenn nicht vorhanden
			return trim($str);
		}

	}
}
