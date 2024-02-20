<?php
/*
    Copyright by Tomas Rapkauskas bugzilla_base/license.txt 
    All rights reserved.
    To use this component please contact slidertom@gmail.com to obtain a license.
*/

require_once (__DIR__).'/../func/profiles.php';
require_once (__DIR__).'/../func/products.php';
require_once (__DIR__).'/../func/bug_data.php';

require_once (__DIR__).'/../bugzilla_base/bugs_sql.php';

require_once (__DIR__).'/../tools/date_time_util.php';
require_once (__DIR__).'/../tools/date_time_select.php';

function bugs_update_worked_time(&$dbh, &$bugs_array)
{
    foreach ($bugs_array as $bug) {
        $bug->m_worked_time = get_bug_work_time($dbh, $bug->m_bug_id);
    }
}

function bugs_get_remaining_time(&$bugs_array)
{
    $remaining_time = 0;
    foreach ($bugs_array as $bug) {
        $remaining_time += $bug->get_bug_remaining_time();
    }
    return $remaining_time;
}

function bugs_get_complete($rem, $wrk)
{
    $all = $rem + $wrk;
    $per = $all > 0 ? $wrk / $all * 100 : 0;
    $per = number_format($per, 1);
    $per = $per. "%";
    return $per;
}

function bugs_get_work_time(&$bugs_array)
{
    if ( !is_array($bugs_array) ) {
        return 0;
    }
    
    $work_time = 0;
    foreach ($bugs_array as $bug) {
        $work_time += $bug->m_worked_time;
    }
    return $work_time;
}

function echo_table_summary_header()
{
    echo "<thead>\n";
    echo "<tr class='header'>\n";
    /*1*/echo "\t<th width=100> &nbsp;            </th>\n";
    /*2*/echo "\t<th width= 50> Bugs              </th>\n";
    /*3*/echo "\t<th width= 45> Worked&nbsp;(h)   </th>\n";
    /*4*/echo "\t<th width= 45> Left&nbsp;(h)     </th>\n";
    /*5*/echo "\t<th width= 40> Completed&nbsp;(%)</th>\n";
    /*6*/echo "\t<th width= 45> Worked&nbsp;(days)</th>\n";
    /*7*/echo "\t<th width= 45> Left&nbsp;(days)  </th>\n";
    echo "</tr>\n";
    echo "</thead>\n";
}

function echo_table_summary($bug_cnt, $all_work_time, $all_remaining_time, $all_complete, $title)
{
    $left_days = hours_to_days($all_remaining_time);
    $work_days = hours_to_days($all_work_time);
    
    echo "<tr class = 'summary'>\n";
    /*1*/echo "<td width=100>          $title                                        </td>\n";
    /*2*/echo "<td class = 'center' width=50>     $bug_cnt                           </td>\n";
    /*3*/echo "<td align=right width=50>          $all_work_time                     </td>\n";
    /*4*/echo "<td align=right width=50>          $all_remaining_time                </td>\n";
    /*5*/echo "<td align=right width=80>          $all_complete                      </td>\n";
    /*6*/echo "<td align=right width=80>          $work_days                         </td>\n";
    /*7*/echo "<td align=right width=80>          $left_days                         </td>\n";
    echo "</tr>\n";
}

function open_bugs_to_table(&$bugs_opened_array)
{
    $all_opened_remaining_time = bugs_get_remaining_time($bugs_opened_array);
    $all_opened_work_time      = bugs_get_work_time($bugs_opened_array);
    $all_opened_complete       = bugs_get_complete($all_opened_remaining_time, $all_opened_work_time);
    $bug_opened_cnt            = count($bugs_opened_array);

    $opened_bugs = "Opened bugs";
    
    echo "<h3> Summary: </h3>";
    echo "<table class = 'summary'>\n";
    echo_table_summary_header();
    echo "<tbody>";
    echo_table_summary($bug_opened_cnt, $all_opened_work_time, $all_opened_remaining_time, $all_opened_complete, $opened_bugs);
    echo "</table>\n";
    
    echo "<br>";
    echo "<p><em>TIP!</em> Sort multiple columns simultaneously by holding down the shift key and clicking a second, third or even fourth column header.</p>\n";
    
    if ( count($bugs_opened_array) > 0 )
    {
        echo "<h3> ${opened_bugs}: </h3>";
        bugs_echo_table($bugs_opened_array, "", "openTable tablesorter show_milestone");
    }
}

function create_input($options)
{
    $options += [
        'ID'                => '',
        'Value'             => '',
        //'Title'             => '',
        'Type'              => 'text',
        'Step'              => 'any',   //only when type === number
        'Class'             => '',
        'Hidden'            => false, /* display: none */
        'Invisible'         => false, /* visibility:hidden */
        'Label'             => '',
        'LabelPos'          => 'left',
        'LabelClass'        => '',
        'MaxLen'            => '',
        'Min'               => '',
        'Max'               => '',
        'Size'              => '',  // The size attribute specifies the visible width, in characters, of an <input> element.
        'Name'              => '',
        'Autocomplete'      => '',
        'Role'              => '',
        'AdditionalData'    => [],
        'Special'           => '',
        'Placeholder'       => '',
        'Html'              => false,
        'Readonly'          => false,
        'Disabled'          => false,
        'TextStyle'         => 'text ui-widget-content ui-corner-all',
        
        'ValidatorInteger'          => false,
        'ValidatorFloat'            => false,
        'ValidatorDecimalPlaces'    => false,
        'ValidatorMax'              => false,
    ];
    
    $id             = $options['ID']            !== ''  ? " id='{$options["ID"]}'"                      : '';
    $value          = $options['Value']         !== ''  ? " value='{$options["Value"]}'"                : '';
    $class          = $options['Class']         !== ''  ? " " . $options["Class"]                       : '';
    $role           = $options['Role']          !== ''  ? " role='{$options["Role"]}'"                  : '';
    $maxlen         = $options['MaxLen']        !== ''  ? " maxlength='{$options["MaxLen"]}'"           : '';
    $min            = $options['Min']           !== ''  ? " min='{$options["Min"]}'"                    : '';
    $size           = $options['Size']          !== ''  ? " size='{$options["Size"]}'"                  : '';
    $max            = $options['Max']           !== ''  ? " max='{$options["Max"]}'"                    : '';
    $name           = $options['Name']          !== ''  ? " name='{$options["Name"]}'"                  : '';
    $name           = $options['Autocomplete']  !== ''  ? " autocomplete='{$options["Autocomplete"]}'"  : '';
    $special        = $options['Special']       !== ''  ? " " . $options["Special"]                     : '';
    $step           = $options['Type'] === 'number' ? " step='{$options["Step"]}'"                      : '';
    $readonly       = $options['Readonly']              ? " readonly"                                   : '';
    $disabled       = $options['Disabled']              ? " disabled"                                   : '';
    $invisible      = $options['Invisible']             ? " style='visibility:hidden'"                  : '';
    $placeholder    = $options['Placeholder']   !== ''  ? " placeholder='{$options["Placeholder"]}'"    : '';
    $hidden         = $options["Hidden"]                ? " style='display:none'" : '';
    $text_style     = $options['TextStyle'];
    
    $label = '';
    if ($options["Label"] !== '')
    {
        $label_class    = $options["LabelClass"]        !== ''  ? " class='{$options["LabelClass"]}'"       : "";
        $for            = $options["ID"]                !== ''  ? " for='{$options["ID"]}'"                 : "";
        $label = "<label$label_class$for>{$options["Label"]}</label>";
    }
    
    $additional_data = '';
    if( !empty($options['AdditionalData']) )
    {
        foreach($options['AdditionalData'] as $arg => $data) {
            $additional_data .= " data-$arg='$data'";
        }
    }
    
    /* VALIDATORS */
    if ($options['ValidatorInteger'] || $options['ValidatorFloat'] || $options['ValidatorDecimalPlaces'] || $options['Max']) {
        $class .= ' ui-validator';
        $additional_data .= " data-valid-value='{$options["Value"]}'";
    }

    if ($options['ValidatorInteger']) {
        $class          .= ' validator-integer';
    }

    if ($options['ValidatorFloat'] || $options['ValidatorDecimalPlaces']) {
        $class          .= ' validator-float';

        if ($options['ValidatorDecimalPlaces']) {
            $additional_data .= " data-valid-dec-places='{$options["ValidatorDecimalPlaces"]}'";
        }
    }    

    if ($options['Max']) {
        $class          .= ' validator-max';
        $additional_data .= " data-valid-max='{$options["Max"]}'";
    }    
    
    $html = '';
    
    if ($options["LabelPos"] === 'left') $html .= $label;
    $html .= "<input type='". $options['Type']."'$step class='$text_style$class'$id$maxlen$size$role$value$special$min$max$additional_data$hidden$name$readonly$disabled$invisible$placeholder>";
    if ($options["LabelPos"] === 'right') $html .= $label;
    
    if ($options['Html']) return $html;
    else echo $html;
}

function tablesorter_create_filter($id) {
    echo "<span class='font-10 bold padded_label'>".'Filter: '.'</span>';
    create_input([
        'ID'            => $id,
        'MaxLen'        => 30,
        'Size'          => 30,
    ]);
}

function bugs_to_table(&$bugs_opened_array, &$bugs_closed_array, $filter_id = -1)
{
    $all_opened_remaining_time = bugs_get_remaining_time($bugs_opened_array);
    $all_opened_work_time      = bugs_get_work_time($bugs_opened_array);
    $all_opened_complete       = bugs_get_complete($all_opened_remaining_time, $all_opened_work_time);
    $bug_opened_cnt            = count($bugs_opened_array);
    
    $all_closed_work_time      = bugs_get_work_time($bugs_closed_array);
    $bug_closed_cnt            = count($bugs_closed_array);
    
    $all_bugs_cnt             = $bug_closed_cnt + $bug_opened_cnt;
    $all_work_time            = $all_opened_work_time + $all_closed_work_time;
    //$bug_all_cnt              = $bug_opened_cnt."/".$all_bugs_cnt;
    $all_complete             = bugs_get_complete($all_opened_remaining_time, $all_work_time);
    
    $opened_bugs = "Open Bugs";
    $closed_bugs = "Closed Bugs";
    $all_bugs    = "All Bugs";
    
    echo "<h3> Summary: </h3>";
    echo "<table class = 'summary'>\n";
    
    echo_table_summary_header();
    echo "<tbody>";
    echo_table_summary($bug_opened_cnt, $all_opened_work_time, $all_opened_remaining_time, $all_opened_complete, $opened_bugs);
    if ( $bug_closed_cnt > 0 ) {
        echo_table_summary($bug_closed_cnt, $all_closed_work_time, "0", "100%", $closed_bugs);
    }
    echo "</tbody>";
    if ( $bug_closed_cnt > 0 )
    {
        echo "<tfoot>";
        echo_table_summary($all_bugs_cnt, $all_work_time, $all_opened_remaining_time, $all_complete, $all_bugs);
        echo "</tfoot>";
    }
    echo "</table>\n";
    if ( $filter_id != -1) {
        tablesorter_create_filter($filter_id);
    }
    echo "<br>";
    echo "<p><em>TIP!</em> Sort multiple columns simultaneously by holding down the shift key and clicking a second, third or even fourth column header.</p>\n";
    
    if ( count($bugs_opened_array) > 0 ) {
        echo "<h3> ${opened_bugs} </h3>";
        bugs_echo_table($bugs_opened_array, "open_bugs_table", "openTable tablesorter");
    }
    
    if ( is_array($bugs_closed_array) && count($bugs_closed_array) > 0 )
    {
        echo "<h3> ${closed_bugs} </h3>";
        bugs_echo_table($bugs_closed_array, "closed_bugs_table", "closeTable tablesorter");
    }
}

function bugs_create_product_table(&$dbh, $product_id, $milestone)
{
    $users    = get_user_profiles($dbh); // <userid><login_name>
    $products = products_get($dbh);
    
    if ( $milestone == "open_bugs" ) {
        $bugs_array = bugs_get_open_by_product($dbh, $users, $products, $product_id);
        bugs_update_worked_time($dbh, $bugs_array);
        open_bugs_to_table($bugs_array);
        return;
    }

    if ( $milestone == "assigned_bugs" ) {
        $bugs_array = bugs_get_assigned_by_product($dbh, $users, $products, $product_id);
        bugs_update_worked_time($dbh, $bugs_array);
        open_bugs_to_table($bugs_array);
        return;
    }
    
    if ($milestone == "quarter" ) {
        $bugs_array = bugs_get_quarter_bugs($dbh, $users, $products, $product_id);
        quarter_bugs_to_table($bugs_array);
        return;
    }
    
    if ($milestone == "month") {
        $year  = isset($_GET['year'])  ? $_GET['year']  : DateTimeUtil::get_current_year();
        $month = isset($_GET['month']) ? $_GET['month'] : DateTimeUtil::current_month();
        echo "<br>";
        create_year_month_select_table($year, $month);
        $bugs_array = bugs_get_product_month_bugs($dbh, $users, $products, $product_id, $year, $month);
        quarter_bugs_to_table($bugs_array);
        return;
    }
    
    $bugs_array = bugs_get_open_by_milestone($dbh, $users, $products, $product_id, $milestone);
    bugs_update_worked_time($dbh, $bugs_array);

    $bugs_closed_array  = bugs_get_closed_by_milestone($dbh, $users, $products, $product_id, $milestone);
                          bugs_update_worked_time($dbh, $bugs_closed_array);
                          
    $filter_id = "product_filter";              
    bugs_to_table($bugs_array, $bugs_closed_array, $filter_id);
}

?>