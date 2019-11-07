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

if (!defined('EQDKP_INC'))
{
  header('HTTP/1.0 404 Not Found');exit;
}


/*+----------------------------------------------------------------------------
  | calendarevents_article_parse_hook
  +--------------------------------------------------------------------------*/
if (!class_exists('calendarevents_article_parse_hook'))
{
  class calendarevents_article_parse_hook extends gen_class
  {

	/**
    * hook_search
    * Do the hook 'search'
    *
    * @return array
    */
	public function article_parse($arrOptions)
	{

		$strContent = $arrOptions['content'];

		//Parse all links
		$arrLinks = array();
		$a = $this->routing->simpleBuild('Calendarevent');
		$intLinks = preg_match_all('@<a href="(.*)">(.*)'.preg_quote($a, "@").'(.*)</a>@', $strContent, $arrLinks);

		if ($intLinks){
			foreach ($arrLinks[0] as $key => $fullMatch){
				$link = $arrLinks[3][$key];
				$arrParts = parse_url($link);
				$link = $arrParts['path'];

				$link = str_replace(array('.php', '.html', '/'), '', strip_tags($link));
				$arrPath = array_filter(explode('-', $link));
				$arrPath = array_reverse($arrPath);
				$strMyPath = $arrPath[0];

				$intEventID = intval($strMyPath);

				if($intEventID){
					include_once($this->root_path.'core/article.class.php');
					$objArticleHelper = registry::register('article');
					$strOut = $objArticleHelper->buildCalendarevent($intEventID);
					if($strOut) $strContent = str_replace($fullMatch, $strOut, $strContent);
				}
			}
		}

		$arrOptions['content'] = $strContent;

		return $arrOptions;
	}

  }
}
