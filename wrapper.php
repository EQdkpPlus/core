<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2006
 * Date:		$Date$
 * -----------------------------------------------------------------------
 * @author		$Author$
 * @copyright	2006-2011 EQdkp-Plus Developer Team
 * @link		http://eqdkp-plus.com
 * @package		eqdkp-plus
 * @version		$Rev$
 *
 * $Id$
 */

/* TODO
	- rewrite the JS code to use jquery -> not neccessary atm
*/

define('EQDKP_INC', true);
$eqdkp_root_path = './';
include_once($eqdkp_root_path . 'common.php');

class wrapper extends page_generic {
	public static function __shortcuts() {
		$shortcuts = array('user', 'tpl', 'in', 'pdh', 'config', 'core', 'env', 'hooks');
		return array_merge(parent::$shortcuts, $shortcuts);
	}

	private $data = false;

	public function __construct() {
		$handler = array(
			'id'	=> array('process'	=> 'handle_id'),
		);
		parent::__construct(false, $handler, array());
		$this->process();
	}

	public function handle_id(){
		$linkID = $this->in->get('id', '');

		//Plus-Link
		if((int)$linkID > 0){
			$urldata	= $this->pdh->get('links', 'data', array((int)$linkID));
			if ($urldata){
				$this->data = array(
					'url'	=> $urldata['url'],
					'title'	=> $urldata['name'],
					'window'=> (int)$urldata['window'],
					'height'=> $urldata['height'],
				);
			}
		}

		//Board
		if (($linkID == 'board') and (strlen($this->config->get('cmsbridge_url')) > 0)){
			$this->data = array(
				'url'	=> $this->config->get('cmsbridge_url'),
				'title'	=> $this->user->lang('forum'),
				'window'=> (int)$this->config->get('cmsbridge_embedded'),
				'height'=> '4024',
			);
		}

		//Register
		if(($linkID == 'register') and (strlen($this->config->get('cmsbridge_reg_url')) > 0)){
			$this->data = array(
				'url'	=> $this->config->get('cmsbridge_reg_url'),
				'title'	=> $this->user->lang('forum'),
				'window'=> (int)$this->config->get('cmsbridge_reg_embedded'),
				'height'=> '4024',
			);
		}
		
		//Hooks
		$arrHooks = $this->hooks->process('wrapper', array('id'=>$linkID, 'link'=>rawurldecode($this->in->get('l'))));

		if (count($arrHooks) > 0){
			foreach($arrHooks as $arrHook){
				//If wrapper id is not for hook, continue;
				if (!$arrHook) continue;
				//If wrapper id equals hook id
				if ($arrHook['id'] == $linkID){
					$arrData = $arrHook['data'];
					//If URL should be verified, do this
					if ($arrData['verify'] && !$this->VerifyLink($arrData['url'], $arrData['verify'])) break;
					$this->data = $arrData;
					break;
				}
			}
		}
	}

	public function display(){	
		if (!$this->data || $this->data['url'] == ''){
			message_die('URL not found');
		} else {
			$sop = parse_url($this->data['url']);
			$sop = ( $sop['host'] == $this->env->server_name) ? true : false;
			$output = '<div id="wrapper">';

			if(!$sop){
				$output .='<!--[IF IE]>
								<iframe id="boardframe" src="'.$this->data['url'].'" allowtransparency="true" height="'.$this->data['height'].'" width="100%" marginwidth="0" marginheight="0" frameborder="0" vspace="0" hspace="0"></iframe>
							<![if ! IE]><!-->
								<iframe id="boardframe" src="'.$this->data['url'].'" height="'.$this->data['height'].'" width="100%" marginwidth="0" marginheight="0" frameborder="0" vspace="0" hspace="0"></iframe>
							<!--><![ENDIF]><![ENDIF]-->';
			} else {
				$this->CreateDynamicIframeJS();
				$output .='<!--[IF IE]>
								<iframe id="boardframe" src="'.$this->data['url'].'" allowtransparency="true" width="100%" height="'.$this->data['height'].'" scrolling="no" marginwidth="0" marginheight="0" frameborder="0" vspace="0" hspace="0"></iframe>
							<![if ! IE]><!-->
								<iframe id="boardframe" src="'.$this->data['url'].'" width="100%" scrolling="no" marginwidth="0" marginheight="0" height="'.$this->data['height'].'" frameborder="0" vspace="0" hspace="0"></iframe>
							<!--><![ENDIF]><![ENDIF]-->';
			}

			$output .= '</div>';

			$this->tpl->assign_vars(array(
				'BOARD_OUPUT' => $output)
			);
		}

		$this->core->set_vars(array(
			'page_title'		=> isset($this->data['title']) ? $this->data['title'] : 'Wrapper',
			'page_body'			=> (isset($this->data['window']) && $this->data['window'] == 4) ? 'full' : '',
			'template_file'		=> 'wrapper.html',
			'display'			=> true)
		);

	}


	/**
	 * Check a link with a whitelist....
	 *
	 * @param     string     $strUrl          URL to be checked
	 * @param     bool       $arrWhitelist    Array with allowed domain names, p.e. array('localhost','kompsoft.de');
	 * @param     string     $strProtocol     URL Protocol, default is http, might be ftp, http, https....
	 * @return    bool                   	  Return true if URL is valid
	 */
	function VerifyLink($strUrl, $arrWhitelist, $strProtocol = ''){

		//Parse the Domain...
		$arrUrlData = parse_url($strUrl);

		//Check Protocol
		if ($strProtocol != ''){
			if($arrUrlData['scheme'] != $strProtocol){
				return false;
			}
		} else {
			//Check also against https!
			if($arrUrlData['scheme'] != 'http' && $arrUrlData['scheme'] != 'https'){
				return false;
			}
		}

		//Add the Server path to the whitelist
		if (is_array($arrWhitelist)){
			array_push($arrWhitelist, $this->config->get('server_name'));
		} else {
			$arrWhitelist = array($this->config->get('server_name'));
		}

		// Check if the Server matches the whitelist...
		if(!in_array($arrUrlData['host'], $arrWhitelist)){
			return false;
		}

		// all ok, return true
		return true;
	}

	private function CreateDynamicIframeJS(){
		$out = '
			//ID of iFrame
			var iframeid = "boardframe";

			function resizeIframe(frameid){
				var currentfr = document.getElementById(frameid);

				if (currentfr){
					currentfr.style.display = "block";
					if (currentfr.contentDocument && currentfr.contentDocument.body.offsetHeight){ //ns6 syntax
						currentfr.height = currentfr.contentDocument.body.offsetHeight;
					} else if (currentfr.Document && currentfr.Document.body.scrollHeight) {//ie5+ syntax
						currentfr.height = currentfr.Document.body.scrollHeight;
					}
					if (currentfr.addEventListener) {
						currentfr.addEventListener("load", readjustIframe, false);
					} else if (currentfr.attachEvent){
						currentfr.detachEvent("onload", readjustIframe); // Bug fix line
						currentfr.attachEvent("onload", readjustIframe);
					}
				}
			}

			function scrollToPosition(frameid){
				var currentfr = document.getElementById(frameid);
				var hash = "";
				if (currentfr.contentDocument) {
					hash = currentfr.contentDocument.location.hash;
				} else if (currentfr.Document){
					hash = currentfr.Document.location.hash;
				}

				if (hash && hash!= ""){
					hash = hash.substring(1);

					var el = false;

					if (currentfr.contentDocument) {
						el = currentfr.contentDocument.getElementById(hash);
						if (!el){
							el = currentfr.contentDocument.getElementsByName(hash)[0];
						}
					} else if (currentfr.Document){
						el = currentfr.Document.getElementById(hash);
						if (!el){
							el = currentfr.Document.getElementsByName(hash)[0];
						}
					}

					if (el){
						var elpos = findPos(el)[1];
						var framepos = findPos(currentfr)[1];
						scrollTo(0,elpos + framepos);
					} else {
						scrollTo(0,0);
					}

				} else {
					scrollTo(0,0);
				}
			}

			function findPos(obj) {
				var curleft = curtop = 0;
					if (obj.offsetParent) {
						do {
						curleft += obj.offsetLeft;
						curtop += obj.offsetTop;
					} while (obj = obj.offsetParent);
				}
				return [curleft,curtop];
			}

			function readjustIframe(loadevt) {
				var crossevt=(window.event) ? event : loadevt;
				var iframeroot=(crossevt.currentTarget)? crossevt.currentTarget : crossevt.srcElement;
				if (iframeroot) {
					resizeIframe(iframeroot.id);
					scrollToPosition(iframeroot.id);
				}
			}

			function resizeCaller() {
				resizeIframe(iframeid);
				var tempobj = document.getElementById(iframeid);
				tempobj.style.display = "block";
			}

			if (window.addEventListener){
				window.addEventListener("load", resizeCaller, false);
			} else if (window.attachEvent) {
				window.attachEvent("onload", resizeCaller);
			} else {
				window.onload=resizeCaller;
			}

			var aktiv = window.setInterval(resizeCaller, 1000*2); //2 Sekunden
		';

		 $this->tpl->add_js($out, 'docready');
	} #end function

}
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_wrapper', wrapper::__shortcuts());
registry::register('wrapper');
?>