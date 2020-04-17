<?php
/*
    Copyright by Tomas Rapkauskas bugzilla_base/license.txt 
    All rights reserved.
    To use this component please contact slidertom@gmail.com to obtain a license.
*/

class CKeyword
{
    public $m_id;
    public $m_name;
    public $m_open_bug_count;
};

function get_keyword_bugs_count($dbh, $keyword_id)
{
    $result = 0;
    try {
        $sql = "SELECT COUNT(*) FROM keywords where (keywordid='$keyword_id')";
        $result = $dbh->query($sql);
    }
    catch(PDOException $e) {
        echo $e->getMessage();
        return 0;
    }
    
    foreach ($result as $row) {
        return $row['COUNT(*)'];
    }

    return $result;
}

function keywords_get(&$dbh)
{
    $sql = "SELECT * FROM keyworddefs";
    $qr  = $dbh->query($sql);
    
    $keywrods = array();
    foreach ($qr as $row)
    {
        $keyword                   = new CKeyword();
        $keyword->m_id             = $row['id'];
        $keyword->m_name           = $row['name'];
        $keyword->m_open_bug_count = get_keyword_bugs_count($dbh, $keyword->m_id);
        $keywrods[$keyword->m_id]  = $keyword;
    }
    return $keywrods;
}

function keywords_to_combo(&$keywords)
{
    $first_id = -1;
    foreach($keywords as $keyword) {
        $id = $keyword->m_id;
        if ( $first_id == -1 ) {
            $first_id = $id;
        }
        echo "<option value=$id>$keyword->m_name (bugs count $keyword->m_open_bug_count)</option>";
    }
    
    return $first_id;
}

// returns selected product value in the combo box
function products_create_combo($dbh)
{
    $keywords = keywords_get($dbh);
    echo "<select name='Keyword' id='Keyword'>";
    $first_id = keywords_to_combo($keywords);
    echo"</select>";
    if ( count($keywords) > 0 ) {
        return $first_id;
    }
    return -1;
}

function get_keyword_id_by_name($keywords, $name)
{
    foreach ($keywords as $keyword)
        if ($name == $keyword->m_name)
            return $keyword->m_id;
    
    return NULL;
}

?>