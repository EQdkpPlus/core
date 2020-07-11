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

if (!class_exists("socialplugins")) {
	class socialplugins extends gen_class {
		public static $shortcuts	= array('puf'	=> 'urlfetcher');

		private $plugins = array('opengraph_tags');
		private $buttons = array('facebook_share', 'twitter_share', 'whatsapp_share', 'facebook_like');
		private $js_included = false;
		private $intCacheTime = 10;
		private $cache = -1;
		private $blnMetaAdded = false;


		public function getSocialPlugins($blnOnlyActive = false){
			if ($blnOnlyActive){
				$arrOut = array();
				foreach ($this->plugins as $key){
					if ((int)$this->config->get('sp_'.$key) == 1){
						$arrOut[] = $key;
					}
				}
				return $arrOut;
			}

			return $this->plugins;
		}

		public function getSocialButtons($blnOnlyActive = false){
			if ($blnOnlyActive){
				$arrOut = array();
				foreach ($this->buttons as $key){
					if ((int)$this->config->get('sp_'.$key) == 1){
						$arrOut[] = $key;
					}
				}
				return $arrOut;
			}

			return $this->buttons;
		}



		public function createSocialButtons($urlToShare, $text, $height = 20){
			if (!$this->js_included){
				$this->addSocialButtonCountJS();
				$this->js_included = true;
			}

			$arrButtons = $this->getSocialButtons(true);
			if (count($arrButtons)){
				$html = '<ul class="social-bookmarks" data-url="'.rawurlencode($urlToShare).'" data-target-id="'.md5($urlToShare).'">';

				foreach ($arrButtons as $key){
					$html .= '<li class="'.$key.'">'.$this->$key($urlToShare, $text, $height).'</li>';
				}

				$html .= '</ul>';
				return $html;
			}
			return '';
		}

		public function getSocialButtonCount($urlToShare, $strTarget){
			$arrButtons = $this->getSocialButtons(true);
			$arrOut = array('_target' => $strTarget);

			//Check URL
			$sop = parse_url($urlToShare);
			$sop = ( $sop['host'] == $this->env->server_name) ? true : false;
			if (!$sop) return json_encode($arrOut);

			if (count($arrButtons)){
				foreach ($arrButtons as $key){
					$methodname = 'count_'.$key;
					if (method_exists($this, $methodname)){
						$arrOut = array_merge($arrOut, $this->$methodname($urlToShare));
					}
				}
			}

			return json_encode($arrOut);
		}

		public function callSocialPlugins($title, $description, $image){
			foreach ($this->getSocialPlugins(true) as $key){
				$this->$key($title, $description, $image);
			}
		}

		private function opengraph_tags($title, $description, $image){
			if($this->blnMetaAdded) return;

			// the logo...
			$strHeaderLogoPath	= "templates/".$this->user->style['template_path']."/images/";

			// the logo...
			if(is_file($this->pfh->FolderPath('','files').$this->config->get('custom_logo'))){
				$headerlogo	= $this->pfh->FolderPath('','files', 'absolute').$this->config->get('custom_logo');
			} else if(file_exists($this->root_path.$strHeaderLogoPath.'logo.png')){
				$headerlogo	= $this->env->link.$strHeaderLogoPath.'logo.png';
			} else if(file_exists($this->root_path.$strHeaderLogoPath.'logo.svg')){
				$headerlogo	= $this->env->link.$strHeaderLogoPath.'logo.svg';
			} else $headerlogo = "";

			if($image == "") $image = $headerlogo;

			$strMetatags = '<meta property="og:title" content="'.$title.'" />
							<meta property="og:type" content="article" />
							<meta property="og:url" content="'.htmlentities($this->env->httpHost.$this->user->removeSIDfromString($this->env->request)).'" />
							<meta property="og:description" content="'.htmlspecialchars(trim(strip_tags($description)), ENT_QUOTES).'" />
							<meta property="og:image" content="'.$image.'" />';

			$this->tpl->add_meta($strMetatags);

			$this->blnMetaAdded = true;
		}

		public function getFirstImage($strHTML){
			return get_first_image($strHTML);
		}

		private function facebook_share($urlToShare, $text, $height){
			$intCount = ($this->getCache($urlToShare, 'facebook') !== false) ? $this->getCache($urlToShare, 'facebook') : 0;
			$html = '<a class="social-bookmarks-count facebook" href="https://www.facebook.com/sharer/sharer.php?u='.rawurlencode($urlToShare).'" onclick="window.open(this.href, \'\', \'width=500,height=350,modal=yes,left=100,top=50,location=no,menubar=no,resizable=yes,scrollbars=yes,status=no,toolbar=no\'); return false;"><i class="fa fa-facebook-square"></i> <span class="share-text">'.$this->user->lang('sp_btn_facebook_share').'</span><span class="share-count">'.$intCount.'</span></a>';
			return $html;
		}

		private function facebook_like($urlToShare, $text, $height){
			$html = '<iframe src="https://www.facebook.com/plugins/like.php?href='.rawurlencode($urlToShare).'&amp;layout=button_count&amp;show_faces=false&amp;width=110&amp;action=like&amp;font&amp;colorscheme=light&amp;height='.$height.'" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:110px; height:'.$height.'px;" class="absmiddle" loading="lazy"></iframe>';
			return $html;
		}

		private function twitter_share($urlToShare, $text, $height){
			$html = '<a class="social-bookmarks-nocount twitter" href="https://twitter.com/share?text='.rawurlencode($text).'&amp;url='.rawurlencode($urlToShare).'" onclick="window.open(this.href, \'\', \'width=570,height=370,modal=yes,left=100,top=50,location=no,menubar=no,resizable=yes,scrollbars=yes,status=no,toolbar=no\'); return false;" title="'.$this->user->lang('sp_twitter_share').'"><i class="fa fa-twitter"></i> <span class="share-text">'.$this->user->lang('sp_btn_twitter_share').'</span></a>';
			return $html;
		}

		private function whatsapp_share($urlToShare, $text, $height){
			$html = '<a class="social-bookmarks-nocount whatsapp" href="whatsapp://send?text='.rawurlencode($text).' '.rawurlencode($urlToShare).'"><i class="fa fa-whatsapp"></i> <span class="share-text">'.$this->user->lang('sp_btn_facebook_share').'</span></a>';
			return $html;
		}

		private function count_facebook_share($urlToShare){
			$intShareCount = 0;

			$intCache = $this->getCache($urlToShare, 'facebook');

			if ($intCache !== false){
				$intShareCount = $intCache;
			} else {
				$url = "https://graph.facebook.com/?id=".rawurlencode($urlToShare);
				$objResult = $this->puf->fetch($url);
				if ($objResult){
					$arrResult = json_decode($objResult);
					if (isset($arrResult->shares)){
						$intShareCount = intval($arrResult->shares);
					}
				}
				$this->addToCache($urlToShare, 'facebook', $intShareCount);
			}
			return array('facebook' => $intShareCount);
		}

		private function count_google_plusone($urlToShare){
			$intShareCount = 0;

			$intCache = $this->getCache($urlToShare, 'gplus');

			if ($intCache !== false){
				$intShareCount = $intCache;
			} else {
				$url = "https://clients6.google.com/rpc?key=AIzaSyCKSbrvQasunBoV16zDH9R33D88CeLr9gQ";

				$arrJson = array(
					'method'		=> 'pos.plusones.get',
					'id'			=> 'p',
					'jsonrpc'		=> '2.0',
					'key'			=> 'p',
					'apiVersion'	=> 'v1',
					'params'		=> array(
						'nolog'   => 'true',
						'id'      => $urlToShare,
						'source'  => 'widget',
						'userId'  => '@viewer',
						'groupId' => '@self',
					),
				);

				$objResult = $this->puf->post($url, json_encode($arrJson), 'application/json');
				if ($objResult){
					$arrResult = json_decode($objResult);
					if (isset($arrResult->result->metadata->globalCounts->count)){
						$intShareCount = intval($arrResult->result->metadata->globalCounts->count);
					}
				}
				$this->addToCache($urlToShare, 'gplus', $intShareCount);
			}
			return array('gplus' => $intShareCount);
		}

		private function getCache($url, $service){
			$this->loadCache();
			if (!isset($this->cache[md5($url)]) && !isset($this->cache[md5($url)][$service])) return false;

			//Check Time
			$time = $this->cache[md5($url)][$service]['time'];
			if ($time+$this->intCacheTime < $this->time->time) return false;

			return $this->cache[md5($url)][$service]['count'];
		}

		private function loadCache(){
			if ($this->cache === -1){
				$this->cache = $this->pdc->get('social_share_count');
			}
		}

		private function addToCache($url, $service, $intCount){
			if (!isset($this->cache[md5($url)])) $this->cache[md5($url)] = array();
			if (!isset($this->cache[md5($url)][$service])) $this->cache[md5($url)][$service] = array();

			$this->cache[md5($url)][$service] = array('time' => $this->time->time, 'count' => $intCount);
			$this->pdc->put('social_share_count', $this->cache, 60*60*24);
		}

		private function addSocialButtonCountJS(){
			$this->tpl->add_js("
					(function($){
	$.fn.extend({

		//pass the options variable to the function
		eqdkp_socialbuttoncount: function(options) {

		return this.each(function() {
			var url = $(this).data('url');
			var target = $(this).data('target-id');

			var posturl = mmocms_root_path + 'exchange.php'+ mmocms_sid + '&out=socialcounts';
			$.post( posturl, { url: url, target: target }, function( data ) {
				var target = data._target;

				$.each(data, function(i,item){
					if (i != '_target') {
						var a = $('ul[data-target-id='+target+']');
						a.find('a.'+i).find('span.share-count').html(item);
					}

				})
			});

			});
		}
	});
})(jQuery);

	$('.social-bookmarks').eqdkp_socialbuttoncount();		", 'docready');


		}

	}
}
