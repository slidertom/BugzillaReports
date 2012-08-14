<?php
/*
	Copyright by Tomas Rapkauskas bugzilla_base/license.txt 
	All rights reserved.
	To use this component please contact slidertom@gmail.com to obtain a license.
*/

require_once("../_Bugzilla/bug_data.php");
require_once("../_Bugzilla/quarter_operations.php");
require_once("../bugzilla_base/bugs_sql.php");

function bugs_get_developer_quarter_bugs(&$dbh, &$users, &$products, $developer_id)
{
	$quat = CurrentQuarter() - 1;
	$quat_beg;
	$quat_end;
	bugs_get_quarter_begin_end($quat, $quat_beg, $quat_end);

	$bugs  = get_worked_developer_bugs_by_dates($dbh, $developer_id, $quat_beg, $quat_end, $users, $products);
	
	return $bugs;
}

function get_pie_colors_array()
{
	$colors = array("#3366cc", "#990099", "#109618", "#dc3912", 
					"#ff9900", "#958c12", "#953579", "#4b5de4", 
					"#d8b83f", "#ff5800", "#0085cc", "#222222");
	return $colors;
}

function quarter_developer_bugs_to_table(&$bugs_array)
{
	if ( !$bugs_array )
	{
		echo "<h3>There is no bugs fixed.</h3>";
		return;
	}
	
	$colors = get_pie_colors_array();
	$colors_count = count($colors);
	
	$all_time = bugs_get_work_time($bugs_array);
	$all_bugs = count($bugs_array);
	$all_days = hours_to_days($all_time);
	
	bugs_explode_by_product($product_bugs, $bugs_array);
	ksort($product_bugs);
	echo "<table><tr><td>";
	echo "<table id='dev_quater_summary' class = 'summary'>\n";
		echo "<thead>\n";
		echo "<tr class='header'>\n";
		/*1*/echo "\t<th width=130> Product:          </th>\n";
		/*2*/echo "\t<th width= 50> Bugs              </th>\n";
		/*3*/echo "\t<th width= 45> Worked&nbsp;(h)   </th>\n";
		/*4*/echo "\t<th width= 45> Worked&nbsp;(days)</th>\n";
		/*5*/echo "\t<th width= 45> &nbsp;%           </th>\n";
		echo "</tr>";
		echo "</thead>\n";
		
		$pie_data = array();
		$color_index = 0;
		
		echo "<tbody>\n";
		foreach($product_bugs as $product_name => $product )
		{
			$work_time = bugs_get_work_time($product);
			$bug_cnt   = count($product);
			$days      = hours_to_days($work_time);
			$perc      = percent($work_time, $all_time);
			
			$back_color = $colors[$color_index];
			
			echo "<tr class = 'summary'>";
				echo "<td style='color:rgb(255,255,255); background-color:$back_color'>$product_name  </td>";
				echo "<td>$bug_cnt       </td>";
				echo "<td>$work_time     </td>";
				echo "<td>$days          </td>";
				echo "<td>".$perc."%     </td>";
			echo "</tr>";
			$pie_data[$product_name] = intval(round($perc));
			++$color_index;
			$color_index = $color_index >= $colors_count ? 0 : $color_index;
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
		
	echo "</td><td>";
		echo "<div id='bugs_pie_chart'></div>";
	echo "</td></tr></table>";
	
	$json_data = json_encode($pie_data);
	echo "<div id='bugs_pie_data' style='visibility:hidden'>$json_data</div>";
	
	foreach($product_bugs as $product_name => $mile )
	{
		echo "<h3> $product_name: </h3>";
		bugs_echo_table($mile, "", "openTable tablesorter");	
	}
}

function quarter_developer_milestone_bugs_to_table(&$bugs_array)
{
	if ( !$bugs_array )
	{
		echo "<h3>There is no bugs fixed.</h3>";
		return;
	}
	
	$colors = get_pie_colors_array();
	$colors_count = count($colors);
	
	$all_time = bugs_get_work_time($bugs_array);
	$all_bugs = count($bugs_array);
	$all_days = hours_to_days($all_time);
	
	bugs_get_quater_split_by_product_and_mile($product_bugs, $bugs_array);
	ksort($product_bugs);
	
	echo "<table><tr><td>";
	echo "<table class = 'summary'>\n";
		echo "<thead>\n";
		echo "<tr class='header'>\n";
		/*1*/echo "\t<th width=130> Product:          </th>\n";
		/*2*/echo "\t<th width=130> Milestone:        </th>\n";
		/*3*/echo "\t<th width= 50> Bugs              </th>\n";
		/*4*/echo "\t<th width= 45> Worked&nbsp;(h)   </th>\n";
		/*5*/echo "\t<th width= 45> Worked&nbsp;(days)</th>\n";
		/*6*/echo "\t<th width= 45> &nbsp;%           </th>\n";
		echo "</tr>";
		echo "</thead>\n";
		
		$pie_data = array();
		$pie_product_data = array();
		
		echo "<tbody>\n";
		$color_index = 0;
		$color_prod_index = 0;
		
		foreach($product_bugs as $product_name => $product_miles )
		{
			ksort($product_miles);
			$prod_color = $colors[$color_prod_index];
			
			foreach ($product_miles as $mile_name => $bugs )
			{
				$back_color = $colors[$color_index];
				$work_time  = bugs_get_work_time($bugs);
				$bug_cnt    = count($bugs);
				$days       = hours_to_days($work_time);
				$perc       = percent($work_time, $all_time);
				
				echo "<tr class = 'summary'>";
					echo "<td style='color:rgb(255,255,255); background-color:$prod_color'>$product_name  </td>";
					echo "<td style='color:rgb(255,255,255); background-color:$back_color'>$mile_name     </td>";
					echo "<td>$bug_cnt       </td>";
					echo "<td>$work_time     </td>";
					echo "<td>$days          </td>";
					echo "<td>".$perc."%     </td>";
				echo "</tr>";
				
				$perc_round = intval(round($perc));
				//if ( $perc_round > 0 )
				{
					$pie_data[$product_name." ".$mile_name] = $perc_round;
					if ( isset($pie_product_data[$product_name]) )
					{
						$pie_product_data[$product_name] += $perc_round;
					}
					else
					{
						$pie_product_data[$product_name] = $perc_round;
					}
				}
				
				++$color_index;
				$color_index = $color_index >= $colors_count ? 0 : $color_index;
			}
			
			++$color_prod_index;
			$color_prod_index = $color_prod_index >= $colors_count ? 0 : $color_prod_index;
		}
		echo "</tbody>\n";
		echo "<tfoot>\n";
		echo "<tr class = 'summary'>";
			echo "<td>          </td>";
			echo "<td>          </td>";
			echo "<td>$all_bugs </td>";
			echo "<td>$all_time </td>";
			echo "<td>$all_days </td>";
			echo "<td>100%      </td>";
		echo "</tr>";
		echo "</tfoot>\n";
	echo "</table>";
	echo "</td><td>";
		echo "<div id='bugs_pie_chart'></div>";
	echo "</td></tr></table>";
	
	$json_data_mile    = json_encode($pie_data);
	$json_data_product = json_encode($pie_product_data);
	echo "<span id='bugs_pie_data' style='visibility:hidden'>$json_data_product</span>";
	echo "<span id='bugs_pie_mile_data' style='visibility:hidden'>$json_data_mile</span>";
	
	foreach($product_bugs as $product_name => $product_miles )
	{	
		foreach($product_miles as $mile_name => $mile )
		{
			echo "<span id=$product_name$mile_name>"; 
			echo "<h3>$product_name &nbsp; $mile_name: </h3>";
			bugs_echo_table($mile, "", "openTable tablesorter");	
			echo "</span>";
		}
	}
}

?>