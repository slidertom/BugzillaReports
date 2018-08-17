<?php
require_once '../_Bugzilla/bug_data.php';
require_once '../bugzilla_base/bugs_sql.php';

function get_pie_colors_array()
{
	$colors = array("#3366cc", "#990099", "#109618", "#dc3912", 
					"#ff9900", "#958c12", "#953579", "#4b5de4", 
					"#d8b83f", "#ff5800", "#0085cc", "#222222");
	return $colors;
}

function developer_bugs_by_product_summary_table(&$bugs_array, &$product_bugs)
{
    $colors = get_pie_colors_array();
	$colors_count = count($colors);
	
	$all_time = bugs_get_work_time($bugs_array);
	$all_bugs = count($bugs_array);
	$all_days = hours_to_days($all_time);
	
    $pie_data = array();
    $color_index = 0;
		
	echo "<table><tr><td>";
	echo "<table id='dev_quater_summary' class = 'summary'>\n";
		echo "<thead>\n";
		echo "<tr class='header'>\n";
		/*1*/echo "\t<th width=130> Product           </th>\n";
		/*2*/echo "\t<th width= 50> Bugs              </th>\n";
		/*3*/echo "\t<th width= 45> Worked&nbsp;(h)   </th>\n";
		/*4*/echo "\t<th width= 45> Worked&nbsp;(days)</th>\n";
		/*5*/echo "\t<th width= 45> &nbsp;%           </th>\n";
		echo "</tr>";
		echo "</thead>\n";
		
		echo "<tbody>\n";
		foreach ($product_bugs as $product_name => $product)
		{
			$work_time = bugs_get_work_time($product);
			$bug_cnt   = count($product);
			$days      = hours_to_days($work_time);
			$perc      = percent($work_time, $all_time);
			
			$back_color = $colors[$color_index];
			$work_time  = round($work_time, 3);
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
}

?>