<?php
 /*
 * Project:     EQdkp-Plus
 * License:     Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:       2010
 * Date:        $Date$
 * -----------------------------------------------------------------------
 * @author      $Author$
 * @copyright   2006-2010 EQdkp-Plus Developer Team
 * @link        http://eqdkp-plus.com
 * @package     eqdkp-plus
 * @version     $Rev$
 * 
 * $Id$
 */

if ( !defined('EQDKP_INC') ){
    header('HTTP/1.0 404 Not Found');exit;
}

if (!class_exists("datetime_mgr")){
  class timehandler {
  	
  	var $ArrTimezones = '';
  	
  	public function __construct(){
  		$tmp_timezone = $this->get_serverTimezone();
      date_default_timezone_set($tmp_timezone);
			$this->timestamp			= time();
			$this->userTimeZone		= new DateTimeZone('GMT');
			$this->serverTimeZone	= new DateTimeZone($tmp_timezone);
  	}
  	
  	public function setTimezone($value){
  		$this->userTimeZone	= new DateTimeZone($value);	
  	}
  	
  	/**
  	* Fetch the data provided in this class
  	*      	
  	*/
  	public function __get($strKey){
    	switch ($strKey)
			{
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
    }
		
		/**
  	* Return the Date, an timestamp or now...
  	*      	
  	* @return timestamp
  	*/
		private function helper_dtime($dtime){
			return (($dtime) ? ((is_numeric($dtime)) ? "@$dtime" : $dtime) : "now");	
		}
		
		/**
  	* Return the default timezone if needed
  	*      	
  	* @return timestamp
  	*/
		public function get_serverTimezone(){
			global $core;
			if(ini_get('date.timezone')){
				return ini_get('date.timezone');
			}elseif($core->config['server_timezone']){
				return $core->config['server_timezone'];
			}elseif(date_default_timezone_get()){
				return @date_default_timezone_get();
			}else{
				return 'GMT';
			}
		}
		
  	/**
  	* Genereate time() in GMT
  	*      	
  	* @return timestamp
  	*/
    public function gen_time($dtime=''){
    	$dateTime = new DateTimeLocale($this->helper_dtime($dtime), $this->serverTimeZone);
      return strtotime($dateTime->format("Y-m-d H:i:s"));
    }
		
		/**
    * Mktime in GMT
    *
    * @param $hour   Hour      int
    * @param $min    Minutes   int
    * @param $sec    Seconds   int
    * @param $month  Month     int
    * @param $day    Day       int
    * @param $year   Year      int
    * @return Timestamp
    */
    public function mktime($hour='0', $min='0', $sec='0', $month='0', $day='0', $year='0'){
      return  $this->gen_time((($year>0) ? $year : '0').'-'.(($month>0) ? $month : '0').'-'.(($day>0) ? $day : '0').' '.(($hour>0) ? $hour : '0').':'.(($min>0) ? $min : '0').':'.(($sec>0) ? $sec : '0'));
    }
		
    /**
    * Output proper Time
    *
    * @param $format     Format of the Output
    * @param $tstamp     Timestamp
    * @return Formatted time string
    */
    public function date($format="Y-m-d H:i:s", $dtime=''){
    	$dateTime = new DateTimeLocale($this->helper_dtime($dtime), $this->serverTimeZone);
    	$dateTime->setTimezone($this->userTimeZone);
      return $dateTime->format($format);
    }
    
    /**
    * Fetch the timezones from server
    *
    * @return array of timezones
    */
    private function fetch_timezones(){
    	if(!is_array($this->ArrTimezones)){
	    	foreach(DateTimeZone::listIdentifiers() as $value){
					//Only get timezones explicitely not part of "Others", see http://www.php.net/manual/en/timezones.others.php
					if ( preg_match( '/^(America|Antartica|Arctic|Asia|Atlantic|Europe|Indian|Pacific)\//', $value ) ){
						$ex					= explode("/",$value);
						
						//obtain the continent
						if ($continent!=$ex[0]){
							$continent	= $ex[0];
						}
	
						// Add it to the Array...
						$this->ArrTimezones[$continent][$value] = $ex[1];            
					}
				}
    	}
    	return $this->ArrTimezones;
    }
        
    /**
    * Return Date in RFC 2822 Compatible Output
    *
    * @param $tstamp     Timestamp
    * @return RFC2822 Time String
    */
    public function RFC2822($dtime){
    	$date = new DateTime($this->helper_dtime($dtime), $this->userTimeZone);
    	return $date->format(DATE_RFC3339);
    }
  	
  	public function toSeconds($time, $ff='day'){
  		switch($ff){
  			case 'day':			$out = $time*24*60*60;
  			case 'hour':		$out = $time*60*60;
  			case 'minute':	$out = $time*60*60;
  		}
  		return $out;
  	}
  	
  	function MonthName($month, $year){
  	 global $user;
  	 $month = sprintf("%02d",$month);
      switch ($month){
          case '01': 	$dayInMonth = 31; $MonthName=$user->lang['time_monthnames'][0]; break;
          case '02': 	( $year%4 == 0 ) ? $dayInMonth = 29 : $dayInMonth = 28; $MonthName=$user->lang['time_monthnames'][1]; break;
          case '03':  $dayInMonth = 31; $MonthName=$user->lang['time_monthnames'][2]; break;
          case '04':  $dayInMonth = 30; $MonthName=$user->lang['time_monthnames'][3]; break;
          case '05':  $dayInMonth = 31; $MonthName=$user->lang['time_monthnames'][4]; break;
          case '06':  $dayInMonth = 30; $MonthName=$user->lang['time_monthnames'][5]; break;
          case '07':  $dayInMonth = 31; $MonthName=$user->lang['time_monthnames'][6]; break;
          case '08':  $dayInMonth = 31; $MonthName=$user->lang['time_monthnames'][7]; break;
          case '09':  $dayInMonth = 30; $MonthName=$user->lang['time_monthnames'][8]; break;
          case '10':  $dayInMonth = 31; $MonthName=$user->lang['time_monthnames'][9]; break;
          case '11':  $dayInMonth = 30; $MonthName=$user->lang['time_monthnames'][10]; break;
          case '12':  $dayInMonth = 31; $MonthName=$user->lang['time_monthnames'][11]; break;
      }
      $month_array = array(
                        'days'    => $dayInMonth,
                        'name'    => $MonthName
                      );
      return $month_array;
    }
  	
  	/**
  	* Generate the Weekday Array for Calendar Mode
  	*
  	* @param $startday The First day in the week
  	* @return array week
  	*/
  	public function weekdays($startday){
      global $user;
      $weekdays0 = array($user->lang['time_daynames'][6]);
      $weekdays1 = array(
                    $user->lang['time_daynames'][0],
                    $user->lang['time_daynames'][1],
                    $user->lang['time_daynames'][2],
                    $user->lang['time_daynames'][3],
                    $user->lang['time_daynames'][4],
                    $user->lang['time_daynames'][5]
                  );
      
      return ($startday == 'monday') ? array_merge($weekdays1, $weekdays0) : array_merge($weekdays0, $weekdays1);
    }
  	
  	public function dateDiff($dt1, $dt2, $out='sec'){
  		// Build the Dates
	    $dt1	= new DateTime(((is_numeric($dt1)) ? "@$dt1" :$dt1), $this->userTimeZone);
	    $dt2	= new DateTime(((is_numeric($dt2)) ? "@$dt2" :$dt2), $this->userTimeZone);
	    $ts1	= $dt1->format('Y-m-d H:i:s');
	    $ts2	= $dt2->format('Y-m-d H:i:s');
	    
	    // calculate the difference
	    $diff = abs(strtotime($ts1)-strtotime($ts2));
	    
	    // generate the output
	    switch($out){
	    	case 'day':
	    		return $diff /= 3600*24;
	    	break;
	    	case 'min':
	    		return $diff /= 60;
	    	break;
				case 'hour':
	    		return $diff /= 3600;
	    	break;
	    	default:
	    		return $diff;
	    }
	    return $diff;
		}
		
		// Count Dates in an array higher than the start date
		public function countFutureDates($array, $startdate, $funcname= 'raw_date'){
      $this->cfd_startdat2		= $startdate;
      $this->cfd_tmpfuncname	= $funcname;
      if(is_array($array)){
        return count(array_filter($array, array($this, 'helper_countfuturedays')));
      }else{
        return false;
      }
    }
    
    // Count Entries in an array between two dates
    function countBetweenDates($array, $startdate, $enddate){
      $this->cbd_enddate    = ($enddate) ? $enddate : $this->gen_time();
      $this->cbd_startdate  = $startdate;
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
	public function format($format) {
		global $user;
		
		// define the english names
		$english_days		= array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday');
		$english_months = array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');
		
		if(is_array($user->lang['time_daynames']) && count($user->lang['time_daynames']) > 1){
			$out = str_replace($english_days, $user->lang['time_daynames'], parent::format($format));
			return str_replace($english_months, $user->lang['time_monthnames'], $out);
		}else{
			return parent::format($format);
		}
	}
}
?>