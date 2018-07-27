/*
	Copyright by Tomas Rapkauskas bugzilla_base/license.txt 
	All rights reserved.
	To use this component please contact slidertom@gmail.com to obtain a license.
*/

jQuery.noConflict();

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
	if (str=="")
	{
		document.getElementById("milestoneHint").innerHTML="";
		return "";
	} 
	
	var values  = "Product="+str+"&Milestone="+milestone;
	
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
		jQuery(".openTable").tablesorter( { sortList: [[2,0], [1, 0]], widgets: ['zebra']}); 
		jQuery(".closeTable").tablesorter({ sortList: [[2,0], [1, 0]], widgets: ['zebra']}); 
		
		if ( !jQuery(".openTable").hasClass("show_milestone") )
		{
			jQuery(".openTable").find('td:nth-child(9),th:nth-child(9)').hide();  // TargetM
		}
		jQuery(".openTable").find('td:nth-child(8),th:nth-child(8)').hide();  // Product  
		jQuery(".openTable").find('td:nth-child(10),th:nth-child(10)').hide(); // start date	
		jQuery(".openTable").find('td:nth-child(11),th:nth-child(11)').hide(); // end date	
		
		jQuery(".closeTable").find('td:nth-child(9),th:nth-child(9)').hide(); // TargetM
		jQuery(".closeTable").find('td:nth-child(8),th:nth-child(8)').hide(); // Product
		jQuery(".closeTable").find('td:nth-child(6),th:nth-child(6)').hide(); // Left	
		jQuery(".closeTable").find('td:nth-child(10),th:nth-child(10)').hide(); // start date	
		jQuery(".closeTable").find('td:nth-child(11),th:nth-child(11)').hide(); // end date	
	});
	
	var release_hint = document.getElementById("ReleaseHint");
	if ( release_hint )
	{
		ajaxPost("ajax_get_product_release_date.php?"+values, "", function(data) 
		{
			//alert(data);
			data = jQuery.trim(data);
			
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
	
	if ( pos == -1 )
	{
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
jQuery("#Milestone").live("change", function() 
{
	if ( g_product_change_mode )
	{
		return;
	}
	g_product_change_mode = true;
	var milestone = jQuery("#Milestone").val();
	Milestone_Change(milestone);
	g_product_change_mode = false;
});

jQuery("#Product").live("change", function() 
{
	if ( g_product_change_mode )
	{
		return;
	}
	g_product_change_mode = true;
	var product = jQuery("#Product").val();
	var hash = Product_ChangeWithMilestone(product, "");
	set_hash(hash);
	var milestone = jQuery("#Milestone").val();
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

function open_in_new_tab(url)
{
  window.open(url, '_blank');
  window.focus();
}

function create_gantt_chart(product, milestone)
{
	try
	{
		var values  = "Product="+product+"&Milestone="+milestone;
		
		//ajaxPost("ajax_json_get_product_bugs.php?"+values, "", function(gantt_data) 
		jsonPost("ajax_json_get_product_bugs.php?"+values, "", function(gantt_data) 
		{
			if ( !gantt_data || gantt_data.length <= 0)
			{
				jQuery("#product_gantt").html("");
				return;
			}
			
			jQuery("#product_gantt").gantt(
			{
				source:gantt_data,
				scale: "days",
				minScale: "days",
				maxScale: "months",
				onItemClick: function(data) {
				    if ( jQuery("#bug_tab").val() == "true" )
				    {
						open_in_new_tab(data);
					}
					else
					{
						window.location.href = data;
					}
					//alert("Item clicked - show some details");
				},
				onAddClick: function(dt, rowId) {
					//alert("Empty space clicked - add an item!");
				}
			});		
			//delay(3000);
			//alert("2");
			//$("#Product").addTip('Slick', 'Title', { showOn: 'click', style: 'slick', tipJoint: [ 'left', 'middle' ], target: true });
	
			//$(".GanttBug").each(function() {	
				
				//}
			//alert("xx");
		});
	}
	catch (e)
	{
		alert(e.message);
	}
}

function create_bug_tooltip(item_id, bug_id_text, bug_data)
{
	try
	{
		var title           = bug_id_text+": "      +bug_data.summary;
		var reporter_div    = "<div>Reporter:       "+bug_data.reporter+"</div>";
		var remain_time_div = "<div>Remaining time: "+bug_data.remain_time+" h</div>";
		var priority_div    = "<div>Priority:       "+bug_data.priority+"</div>";
		var severity_div    = "<div>Severity:       "+bug_data.severity+"</div>";
		var complete_div    = "<div>Completed:      "+bug_data.complete+"</div>";
		var worked_div      = "<div>Worked:         "+bug_data.worked_time+" h</div>";
		
		var content = priority_div + severity_div + reporter_div + remain_time_div+worked_div+complete_div;
		// now just create a tooltip
		$(item_id).addTip(content, title, { target: true, stem: true, tipJoint: [ "left", "middle" ], showOn: "creation", showEffect: 'appear' });
		// next time please show tooltip on mouseover
		$(item_id).addTip(content, title, { target: true, stem: true, tipJoint: [ "left", "middle" ], showEffect: 'appear' });
	}
	catch (e)
	{
		alert(e.message);
	}
}

jQuery('.GanttBug').live('mouseover', function() 
{ 
	try
	{
		var child = jQuery(this).find(".fn-label").first();
		if ( !child )
		{
			return;
		}
		
		//var str_id = parseInt(jQuery(this).attr("id"));
		var str_id = jQuery(this).attr("id");
		if ( typeof str_id !== "undefined" )
		{
			return;
		}
		
		var bug_id_text = child.text();
		var values = "bug_id="+bug_id_text;
		
		//ajaxPostSync("ajax_json_get_bug_info.php?"+values, "", function(bug_data) 
		var external_bug_data;
		jsonPostSync("ajax_json_get_bug_info.php?"+values, "", function(bug_data) 
		{	
			external_bug_data = bug_data;
		});
		
		var item_id = "bug_" + bug_id_text;
		//alert(item_id);
		jQuery(this).attr("id", item_id);
	
		//alert(child.text());
		create_bug_tooltip(item_id, bug_id_text, external_bug_data);
	}
	catch (e)
	{
		alert(e.message);
	}
});

jQuery(document).ready(function() 
{
	g_product_change_mode = true;
	InitMile();
	InitBugs();
	g_product_change_mode = false;
});

jQuery(window).bind('hashchange', function() 
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
