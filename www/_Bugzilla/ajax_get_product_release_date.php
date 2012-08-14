<?php
/*
	Copyright by Tomas Rapkauskas bugzilla_base/license.txt 
	All rights reserved.
	To use this component please contact slidertom@gmail.com to obtain a license.
*/

require_once("../bugzilla_base/connect_to_bugzilla_db.php");
require_once("bugs_fnc.php");
require_once("bugs_start_end_dates.php");

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

$users    = get_user_profiles($dbh); // <userid><login_name>
$products = products_get($dbh);
$bugs = bugs_get_open_by_milestone($dbh, $users, $products, $product_id, $milestone);

$developer_bugs = array();

foreach ($bugs as $bug_id => $bug )
{
	$dev_id = $bug->m_assigned_to->m_id;
	
	if ( !isset($developer_bugs[$dev_id]) )
	{
		$dev_bugs = bugs_get_by_developer($dbh, $users, $products, $dev_id);
		bugs_update_worked_time($dbh, $dev_bugs);
		bugs_init_start_end_dates($dev_bugs);
		$developer_bugs[$dev_id] = $dev_bugs;
	}
	
	$dev_bug = $developer_bugs[$dev_id][$bug_id];
	$bug->m_start_date = $dev_bug->m_start_date;
	$bug->m_end_date   = $dev_bug->m_end_date;
}

$end_date = null;

foreach ($bugs as $bug )
{
	if ( $end_date == null )
	{
		$end_date = $bug->m_end_date;
	}
	else if ( $end_date < $bug->m_end_date )
	{
		$end_date = $bug->m_end_date;
	}
}

if ( !$end_date )
{
	echo "";
	return;
}

$formated_date = $end_date->format('Y-m-d');
$date = new DateTime($formated_date);  	
 
if ( $end_date > $date )
{
	$end_date->add(new DateInterval('P1D')); // add 1 day -> rounding
	if ( is_weekend($end_date) )
	{
		$end_date->add(new DateInterval('P2D')); // add 2 days (saturday and sunday)
		//assert(!is_weekend($now));
	}
	
	$formated_date = $end_date->format('Y-m-d');
}

echo "$formated_date";

?>