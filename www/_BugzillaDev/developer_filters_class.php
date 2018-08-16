<?php
// this is predefined filters, but in general
// it must be provided to create any filter using these constraints:
// start-end dates, product or milestone 

// TODO: multi language support (gettext)
abstract class DeveloperFilters
{
    const Open           = "open_bugs";
    const Assigned       = "assigned_bugs";
    const PrevQuaterProd = "quarter_bugs_product";
    const ThisQuaterProd = "this_quarter_bugs_product";
    const PrevQuaterMile = "quarter_bugs";
    const ThisQuaterMile = "this_quarter_bugs";
    const PrevMonth      = "prev_month_bugs";
    const ThisMonth      = "this_month_bugs";
    const PrevYear       = "prev_year";
    const ThisYear       = "this_year";
}

function get_developer_filter_name($filter)
{
    switch ($filter)
    {
    case DeveloperFilters::Open:
        return "&nbsp;- Open Bugs -";
        break;
    case DeveloperFilters::Assigned:
        return "&nbsp;- In Progress Bugs -";
        break;
    case DeveloperFilters::PrevQuaterProd:    
        return "&nbsp;- Prev. Quarter Bugs by Product   -";
        break;
    case DeveloperFilters::ThisQuaterProd:
        return "&nbsp;- This Quarter Bugs by Product    -";
        break;
    case DeveloperFilters::PrevQuaterMile:
        return "&nbsp;- Prev. Quarter Bugs by Milestone -";
        break;
    case DeveloperFilters::ThisQuaterMile:
        return "&nbsp;- This  Quarter Bugs by Milestone -";
        break;  
    case DeveloperFilters::PrevMonth:
        return "&nbsp;- Prev. Month Bugs by Product   -";
        break;  
    case DeveloperFilters::ThisMonth:
        return "&nbsp;- This Month Bugs by Product   -";
        break;  
    case DeveloperFilters::PrevYear:
        return "&nbsp;- Prev. Year Bugs by Product   -";
        break;  
    case DeveloperFilters::ThisYear:
        return "&nbsp;- This Years Bugs by Product   -";
        break;  
    }
    
    return "";
}

?>