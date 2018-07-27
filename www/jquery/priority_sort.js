$.tablesorter.addParser({
     id: "priority",
        is: function (s) {
            // return false so this parser is not auto detected 
            return /^[P?.]/.test(s);
        }, format: function (s) {
            return jQuery.tablesorter.formatFloat(s.replace(new RegExp(/[P]/g), ""));
        }, type: "numeric"
    });
	
  
    