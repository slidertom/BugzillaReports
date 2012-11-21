<?php
/*
	Copyright by Tomas Rapkauskas bugzilla_base/license.txt 
	All rights reserved.
	To use this component please contact slidertom@gmail.com to obtain a license.
*/

require_once(dirname(__FILE__)."/../common/header.php");

require_once(dirname(__FILE__)."/../bugzilla_base/connect_to_bugzilla_db.php");
require_once(dirname(__FILE__)."/products.php");
require_once(dirname(__FILE__)."/milestones.php");

class CGenerateBugzillaPage extends CGeneratePage
{
	protected function GenerateModule() 
	{
		$dbh = connect_to_bugzilla_db();
		
		if ( $dbh == NULL ) 
		{
			return;
		}
		
		$prod_id = -1;
		
		echo "<table border=0>\n";
			echo "<tr>\n";
				echo "<td><b>Product: </b></td>";
				echo "<td>";$prod_id = products_create_combo($dbh); echo "</td>\n";
				echo "<td><b>Milestones: </b></td>\n";
				echo "<td>";
						if ( $prod_id != -1 )
						{   // create milestones combo with default product id
							echo "<div id='milestoneHint'>";
							$mil_str = milestones_create_combo($dbh, $prod_id, "");
							echo "</div>";
						}
						else
						{					
							echo "<div id='milestoneHint'><b>Milestones info will be listed here.</b></div>";
						}
				echo "</td>\n";
				echo "<td>";
				echo "<span id='ReleaseHint'></span>";
				echo "</td>\n";
			echo "</tr>\n";
		echo "</table>\n";
		
		echo "<div  id='product_gantt'></div>\n";
		echo "<span id='OpenedHint'><b>Bugs info will be listed here.</b></span>\n";
	}
	
	protected function GenerateHeadData() 
	{
		echo "<link rel='stylesheet' type='text/css' href='bugzilla.css' />\n";
		echo "<link rel='stylesheet' type='text/css' href='sort_style.css' />\n";
		echo "<link rel='stylesheet' type='text/css' href='gantt/css/style.css' />\n";
		echo "<link rel='stylesheet' type='text/css' href='../jquery/opentip/opentip.css' />";
	}
	
	protected function GenerateJsData()   
	{ 
		echo "<script type='text/javascript' src='../jquery/jquery-latest.js'></script>\n";
		echo "<script type='text/javascript' src='../jquery/jquery.tablesorter.js'></script>\n";
		echo "<script type='text/javascript' src='../jquery/priority_sort.js'></script>\n";
		echo "<script type='text/javascript' src='../jquery/table_hover.js'></script>\n";
		echo "<script type='text/javascript' src='../jquery/ajaxPost.js'></script>\n";
		echo "<script type='text/javascript' src='gantt/js/jquery.fn.gantt.js'></script>\n";
		// opentip
		echo "<script type='text/javascript' src='../jquery/prototype.js'></script>";
		echo "<script type='text/javascript' src='../jquery/opentip/test/scriptaculous-1.9.0/scriptaculous.js'></script>"; 
		echo "<script type='text/javascript' src='../jquery/opentip/opentip.js'></script>"; 
		echo "<script type='text/javascript' src='../jquery/opentip/excanvas.js'></script>";
		// end opentip
		echo "<script type='text/javascript' src='bugzilla_product.js'></script>\n"; 
	}
}

$gen_page = new CGenerateBugzillaPage();
$gen_page->Generate();

?>