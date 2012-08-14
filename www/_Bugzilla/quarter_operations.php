<?php
/*
	Copyright by Tomas Rapkauskas bugzilla_base/license.txt 
	All rights reserved.
	To use this component please contact slidertom@gmail.com to obtain a license.
*/

function CurrentQuarter(){
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

function bugs_get_quarter_begin_end($quat, &$quat_beg, &$quat_end)
{
	$year = date("Y");
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
		$quat_end = $year."-06-31";
	}
	else if ( $quat == 3 )
	{
		$quat_beg = $year."-07-01";
		$quat_end = $year."-09-31";
	}
	else if ( $quat == 4 )
	{
		$quat_beg = $year."-10-01";
		$quat_end = $year."-12-31";
	}
}

function bugs_get_quater_split_by_mile(&$mile_bugs, &$bugs)
{
	$mile_bugs = array();
	foreach ($bugs as $bug )
	{
		if ( !isset($mile_bugs[$bug->m_target_milestone]) )
		{
			$mile_bugs[$bug->m_target_milestone] = array();
		}
		
		$mile_bugs[$bug->m_target_milestone][] = $bug;
	}
}

function bugs_get_quater_split_by_product_and_mile(&$product_mile_bugs, &$bugs_array)
{
	bugs_explode_by_product($product_bugs, $bugs_array);
	
	foreach($product_bugs as $product_name => $product )
	{
		if ( !isset($product_mile_bugs[$product_name]) )
		{
			$product_mile_bugs[$product_name] = array();
		}
		
		bugs_get_quater_split_by_mile($mile_bugs, $product);
		
		foreach ($mile_bugs as $mile_name => $bugs )
		{
			$product_mile_bugs[$product_name][$mile_name] = $bugs;
		}
	}
}

function percent($num_amount, $num_total) 
{
	$count1 = $num_amount / $num_total;
	$count2 = $count1 * 100;
	$count = number_format($count2, 2);
	return $count;
}

?>