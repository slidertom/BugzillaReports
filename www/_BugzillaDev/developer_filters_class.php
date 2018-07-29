<?php
// this is predefined filters, but in general
// it must be provided to create any filter using these constraints:
// start-end dates, product or milestone 
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
}
?>