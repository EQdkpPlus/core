<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2011
 * Date:		$Date$
 * -----------------------------------------------------------------------
 * @author		$Author$
 * @copyright	2006-2011 EQdkp-Plus Developer Team
 * @link		http://eqdkp-plus.com
 * @package		eqdkp-plus
 * @version		$Rev$
 *
 * $Id$
 */

if ( !defined('EQDKP_INC') ){
  header('HTTP/1.0 404 Not Found');exit;
}

include_once(registry::get_const('root_path').'maintenance/includes/sql_update_task.class.php');

class update_1101 extends sql_update_task {
	public $author		= 'Wallenium';
	public $version		= '1.1.0.1'; //new plus-version
	public $name		= '1.1.0 Update 1';
	
	protected $fields2change	= array(
		'__calendars'		=> array('field' => array('color'), 'id' => 'id'),
		'__news_categories'	=> array('field' => array('category_color'), 'id' => 'category_id'),
		'__styles'			=> array('field' => array(
			'body_background',
			'body_link',
			'body_hlink',
			'header_link',
			'header_hlink',
			'tr_color1',
			'tr_color2',
			'th_color1',
			'fontcolor1',
			'fontcolor2',
			'fontcolor3',
			'fontcolor_neg',
			'fontcolor_pos',
			'table_border_color',
			'input_color',
			'input_border_color',
		), 'id' => 'style_id')
	);
	
	public static function __shortcuts() {
		$shortcuts = array('time');
		return array_merge(parent::__shortcuts(), $shortcuts);
	}
	public function __construct(){
		parent::__construct();

		$this->langs = array(
			'english' => array(
				'update_1101'		=> 'EQdkp Plus 1.1.0 Update 1',
				'update_function'	=> 'Change all colour fields to be HTML5 compliant',
			),
			'german' => array(
				'update_1101'		=> 'EQdkp Plus 1.1.0 Update 1',
				'update_function'	=> 'Ändere alle Farbfelder um dem HTML5 Standard zu entsprechen',
			),
		);
	}

	public function update_function() {
		foreach($this->fields2change as $dbtable=>$dbfields){
			foreach($dbfields['field'] as $dbfieldvalue){
				$this->db->query('ALTER TABLE '.$dbtable.' CHANGE `'.$dbfieldvalue.'` `'.$dbfieldvalue.'` VARCHAR(10)');

				// now, lets change the values
				$sql	= 'SELECT '.$dbfieldvalue.' as mycolorvalue, '.$dbfields['id'].' as mycolorid FROM '.$dbtable.';';
				$query = $this->db->query($sql);
				$update = array();
				while ($row = $this->db->fetch_record($query)) {
					if(trim($row['mycolorvalue']) != ''){
						// check if the # is already in the value
						if (preg_match('/^#[a-f0-9]{6}$/i', $row['mycolorvalue'])) {
							continue;
						}else if (preg_match('/^[a-f0-9]{6}$/i', $row['mycolorvalue'])) {
							$sql = "UPDATE ".$dbtable." SET ".$dbfieldvalue." = '#".$row['mycolorvalue']."' WHERE ".$dbfields['id']." = '".$row['mycolorid']."';";
							$this->db->query($sql);
						}
					}
				}
			}
		}
		return true;
	}

}
?>