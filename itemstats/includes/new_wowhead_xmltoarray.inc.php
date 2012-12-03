<?php

/* 
 * XmlToArray
 * 31/05/2007
 * author: Frank Matheron
 * email: fenuzz@gmail.com
 * description: convert a xml document to an array
 *
 */

// convert a xml document to an array
class XmlToArray
{
	var $xml_obj = null;
   	var $output = array();

	function XmlToArray() 
	{
		$this->xml_obj = xml_parser_create();
		xml_set_object($this->xml_obj,$this);
		xml_set_character_data_handler($this->xml_obj, 'dataHandler');
		xml_set_element_handler($this->xml_obj, "startHandler", "endHandler");
	}

	function parse($data) 
	{
		$this->output = array();

		// Do the parsing.
		xml_parse($this->xml_obj, $data);
		xml_parser_free($this->xml_obj);
		
		return $this->output;
	}

	function startHandler($parser, $name, $attr){
		// create element
		$element['name'] = $name;
		$element['attr'] = $attr;

		// push the element to the stack
		array_push($this->output, $element);
	}

	function dataHandler($parser, $data) {
		// add the data to the latest element
		$element = array_pop($this->output);
		$element['data'] .= $data;
		array_push($this->output, $element);
	}

   	function endHandler($parser, $name) {
		// is this the last close tag?
		if (count($this->output) > 1) {
			
			// pop the just created element
			$element = array_pop($this->output);
	
			// pop its parent
			$parent = array_pop($this->output);
	
			// add the current element to its parent
			$parent['child'][] = $element;
	
			// push the parent back to the stack
			array_push($this->output, $parent);
		}
	}
}

?>