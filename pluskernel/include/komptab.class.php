<?php
/******************************
 * EQDKP PLUGIN: PLUSkernel
 * (c) 2007 by EQDKP Plus Dev Team
 * originally written by S.Wallmann
 * http://www.eqdkp-plus.com
 * ------------------
 * komptab.class.php
 * Start: 2006
 * $Id$
 *
 * This script is part of the kompCMS
 * Framework by kompsoft.de, written
 * by Simon Wallmann
 ******************************/

if ( !defined('EQDKP_INC') ){
    header('HTTP/1.0 404 Not Found');exit;
}

class kompTabs
{

  function startPane($id)
	{
		$tab = "<div class='tab-pane' id='".$id."'>";
		return $tab;
	}

	/**
	* Ends Tab Pane
	*/
	function endPane() {
		$tab = "</div>";
		return $tab;

	}

	/*
	* Creates a tab with title text and starts that tabs page
	* @param tabText - This is what is displayed on the tab
	* @param paneid - This is the parent pane to build this tab on
	*/

	function startTab( $tabText, $paneid ) {
		$tab = "<div class='tab-page'><h2 class='tab' id='".$paneid."'>".$tabText."</h2>";

		return $tab;
	}

	/*
	* Ends a tab page
	*/
	function endTab() {

		$tab = "</div>";

		return $tab;
	}



function startTable() {
		$tab = "<table border=0 cellpadding=1 cellspacing=1 width=100%>";
		return $tab;
	}

	/*
	* Ends a tab page
	*/
	function endTable() {

		$tab = "</table>";
		return $tab;
	}

function tableheader($text) {
		$tab = "<tr><td align='left' colspan=2></td> </tr>";
		$tab .= "<tr class=row2><th align='left' colspan=2>".$text."</td> </tr>";

		return $tab;
	}



}
?>
