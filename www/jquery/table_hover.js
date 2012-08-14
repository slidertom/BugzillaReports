$(function() {
	   
$("table.tablesorter tbody tr").live("mouseover mouseout", function (event) 
{
	if (event.type == "mouseover") 
	{ 
		$(this).children().addClass("hover");
	    
	}
    else 
	{ 
		$(this).children().removeClass("hover"); 
	};
});

});
