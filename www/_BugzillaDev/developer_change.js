/*
    Copyright by Tomas Rapkauskas bugzilla_base/license.txt 
    All rights reserved.
    To use this component please contact slidertom@gmail.com to obtain a license.
*/

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

function LoadDeveloperBugs(developer, filter, add_param=null)
{
    let values = "Developer="+developer+"&Filter="+filter;
    if ( add_param ) {
        values += "&";
        values += add_param;
    }
    
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
        
            if ( document.getElementById("bugs_pie_mile_data") ) {
                draw_developer_pie_mile_chart();
            }
            else {
                draw_developer_pie_chart();
            }
        }
		
		bind_year_select_change();  // currently it's additional item every time is regenerated
		bind_month_select_change(); // currently it's additional item every time is regenerated
    });
}

function history_update()
{
	let urlParams = new URLSearchParams(window.location.search);
	
	let developer = $('#Developer').val();
	let filter    = $("#Developer_Filters_Combo").val();
	
	let year_select = document.getElementById("year_select");
	if ( year_select ) {
		let year = year_select.value
		urlParams.set('year', year);
	}
	
	let month_select = document.getElementById("month_select");
	if ( month_select ) {
		let month = month_select.value
		urlParams.set('month', month);
	}
	
	urlParams.set('developer', developer);
	urlParams.set('filter',    filter);
	
	let page = window.location.pathname.split("/").pop();
	let new_url_params = urlParams.toString();
	history.pushState({id: 'dev_page'}, '', page + '?' + new_url_params);
}

var g_internal_change = false;	
function refresh_developer_bugs()
{
	if ( g_internal_change ) {
		return;
	}
	
	g_internal_change = true;
	let add_param  = format_additional_date_time_ajax_params();            
    let developer  = $('#Developer').val();
    let filter     = $("#Developer_Filters_Combo").val();
	LoadDeveloperBugs(developer, filter, add_param);
	history_update();
	g_internal_change = false;
}

function bind_year_select_change() {
	bind_select_change("year_select", refresh_developer_bugs);
}

function bind_month_select_change() {
	bind_select_change("month_select", refresh_developer_bugs);
}

$(document).ready(function() 
{
	const urlParams = new URLSearchParams(window.location.search);
	
	const developer = urlParams.get('developer');
	if ( developer ) {
		select_set_value("Developer", developer);
	}
	
	const filter = urlParams.get('filter');
	if ( filter ) {
		select_set_value("Developer_Filters_Combo", filter);
	}
	
    refresh_developer_bugs();
	
	bind_select_change('Developer_Filters_Combo', refresh_developer_bugs);
	bind_select_change('Developer', refresh_developer_bugs);
	
	let bind_history_change = function()
	{
		window.addEventListener('popstate', function (event) {
			if (history.state && history.state.id === 'dev_page') {		
				const urlParams = new URLSearchParams(window.location.search);
				const developer = urlParams.get('developer');
				const filter    = urlParams.get('filter');
				const year      = urlParams.get('year');
				const month     = urlParams.get('month');
				let add_param = "";
				if ( year ) {
					add_param += "year="+year; 
				}
				if ( month && year ) {
					add_param += "&";
				}
				if ( month ) {
					add_param += "month="+month;
				}
				LoadDeveloperBugs(developer, filter, add_param);
			}
		}, false);
	};
	bind_history_change();
});