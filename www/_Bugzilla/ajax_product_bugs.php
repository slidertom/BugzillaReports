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

if ( !isset($_GET['Milestone']) ) {
    return;
}

if ( !isset($_GET['Product']) ) {
    return;
}

$milestone  = $_GET['Milestone'];
$product_id = $_GET['Product'];

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

bugs_create_product_table($dbh, $product_id, $milestone);