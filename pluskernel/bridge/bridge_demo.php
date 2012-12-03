<?php
 /*
 * Project:     EQdkp-Plus
 * License:     Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:       11 June 2007
 * Date:        $Date:  $
 * -----------------------------------------------------------------------
 * @author      $Author:  $
 * @copyright   2006-2008 Corgan - Stefan Knaak | Wallenium & the EQdkp-Plus Developer Team
 * @link        http://eqdkp-plus.com
 * @package     eqdkp-plus
 * @version     $Rev: 516 $
 * 
 * $Id:  $
 */

define('EQDKP_INC', true);

//dependent on your folder change to rootpath"
$eqdkp_root_path = '../../';

//include the settings and class files
include('bridge_common.php');
include_once($eqdkp_root_path . 'includes/db/mysql.php');
include_once('bridge_class.php');

//create a new data-bridge
$bridge = new eqdkp_bridge() ;


# Demo functions:
##################################

#Eqdkp Infos: items, raid, aso.
echo "<hr>Eqdkp Status <br>" ;
print_a($bridge->get_eqdkp_status());

#next raids
echo "<hr>Next 10 Raids <br>" ;
print_a($bridge->get_next_raids(10));

#last items
echo "<hr>The last 10 Items <br>" ;
print_a($bridge->get_last_items(10));

#last raids
echo "<hr>The Last 10 Raids <br>" ;
print_a($bridge->get_last_Group_Raids(10));

echo "get_GroupDKPInfo";
print_a($bridge->get_GroupDKPInfo());

echo "get_last_Group_Raids";
print_a($bridge->get_last_Group_Raids(10));

echo "get_next_raids";
print_a($bridge->get_next_raids(10));

echo "get_is_player_signin_raid";
print_a($bridge->get_is_player_signin_raid('Corgan', 33));

#Member Infos:
###############

echo "the last 10 member items get_last_member_items";
print_a($bridge->get_last_member_items("Corgan",10));

echo "the last 10 raids from a member get_last_Member_Raids";
print_a($bridge->get_last_Member_Raids("Corgan",10));

# return the member DKP
echo "get_memberInfo";
print_a($bridge->get_memberInfo('Corgan'));

#Admin Stuff
###############

#checked if the user corgan exist
#return fals in not
#var_dump($bridge->check_is_user('corgan'));


# create functions
# if ok, return true
# if return = arry, then there was an error. the error includet in the return array
########################################################################

#create user
#($is_admin, $user_name, $user_pw, $user_email)
#$bridge->new_user(1, 'corgan', md5('admin'), 'corgan@allvatar.com');

#create member
#$bridge->new_member($member_name, $member_level, $member_race, $member_class)
#$bridge->new_member(70, 'Human', 'Warlock');

#delete member
#$bridge->delete_member('Sdfsd');

#delete user
#$bridge->delete_user('sdfsdf');



###############################################
# debug function - just ignore
###############################################

 function print_a( $TheArray )
  { // Note: the function is recursive

  	if(!is_array($TheArray))
  	{return "no array";}

    echo "<table border=0>\n";

    $Keys = array_keys( $TheArray );
    foreach( $Keys as $OneKey )
    {
      echo "<tr>\n";

      echo "<td bgcolor='#727450'>";
      echo "<B>" . $OneKey . "</B>";
      echo "</td>\n";

      echo "<td bgcolor='#C4C2A6'>";
        if ( is_array($TheArray[$OneKey]) )
          print_a($TheArray[$OneKey]);
        else
          echo $TheArray[$OneKey];
      echo "</td>\n";

      echo "</tr>\n";
    }
    echo "</table>\n";
  }



?>