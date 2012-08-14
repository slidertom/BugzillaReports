<?php
/*
	Copyright by Tomas Rapkauskas bugzilla_base/license.txt 
	All rights reserved.
	To use this component please contact slidertom@gmail.com to obtain a license.
*/

require_once("../_Bugzilla/bugs_fnc.php");

function get_developer_products($dbh, $developer_id)
{
	$users        = get_user_profiles($dbh); // <userid><login_name>
	$products     = products_get($dbh);
	
	$dev_products = array();
	// trade off: try to avoid selects, in case of database fileds change it will be less code to update
	$bugs = bugs_get_by_developer($dbh, $users, $products, $developer_id); 
	
	bugs_explode_by_product($pro_bugs, $bugs);
	
	foreach ($bugs as $bug )
	{
		if ( !isset($dev_products[$bug->m_product->m_id] ) )
		{
			$count = count($pro_bugs[$bug->m_product->m_name]);
			$dev_products[$bug->m_product->m_id] = $bug->m_product->m_name." (bugs count: ".$count.")";
		}
	}
	
	return $dev_products;
}

function create_developer_filters_combo($dbh, $sel_dev_id, $filter)
{
	$dev_products = get_developer_products($dbh, $sel_dev_id);
	
	$open_bugs            = "&nbsp;- Open Bugs -";
	$progress_bugs        = "&nbsp;- In Progress Bugs -";
	$quarter_bugs  		  = "&nbsp;- Quarter Bugs by Milestone -";
	$quarter_bugs_product = "&nbsp;- Quarter Bugs by Product   -";
	
	echo "<select id='Developer_Filters_Combo'>";
		if ( $filter == "" || $filter ==  "open_bugs" )
		{
			echo "<option selected  value='open_bugs'>".$open_bugs."</option>";	
		}
		else
		{
			echo "<option value='open_bugs'>".$open_bugs."</option>";	
		}
		
		if ( $filter == "assigned_bugs" )
		{
			echo "<option selected value='assigned_bugs'>".$progress_bugs."</option>";	
		}
		else
		{
			echo "<option value='assigned_bugs'>".$progress_bugs."</option>";	
		}
		
		if ( $filter == "quarter_bugs_product" )
		{
			echo "<option selected value='quarter_bugs_product'>".$quarter_bugs_product."</option>";	
		}
		else
		{
			echo "<option value='quarter_bugs_product'>".$quarter_bugs_product."</option>";	
		}
		
		if ( $filter == "quarter_bugs" )
		{
			echo "<option selected value='quarter_bugs'>".$quarter_bugs."</option>";	
		}
		else
		{
			echo "<option value='quarter_bugs'>".$quarter_bugs."</option>";	
		}
		
		foreach ($dev_products as $product_id => $product_name )
		{
			if ( $filter == $product_id )
			{
				echo "<option selected value='$product_id'>".$product_name."</option>";	
			}
			else
			{
				echo "<option value='$product_id'>".$product_name."</option>";	
			}	
		}
		
	echo"</select>";
}

?>