<?php
require_once "../bugzilla_base/connect_to_bugzilla_db.php";
require_once (__DIR__)."/../_Bugzilla/profiles.php";
require_once (__DIR__)."/../tools/date_time_util.php";

$dbh = connect_to_bugzilla_db();        
if ($dbh == NULL) {
    return;
}

$dir = (__DIR__)."/../weekly_plans";
if ( !file_exists($dir) ) {
    mkdir($dir);
}

$dir = realpath($dir);
$index_dir = $dir."/../tools/folder_to_html.php";
$index_dir = realpath($index_dir);
copy($index_dir, $dir."/index.php");

$converter_path = (__DIR__)."/../tools/wkhtmltox/bin/wkhtmltopdf.exe";
$converter_path = realpath($converter_path);
if ( !file_exists($converter_path) ) {
    echo "$converter_path was not found!";
    return;
}

$week = DateTimeUtil::get_current_week();
$year = DateTimeUtil::get_current_year();

$week_start = date('Y-m-d');
$week_end   = date('Y-m-d', strtotime(' +7 day'));

//var_dump($_SERVER);
$call_path = $_SERVER['REQUEST_SCHEME']."://".$_SERVER['HTTP_HOST']."/_BugzillaDev/index_developer_next_week_plan.php";
//var_dump($call_path);
$users  = get_user_profiles($dbh); // <userid><login_name>
foreach($users as $id => $user) {
    if ($user->m_real_name == "") {
        continue;
    }
  
    if ($user->m_disabled_text != "") {
        continue;
    }
    
    $developer_call = $call_path."?developer=$id&filter=next_week_plan";
    //var_dump($developer_call);
    $developer_path = $dir."/".$user->m_login_name;
    if (!file_exists($developer_path)) {
        //var_dump($developer_path);
        mkdir($developer_path);
    }

    copy($index_dir, $developer_path."/index.php");

    $now_time = date("H__i");
    var_dump($now_time);
    $week_start_end_format = "[".$week_start."-".$week_end."][".$now_time."]";
    $report_pdf = $developer_path."/".$year."-".$week."_".$week_start_end_format.".pdf";
    //var_dump($id);
    $cmd = $converter_path." --javascript-delay 2000 -O landscape "."\"".$developer_call."\""." "."\"".$report_pdf."\"";
    $re = shell_exec("$cmd 2>&1");
    echo "<br><div>$cmd</div>";
    //http://localhost:89/_BugzillaDev/index.php?developer=19&filter=next_week_plan
}

?>