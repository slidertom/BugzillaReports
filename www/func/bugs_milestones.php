<?php

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

?>