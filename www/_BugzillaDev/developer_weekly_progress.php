<?php

require_once (__DIR__).'/../func/bugs_operations.php';

function echo_bug_numbers_with_links($bugs)
{
    foreach ($bugs as $bug ) {
        echo "<span>".$bug->m_bug_id."&nbsp;</span>";
    }
}

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
    if ( $bugs ) {
        developer_milestone_bugs_to_table($bugs);    
    }
 
    $bugs_fixed      = get_changed_developer_bugs_by_dates($dbh, $developer_id, $week_start, $week_end, $users, $products, "FIXED");
    $bugs_reopen     = get_changed_developer_bugs_by_dates($dbh, $developer_id, $week_start, $week_end, $users, $products, "REOPENED");
    $bugs_verified   = get_changed_developer_bugs_by_dates($dbh, $developer_id, $week_start, $week_end, $users, $products, "VERIFIED");
    $bugs_created    = get_managed_developer_bugs_by_dates($dbh, $developer_id, $week_start, $week_end, $users, $products, 19);

    echo "<h3>Development</h3>";
    
    echo "<table>";
        echo "<tr>";
            echo "<td>Worked on bugs by Developer:</td><td>$worked_bugs_cnt</td>";
            echo "<td>";
                $worked_bugs_cnt = count($bugs);
                echo_bug_numbers_with_links($bugs);
            echo "</td>";
        echo "</tr>";
        echo "<tr>";
            $fixed_bugs_cnt = count($bugs_fixed);
            echo "<td>Fixed status applied by Developer:</td><td>$fixed_bugs_cnt</td>";
            echo "<td>";
                echo_bug_numbers_with_links($bugs_fixed);
            echo "</td>";
        echo "</tr>";
        echo "<tr>";
            echo "<td>Created bugs by Developer</td><td>$created_bugs_cnt</td>";
            echo "<td>";
                $created_bugs_cnt = count($bugs_created);
                echo_bug_numbers_with_links($bugs_created);
            echo "</td>";
        echo "</tr>";
        echo "<tr>";
            $reopen_bugs_cnt = count($bugs_reopen);
            echo "<td>Reopened status applied by Developer</td><td>$reopen_bugs_cnt</td>";
            echo "<td>";
                echo_bug_numbers_with_links($bugs_reopen);
            echo "</td>";
        echo "</tr>";
        echo "<tr>";
            $verify_bugs_cnt = count($bugs_verified);
            echo "<td>Verified status applied by Developer</td><td>$verify_bugs_cnt</td>";
            echo "<td>";
                echo_bug_numbers_with_links($bugs_verified);
            echo "</td>";
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

    $prioritized_cnt     = count($bugs_prioritized);
    $duplicate_bugs_cnt  = count($bugs_duplicate);
    $invalid_bugs_cnt    = count($bugs_invalid);
    $reassigned_bugs_cnt = count($bugs_reassigned);
    $keyworded_bugs_cnt  = count($bugs_keyworded);
    $sumarry_bugs_cnt    = count($bugs_summarry);
    $milestone_cnt       = count($bugs_milestone);
    $product_cnt         = count($bugs_product_change);
    $severity_cnt        = count($bugs_severity_change);

    echo "<h3>Management</h3>";
    echo "<table>";
        echo "<tr>";
            echo "<td>Priority changed by Developer</td><td>$prioritized_cnt</td>";
        echo "</tr>";
        echo "<tr>";
            echo "<td>Severity changed by Developer</td><td>$severity_cnt</td>";
        echo "</tr>";
        echo "<tr>";
            echo "<td>Reassigned done by Developer</td><td>$reassigned_bugs_cnt</td>";
        echo "</tr>";
        echo "<tr>";
            echo "<td>Milestone changed by Developer</td><td>$milestone_cnt</td>";
        echo "</tr>";
        echo "<tr>";
            echo "<td>Product changed by Developer</td><td>$product_cnt</td>";
        echo "</tr>";
        echo "<tr>";
            echo "<td>Duplicate bugs found by Developer</td><td>$duplicate_bugs_cnt</td>";
        echo "</tr>";
        echo "<tr>";
            echo "<td>Invalid bugs found by Developer</td><td>$invalid_bugs_cnt</td>";
        echo "</tr>";
        echo "<tr>";
            echo "<td>Keyword applied by Developer</td><td>$keyworded_bugs_cnt</td>";
        echo "</tr>";
        echo "<tr>";
            echo "<td>Summary updated by Developer</td><td>$sumarry_bugs_cnt</td>";
        echo "</tr>";
        
    echo "</table>";
}

?>