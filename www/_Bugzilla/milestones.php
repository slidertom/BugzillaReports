<?php
/*
    Copyright by Tomas Rapkauskas bugzilla_base/license.txt 
    All rights reserved.
    To use this component please contact slidertom@gmail.com to obtain a license.
*/

require_once (__DIR__)."/../bugzilla_base/connect_to_bugzilla_db.php";
require_once (__DIR__).'/../func/bugs_milestones.php';

function milestones_to_combo(&$milestones, $sel)
{
    if ( $sel == "open_bugs" )   {
        echo "<option selected value='open_bugs'> - Open Bugs - </option>\n";
    }
    else {
        echo "<option value='open_bugs'> - Open Bugs - </option>\n";
    }
    
    if ( $sel == "assigned_bugs" )  {
        echo "<option selected value='assigned_bugs'> - In Progress Bugs - </option>\n";
    }
    else {
        echo "<option value='assigned_bugs'> - In Progress Bugs - </option>\n";
    }

    if ( $sel == "quarter" )     {
        echo "<option selected value='quarter'> - Quarter Bugs - </option>\n";
    }
    else {
        echo "<option value='quarter'> - Quarter bugs - </option>\n";
    }
	
	$month_bugs_descr = "- Month Bugs -";
	if ( $sel == "month" )     {
        echo "<option selected value='month'>$month_bugs_descr</option>\n";
    }
    else {
        echo "<option value='month'>$month_bugs_descr</option>\n";
    }
    
    $milestones_rev = array_reverse($milestones);
    foreach($milestones_rev as $milestone)
    {
        $bug_count = $milestone->m_open_bug_count;
        
        if ( $milestone->m_name == $sel )
        {
            echo "<option selected value='$milestone->m_name'>$milestone->m_name (bugs count: $bug_count)</option>\n";
        }
        else
        {
            echo "<option value='$milestone->m_name'>$milestone->m_name (bugs count: $bug_count)</option>\n";
        }
    }
}

function milestones_create_combo($dbh, $product_id, $sel)
{
    $milestones = milestones_get_by_product_id($dbh, $product_id);
    echo "<select name='Milestone' id='Milestone'>";
    milestones_to_combo($milestones, $sel);
    echo"</select>";
    
    if ( count($milestones) > 0 )
    {
        return $milestones[0];
    }
    
    return -1;
}
$milestone  = isset($_GET["Milestone"]) ? $_GET["Milestone"] : "";
$product_id = isset($_GET["Product"])   ? $_GET["Product"]   : -1;
//echo "milestone:";
//echo "$milestone";
//echo "product:";
//echo "$product_id";
if ( $product_id == -1 ) {
    return;
}

$dbh = connect_to_bugzilla_db();
if ( $dbh == NULL ) {
    return;
}

milestones_create_combo($dbh, $product_id, $milestone);
/*echo "<script type='text/javascript'>alert('<?php echo $combo_mil ?>');</script>";*/