<?php

require_once (__DIR__).'/../func/bugs_operations.php';

function print_bug_numbers_with_links($bugs)
{
    $ret = "";
    foreach ($bugs as $bug_id_key => $bug ) {
        //$bug_id = isset($bug['m_bug_id']) ? $bug['m_bug_id']: $bug_id_key;
        $bug_id = $bug_id_key; //isset($bug['m_bug_id']) ? $bug['m_bug_id']: $bug_id_key;
        $ret = $ret."<span>".generate_bug_link_href($bug_id)."&nbsp;</span>";
    }
    return $ret;
}

function developer_weekly_progress($dbh, $users, $products, $developer_id, $year, $week)
{
    echo "<br>";
    create_year_week_select_table($year, $week);

    
    $week_start;
    $week_end;
    DateTimeUtil::get_week_begin_end($year, $week, $week_start, $week_end);

    $bugs = get_worked_developer_bugs_by_dates($dbh, $developer_id, $week_start, $week_end, $users, $products);
    if ( $bugs ) {
        developer_milestone_bugs_to_table($bugs);    
    }
 
    $bugs_fixed      = get_changed_developer_bugs_by_dates($dbh, $developer_id, $week_start, $week_end, $users, $products, "FIXED");
    $bugs_reopen     = get_changed_developer_bugs_by_dates($dbh, $developer_id, $week_start, $week_end, $users, $products, "REOPENED");
    $bugs_verified   = get_changed_developer_bugs_by_dates($dbh, $developer_id, $week_start, $week_end, $users, $products, "VERIFIED");
    $bugs_created    = get_managed_developer_bugs_by_dates($dbh, $developer_id, $week_start, $week_end, $users, $products, 19);
    $bugs_estimated  = get_managed_developer_bugs_by_dates($dbh, $developer_id, $week_start, $week_end, $users, $products, 50);

    echo "<h3>Development</h3>";
    
    echo "<table class='tablesorter'>";
        echo "<tr>";
            echo "<td>Worked on bugs by Developer:</td><td>".count($bugs)."</td>";
            echo "<td>".print_bug_numbers_with_links($bugs)."</td>";
        echo "</tr>";
        echo "<tr>";
            echo "<td>Fixed status applied by Developer:</td><td>".count($bugs_fixed)."</td>";
            echo "<td>".print_bug_numbers_with_links($bugs_fixed)."</td>";
        echo "</tr>";
        echo "<tr>";
            echo "<td>Estimated bugs by Developer:</td><td>".count($bugs_estimated)."</td>";
            echo "<td>".print_bug_numbers_with_links($bugs_estimated)."</td>";
        echo "</tr>";
        echo "<tr>";
            echo "<td>Created bugs by Developer</td><td>".count($bugs_created)."</td>";
            echo "<td>".print_bug_numbers_with_links($bugs_created)."</td>";
        echo "</tr>";
        echo "<tr>";
            echo "<td>Reopened status applied by Developer</td><td>".count($bugs_reopen)."</td>";
            echo "<td>".print_bug_numbers_with_links($bugs_reopen)."</td>";
        echo "</tr>";
        echo "<tr>";
            echo "<td>Verified status applied by Developer</td><td>".count($bugs_verified)."</td>";
            echo "<td>".print_bug_numbers_with_links($bugs_verified)."</td>";
        echo "</tr>";
    echo "</table>";    

    $bugs_duplicate       = get_changed_developer_bugs_by_dates($dbh, $developer_id, $week_start, $week_end, $users, $products, "DUPLICATE");
    $bugs_invalid         = get_changed_developer_bugs_by_dates($dbh, $developer_id, $week_start, $week_end, $users, $products, "INVALID");
    $bugs_reassigned      = get_managed_developer_bugs_by_dates($dbh, $developer_id, $week_start, $week_end, $users, $products, 15);
    $bugs_prioritized     = get_managed_developer_bugs_by_dates($dbh, $developer_id, $week_start, $week_end, $users, $products, 13);
    $bugs_keyworded       = get_managed_developer_bugs_by_dates($dbh, $developer_id, $week_start, $week_end, $users, $products, 10);
    $bugs_summarry        = get_managed_developer_bugs_by_dates($dbh, $developer_id, $week_start, $week_end, $users, $products, 2);
    $bugs_milestone       = get_managed_developer_bugs_by_dates($dbh, $developer_id, $week_start, $week_end, $users, $products, 26);
    $bugs_product_change  = get_managed_developer_bugs_by_dates($dbh, $developer_id, $week_start, $week_end, $users, $products, 3);
    $bugs_severity_change = get_managed_developer_bugs_by_dates($dbh, $developer_id, $week_start, $week_end, $users, $products, 12);

    $bugs_dependency_change = get_managed_developer_bugs_by_dates($dbh, $developer_id, $week_start, $week_end, $users, $products, 20);
    $bugs_dependency_change += get_managed_developer_bugs_by_dates($dbh, $developer_id, $week_start, $week_end, $users, $products, 21);

    echo "<h3>Management</h3>";
    echo "<table class='tablesorter'>";
        echo "<tr>";
            echo "<td>Priority changed by Developer</td><td>".count($bugs_prioritized)."</td>";
            echo "<td>".print_bug_numbers_with_links($bugs_prioritized)."</td>";
        echo "</tr>";
        echo "<tr>";
            echo "<td>Severity changed by Developer</td><td>".count($bugs_severity_change)."</td>";
            echo "<td>".print_bug_numbers_with_links($bugs_severity_change)."</td>";
        echo "</tr>";
        echo "<tr>";
            echo "<td>Reassigned done by Developer</td><td>".count($bugs_reassigned)."</td>";
            echo "<td>".print_bug_numbers_with_links($bugs_reassigned)."</td>";
        echo "</tr>";
        echo "<tr>";
            echo "<td>Milestone changed by Developer</td><td>".count($bugs_milestone)."</td>";
            echo "<td>".print_bug_numbers_with_links($bugs_milestone)."</td>";
        echo "</tr>";
        echo "<tr>";
            echo "<td>Product changed by Developer</td><td>".count($bugs_product_change)."</td>";
            echo "<td>".print_bug_numbers_with_links($bugs_product_change)."</td>";
        echo "</tr>";
        echo "<tr>";
            echo "<td>Duplicate bugs found by Developer</td><td>".count($bugs_duplicate)."</td>";
            echo "<td>".print_bug_numbers_with_links($bugs_duplicate)."</td>";
        echo "</tr>";
        echo "<tr>";
            echo "<td>Invalid bugs found by Developer</td><td>".count($bugs_invalid)."</td>";
            echo "<td>".print_bug_numbers_with_links($bugs_invalid)."</td>";
        echo "</tr>";
        echo "<tr>";
            echo "<td>Keyword applied by Developer</td><td>".count($bugs_keyworded)."</td>";
            echo "<td>".print_bug_numbers_with_links($bugs_keyworded)."</td>";
        echo "</tr>";
        echo "<tr>";
            echo "<td>Summary updated by Developer</td><td>".count($bugs_summarry)."</td>";
            echo "<td>".print_bug_numbers_with_links($bugs_summarry)."</td>";
        echo "</tr>";
        echo "<tr>";
            echo "<td>Dependable bugs change by Developer</td><td>".count($bugs_dependency_change)."</td>";
            echo "<td>".print_bug_numbers_with_links($bugs_dependency_change)."</td>";
        echo "</tr>";
        
    echo "</table>";
}

?>