<?php
/*
    Copyright by Tomas Rapkauskas bugzilla_base/license.txt 
    All rights reserved.
    To use this component please contact slidertom@gmail.com to obtain a license.
*/

require_once "../common/header.php";
require_once "../bugzilla_base/connect_to_bugzilla_db.php";
require_once "developers.php";
require_once "developer_filters.php";
require_once "developer_next_week_plan.php";

class CGenerateDeveloperNextWeelPlanPage
{
    public function Generate()
    {
        echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n";
        echo "<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"en\" lang=\"en\">\n";
        
        $this->GenerateTitle();
        $this->GenerateHead();
        $this->GenerateModule();
        echo "</html>\n";
        
        $this->GenerateJs();
    }
    
    protected function GenerateTitle()    { echo "<title>" . get_system_name() . get_version() . "</title>\n"; }
    protected function GenerateHead()
    {	
        echo "<head>\n";
            echo "<meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge\">\n";
            echo "<meta http-equiv=\"content-type\" content=\"text/html;charset=UTF-8\">\n";
            
            $favicon  = '../res/favicon.ico';		
            echo "  <link rel='shortcut icon' href='$favicon' type='image/x-icon'>\n";
            echo "  <link rel='icon'          href='$favicon' type='image/x-icon'>\n";
            $this->GenerateHeadData();
        echo "</head>\n\n";
    }
    
    protected function GenerateHeadData() 
    {
        echo "<link rel='stylesheet' type='text/css' href='../_Bugzilla/bugzilla.css'          />\n";
        echo "<link rel='stylesheet' type='text/css' href='../_Bugzilla/sort_style.css'        />\n";
        echo "<link rel='stylesheet' type='text/css' href='../jquery/jgplot/jquery.jqplot.css' />\n";
        echo "<style type='text/css'>#bugs_pie_chart .jqplot-data-label { color:rgb(255,255,255); }</style>\n";
    }
    
    protected function GenerateJs()   
    {
        echo "<script type='text/javascript' src='../jquery/jquery-latest.js'></script>";
        echo "<script type='text/javascript' src='../jquery/table_hover.js'></script>"; 
        echo "<script type='text/javascript' src='../jquery/jquery.tablesorter.js'></script>"; 
        echo "<script type='text/javascript' src='../jquery/priority_sort.js'></script>"; 
        echo "<script type='text/javascript' src='../jquery/ajaxPost.js'></script>"; 

        echo "<script type='text/javascript' src='../jquery/jgplot/jquery.jqplot.min.js'></script>";
        echo "<script type='text/javascript' src='../jquery/jgplot/plugins/jqplot.pieRenderer.min.js'></script>";
        echo "<script type='text/javascript' src='../jquery/jgplot/plugins/jqplot.donutRenderer.min.js'></script>";
        //echo "<script type='text/javascript' src='../tools/select_ctrl.js'></script>"; 
        //echo "<script type='text/javascript' src='../tools/date_time_util.js'></script>";
        //echo "<script type='text/javascript' src='developer_change.js'></script>"; 
    }
    
    protected function GenerateModule() 
    {
        $dbh = connect_to_bugzilla_db();
        
        if ($dbh == NULL) {
            return;
        }
    
        if ( !isset($_GET['developer']) ) {
            return;
        }
        
        $developer_id = $_GET['developer'];
    
        $users    = get_user_profiles($dbh); // <userid><login_name>
        $products = products_get($dbh);

        $developer = $users[$developer_id];
        $date = date("Y-m-d H:i");
        echo "<h3>$developer->m_real_name $date</h3>";
        
        echo_developer_next_week_plan($dbh, $users, $products, $developer_id, false);
    }
}

$gen_page = new CGenerateDeveloperNextWeelPlanPage();
$gen_page->Generate();
?>