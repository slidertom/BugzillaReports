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

function SelectDeveloper(developer_id) 
{
	//alert(developer_id);
	if ( developer_id != -1 )
	{   
		Select_Value_Set("Developer", developer_id);
	}
}

function set_developer_hash(developer, filter)
{
	window.location.hash = developer+"?"+filter;
}

function json_key_string_to_array(values)
{
	var objs = jQuery.parseJSON(values);
	var data = new Array();
	for (var key in objs) {
		if (objs.hasOwnProperty(key)) {
			var element = new Array();
			element.push(key);
			element.push(objs[key]);
			data.push(element);
			//alert(key + " -> " + objs[key]);
		}
	}
	
	return data;
}

function draw_developer_pie_chart()
{
	try
	{
		var height = $("#bugs_pie_chart").parent().parent().height();		
		var values = $("#bugs_pie_data").html();
		
		//alert(values);
		$("#bugs_pie_chart").height(height);
		
		var data = json_key_string_to_array(values);
		
		var plot1 = jQuery.jqplot('bugs_pie_chart', [data],
		{
			  seriesColors: [ "#3366cc", "#990099", "#109618", "#dc3912", "#ff9900", "#958c12", "#953579", "#4b5de4", "#d8b83f", "#ff5800", "#0085cc"], 
			  seriesDefaults: {
				// Make this a pie chart.
				renderer: jQuery.jqplot.PieRenderer,
				rendererOptions: {
				  // Put data labels on the pie slices.
				  // By default, labels show the percentage of the slice.
				  showDataLabels: true,
				  sliceMargin: 1
				}
			  },
			  legend: { show:true, location: 'e' }
		});
		//  alert("ok");
	}
	catch (e)
	{
		alert(e.message);
	}
}

function draw_developer_pie_mile_chart()
{
	try
	{
		var height = $("#bugs_pie_chart").parent().parent().height();		
		var prod_values = $("#bugs_pie_data").html();
		var mile_values = $("#bugs_pie_mile_data").html();
		
		$("#bugs_pie_chart").height(height);
		
		var mile_data = json_key_string_to_array(mile_values);
		var prod_data = json_key_string_to_array(prod_values);
		
		var plot1 = jQuery.jqplot('bugs_pie_chart', [mile_data, prod_data],
		{
			  seriesColors: [ "#3366cc", "#990099", "#109618", "#dc3912", "#ff9900", "#958c12", "#953579", "#4b5de4", "#d8b83f", "#ff5800", "#0085cc", "#222222"], 
			  seriesDefaults: {
				// Make this a pie chart.
				renderer: jQuery.jqplot.DonutRenderer,
				rendererOptions: {
				  // Put data labels on the pie slices.
				  // By default, labels show the percentage of the slice.
				  showDataLabels: true,
				  sliceMargin: 3
				}
			  },
			  legend: { show:true, location: 'e' }
		});
		//  alert("ok");
	}
	catch (e)
	{
		alert(e.message);
	}
}

function LoadDeveloperBugs(developer, filter)
{
	var values  = "Developer="+developer+"&Filter="+filter;
	
	ajaxPostSync("ajax_developer_bugs.php?"+values, "", function(data) 
	{
		var open_hint = document.getElementById("OpenedHint");
		open_hint.innerHTML=data;	
		// This tells tablesorter to sort on third column in ascending order.
		$(".openTable").tablesorter({sortList: [[2,0], [1, 0]], widgets: ['zebra']}); 
		//$('td:nth-child(2),th:nth-child(2)').hide();
		
		$(".summary").tablesorter({widgets: ['zebra']}); 
		
		if ( document.getElementById("bugs_pie_chart") )
		{
			$(".openTable").find('td:nth-child(10),th:nth-child(10)').hide(); // start date	
			$(".openTable").find('td:nth-child(11),th:nth-child(11)').hide(); // end date	
		
			if ( document.getElementById("bugs_pie_mile_data") )
			{
				draw_developer_pie_mile_chart();
			}
			else
			{
				draw_developer_pie_chart();
			}
		}
	});
}

function LoadFiltersCombo(developer, filter)
{
	var values = "Developer="+developer+"&Filter="+filter;
	ajaxPostSync("ajax_developer_filters.php?"+values, "", function(data) 
	{
		var open_hint = document.getElementById("openedDevFilters");
		open_hint.innerHTML=data;	
	});
}

function HashGetDeveloperFilter()
{
	try
	{
		var hash = window.location.hash.substring(1);
		var pos = hash.indexOf("?");
		
		if ( pos == -1 )
		{
			return "";
		}
		
		var filter = hash.substring(pos);
		filter     = filter.substring(1); // drop ?
		return filter;
	}
	catch (e)
	{
		alert(e.message);
	}
	return "";
}

function Developer_Change(developer) 
{ 
	var open_hint = document.getElementById("OpenedHint");
	
	if (developer=="")
	{
		window.location.hash = "";
		open_hint.innerHTML="";
		return;
	} 
	
	// every developer can have different filters
	// but there are some same filters, so we try the filter in any case
	var filter = HashGetDeveloperFilter();
	LoadFiltersCombo(developer, filter);
	filter = $("#Developer_Filters_Combo").val(); // if filter load failed, take the default
	set_developer_hash(developer, filter);
	LoadDeveloperBugs(developer, filter);
 } 

function HashGetDeveloper()
{
	var hash = window.location.hash.substring(1);
	var pos  = hash.indexOf("?");
	
	if ( pos == -1 )
	{
		return hash;
	}
	
	var developer  = hash.substring(0, pos);
	return developer;
}

function InitDeveloper()
{
	try
	{
		var developer = HashGetDeveloper();
		
		if ( developer == "" )
		{
			var obj    = document.getElementById("Developer");
			var value  = obj.value;
			if ( value != "" )
			{
				Developer_Change(value);
			}
			
			return;
		}
		
		SelectDeveloper(developer);
		var filter = HashGetDeveloperFilter();
		LoadFiltersCombo(developer, filter);
	}
	catch (e)
	{
		alert(e.message);
	}
}

function InitBugs()
{
	var developer = HashGetDeveloper();
	
	if ( developer == "" )
	{
		return;
	}
	
	var filter = HashGetDeveloperFilter();
	LoadDeveloperBugs(developer, filter);
}

var g_developer_change_mode = false;

$('#Developer').change(function() 
{
	if ( g_developer_change_mode )
	{
		return;
	}
	
	var developer = $('#Developer').val();
	//alert(developer);
	g_developer_change_mode = true;
	Developer_Change(developer);
	g_developer_change_mode = false;
});

$("#Developer_Filters_Combo").live("change", function() 
{
	if ( g_developer_change_mode )
	{
		return;
	}
	
	g_developer_change_mode = true;
	var developer = $('#Developer').val();
	var filter = $("#Developer_Filters_Combo").val();
	set_developer_hash(developer, filter);
	LoadDeveloperBugs(developer, filter);
	g_developer_change_mode = false;
});

$(document).ready(function() 
{
	g_developer_change_mode = true;
	InitDeveloper();
	InitBugs(); 
	g_developer_change_mode = false;
});

$(window).bind('hashchange', function() 
{
	if ( g_developer_change_mode )
	{
		return;
	}
	
	g_developer_change_mode = true;
	InitDeveloper();
	InitBugs(); 
	g_developer_change_mode = false;
});