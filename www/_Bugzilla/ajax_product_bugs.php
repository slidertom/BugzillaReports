<?php
/*
	Copyright by Tomas Rapkauskas bugzilla_base/license.txt 
	All rights reserved.
	To use this component please contact slidertom@gmail.com to obtain a license.
*/

require_once("../bugzilla_base/connect_to_bugzilla_db.php");
require_once("bugs_fnc.php");

//echo "1";
$milestone  = isset($_GET["Milestone"]) ? $_GET["Milestone"] : -1;
$product_id = isset($_GET["Product"])   ? $_GET["Product"]   : -1;
if ( $milestone == -1 && $product_id == -1 ){
	return;
}

$dbh = connect_to_bugzilla_db();
if ( $dbh == NULL ) {
	return;
}	

bugs_create_table($dbh, $product_id, $milestone);

?>