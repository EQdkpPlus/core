<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2010
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

class search_pageobject extends pageobject {

	public function __construct() {
		$handler = array();
		parent::__construct('u_search', $handler, array(), null, '');
		$this->process();
	}

	public function display(){
		$this->search_array = array(
			'article'	=> array(
				'category'	=> $this->user->lang('articles'),
				'module'	=> 'articles',
				'method'	=> 'search',
			),
			'article_categories' => array(
				'category'	=> $this->user->lang('article_categories'),
				'module'	=> 'article_categories',
				'method'	=> 'search',
			),
			'calendar'	=> array(
				'category'	=> $this->user->lang('calendar'),
				'module'	=> 'calendar_events',
				'method'	=> 'search',
				'permission' => $this->user->check_pageobjects(array('calendar', 'calendarevent'), 'AND', false)
			),
			'members'	=> array(
				'category'	=> $this->user->lang('members'),
				'module'	=> 'member',
				'method'	=> 'search',
				'permission' => $this->user->check_pageobjects(array('character'), 'AND', false)
			),
			'raids'	=> array(
				'category'	=> $this->user->lang('menu_raids'),
				'module'	=> 'raid',
				'method'	=> 'search',
				'permission' => $this->user->check_pageobjects(array('raids'), 'AND', false)
			),
			'items'	=> array(
				'category'	=> $this->user->lang('items'),
				'module'	=> 'item',
				'method'	=> 'search',
				'permission' => $this->user->check_pageobjects(array('items'), 'AND', false)
			),
			'events'	=> array(
				'category'	=> $this->user->lang('events'),
				'module'	=> 'event',
				'method'	=> 'search',
				'permission' => $this->user->check_pageobjects(array('events'), 'AND', false)
			),
			'user'	=> array(
				'category'	=> $this->user->lang('users'),
				'module'	=> 'user',
				'method'	=> 'search',
				'permissions'	=> array('u_userlist'),
			)
		);
		
		$arrHooks = $this->hooks->process('search');
		if (is_array($arrHooks)){
			foreach ($arrHooks as $plugin => $value){
				if (is_array($value)){
					$this->search_array = array_merge($this->search_array, $value);
				}
			}
		}

		//perform search
		$blnSearched = false;
		$intResultCount = 0;
		$blnResults = false;

		if (strlen($this->in->get('svalue', '')) > 2 && $this->in->get('svalue', '') != $this->user->lang('search').'...'){
			$blnSearched = true;
			foreach ($this->search_array as $key => $value){
				$blnPermission = true;
				if (isset($value['permission']) && !$value['permission']){
					$blnPermission = false;
				}
				
				if (is_array($value['permissions']) && count($value['permissions']) > 0){
					foreach ($value['permissions'] as $perm){
						if (!$this->user->check_auth($perm, false)){
							$blnPermission = false;
							break;
						}
					}
				}
				
				if ($blnPermission){
					$retArray = $this->pdh->get($value['module'], $value['method'], array($this->in->get('svalue')));
					if (is_array($retArray) && count($retArray) > 0){
						$blnResults = true;

						$this->tpl->assign_block_vars('tabs', array(
							'ID'	=> $key,
							'NAME'	=> $value['category'],
							'COUNT'	=> count($retArray),
						));
						$intResultCount += count($retArray);

						//Bring them to template;
						foreach ($retArray as $val){
							$this->tpl->assign_block_vars('tabs.results', array(
								'NAME'	=> $val['name'],
								'LINK'	=> $val['link'],
								'ID'	=> $val['id']
							));
						}
					}

				}
			}
		} elseif ($this->in->exists('svalue') && $this->in->get('svalue', '') != $this->user->lang('search').'...'){
			$this->tpl->assign_vars(array(
				'S_VALUE_TOO_SHORT'	=> true,
			));
		}

		$this->jquery->Tab_header('search_result_tabs', true);
		$this->tpl->assign_vars(array(
			'S_RESULTS'			=> $blnResults,
			'S_SEARCHED'		=> $blnSearched,
			'SEARCH_VALUE'		=> ($this->in->get('svalue', '') != $this->user->lang('search').'...') ? sanitize($this->in->get('svalue', '')) : '',
			'L_SEARCH_RESULTS'	=> sprintf($this->user->lang('search_results'), $this->in->get('svalue', ''), $intResultCount),
			'L_NO_RESULTS'		=> sprintf($this->user->lang('search_no_results'), $this->in->get('svalue', '')),
		));

		$this->core->set_vars(array(
			'page_title'		=> $this->user->lang('search'),
			'template_file'		=> 'search.html',
			'display'			=> true)
		);
	}

}

?>