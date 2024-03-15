<?php

require_once '../func/bugs_fnc.php';
require_once '../func/profiles.php';
require_once '../func/products.php';

function sql_release_notes_result_to_bugs($bugs, $users, $products)
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

function get_release_note_bugs_impl($dbh, $product_id, $milestone)
{
    $users    = get_user_profiles($dbh); // <userid><login_name>
    $products = products_get($dbh);

    $result = get_release_notes_bugs($dbh, $product_id, $milestone);
    $bugs_array = sql_release_notes_result_to_bugs($result, $users, $products);
    return $bugs_array;
}

?>