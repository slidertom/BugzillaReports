$(function() {
	   
jQuery("table.tablesorter tbody tr").live("mouseover mouseout", function (event) 
{
	if (event.type == "mouseover") 
	{ 
		jQuery(this).children().addClass("hover");
	    
	}
    else 
	{ 
		jQuery(this).children().removeClass("hover"); 
	};
});

});
