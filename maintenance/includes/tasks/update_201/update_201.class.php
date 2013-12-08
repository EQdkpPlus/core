<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2011
 * Date:		$Date: 2013-03-03 18:38:23 +0100 (So, 03 Mrz 2013) $
 * -----------------------------------------------------------------------
 * @author		$Author: wallenium $
 * @copyright	2006-2011 EQdkp-Plus Developer Team
 * @link		http://eqdkp-plus.com
 * @package		eqdkp-plus
 * @version		$Rev: 13156 $
 *
 * $Id: update_1105.class.php 13156 2013-03-03 17:38:23Z wallenium $
 */

if ( !defined('EQDKP_INC') ){
  header('HTTP/1.0 404 Not Found');exit;
}

include_once(registry::get_const('root_path').'maintenance/includes/sql_update_task.class.php');

class update_201 extends sql_update_task {
	public $author		= 'Hoofy';
	public $version		= '2.0.1'; //new plus-version
	public $name		= 'Change saving of portal-module settings';
	
	public function __construct(){
		parent::__construct();

		$this->langs = array(
			'english' => array(
				'update_201' => 'Change saving of portal-module settings',
				'update_function' => 'Port portal-module settings',
			),
		);
		
		// init SQL querys
		$this->sqls = array();
	}
	 
	public function update_function() {
		
		$settings_conv = array(
			'latestsposts' => array(
				'pk_latestposts_bbmodule'	=> 'bbmodule',
				'pk_latestposts_dbprefix'	=> 'dbprefix',
				'pk_latestposts_dbmode' 	=> 'dbmode',
				'pk_latestposts_dbhost' 	=> 'dbhost',
				'pk_latestposts_dbname' 	=> 'dbname',
				'pk_latestposts_dbuser' 	=> 'dbuser',
				'pk_latestposts_dbpassword' => 'dbpassword',
				'pk_latestposts_url' 		=> 'url',
				'pk_latestposts_trimtitle' 	=> 'trimtitle',
				'pk_latestposts_amount' 	=> 'amount',
				'pk_latestposts_linktype' 	=> 'linktype',
				'pk_latestposts_blackwhitelist' => 'blackwhitelist'
			),
			'offi_conf' => array(
				'pk_oc_type' 	=> 'type',
				'pk_oc_period' 	=> 'period',
				'pk_oc_day' 	=> 'day',
				'pk_oc_date' 	=> 'date',
				'pk_oc_time_type' => 'time_type',
				'pk_oc_time' 	=> 'time'
			),
		);
		
		/* standalone code to get output for settings conversion
	$data =array(
		// paste old settings here
	);
	
	foreach ($data as $key => $stuff) {
		echo "'".$key."'\t \t => '".str_replace('pk_oc_', '', $key)."',<br />";
	}
	*/
		
		//Clear cache
		$this->pdc->flush();
		
		return true;
	}
}
?>