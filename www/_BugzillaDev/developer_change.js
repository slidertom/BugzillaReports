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

/*
function history_change()
{
	window.addEventListener('popstate', function (event) {
		let type;
		if (history.state && history.state.id === 'dev_page') {		
		
		}
	}, false);
}
*/

function format_additional_ajax_params()
{
	let add_param = "";
	let added = false;
	let year_select = document.getElementById("year_select");
	if ( year_select ) {
		let year = year_select.value
		add_param  = "year=";
        add_param += year;
		added = true;
	}
	else { // if no control try to get from url
		const urlParams = new URLSearchParams(window.location.search);
		let year = urlParams.get('year');
		if ( year )  {
			add_param  = "year=";
			add_param += year;
			added = true;
		}
	}
	
	let month_select = document.getElementById("month_select");
	if ( month_select ) {
		if ( added ) {
			add_param += "&";
		}
		let month = month_select.value
		add_param += "month=";
        add_param += month;
	}
	else { // if no control try to get from url
		const urlParams = new URLSearchParams(window.location.search);
		let month = urlParams.get('month');
		if ( month )  {
			if ( added ) {
				add_param += "&";
			}
			add_param += "month=";
			add_param += month;
		}
	}
	
	return add_param;
}
	
function refresh_developer_bugs()
{
	let add_param  = format_additional_ajax_params();            
    let developer  = $('#Developer').val();
    let filter     = $("#Developer_Filters_Combo").val();
	LoadDeveloperBugs(developer, filter, add_param);
	history_update();
}

function bind_select_change(select_id)
{
	let developer_select = document.getElementById(select_id);
    if ( !developer_select ) {
        return;
    }
    developer_select.addEventListener('change', (event) => {
        refresh_developer_bugs();
    });
}

function bind_year_select_change() {
	bind_select_change("year_select");
}

function bind_month_select_change() {
	bind_select_change("month_select");
}

$(document).ready(function() 
{
	let select_set_value = function(SelectName, Value)  {
		let obj = document.getElementById(SelectName);
		for (index = 0;  index < obj.length; ++index)  {
			if( obj[index].value == Value) {
				obj.selectedIndex = index;
				return true;
			}
		}
		return false;
	}

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
	
	bind_select_change('Developer_Filters_Combo');
	bind_select_change('developer');
});