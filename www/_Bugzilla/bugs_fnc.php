<?php
/*
	Copyright by Tomas Rapkauskas bugzilla_base/license.txt 
	All rights reserved.
	To use this component please contact slidertom@gmail.com to obtain a license.
*/

require_once("profiles.php");
require_once("products.php");
require_once("bug_data.php");
require_once("quarter_products.php");
require_once("../bugzilla_base/bugs_sql.php");

function bugs_update_worked_time(&$dbh, &$bugs_array)
{
	foreach ($bugs_array as $bug)
	{
		$bug->m_worked_time = get_bug_work_time($dbh, $bug->m_bug_id);
	}
}

function bugs_get_remaining_time(&$bugs_array)
{
	$remaining_time = 0;
	foreach ($bugs_array as $bug)
	{
		$remaining_time += $bug->get_bug_remaining_time();
	}
	return $remaining_time;
}

function bugs_get_complete($rem, $wrk)
{
	$all = $rem + $wrk;
	$per = $all > 0 ? $wrk / $all * 100 : 0;
	$per = number_format($per, 1);
	$per = $per. "%";
	return $per;
}

function bugs_get_work_time(&$bugs_array)
{
	if ( !is_array($bugs_array) )
	{
		return 0;
	}
	
	$work_time = 0;
	foreach ($bugs_array as $bug)
	{
		$work_time += $bug->m_worked_time;
	}
	return $work_time;
}

function echo_table_summary_header()
{
	echo "<thead>\n";
	echo "<tr class='header'>\n";
	/*1*/echo "\t<th width=100> &nbsp;            </th>\n";
	/*2*/echo "\t<th width= 50> Bugs              </th>\n";
	/*3*/echo "\t<th width= 45> Worked&nbsp;(h)   </th>\n";
	/*4*/echo "\t<th width= 45> Left&nbsp;(h)     </th>\n";
	/*5*/echo "\t<th width= 40> Completed&nbsp;(%)</th>\n";
	/*6*/echo "\t<th width= 45> Worked&nbsp;(days)</th>\n";
	/*7*/echo "\t<th width= 45> Left&nbsp;(days)  </th>\n";
	echo "</tr>\n";
	echo "</thead>\n";
}

function echo_table_summary($bug_cnt, $all_work_time, $all_remaining_time, $all_complete, $title)
{
	$left_days = hours_to_days($all_remaining_time);
	$work_days = hours_to_days($all_work_time);
	
	echo "<tr class = 'summary'>\n";
	/*1*/echo "<td width=100>          $title                                        </td>\n";
	/*2*/echo "<td class = 'center' width=50>     $bug_cnt                           </td>\n";
	/*3*/echo "<td align=right width=50>          $all_work_time                     </td>\n";
	/*4*/echo "<td align=right width=50>          $all_remaining_time                </td>\n";
	/*5*/echo "<td align=right width=80>          $all_complete                      </td>\n";
	/*6*/echo "<td align=right width=80>          $work_days                         </td>\n";
	/*7*/echo "<td align=right width=80>          $left_days                         </td>\n";
	echo "</tr>\n";
}

function open_bugs_to_table(&$bugs_opened_array)
{
	$all_opened_remaining_time = bugs_get_remaining_time($bugs_opened_array);
	$all_opened_work_time      = bugs_get_work_time($bugs_opened_array);
	$all_opened_complete       = bugs_get_complete($all_opened_remaining_time, $all_opened_work_time);
	$bug_opened_cnt            = count($bugs_opened_array);

	$opened_bugs = "Opened bugs";
	
	echo "<h3> Summary: </h3>";
	echo "<table class = 'summary'>\n";
	echo_table_summary_header();
	echo "<tbody>";
	echo_table_summary($bug_opened_cnt, $all_opened_work_time, $all_opened_remaining_time, $all_opened_complete, $opened_bugs);
	echo "</table>\n";
	
	echo "<br>";
	echo "<p><em>TIP!</em> Sort multiple columns simultaneously by holding down the shift key and clicking a second, third or even fourth column header.</p>\n";
	
	if ( count($bugs_opened_array) > 0 )
	{
		echo "<h3> ${opened_bugs}: </h3>";
		bugs_echo_table($bugs_opened_array, "", "openTable tablesorter show_milestone");
	}
}

function bugs_to_table(&$bugs_opened_array, &$bugs_closed_array)
{
	$all_opened_remaining_time = bugs_get_remaining_time($bugs_opened_array);
	$all_opened_work_time      = bugs_get_work_time($bugs_opened_array);
	$all_opened_complete       = bugs_get_complete($all_opened_remaining_time, $all_opened_work_time);
	$bug_opened_cnt            = count($bugs_opened_array);
	
	$all_closed_work_time      = bugs_get_work_time($bugs_closed_array);
	$bug_closed_cnt            = count($bugs_closed_array);
	
	$all_bugs_cnt             = $bug_closed_cnt + $bug_opened_cnt;
	$all_work_time            = $all_opened_work_time + $all_closed_work_time;
	//$bug_all_cnt              = $bug_opened_cnt."/".$all_bugs_cnt;
	$all_complete             = bugs_get_complete($all_opened_remaining_time, $all_work_time);
	
	$opened_bugs = "Opened bugs";
	$closed_bugs = "Closed bugs";
	$all_bugs    = "All bugs";
	
	echo "<h3> Summary: </h3>";
	echo "<table class = 'summary'>\n";
	
	echo_table_summary_header();
	echo "<tbody>";
	echo_table_summary($bug_opened_cnt, $all_opened_work_time, $all_opened_remaining_time, $all_opened_complete, $opened_bugs);
	if ( $bug_closed_cnt > 0 )
	{
		echo_table_summary($bug_closed_cnt, $all_closed_work_time, "0", "100%", $closed_bugs);
	}
	echo "</tbody>";
	if ( $bug_closed_cnt > 0 )
	{
		echo "<tfoot>";
		echo_table_summary($all_bugs_cnt,   $all_work_time,        $all_opened_remaining_time, $all_complete,        $all_bugs);
		echo "</tfoot>";
	}
	echo "</table>\n";
	
	echo "<br>";
	echo "<p><em>TIP!</em> Sort multiple columns simultaneously by holding down the shift key and clicking a second, third or even fourth column header.</p>\n";
	
	if ( count($bugs_opened_array) > 0 )
	{
		echo "<h3> ${opened_bugs}: </h3>";
		bugs_echo_table($bugs_opened_array, "", "openTable tablesorter");
	}
	
	if ( is_array($bugs_closed_array) && count($bugs_closed_array) > 0 )
	{
		echo "<h3> ${closed_bugs}: </h3>";
		bugs_echo_table($bugs_closed_array, "", "closeTable tablesorter");
	}
}

function bugs_create_table(&$dbh, $product_id, $milestone)
{
	$users    = get_user_profiles($dbh); // <userid><login_name>
	$products = products_get($dbh);
	
	if ( $milestone == "open_bugs" )
	{
		$bugs_array = bugs_get_open_by_product($dbh, $users, $products, $product_id);
		bugs_update_worked_time($dbh, $bugs_array);
		open_bugs_to_table($bugs_array);
		return;
	}

	if ( $milestone == "assigned_bugs" )
	{
		$bugs_array = bugs_get_assigned_by_product($dbh, $users, $products, $product_id);
		bugs_update_worked_time($dbh, $bugs_array);
		open_bugs_to_table($bugs_array);
		return;
	}
	
	if ($milestone == "quarter" )
	{
		$bugs_array = bugs_get_quarter_bugs($dbh, $users, $products, $product_id);
		quarter_bugs_to_table($bugs_array);
		return;
	}
	
	$bugs_array = bugs_get_open_by_milestone($dbh, $users, $products, $product_id, $milestone);
	bugs_update_worked_time($dbh, $bugs_array);

	$bugs_closed_array  = bugs_get_closed_by_milestone($dbh, $users, $products, $product_id, $milestone);
						  bugs_update_worked_time($dbh, $bugs_closed_array);
					  
	bugs_to_table($bugs_array, $bugs_closed_array);
}

?>