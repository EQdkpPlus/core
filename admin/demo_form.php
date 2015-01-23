<?php
/*	Project:	EQdkp-Plus
 *	Package:	EQdkp-plus
 *	Link:		http://eqdkp-plus.eu
 *
 *	Copyright (C) 2006-2015 EQdkp-Plus Developer Team
 *
 *	This program is free software: you can redistribute it and/or modify
 *	it under the terms of the GNU Affero General Public License as published
 *	by the Free Software Foundation, either version 3 of the License, or
 *	(at your option) any later version.
 *
 *	This program is distributed in the hope that it will be useful,
 *	but WITHOUT ANY WARRANTY; without even the implied warranty of
 *	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *	GNU Affero General Public License for more details.
 *
 *	You should have received a copy of the GNU Affero General Public License
 *	along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

define('EQDKP_INC', true);
define('IN_ADMIN', true);
$eqdkp_root_path = './../';
include_once($eqdkp_root_path . 'common.php');

class demo_form extends page_generic {
	public static $shortcuts = array('form' => array('form', array('demoform')));
	
	
	// usually defined in the lang-files
	private $language = array(
		'demo_tab_tab1'				=> 'Tab 1',
		'demo_tab_tab2'				=> 'Tab 2',
		'demo_fs_fieldset1'			=> 'Fieldset 1',
		'demo_fs_info_fieldset1'	=> 'Here are additional information about Fieldset 1.',
		'demo_fs_fieldset2'			=> 'Fieldset 2',
		'demo_f_mymail'				=> 'Enter you e-mail',
		'demo_f_help_mymail'		=> 'You can only use a student e-mail from myuni.com.',
		'demo_f_enlist'				=> 'Do you want to be listed?',
		'germany'					=> 'Germany',
		'united_states'				=> 'United States',
		'united_kingdom'			=> 'United Kingdom',
		'demo_f_dropdown1'			=> 'Select an option',
		'demo_f_help_dropdown1'		=> 'Depending on the selection, different fields will be displayed/hidden and enabled/disabled.',
		'demo_f_selectme'			=> 'A radio field',
		'demo_f_textit'				=> 'Lots of text to input',
		'demo_f_whenwasthis'		=> 'Do you remember the date?',
		'demo_f_pointless'			=> 'Choose a pointless number',
		'optf'						=> 'Option F',
		'optg'						=> 'Option G',
		'optv'						=> 'Option V',
		'optw'						=> 'Option W',
		'optx'						=> 'Option X',
		'opty'						=> 'Option Y',
		'demo_f_dropdown2'			=> 'Select an option',
		'demo_f_help_dropdown2'		=> 'Depending on the selection, the options in the other two dropdowns will change.',
		'demo_f_dropdown3'			=> 'Select an option',
		'demo_f_dropdown4'			=> 'Select an option',
		
	);
	
	public function __construct() {
		$this->user->add_lang('german', $this->language);
		parent::__construct(false);
		$this->process();
	}
	
	public function display() {
	
		if($this->in->get('ajax', false)) {
			$data = array(
				'opt1' => array('optx', 'opty'),
				'opt2' => array('optf', 'optg'),
				'opt3' => array('optv', 'optw'),
			);
			$options = array(
				'options_only'	=> true,
				'tolang'		=> true,
				'no_key'		=> true,
				'options' 		=> $data[$this->in->get('requestid')],
				'value'			=> 'opty',
			);
			echo new hdropdown('dummy', $options);
			exit;
		}
		
		
		// initialize form class
		$this->form->lang_prefix = 'demo_';
		$this->form->use_tabs = true;
		$this->form->use_fieldsets = true;
		
		$definitions = array(
			'tab1'	=> array(
				'fieldset1'	=> array(
					'myname'	=> array(
						'type'	=> 'text',
						'lang'	=> 'name',
					),
					'mymail'	=> array(
						'type'	=> 'text',
						'text'	=> 'student.',
						'text2'	=> '@myuni.com',
					),
					'enlist'	=> array(
						'type'	=> 'radio',
					),
					'country'	=> array(
						'type'		=> 'dropdown',
						'dir_lang'	=> 'Select your country',
						'dir_help'	=> 'dir_help and dir_lang will directly show the entered string',
						'tolang'	=> true,
						'options'	=> array(
							'ger'		=> 'germany',
							'us'		=> 'united_states',
							'uk'		=> 'united_kingdom'
						),
						'default'	=> 'us'
					),
					'whenwasthis' => array(
						'type'	=> 'datepicker'
					),
				),
				'fieldset2'	=> array(
					'dropdown1' => array(
						'type'	=> 'dropdown',
						'options' => array(
							'opt1' => 'Option 1',
							'opt2' => 'Option 2',
							'opt3' => 'Option 3',
						),
						'dependency' => array('opt1' => array('selectme', 'textit'), 'opt2' => array('pointless'))
					),
					'selectme' => array(
						'type'	=> 'radio',
						'options' => array(
							'opta' => 'Option a',
							'optb' => 'Option b',
							'optc' => 'Option c',
						),
						'default' => 'optc'
					),
					'textit' => array(
						'type'	=> 'textarea',
					),
					'pointless' => array(
						'type'	=> 'spinner',
					)
				),
			),
			'tab2'	=> array(
				'fieldset2' => array(
					'dropdown2'	=> array(
						'type'	=> 'dropdown',
						'options' => array(
							'opt1' => 'Option 1',
							'opt2' => 'Option 2',
							'opt3' => 'Option 3',
						),
						'ajax_reload' => array(array('dropdown3', 'dropdown4'), 'demo_form.php'.$this->SID.'&ajax=true'),
					),
					'dropdown3' => array(
						'type'	=> 'dropdown',
					),
					'dropdown4' => array(
						'type'	=> 'dropdown',
					),
				),
			),
		);
		$this->form->add_tabs($definitions);
		
		
		if($this->in->exists('save')) {
			$values = $this->form->return_values();
			echo $this->pdl->format_var($values);
			exit;
		}
		
		$values = array(
			'myname' 	=> 'This is my Name',
			'mymail' 	=> 'huuh',
			'enlist'	=> 1,
			'country'	=> 'uk',
			'dropdown2'	=> 'opt2',
			'whenwasthis' => time()-3600*24*7*4,
		);
		$this->form->output($values);

		$this->core->set_vars(array(
			'page_title'		=> 'Demo-Formular',
			'template_file'		=> 'admin/demo_form.html',
			'display'			=> true)
		);
	}
}
registry::register('demo_form');
?>