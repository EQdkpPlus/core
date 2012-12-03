<?php
class CharTools
{
	function updateConnection($member_id)
	{
	global $db, $user, $config;
	// Users -> Members associations
        $sql = 'DELETE FROM ' . MEMBER_USER_TABLE . '
                WHERE user_id = ' . $user->data['user_id'];
        $db->query($sql);

        if ( (isset($_POST['member_id'])) && (is_array($_POST['member_id'])) )
        {
            $sql = 'INSERT INTO ' . MEMBER_USER_TABLE . '
                    (member_id, user_id)
                    VALUES ';

            $query = array();
            foreach ( $_POST['member_id'] as $member_id )
            {
                $query[] = '(' . $member_id . ', ' . $user->data['user_id'] . ')';
            }

            $sql .= implode(', ', $query);
            $db->query($sql);
            return true;
        }else{
            return false;
        }
        
	}
	function addChar($membername)
	{
		global $db, $user, $table_prefix;
		if (!defined('MEMBER_ADDITION_TABLE')) { define('MEMBER_ADDITION_TABLE', $table_prefix . 'member_additions'); }
          // Make sure that each member's name is properly capitalized
        $member_name = strtolower(preg_replace('/[[:space:]]/i', ' ', $membername));
        $member_name = ucwords($member_name);

	// Check for existing member name
	$sql = "SELECT member_id FROM " . MEMBERS_TABLE ." WHERE member_name = '".$member_name."'";
	$member_id = $db->query_first($sql);

	// Error out if member name exists
	     if ( isset($member_id) && $member_id > 0 ) {
          $failure_message = array('false',$member_name,$member_id);
          return $failure_message;
        }else{
          $failure_message = array('true','','');
        }

				// Add into members table
        $query = $db->build_query('INSERT', array(
            'member_name'       => $member_name,
            'member_earned'     => 0,
            'member_spent'      => 0,
            'member_adjustment' => 0,
            'member_firstraid'  => 0,
            'member_lastraid'   => 0,
            'member_raidcount'  => 0,
            'member_level'      => $_POST['member_level'],
            'member_race_id'    => $_POST['member_race_id'],
            'member_class_id'   => $_POST['member_class_id'])
        );
        $blubb = $db->query('INSERT INTO ' . MEMBERS_TABLE . $query);
        
        // get the member ID
        $memberid_sql = "SELECT member_id FROM ".MEMBERS_TABLE." WHERE member_name='".$member_name."'";
        $member_result = $db->query($memberid_sql);
        $row = $db->fetch_record($member_result);
        
				// add into members_addition table
				$query = $db->build_query('INSERT', array(
            'member_id'       => $row[0],
            'picture'     		=> $_POST['member_pic'],
            'fir'							=> $_POST['fire'],
            'nr'							=> $_POST['nature'],
            'sr'							=> $_POST['shadow'],
            'ar'							=> $_POST['arcane'],
            'frr'							=> $_POST['ice'],
            'skill_1'					=> $_POST['skill_1'],
            'skill_2'					=> $_POST['skill_2'],
            'skill_3'					=> $_POST['skill_3'],
            'guild'						=> $_POST['guild'],
            'blasc_id'				=> $_POST['blasc_id'],
            'ct_profile'			=> $_POST['ct_profile'],
            'curse_profiler'	=> $_POST['curse_profiler'],
            'allakhazam'			=> $_POST['allakhazam'],
            'talentplaner'		=> $_POST['talentplaner'],
            )
        );
        $blubb = $db->query('INSERT INTO ' . MEMBER_ADDITION_TABLE . $query);        
        
        // set the char to the user if he wants it ;)
        if ($_POST['overtakeuser']){
        $sql = "SELECT member_id FROM " . MEMBERS_TABLE ." WHERE member_name = '".$member_name."'";
	      $member_id = $db->query_first($sql);
          $sql = 'INSERT INTO ' . MEMBER_USER_TABLE . '
                    (member_id, user_id)
                    VALUES (' . $member_id . ', ' . $user->data['user_id'] . ')';
          $db->query($sql);
        }
        
        if ($blubb){
          return $failure_message;
        }else{
          $failure_message = array('false','','');
          return $failure_message;
        }
	} // end of add

function updateChar($memberid)
	{
		global $db, $user, $table_prefix;
        if (!defined('MEMBER_ADDITION_TABLE')) { define('MEMBER_ADDITION_TABLE', $table_prefix . 'member_additions'); }
          // Make sure that each member's name is properly capitalized
        $member_name = strtolower(preg_replace('/[[:space:]]/i', ' ', $_POST['member_name']));
        $member_name = ucwords($member_name);

        $query = $db->build_query('UPDATE', array(
            'member_name'       => $member_name,
            'member_level'      => $_POST['member_level'],
            'member_race_id'    => $_POST['member_race_id'],
            'member_class_id'   => $_POST['member_class_id'])
        );
        $blubb = $db->query('UPDATE ' . MEMBERS_TABLE . ' SET ' . $query . " WHERE member_id='" . $memberid . "'");
        
        // check  if its an update or an new entry in the additional table
        $memberadd_sql = "SELECT member_id FROM ".MEMBER_ADDITION_TABLE." WHERE member_id='" . $memberid . "'";
        $additional_result = $db->query($memberadd_sql);
        if (!$db->fetch_record($additional_result)){
        	$query = $db->build_query('INSERT', array(
            'member_id'       => $memberid,
            'picture'     		=> $_POST['member_pic'],
            'fir'							=> $_POST['fire'],
            'nr'							=> $_POST['nature'],
            'sr'							=> $_POST['shadow'],
            'ar'							=> $_POST['arcane'],
            'frr'							=> $_POST['ice'],
            'skill_1'					=> $_POST['skill_1'],
            'skill_2'					=> $_POST['skill_2'],
            'skill_3'					=> $_POST['skill_3'],
            'guild'						=> $_POST['guild'],
            'blasc_id'				=> $_POST['blasc_id'],
            'ct_profile'			=> $_POST['ct_profile'],
            'curse_profiler'	=> $_POST['curse_profiler'],
            'allakhazam'			=> $_POST['allakhazam'],
            'talentplaner'		=> $_POST['talentplaner'],
            )
        );
        $blubb = $db->query('INSERT INTO ' . MEMBER_ADDITION_TABLE . $query);
        } else {
        // delete the old picture
        $memberid_sql = "SELECT picture FROM ".MEMBER_ADDITION_TABLE." WHERE member_id='" . $memberid . "'";
        $member_result = $db->query($memberid_sql);
        $row = $db->fetch_record($member_result);
        if ($row[0] != $_POST['member_pic']){
        	$dateiname="images/upload/".$row[0];
        	echo $dateiname;
					unlink($dateiname);
				}
        
				// add into members_addition table
				$query = $db->build_query('UPDATE', array(
            'picture'     		=> $_POST['member_pic'],
            'fir'							=> $_POST['fire'],
            'nr'							=> $_POST['nature'],
            'sr'							=> $_POST['shadow'],
            'ar'							=> $_POST['arcane'],
            'frr'							=> $_POST['ice'],
            'skill_1'					=> $_POST['skill_1'],
            'skill_2'					=> $_POST['skill_2'],
            'skill_3'					=> $_POST['skill_3'],
            'guild'						=> $_POST['guild'],
            'blasc_id'				=> $_POST['blasc_id'],
            'ct_profile'			=> $_POST['ct_profile'],
            'curse_profiler'	=> $_POST['curse_profiler'],
            'allakhazam'			=> $_POST['allakhazam'],
            'talentplaner'		=> $_POST['talentplaner'],
            )
        );
        //message_die('UPDATE ' . MEMBER_ADDITION_TABLE . ' SET ' . $query . " WHERE member_id='" . $memberid . "'");
        $blubb = $db->query('UPDATE ' . MEMBER_ADDITION_TABLE . ' SET ' . $query . " WHERE member_id='" . $memberid . "'");
        
        if ($blubb){
          return $failure_message = array('true','','');
        }else{
          $failure_message = array('false','','');
          return $failure_message;
        }
     }// end of if update
	} // end of update

}// end of class
?>