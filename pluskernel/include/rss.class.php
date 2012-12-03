<?php
 /*
 * Project:     EQdkp-Plus
 * License:     Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:       24.12.2007
 * Date:        $Date$
 * -----------------------------------------------------------------------
 * @author      $Author$
 * @copyright   2006-2008 Corgan - Stefan Knaak | Wallenium & the EQdkp-Plus Developer Team
 * @link        http://eqdkp-plus.com
 * @package     eqdkp-plus
 * @version     $Rev$
 * 
 * $Id$
 */


if ( !defined('EQDKP_INC') ){
    header('HTTP/1.0 404 Not Found');exit;
}

/**
 * RSS-News Parser Class written by Stefan "Corgan" Knaak
 * The RSS Data stored per default for 3 Hours in the Database, before refresh
 *
 * assign TPL VARs NEWS_TICKER_H and NEWS_TICKER_V
 * Return the array $rss->news wich includes all news
 *
 * Its not allowed to change the RSS URL.
 * This RSS Class only works with the RSS Feed from www.Allvatar.com !
 * This is not a bug, this is volitional!
 */
class rss
{

	//Config
	var $cachetime      = 10800;  // refresh time in seconds default 3 hours = 10800 seconds
	var $tooltipcrop	 = 60;	// after that number of symbols the text in the tooltip wraps
	var $titlecrop		 = 30;	// after that number of symbols the text in the title wraps
	var $checkURL_first	 = true ;

	//return vars
	var $title 			= null;
	var $link 			= null;
	var $description	= null;
	var $lastcreate 	= null;
	var $feed			= null;
	var $news			= null;
	var $updated = null;
	
	var $output_left	= '';


	/**
	 * Constructor
	 *
	 * @return rss
	 */
	function rss()
	{
		global $eqdkp,$user;

		$rss_number = 1 ;
		switch (strtolower($eqdkp->config['default_game']))
		{
			case 'wow': 				$rss_number = 1 ; break;
			case 'daoc': 				$rss_number = 7 ; break;
			case 'everquest': 			$rss_number = 10 ; break;
			case 'everquest2': 			$rss_number = 10 ; break;
			case 'lotro': 				$rss_number = 4 ; break;
			case 'tr': 					$rss_number = 19 ; break;
			case 'vanguard-soh': 		$rss_number = 5 ; break;
			case 'guildwars': 			$rss_number = 3 ; break;
			case 'aoc': 				$rss_number = 13 ; break;
			case 'warhammer': 			$rss_number = 14 ; break;
			case 'aion': 				$rss_number = 22 ; break;
			case 'runesofmagic': 		$rss_number = 37 ; break;
			
			default: $rss_number = '' ;
				break;
		}

		$lang = '-en';

		if (($eqdkp->config['default_lang'] == 'german') or ($user->data['user_lang'] == 'german'))
		{
			$lang = '';
		}

		$this->checkURL_first = $conf_plus['pk_Rss_checkURL'] ;
		$this->rssurl = 'http://rss.allvatar.com/news-'.$rss_number.$lang.'.xml';
		
		if (strtolower($eqdkp->config['default_game']) == 'wow' && $eqdkp->config['game_language'] == 'en') 
		{
			$this->rssurl = 'http://manaflask.com/xml/news_english.xml';
		}
		elseif (strtolower($eqdkp->config['default_game']) == 'wow' && $eqdkp->config['game_language'] == 'es')
		{
			$this->rssurl = 'http://feeds.feedburner.com/guiaswowraideando';
		}
		$this->parseXML($this->GetRSS($this->rssurl));

		if ($this->news)
		{
			$this->createTPLvar($this->news);
		}

	}

	/**
	 * GetRSS get the RSS Feed from an given URL
	 * Check if an refresh is needed
	 *
	 * @param String $url must be an valid RSS Feed
	 * @return XMLString
	 */
	function GetRSS($url)
	{
		global $db, $urlreader, $pdc ;
		$rss_string = null;

		$rss_string = $pdc->get('plus_rss',false,true);
		
		if (!$rss_string) 
		{		
			$sql = "SELECT updated,rss FROM __plus_rss";
			$result = $db->query($sql);
			if($row = $db->fetch_record($result))
	    	{
	      		$this->updated = $row['updated'];
				
	      		if( (time() - $this->updated) > $this->cachetime )
				{
					$urlreader->checkURL_first = $this->checkURL_first;
					$rss_string = $urlreader->GetURL($url) ;
					$this->saveRSS($rss_string);	
				}elseif (isset($row['rss']) )
				{					
					$rss_string = @base64_decode($row['rss']) ;
					$rss_string = @gzuncompress($rss_string) ;
					$rss_string = @unserialize($rss_string);						
					$rss_string = stripslashes($rss_string);
				}
			}else 
			{
					$urlreader->checkURL_first = $this->checkURL_first;
					$rss_string = $urlreader->GetURL($url) ;
					$this->saveRSS($rss_string);				
			}
			$pdc->put('plus_rss',$rss_string,$this->cachetime-5,false,true);
		}
		return $rss_string ;
	}

	/**
	 * saveRSS
	 * Save the given RSS String into the Database
	 *
	 * @param String $rss
	 */
	function saveRSS($rss)
	{
		global $db, $eqdkp;

		if (strlen($rss)>1)
		{
			$sql = "TRUNCATE TABLE __plus_rss ";
			$db->query($sql);

			$rss = addslashes($rss);
			$rss = @base64_encode(gzcompress(serialize($rss))) ;
			
			$sql = "INSERT INTO __plus_rss SET ".
				"  updated='".time()."'".
				",  rss='".$rss."'".
				", game='".$eqdkp->config['default_game']."'";

			$db->query($sql);
		}
	}

	/**
	 * parseXML
	 * parse the XML Data into an Array
	 *
	 * @param RSS-XML $rss
	 */
	function parseXML($rss)
	{
		global $eqdkp,$conf_plus, $eqdkp_root_path;

		if (version_compare(phpversion(), "5.0.0", ">="))
		{
			include_once($eqdkp_root_path.'pluskernel/include/parser/xml_php5.php'); // Load for php5
		} else
		{
			include_once($eqdkp_root_path.'pluskernel/include/parser/xml_php4.php'); // Load for php4
		}


		$parser = new XMLParser($rss);
		if ($parser)
		{
			$parser->Parse();
			$this->title 		= $parser->document->channel[0]->title[0]->tagData ;
			$this->link 		= $parser->document->channel[0]->link[0]->tagData ;
			$this->description	= $parser->document->channel[0]->description[0]->tagData ;
			$this->lastcreate 	= $parser->document->channel[0]->lastbuilddate[0]->tagData;
			$this->feed			= $parser->document->channel[0]->generator[0]->tagData;

			$this->news = array() ;

			if (is_array($parser->document->channel[0]->item))
			{
				foreach ($parser->document->channel[0]->item as $key => $value)
				{
					if ($i >= intval($conf_plus['pk_Rss_count'])  and ($conf_plus['pk_Rss_count'] <> '') )
					{return;}

					$this->news[$key]['title'] 			= utf8_decode($value->title[0]->tagData);
					$this->news[$key]['link'] 			=  $value->link[0]->tagData;
					$this->news[$key]['description'] 	=  utf8_decode($value->description[0]->tagData);
					$this->news[$key]['author'] 		=  $value->author[0]->tagData;
					$this->news[$key]['pubdate'] 		=  $value->pubdate[0]->tagData;
					$i++;

				}
			}

		}


	} # end function

	/**
	 * createTPLvar
	 * Createas the {NEWS_TICKER_H} and {NEWS_TICKER_V} Vars
	 * wich could be displayed in the templates
	 *
	 * @param Array $news
	 * @return NewstickerArray
	 */
	function createTPLvar($news)
	{
		global $tpl, $eqdkp,$eqdkp_root_path,$user,$conf_plus,$user, $html,$jqueryp;

		$updated_time = date("H:i", $this->updated);
		$this->header = $eqdkp->config['default_game'].' News '.$updated_time ;

		if (is_array($news))
		{

			foreach ($news as $key => $value)
			{
			  // Generate an array fo an accordion
			  // array style: title => content
			  $newstick_array[$value['title']] = $this->createBody(
												 $value['description'],
												 $value['link'],
												 $value['author'],
												 $value['pubdate']
												);

				$newsticker_v_body .= $this->createLink(
													$value['title'],
													$value['link'],
													$value['description'],
												    $value['author'],
												    $value['pubdate'],
													false
													) . " | ";


			}#  end foreach

			$submitNews = "<a href='http://www.allvatar.com/news/index.php?p=nnews' target=_blank>".$html->ToolTip($this->wrapText($user->lang['SubmitNews_help']),$user->lang['SubmitNews'])." </a>";
			$table_title = " ";

			//ticker
			$newsticker_H  = '<table width="100%" style="table-layout:fixed" cellspacing="1" cellpadding="2"> <tr> <th> <marquee scrolldelay="110" onMouseover="javascript: this.scrollAmount=\'0\' " onMouseout="javascript: this.scrollAmount=\'8\'" >'.$newsticker_v_body;
			$newsticker_H .= $submitNews. '</marquee> </th></tr></table>';


			//Menunews
			$newsticker_V  = '<div class="forumline"><table width="100%" border="0" cellspacing="0" cellpadding="2">
							  <tr> <th class="smalltitle"> '.$this->header.'</th></tr>';
			$newsticker_V .= '<tr class="row1"><td>'.$jqueryp->accordion('rrs_news',$newstick_array).'</td></tr>';
			$newsticker_V .= '<tr class="row2"><td align=center>'.$submitNews.' </td></tr>';
			$newsticker_V .= '</table></div>';

			//Set Template Variables
			if(($conf_plus['pk_Rss_Style'] == '1' ) or ($conf_plus['pk_Rss_Style'] == '0' ))
			{
				#$tpl->assign_vars(array('NEWS_TICKER_V'			=> $newsticker_V  ));
				$this->output_left = $newsticker_V;
			}

			if(($conf_plus['pk_Rss_Style'] == '2' ) or ($conf_plus['pk_Rss_Style'] == '0' ))
			{
				$tpl->assign_vars(array('NEWS_TICKER_H'			=> $newsticker_H));				
			}

		}

		return $newsticker_V ;

	} # end function

	/**
	 * createLink
	 * Creates an link with the description in a tooltip.
	 *
	 * @param String $title
	 * @param String $link
	 * @param  String $disc
	 * @return String
	 */
	function createLink($title,$link,$disc,$author="",$date="",$crop_title=false)
	{
		global $html,$eqdkp ;

		$tt = stripslashes($disc);
		$tt = str_replace('"', "'", $tt);
		$tt = str_replace(array("\n", "\r"), '', $tt);
   	$tt = addslashes($tt);

   	$header = "<b>".addslashes($title)."</b><hr noshade />";
   	$content = $this->wrapText($tt,$this->tooltipcrop) ;
		$footer = "<hr noshade />".date('d.m.Y, H:i', strtotime($date))." by <b>".$author."</b>";

		if ($crop_title)
		{
			$title =	$this->cropText($title,$this->titlecrop) ;
		}
		$_link = "<a href='".$link."' target=_blank>".$title."</a>" ;

		$ret = " ".$html->ToolTip($header.$content.$footer,$_link);

		return $ret ;
	}

  /**
	 * createBody
	 *
	 * @param  String $disc
	 * @param  String $author
	 * @param  String $date
	 * @return String
	 */
	function createBody($disc,$link,$author="",$date="")
	{
    $content = '<a href="'.$link.'"" target="_blank">'.$this->cropText($disc,280).'</a>';
    $footer = date('d.m.Y, H:i', strtotime($date))." by <b>".$author."</b>";
    return $content.'<br />'.$footer;
	}

	/**
	 * cropText
	 * crop the text after a given lenght
	 *
	 * @param String $text
	 * @param Integer $len
	 * @return String
	 */
	function cropText($text,$len)
	{
		$ret = "";

		$ret = substr($text,0,$len);
		if (strlen($text) > $len)
		{
			$ret .= '..';
		}

		return $ret ;
	}

	/**
	 * wrapText
	 * wraps the text after a given lenght
	 * but only after an word!
	 *
	 * @param String $text
	 * @param Integer $len
	 * @return String
	 */
	function wrapText($text,$len=60)
	{
		$croplen = $len ;
		$ret = "";
		for($i=0;;$i++)
		{
			if ($i == strlen($text)) {
				break;
			}

			$ret .= $text[$i];
			if ($i >= $len)
			{
				if ($text[$i] == " ")
				{
					$ret .= str_replace(' ', '<br />', $text[$i] );
					$len = $len+$croplen;
				}

			}
		}
		return $ret ;
	}




}// end of class
?>