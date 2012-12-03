<?php
// ##################################################################################
// ####################### DKP Leaderboard AddOn by Legedric ########################
// ##################################################################################
// ############################# www.derdunklepakt.de ###############################
// ##################################################################################


// Language (en/de/fr)
if($eqdkp->config['default_game'] == 'WoW_english')
{
	define('LB_LANGUAGE', "en");
}
elseif($eqdkp->config['default_game'] == 'WoW_german')
{
	define('LB_LANGUAGE', "de");
}

function showDKPLeaderboard($multifilter)
{
	// get needed global vars
	global $eqdkp, $db, $tpl, $SID, $conf_plus, $htmlPlus;

	// Max players listed per class (0 = all)
	define('MAXLIST', $conf_plus['pk_leaderboard_limit']);

	// define classes and captions
	$wow_classes = array(
		"en" => array("Druid","Hunter","Mage","Paladin","Priest","Rogue","Shaman","Warlock","Warrior"),
		"de" => array("Druide","Jäger","Magier","Paladin","Priester","Schurke","Schamane","Hexenmeister","Krieger"),
		"fr" => array("Druide","Chasseur","Mage","Paladin","Prêtre","Voleur","Chaman","Démonistes","Guerrier")
	);

	// produce class names
	$tpl->assign_vars(array(
		'Caption_Druid'   	=> $wow_classes[LB_LANGUAGE][0],
		'Caption_Hunter'   	=> $wow_classes[LB_LANGUAGE][1],
		'Caption_Mage'   		=> $wow_classes[LB_LANGUAGE][2],
		'Caption_Paladin'   => $wow_classes[LB_LANGUAGE][3],
		'Caption_Priest'   	=> $wow_classes[LB_LANGUAGE][4],
		'Caption_Rogue'   	=> $wow_classes[LB_LANGUAGE][5],
		'Caption_Shaman'   	=> $wow_classes[LB_LANGUAGE][6],
		'Caption_Warlock'   => $wow_classes[LB_LANGUAGE][7],
		'Caption_Warrior'   => $wow_classes[LB_LANGUAGE][8])
	);
	
	

 if ($conf_plus['pk_multidkp'] == 1)
{
    	
	$sql = 'SELECT multidkp_name, multidkp_disc, multidkp_id
	        FROM ' . MULTIDKP_TABLE . '
	        WHERE multidkp_name IS NOT NULL'
	        ;
	          
	 if ( !($multi_result = $db->query($sql)) )
	 {
	 	message_die('Could not obtain MultiDKP information', '', __FILE__, __LINE__, $sql);
	 }
	 
	 $tpl->assign_block_vars('multi_row', array(
        'VALUE'    => strtolower("None"),
        'SELECTED' => ( $filter == strtolower("None") ) ? ' selected="selected"' : '',
        'OPTION'   => str_replace('_', ' ', "None"))
    );
    
    // Add in the cute ---- line, filter on None if some idiot selects it
    $tpl->assign_block_vars('multi_row', array(
        'VALUE'    => strtolower("None"),
        'SELECTED' => ( $filter == strtolower("NULL") ) ? ' selected="selected"' : '',
        'OPTION'   => str_replace('_', ' ', "--------")));
        
	  $showmultifilter = '';        
		while ( $row = $db->fetch_record($multi_result) )
		{
		 $tpl->assign_block_vars('multi_row', array(
      'VALUE' => $row['multidkp_id'],
      'SELECTED' => ( strtolower($multifilter) == strtolower($row['multidkp_id']) ) ? ' selected="selected"' : '',
      'OPTION'   => ( !empty($row['multidkp_name']) ) ? stripslashes($row['multidkp_name']) : '(None)' )
      );
      
      if(strtolower($multifilter) == strtolower($row['multidkp_id']))
      {
      $showmultifilter = " - ". stripslashes($row['multidkp_name']) ;
      }
      
		}
		
	$tpl->assign_vars(array(
    										 'SHOW_MULTI'    => true,
    										 'SHOW_MULTI_FILTER'    => $showmultifilter,
    										 ));
     	
		if ( isset($_GET['multifilter']) ) 
		{
			$html = new htmlPlus(); // plus html class 
			$member_multidkp = $html-> multiDkpAllMemberArray($_GET['multifilter']) ; // create the multiDKP Array 
			
				$tpl->assign_vars(array(
    										 'SHOW_CAPTIONS'    => true,
    										 'SHOW_BOARD'    => true
    										 ));
					
			if(!empty($member_multidkp))
			{					
				foreach ($member_multidkp as $key) 
			  { 
				 $class = 'classlist_row_'.strtolower(renameClasstoenglish($key['class'])) ;		 					 
				 $tpl->assign_block_vars($class, array(
						'NAME'          => $html->ToolTip($key['dkp_tooltip'],get_coloredLinkedName($key['name'])) , 
						'CURRENT' 			=> $html->ToolTip($key['dkp_tooltip'],$key['current']) ,
						'C_CURRENT'     => color_item(round($key['current'])),
						'U_VIEW_MEMBER' => 'viewmember.php' . $SID . '&amp;' . URI_NAME . '='.$key['name'])
				);
			 }; # end for each
		  }; # end if empty	
		}; # end if set filter					      	
    	
    	
}
else
{

	$tpl->assign_vars(array(
			'SHOW_CAPTIONS'   => true,
			'SHOW_BOARD'   => true
		)
	);

	// build up member data and produce it
	for($i=0;$i<count($wow_classes[LB_LANGUAGE]);$i++)
	{
		#echo strtolower($wow_classes['en'][$i]);
		// build sql string
		$sql = "SELECT m.*, (m.member_earned-m.member_spent+m.member_adjustment) AS member_current,
			 			member_status, r.rank_name, r.rank_hide, r.rank_prefix, r.rank_suffix,
						c.class_name AS member_class, c.class_armor_type AS armor_type,
			 			c.class_min_level AS min_level, c.class_max_level AS max_level
				FROM " . MEMBERS_TABLE . " m, " . MEMBER_RANKS_TABLE . " r, " . CLASS_TABLE . " c
				WHERE c.class_id = m.member_class_id
					AND (m.member_rank_id = r.rank_id)
					AND c.class_name =  '".$wow_classes[LB_LANGUAGE][$i]."'
					AND rank_hide = '0'";

		// Are we hiding inactive members?
		if ( $eqdkp->config['hide_inactive'] == '1' )
			$sql .= " AND member_status <> '0'";

		$sql .= " ORDER BY member_current desc";

		// add limit if set
		if (MAXLIST > 0)
			$sql .= " LIMIT 0,".MAXLIST;


		if ( !($class_result = $db->query($sql)) )
		{
			message_die('Could not obtain member information', '', __FILE__, __LINE__, $sql);
		}

		// produce output
		while ( $row = $db->fetch_record($class_result) )
		{
			$tpl->assign_block_vars('classlist_row_'.strtolower($wow_classes['en'][$i]), array(
					'NAME'          => $row['rank_prefix'] . (( $row['member_status'] == '0' ) ? '<i>' . get_coloredLinkedName($row['member_name']) . '</i>' : get_coloredLinkedName($row['member_name'])) . $row['rank_suffix'],
					'CURRENT'       => round($row['member_current']),
					'C_CURRENT'     => color_item(round($row['member_current'])),
					'U_VIEW_MEMBER' => 'viewmember.php' . $SID . '&amp;' . URI_NAME . '='.$row['member_name'])
			);
		}
		$db->free_result($class_result);
	}
} # end not multi

	     //===========================
		 // count shaman
		$sql = 'SELECT count(member_race_id)
				FROM ' . MEMBERS_TABLE . '
				WHERE (member_class_id = 8) or (member_class_id = 9)' ;

		$result = $db->query($sql);
		$shaman_count = $db->fetch_record($result) ;


		$db->free_result($result);
	    //===========================

	     //===========================
		 // count pala

		$sql = 'SELECT count(member_race_id)
				FROM ' . MEMBERS_TABLE . '
				WHERE (member_class_id = 5) or (member_class_id = 13)' ;

		$result = $db->query($sql);
		$pala_count = $db->fetch_record($result) ;


		$db->free_result($result);
	    //===========================

if($shaman_count[0]>0)
{
	$ishorde = true ;
}
else
{
	$ishorde = false;
}

if($pala_count[0]>0)
{
	$isalliance = true ;
}
else
{
	$isalliance = false;
}


	// produce faction
	$tpl->assign_vars(array(
		'S_ISALLIANCE'   => $isalliance,
		'S_ISHORDE'   => $ishorde)
	);
}


?>