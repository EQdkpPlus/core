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
		public static $shortcuts = array('core', 'user', 'pdh', 'pm', 'config');

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
				'/\[email\](.*?)\[\/email\]/msi',
				'/\[img\](.*?)\[\/img\]/msi',
				'/\[url\="?(.*?)"?\](.*?)\[\/url\]/msi',
				'/\[size\="?(.*?)"?\](.*?)\[\/size\]/msi',
				'/\[color\="?(.*?)"?\](.*?)\[\/color\]/msi',
				'/\[bluepost\="?(.*?)"?\](.*?)\[\/bluepost\]/msi',
				'/\[quote](.*?)\[\/quote\]/msi',
				'/\[center](.*?)\[\/center\]/msi',
				'/\[left](.*?)\[\/left\]/msi',
				'/\[right](.*?)\[\/right\]/msi',
				'/\[list\=(.*?)\](.*?)\[\/list\]/msi',
				'/\[list\](.*?)\[\/list\]/msi',
				'/\[\*\]\s?(.*?)\n/msi',
				'/\[br\]/msi',
			);
/*

$myoutt =  '<div class="image_resized" onmouseover="$(\'#imgresize_'.$this->img_id.'\').show()" onmouseout="$(\'#imgresize_'.$this->img_id.'\').hide()" style="width: '.$resize_value.'px;">
									<div id="imgresize_'.$this->img_id.'" class="markImageResized">
										<a href="'.$img_full.'" '.$lb_enabled.'>
											<img src="images/global/zoom.png" alt="Resized"/>
										</a>
									</div>
									<a href="'.$img_full.'" '.$lb_enabled.'>
										<img src="'.$img_thumb.'" alt="'.$this->user->lang('images_userposted').'" />
									</a>
								</div>';

*/
			// And replace them by...
			if($bbrss){
				$out = array(
					'&amp;',
					'<strong>\1</strong>',
					'<em>\1</em>',
					'<u>\1</u>',
					'<a href="mailto:\1">\1</a>',
					'<img src="\1" alt="Image" />',
					'<a href="\1">\2</a>',
					'\2',
					'\2',
					'\2',
					'\1',
					'\1',
					'\1',
					'\1',
					'<ol start="\1">\2</ol>',
					'<ul>\1</ul>',
					'<li>\1</li>',
					'<br/>',
				);
			}else{
				$out = array(
					'&amp;',
					'<strong>\1</strong>',
					'<em>\1</em>',
					'<u>\1</u>',
					'<a href="mailto:\1">\1</a>',
					'<img src="\1" alt="Image" />',
					'<a href="\1">\2</a>',
					'<span style="font-size:\1%">\2</span>',
					'<span style="color:\1">\2</span>',
					'<style type="text/css">.bluepost strong {color:#ffffff;}</style><div class="ui-corner-top ui-corner-bottom ui-corner-right ui-corner-left" style="background-color:#333333; color:#1499ff"><div style="padding:5px;"><div style="border-bottom:1px solid #1d1d1e; padding-bottom:3px;"><img src="'.$this->root_path.'images/logos/blizz.gif"> <b>'.$this->user->lang('quote_of').' \1:</b></div><div style="padding-top:3px;" class="bluepost">\2</div></div></div>',
					'<blockquote>\1</blockquote>',
					'<div align="center">\1</div>',
					'<div align="left">\1</div>',
					'<div align="right">\1</div>',
					'<ol start="\1">\2</ol>',
					'<ul>\1</ul>',
					'<li>\1</li>',
					'<br/>',
				);
			}
			$text = preg_replace($in, $out, $text);
/*
			// Infotooltip Parsing
			if ($this->config->get('infotooltip_use') == 1){
				include_once($this->root_path.'infotooltip/infotooltip.class.php');
				infotooltip_js();
				$text = itt_replace_bbcode($text, $this->user->lang('XML_LANG'));
			}
*/
			// paragraphs
			$text = str_replace("\r", "", $text);
			$text = nl2br($text);

			$text = preg_replace_callback('/<pre>(.*?)<\/pre>/msi', array($this,"removeBr"), $text);
			$text = preg_replace('/<p><pre>(.*?)<\/pre><\/p>/msi', "<pre>\\1</pre>", $text);

			$text = preg_replace_callback('/<ul>(.*?)<\/ul>/msi', array($this,"removeBr"), $text);
			$text = preg_replace('/<p><ul>(.*?)<\/ul><\/p>/msi', "<ul>\\1</ul>", $text);

			//Do Hooks - Heavy BB-Code Replacements
			if(is_object($this->pm)){
				$text = $this->pm->do_hooks('bbcodes', array('text' => $text), 'text');
			}

			return $text;
		}

		//Parse shorttags
		public function parse_shorttags($text, $filter = array()){
			$tags = preg_split('/{{([^}]+)}}/', $text, -1, PREG_SPLIT_DELIM_CAPTURE);

			$strBuffer = '';
/*
			// Infotooltip Parsing
			if ($this->config->get('infotooltip_use') == 1){
				include_once($this->root_path.'infotooltip/infotooltip.class.php');
				infotooltip_js();
			}
*/
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

				}
				//Infotooltips
				if (strpos('item', strtolower($elements[0])) === 0){
					$game_id = (is_numeric($elements[1])) ? intval($elements[1]) : 0;
					$data = array(
								'name' => $elements[1],
								'game_id' => $game_id,
								'lang' => '',
								'direct' => 0,
								'onlyicon' => false,
								'char_name' => '',
								'server' => '',
								'slotid' => 0,
							);
					$pre_options = explode(' ', $elements[0]);
					foreach($pre_options as $option) {
						if(strpos($option, '=') === false) continue;
						list($key, $val) = explode('=', $option);
						//check for invalid chars
						if(preg_match('#[^a-zA0-9_]#', $key) OR !isset($data[$key])) continue;
						$data[$key] = $val;
					}
					$direct = ($data['direct']) ? 1 : 0;
					unset($data['direct']);
					$id = uniqid();
					$data['lang'] = (strlen($data['lang'])) ? $data['lang'] : $this->user->lang('XML_LANG');
					$insert = '<span class="infotooltip" id="bb_'.$id.'" title="'.$direct.base64_encode(serialize($data)).'">'.$data['name'].'</span>';
					$arrCache[$strTag] = $insert;
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