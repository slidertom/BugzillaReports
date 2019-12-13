<?php
require_once '../func/bugs_operations.php';
require_once '../tools/date_time_select.php';
require_once 'developer_milestone_table.php';

function developer_bugs_by_moth_by_mile($dbh, $users, $products, $developer_id, $year, $month)
{
	$bugs = bugs_get_developer_month_bugs($dbh, $users, $products, $developer_id, $year, $month);
    if ( !$bugs ) {
		echo "<h3>There are no bugs fixed.</h3>";
		return;
	}
	
	echo "<br>";
	
	echo "<table><tr>";
		echo "<td><b>Year: </b></td>";
		echo "<td>";create_years_select_impl($year);echo "</td>";
		echo "<td><b>Month: </b></td>";
		echo "<td>";create_month_select_impl($month);echo "</td>";
	echo "</tr></table>";
	
	developer_milestone_bugs_to_table($bugs);
}

?>