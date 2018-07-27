<?php
/*
	Copyright by Tomas Rapkauskas bugzilla_base/license.txt 
	All rights reserved.
	To use this component please contact slidertom@gmail.com to obtain a license.
*/

require_once("_bugzilla_reports_settings.php");

function parse_row_to_bug_data($row, &$users, &$products)
{
	$bug = new CBugData();
	$bug->m_bug_id           = $row['bug_id'];
	$bug->m_severity         = $row['bug_severity'];
	$bug->m_priority         = $row['priority'];
	$bug->m_assigned_to      = $users[$row['assigned_to']]; // assigned to
	$bug->m_reporter         = $users[$row['reporter']]; // reporter
	$bug->m_summary          = $row['short_desc'];
	$bug->m_estimated_time   = $row['estimated_time'];
	$bug->m_remaining_time   = $row['remaining_time'];
	$bug->m_status           = $row['bug_status'];
	$bug->m_product          = $products[$row['product_id']];
	$bug->m_target_milestone = $row['target_milestone'];
	
	return $bug;
}

function bugs_status_to_sql($defines)
{
	$first = true;
	$status_sql;
	foreach ($defines as $def)
	{	
		if ( $first )
		{
			$status_sql = "bug_status='".$def."'";
			$first = false;
		}
		else
		{
			$status_sql = $status_sql."OR bug_status='".$def."'";
		}
	}
	
	return $status_sql;	
}

function bugs_get(&$dbh, &$users, &$products, $sql)
{
	$bugs = $dbh->query($sql);
	$bugs_array = array();
	foreach ($bugs as $row)
	{
		$bug = parse_row_to_bug_data($row, $users, $products);
		$bugs_array[$bug->m_bug_id] = $bug;
	}
	return $bugs_array;
}
function get_bug_work_time(&$dbh, $bug_id)
{
	$sql   = "SELECT longdescs.work_time FROM longdescs where bug_id = '".$bug_id."'";
	$times = $dbh->query($sql);
	$work_time = 0;
	foreach ($times as $row)
	{
		$work_time += $row['work_time'];
	}
	return $work_time;
}

function bugs_get_by_developer(&$dbh, &$users, &$products, $developer_id)
{
	$defines    = get_bugs_open_defines();
	$status_sql	= bugs_status_to_sql($defines);
	$sql = "SELECT * FROM bugs where (".$status_sql.") AND assigned_to ='$developer_id'";
	return bugs_get($dbh, $users, $products, $sql);
}

function bug_get_by_bug_id(&$dbh, &$users, &$products, $bug_id)
{
	try
	{
		$sql = "SELECT * FROM bugs where bug_id ='$bug_id'";
		//echo "$sql";
		$bugs = bugs_get($dbh, $users, $products, $sql);
		foreach ($bugs as $bug ) 
		{
			return $bug;
		}
	}
	catch(PDOException $e)
	{
		echo $e->getMessage();
	}
	
	return NULL;
}

function bugs_get_assigned_by_developer(&$dbh, &$users, &$products, $developer_id)
{
	$sql = "SELECT * FROM bugs where (bug_status='ASSIGNED' OR bug_status='REOPENED') AND assigned_to ='$developer_id'";
	return bugs_get($dbh, $users, $products, $sql);
}

function bugs_get_open_by_product(&$dbh, &$users, &$products, $product_id)
{
	$defines    = get_bugs_open_defines();
	$status_sql	= bugs_status_to_sql($defines);
	$sql = "SELECT * FROM bugs where (".$status_sql.") AND product_id ='$product_id' ORDER BY bug_severity";
	return bugs_get($dbh, $users, $products, $sql);
}

function bugs_get_assigned_by_product(&$dbh, &$users, &$products, $product_id)
{
	$sql = "SELECT * FROM bugs where (bug_status='ASSIGNED' OR bug_status='REOPENED') AND product_id ='$product_id' ORDER BY bug_severity";
	return bugs_get($dbh, $users, $products, $sql);
}

function bugs_get_open_by_milestone(&$dbh, &$users, &$products, $product_id, $mile)
{
	$defines    = get_bugs_open_defines();
	$status_sql	= bugs_status_to_sql($defines);
	$sql = "SELECT * FROM bugs where (".$status_sql.") AND product_id ='$product_id' AND target_milestone='$mile' ORDER BY bug_severity";
	return bugs_get($dbh, $users, $products, $sql);
}

function bugs_get_closed_by_milestone(&$dbh, &$users, &$products, $product_id, $mile)
{
	$defines = get_bugs_close_defines();
	$status_sql	= bugs_status_to_sql($defines);
	$sql = "SELECT * FROM bugs where (".$status_sql.") AND product_id ='$product_id' AND target_milestone='$mile' ORDER BY bug_severity";
	return bugs_get($dbh, $users, $products, $sql);
}

function get_developer_bugs_count($dbh, $developer_id)
{
	$result = 0;
	try
	{
		$defines = get_bugs_open_defines();
		$status_sql	= bugs_status_to_sql($defines);
		
		$sql = "SELECT COUNT(*) FROM bugs where (".$status_sql.") AND assigned_to ='$developer_id'";
		$result = $dbh->query($sql);
	}
	catch(PDOException $e)
	{
		echo $e->getMessage();
		return 0;
	}
	
	foreach ($result as $row)
	{
		return $row['COUNT(*)'];
	}

	return $result;
}

function is_non_web_kozinjn_bug($bug)
{
	return $bug->m_product->m_id != "26";
}

function get_worked_developer_bugs_by_dates($dbh, $developer_id, $quat_beg, $quat_end, &$users, &$products)
{
	$sql   = "SELECT bugs.*,longdescs.work_time FROM longdescs,bugs where longdescs.bug_when between'".$quat_beg."'and'".$quat_end."' and longdescs.work_time>0 and longdescs.who='".$developer_id."'and bugs.bug_id = longdescs.bug_id";
	$times = $dbh->query($sql);
	$bugs  = array();
	
	foreach ($times as $row)
	{
		$time   = $row['work_time'];
		$bug_id = $row['bug_id'];
		
		if ( isset($bugs[$bug_id]) )
		{
			$bugs[$bug_id]->m_worked_time += $time;
		}
		else
		{
			$bug = parse_row_to_bug_data($row, $users, $products);
			$bug->m_worked_time += $time;
			$bugs[$bug_id] = $bug;
		}
	}
	
	$bugs = array_filter($bugs, "is_non_web_kozinjn_bug");
	
	return $bugs;
}

?>