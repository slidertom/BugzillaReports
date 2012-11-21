<?php

/*
	Copyright by Tomas Rapkauskas bugzilla_base/license.txt 
	All rights reserved.
	To use this component please contact slidertom@gmail.com to obtain a license.
*/

ob_start("ob_gzhandler");

require_once("developer_filters.php");
require_once("../bugzilla_base/connect_to_bugzilla_db.php");

if ( !isset($_GET["Developer"]) )
{
	return;
}

$developer_id = $_GET["Developer"];
$filter       = isset($_GET["Filter"]) ? $_GET["Filter"] : "open_bugs";

$dbh = connect_to_bugzilla_db();
if ( $dbh == NULL ) {
	return;
}	

create_developer_filters_combo($dbh, $developer_id, $filter);

?>