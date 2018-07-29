<?php

function CurrentMonth() {
     $n = date('n');
     return $n;
}

function bugs_get_month_begin_end($month, &$month_beg, &$month_end)
{
	$year = date("Y");
	if ( $month == 0 ) {
		$month = 12;
		$year = $year - 1;
	}
	
	if ( $month == 1 )
	{
		$month_beg = $year."-01-01";
		$month_end = $year."-01-31";
	}
	else if ( $month == 2 )
	{
		$month_beg = $year."-02-01";
		$month_end = $year."-02-29"; // leap year
	}
	else if ( $month == 3 )
	{
		$month_beg = $year."-03-01";
		$month_end = $year."-03-31";
	}
	else if ( $month == 4 )
	{
		$month_beg = $year."-04-01";
		$month_end = $year."-04-30";
	}
    else if ( $month == 5 )
	{
		$month_beg = $year."-05-01";
		$month_end = $year."-05-31";
	}
    else if ( $month == 6 )
	{
		$month_beg = $year."-06-01";
		$month_end = $year."-06-30";
	}
    else if ( $month == 7 )
	{
		$month_beg = $year."-07-01";
		$month_end = $year."-07-31";
	}
    else if ( $month == 8 )
	{
		$month_beg = $year."-08-01";
		$month_end = $year."-08-31";
	}
    else if ( $month == 9 )
	{
		$month_beg = $year."-09-01";
		$month_end = $year."-09-30";
	}
    else if ( $month == 10 )
	{
		$month_beg = $year."-10-01";
		$month_end = $year."-10-31";
	}
    else if ( $month == 11 )
	{
		$month_beg = $year."-11-01";
		$month_end = $year."-11-30";
	}
    else if ( $month == 12 )
	{
		$month_beg = $year."-12-01";
		$month_end = $year."-12-30";
	}
}

?>