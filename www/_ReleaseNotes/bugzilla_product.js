/*
    Copyright by Tomas Rapkauskas bugzilla_base/license.txt 
    All rights reserved.
    To use this component please contact slidertom@gmail.com to obtain a license.
*/

jQuery.noConflict();

function update_milestone_dropdown() 
{ 
    const product = select_get_value("Product");
    if ( !product ) {
        return;
    }
    
    const urlParams = new URLSearchParams(window.location.search);
    let filter = urlParams.get('filter');
        filter = filter ? filter : "";
    const values = "Product="+product+"&Milestone="+filter;
    ajaxPostSync("ajax_get_milestones_combo.php?"+values, "", function(data) {
        document.getElementById("milestoneHint").innerHTML=data;
        bind_milestone_change();
    });	
} 

function Milestone_ChangeWithProduct() 
{ 
    const product   = select_get_value("Product");
    const milestone = select_get_value("Milestone");
    
    if (!milestone || !product) {
        document.getElementById("OpenedHint").innerHTML="";
        return;
    } 
    
    const values = "Product="+product+"&Milestone="+milestone;
    
    ajaxPostSync("ajax_product_bugs.php?"+values, "", function(data) 
    {
        document.getElementById("OpenedHint").innerHTML = data;

        jQuery(".openTable").tablesorter( { sortList: [[2,0], [1, 0]], widgets: ['zebra']}); 
        
        const filter_input = document.getElementById('product_filter');
        if ( filter_input ) {    
            filter_input.value = ''; // reset filter value   
            bind_key_up_event('#product_filter', function() {
                filter_table('open_bugs_table', this.value);
            });
        }
    });
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

function history_update()
{
    const urlParams = new URLSearchParams(window.location.search);
    
    let product = select_get_value("Product");
    let filter  = select_get_value("Milestone");
    
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