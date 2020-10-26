<?php

require_once (__DIR__).'/../func/bugs_operations.php';

function developer_weekly_progress($dbh, $users, $products, $developer_id)
{
    $curren_week = DateTimeUtil::get_current_week();
    $curren_year = DateTimeUtil::get_current_year();
    $week_start;
    $week_end;
    DateTimeUtil::get_week_begin_end($curren_year, $curren_week, $week_start, $week_end);

    echo "<table>";
        echo "<tr>";
            echo "<td>Week Start Date:</td><td>$week_start</td>";
        echo "</tr>";
        echo "<tr>";
            echo "<td>Week Start Date:</td><td>$week_end</td>";
        echo "</tr>";
    echo "</table>";


    $bugs = get_worked_developer_bugs_by_dates($dbh, $developer_id, $week_start, $week_end, $users, $products);
    if ( !$bugs ) {
        echo "<h3>There are no bugs fixed.</h3>";
        return;
    }
 
    developer_milestone_bugs_to_table($bugs);
}

?>