<?php

function hours_to_days($hours) {
	$all_days = $hours / 8;
	$all_days = number_format($all_days, 2);
	return $all_days;
}

function current_month() {
     $n = date('n');
     return $n;
}

function current_quater() {
     $n = date('n');
     if($n < 4){
          return "1";
     } elseif($n > 3 && $n <7){
          return "2";
     } elseif($n >6 && $n < 10){
          return "3";
     } elseif($n >9){
          return "4";
     }
}

function current_year() {
    $year = date("Y");
    return $year;
}

function is_leap_year($year) {
	return ((($year % 4) == 0) && ((($year % 100) != 0) || (($year % 400) == 0)));
}

function get_year_begin_end($year, &$year_beg, &$year_end)
{
    $year_beg = $year."-01-01";
    $year_end = $year."-12-31";
}

function get_quarter_begin_end($year, $quat, &$quat_beg, &$quat_end)
{
	if ( $quat == 0 )
	{
		$quat = 4;
		$year = $year - 1;
	}
	
	if ( $quat == 1 )
	{
		$quat_beg = $year."-01-01";
		$quat_end = $year."-03-31";
	}
	else if ( $quat == 2 )
	{
		$quat_beg = $year."-04-01";
		$quat_end = $year."-06-30";
	}
	else if ( $quat == 3 )
	{
		$quat_beg = $year."-07-01";
		$quat_end = $year."-09-30";
	}
	else if ( $quat == 4 )
	{
		$quat_beg = $year."-10-01";
		$quat_end = $year."-12-31";
	}
}

function get_month_begin_end($year, $month, &$month_beg, &$month_end)
{
	if ( $month == 0 ) {
		$month = 12;
		$year = $year - 1;
	}
	
	if ( $month == 1 ) {
		$month_beg = $year."-01-01";
		$month_end = $year."-01-31";
	}
	else if ( $month == 2 ) {
		$month_beg = $year."-02-01";
        if ( is_leap_year($year) ) {
            $month_end = $year."-02-29"; // leap year
        }
        else {
            $month_end = $year."-02-28"; 
        }
	}
	else if ( $month == 3 ) {
		$month_beg = $year."-03-01";
		$month_end = $year."-03-31";
	}
	else if ( $month == 4 ) {
		$month_beg = $year."-04-01";
		$month_end = $year."-04-30";
	}
    else if ( $month == 5 ) {
		$month_beg = $year."-05-01";
		$month_end = $year."-05-31";
	}
    else if ( $month == 6 ) {
		$month_beg = $year."-06-01";
		$month_end = $year."-06-30";
	}
    else if ( $month == 7 ) {
		$month_beg = $year."-07-01";
		$month_end = $year."-07-31";
	}
    else if ( $month == 8 ) {
		$month_beg = $year."-08-01";
		$month_end = $year."-08-31";
	}
    else if ( $month == 9 ) {
		$month_beg = $year."-09-01";
		$month_end = $year."-09-30";
	}
    else if ( $month == 10 ) {
		$month_beg = $year."-10-01";
		$month_end = $year."-10-31";
	}
    else if ( $month == 11 ) {
		$month_beg = $year."-11-01";
		$month_end = $year."-11-30";
	}
    else if ( $month == 12 ) {
		$month_beg = $year."-12-01";
		$month_end = $year."-12-31";
	}
}

?>