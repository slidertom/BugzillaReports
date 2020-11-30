<?php
function cmp_bug_priority($bug1, $bug2)
{
    if (strcmp($bug1->m_priority, $bug2->m_priority) == 0) {
        return 0; // TODO compare by severity
    }

    $p1 = intval(ltrim($bug1->m_priority, 'P'));
    $p2 = intval(ltrim($bug2->m_priority, 'P'));
    return ($p1 < $p2) ? -1 : 1;
}

function filter_bugs_till_remain_40h($bugs)
{
    usort($bugs, "cmp_bug_priority");
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
                return $bugs_ret;    
            }
        }
    }
    //var_dump($all_time);
    return $bugs_ret;
}


?>