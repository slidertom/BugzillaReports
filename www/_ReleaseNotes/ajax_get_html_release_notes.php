<?php

ob_start("ob_gzhandler");
    
require_once '../bugzilla_base/connect_to_bugzilla_db.php';
require_once 'bugs_release_notes.php';

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

$bugs_array = get_release_note_bugs_impl($dbh, $product_id, $milestone);

echo "<section class='release-notes'>\n";
    echo "<h3>Release Notes</h3>\n";
foreach ($bugs_array as $bug) 
{
    $descr        = $bug->m_summary;
    $release_note = $bug->m_add_info_array['thetext'];
    echo "<ul>\n";
        echo "<li><span class='descr'>$descr</span>$release_note</li>";
    echo "</ul>\n";
}
 
echo "</section>";

?>