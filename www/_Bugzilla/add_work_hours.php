<?php
/*
	Copyright by Tomas Rapkauskas bugzilla_base/license.txt 
	All rights reserved.
	To use this component please contact slidertom@gmail.com to obtain a license.
*/

function is_weekend(&$timestamp)
{
	$dw = $timestamp->format("w");
	if ( $dw == 6 || $dw == 0 ) // note: in the other countries Sunday is a work day, Friday is a weekend
	{
		return true;
	}
	
	return false;
}

function add_work_hours(&$now, $work_hours, &$already_worked_this_day)
{
	if ( $work_hours <= 0 )
	{
		return;
	}
	
	$work_hours_per_day = 8;
	
	$hour_interval = new DateInterval('PT3600S');
	for ( $hour = 0; $hour < $work_hours; ++$hour)
	{
		++$already_worked_this_day;
		$now->add($hour_interval);
		
		if ( $already_worked_this_day >= $work_hours_per_day )
		{ 
			$current_day_hour = intval($now->format("G")); // 0:23
			$current_day_hour = $current_day_hour == 0 ? 24 : $current_day_hour;
			$time_to_add = 24 - $current_day_hour; 
			$hour_part_seconds = intval($time_to_add*3600);
			$hour_part_interval = new DateInterval("PT".$hour_part_seconds."S");
			$now->add($hour_part_interval); // move to the next day
			
			$already_worked_this_day = 0;
		}
		
		if ( is_weekend($now) )
		{
			$now->add(new DateInterval('P2D')); // add 2 days (saturday and sunday)
			//assert(!is_weekend($now));
		}
	}
}

?>