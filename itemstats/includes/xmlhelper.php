<?php

// Simple XML Helper Class
class XmlHelper
{
	var $parser;
	var $desired_element = '';
	var $current_element = '';
	var $element_data = '';

	function XmlHelper() 
	{
	}

	function close()
	{
	}

	function parse($data, $element) 
	{
		$this->parser = xml_parser_create();
		if ($this->parser == false)
		{
			return null;
		}

		// Initialize variables.
		$this->desired_element = $element;
		$this->current_element = '';
		$this->element_data = '';

		// Set the parse handlers.
		xml_set_object($this->parser, $this);
		xml_set_element_handler($this->parser, 'handle_start_element', 'handle_end_element');
		xml_set_character_data_handler($this->parser, 'handle_character_data');
		
		// Do the parsing.
		xml_parse($this->parser, $data);
		xml_parser_free($this->parser);
		return $this->element_data;
	}

	function handle_start_element($parser, $name, $attributes) 
	{
		$this->current_element = $name;
	}


	function handle_character_data($parser, $data) 
	{
		if ((trim($data) != '') && (strcasecmp($this->current_element, $this->desired_element) == 0))
		{
			$this->element_data = $this->element_data . $data;
		}
	}

	function handle_end_element($parser, $name) 
	{
	}
}
?>