<?php

// Simple SQL Helper Class (Hack from the EQDKP sql class)
class SqlHelper
{

	var $db;
    var $connected      = false;               // is connected ?
    var $query_id       = 0;                   // Query ID                 @var query_id
    var $record         = array();             // Record                   @var record
    //var $closeConnect   = false;

	function SqlHelper($dbhost, $dbname, $dbuser, $dbpass, $bNewConnection = false)
	{
        $this->Connect($dbhost, $dbname, $dbuser, $dbpass, $bNewConnection);
	}
    
    function Connect($dbhost, $dbname, $dbuser, $dbpass, $bNewConnection = false)
    {
        if ($this->connected == true)
            return (false);
            
        if ($bNewConnection == true && isset($GLOBALS["connectionId"]))
        {
            $bNewConnection = false;
            $this->db = $GLOBALS["connectionId"];
        }
        else
        {
            $this->db = mysql_connect($dbhost, $dbuser, $dbpass, $bNewConnection);
        }
        if ($bNewConnection)
            $GLOBALS["connectionId"] = $this->db;

        if (!($this->db) || is_null($this->db))
        {
			var_dump("Unable to connect to SQL host: " . $dbhost);
			unset($this->db);
            return (false);
        }
		if (isset($this->db) && !@mysql_select_db($dbname, $this->db))
		{
			var_dump("Unable to select the database: " . $dbname);
            /*if ($closeConnect == true)
			    mysql_close($this->db);*/
			unset($this->db);
            return (false);
		}
        $this->connected = true;        
        return (true);
    }

	function close()
	{
        if ($this->connected == false)
            return (false);
        if (isset($this->db) && $this->db)
        {
	        if (isset($this->query_id) && $this->query_id)
	        {
	            @mysql_free_result($this->query_id);
                unset($this->query_id);                
	        }            
            // Cela pose probleme dans le cas de la reutilisation d'une connection
            /*if ($closeConnect == true)
	            @mysql_close($this->db);*/
        }
        $this->connected == false;
        return (true);
	}


    function error()
    {
        $result['message'] = @mysql_error();
        $result['code'] = @mysql_errno();
        
        return $result;
    }

    
	function query($query)
    {	
        if ($this->connected == false)
            return (false);
        
        // Remove pre-existing query resources
        unset($this->query_id);
        
        if ($query != '')
            $this->query_id = @mysql_query($query, $this->db);
        if (!empty($this->query_id))
            return ($this->query_id);
        else
            return (false);
    }


    function query_first($query)
    {
        if ($this->connected == false)
            return (false);
        
        $this->query($query);
        $record = $this->fetch_record($this->query_id);
        $this->free_result($this->query_id);
        unset($this->query_id);
        return $record[0];
    }


    function fetch_record($query_id = 0)
    {
        if (!$query_id)
            $query_id = $this->query_id;
        if ($query_id)
        {
            $this->record = @mysql_fetch_array($query_id);
            return $this->record;
        }
        else
        {
            return false;
        }
    }
    
    function free_result($query_id = 0)
    {
        if ($query_id == $this->query_id)
            unset($this->query_id);
        if (!$query_id && isset($this->query_id))
        {
            $query_id = $this->query_id;
            unset($this->query_id);
        }        
        if (is_resource($query_id))
        {
            @mysql_free_result($query_id);
            return true;
        }
        else
        {
            return false;
        }
    }
}
?>