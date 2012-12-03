<?php
 /*
 * Project:     EQdkp-Plus
 * License:     Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:       2008
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


if ( !defined('EQDKP_INC') )
{
    header('HTTP/1.0 404 Not Found');
    exit;
}

class siteDisplay
{
	var $left = '';
	var $buttom = '';
	var $data = '';
	var $info = '';
	
	function siteDisplay()
	{
		global $user, $eqdkp, $tpl, $eqdkp_root_path,$_HMODE;		
		if (!check_auth_admin($user->data['user_id']) && Raidcount() && validate())
		{		
			$g = strtolower($eqdkp->config['default_game']);
			$l = strtolower($user->data['user_lang']);
			$d = strtolower($eqdkp->config['default_lang']);
			
			if ($g == 'wow')
			{
				if (($l == 'german') || ($d == 'german'))
				{
					$buttom_code = "<script type='text/javascript'><!--// <![CDATA[
							        /* [id16] WOW Deutsch 728x90 */
     								OA_show(16);
									// ]]> --></script><noscript><a target='_blank' href='http://ads.allvatar.com/adserver/www/delivery/ck.php?n=7e3711c'><img border='0' alt='' src='http://ads.allvatar.com/adserver/www/delivery/avw.php?zoneid=16&amp;n=7e3711c' /></a></noscript>";
					$left_code = "<script type='text/javascript'><!--// <![CDATA[
								   /* [id17] WOW Deutsch 120x600 */
								   OA_show(17);
								   // ]]> --></script><noscript><a target='_blank' href='http://ads.allvatar.com/adserver/www/delivery/ck.php?n=e922c2d'><img border='0' alt='' src='http://ads.allvatar.com/adserver/www/delivery/avw.php?zoneid=17&amp;n=e922c2d' /></a></noscript>";
				}else {
					$buttom_code = "<script type='text/javascript'><!--// <![CDATA[
								    /* [id19] WOW Englisch 728x90 */
								    OA_show(19);
									// ]]> --></script><noscript><a target='_blank' href='http://ads.allvatar.com/adserver/www/delivery/ck.php?n=3c731b1'><img border='0' alt='' src='http://ads.allvatar.com/adserver/www/delivery/avw.php?zoneid=19&amp;n=3c731b1' /></a></noscript>";
					$left_code = "<script type='text/javascript'><!--// <![CDATA[
							      /* [id18] WOW Englisch 120x600 */
							      OA_show(18);
								  // ]]> --></script><noscript><a target='_blank' href='http://ads.allvatar.com/adserver/www/delivery/ck.php?n=6aa2d76'><img border='0' alt='' src='http://ads.allvatar.com/adserver/www/delivery/avw.php?zoneid=18&amp;n=6aa2d76' /></a></noscript>";
				}
			}elseif($g=='lotro')
			{
				if (($l == 'german') || ($d == 'german'))
				{
					$buttom_code = "<script type='text/javascript'><!--// <![CDATA[
								    /* [id22] Lord Of The Rings Deutsch 728x90 */
									OA_show(22);
									// ]]> --></script><noscript><a target='_blank' href='http://ads.allvatar.com/adserver/www/delivery/ck.php?n=b1da868'><img border='0' alt='' src='http://ads.allvatar.com/adserver/www/delivery/avw.php?zoneid=22&amp;n=b1da868' /></a></noscript>";
					$left_code = "<script type='text/javascript'><!--// <![CDATA[
								  /* [id20] Lord Of The Rings Deutsch 120x600 */
								  OA_show(20);
								  // ]]> --></script><noscript><a target='_blank' href='http://ads.allvatar.com/adserver/www/delivery/ck.php?n=b6e5d74'><img border='0' alt='' src='http://ads.allvatar.com/adserver/www/delivery/avw.php?zoneid=20&amp;n=b6e5d74' /></a></noscript>";
				}else {
					$buttom_code = "<script type='text/javascript'><!--// <![CDATA[
								    /* [id23] Lord Of The Rings Englisch 728x90 */
								    OA_show(23);
									// ]]> --></script><noscript><a target='_blank' href='http://ads.allvatar.com/adserver/www/delivery/ck.php?n=6b92a4d'><img border='0' alt='' src='http://ads.allvatar.com/adserver/www/delivery/avw.php?zoneid=23&amp;n=6b92a4d' /></a></noscript>";
					$left_code = "<script type='text/javascript'><!--// <![CDATA[
								  /* [id21] Lord Of The Rings Englisch 120x600 */
								  OA_show(21);
								  // ]]> --></script><noscript><a target='_blank' href='http://ads.allvatar.com/adserver/www/delivery/ck.php?n=e72c25e'><img border='0' alt='' src='http://ads.allvatar.com/adserver/www/delivery/avw.php?zoneid=21&amp;n=e72c25e' /></a></noscript>";					
				}
			}else
			{
				if (($l == 'german') || ($d == 'german'))
				{
					$buttom_code = "<script type='text/javascript'><!--// <![CDATA[
								    /* [id25] Allgemein Deutsch 728x90 */
								    OA_show(25);
									// ]]> --></script><noscript><a target='_blank' href='http://ads.allvatar.com/adserver/www/delivery/ck.php?n=0529b4c'><img border='0' alt='' src='http://ads.allvatar.com/adserver/www/delivery/avw.php?zoneid=25&amp;n=0529b4c' /></a></noscript>";
					$left_code = "<script type='text/javascript'><!--// <![CDATA[
								  /* [id24] Allgemein Deutsch 120x600 */
								  OA_show(24);
								  // ]]> --></script><noscript><a target='_blank' href='http://ads.allvatar.com/adserver/www/delivery/ck.php?n=ad3e157'><img border='0' alt='' src='http://ads.allvatar.com/adserver/www/delivery/avw.php?zoneid=24&amp;n=ad3e157' /></a></noscript>";			
				}else {
					$buttom_code = "<script type='text/javascript'><!--// <![CDATA[
								    /* [id27] Allgemein Englisch 728x90 */
								    OA_show(27);
									// ]]> --></script><noscript><a target='_blank' href='http://ads.allvatar.com/adserver/www/delivery/ck.php?n=f9495b0'><img border='0' alt='' src='http://ads.allvatar.com/adserver/www/delivery/avw.php?zoneid=27&amp;n=f9495b0' /></a></noscript>";
					$left_code = "<script type='text/javascript'><!--// <![CDATA[
				   					/* [id26] Allgemein Englisch 120x600 */
				    				OA_show(26);
									// ]]> --></script><noscript><a target='_blank' href='http://ads.allvatar.com/adserver/www/delivery/ck.php?n=053eaf4'><img border='0' alt='' src='http://ads.allvatar.com/adserver/www/delivery/avw.php?zoneid=26&amp;n=053eaf4' /></a></noscript>";
				}
			}

			$left = '<br><table width="100%" border="0" cellspacing="1" cellpadding="2">';
			$left .= '<tr><td align=center>';
			$left .= $left_code ;
			$left .= '</td></tr>';
			$left .= '<tr><td align=center><a class="small" target="_self" href="'.$eqdkp_root_path.'ads.php">'.$user->lang['ads_remove'].'</a></td></tr>';
			$left .= '</table>';

			$buttom = '<table width="100%" border="0" cellspacing="1" cellpadding="2">';
			$buttom .= '<tr><td align=center>';
			$buttom .= $buttom_code ;
			$buttom .= '</td></tr>';
			$buttom .= '</table>';
			
			$this->buttom = $buttom;
			$this->left = $left;
			$tpl->assign_var('H',$buttom);
		}
		
		$g = '<script type="text/javascript">
				var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
				document.write(unescape("%3Cscript src=\'" + gaJsHost + "google-analytics.com/ga.js\' type=\'text/javascript\'%3E%3C/script%3E"));
				</script>
		
				<script type="text/javascript">
				var pageTracker = _gat._getTracker("UA-3916299-1");
				pageTracker._initData();
				pageTracker._trackPageview();
				</script>';
		
		
   		if ($_HMODE) 
    	{
			$g ='	<script type="text/javascript">
					try {
					var pageTracker = _gat._getTracker("UA-7108574-1");
					pageTracker._trackPageview();
					} catch(err) {}</script>
			
					<script src=\'http://www.google-analytics.com/urchin.js\' type=\'text/javascript\'></script><script type="text/javascript"> _uacct = "UA-2163237-1"; urchinTracker(); </script>			
			
					<script type="text/javascript">
					var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
					document.write(unescape("%3Cscript src=\'" + gaJsHost + "google-analytics.com/ga.js\' type=\'text/javascript\'%3E%3C/script%3E"));
					</script>
					
			';
    	}
    	
		$tpl->assign_var('G',$g);
		
	}

}

