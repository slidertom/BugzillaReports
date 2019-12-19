<?php
/*
	Copyright by Tomas Rapkauskas bugzilla_base/license.txt 
	All rights reserved.
	To use this component please contact slidertom@gmail.com to obtain a license.
*/

require_once("../common/header.php");
require_once("../bugzilla_base/connect_to_bugzilla_db.php");
require_once("developers.php");
require_once("developer_filters.php");

class CGenerateDeveloperPage extends CGeneratePage
{
	protected function GenerateHeadData() 
	{
		echo "<link rel='stylesheet' type='text/css' href='../_Bugzilla/bugzilla.css'          />\n";
		echo "<link rel='stylesheet' type='text/css' href='../_Bugzilla/sort_style.css'        />\n";
		echo "<link rel='stylesheet' type='text/css' href='../jquery/jgplot/jquery.jqplot.css' />\n";
		echo "<style type='text/css'>#bugs_pie_chart .jqplot-data-label { color:rgb(255,255,255); }</style>\n";
	}
	
	protected function GenerateJsData()   
	{
		echo "<script type='text/javascript' src='../jquery/jquery-latest.js'></script>";
		echo "<script type='text/javascript' src='../jquery/table_hover.js'></script>"; 
		echo "<script type='text/javascript' src='../jquery/jquery.tablesorter.js'></script>"; 
		echo "<script type='text/javascript' src='../jquery/priority_sort.js'></script>"; 
		echo "<script type='text/javascript' src='../jquery/ajaxPost.js'></script>"; 

		echo "<script type='text/javascript' src='../jquery/jgplot/jquery.jqplot.min.js'></script>";
		echo "<script type='text/javascript' src='../jquery/jgplot/plugins/jqplot.pieRenderer.min.js'></script>";
		echo "<script type='text/javascript' src='../jquery/jgplot/plugins/jqplot.donutRenderer.min.js'></script>";
		echo "<script type='text/javascript' src='../tools/select_ctrl.js'></script>"; 		
		echo "<script type='text/javascript' src='../tools/date_time_util.js'></script>"; 		
		echo "<script type='text/javascript' src='developer_change.js'></script>"; 
	}
	
	protected function GenerateModule() 
	{
		$dbh = connect_to_bugzilla_db();
		
		if ($dbh == NULL) {
			return;
		}
	
		echo "<table border=0>\n";
			echo "<tr>\n";
				echo "<td><b>Developer: </b></td>";
				echo "<td>"; 
					$dev = isset($_GET['developer']) ? $_GET['developer'] : "";
					//var_dump($dev);
					$sel_dev_id = developers_create_combo($dbh, $dev); 
				echo "</td>\n";
				echo "<td><b>Filters: </b></td>";
				echo "<td>";
					echo "<span id=openedDevFilters>";
						$filter = isset($_GET['filter']) ? $_GET['filter'] : "";
						create_developer_filters_combo($dbh, $sel_dev_id, $filter);
					echo "</span>";
				echo "</td>";
			echo "</tr>\n";
		echo "</table>\n";
		
		if ( $sel_dev_id != -1 )
		{
			echo "<div id='OpenedHint'><b></b></div>\n";
		}
		else
		{
			echo "<div id='OpenedHint'><b>Bugs info will be listed here.</b></div>\n";
		}
	}
}

$gen_page = new CGenerateDeveloperPage();
$gen_page->Generate();
?>
