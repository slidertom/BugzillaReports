<?php
/*
    Copyright by Tomas Rapkauskas bugzilla_base/license.txt 
    All rights reserved.
    To use this component please contact slidertom@gmail.com to obtain a license.
*/

if ( !ob_start("ob_gzhandler") ) {
    echo "Client does not support gzip!";
}

require_once "../bugzilla_base/connect_to_bugzilla_db.php";
require_once "../func/bugs_fnc.php";

if ( !isset($_GET['Keyword']) ) {
    return;
}

$keyword_id = $_GET['Keyword'];

$dbh = connect_to_bugzilla_db();
if ( $dbh == NULL ) {
    return;
}   

global $g_open_bugs_in_the_new_tab;
if ( $g_open_bugs_in_the_new_tab ) {
    echo "<input type='hidden' id='bug_tab' value='true' />\n";
}
else {
    echo "<input type='hidden' id='bug_tab' value='false' />\n";
}

bugs_create_keyword_table($dbh, $keyword_id);

function bugs_create_keyword_table(&$dbh, $keyword_id)
{
    $users    = get_user_profiles($dbh); // <userid><login_name>
    $products = products_get($dbh);
    
    $bugs_array = bugs_get_open_by_keyword($dbh, $users, $products, $keyword_id);
                  bugs_update_worked_time($dbh, $bugs_array);

    $bugs_closed_array  = bugs_get_closed_by_keyword($dbh, $users, $products, $keyword_id);
                          bugs_update_worked_time($dbh, $bugs_closed_array);
                      
    bugs_to_table($bugs_array, $bugs_closed_array, "keyword_bugs_filter");
}

?>