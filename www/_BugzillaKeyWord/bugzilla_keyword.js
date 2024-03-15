/*
    Copyright by Tomas Rapkauskas bugzilla_base/license.txt 
    All rights reserved.
    To use this component please contact slidertom@gmail.com to obtain a license.
*/

jQuery.noConflict();

function keyword_change() 
{ 
	let keyword = select_get_value("Keyword");
    create_gantt_chart(keyword);
    
    if (!keyword) {
        document.getElementById("OpenedHint").innerHTML="";
        return;
    } 
    
    let values    = "Keyword="+keyword;
    let add_param = format_additional_date_time_ajax_params(); // year, month      
    if ( add_param ) {
        values += "&";
        values += add_param;
    }
    ajaxPostSync("ajax_keyword_bugs.php?"+values, "", function(data) 
    {
        document.getElementById("OpenedHint").innerHTML=data;
        let open_table  = jQuery(".openTable");
        let close_table = jQuery(".closeTable");
        open_table.tablesorter( { sortList: [[2,0], [1, 0]], widgets: ['zebra']}); 
        close_table.tablesorter({ sortList: [[2,0], [1, 0]], widgets: ['zebra']}); 
        
        if ( !open_table.hasClass("show_milestone") ) {
            open_table.find('td:nth-child(9),th:nth-child(9)').hide();  // TargetM
        }
        open_table.find('td:nth-child(8),th:nth-child(8)').hide();  // Product  
        open_table.find('td:nth-child(10),th:nth-child(10)').hide(); // start date    
        open_table.find('td:nth-child(11),th:nth-child(11)').hide(); // end date  
        
        close_table.find('td:nth-child(9),th:nth-child(9)').hide(); // TargetM
        close_table.find('td:nth-child(8),th:nth-child(8)').hide(); // Product
        close_table.find('td:nth-child(6),th:nth-child(6)').hide(); // Left   
        close_table.find('td:nth-child(10),th:nth-child(10)').hide(); // start date   
        close_table.find('td:nth-child(11),th:nth-child(11)').hide(); // end date 
     
        // reset filter value
        document.getElementById('keyword_bugs_filter').value = '';     
        bind_key_up_event('#keyword_bugs_filter', function() {
            filter_table('open_bugs_table', this.value);
        });
    });
    
    var release_hint = document.getElementById("ReleaseHint");
    if ( release_hint )
    {
        ajaxPost("ajax_get_keyword_release_date.php?"+values, "", function(data)  {
            data = jQuery.trim(data);
            if ( data.length > 0 ) {
                let release = "<b>Release date: ";
                release += data;
                release += "</b>";
                release_hint.innerHTML=release;
            }
            else {
                release_hint.innerHTML="";
            }
        });
    }
} 
  
var g_keyword_change_mode = false;

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

function create_gantt_chart(keyword)
{
	let gantt_ctrl = document.getElementById('product_gantt');
	if ( !gantt_ctrl ) {
		return;
	}
	
    try
    {
        let values  = "Keyword="+keyword;
        jsonPost("ajax_json_get_keyword_bugs.php?"+values, "", function(gantt_data) {
            if ( !gantt_data || gantt_data.length <= 0) {
                jQuery("#product_gantt").html("");
                return;
            }
			
            jQuery("#product_gantt").gantt(
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
    catch (e) {
        //alert(e.message);
    }
}

function history_update()
{
    let urlParams = new URLSearchParams(window.location.search);
    
    let keyword = select_get_value("Keyword");
    
    update_history_with_date_time(urlParams);
    
    urlParams.set('keyword', keyword);
    
    let page = window.location.pathname.split("/").pop();
    let new_url_params = urlParams.toString();
    history.pushState({id: 'keyword_page'}, '', page + '?' + new_url_params);
}

function refresh_keyword_bugs()
{
    if ( g_keyword_change_mode ) {
        return;
    }
    g_keyword_change_mode = true;
	keyword_change();
    history_update();
    g_keyword_change_mode = false;
}

function init_keyword_bugs_by_url()
{
	const urlParams = new URLSearchParams(window.location.search);
	let keyword = urlParams.get('keyword');
	
    g_keyword_change_mode = true;
    if ( keyword ) {
		select_set_value("Keyword", keyword);	
	}
	
    keyword_change();
    
    g_keyword_change_mode = false;
}

jQuery(document).ready(function() 
{
    init_keyword_bugs_by_url();
	
    bind_select_change('Keyword', refresh_keyword_bugs);
  
	let bind_history_change = function()
	{
		window.addEventListener('popstate', function (event) {
			if (history.state && history.state.id === 'keyword_page') {		
				init_keyword_bugs_by_url();
			}
		}, false);
	};
	bind_history_change();
});

