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

if ( !defined('EQDKP_INC') ){
	header('HTTP/1.0 404 Not Found');exit;
}

if (!class_exists("socialplugins")) {
	class socialplugins extends gen_class {
		public static $shortcuts = array('user', 'config', 'tpl', 'env'
		);
		
		private $plugins = array();
		private $buttons = array();
		
		public function __construct(){
			$this->plugins = array(						
				'opengraph_tags' => $this->user->lang('sp_opengraph_tags'),
			);
			
			$this->buttons = array(						
				'google_plusone'=> $this->user->lang('sp_google_plusone'),
				'twitter_tweet' => $this->user->lang('sp_twitter_tweet'),
				'facebook_like'	=> $this->user->lang('sp_facebook_like'),
				'facebook_share'=> $this->user->lang('sp_facebook_share'),
				'twitter_share' => $this->user->lang('sp_twitter_share'),
			);
			
		}
		
		public function getSocialPlugins($blnOnlyActive = false){
			if ($blnOnlyActive){
				$arrOut = array();
				foreach ($this->plugins as $key => $name){
					if ((int)$this->config->get('sp_'.$key) == 1){
						$arrOut[$key] = $name;
					}
				}
				return $arrOut;
			}
			
			return $this->plugins;
		}
		
		public function getSocialButtons($blnOnlyActive = false){
			if ($blnOnlyActive){
				$arrOut = array();
				foreach ($this->buttons as $key => $name){
					if ((int)$this->config->get('sp_'.$key) == 1){
						$arrOut[$key] = $name;
					}
				}
				return $arrOut;
			}
			
			return $this->buttons;
		}
		
		
		
		public function createSocialButtons($urlToShare, $text, $height = 20){
			$html = '';
			foreach ($this->getSocialButtons(true) as $key => $name){
				$html .= ' '.$this->$key($urlToShare, $text, $height);
			}
			return $html;
		}
		
		public function callSocialPlugins($title, $description, $image){
			foreach ($this->getSocialPlugins(true) as $key => $name){
				$this->$key($title, $description, $image);
			}
		}
		
		private function opengraph_tags($title, $description, $image){
			$strMetatags = '<meta property="og:title" content="'.$title.'" />
							<meta property="og:type" content="article" />
							<meta property="og:url" content="'.(($this->env->ssl) ? 'https://' : 'http://').$_SERVER['HTTP_HOST'].xhtml_entity_decode($_SERVER['REQUEST_URI']).'" />
							<meta property="og:description" content="'.htmlspecialchars(trim(strip_tags($description)), ENT_QUOTES).'" />
							<meta property="og:image" content="'.$image.'" />';

			$this->tpl->add_meta($strMetatags);
		}
		
		public function getFirstImage($strHTML){
			if (class_exists("DOMDocument")){
				$dom = new DOMDocument();
				$dom->loadHTML('<html><body>'.$strHTML.'</body></html>');
				$images = $dom->getElementsByTagName('img');
				 foreach ($images as $image) {
					$src = $image->getAttribute('src');
					if ($src && strlen($src)){
						if (strpos($src, '/') === 0){
							return register('env')->httpHost.$src;
						} else {
							return $src;
						}
					}
				}
			}
			return '';
		}
		
		private function facebook_share($urlToShare, $text, $height){
			$html = '<a href="https://www.facebook.com/sharer.php?t='.rawurlencode($text).'&amp;u='.rawurlencode($urlToShare).'" onclick="window.open(this.href, \'\', \'width=500,height=350,modal=yes,left=100,top=50,location=no,menubar=no,resizable=yes,scrollbars=yes,status=no,toolbar=no\'); return false;" title="'.$this->user->lang('sp_facebook_share').'"><img src="'.$this->root_path.'images/logos/facebook_icon_16.png" alt="Facebook" /></a>';
			return $html;
		}
		
		private function facebook_like($urlToShare, $text, $height){
			$html = '<iframe src="http://www.facebook.com/plugins/like.php?href='.rawurlencode($urlToShare).'&amp;layout=button_count&amp;show_faces=false&amp;width=105&amp;action=like&amp;font&amp;colorscheme=light&amp;height='.$height.'" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:105px; height:'.$height.'px;" class="absmiddle"></iframe>';
			return $html;
		}
		
		private function twitter_share($urlToShare, $text, $height){
			$html = '<a href="https://twitter.com/share?text='.rawurlencode($text).'&amp;url='.rawurlencode($urlToShare).'" onclick="window.open(this.href, \'\', \'width=570,height=370,modal=yes,left=100,top=50,location=no,menubar=no,resizable=yes,scrollbars=yes,status=no,toolbar=no\'); return false;" title="'.$this->user->lang('sp_twitter_share').'"><img src="'.$this->root_path.'images/logos/twitter_icon_16.png" alt="Twitter" /></a>';
			return $html;
		}
		
		private function twitter_tweet($urlToShare, $text, $height){
			$this->tpl->add_js('!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");');
			$html = '<a href="https://twitter.com/share" class="twitter-share-button absmiddle" data-url="'.$urlToShare.'" data-text="'.$text.'" style="width: 100px">Tweet</a>';
			return $html;
		}
		
		private function google_plusone($urlToShare, $text, $height){
			$lang = ($this->user->lang_name == 'german') ? "window.___gcfg = {lang: 'de'};" : '';
			$this->tpl->add_js($lang."  (function() {
    var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
    po.src = 'https://apis.google.com/js/plusone.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
  })();");
			$html = '<g:plusone size="medium" annotation="inline" width="120" href="'.$urlToShare.'" class="absmiddle"></g:plusone>';
			return $html;
		}
		
	}
}
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_socialplugins', socialplugins::$shortcuts);
?>