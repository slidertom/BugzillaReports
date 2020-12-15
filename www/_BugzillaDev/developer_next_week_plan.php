<?php
require_once 'developer_milestone_table.php';
require_once (__DIR__).'/../_Bugzilla/bugs_start_end_dates.php';

function cmp_bug_priority_severity($bug1, $bug2)
{
    if (strcmp($bug1->m_priority, $bug2->m_priority) == 0) {
        return strcmp($bug1->m_severity, $bug2->m_severity);
    }

    $p1 = intval(ltrim($bug1->m_priority, 'P'));
    $p2 = intval(ltrim($bug2->m_priority, 'P'));
    return ($p1 < $p2) ? -1 : 1;
}

function filter_bugs_till_remain_40h($bugs)
{
    usort($bugs, "cmp_bug_priority_severity");
    $all_time = 0;
    $bugs_ret = [];
    $bugs_review = [];
    foreach($bugs as $key => $bug) 
    {
        //var_dump($bug);
        //var_dump($bug->m_priority);
        if ( $bug->InProgress() ) {
            $all_time += $bug->get_bug_remaining_time();
            $bugs_ret[$key] = $bug;
            continue;
        }
        
        $bugs_review[$key] = $bug;
    }
    //var_dump($all_time);
    //return $bugs_ret;
    if ( $all_time < 40 ) {
        foreach($bugs_review as $key => $bug) 
        {
            $all_time += $bug->get_bug_remaining_time();
            $bugs_ret[$key] = $bug;
            if ($all_time > 40) {
                //var_dump($all_time);
                usort($bugs_ret, "cmp_bug_priority_severity");
                return $bugs_ret;    
            }
        }
    }
    //var_dump($all_time);
    usort($bugs_ret, "cmp_bug_priority_severity");
    return $bugs_ret;
}

function echo_developer_next_week_plan($dbh, $users, $products, $developer_id, $create_link)
{
    $date = date("Y-m-d");
    
    $week = DateTimeUtil::get_current_week();
    $week_start = $date;
    $week_end   = date('Y-m-d', strtotime(' +7 day'));

    echo "<br>Next Week Plan: [$week_start - $week_end]";
    if ( $create_link ) {
        $dir = (__DIR__)."/../weekly_plans";
        $dir = realpath($dir);
        $login_name = $users[$developer_id]->m_login_name;
        $developer_path = $dir."/".$login_name;
        $year = DateTimeUtil::get_current_year();
        $files = glob($developer_path."/".$year."-".$week."*.pdf");
        //var_dump($files);
        $fileList = [];
        foreach ($files as $file) {
            $fileList[filemtime($file)] = $file;
        }
        ksort($fileList);
        foreach ($fileList as $file) {
            $basename = basename($file);
            echo "<span>&nbsp&nbsp&nbsp&nbspGenerated document:&nbsp</span>";
            $show_name = str_replace("__", ":", $basename);
            echo "<a href='/weekly_plans/$login_name/$basename'>$show_name</a><br>";
            break;
        }
    }
    echo "<br>";
    $bugs = bugs_get_by_developer($dbh, $users, $products, $developer_id);
    $bugs = array_filter($bugs, "is_non_web_kozinjn_bug");
    bugs_update_worked_time($dbh, $bugs);
    bugs_init_start_end_dates($bugs);
    $bugs = filter_bugs_till_remain_40h($bugs);
    $work_time_left = get_bugs_work_time($bugs);
    echo "<br><div><b>Remaining time:</b> $work_time_left h</div>";
    developer_milestone_bugs_to_table($bugs);
}

?>