<?php
/*
	Copyright by Tomas Rapkauskas bugzilla_base/license.txt 
	All rights reserved.
	To use this component please contact slidertom@gmail.com to obtain a license.
*/

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

?>