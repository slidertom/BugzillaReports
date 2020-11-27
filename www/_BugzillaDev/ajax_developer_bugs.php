<?php
/*
    Copyright by Tomas Rapkauskas bugzilla_base/license.txt 
    All rights reserved.
    To use this component please contact slidertom@gmail.com to obtain a license.
*/

ob_start('ob_gzhandler');

require_once '../_Bugzilla/bugs_fnc.php';
require_once '../_Bugzilla/bugs_start_end_dates.php';
require_once '../bugzilla_base/connect_to_bugzilla_db.php';
require_once '../func/bugs_operations.php';
require_once 'developer_milestone_table.php';
require_once 'developer_filters_class.php';
require_once 'developer_bugs_year.php';
require_once 'developer_bugs_by_month_mile.php';
require_once 'developer_weekly_progress.php';

$product_filter;
function filter_by_product($bug)
{
    global $product_filter;
    return $bug->m_product->m_id == $product_filter;
}

function bugs_by_developer_echo_table(&$dbh, $developer_id, $filter)
{
    $users        = get_user_profiles($dbh); // <userid><login_name>
    $products     = products_get($dbh);
    $bugs;
    
    if ( $filter == DeveloperFilters::Assigned ) {
        $bugs = bugs_get_assigned_by_developer($dbh, $users, $products, $developer_id);
    }
    else if ( $filter == DeveloperFilters::WeeklyProgress ) {
        $year  = isset($_GET['year']) ? ($_GET['year'] != 'current' ? intval($_GET['year']) : DateTimeUtil::get_current_year()) : DateTimeUtil::get_current_year();
        $week  = isset($_GET['week']) ? ($_GET['week'] != 'current' ? intval($_GET['week']) : DateTimeUtil::get_current_week()) : DateTimeUtil::get_current_week();
        developer_weekly_progress($dbh, $users, $products, $developer_id, $year, $week);
        return;
    }
    else if ( $filter == DeveloperFilters::ThisYear ) {
        $year = isset($_GET['year']) ? $_GET['year'] : DateTimeUtil::get_current_year();
        developer_bugs_year_by_product($dbh, $users, $products, $developer_id, $year);
        return;
    }
    else if ( $filter == DeveloperFilters::ThisMonth ) {
        $year  = isset($_GET['year'])  ? $_GET['year']  : DateTimeUtil::get_current_year();
        $month = DateTimeUtil::current_month();
        $bugs = bugs_get_developer_month_bugs($dbh, $users, $products, $developer_id, $year, $month);
        developer_bugs_to_table_by_product($bugs);
        return;
    }
    else if ( $filter == DeveloperFilters::PrevMonth ) {
        $year  = isset($_GET['year'])  ? $_GET['year']  : DateTimeUtil::get_current_year();
        $month = DateTimeUtil::current_month() - 1;
        $bugs = bugs_get_developer_month_bugs($dbh, $users, $products, $developer_id, $year, $month);
        developer_bugs_to_table_by_product($bugs);
        return;
    }
    else if ( $filter == DeveloperFilters::MonthMile ) {
        $year  = isset($_GET['year'])  ? $_GET['year']  : DateTimeUtil::get_current_year();
        $month = isset($_GET['month']) ? $_GET['month'] : DateTimeUtil::current_month();
        developer_bugs_by_moth_by_mile($dbh, $users, $products, $developer_id, $year, $month);
        return;
    }
    else if ( $filter == DeveloperFilters::PrevQuaterProd ) {
        echo "<br>";
        $bugs = prev_bugs_get_developer_quarter_bugs($dbh, $users, $products, $developer_id);
        developer_bugs_to_table_by_product($bugs);
        return;
    }
    else if ( $filter == DeveloperFilters::PrevQuaterMile ) {
        echo "<br>";
        $bugs = prev_bugs_get_developer_quarter_bugs($dbh, $users, $products, $developer_id);
        developer_milestone_bugs_to_table($bugs);
        return;
    }
    else if ( $filter == DeveloperFilters::ThisQuaterProd ) {
        echo "<br>";
        $bugs = this_bugs_get_developer_quarter_bugs($dbh, $users, $products, $developer_id);
        developer_bugs_to_table_by_product($bugs);
        return;
    }
    else if ( $filter == DeveloperFilters::ThisQuaterMile ) {
        echo "<br>";
        $bugs = this_bugs_get_developer_quarter_bugs($dbh, $users, $products, $developer_id);
        developer_milestone_bugs_to_table($bugs);
        return;
    }
    else if ( $filter == DeveloperFilters::Open ) {
        $bugs = bugs_get_by_developer($dbh, $users, $products, $developer_id);
    }
    else if ( strlen($filter) > 0 ) {
        $bugs = bugs_get_by_developer($dbh, $users, $products, $developer_id);
        global $product_filter;
        $product_filter = $filter;
        $bugs = array_filter($bugs, "filter_by_product"); 
    }
    else {
        $bugs = bugs_get_by_developer($dbh, $users, $products, $developer_id);
    }
             
    if ( !$bugs || count($bugs) == 0 )
    {
        echo "<h3>There is no bugs fixed.</h3>";
        return;
    }
    
    bugs_update_worked_time($dbh, $bugs);
    bugs_init_start_end_dates($bugs);
    
    $cnt          = count($bugs);
    $work_time    = get_bugs_work_time($bugs);
    
    echo "<br>\n";
    echo "<p><span>Opened bugs count: $cnt</span><span>&nbsp;&nbsp;&nbsp;&nbsp;Remaining time: $work_time&nbsp;h</span></p>";
    
    bugs_echo_table($bugs, " ", "openTable tablesorter");
}

if ( !isset($_GET['Developer']) ) {
    return;
}

$developer_id = $_GET['Developer'];
$filter       = isset($_GET['Filter']) ? $_GET['Filter'] : "open_bugs";

$dbh = connect_to_bugzilla_db();
if ( $dbh == NULL ) {
    return;
}	

bugs_by_developer_echo_table($dbh, $developer_id, $filter);
?>