<?php
/*
	Copyright by Tomas Rapkauskas bugzilla_base/license.txt 
	All rights reserved.
	To use this component please contact slidertom@gmail.com to obtain a license.
*/

require_once("bug_data.php");
require_once("quarter_operations.php");

function bugs_get_quarter_bugs(&$dbh, &$users, &$products, $product_id)
{
	$quat = CurrentQuarter() - 1;
	$quat_beg;
	$quat_end;
	bugs_get_quarter_begin_end($quat, $quat_beg, $quat_end);
	
	$sql   = "SELECT bugs.*,longdescs.work_time FROM longdescs,bugs where longdescs.bug_when between'".$quat_beg."'and'".$quat_end."' and longdescs.work_time>0 and bugs.product_id='".$product_id."'and bugs.bug_id = longdescs.bug_id";
	$times = $dbh->query($sql);
	$bugs  = array();

	foreach ($times as $row)
	{
		$time   = $row['work_time'];
		$bug_id = $row['bug_id'];
		
		if ( isset($bugs[$bug_id]) )
		{
			$bugs[$bug_id]->m_worked_time += $time;
		}
		else
		{
			$bug = parse_row_to_bug_data($row, $users, $products);
			$bug->m_worked_time += $time;
			$bugs[$bug_id] = $bug;
		}
	}
	
	return $bugs;
}

function quarter_bugs_to_table(&$bugs_array)
{
	$all_time = bugs_get_work_time($bugs_array);
	$all_bugs = count($bugs_array);
	$all_days = hours_to_days($all_time);
	
	bugs_get_quater_split_by_mile($mile_bugs, $bugs_array);
	ksort($mile_bugs);
	
	echo "<table class = 'summary'>\n";
		echo "<thead>\n";
		echo "<tr class='header'>\n";
		/*1*/echo "\t<th width=100> Milestone:        </th>\n";
		/*2*/echo "\t<th width= 50> Bugs              </th>\n";
		/*3*/echo "\t<th width= 45> Worked&nbsp;(h)   </th>\n";
		/*4*/echo "\t<th width= 45> Worked&nbsp;(days)</th>\n";
		/*5*/echo "\t<th width= 45> &nbsp;%           </th>\n";
		echo "</tr>";
		echo "</thead>\n";
		
		echo "<tbody>\n";
		foreach($mile_bugs as $mile_name => $mile )
		{
			$work_time = bugs_get_work_time($mile);
			$bug_cnt   = count($mile);
			$days      = hours_to_days($work_time);
			$perc      = percent($work_time, $all_time);
			
			echo "<tr class = 'summary'>";
				echo "<td>$mile_name </td>";
				echo "<td>$bug_cnt   </td>";
				echo "<td>$work_time </td>";
				echo "<td>$days      </td>";
				echo "<td>$perc      </td>";
			echo "</tr>";
		}
		echo "</tbody>\n";
		echo "<tfoot>\n";
		echo "<tr class = 'summary'>";
			echo "<td>          </td>";
			echo "<td>$all_bugs </td>";
			echo "<td>$all_time </td>";
			echo "<td>$all_days </td>";
			echo "<td>100%      </td>";
		echo "</tr>";
		echo "</tfoot>\n";
	echo "</table>";
	
	foreach($mile_bugs as $mile_name => $mile )
	{
		echo "<h3> $mile_name: </h3>";
		bugs_echo_table($mile, "", "openTable tablesorter");	
	}
}

?>
