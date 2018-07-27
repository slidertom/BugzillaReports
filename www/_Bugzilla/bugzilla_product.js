/*
	Copyright by Tomas Rapkauskas bugzilla_base/license.txt 
	All rights reserved.
	To use this component please contact slidertom@gmail.com to obtain a license.
*/

function Select_Value_Set(SelectName, Value) 
{
	var obj = document.getElementById(SelectName);
	
	for(index = 0;  index < obj.length; index++) 
	{
		if( obj[index].value == Value)
		{
			obj.selectedIndex = index;
			return true;
		}
	}
	return false;
}

function SelectProduct(product_id) 
{
	if ( product_id != -1 )
	{   
		Select_Value_Set("Product", product_id);
	}
}

function SelectMilestone(milestone_str) 
{
	if ( milestone_str != -1 )
	{
		Select_Value_Set("Milestone", milestone_str);
	}
}

function Product_ChangeWithMilestone(str, milestone) 
{ 
	if (str=="") {
		document.getElementById("milestoneHint").innerHTML="";
		return "";
	} 
	
	let values  = "Product="+str+"&Milestone="+milestone;
	ajaxPostSync("milestones.php?"+values, "", function(data) 
	{
		document.getElementById("milestoneHint").innerHTML=data;
	});
	
	return str+"?"+milestone;
 } 
 
function set_hash(str)
{
	window.location.hash = str;
}

function Milestone_ChangeWithProduct(str, product) 
{ 
	create_gantt_chart(product, str);
	
	if (str=="")
	{
		document.getElementById("OpenedHint").innerHTML="";
		return;
	} 
	set_hash(product+"?"+str);
	var values  = "Product="+product+"&Milestone="+str;
	
	ajaxPostSync("ajax_product_bugs.php?"+values, "", function(data) 
	{
		document.getElementById("OpenedHint").innerHTML=data;
		$(".openTable").tablesorter( { sortList: [[2,0], [1, 0]], widgets: ['zebra']}); 
		$(".closeTable").tablesorter({ sortList: [[2,0], [1, 0]], widgets: ['zebra']}); 
		
		if ( !$(".openTable").hasClass("show_milestone") )
		{
			$(".openTable").find('td:nth-child(9),th:nth-child(9)').hide();  // TargetM
		}
		$(".openTable").find('td:nth-child(8),th:nth-child(8)').hide();  // Product  
		$(".openTable").find('td:nth-child(10),th:nth-child(10)').hide(); // start date	
		$(".openTable").find('td:nth-child(11),th:nth-child(11)').hide(); // end date	
		
		$(".closeTable").find('td:nth-child(9),th:nth-child(9)').hide(); // TargetM
		$(".closeTable").find('td:nth-child(8),th:nth-child(8)').hide(); // Product
		$(".closeTable").find('td:nth-child(6),th:nth-child(6)').hide(); // Left	
		$(".closeTable").find('td:nth-child(10),th:nth-child(10)').hide(); // start date	
		$(".closeTable").find('td:nth-child(11),th:nth-child(11)').hide(); // end date	
	});
	
	var release_hint = document.getElementById("ReleaseHint");
	if ( release_hint )
	{
		ajaxPost("ajax_get_product_release_date.php?"+values, "", function(data) 
		{
			//alert(data);
			data = $.trim(data);
			
			if ( data.length > 0 )
			{
				var release = "<b>Release date: ";
				release += data;
				release += "</b>";
				release_hint.innerHTML=release;
			}
			else
			{
				release_hint.innerHTML="";
			}
		});
	}
} 
 
function Milestone_Change(str) 
{ 
	var product = document.getElementById("Product").value;
	Milestone_ChangeWithProduct(str, product);
} 
 
function HashGetProduct()
{
	var hash = window.location.hash.substring(1);
	var pos  = hash.indexOf("?");
	
	if ( pos == -1 ) {
		return "";
	}
	var product   = hash.substring(0, pos);
	return product;
}

function HashGetMilestone()
{
	var hash = window.location.hash.substring(1);
	var pos = hash.indexOf("?");
	
	if ( pos == -1 )
	{
		return "";
	}
	
	var milestone = hash.substring(pos);
	milestone     = milestone.substring(1); // drop ?
	return milestone;
}

function InitMile()
{
	var product   = HashGetProduct();
	var milestone = HashGetMilestone();
	if ( product == "" || milestone == "" )
	{
		return;
	}
	
	SelectProduct(product);
	var hash = Product_ChangeWithMilestone(product, milestone);
	set_hash(hash);
}

function InitBugs()
{
	var product   = HashGetProduct();
	var milestone = HashGetMilestone();
	if ( product == "" && milestone == "" )
	{
		product   = document.getElementById("Product").value;
		milestone = document.getElementById("Milestone").value;
	}
	
	if ( product == "" || milestone == "" )
	{
		return;
	}
	Milestone_ChangeWithProduct(milestone, product);
}

var g_product_change_mode = false;
$("#Milestone").live("change", function() 
{
	if ( g_product_change_mode )
	{
		return;
	}
	g_product_change_mode = true;
	var milestone = $("#Milestone").val();
	Milestone_Change(milestone);
	g_product_change_mode = false;
});

$("#Product").live("change", function() 
{
	if ( g_product_change_mode )
	{
		return;
	}
	g_product_change_mode = true;
	var product = $("#Product").val();
	var hash = Product_ChangeWithMilestone(product, "");
	set_hash(hash);
	var milestone = $("#Milestone").val();
	Milestone_Change(milestone);
	g_product_change_mode = false;
});

function get_bug_title(obj)
{	
	if ( obj )
	{
		return obj.html();
	}
	return "bug_title";
}

function create_gantt_chart(product, milestone)
{
	let values  = "Product="+product+"&Milestone="+milestone;
	jsonPost("ajax_json_get_product_bugs.php?"+values, "", function(gantt_data) 
	{
		if ( !gantt_data || gantt_data.length <= 0) {
			$("#product_gantt").html("");
            return;
		}
		
		var proj = $.trim(product);
		var mil  = $.trim(milestone);
		proj = proj.replace(/\s+/g, ''); // remove spaces
		mil  = mil.replace(/\s+/g, '');  // remove spaces

		/*
		var gantt_id = "Product_"+proj+"_Milestone_"+mil;
		var gantt_div = "<div id='"+gantt_id+"'></div>";
		//$(".product_gantt").text(gantt_div);
		document.getElementById("product_gantt").innerHTML = gantt_div;
		
		if ( document.getElementById(gantt_id) )
		{
			alert("found");
		}		
		gantt_id = "#"+gantt_id;
		*/
		//alert("#Product_2_Milestone_6.0Beta");
		$("#product_gantt").gantt({
			source:gantt_data,
			scale: "days",
			minScale: "days",
			maxScale: "months",
			onItemClick: function(data) {
				window.location.href = data;
				//alert("Item clicked - show some details");
			},
			onAddClick: function(dt, rowId) {
				//alert("Empty space clicked - add an item!");
			}
		});
/*	
		$("#product_gantt").popover({
				selector: ".bar",
				title: get_bug_title($(this)),
				content: "And I'm the content of said popover."
		});	
	*/	
		

	});
}

$(document).ready(function() 
{
	g_product_change_mode = true;
	InitMile();
	InitBugs();
	g_product_change_mode = false;
});

$("#product_gantt").live("hover",
	function () 
	{
		alert("ok");
		//$(this).addClass("hover");
	},
	function () 
	{
		//$(this).removeClass("hover");
	}
);

$(window).bind('hashchange', function() 
{
	if ( g_product_change_mode )
	{
		return;
	}
	
	g_developer_change_mode = true;
	InitMile();
	InitBugs();
	g_developer_change_mode = false;
});
