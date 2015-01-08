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

if ( !defined('EQDKP_INC') ){
	header('HTTP/1.0 404 Not Found');exit;
}

if (!class_exists("feed")) {

	class feed extends gen_class {
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
			$arrFind = array('"', "'", '<', '>', '&');
			$arrReplace = array('&#34;', '&#39;', '&lt;', '&gt;', '&amp;');
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
?>