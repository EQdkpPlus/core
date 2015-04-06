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

if (!class_exists("timehandler")){
	class timehandler extends gen_class {

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
		public $summertime = 0;
		private $userTimeZone = 0;
		private $serverTimeZone = 0;

		public function __construct() {
			$tmp_timezone			= $this->get_serverTimezone();
			date_default_timezone_set($tmp_timezone);
			$this->timestamp		= time();
			$this->summertime		= (date('I',$this->timestamp)) ? true : false;
			$this->userTimeZone		= new DateTimeZone('GMT');
			$this->serverTimeZone	= new DateTimeZone($tmp_timezone);
			$this->pdl->register_type('time_error', null, null, array(2,3,4));
		}

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
				case 'gmt_tz':
					return $this->serverTimeZone->getName();
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
		* Return the Date, an timestamp or now...
		*
		* @return timestamp
		*/
		private function helper_dtime($dtime){
			return ($dtime && !is_object($dtime)) ? (is_numeric($dtime) ? "@$dtime" : $dtime) : "now";
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
				return 'GMT';
			}
		}
		
		/**
		* Generate time() in GMT
		*
		* @return timestamp
		*/
		private function gen_time($dtime=''){
			$dateTime = new DateTimeLocale($this->helper_dtime($dtime), $this->serverTimeZone);
			return $dateTime->format("U");
		}

		/**
		* Mktime in GMT
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
		* @param $tstamp		Timestamp
		* @return Formatted		time string
		*/
		public function date($format="Y-m-d H:i:s", $dtime='', $chk_summer=true){
			$observeDST = $this->timeZoneObservesDST($this->userTimeZone);

			//check for summer time
			if($this->summertime && $chk_summer && $observeDST) {
				$summertime = ($this->date('I', $dtime, false)) ? true : false;

				if($summertime !== $this->summertime && is_numeric($dtime)) {
					$dtime += 3600;
				}
			}
			
			$dateTime = new DateTimeLocale($this->helper_dtime($dtime), $this->serverTimeZone);
			$dateTime->setTimezone($this->userTimeZone);
			return $dateTime->format($format);
		}
		
		private function timeZoneObservesDST($objDateTimeZone){
			$dateTime1 = new DateTime("@".strtotime('June 1'));
			$dateTime1->setTimezone($objDateTimeZone);
			$dateTime2 = new DateTime("@".strtotime('Jan 1'));
			$dateTime2->setTimezone($objDateTimeZone);
			return ($dateTime1->format("I") !== $dateTime2->format("I"));
		}

		/**
		 * Output Date in user-format
		 *
		 * @int 	$time			Timestamp
		 * @bool 	$withtime		also display time?
		 * @bool 	$long			Date in Long-format?
		 * @bool	$timeonly		only display time?
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
			if($format === 1) $format = $this->user->style['date_notime_short'].' '.$this->user->style['time'];
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
			$dateTime->setTimezone($this->serverTimeZone);
			$stamp = $dateTime->getTimestamp();
			// hack to allow negative timestamps
			if(!$stamp) $stamp = $dateTime->format('U');
			//check for summer time
			if($this->summertime) {
				$summertime = ($this->date('I', $stamp, false)) ? true : false;
				if($summertime !== $this->summertime) $stamp -= 3600;
			}
			return $stamp;
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
			//php => momentjs
			$types = array(
				'd'		=> 'DD',
				'z'		=> 'DDD',
				'l'		=> 'dddd',
				'D'		=> 'ddd',
				'j'		=> 'D',
				'm'		=> 'MM',
				'n'		=> 'm',
				'F'		=> 'MMMM',
				'Y'		=> 'YYYY',
				'y'		=> 'YY',
				'a'		=> 'a',
				'A'		=> 'A',
				'h'		=> 'hh',
				'H'		=> 'HH',
				'g'		=> 'h',
				'G'		=> 'H',
				'i'		=> 'mm',
				's'		=> 'ss'	
			);
			return str_replace(array_keys($types), array_values($types), $format);
		}
		
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

		public function newtime($timestamp, $newtime='now'){
			$newtime	= ($newtime=='now') ? $this->date('H').':'.$this->date('i') : $newtime;
			$a_times	= explode(':', $newtime);
			$timestamp -= ($this->date('H', $timestamp)*3600 + $this->date('i', $timestamp)*60);
			$seconds	= (isset($a_times[2]) && $a_times[2] > 0) ? ($a_times[0]*60) : 0;
			return $timestamp + ($a_times[0]*3600) + ($a_times[1]*60) + $seconds;
		}

		public function adddays($timestamp,$daystoadd=1){
			return $timestamp +(60*60*24*$daystoadd);
		}

		public function dateDiff($ts1, $ts2, $out='sec'){
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
			return ($ts2 - $ts1 - ($ts2 - $ts1)%$secs[$out])/$secs[$out];
		}

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

		// HELPER FUNCTIONS
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
	
	public function __construct($time='now', $timezone=null) {
		try {
			parent::__construct($time, $timezone);
		} catch(Exception $e) {
			parent::__construct('now', $timezone);
			registry::register('plus_debug_logger')->log('time_error', 'Unable to create DateTime-object with given time-string \''.$time.'\', using \'now\' as fallback.');
			registry::register('plus_debug_logger')->log('php_error', 'EXCEPTION', 0, $e->getMessage(), __FILE__, __LINE__);
		}
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
		if(is_array(registry::fetch('user')->lang('time_daynames', false, false)) && count(registry::fetch('user')->lang('time_daynames', false, false)) > 1){
			$arrSearch = array_merge(self::$english_days, self::$english_days_short, self::$english_months);
			$arrReplace = array_merge(registry::fetch('user')->lang('time_daynames', false, false), registry::fetch('user')->lang('time_daynames_short', false, false), registry::fetch('user')->lang('time_monthnames', false, false));			
			$out =  parent::format($format);
			foreach($arrSearch as $key => $val){
				$out = preg_replace('/\b'.$val.'\b/u', $arrReplace[$key], $out);
			}

			return $out;

		}else{
			return parent::format($format);
		}
	}
	
	public static function createFromFormat($format, $string, $timezone=null) {
		if(is_array(registry::fetch('user')->lang('time_daynames', false, false)) && count(registry::fetch('user')->lang('time_daynames', false, false)) > 1){
			$arrReplace = array_merge(self::$english_days, self::$english_days_short, self::$english_months);
			$arrSearch = array_merge(registry::fetch('user')->lang('time_daynames', false, false), registry::fetch('user')->lang('time_daynames_short', false, false), registry::fetch('user')->lang('time_monthnames', false, false));

			foreach($arrSearch as $key => $val){
				$string = preg_replace('/\b'.$val.'\b/u', $arrReplace[$key], $string);
			}
		}
		return parent::createFromFormat($format, $string, $timezone);
	}
}
?>
