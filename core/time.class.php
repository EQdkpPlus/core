<?php
/*	Project:	EQdkp-Plus
 *	Package:	EQdkp-plus
 *	Link:		http://eqdkp-plus.eu
 *
 *	Copyright (C) 2006-2016 EQdkp-Plus Developer Team
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

if (!class_exists("time")){
	class time extends gen_class {

		private static $ArrTimezones = array();

		private $formtrans = array(
			//php		//js
			'd'		=> 'dd',
			'j'		=> 'd',
			'z'		=> 'o',
			'l'		=> 'DD',
			'm'		=> 'mm',
			'n'		=> 'm',
			'F'		=> 'MM',
			'Y'		=> 'yy',
			'a'		=> 'T',
			'A'		=> 'TT',
			'h'		=> 'hh',
			'H'		=> 'HH',
			'g'		=> 'h',
			'G'		=> 'h',
			'i'		=> 'mm',
			's'		=> 'ss'
		);
		private $possible_formats = array(
			'd'		=> 2,
			'D'		=> 3,
			'j'		=> array(1, 2),
			'l'		=> 'string',
			'N'		=> 1,
			'S'		=> 2,
			'w'		=> 1,
			'z'		=> array(1, 3),
			'W'		=> array(1, 2),
			'F'		=> 'string',
			'm'		=> 2,
			'M'		=> 3,
			'n'		=> array(1, 2),
			't'		=> 2,
			'L'		=> 1,
			'o'		=> 4,
			'Y'		=> 4,
			'y'		=> 2,
			'a'		=> 2,
			'A'		=> 2,
			'B'		=> 3,
			'g'		=> array(1, 2),
			'G'		=> array(1, 2),
			'h'		=> 2,
			'H'		=> 2,
			'i'		=> 2,
			's'		=> 2,
			'u'		=> array(1, 6),
			'e'		=> 'string',
			'I'		=> 1,
			'O'		=> 5,
			'P'		=> 6,
			'T'		=> 3,
			'Z'		=> array(1, 5),
			'c'		=> '#Y-d-m\TH:i:sP',
			'r'		=> '#D, j M Y H:i:s O',
			'U'		=> 'return',
		);

		private $timestamp = 0;
		private $userTimeZone = 0;
		private $utcTimeZone = 0;

		public function __construct() {
			date_default_timezone_set('UTC');
			$this->timestamp		= time();
			$this->userTimeZone		= new DateTimeZone('UTC');
			$this->utcTimeZone		= new DateTimeZone('UTC');

			$this->pdl->register_type('time_error', null, null, array(2,3,4));
		}

		/**
		 * Sets the User Timezone
		 * @param string $value Timezone
		 */
		public function setTimezone($value){
			$this->userTimeZone	= new DateTimeZone($value);
		}

		/**
		* Fetch the data provided in this class
		*
		*/
		public function __get($strKey){
			switch ($strKey){
				case 'user_tz':
					return $this->userTimeZone->getName();
				break;
				case 'server_tz':
					return $this->get_serverTimezone()->getName();
				break;
				case 'utz_tz':
					return $this->utcTimeZone->getName();
					break;
				case 'timezones':
					return $this->fetch_timezones();
				break;
				case 'time':
					return $this->gen_time();
				break;
				case 'minDate':
					return $this->gen_time("1980-01-01 0:0:0");
				break;
				case 'maxDate':
					return $this->gen_time("2038-01-19 0:0:0");
				break;
			}
			return parent::__get($strKey);
		}

		/**
		* Return the default timezone if needed
		*
		* @return timestamp
		*/
		public function get_serverTimezone(){
			if($this->config->get('timezone')){
				return $this->config->get('timezone');
			}elseif(ini_get('date.timezone')){
				return ini_get('date.timezone');
			}elseif(date_default_timezone_get()){
				return @date_default_timezone_get();
			}else{
				return 'UTC';
			}
		}

		/**
		* Generate time() in UTC
		*
		* @return timestamp
		*/
		private function gen_time($dtime=''){
			$dateTime = new DateTimeLocale($this->helper_dtime($dtime), $this->utcTimeZone);
			return $dateTime->format("U");
		}

		/**
		* Mktime in UTC
		*
		* @param $hour		Hour		int
		* @param $min		Minutes		int
		* @param $sec		Seconds		int
		* @param $month		Month		int
		* @param $day		Day			int
		* @param $year		Year		int
		* @return Timestamp
		*/
		public function mktime($hour='0', $min='0', $sec='0', $month='0', $day='0', $year='0'){
			$hour = ($hour>0) ? (int) $hour : 0;
			$min = ($min>0) ? (int) $min : 0;
			$sec = ($sec>0) ? (int) $sec : 0;
			$month = ($month>0) ? (int) $month : 0;
			$day = ($day>0) ? (int) $day : 0;
			$year = ($year>0) ? (int) $year : 0;
			return  $this->gen_time($year.'-'.$month.'-'.$day.' '.$hour.':'.$min.':'.$sec);
		}

		/**
		* Output proper Time
		*
		* @param $format		Format of the Output
		* @param $dtime			Timestamp in UTC
		* @return Formatted		time string
		*/
		public function date($format="Y-m-d H:i:s", $dtime=''){
			$dateTime = new DateTimeLocale($this->helper_dtime($dtime), $this->utcTimeZone);
			$dateTime->setTimezone($this->userTimeZone);
			return $dateTime->format($format);
		}

		/**
		 * Output Date in user-format
		 *
		 * @int 	$time			Timestamp, in UTC
		 * @bool 	$withtime		also display time?
		 * @bool 	$long			Date in Long-format?
		 * @bool	$timeonly		only display time?
		 * @param	boolean $long
		 * @param	boolean $fromformat
		 * @param	boolean $withday
		 * @return 	formatted String
		 */
		public function user_date($time=false, $withtime=false, $timeonly=false, $long=false, $fromformat=true, $withday=false) {
			if($time === 0 || $time === '0') return $this->user->lang('never');
			if(!$time) return '';

			$format = (($withday) ? (($withday === '2') ? 'D, ' : 'l, ') : '').(($long) ? $this->user->style['date_notime_long'] : $this->user->style['date_notime_short']);
			if($withtime) $format .= ' '.$this->user->style['time'];
			if($timeonly) $format = $this->user->style['time'];
			if(!$fromformat) $format = 'Y-m-d'.(($withtime) ? ' H:i' : '');
			return $this->date($format, $time);
		}

		/**
		 * Output Date in format for a specific User
		 *
		 * @param integer $intUserID
		 * @param integer $time Timestamp, in UTC
		 * @param boolean $withtime
		 * @param boolean $timeonly
		 * @param boolean $long
		 * @param boolean $fromformat
		 * @param boolean $withday
		 */
		public function date_for_user($intUserID, $time, $withtime=false, $timeonly=false, $long=false, $fromformat=true, $withday=false){
			$strTimezone = $this->pdh->get('user', 'timezone', array($intUserID));
			$strUserlang = $this->pdh->get('user', 'lang', array($intUserID));

			if($time === 0 || $time === '0') return $this->user->lang('never', false, false, $strUserlang);
			if(!$time) return '';

			$format = (($withday) ? (($withday === '2') ? 'D, ' : 'l, ') : '').(($long) ? $this->pdh->get('user', 'date_long', array($intUserID)) : $this->pdh->get('user', 'date_short', array($intUserID)));
			if($withtime) $format .= ' '.$this->pdh->get('user', 'date_time', array($intUserID));
			if($timeonly) $format = $this->pdh->get('user', 'date_time', array($intUserID));
			if(!$fromformat) $format = 'Y-m-d'.(($withtime) ? ' H:i' : '');
			$dateTime = new DateTimeLocale($this->helper_dtime($time), $this->utcTimeZone, $strUserlang);
			if(isset($strTimezone) && $strTimezone != '') { $dateTime->setTimezone(new DateTimeZone($strTimezone)); }
			return $dateTime->format($format);
		}

		/**
		 * Converts a String in Usertime to a User Timestamp, usable for the other time methods
		 *
		 * @param unknown $strTime
		 */
		public function convert_usertimestring_to_utc($strTime){
			$objDate = new DateTime($strTime, $this->userTimeZone);
			return $objDate->format("U");
		}

		/**
		 * Get the timezone offset in hours or seconds
		 *
		 * @param unknown $inhours
		 */
		public function get_timediff_to_utc($inhours=false){
			$objDate = new DateTime('now', $this->userTimeZone);
			return ($inhours) ? $objDate->format("O") : $objDate->format("Z");
		}

		/**
		 * Adds an offset to a timestamp
		 *
		 * @param integer $intTimestamp
		 * @param integer $intOffset in Seconds, e.g. 3600
		 * @return integer
		 */
		public function timestamp_offset($intTimestamp, $intOffset){
			return $intTimestamp + $intOffset;
		}

		/**
		 * Converts a timestamp in another Timezone into an UTC Timestamp
		 *
		 * @param integer $intSourceTimestamp
		 * @param string $strSourceTimezone
		 * @return integer
		 */
		public function convert_timestamp_to_utc($intSourceTimestamp, $strSourceTimezone){
			$second = date('s', $intSourceTimestamp);
			$minute = date('i', $intSourceTimestamp);
			$hour   = date('H', $intSourceTimestamp);
			$day    = date('d', $intSourceTimestamp);
			$month  = date('m', $intSourceTimestamp);
			$year   = date('Y', $intSourceTimestamp);

			$string = $year.'-'.$month.'-'.$day.' '.$hour.':'.$minute.':'.$second;

			$objDate = new DateTime($string, new DateTimeZone($strSourceTimezone));

			return $objDate->format("U");
		}

		public function convert_timestamp_from_utc($intSourceTimestamp){
			$second = date('s', $intSourceTimestamp);
			$minute = date('i', $intSourceTimestamp);
			$hour   = date('H', $intSourceTimestamp);
			$day    = date('d', $intSourceTimestamp);
			$month  = date('m', $intSourceTimestamp);
			$year   = date('Y', $intSourceTimestamp);

			$string = $year.'-'.$month.'-'.$day.' '.$hour.':'.$minute.':'.$second;

			return (new DateTime($string, $this->userTimeZone))->format("U");
		}

		/**
		 * Output Date in nice-format, like 7 days ago
		 *
		 * @int 	$time			Unix-Timestamp
		 * @bool 	$differenceForDeactivating	Difference in ms when user_date should be output instead of nice format
		 * @bool 	$withtime		also display time?
		 * @bool 	$long			Date in Long-format?
		 * @bool	$timeonly		only display time?
		 * @return 	formatted String
		 */
		public function nice_date($time=false, $differenceForDeactivating=false, $withtime=false, $timeonly=false, $long=false, $fromformat=true, $withday=false) {

			if($time === 0 || $time === '0') return $this->user->lang('never');
			if(!$time) return '';

			$lengths	= array("60","60","24","7","4.35");
			$now		= $this->time;

			if (!is_numeric($time)){
				$unix_date	= strtotime($time);
			} else {
				$unix_date = $time;
			}

			// check validity of date
			if(empty($unix_date)) {
				return "Bad date";
			}

			// is it future date or past date
			$langTense = $this->user->lang('time_tense');
			if($now > $unix_date) {
				$difference		= $now - $unix_date;
				$tense			= $langTense[1];
			} else {
				$difference		= $unix_date - $now;
				$tense			= $langTense[0];
			}

			if ($differenceForDeactivating && $difference > $differenceForDeactivating) return $this->user_date($time, $withtime, $timeonly, $long, $fromformat, $withday);

			for($j = 0; $difference >= $lengths[$j] && $j < count($lengths)-1; $j++) {
				$difference /= $lengths[$j];
			}
			$difference = round($difference);

			if($difference != 1) {
				$langPeriods = $this->user->lang('time_periods');
				$period = $langPeriods[$j];
			} else {
				$langPeriod = $this->user->lang('time_period');
				$period = $langPeriod[$j];
			}
			return sprintf($this->user->lang('nicetime_format'), "$difference $period", $tense);

		}

		/**
		 * Checks if $string is given in format $format
		 * @string 	$string		a formatted timestring
		 * @string 	$format		a correct format for date()
		 * @return	boolean
		 */
		private function check_format($string, $format) {
			$flen = strlen($format);
			$slen = strlen($string);
			$cb = '';
			$ca = '';
			$escape = false;
			$stroff = 0;
			for($i=0; $i<$flen; $i++) {
				if($stroff && $stroff >= $slen) {
					$this->pdl->log('time_error', 'Unexpected end in '.$string.' compared to format '.$format.'.');
					return false;
				}
				$c	= substr($format, $i, 1);
				$ca	= substr($format, ($i+1), 1);
				if($c == '\\' && $cb != '\\') {
					$escape = true;
					$cb = $c;
					continue;
				}
				if(in_array($c, array_keys($this->possible_formats)) && !$escape) {
					if(is_string($this->possible_formats[$c])) {
						if(strpos($this->possible_formats[$c], '#') === 0) {
							$new_format = substr($format, 0, $i).substr($this->possible_formats[$c], 1).substr($format, ($i+1));
							return $this->check_format($string, $new_format);
						}
						//handle strings with no given length
						$partial_string = substr($string, $stroff);
						$match = array();
						preg_match('#^([a-zA-Z]+)#', $partial_string, $match);
						if(isset($match[1])) $stroff += strlen($match[1])-1;
						else {
							$this->pdl->log('time_error', 'Format mismatch at position ('.($stroff+1).'.) in '.$string.' compared to format '.$format.'.');
							return false;
						}
					} elseif(is_array($this->possible_formats[$c])) {
						//Search for next char in the string which is no number
						$partial_string = substr($string, $stroff, $this->possible_formats[$c][1]); //maximum length string
						$k = 0;
						for($j=$this->possible_formats[$c][0]; $j<=$this->possible_formats[$c][1]; $j++) {
							$psc = substr($partial_string, $j, 1);
							if(!is_numeric($psc)) {
								$k = $j;
								break;
							}
						}
						if($k) $stroff += ($k-1);
						else {
							$this->pdl->log('time_error', 'Format mismatch at position ('.($stroff+$this->possible_formats[$c][1]).') in '.$string.' compared to format '.$format.'.');
							return false;
						}
					} elseif($this->possible_formats[$c] > 0) $stroff += $this->possible_formats[$c]-1;
				} elseif($c != substr($string, $stroff, 1)) {
					$this->pdl->log('time_error', 'Format mismatch at position ('.($stroff+1).') in '.$string.' compared to format '.$format.'.');
					return false;
				}
				$escape = false;
				$cb = $c;
				$stroff++;
			}
			return true;
		}

		/*
		 * Create Timestamp from formatted string
		 *
		 * @string	$string		Date-String (e.g. '14. February 1993 13:40:12')
		 * @string	$format		format of date-string (e.g. 'd. F Y H:i:s')
		 * @return 	string		timestamp
		 */
		public function fromformat($string, $format=0) {
			if($format === 0) $format = $this->user->style['date_notime_short'];
			if($format === 1){
				$format = $this->user->style['date_notime_short'].' '.$this->user->style['time'];

				/***************************************************************************
				 * This workaround is for a and P instead of AM and PM. This is allowed for
				 * timepicker and momentJS, but not common in PHP and causes the convertion
				 * to fail. A and P is extended by the M to AM and PM.
				 ***************************************************************************/
				if(strtolower(substr($this->user->style['time'],-1)) == 'a'){
					if(strtolower(substr($string, -1)) == 'a' || strtolower(substr($string, -1)) == 'p'){
						$string = $string.'M';
					}
				}
				// end of workaround
			}
			if(function_exists('date_create_from_format')) {
				if(!$this->check_format($string, $format)) return $this->time;
				$dateTime = DateTimeLocale::createFromFormat($format, $string, $this->userTimeZone);
				if(!is_object($dateTime)) {
					$this->pdl->log('time_error', "parsing string '".$string."' in format '".$format."' did not work");
					return $this->time;
				}
			} else {
				list($year, $month, $day, $hour, $minute) = sscanf($string, '%04s-%02s-%02s %02s:%02s');
				$pstring = $year.'-'.$month.'-'.$day.' '.$hour.':'.$minute.':00';
				$dateTime = new DateTimeLocale($this->helper_dtime($pstring), $this->userTimeZone);
				if(!is_object($dateTime)) {
					$this->pdl->log('time_error', "unable to parse '".$pstring."' into a correct time, input string was '".$string."'.");
					return $this->time;
				}
			}
			$dateTime->setTimezone($this->userTimeZone);
			$stamp = $dateTime->getTimestamp();
			// hack to allow negative timestamps
			if(!$stamp) $stamp = $dateTime->format('U');
			return $stamp;
		}

		/**
		 * Removes H:i from Y-m-d H:i
		 *
		 * @param string $timestamp
		 * @return integer
		 */
		public function removetimefromtimestamp($timestamp) {
			return ($timestamp) > 0 ? $this->fromformat($this->date("Y-m-d", $timestamp), "Y-m-d") : 0;
		}

		/*
		 * Transforms a php-date-format so it can be used for jquery
		 *
		 * @string	$format		PHP-Date-Format
		 * @return 	string		JS-Date-Format
		 */
		public function translateformat2js($format) {
			return str_replace(array_keys($this->formtrans), array_values($this->formtrans), $format);
		}

		/**
		 * Transforms php date format into moment.js format
		 *
		 * @param string $format PHP-Date-Format
		 * @return string	moment.js Format
		 */
		public function translateformat2momentjs($format){
			/* Because str_replace replaces from left to right, it will also replace the replaced strings before.
			 * As D is a search character, some strings will be replaced through K, which will be replaced with D at the end.
			 */

			//php => momentjs
			$types = array(
				'd'		=> 'KK', //KK as placeholder for DD
				'z'		=> 'KKK', //KKK as placeholder for DDD
				'l'		=> 'dddd',
				'D'		=> 'ddd',
				'j'		=> 'K',
				'm'		=> 'MM',
				'n'		=> 'm',
				'F'		=> 'MMMM',
				'Y'		=> 'YYYY',
				'y'		=> 'YY',
				'a'		=> 'a',
				'A'		=> 'A',
				'tt'	=> 'a',
				'TT'	=> 'A',
				'h'		=> 'hh',
				'H'		=> 'HH',
				'g'		=> 'h',
				'G'		=> 'H',
				'i'		=> 'mm',
				's'		=> 'ss',
				'K'		=> 'D', //Replace Placeholder
			);
			return str_replace(array_keys($types), array_values($types), $format);
		}

		/**
		 * Produces Time-Tag for moment.js
		 *
		 * @param integer $date
		 * @param string $strText
		 * @param string $strCSSClass
		 * @return string
		 */
		public function createTimeTag($date, $strText, $strCSSClass=""){
			return '<time class="datetime '.$strCSSClass.'" data-timestamp="'.$date.'" datetime="'.$this->date(DATE_ATOM, $date).'" title="'.$strText.'">
			'.$strText.'</time>';
		}

		/**
		 * Converts timezone offset to human-readable form
		 * Part of timezone fix
		 *
		 * @param string $offset
		 * @return string
		 */
		private static function formatOffset($offset){
			$hours		= $offset / 3600;
			$remainder	= $offset % 3600;
			$sign		= $hours > 0 ? '+' : '-';
			$hour		= (int) abs($hours);
			$minutes	= (int) abs($remainder / 60);

			if ($hour == 0 AND $minutes == 0) {
				$sign = ' ';
			}
			return $sign . str_pad($hour, 2, '0', STR_PAD_LEFT) .':'. str_pad($minutes,2, '0');
		}

		/**
		* Fetch the timezones from server
		*
		* @return array of timezones
		*/
		public static function fetch_timezones(){
			if(!is_array(self::$ArrTimezones) || empty(self::$ArrTimezones)) {
				$timezone_data = DateTimeZone::listIdentifiers(1022);
				$timezone_ab = DateTimeZone::listAbbreviations();

				$london = new DateTimeZone('Europe/London');
				$london_dt = new DateTime('31-12-2014', $london);
				foreach($timezone_ab as $key => $more_data) {
					foreach($more_data as $tz) {
						$value = $tz['timezone_id'];
						if(!in_array($value, $timezone_data) || $tz['dst']) continue;
						$slash = strpos($value, '/');
						$continent = substr($value, 0, $slash);
						$region = substr($value, $slash+1);

						try {
							$current_tz = new DateTimeZone($value);
							$offset = $current_tz->getOffset($london_dt);
							$tzdata[$value] = 'GMT '.trim(self::formatOffset($offset));
							$tzlist[$continent][] = $value;
						} catch (Exception $e) {
						}
					}
				}
				ksort($tzlist);
				foreach ( $tzlist as $region=>$locales ) {
					sort($locales);
					foreach( $locales as $locale ) {
						$city = substr($locale, (stripos($locale, '/')+1));
						self::$ArrTimezones[$region][$locale] = $city.' ('.$tzdata[$locale].')';
					}
				}
			}
			return self::$ArrTimezones;
		}

		/**
		* Return Date in RFC 2822 Compatible Output
		*
		* @param $tstamp		Timestamp
		* @return RFC2822 Time String
		*/
		public function RFC2822($dtime){
			$date	= new DateTimeLocale($this->helper_dtime($dtime), $this->userTimeZone);
			return $date->format(DATE_RFC2822);
		}

		public function RFC822($dtime){
			$date	= new DateTime($this->helper_dtime($dtime), $this->userTimeZone);
			return $date->format(DATE_RFC822);
		}

		public function DateRSS($dtime){
			$date	= new DateTime($this->helper_dtime($dtime), $this->userTimeZone);
			return $date->format(DATE_RSS);
		}

		/**
		 * Return Date in RFC 3339 Compatible Output
		 *
		 * @param $tstamp		Timestamp
		 * @return RFC3339 Time String
		 */
		public function RFC3339($dtime){
			$date	= new DateTimeLocale($this->helper_dtime($dtime), $this->userTimeZone);
			return $date->format(DATE_RFC3339);
		}

		public function getdate($dtime='') {
			$dtime	= $this->gen_time($dtime);
			$data	= array($dtime);
			$date	= $this->date('s.i.H.d.w.m.Y.z.l.F', $dtime);
			list($data['seconds'], $data['minutes'], $data['hours'], $data['mday'], $data['wday'], $data['mon'], $data['year'], $data['yday'], $data['weekday'], $data['month']) = explode('.', $date);
			return $data;
		}

		public function toSeconds($time, $ff='day'){
			switch($ff){
				case 'day':			$out = $time*24*60*60;
				case 'hour':		$out = $time*60*60;
				case 'minute':		$out = $time*60;
			}
			return $out;
		}

		public function newtime($timestamp, $newtime='now', $timeInUTC=true){
			$newtime	= ($newtime=='now') ? (new DateTime())->format('H:i') : $newtime;

			// get the date
			$objDate	= new DateTime('@'.(int)$timestamp, $this->utcTimeZone);
			$datewotime	= $objDate->format('Y-m-d');

			// now, get the time in UTC
			if($timeInUTC){
				$objTime	= new DateTime($newtime, $this->utcTimeZone);
				$newtime	= $objTime->setTimezone($this->userTimeZone)->format('H:i');
			}

			// generate the new timestamp
			$out = new DateTime($datewotime. ' '.$newtime, $this->userTimeZone);
			return $out->format('U');
		}

		public function dateDiff($ts1, $ts2, $out='sec', $pos_neg=false){
			// Build the Dates
			if(!is_numeric($ts1)) {
				$dt1	= new DateTimeLocale($dt1, $this->userTimeZone);
				$ts1	= $dt1->format('U');
			}
			if(!is_numeric($ts2)) {
				$dt2	= new DateTimeLocale($dt2, $this->userTimeZone);
				$ts2	= $dt2->format('U');
			}
			// calculate the difference
			$secs['sec']	= 1;
			$secs['min']	= 60;
			$secs['hour']	= $secs['min']*60;
			$secs['day']	= $secs['hour']*24;
			$secs['week']	= $secs['day']*7;
			$secs['month']	= $secs['day']*30;
			$secs['year']	= $secs['day']*365;
			$result = ($ts2 - $ts1 - ($ts2 - $ts1)%$secs[$out])/$secs[$out];
			return ($pos_neg) ? $this->pos_neg($result) : $result;
		}

		public function pos_neg($timediff){
			return ((substr($timediff, 0, 1) === '-') ? '' : '+').$timediff;
		}

		/**
		 * Returns the age of an date
		 *
		 * @param integer $date
		 * @return integer
		 */
		public function age($date) {
			if(!$date) return 0;
			$bday		= $this->getdate($date);
			$today		= $this->getdate();
			$yeardiff	= ($today['year'] - $bday['year']);
			if (($today['mon'] < $bday['mon']) || (($today['mon'] == $bday['mon']) && ($today['mday'] < $bday['mday']))) {
				$yeardiff = $yeardiff - 1;
			}
			return $yeardiff;
		}

		// Count Entries in an array between two dates
		public function countBetweenDates($array, $startdate, $enddate){
			$this->cbd_enddate		= ($enddate) ? $enddate : $this->gen_time();
			$this->cbd_startdate	= $startdate;
			if(is_array($array)){
				return count(array_filter($array, array($this, 'helper_countbetweendates')));
			}else{
				return false;
			}
		}

		/**
		 * Adds Seconds to a given UTC Timestamp.
		 * Respects Winter/Summertime, because of given Timezone of Event
		 * Returns UTC Timestamp
		 *
		 * @param integer $intUtcTimestamp
		 * @param integer $intSecondsToAdd
		 * @param string $strEventTimezone
		 * @return integer UTC Timestamp
		 */
		public function createRepeatableEvents($intUtcTimestamp, $intSecondsToAdd, $strEventTimezone=''){
			$objTimeZone = ($strEventTimezone === '') ? $this->userTimeZone : new DateTimeZone($strEventTimezone);
			$objTime = new DateTimeLocale($this->helper_dtime($intUtcTimestamp), new DateTimeZone('UTC'));
			$objTime->setTimezone($objTimeZone);

			$blnIsSummertimeBefore = $objTime->format("I");
			$intTimestamp = $objTime->format("U");

			$strHelper = $this->helper_repeatable_events($intUtcTimestamp, $intSecondsToAdd, $objTimeZone);
			list($intNewEventTimestamp, $intIsSummertime) = explode(';', $strHelper);

			$diff = $intNewEventTimestamp - $intTimestamp;
			//Workaround, as from Summer to Wintertime, no offset is created, only the other way...
			if($blnIsSummertimeBefore && !$intIsSummertime && ($diff == $intSecondsToAdd)){
				$intNewEventTimestamp += 3600;
			}

			return $intNewEventTimestamp;
		}

		private function helper_repeatable_events($intUtcTimestamp, $intSecondsToAdd, $objTimeZone){
			$objTime = new DateTimeLocale($this->helper_dtime($intUtcTimestamp), new DateTimeZone('UTC'));
			$objTime->setTimezone($objTimeZone);

			$blnIsSummertimeBefore = $objTime->format("I");

			if($intSecondsToAdd > 0){
				$objTime->add(new DateInterval("PT".$intSecondsToAdd."S"));
			} else {
				$objTime->sub(new DateInterval("PT".abs($intSecondsToAdd)."S"));
			}

			$blnIsSummertimeAfter = $objTime->format("I");

			return $objTime->format("U;I");
		}

		// HELPER FUNCTIONS
		/**
		 * Return the Date, an timestamp or now...
		 *
		 * @return timestamp
		 */
		private function helper_dtime($dtime){
			return ($dtime && !is_object($dtime)) ? (is_numeric($dtime) ? "@$dtime" : $dtime) : "now";
		}

		private function helper_countbetweendates($timez){
			return $timez < $this->cbd_enddate && $timez > $this->cbd_startdate;
		}

		private function helper_countfuturedays($timez){
			return $timez[$this->cfd_tmpfuncname] > $this->cfd_startdate;
		}
	}
}

// Helper to make the datetime translatable
class DateTimeLocale extends DateTime {
	// define the english names
	private static $english_days		= array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday');
	private static $english_days_short	= array('Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun');
	private static $english_months		= array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');
	private static $language = false;
	private $cache = array();

	public function __construct($time='now', $timezone=null, $language=false) {
		try {
			parent::__construct($time, $timezone);
		} catch(Exception $e) {
			parent::__construct('now', $timezone);
			registry::register('plus_debug_logger')->log('time_error', 'Unable to create DateTime-object with given time-string \''.$time.'\', using \'now\' as fallback.');
			registry::register('plus_debug_logger')->log('php_error', 'EXCEPTION', 0, $e->getMessage(), __FILE__, __LINE__);
		}
		self::$language = $language;
	}

	public function __call($name, $arguments) {
		switch($name) {
			case 'getTimestamp':
				return $this->format('U');
			default:
				return;
		}
	}

	public function format($format) {
		if(is_array(registry::fetch('language')->get(self::$language, 'time_daynames')) && count(registry::fetch('user')->lang('time_daynames', false, false, self::$language)) > 1){
			$arrSearch = array_merge(self::$english_days, self::$english_days_short, self::$english_months);
			$arrReplace = array_merge(registry::fetch('user')->lang('time_daynames', false, false, self::$language), registry::fetch('user')->lang('time_daynames_short', false, false, self::$language), registry::fetch('user')->lang('time_monthnames', false, false, self::$language));
			$out =  parent::format($format);
			if(isset($this->cache[self::$language][$format])) return $this->cache[self::$language][$format];
			foreach($arrSearch as $key => $val){
				$out = preg_replace('/\b'.$val.'\b/u', $arrReplace[$key], $out);
			}
			$this->cache[self::$language][$format] = $out;
			return $out;

		}else{
			return parent::format($format);
		}
	}

	public static function createFromFormat($format, $string, $timezone=null) {
		if(is_array(registry::fetch('user')->lang('time_daynames', false, false, self::$language)) && count(registry::fetch('user')->lang('time_daynames', false, false, self::$language)) > 1){
			$arrReplace = array_merge(self::$english_days, self::$english_days_short, self::$english_months);
			$arrSearch = array_merge(registry::fetch('user')->lang('time_daynames', false, false, self::$language), registry::fetch('user')->lang('time_daynames_short', false, false, self::$language), registry::fetch('user')->lang('time_monthnames', false, false, self::$language));

			foreach($arrSearch as $key => $val){
				$string = preg_replace('/\b'.$val.'\b/u', $arrReplace[$key], $string);
			}
		}
		return parent::createFromFormat($format, $string, $timezone);
	}
}
