$(function() {
	   
jQuery(document).on("mouseover mouseout", "table.tablesorter tbody tr", function(event)  {
	if (event.type == "mouseover")  { 
		jQuery(this).children().addClass("hover");
	    
	}
    else  { 
		jQuery(this).children().removeClass("hover"); 
	};
});

});
