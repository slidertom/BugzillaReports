<?php
require_once 'developer_bugs_by_product_summary_table.php';
require_once 'developer_milestone_table.php';
require_once '../func/bugs_operations.php';
require_once '../tools/date_time_util.php';
require_once '../tools/date_time_select.php';

function bugs_get_developer_year_bugs(&$dbh, &$users, &$products, $developer_id, $year)
{
    $year_beg; $year_end;
    get_year_begin_end($year, $year_beg, $year_end);
    $bugs = get_worked_developer_bugs_by_dates($dbh, $developer_id, $year_beg, $year_end, $users, $products);
    return $bugs;
}

function bugs_get_developer_year_month_bugs(&$dbh, &$users, &$products, $developer_id, $year, $month)
{
    $month_beg; $month_end;
    get_month_begin_end($year, $month, $month_beg, $month_end);
    //var_dump($month_beg);
    //var_dump($month_end);
    //echo "<br>";
    $bugs = get_worked_developer_bugs_by_dates($dbh, $developer_id, $month_beg, $month_end, $users, $products);
    return $bugs;
}

function developer_bugs_by_period_summary_table($bugs_by_month, $period_name)
{
    $colors = get_pie_colors_array();
	$colors_count = count($colors);
    
    $pie_data = array();
    $color_index = 0;
    
    $all_time = 0;
    foreach ($bugs_by_month as $month => $bugs) {
        $work_time = bugs_get_work_time($bugs);
        $all_time += $work_time;
    }
    $all_days = hours_to_days($all_time);
     
	$bugs_my_month_by_mile = array();
	foreach ($bugs_by_month as $month => $bugs) {
        $bugs_my_month_by_mile[$month] = bugs_split_by_milestone($bugs);
    }
	/*
	$mile_keys = array();
	foreach ($bugs_my_month_by_mile as $month => $by_mile) {
		foreach ($by_mile as $mile => $mile_bugs) {
			if ( !isset($mile_keys[$mile]) ) {
				$mile_keys[] = $mile;
			}
		}
	}
	*/
    echo "<table class = 'summary'>\n";
        echo "<thead>\n";
		echo "<tr class='header'>\n";
		/*1*/echo "\t<th width=130> $period_name      </th>\n";
		/*2*/echo "\t<th width= 50> Bugs              </th>\n";
		/*3*/echo "\t<th width= 45> Worked&nbsp;(h)   </th>\n";
		/*4*/echo "\t<th width= 45> Worked&nbsp;(days)</th>\n";
		/*5*/echo "\t<th width= 45> &nbsp; %          </th>\n";
		echo "</tr>";
		echo "</thead>\n";
        //m_target_milestone
        echo "<tbody>\n";
		foreach ($bugs_by_month as $month => $bugs)    
        {
            $work_time = bugs_get_work_time($bugs);
			$bug_cnt   = count($bugs);
			$days      = hours_to_days($work_time);
			$perc      = percent($work_time, $all_time);
           
            $back_color = $colors[$color_index];
			$work_time  = round($work_time, 3);
			
            echo "<tr class = 'summary'>";
				echo "<td style='color:rgb(255,255,255); background-color:$back_color'>$month</td>";
				echo "<td>$bug_cnt       </td>";
				echo "<td>$work_time     </td>";
				echo "<td>$days          </td>";
				echo "<td>".$perc."%     </td>";
			echo "</tr>";
            $pie_data[$month] = intval(round($perc));
			++$color_index;
			$color_index = $color_index >= $colors_count ? 0 : $color_index;
        }
        echo "</tbody>\n";
		echo "<tfoot>\n";
		echo "<tr class = 'summary'>";
			echo "<td>Summary   </td>";
			echo "<td>          </td>"; // same bug can be included into the multiple months
			echo "<td>$all_time </td>";
			echo "<td>$all_days </td>";
			echo "<td>100%      </td>";
		echo "</tr>";
		echo "</tfoot>\n";
	echo "</table>";
}

function developer_bugs_by_assignee_summary_table(&$bugs, &$users)
{
    echo "<h3>Work Load by Assignee</h3>";
    
    bugs_explode_by_product_developer_id($bugs_by_assignee, $bugs);
    $all_time = 0;
    foreach ($bugs_by_assignee as $developer_id => $bugs) {
        $work_time = bugs_get_work_time($bugs);
        $all_time += $work_time;
    }
    $all_days = hours_to_days($all_time);
    
    echo "<table class = 'summary'>";
        echo "<thead>\n";
		echo "<tr class='header'>\n";
		/*1*/echo "\t<th width=200> Original Assignee </th>\n";
		/*3*/echo "\t<th width= 45> Worked&nbsp;(h)   </th>\n";
		/*4*/echo "\t<th width= 45> Worked&nbsp;(days)</th>\n";
		/*5*/echo "\t<th width= 45> &nbsp;%           </th>\n";
		echo "</tr>";
		echo "</thead>\n";
    echo "<tbody>\n";
    foreach ($bugs_by_assignee as $developer_id => $developer_bugs) {
        $assignee   = $users[$developer_id]->m_real_name;
        $work_time  = bugs_get_work_time($developer_bugs);
        $days       = hours_to_days($work_time);
        $perc       = percent($work_time, $all_time);
        $work_time  = round($work_time, 3);
        echo "<tr>";
        echo "<td>$assignee</td>";
        echo "<td>$work_time</td>";
        echo "<td>$days</td>";
        echo "<td>".$perc."%     </td>";
        echo "</tr>";
    }
    echo "</tbody>\n";
    echo "<tfoot>\n";
    echo "<tr class = 'summary'>";
        echo "<td>Summary   </td>";
        echo "<td>$all_time </td>";
        echo "<td>$all_days </td>";
        echo "<td>100%      </td>";
    echo "</tr>";
    echo "</tfoot>\n";
    echo "</table>";
}

function developer_bugs_year_by_product($dbh, $users, $products, $developer_id, $year)
{
	echo "<br>";
	
	echo "<b>Year: </b>";
	create_years_select_impl($year);
	
    $bugs = bugs_get_developer_year_bugs($dbh, $users, $products, $developer_id, $year);
    if ( !$bugs ) {
		echo "<h3>There are no bugs fixed.</h3>";
		return;
	}
	
    bugs_explode_by_product($product_bugs, $bugs);
	ksort($product_bugs);
    
	developer_bugs_by_product_summary_table($bugs, $product_bugs);
    {
        $bugs_by_quater = array();
        for ($quat = 1;  $quat != 5; ++$quat) {
            $quater_bugs = bugs_get_developer_quarter_bugs($dbh, $users, $products, $developer_id, $year, $quat);
            $bugs_by_quater[$quat] = $quater_bugs;
        }
        developer_bugs_by_period_summary_table($bugs_by_quater, "Quater");
    }
    {
        $bugs_by_month = array();
        for ($month = 1;  $month != 13; ++$month) {
            $month_bugs = bugs_get_developer_year_month_bugs($dbh, $users, $products, $developer_id, $year, $month);
            $bugs_by_month[$month] = $month_bugs;
        }
        developer_bugs_by_period_summary_table($bugs_by_month, "Month");
    }
    
    developer_bugs_by_assignee_summary_table($bugs, $users);
    
	developer_bugs_by_products_tables($product_bugs);
}

?>