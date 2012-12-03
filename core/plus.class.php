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


if ( !defined('EQDKP_INC') ){
    header('HTTP/1.0 404 Not Found');exit;
}
if(!class_exists('plus')) {
  class plus
  {

	function createVTable($misc,$header)
    {
      global $eqdkp_root_path, $tpl, $jquery;
      $id = 'coll'.strtolower($header);
      $start = '<table width="100%" border="0" cellpadding="0" cellspacing="0" class="forumline">
			  	        <tr>
	  			   	      <th class="smalltitle" align="left" style="padding:3px;">
                      '.$jquery->Collapse($id).' <span align="center" id="txtrecruitment">'.$header.'</span>
                      
                    </th>
	  			        </tr>
                  <tr>
                    <td>
                      <div id="'.$id.'" style="display:show">
                        <table width="100%" border="0" cellspacing="1" cellpadding="2" class="borderless">';
      $end = "          </table>
                      </div>
                    </td>
                  </tr>
                </table>";
        $out = $start.$misc.$end ;

        return $out;
    }

    
  function create_shop_link($nick,$class_id, $race_id, $gilde, $realm, $level, $gender)
{

	global $user ,$eqdkp_root_path, $core, $game, $pdh;
	
	$gender = ($gender=='Female') ? 'w' : 'm';

	$class_name = $game->get_name('classes', $class_id, 'english');
	$race_nam  = $game->get_name('races', $race_id, 'english');

	$motivid['warrior_troll'] = 6693;
	$motivid['warrior_night elf'] = 6695;
	$motivid['warrior_undead'] = 6694;
	$motivid['warrior_orc'] = 6691;
	$motivid['warrior_dwarf'] = 6688;
	$motivid['warrior_draenei'] = 6687;
	$motivid['warrior_tauren'] = 6692;
	$motivid['warrior_gnome'] = 6689;
	$motivid['warlock_gnome'] = 6684;
	$motivid['warlock_undead'] = 6686;
	$motivid['warlock_human'] = 6685;
	$motivid['warlock_blood elf'] = 6683;
	$motivid['warrior_human'] = 6690;
	$motivid['shaman_orc'] = 6680;
	$motivid['shaman_troll'] = 6682;
	$motivid['shaman_draenei'] = 6678;
	$motivid['shaman_tauren'] = 6681;
	$motivid['hunter_troll'] = 6653;
	$motivid['hunter_night elf'] = 6650;
	$motivid['hunter_dwarf'] = 6649;
	$motivid['hunter_orc'] = 6651;
	$motivid['hunter_blood elf'] = 6648;
	$motivid['hunter_tauren'] = 6652;
	$motivid['mage_human'] = 6657;
	$motivid['mage_draenei'] = 6655;
	$motivid['mage_blood elf'] = 6654;
	$motivid['mage_gnome'] = 6656;
	$motivid['mage_undead'] = 6659;
	$motivid['mage_troll'] = 6658;
	$motivid['priest_blood elf'] = 6663;
	$motivid['priest_human'] = 6666;
	$motivid['priest_draenei'] = 6664;
	$motivid['priest_dwarf'] = 6665;
	$motivid['priest_night elf'] = 6667;
	$motivid['priest_troll'] = 6668;
	$motivid['priest_undead'] = 6669;
	$motivid['paladin_dwarf'] = 6661;
	$motivid['paladin_blood elf'] = 6660;
	$motivid['paladin_human'] = 6662;
	$motivid['rogue_troll'] = 6676;
	$motivid['rogue_human'] = 6673;
	$motivid['rogue_night elf'] = 6674;
	$motivid['rogue_orc'] = 6675;
	$motivid['rogue_undead'] = 6677;
	$motivid['rogue_gnome'] = 6672;
	$motivid['rogue_blood elf'] = 6670;
	$motivid['rogue_dwarf'] = 6671;
	$motivid['druid_tauren'] = 6647;
	$motivid['druid_night elf'] = 6646;

	$motivid_fem['druid_night elf']=8074;
	$motivid_fem['druid_tauren']=8075;
	$motivid_fem['hunter_blood elf']=8076;
	$motivid_fem['hunter_dwarf']=8077;
	$motivid_fem['hunter_night elf']=848785;
	$motivid_fem['hunter_orc']=8079;
	$motivid_fem['hunter_tauren']=8080;
	$motivid_fem['hunter_troll']=8081;
	$motivid_fem['mage_blood elf']=8082;
	$motivid_fem['mage_draenei']=8083;
	$motivid_fem['mage_troll']=8084;
	$motivid_fem['mage_gnome']=8085;
	$motivid_fem['mage_human']=8086;
	$motivid_fem['mage_undead']=8087;
	$motivid_fem['paladin_blood elf']=8088;
	$motivid_fem['paladin_dwarf']=8089;
	$motivid_fem['paladin_human']=8090;
	$motivid_fem['priest_blood elf']=8091;
	$motivid_fem['priest_draenei']=8092;
	$motivid_fem['priest_dwarf']=8093;
	$motivid_fem['priest_human']=8094;
	$motivid_fem['priest_night elf']=8095;
	$motivid_fem['priest_troll']=8096;
	$motivid_fem['priest_undead']=8097;
	$motivid_fem['rouge_blood elf']=8098;
	$motivid_fem['rouge_troll']=8099;
	$motivid_fem['rouge_dwarf']=8100;
	$motivid_fem['rouge_gnome']=8101;
	$motivid_fem['rouge_human']=8102;
	$motivid_fem['rouge_night elf']=8103;
	$motivid_fem['rouge_orc']=8104;
	$motivid_fem['rouge_undead']=8105;
	$motivid_fem['shaman_draenei']=8106;
	$motivid_fem['shaman_orc']=8107;
	$motivid_fem['shaman_tauren']=8108;
	$motivid_fem['shaman_troll']=8109;
	$motivid_fem['warlock_blood elf']=8110;
	$motivid_fem['warlock_gnome']=8111;
	$motivid_fem['warlock_human']=8112;
	$motivid_fem['warlock_undead']=8113;
	$motivid_fem['warrior_dranei']=8114;
	$motivid_fem['warrior_troll']=8115;
	$motivid_fem['warrior_dwarf']=8116;
	$motivid_fem['warrior_gnome']=8117;
	$motivid_fem['warrior_human']=8118;
	$motivid_fem['warrior_night elf']=8119;
	$motivid_fem['warrior_orc']=8120;
	$motivid_fem['warrior_tauren']=8121;
	$motivid_fem['warrior_undead']=8122;


	 if ($gender == 'w')  $motivid = $motivid_fem[$class_name.'_'.$race_nam];
	 else $motivid = $motivid[$class_name.'_'.$race_nam];

	 if (!strtolower($core->config['default_game'])=='wow' )
	 {
	 	$motivid = -1 ;
	 }

	$nick = (strlen($nick)>1) ? $nick : $user->lang['your_name'] ;
	if ($nick && $nick != '') $link_ext .= '&shrModText_char='.urlencode(strtoupper($nick));
	else  $link_ext .= '&shrModText_char=';

	$level = ($level > 0) ? $level : 70 ;
	$link_ext .= '&shrModText_level='.$level;

	$realm = (strlen($realm)>1) ? $realm : $user->lang['your_server'] ;
	if ($realm && $realm != '') $link_ext .= '&shrModText_server='.urlencode(strtoupper($realm));
	else $link_ext .= '&shrModText_server=';

	$gilde = (strlen($gilde)>1) ? $gilde : $user->lang['your_guild'] ;
	if ($gilde && $gilde != '') $link_ext .= '&shrModText_guild='.urlencode(strtoupper($gilde));
	else $link_ext .= '&shrModText_guild=';

	$motivid = ($motivid > 0) ? $motivid : 6662 ;
	$link_ext .= '&shrModMotive_outline='.$motivid;

	if (strlen($nick)<1)
	{
		$shoplink = 'http://shirt-druck.shirtinator.net/myShop/produkte/?SISID=63036';
	}else
	{
		$shoplink = 'http://shirt-druck.shirtinator.net/myShop/produkte/?SISID=63036'.$link_ext;
	}

	return $shoplink;

}

function createShirtBox($member)
{
	global $eqdkp_root_path,$user,$core;

	if (($user->data['user_lang'] == 'german') or ($core->config['default_lang'] == 'german'))
	{
		$out = "<table>
					<tr>
						<td><a href='".$eqdkp_root_path."shop.php?id=".urlencode($member)."'> <img src=".$eqdkp_root_path."images/shirt.png> </a></td><td valign=top><a href='".$eqdkp_root_path."shop.php?id=".urlencode($member)."'> ".$user->lang['shirt_ad1']." </a></td>
					</tr>
				</table> ";

		return $out ;
	}
}

   /**
    * Implode wrapped version
    *
    * @param $before	before the value
    * @param $after		after the value
    * @param $glue		semicolon or other divorce-signs
    * @param $array		the data array
    * @return sanatized text
    */
    public function implode_wrapped($before, $after, $glue, $array){
	    $output = '';
	    foreach($array as $item){
	        $output .= $before . $item . $after . $glue;
	    }
	    return substr($output, 0, -strlen($glue));
		}

  }// end of class
}

?>