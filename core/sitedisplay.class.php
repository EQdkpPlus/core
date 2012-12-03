<?php
 /*
 * Project:     EQdkp-Plus
 * License:     Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:       2010
 * Date:        $Date$
 * -----------------------------------------------------------------------
 * @author      $Author$
 * @copyright   2006-2010 EQdkp-Plus Developer Team
 * @link        http://eqdkp-plus.com
 * @package     eqdkp-plus
 * @version     $Rev$
 * 
 * $Id$
 */


if ( !defined('EQDKP_INC') )
{
    header('HTTP/1.0 404 Not Found');
    exit;
}

class siteDisplay
{
	var $left = '';
	var $right = '';
	var $top = '';
	var $buttom = '';
	var $content = '';
	var $data = '';
	var $info = '';
	
	function siteDisplay()
	{
		global $user, $core, $tpl, $eqdkp_root_path, $_HMODE;		
		if (!$user->check_auth('a_', false) && Raidcount() && validate())
		{		
			$g = strtolower($core->config['default_game']);
			$l = strtolower($user->data['user_lang']);
			$d = strtolower($core->config['default_lang']);
			
			if ($g == 'wow')
			{
				if (($l == 'german') || ($d == 'german'))
				{
					$buttom_code 	="OA_show(16);";
					$left_code 		="OA_show(17);";
					$right_code 	="OA_show(53);";
					$top_code 		="OA_show(64);";
					$content_code	="OA_show(52);";
				}else {
					$buttom_code 	="OA_show(19);";
					$left_code 		="OA_show(18);";									
					$right_code 	="OA_show(54);";
					$top_code 		="OA_show(63);";
					$content_code	="OA_show(51);";
				}
			}elseif($g=='lotro')
			{
				if (($l == 'german') || ($d == 'german'))
				{
					$buttom_code 	= "OA_show(22);";
					$left_code 		= "OA_show(20);";
					$right_code 	= "OA_show(55);";
					$top_code 		= "OA_show(62);";
					$content_code	= "OA_show(50);";
				}else {
					$buttom_code 	= "OA_show(23);";
					$left_code 		= "OA_show(21);";					
					$right_code 	= "OA_show(56);";
					$top_code 		= "OA_show(61);";
					$content_code	= "OA_show(49);";
				}
			}else
			{
				if (($l == 'german') || ($d == 'german'))
				{
					$buttom_code 	= "OA_show(25);";
					$left_code 		= "OA_show(24);";			
					$right_code 	= "OA_show(57);";
					$top_code 		= "OA_show(60);";
					$content_code	= "OA_show(48);";
				}else {
					$buttom_code 	= "OA_show(27);";
					$left_code 		= "OA_show(26);";
					$right_code 	= "OA_show(58);";
					$top_code 		= "OA_show(59);";
					$content_code	= "OA_show(47);";
				}
			}

			if ($_HMODE){						
				$buttom_code 	= "OA_show(12);";
				$left_code 		= "OA_show(9);";
				$right_code 	= "OA_show(10);";
				$top_code 		= "OA_show(11);";
				$content_code	= "OA_show(8);";
	    }
	    	
			$left  = '<br><table width="100%" border="0" cellspacing="1" cellpadding="2">';
			$left .= "<tr><td align=center><script type='text/javascript'>";
			$left .= $left_code ;
			$left .= '</script></td></tr>';
			$left .= '<tr><td align=center><a class="small" target="_self" href="'.$eqdkp_root_path.'ads.php">'.$user->lang['ads_remove'].'</a></td></tr>';
			$left .= '</table>';

			$right  = "<script type='text/javascript'>";
			$right .= $right_code ;
			$right .= '</script>';


			$top  = '<table width="100%" border="0" cellspacing="1" cellpadding="2">';
			$top .= "<tr><td align=center><script type='text/javascript'>";
			$top .= $top_code ;
			$top .= '</script></td></tr>';
			$top .= '</table>';
			
			$buttom  = '<table width="100%" border="0" cellspacing="1" cellpadding="2">';
			$buttom .= "<tr><td align=center><script type='text/javascript'>";
			$buttom .= $buttom_code ;
			$buttom .= '</script></td></tr>';
			$buttom .= '</table>';
			
			$content .= "<script type='text/javascript'>";
			$content .= $content_code ;
			$content .= '</script>';		

			
			$this->buttom		= $buttom;
			$this->left			= $left;
			$this->right		= $right;
			$this->top			= $top;
			$this->content	= $content;
			
			$tpl->assign_var('H',$buttom);
			$tpl->assign_var('TOP',$top);
		}
		
		$g = '
			var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
			document.write(unescape("%3Cscript src=\'" + gaJsHost + "google-analytics.com/ga.js\' type=\'text/javascript\'%3E%3C/script%3E"));
			
			var pageTracker = _gat._getTracker("UA-3916299-1");
			pageTracker._initData();
			pageTracker._trackPageview();';
		
   		if ($_HMODE) 
    	{
    		$tpl->js_file('http://www.google-analytics.com/urchin.js');
			$g ='
				try {
				_uacct = "UA-2163237-1";
				urchinTracker();
				} catch(err) {}
							
				var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
				document.write(unescape("%3Cscript src=\'" + gaJsHost + "google-analytics.com/ga.js\' type=\'text/javascript\'%3E%3C/script%3E"));

				try {
				var pageTracker = _gat._getTracker("UA-7108574-1");
				pageTracker._trackPageview();
				} catch(err) {}		
			';
    	}
    $tpl->add_js($g, 'eop2');
		
	}

}

