function format_additional_date_time_ajax_params()
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
