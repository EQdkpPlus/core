<?php
 /*
 * Project:     EQdkp-Plus
 * License:     Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		    http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:       2007
 * Date:        $Date$
 * -----------------------------------------------------------------------
 * @author      $Author$
 * @copyright   2007-2008 sz3
 * @link        http://eqdkp-plus.com
 * @package     eqdkp-plus
 * @version     $Rev$
 * 
 * $Id$
 */
 
if ( !defined('EQDKP_INC') )
{
     die('Do not access this file directly.');
}

if ( !class_exists( "html_leaderboard" ) ) {
  class html_leaderboard{
    
      private $mdkpid;
      private $vpre;
      
      public function get_html_leaderboard($mdkpid, $class_array, $class_break = 8, $filter_zeros = -1, $max_member = 0, $hide_inaktive = -1, $sort = 'asc'){
      global $core, $pdh;
        
        if($mdkpid == 0)
          return '';
        
        if($max_member <= 0){
          if(($core->config['pk_leaderboard_limit'] == '') or ($core->config['pk_leaderboard_limit'] == 0)){
            $max_member = 999;
          }else{
            $max_member = intval($core->config['pk_leaderboard_limit']);
          }
        }
        
        if($filter_zeros == -1){
          $filter_zeros = $core->config['pk_leaderboard_hide_zero'];
        }
        
        if($hide_inaktive == -1){
          $hide_inaktive = $core->config['hide_inactive'];
        }
        
        $this->mdkpid = $mdkpid;
        
        //sort direction
        $this->sort_direction = ($sort == 'asc') ? 1 : -1;
        
        //system dependant output
        $this->vpre = $pdh->pre_process_preset('current', array(), 0);
        
        $view_list = $pdh->get('member', 'id_list', array(false, false));

        $class_list = array();
        foreach($view_list as $member_id){
          $class_id = $pdh->get('member', 'classid', array($member_id));
          if( in_array($class_id, $class_array) ){
            $class_list[$class_id][] = $member_id;
          }        
        }
        
        foreach(array_keys($class_list) as $class){
          usort($class_list[$class], array(&$this, "sort_by_points"));
        }

        foreach($class_array as $class_id){
          $filtered_class_list[$class_id] = $class_list[$class_id];
        }
        
        
        $leaderboard = '<table width="100%" border="0" cellpadding="1" cellspacing="1"><tr><th colspan="22">Leaderboard</th></tr><tr>';
        
        $classnr = 0;
        foreach($filtered_class_list as $class_id => $member_ids){
          $leaderboard .= '<td align="center" nowrap="nowrap" valign="top"><table class="borderless" border="0" cellpadding="2" cellspacing="0" width="100%"><tr><th colspan="2">'.$pdh->get('game', 'name', array('classes', $class_id)).'</th></tr>';
          
          $rows = ($max_member < count($member_ids))? $max_member : count($member_ids);
          for($i=0; $i<$rows; $i++){
            $leaderboard .= '<tr class="'.$core->switch_row_class().'"><td align="left">'.$pdh->geth('member', 'memberlink', array($member_ids[$i], 'viewcharacter.php', '', true, true)).'</td><td align="right">'.$pdh->geth($this->vpre[0], $this->vpre[1], $this->vpre[2], array('%member_id%' => $member_ids[$i], '%dkp_id%' => $mdkpid)).'</td></tr>';
          }
          $leaderboard .= '</table></td>';
          $classnr++;
          if($classnr == $class_break){
            $classnr = 0;
            $leaderboard .= '</tr><tr>';
          }
        }
        
        $leaderboard .= '</tr></table>';
        return $leaderboard;
      }
     
      private function sort_by_points($a, $b){
        global $pdh;
        //return $pdh->comp('points', 'current', -1, array($a, $this->mdkpid ), array($b, $this->mdkpid ));
        $params1 = $pdh->post_process_preset($this->vpre[2], array('%member_id%' => $a, '%dkp_id%' => $this->mdkpid));
        $params2 = $pdh->post_process_preset($this->vpre[2], array('%member_id%' => $b, '%dkp_id%' => $this->mdkpid));
        
        return $pdh->comp($this->vpre[0], $this->vpre[1], $this->sort_direction, $params1, $params2);
      }
  }//end class
}//end if
?>
