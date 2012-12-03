<?php

// Simple SQL Helper Class (Hack from the EQDKP sql class)
class SqlHelper
{
	var $db;
    var $query_id    = 0;                   // Query ID                 @var query_id
    var $record      = array();             // Record                   @var record
    var $record_set  = array();             // Record set               @var record_set
    var $query_count = 0;                   // Query count              @var query_count
    var $queries     = array();             // Queries                  @var queries

	function SqlHelper($dbhost, $dbname, $dbuser, $dbpass)
	{
		$this->db = @mysql_pconnect($dbhost, $dbuser, $dbpass);

        if (!is_resource($this->db) || is_null($this->db))
        {
			var_dump("Unable to connect to SQL host: " . $host);
			unset($this->db);
        }
        
		if (isset($this->db) && !@mysql_select_db($dbname, $this->db))
		{
			var_dump("Unable to select the database: " . $dbname);
			@mysql_close($this->db);
			unset($this->db);
		}
	}
	
	function close()
	{
       if ($this->db)
        {
            if ($this->query_id)
            {
                @mysql_free_result($this->query_id);
            }
            
            @mysql_close($this->db);
        }
	}

    function error()
    {
        $result['message'] = @mysql_error();
        $result['code'] = @mysql_errno();
        
        return $result;
    }

    
	function query($query)
    {
        // Remove pre-existing query resources
        unset($this->query_id);
        
        if ( $query != '' )
        {
            $this->query_count++;
            $this->query_id = @mysql_query($query, $this->db);
        }
        if ( !empty($this->query_id) )
        {
            if ( $DEBUG == 2 )
            {
                $this->queries[$this->query_count] = $query;
            }
            
            unset($this->record[$this->query_id]);
            unset($this->record_set[$this->query_id]);
            return $this->query_id;
        }
        else
        {
            if ( $DEBUG )
            {
                $error = $this->error();
                $message  = 'SQL query error<br /><br />';
                $message .= 'Query: '.$query.'<br />';
                $message .= 'Message: '.$error['message'].'<br />';
                $message .= 'Code: '.$error['code'];
                
				die($message);
            }
            
            return false;
        }
    }

    function query_first($query)
    {
        $this->query($query);
        $record = $this->fetch_record($this->query_id);
        $this->free_result($this->query_id);
        
        return $record[0];
    }

    function fetch_record($query_id = 0)
    {
        if ( !$query_id )
        {
            $query_id = $this->query_id;
        }
        
        if ( $query_id )
        {
            $this->record[$query_id] = @mysql_fetch_array($query_id);
            return $this->record[$query_id];
        }
        else
        {
            return false;
        }
    }
    
    function free_result($query_id = 0)
    {
        if ( !$query_id )
        {
            $query_id = $this->query_id;
        }

        if ( $query_id )
        {
            unset($this->record[$query_id]);
            unset($this->record_set[$query_id]);

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