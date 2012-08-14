<?php
/*
	Copyright by Tomas Rapkauskas bugzilla_base/license.txt 
	All rights reserved.
	To use this component please contact slidertom@gmail.com to obtain a license.
*/

require_once("_bugzilla_reports_settings.php");

function connect_to_bugzilla_db()
{
	try 
	{
		$hostanme  = get_bugs_db_hostname();
		$bugs_name = get_bugs_db_name();
		$username  = get_bugs_db_username();
		$password  = get_bugs_db_password();
		
		$dbh = new PDO("mysql:host=$hostanme;dbname=$bugs_name", $username, $password);
		
		// echo a message saying we have connected 
		$dbh->exec('SET CHARACTER SET utf8');

		return $dbh;
	}
	catch(PDOException $e)
	{
		echo $e->getMessage();
	}
	
	return NULL;
}

?>


