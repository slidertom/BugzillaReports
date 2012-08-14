<?php
/*
	Copyright by Tomas Rapkauskas bugzilla_base/license.txt 
	All rights reserved.
	To use this component please contact slidertom@gmail.com to obtain a license.
*/

require_once("bug_data.php");
require_once("add_work_hours.php");

function compare_priority($a, $b)
{
	$a = intval(trim($a, "P"));
	$b = intval(trim($b, "P"));
	//echo "|".$a."-".$b."|";
	return $a > $b;
}

// same priority bugs has same start end dates
function bugs_init_start_end_dates(&$bugs)
{
	$now = new DateTime("now");  	 
	
	bugs_explode_by_priority($priority, $bugs);
	uksort($priority, "compare_priority");
	
	$already_worked_this_day = 0;
	
	foreach ( $priority as $prior_bugs )
	{	
		//echo "|$prio_key|";
		$work_hours = get_bugs_work_time($prior_bugs);
		
		$start_date = clone $now;
		add_work_hours($now, $work_hours, $already_worked_this_day);
		$end_date = clone $now;
		
		foreach ( $prior_bugs as $bug )
		{
			$real_bug = $bugs[$bug->m_bug_id];
			$real_bug->m_start_date = $start_date;
			$real_bug->m_end_date   = $end_date;
		}
	}
}

// same priority bugs has different start end dates
// every bug has at least one day
function bugs_init_pseudo_start_end_dates(&$bugs)
{
	if ( !is_array($bugs) )
	{
		return;
	}
	
	$now = new DateTime("now");  	 
	
	$already_worked_this_day = 0;
	
	bugs_explode_by_priority($priority, $bugs);
	uksort($priority, "compare_priority");
	
	foreach ( $priority as $prior_bugs )
	{	
		foreach ( $prior_bugs as $bug )
		{
			$bug_work_hours = $bug->get_bug_remaining_time();
			
			$start_date = clone $now;
			add_work_hours($now, $bug_work_hours, $already_worked_this_day);
			$end_date   = clone $now;
		
			$real_bug = $bugs[$bug->m_bug_id];
			$real_bug->m_start_date = $start_date;
			$real_bug->m_end_date   = $end_date;
		}
	}
}

?>