<?php
function open($save_path, $session_name)
{
  return(true);
}

function close()
{
  return(true);
}

function read($id)
{
	return apc_fetch('session_'.$id);					
}

function write($id, $sess_data)
{
	return apc_store('session_'.$id, $sess_data);
}

function destroy($id)
{
	return apc_delete($id);				
}

function gc($maxlifetime)
{
}
session_set_save_handler("open", "close", "read", "write", "destroy", "gc");
?>