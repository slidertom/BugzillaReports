/*
    Copyright by Tomas Rapkauskas bugzilla_base/license.txt 
    All rights reserved.
    To use this component please contact slidertom@gmail.com to obtain a license.
*/

function json_key_string_to_array(values)
{
    let objs = jQuery.parseJSON(values);
    let data = new Array();
    for (let key in objs) {
        if (objs.hasOwnProperty(key)) {
            let element = new Array();
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
    try {
        let height = $("#bugs_pie_chart").parent().parent().height();       
        let values = $("#bugs_pie_data").html();
        //alert(values);
        if ( height < 150 ) {
            height = 150;
        }
        $("#bugs_pie_chart").height(height);
        
        var data = json_key_string_to_array(values);
        var plot1 = jQuery.jqplot('bugs_pie_chart', [data],
        {
              seriesColors: [ "#3366cc", "#990099", "#109618", "#dc3912", "#ff9900", "#958c12", "#953579", "#4b5de4", "#d8b83f", "#ff5800", "#0085cc"], 
              seriesDefaults: {
                renderer: jQuery.jqplot.PieRenderer, // Make this a pie chart.
                rendererOptions: {
                  // Put data labels on the pie slices.
                  // By default, labels show the percentage of the slice.
                  showDataLabels: true,
                  sliceMargin: 1
                }
              },
              legend: { show:true, location: 'e' }
        });
    }
    catch (e) {
        alert(e.message);
    }
}

function draw_developer_pie_mile_chart()
{
    try {
        const prod_values = $("#bugs_pie_data").html();
        const mile_values = $("#bugs_pie_mile_data").html();
        const mile_data = json_key_string_to_array(mile_values);
        const prod_data = json_key_string_to_array(prod_values);
        
        if ( mile_data.length <= 1) {
            draw_developer_pie_chart(); // switch into pie chart as only one milestone
            return;
        }
        
        let height = $("#bugs_pie_chart").parent().parent().height();       
        if ( height < 150 ) {
            height = 150;
        }

        $("#bugs_pie_chart").height(height);
        
        var plot1 = jQuery.jqplot('bugs_pie_chart', [mile_data, prod_data],
        {
              seriesColors: [ "#3366cc", "#990099", "#109618", "#dc3912", "#ff9900", "#958c12", "#953579", "#4b5de4", "#d8b83f", "#ff5800", "#0085cc", "#222222"], 
              seriesDefaults: {
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
    catch (e) {
        alert(e.message);
    }
}

function load_developer_bugs(developer, filter, add_param=null)
{
    let values = "developer="+developer+"&Filter="+filter;
    if ( add_param ) {
        values += "&";
        values += add_param;
    }
    
    ajaxPostSync("ajax_developer_bugs.php?"+values, "", function(data) 
    {
        const open_hint = document.getElementById("OpenedHint");
        open_hint.innerHTML=data;   

        // This tells tablesorter to sort on third column in ascending order.
        const bugs_tables = $(".openTable");
        bugs_tables.tablesorter({sortList: [[2,0], [1, 0]], widgets: ['zebra']}); 
        //$('td:nth-child(2),th:nth-child(2)').hide();
        $(".summary").tablesorter({widgets: ['zebra']}); 
        bugs_tables.find('td:nth-child(10),th:nth-child(10)').hide(); // start date 
        bugs_tables.find('td:nth-child(11),th:nth-child(11)').hide(); // end date   
        
        if ( document.getElementById("bugs_pie_chart") )
        {
            if ( document.getElementById("bugs_pie_mile_data") ) {
                draw_developer_pie_mile_chart();
            }
            else {
                draw_developer_pie_chart();
            }
        }
        
        bind_year_select_change();  // currently it's additional item every time is regenerated
        bind_month_select_change(); // currently it's additional item every time is regenerated
        bind_week_select_change();

        const filter_input = document.getElementById('developer_filter');
        if ( filter_input ) {    
            filter_input.value = ''; // reset filter value   
            bind_key_up_event('#developer_filter', function() {
                filter_table('developer_table',   this.value);
            });
        }
    });
}

function history_update()
{
    let urlParams = new URLSearchParams(window.location.search);
    
    let developer = select_get_value('Developer');
    let filter    = select_get_value("Developer_Filters_Combo");
    
    update_history_with_date_time(urlParams);
    
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
    const add_param  = format_additional_date_time_ajax_params();            
    const developer  = select_get_value('Developer');
    const filter     = select_get_value("Developer_Filters_Combo");
    load_developer_bugs(developer, filter, add_param);
    history_update();
    g_internal_change = false;
}

function bind_year_select_change() {
    bind_select_change("year_select", refresh_developer_bugs);
}

function bind_month_select_change() {
    bind_select_change("month_select", refresh_developer_bugs);
}

function bind_week_select_change() {
    bind_select_change("week_select", refresh_developer_bugs);
}

$(document).ready(function() 
{
    let refresh_developer_bugs_and_filters = function()
    {
        const developer = select_get_value('Developer');
        const filter    = select_get_value('Developer_Filters_Combo');
        const values = "developer="+developer+"&Filter="+filter;
        ajaxPost("ajax_developer_filters.php?"+values, "", function(data) 
        {
            let filter_ctrl = document.getElementById("openedDevFilters");
            filter_ctrl.innerHTML=data;   
            refresh_developer_bugs();
            bind_select_change('Developer_Filters_Combo', refresh_developer_bugs);
        });
    };

    refresh_developer_bugs();
    
    bind_select_change('Developer_Filters_Combo', refresh_developer_bugs);
    bind_select_change('Developer', refresh_developer_bugs_and_filters);
    
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
                load_developer_bugs(developer, filter, add_param);
            }
        }, false);
    };
    bind_history_change();
});