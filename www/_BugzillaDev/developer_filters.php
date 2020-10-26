<?php
/*
    Copyright by Tomas Rapkauskas bugzilla_base/license.txt 
    All rights reserved.
    To use this component please contact slidertom@gmail.com to obtain a license.
*/

require_once '../_Bugzilla/bugs_fnc.php';
require_once 'developer_filters_class.php';

function get_developer_products($dbh, $developer_id)
{
    $users    = get_user_profiles($dbh); // <userid><login_name>
    $products = products_get($dbh);
    
    $dev_products = array();
    // trade off: try to avoid selects, in case of database fileds change it will be less code to update
    $bugs = bugs_get_by_developer($dbh, $users, $products, $developer_id); 
    
    bugs_explode_by_product($pro_bugs, $bugs);
    
    foreach ($bugs as $bug )
    {
        if ( !isset($dev_products[$bug->m_product->m_id] ) ) {
            $count = count($pro_bugs[$bug->m_product->m_name]);
            $dev_products[$bug->m_product->m_id] = $bug->m_product->m_name." (bugs count: ".$count.")";
        }
    }
    
    return $dev_products;
}

function create_filter_option($filter_id, $filter)
{
    $name = get_developer_filter_name($filter_id);
    if ($filter == $filter_id) {
        echo "<option selected value='$filter_id'>".$name."</option>";	
    }
    else {
        echo "<option value='$filter_id'>".$name."</option>";	
    }
}

function create_developer_filters_combo($dbh, $sel_dev_id, $filter)
{
    $dev_products = get_developer_products($dbh, $sel_dev_id);
    
    echo "<select id='Developer_Filters_Combo'>";
        create_filter_option(DeveloperFilters::Open,           $filter);
        create_filter_option(DeveloperFilters::Assigned,       $filter);
        create_filter_option(DeveloperFilters::WeeklyProgress, $filter);
        create_filter_option(DeveloperFilters::PrevQuaterProd, $filter);
        create_filter_option(DeveloperFilters::PrevQuaterMile, $filter);
        create_filter_option(DeveloperFilters::ThisQuaterProd, $filter);
        create_filter_option(DeveloperFilters::ThisQuaterMile, $filter);
        create_filter_option(DeveloperFilters::ThisMonth,      $filter);
        create_filter_option(DeveloperFilters::PrevMonth,      $filter);
        create_filter_option(DeveloperFilters::MonthMile,      $filter);
        create_filter_option(DeveloperFilters::ThisYear,       $filter);
        
        foreach ($dev_products as $product_id => $product_name ) {
            if ( $filter == $product_id ) {
                echo "<option selected value='$product_id'>".$product_name."</option>";	
            }
            else {
                echo "<option value='$product_id'>".$product_name."</option>";	
            }	
        }
        
    echo"</select>";
}

?>