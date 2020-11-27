<?php
function get_years()
{
    $this_year = date("Y");
    $years = array();
    for ($i1 = 1998; $i1 <= $this_year; ++$i1) {
        $years[] = $i1;
    }
    return $years;
}

function create_years_select($id, $years, $year_to_select)
{
    $selected = intval($year_to_select);
    echo "<select id='$id'>";
    foreach ($years as $year) {
        if ( $selected == intval($year) ) {
            echo "<option value='$year' selected>$year</option>";
        }
        else {
            echo "<option value='$year'>$year</option>";
        }
    }
    echo "</select>";
}

function create_years_select_impl($year_to_select)
{
    create_years_select("year_select", get_years(), $year_to_select);
}

function create_month_select_impl($month_to_select)
{
    $months = array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12);
    $selected = intval($month_to_select);
    echo "<select id='month_select'>";
    foreach ($months as $month) {
        if ( $selected == intval($month) ) {
            echo "<option value='$month' selected>$month</option>";
        }
        else {
            echo "<option value='$month'>$month</option>";
        }
    }
    echo "</select>";
}

function create_year_month_select_table($year, $month)
{
    echo "<table><tr>";
        echo "<td><b>Year: </b></td>";
        echo "<td>";create_years_select_impl($year);echo "</td>";
        echo "<td><b>Month: </b></td>";
        echo "<td>";create_month_select_impl($month);echo "</td>";
    echo "</tr></table>";
}

function create_week_select_impl($year, $week_to_select)
{
    $week_count = DateTimeUtil::get_week_count_in_year($year);
    
    $selected = intval($week_to_select);
    echo "<select id='week_select'>";
    for ($i1 = 0; $i1 < $week_count; ++$i1) 
    {
        $week = $i1 + 1;

        $week_start;
        $week_end;
        DateTimeUtil::get_week_begin_end($year, $week, $week_start, $week_end);

        if ( $selected == intval($week) ) {
            echo "<option value='$week' selected>$week [$week_start - $week_end]</option>";
        }
        else {
            echo "<option value='$week'>$week [$week_start - $week_end]</option>";
        }
    }
    echo "</select>";
}

function create_year_week_select_table($year, $week)
{
    echo "<table><tr>";
        echo "<td><b>Year: </b></td>";
        echo "<td>";create_years_select_impl($year);echo "</td>";
        echo "<td><b>Week: </b></td>";
        echo "<td>";create_week_select_impl($year, $week);echo "</td>";
    echo "</tr></table>";
}

?>