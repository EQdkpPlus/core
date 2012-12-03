<?php
/******************************
 * EQDKP PLUGIN: PLUSkernel
 * (c) 2006 - 2007 by WalleniuM
 * http://www.kompsoft.de   
 * ------------------
 * komptab.class.php
 * Changed: March 11, 2007
 *
 * This script is part of the kompCMS
 * Framework by kompsoft.de, written
 * by Simon Wallmann
 * 
 ******************************/
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
}
?>
