<?php

/*
	Copyright by Tomas Rapkauskas bugzilla_base/license.txt 
	All rights reserved.
	To use this component please contact slidertom@gmail.com to obtain a license.
*/

require_once("bugs_start_end_dates.php");
require_once("bugs_fnc.php");

function init_product_bugs_dates($dbh, &$bugs)
{
	$developer_bugs = array();

	foreach ($bugs as $bug_id => $bug )
	{
		$dev_id = $bug->m_assigned_to->m_id;
		
		if ( !isset($developer_bugs[$dev_id]) )
		{
			$dev_bugs = bugs_get_by_developer($dbh, $users, $products, $dev_id);
			bugs_update_worked_time($dbh, $dev_bugs);
			bugs_init_pseudo_start_end_dates($dev_bugs);
			$developer_bugs[$dev_id] = $dev_bugs;
		}
		
		$dev_bug = $developer_bugs[$dev_id][$bug_id];
		$bug->m_start_date = $dev_bug->m_start_date;
		$bug->m_end_date   = $dev_bug->m_end_date;
	}
}

?>
