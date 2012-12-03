<?php
 /*
 * Project:     EQdkp-Plus
 * License:     Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:	     	http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:       2009
 * Date:        $Date$
 * -----------------------------------------------------------------------
 * @author      $Author$
 * @copyright   2009 sz3
 * @link        http://eqdkp-plus.com
 * @package     eqdkp-plus
 * @version     $Rev$
 * 
 * $Id$
 */

if ( !defined('EQDKP_INC') ){
  header('HTTP/1.0 404 Not Found');exit;
}

if ( !class_exists( "prunebackups_crontask" ) ) {
  class prunebackups_crontask extends crontask {
		
		var $options = array(
			'days'	=> array(
				'lang'	=> 'Delete Backups older than x days',
				'name'	=> 'days',
				'type'	=> 'int',
				'size'	=> 3,
			),
			'count'	=> array(
				'lang'	=> 'Delete more than x backups',				 
				'name'	=> 'count',
				'type'	=> 'int',
				'size'	=> 3,
			),
		);
		
		public function __construct(){
			$this->defaults['repeat'] = true;
			$this->defaults['repeat_type'] = 'daily';
			$this->defaults['editable'] = true;
			$this->defaults['description'] = 'Prune MySQL Backups';
    }
		
		
    public function run(){
      global $core, $db, $pcache, $backup, $timekeeper;
			
			$crons = $timekeeper->list_crons();
			$params = $crons['prunebackups']['params'];
  		
			$backup->prune_backups($params['days'], $params['count']);
  		
    }	
  }
}
?>