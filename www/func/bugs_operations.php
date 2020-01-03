<?php
/*
	Copyright by Tomas Rapkauskas bugzilla_base/license.txt 
	All rights reserved.
	To use this component please contact slidertom@gmail.com to obtain a license.
*/

require_once (__DIR__)."/../tools/date_time_util.php";

function bugs_split_by_milestone(&$bugs)
{
	$mile_bugs = array();
	foreach ($bugs as $bug) {
		if ( !isset($mile_bugs[$bug->m_target_milestone]) ) {
			$mile_bugs[$bug->m_target_milestone] = array();
		}
		$mile_bugs[$bug->m_target_milestone][] = $bug;
	}
	return $mile_bugs;
}


function bugs_get_developer_month_bugs(&$dbh, &$users, &$products, $developer_id, $year, $month)
{
    $month_beg; $month_end;
    get_month_begin_end($year, $month, $month_beg, $month_end);
    $bugs = get_worked_developer_bugs_by_dates($dbh, $developer_id, $month_beg, $month_end, $users, $products);
    return $bugs;
}


function bugs_get_product_bugs_by_dates(&$dbh, &$users, &$products, $product_id, $beg_date, $end_date)
{
	$sql   = "SELECT bugs.*,longdescs.work_time FROM longdescs,bugs where longdescs.bug_when between'".$beg_date." 00:00:00' and '".$end_date." 23:59:59' and longdescs.work_time!=0 and bugs.product_id='".$product_id."'and bugs.bug_id = longdescs.bug_id";
    $times = $dbh->query($sql);
    $bugs  = array();

    foreach ($times as $row)
    {
        $time   = $row['work_time'];
        $bug_id = $row['bug_id'];
        
        if ( isset($bugs[$bug_id]) ) {
            $bugs[$bug_id]->m_worked_time += $time;
        }
        else {
            $bug = parse_row_to_bug_data($row, $users, $products);
            $bug->m_worked_time += $time;
            $bugs[$bug_id] = $bug;
        }
    }
    
    return $bugs;
}

function bugs_get_product_quarter_bugs(&$dbh, &$users, &$products, $product_id, $year, $quat)
{
    $quat_beg;
    $quat_end;
    get_quarter_begin_end($year, $quat, $quat_beg, $quat_end);
    return bugs_get_product_bugs_by_dates($dbh, $users, $products, $product_id, $quat_beg, $quat_end);
}

function bugs_get_product_month_bugs(&$dbh, &$users, &$products, $product_id, $year, $month)
{
    $month_beg; $month_end;
    get_month_begin_end($year, $month, $month_beg, $month_end);
    return bugs_get_product_bugs_by_dates($dbh, $users, $products, $product_id, $month_beg, $month_end);
}

?>