<?php

/*
    Copyright by Tomas Rapkauskas bugzilla_base/license.txt 
    All rights reserved.
    To use this component please contact slidertom@gmail.com to obtain a license.
*/

require_once("../bugzilla_base/connect_to_bugzilla_db.php");
require_once "../func/bugs_fnc.php";

ob_start("ob_gzhandler");
 
$json_array = array();

if ( !isset($_GET["bug_id"]) )
{
    echo json_encode($json_array);
    //echo "no bug id defined";
    return;
}

$bug_id = $_GET["bug_id"];

$dbh = connect_to_bugzilla_db();
if ( $dbh == NULL ) 
{
    echo json_encode($json_array);
    //echo "connection to the database failed!";
    return;
}

$users    = get_user_profiles($dbh);
$products = products_get($dbh);
$bug = bug_get_by_bug_id($dbh, $users, $products, $bug_id);
if ( !$bug )
{
    echo json_encode($json_array);
    //echo "bug was not found!";
    return;
}

$bug->m_worked_time = get_bug_work_time($dbh, $bug->m_bug_id);

$json_array['summary']     = $bug->m_summary;
$json_array['reporter']    = $bug->m_reporter->m_login_name;
$json_array['assigned']    = $bug->m_assigned_to->m_login_name;
$json_array['remain_time'] = number_format($bug->get_bug_remaining_time(), 2);
$json_array['worked_time'] = number_format(doubleval($bug->m_worked_time), 2);
$json_array['priority']    = $bug->m_priority;
$json_array['severity']    = $bug->m_severity;
$json_array['complete']    = $bug->get_complete();

echo json_encode($json_array);