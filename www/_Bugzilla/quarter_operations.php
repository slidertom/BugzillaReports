<?php
/*
	Copyright by Tomas Rapkauskas bugzilla_base/license.txt 
	All rights reserved.
	To use this component please contact slidertom@gmail.com to obtain a license.
*/

function bugs_get_quater_split_by_mile(&$mile_bugs, &$bugs)
{
	$mile_bugs = array();
	foreach ($bugs as $bug)
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
	
	foreach ($product_bugs as $product_name => $product)
	{
		if ( !isset($product_mile_bugs[$product_name]) )
		{
			$product_mile_bugs[$product_name] = array();
		}
		
		bugs_get_quater_split_by_mile($mile_bugs, $product);
		
		foreach ($mile_bugs as $mile_name => $bugs)
		{
			$product_mile_bugs[$product_name][$mile_name] = $bugs;
		}
	}
}

function percent($num_amount, $num_total) 
{
    if ( $num_total == 0 ) {
        return 0;
    }
	$count1 = $num_amount / $num_total;
	$count2 = $count1 * 100;
	$count = number_format($count2, 2);
	return $count;
}

?>