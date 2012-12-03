<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2010
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

if (!class_exists("feed")) {

	class feed extends gen_class {
		public static $shortcuts = array('pfh', 'time');
		private $encoding		= 'UTF-8';
		protected $items		= array();
		protected $data			= array();

		public function addItem(feeditems $item){
			$this->items[] = $item;
		}

		public function __set($key, $value){
			$this->data[$key] = $value;
		}

		public function __get($key){
			if(isset($this->data[$key])) return $this->data[$key];
			return parent::__get($key);
		}

		private function generate(){
			$xml  = '<?xml version="1.0" encoding="' . $this->encoding . '"?>' . "\n";
			$xml .= '<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom" xmlns:c="http://base.google.com/cns/1.0">' . "\n";
			$xml .= '	<channel>' . "\n";
			$xml .= '		<title>' . $this->specialchars($this->title) . '</title>' . "\n";
			$xml .= '		<description>' . $this->specialchars($this->description) . '</description>' . "\n";
			$xml .= '		<link>' . $this->specialchars($this->link) . '</link>' . "\n";
			$xml .= '		<language>' . $this->language . '</language>' . "\n";
			$xml .= '		<pubDate>' . $this->time->date('r', $this->published) . '</pubDate>' . "\n";
			$xml .= '		<generator>EQDKP-PLUS Gamer CMS</generator>' . "\n";
			$xml .= '		<atom:link href="' . $this->specialchars($this->feedfile) . '" rel="self" type="application/rss+xml" />' . "\n";

			foreach ($this->items as $items){
				$xml .= '		<item>' . "\n";
				$xml .= '			<title>' . $this->specialchars($items->title) . '</title>' . "\n";
				$xml .= '			<description><![CDATA[' . preg_replace('/[\n\r]+/', ' ', $items->description) . ']]></description>' . "\n";
				$xml .= '			<link>' . $this->specialchars($items->link) . '</link>' . "\n";
				$xml .= '			<pubDate>' . $this->time->date('r', $items->published) . '</pubDate>' . "\n";
				$xml .= '			<guid>' . ($items->guid ? $items->guid : $this->specialchars($items->link)) . '</guid>' . "\n";
				if(isset($items->author)){
					$xml .= '			<author>' . $this->specialchars($items->author) . '</author>' . "\n";
				}
				if(isset($items->source)){
					$xml .= '			<source>' . $this->specialchars($items->source) . '</source>' . "\n";
				}

				// Custom Feed Objects (in namespace g:)
				// http://base.google.com/support/bin/answer.py?answer=58085&hl=en
				if(is_array($items->customitems)){
					foreach($items->customitems as $custom_key=>$custom_value){
						$xml .= '<c:'.$custom_key.'>'.$this->specialchars($custom_value).'</c:'.$custom_key.">\n";
					}
				}
				$xml .= '		</item>' . "\n";
			}

			$xml .= '	</channel>' . "\n";
			$xml .= '</rss>';

			return $xml;
		}

		private function specialchars($strString){
			$arrFind = array('"', "'", '<', '>');
			$arrReplace = array('&#34;', '&#39;', '&lt;', '&gt;');
			return str_replace($arrFind, $arrReplace, $strString);
		}

		public function show(){
			return $this->generate();
		}

		public function save($path){
			$this->pfh->putContent($path, $this->generate());
		}
	}

	class feeditems extends gen_class{

		protected $fitems = array();

		public function __construct($fitems=false){
			if (is_array($fitems)){
				$this->fitems = $fitems;
			}
		}

		public function __set($key, $value){
			$this->fitems[$key] = $value;
		}

		public function __get($key){
			return isset($this->fitems[$key]) ? $this->fitems[$key] : parent::__get($key);
		}
	}
}
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_feed', feed::$shortcuts);
?>