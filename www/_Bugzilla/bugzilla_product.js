/*
    Copyright by Tomas Rapkauskas bugzilla_base/license.txt 
    All rights reserved.
    To use this component please contact slidertom@gmail.com to obtain a license.
*/

jQuery.noConflict();

function update_milestone_dropdown() 
{ 
    let product = select_get_value("Product");
    if ( !product ) {
        return;
    }
    
    const urlParams = new URLSearchParams(window.location.search);
    let filter = urlParams.get('filter');
        filter = filter ? filter : "";
    let values = "Product="+product+"&Milestone="+filter;
    ajaxPostSync("milestones.php?"+values, "", function(data) {
        document.getElementById("milestoneHint").innerHTML=data;
        bind_milestone_change();
    });	
} 

function Milestone_ChangeWithProduct() 
{ 
    let product   = select_get_value("Product");
    let milestone = select_get_value("Milestone");
    
    create_gantt_chart(product, milestone);
    
    if (!milestone || !product) {
        document.getElementById("OpenedHint").innerHTML="";
        return;
    } 
    
    let values    = "Product="+product+"&Milestone="+milestone;
    let add_param = format_additional_date_time_ajax_params(); // year, month      
    if ( add_param ) {
        values += "&";
        values += add_param;
    }
    ajaxPostSync("ajax_product_bugs.php?"+values, "", function(data) 
    {
        document.getElementById("OpenedHint").innerHTML=data;
        jQuery(".openTable").tablesorter( { sortList: [[2,0], [1, 0]], widgets: ['zebra']}); 
        jQuery(".closeTable").tablesorter({ sortList: [[2,0], [1, 0]], widgets: ['zebra']}); 
        
        if ( !jQuery(".openTable").hasClass("show_milestone") ) {
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
        
        let filter_input = document.getElementById('product_filter');
        if ( filter_input ) {    
            filter_input.value = ''; // reset filter value   
            bind_key_up_event('#product_filter', function() {
                filter_table('open_bugs_table', this.value);
                filter_table('closed_bugs_table', this.value);
            });
        }
    });
    
    const release_hint = document.getElementById("ReleaseHint");
    if ( release_hint )
    {
        ajaxPost("ajax_get_product_release_date.php?"+values, "", function(data) 
        {
            //alert(data);
            data = jQuery.trim(data);
            
            if ( data.length > 0 ) {
                var release = "<b>Release date: ";
                release += data;
                release += "</b>";
                release_hint.innerHTML=release;
            }
            else {
                release_hint.innerHTML="";
            }
        });
    }
    
    bind_year_select_change();
    bind_month_select_change();
} 
  
var g_product_change_mode = false;

function get_bug_title(obj)
{   
    if ( obj ) {
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
    const gantt_ctrl = document.getElementById('product_gantt');
    if ( !gantt_ctrl ) {
        return;
    }

    const values = "Product="+product+"&Milestone="+milestone;
    jsonPost("ajax_json_get_product_bugs.php?"+values, "", function(gantt_data) 
    {
        if ( !gantt_data || gantt_data.length <= 0) {
            jQuery("#product_gantt").html("");
            return;
        }
        
        const gantt_chart = jQuery("#product_gantt")
        gantt_chart.gantt(
        {
            source:   gantt_data,
            scale:    "days",
            minScale: "days",
            maxScale: "months",
            onItemClick: function(data) {
                if ( jQuery("#bug_tab").val() == "true" ) {
                    open_in_new_tab(data);
                }
                else {
                    window.location.href = data;
                }
                //alert("Item clicked - show some details");
            },
            onAddClick: function(dt, rowId) {
                //alert("Empty space clicked - add an item!");
            },
            onRender: function() {
                gantt_tooltips.init_tooltips();
            }
        });  
    });
}

function history_update()
{
    let urlParams = new URLSearchParams(window.location.search);
    
    let product = select_get_value("Product");
    let filter  = select_get_value("Milestone");
    
    update_history_with_date_time(urlParams);
    
    urlParams.set('product', product);
    urlParams.set('filter',    filter);
    
    let page = window.location.pathname.split("/").pop();
    let new_url_params = urlParams.toString();
    history.pushState({id: 'product_page'}, '', page + '?' + new_url_params);
}

function refresh_product_bugs()
{
    if ( g_product_change_mode ) {
        return;
    }
    g_product_change_mode = true;
    Milestone_ChangeWithProduct();
    history_update();
    g_product_change_mode = false;
}

function bind_milestone_change() {
    bind_select_change('Milestone', refresh_product_bugs);
}

function bind_year_select_change() {
    bind_select_change("year_select", refresh_product_bugs);
}

function bind_month_select_change() {
    bind_select_change("month_select", refresh_product_bugs);
}

function refresh_milestones_and_product_bugs()
{
    if ( g_product_change_mode ) {
        return;
    }
    
    g_product_change_mode = true;
    update_milestone_dropdown();
    
    g_product_change_mode = false;
    
    refresh_product_bugs();
}

function init_product_bugs_by_url()
{
    const urlParams = new URLSearchParams(window.location.search);
    let product = urlParams.get('product');
    
    g_product_change_mode = true;
    if ( product ) {
        select_set_value("Product", product);	
    }
    update_milestone_dropdown();
    Milestone_ChangeWithProduct();
    
    g_product_change_mode = false;
}

jQuery(document).ready(function() 
{
    init_product_bugs_by_url();
    
    bind_select_change('Product', refresh_milestones_and_product_bugs);
  
    let bind_history_change = function()
    {
        window.addEventListener('popstate', function (event) {
            if (history.state && history.state.id === 'product_page') {		
                init_product_bugs_by_url();
            }
        }, false);
    };
    bind_history_change();
});