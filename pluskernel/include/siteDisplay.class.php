<?php
/******************************
 * Corgan
 ******************************/

if ( !defined('EQDKP_INC') )
{
    header('HTTP/1.0 404 Not Found');
    exit;
}

class siteDisplay
{
	function siteDisplay()
	{
		global $user, $eqdkp, $tpl;


		if (!check_auth_admin($user->data['user_id']) && Raidcount())
		{

			$g = strtolower($eqdkp->config['default_game']);
			$l = strtolower($user->data['user_lang']);

			#h=728x90
			#v=120x600

			if ($g == 'wow')
			{
				if ($l == 'german')
				{
					$h = '	<script language=\'JavaScript\' type=\'text/javascript\' src=\'http://h1373986.stratoserver.net/adserver/adx.js\'></script>
							<script language=\'JavaScript\' type=\'text/javascript\'>
                				<!--
							   if (!document.phpAds_used) document.phpAds_used = \',\';
							   phpAds_random = new String (Math.random()); phpAds_random = phpAds_random.substring(2,11);

							   document.write ("<" + "script language=\'JavaScript\' type=\'text/javascript\' src=\'");
							   document.write ("http://h1373986.stratoserver.net/adserver/adjs.php?n=" + phpAds_random);
							   document.write ("&amp;what=zone:16");
							   document.write ("&amp;exclude=" + document.phpAds_used);
							   if (document.referrer)
							      document.write ("&amp;referer=" + escape(document.referrer));
							   document.write ("\'><" + "/script>");
							//-->
							</script><noscript><a href=\'http://h1373986.stratoserver.net/adserver/adclick.php?n=a51a9803\' target=\'_blank\'><img src=\'http://h1373986.stratoserver.net/adserver/adview.php?what=zone:16&amp;n=a51a9803\' border=\'0\' alt=\'\'></a></noscript>';
					$v = '
					<script language=\'JavaScript\' type=\'text/javascript\' src=\'http://h1373986.stratoserver.net/adserver/adx.js\'></script>
					<script language=\'JavaScript\' type=\'text/javascript\'>
                				<!--
					   if (!document.phpAds_used) document.phpAds_used = \',\';
					   phpAds_random = new String (Math.random()); phpAds_random = phpAds_random.substring(2,11);

					   document.write ("<" + "script language=\'JavaScript\' type=\'text/javascript\' src=\'");
					   document.write ("http://h1373986.stratoserver.net/adserver/adjs.php?n=" + phpAds_random);
					   document.write ("&amp;what=zone:17");
					   document.write ("&amp;exclude=" + document.phpAds_used);
					   if (document.referrer)
					      document.write ("&amp;referer=" + escape(document.referrer));
					   document.write ("\'><" + "/script>");
					//-->
					</script><noscript><a href=\'http://h1373986.stratoserver.net/adserver/adclick.php?n=a6125be3\' target=\'_blank\'><img src=\'http://h1373986.stratoserver.net/adserver/adview.php?what=zone:17&amp;n=a6125be3\' border=\'0\' alt=\'\'></a></noscript>
					';
				}else {
					$h = '
					<script language=\'JavaScript\' type=\'text/javascript\' src=\'http://h1373986.stratoserver.net/adserver/adx.js\'></script>
					<script language=\'JavaScript\' type=\'text/javascript\'>
                		<!--
					   if (!document.phpAds_used) document.phpAds_used = \',\';
					   phpAds_random = new String (Math.random()); phpAds_random = phpAds_random.substring(2,11);

					   document.write ("<" + "script language=\'JavaScript\' type=\'text/javascript\' src=\'");
					   document.write ("http://h1373986.stratoserver.net/adserver/adjs.php?n=" + phpAds_random);
					   document.write ("&amp;what=zone:19");
					   document.write ("&amp;exclude=" + document.phpAds_used);
					   if (document.referrer)
					      document.write ("&amp;referer=" + escape(document.referrer));
					   document.write ("\'><" + "/script>");
					//-->
					</script><noscript><a href=\'http://h1373986.stratoserver.net/adserver/adclick.php?n=a0a224d8\' target=\'_blank\'><img src=\'http://h1373986.stratoserver.net/adserver/adview.php?what=zone:19&amp;n=a0a224d8\' border=\'0\' alt=\'\'></a></noscript>
					';
					$v = '
					<script language=\'JavaScript\' type=\'text/javascript\' src=\'http://h1373986.stratoserver.net/adserver/adx.js\'></script>
					<script language=\'JavaScript\' type=\'text/javascript\'>
                		<!--
					   if (!document.phpAds_used) document.phpAds_used = \',\';
					   phpAds_random = new String (Math.random()); phpAds_random = phpAds_random.substring(2,11);

					   document.write ("<" + "script language=\'JavaScript\' type=\'text/javascript\' src=\'");
					   document.write ("http://h1373986.stratoserver.net/adserver/adjs.php?n=" + phpAds_random);
					   document.write ("&amp;what=zone:18");
					   document.write ("&amp;exclude=" + document.phpAds_used);
					   if (document.referrer)
					      document.write ("&amp;referer=" + escape(document.referrer));
					   document.write ("\'><" + "/script>");
					//-->
					</script><noscript><a href=\'http://h1373986.stratoserver.net/adserver/adclick.php?n=ae3b68fd\' target=\'_blank\'><img src=\'http://h1373986.stratoserver.net/adserver/adview.php?what=zone:18&amp;n=ae3b68fd\' border=\'0\' alt=\'\'></a></noscript>
					';
				}
			}elseif($g=='lotro')
			{
				if ($l == 'german')
				{
					$h = '
					<script language=\'JavaScript\' type=\'text/javascript\' src=\'http://h1373986.stratoserver.net/adserver/adx.js\'></script>
					<script language=\'JavaScript\' type=\'text/javascript\'>
                	 <!--
					   if (!document.phpAds_used) document.phpAds_used = \',\';
					   phpAds_random = new String (Math.random()); phpAds_random = phpAds_random.substring(2,11);

					   document.write ("<" + "script language=\'JavaScript\' type=\'text/javascript\' src=\'");
					   document.write ("http://h1373986.stratoserver.net/adserver/adjs.php?n=" + phpAds_random);
					   document.write ("&amp;what=zone:22");
					   document.write ("&amp;exclude=" + document.phpAds_used);
					   if (document.referrer)
					      document.write ("&amp;referer=" + escape(document.referrer));
					   document.write ("\'><" + "/script>");
					//-->
					</script><noscript><a href=\'http://h1373986.stratoserver.net/adserver/adclick.php?n=a3888bae\' target=\'_blank\'><img src=\'http://h1373986.stratoserver.net/adserver/adview.php?what=zone:22&amp;n=a3888bae\' border=\'0\' alt=\'\'></a></noscript>
					';
					$v = '
					<script language=\'JavaScript\' type=\'text/javascript\' src=\'http://h1373986.stratoserver.net/adserver/adx.js\'></script>
					<script language=\'JavaScript\' type=\'text/javascript\'>
                	 <!--
					   if (!document.phpAds_used) document.phpAds_used = \',\';
					   phpAds_random = new String (Math.random()); phpAds_random = phpAds_random.substring(2,11);

					   document.write ("<" + "script language=\'JavaScript\' type=\'text/javascript\' src=\'");
					   document.write ("http://h1373986.stratoserver.net/adserver/adjs.php?n=" + phpAds_random);
					   document.write ("&amp;what=zone:20");
					   document.write ("&amp;exclude=" + document.phpAds_used);
					   if (document.referrer)
					      document.write ("&amp;referer=" + escape(document.referrer));
					   document.write ("\'><" + "/script>");
					//-->
					</script><noscript><a href=\'http://h1373986.stratoserver.net/adserver/adclick.php?n=afe4076e\' target=\'_blank\'><img src=\'http://h1373986.stratoserver.net/adserver/adview.php?what=zone:20&amp;n=afe4076e\' border=\'0\' alt=\'\'></a></noscript>
					';
				}else {
					$h = '
					<script language=\'JavaScript\' type=\'text/javascript\' src=\'http://h1373986.stratoserver.net/adserver/adx.js\'></script>
					<script language=\'JavaScript\' type=\'text/javascript\'>
                	 <!--
					   if (!document.phpAds_used) document.phpAds_used = \',\';
					   phpAds_random = new String (Math.random()); phpAds_random = phpAds_random.substring(2,11);

					   document.write ("<" + "script language=\'JavaScript\' type=\'text/javascript\' src=\'");
					   document.write ("http://h1373986.stratoserver.net/adserver/adjs.php?n=" + phpAds_random);
					   document.write ("&amp;what=zone:23");
					   document.write ("&amp;exclude=" + document.phpAds_used);
					   if (document.referrer)
					      document.write ("&amp;referer=" + escape(document.referrer));
					   document.write ("\'><" + "/script>");
					//-->
					</script><noscript><a href=\'http://h1373986.stratoserver.net/adserver/adclick.php?n=af80b4c3\' target=\'_blank\'><img src=\'http://h1373986.stratoserver.net/adserver/adview.php?what=zone:23&amp;n=af80b4c3\' border=\'0\' alt=\'\'></a></noscript>

					';
					$v = '
					<script language=\'JavaScript\' type=\'text/javascript\' src=\'http://h1373986.stratoserver.net/adserver/adx.js\'></script>
					<script language=\'JavaScript\' type=\'text/javascript\'>
                	 <!--
					   if (!document.phpAds_used) document.phpAds_used = \',\';
					   phpAds_random = new String (Math.random()); phpAds_random = phpAds_random.substring(2,11);

					   document.write ("<" + "script language=\'JavaScript\' type=\'text/javascript\' src=\'");
					   document.write ("http://h1373986.stratoserver.net/adserver/adjs.php?n=" + phpAds_random);
					   document.write ("&amp;what=zone:21");
					   document.write ("&amp;exclude=" + document.phpAds_used);
					   if (document.referrer)
					      document.write ("&amp;referer=" + escape(document.referrer));
					   document.write ("\'><" + "/script>");
					//-->
					</script><noscript><a href=\'http://h1373986.stratoserver.net/adserver/adclick.php?n=ad56b8ca\' target=\'_blank\'><img src=\'http://h1373986.stratoserver.net/adserver/adview.php?what=zone:21&amp;n=ad56b8ca\' border=\'0\' alt=\'\'></a></noscript>
					';
				}
			}else
			{
				if ($l == 'german')
				{
					$h = '
					<script language=\'JavaScript\' type=\'text/javascript\' src=\'http://h1373986.stratoserver.net/adserver/adx.js\'></script>
					<script language=\'JavaScript\' type=\'text/javascript\'>
                	 <!--
					   if (!document.phpAds_used) document.phpAds_used = \',\';
					   phpAds_random = new String (Math.random()); phpAds_random = phpAds_random.substring(2,11);

					   document.write ("<" + "script language=\'JavaScript\' type=\'text/javascript\' src=\'");
					   document.write ("http://h1373986.stratoserver.net/adserver/adjs.php?n=" + phpAds_random);
					   document.write ("&amp;what=zone:25");
					   document.write ("&amp;exclude=" + document.phpAds_used);
					   if (document.referrer)
					      document.write ("&amp;referer=" + escape(document.referrer));
					   document.write ("\'><" + "/script>");
					//-->
					</script><noscript><a href=\'http://h1373986.stratoserver.net/adserver/adclick.php?n=a921ee0d\' target=\'_blank\'><img src=\'http://h1373986.stratoserver.net/adserver/adview.php?what=zone:25&amp;n=a921ee0d\' border=\'0\' alt=\'\'></a></noscript>
					';
					$v = '
					<script language=\'JavaScript\' type=\'text/javascript\' src=\'http://h1373986.stratoserver.net/adserver/adx.js\'></script>
					<script language=\'JavaScript\' type=\'text/javascript\'>
                	 <!--
					   if (!document.phpAds_used) document.phpAds_used = \',\';
					   phpAds_random = new String (Math.random()); phpAds_random = phpAds_random.substring(2,11);

					   document.write ("<" + "script language=\'JavaScript\' type=\'text/javascript\' src=\'");
					   document.write ("http://h1373986.stratoserver.net/adserver/adjs.php?n=" + phpAds_random);
					   document.write ("&amp;what=zone:24");
					   document.write ("&amp;exclude=" + document.phpAds_used);
					   if (document.referrer)
					      document.write ("&amp;referer=" + escape(document.referrer));
					   document.write ("\'><" + "/script>");
					//-->
					</script><noscript><a href=\'http://h1373986.stratoserver.net/adserver/adclick.php?n=a4a4b382\' target=\'_blank\'><img src=\'http://h1373986.stratoserver.net/adserver/adview.php?what=zone:24&amp;n=a4a4b382\' border=\'0\' alt=\'\'></a></noscript>
					';
				}else {
					$h = '
					<script language=\'JavaScript\' type=\'text/javascript\' src=\'http://h1373986.stratoserver.net/adserver/adx.js\'></script>
					<script language=\'JavaScript\' type=\'text/javascript\'>
                	 <!--
					   if (!document.phpAds_used) document.phpAds_used = \',\';
					   phpAds_random = new String (Math.random()); phpAds_random = phpAds_random.substring(2,11);

					   document.write ("<" + "script language=\'JavaScript\' type=\'text/javascript\' src=\'");
					   document.write ("http://h1373986.stratoserver.net/adserver/adjs.php?n=" + phpAds_random);
					   document.write ("&amp;what=zone:27");
					   document.write ("&amp;exclude=" + document.phpAds_used);
					   if (document.referrer)
					      document.write ("&amp;referer=" + escape(document.referrer));
					   document.write ("\'><" + "/script>");
					//-->
					</script><noscript><a href=\'http://h1373986.stratoserver.net/adserver/adclick.php?n=a37611c3\' target=\'_blank\'><img src=\'http://h1373986.stratoserver.net/adserver/adview.php?what=zone:27&amp;n=a37611c3\' border=\'0\' alt=\'\'></a></noscript>
					';
					$v = '
					<script language=\'JavaScript\' type=\'text/javascript\' src=\'http://h1373986.stratoserver.net/adserver/adx.js\'></script>
					<script language=\'JavaScript\' type=\'text/javascript\'>
                	 <!--
					   if (!document.phpAds_used) document.phpAds_used = \',\';
					   phpAds_random = new String (Math.random()); phpAds_random = phpAds_random.substring(2,11);

					   document.write ("<" + "script language=\'JavaScript\' type=\'text/javascript\' src=\'");
					   document.write ("http://h1373986.stratoserver.net/adserver/adjs.php?n=" + phpAds_random);
					   document.write ("&amp;what=zone:26");
					   document.write ("&amp;exclude=" + document.phpAds_used);
					   if (document.referrer)
					      document.write ("&amp;referer=" + escape(document.referrer));
					   document.write ("\'><" + "/script>");
					//-->
					</script><noscript><a href=\'http://h1373986.stratoserver.net/adserver/adclick.php?n=ae94a4f9\' target=\'_blank\'><img src=\'http://h1373986.stratoserver.net/adserver/adview.php?what=zone:26&amp;n=ae94a4f9\' border=\'0\' alt=\'\'></a></noscript>

					';
				}
			}

			$out .= '<table width="100%" border="0" cellspacing="1" cellpadding="2">';
			$out .= '<tr><td>';
			$out .= $v ;
			$out .= '</td></tr>';
			$out .= '</table>';

			$tpl->assign_var('H',$h);
			$tpl->assign_var('V',$out);
		}


		$g = '
		<script type="text/javascript">
		var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
		document.write(unescape("%3Cscript src=\'" + gaJsHost + "google-analytics.com/ga.js\' type=\'text/javascript\'%3E%3C/script%3E"));
		</script>

		<script type="text/javascript">
		var pageTracker = _gat._getTracker("UA-3916299-1");
		pageTracker._initData();
		pageTracker._trackPageview();
		</script>
		';

		$tpl->assign_var('G',$g);


	}



}