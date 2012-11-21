<?php
/*
	Copyright by Tomas Rapkauskas bugzilla_base/license.txt 
	All rights reserved.
	To use this component please contact slidertom@gmail.com to obtain a license.
*/

if ( !ob_start("ob_gzhandler") )
{
	echo "Client does not support gzip!";
}

require_once("../bugzilla_base/connect_to_bugzilla_db.php");
require_once("bugs_fnc.php");
require_once("init_product_bugs_dates.php");

$json_array = array();

$milestone  = isset($_GET["Milestone"]) ? $_GET["Milestone"] : -1;
$product_id = isset($_GET["Product"])   ? $_GET["Product"]   : -1;
if ( $milestone == -1 && $product_id == -1 ){
	echo json_encode($json_array);
	return;
}

$dbh = connect_to_bugzilla_db();
if ( $dbh == NULL ) {
	echo json_encode($json_array);
	return;
}	

$users    = get_user_profiles($dbh); // <userid><login_name>
$products = products_get($dbh);
$bugs     = bugs_get_open_by_milestone($dbh, $users, $products, $product_id, $milestone);
if ( count($bugs) <= 0 )
{
	echo json_encode($json_array);
	return;
}

init_product_bugs_dates($dbh, $bugs);
bugs_explode_by_product_developer_id($product_developer_bugs, $bugs);

$bugs_css = array("ganttRed GanttBug", "ganttGreen GanttBug", "ganttOrange GanttBug");
$bugs_css_count = count($bugs_css);

$now_date = new DateTime("now"); // workaround
$now_date_format = $now_date->format('Y-m-d');

foreach ($product_developer_bugs as $developer_id => $developer_bugs)
{
	$dev_bugs = array();
	$dev_bugs["name"] = $users[$developer_id]->m_real_name;
	$dev_bugs["desc"] = "";

	$css_index = 0;	
	$dev_bugs_array = array();

	foreach ($developer_bugs as $bug_id => $bug)
	{
		$rem_time = $bug->get_bug_remaining_time();
		if ( $rem_time <= 0	 )
		{
			continue;
		}

		if ( strcmp($bug->m_start_date->format('Y-m-d'), $now_date_format) == 0 )
		{
			$bug->m_start_date->sub(new DateInterval('P1D')); // this is workaround, somehow today bugs are shifted to the tomorrow if time > 12h
			//$bug->m_end_date->sub(new DateInterval('P1D'));   // this is workaround, somehow all days are shifted
		}
		
		$bug_array = array();
		$from_mili = $bug->m_start_date->format("U")*1000;
		$to_mili   = $bug->m_end_date->format("U")*1000;
		$bug_array["from"]        = "/Date(".$from_mili.")";
		$bug_array["to"]          = "/Date(".$to_mili.")";
		$bug_array["label"]       = $bug_id;
		//$bug_array["label"]       = $bug->m_start_date->format("Y-m-d H:i").$bug->m_end_date->format("Y-m-d H:i")."|".$bug_array["from"]."|".$bug_array["to"]."|".$bug->m_end_date->format("T");
		//$bug_array["label"]       = $bug->m_start_date->format("Y-m-d")."||".$bug->m_end_date->format("Y-m-d H:i");
		$bug_array["customClass"] = $bugs_css[$css_index];
		$bug_array["dataObj"]     = generate_bug_link($bug_id);
		$dev_bugs_array[]         = $bug_array;
			
		++$css_index;
		$css_index = ($css_index == $bugs_css_count) ? 0 : $css_index;
	}
	
	$dev_bugs["values"] = $dev_bugs_array;
	
	$json_array[] = $dev_bugs;
	//name: "Sprint 0",
	//desc: "Analysis",
	//values: 
	//[
	//				{                
	//					from: "/Date(1320192000000)/",
	//					to: "/Date(1322401600000)/",
	//					label: "Requirement Gathering", 
	//					customClass: "ganttRed",
    //
	//				}, {
	//					from: "/Date(1323802400000)/",
	//					to: "/Date(1325685200000)/",
	//					label: "Development", 
	//					customClass: "ganttGreen"
	//				}
	//]
}

echo json_encode($json_array);

?>