<?php
/*
    Copyright by Tomas Rapkauskas bugzilla_base/license.txt 
    All rights reserved.
    To use this component please contact slidertom@gmail.com to obtain a license.
*/

ob_start("ob_gzhandler");
    
require_once '../bugzilla_base/connect_to_bugzilla_db.php';
require_once '../func/bugs_fnc.php';
require_once '../func/profiles.php';
require_once '../func/products.php';

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

$users    = get_user_profiles($dbh); // <userid><login_name>
$products = products_get($dbh);

$result = get_release_notes_bugs($dbh, $product_id, $milestone);
$bugs_array = sql_release_notees_result_to_bugs($result, $users, $products);

release_notes_bugs_to_table($bugs_array);

function sql_release_notees_result_to_bugs($bugs, $users, $products)
{
    $bugs_array = array();
    foreach ($bugs as $row) 
    {
        $bug = parse_row_to_bug_data($row, $users, $products);
        $bug->m_add_info_array['thetext'] = $row['thetext'];
        $bug->m_add_info_array['who']     = $users[$row['who']];
        $bugs_array[$bug->m_bug_id] = $bug;
    }

    return $bugs_array;
}

function release_notes_bugs_to_table($bugs_array)
{
    if ( count($bugs_array) == 0 ) {
        return;
    }

    $opened_bugs = "Release Notes";
    
    echo "<br>";
    echo "<p><em>TIP!</em> Sort multiple columns simultaneously by holding down the shift key and clicking a second, third or even fourth column header.</p>\n";
    
    tablesorter_create_filter('product_filter');

    echo "<h3> ${opened_bugs} </h3>";
    bugs_release_notes_table::echo_table($bugs_array, "open_bugs_table", "openTable tablesorter show_milestone");
}

class bugs_release_notes_table
{
    static public function echo_table(&$bugs, $table_id, $table_class)
    {
        echo "<table id='$table_id' class='$table_class'>\n";
        bugs_release_notes_table::echo_header();
        bugs_release_notes_table::echo_body($bugs);
        echo "</table>\n";
    }
    
    static private function echo_header()
    {
        echo "<thead>\n";
        echo "<tr class='header'>\n";
        /* 1*/echo "\t<th width= 50> Bug         </th>\n";
        /* 2*/echo "\t<th width= 90> Sev         </th>\n";
        /* 3*/echo "\t<th width= 40> Pri         </th>\n";
        /* 3*/echo "\t<th width= 40> Status      </th>\n"; // Resolution
        /* 4*/echo "\t<th> Release Note          </th>\n";
        /* 6*/echo "\t<th>           Bug Summary </th>\n";
        /* 7*/echo "\t<th style='min-width:100px;'> Note Reporter</th>\n";
        echo "</tr>\n";
        echo "</thead>\n";
    }

    static private function echo_body($bugs)
    {
        echo "<tbody>\n";
        foreach ($bugs as $bug) {
            bugs_release_notes_table::echo_row($bug);
        }
        echo "</tbody>\n";
    }
    
    static private function echo_row($bug)
    {
        $release_note   = $bug->m_add_info_array['thetext'];
        $who            = $bug->m_add_info_array['who'];
        $bug_class      = $bug->m_severity;    
        $reporter_name  = $who->m_real_name;
        $reporter_email = $who->m_login_name;
        
        echo "<tr>\n";
        /* 1*/echo "\t<td>".generate_bug_link_href($bug->m_bug_id)."                                </td>\n";
        /* 2*/echo "\t<td class = '$bug_class'>                          $bug->m_severity           </td>\n";
        /* 3*/echo "\t<td>                                               $bug->m_priority           </td>\n";
        /* 3*/echo "\t<td>                                               $bug->m_status             </td>\n";
        /* 5*/echo "\t<td>                                               $release_note              </td>\n";
        /* 7*/echo "\t<td class = '$bug_class'>                          &nbsp;&nbsp;$bug->m_summary</td>\n";
        /* 8*/echo "\t<td>             <a href=mailto:'$reporter_email'> $reporter_name      </a>   </td>\n";
        echo "</tr>\n\n";
    }
}

?>