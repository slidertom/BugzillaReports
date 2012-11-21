<?php
/*
	Copyright by Tomas Rapkauskas bugzilla_base/license.txt 
	All rights reserved.
	To use this component please contact slidertom@gmail.com to obtain a license.
*/

require_once(dirname(__FILE__)."/../bugzilla_base/connect_to_bugzilla_db.php");

class CMilestone
{
	public $m_open_bug_count;
	public $m_name;
};

function get_milestone_opened_bugs_count($dbh, $product_id, $mile)
{
	$result = 0;
	try
	{
		$sql = "SELECT COUNT(*) FROM bugs where (bug_status='NEW' OR bug_status='ASSIGNED' OR bug_status='REOPENED') AND product_id ='$product_id' AND target_milestone='$mile'";
		$result = $dbh->query($sql);
	}
	catch(PDOException $e)
	{
		echo $e->getMessage();
		return 0;
	}
	
	foreach ($result as $row)
	{
		$count = $row['COUNT(*)'];
		//echo "$count";
		return $count;
	}

	return $result;
}

function milestones_get_by_product_id($dbh, $product_id)
{
	$sql = "SELECT * FROM milestones where product_id=$product_id ORDER BY sortkey";
	//echo "$sql";
	$qr = $dbh->query($sql);
	$milestones = array();
	foreach ($qr as $row)
	{
		$stone = new CMilestone;
		$stone->m_name           = $row['value'];
		$stone->m_open_bug_count = get_milestone_opened_bugs_count($dbh, $product_id, $stone->m_name);
		$milestones[] = $stone;
		//echo "$value";
	}
	return $milestones;
}

function milestones_to_combo(&$milestones, $sel)
{
	if ( $sel == "open_bugs" )	
	{
		echo "<option selected value='open_bugs'> - Open bugs - </option>\n";
	}
	else
	{
		echo "<option value='open_bugs'> - Open bugs - </option>\n";
	}
	
	if ( $sel == "assigned_bugs" )	
	{
		echo "<option selected value='assigned_bugs'> - In Progress bugs - </option>\n";
	}
	else
	{
		echo "<option value='assigned_bugs'> - In Progress bugs - </option>\n";
	}
	if ( $sel == "quarter" )	
	{
		echo "<option selected value='quarter'> - Quarter bugs - </option>\n";
	}
	else
	{
		echo "<option value='quarter'> - Quarter bugs - </option>\n";
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

?>


