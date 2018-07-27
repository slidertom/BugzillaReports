function ajaxPostAbs(url, postdata, success, async_val, data_type)
{
	return jQuery.ajax({
		type: "POST",
		url: url,
		data: postdata,
		dataType: data_type,
		success: success,
		async: async_val,
		timeout: 4000
	});
}

function jsonPost(url, postdata, success)
{
	return ajaxPostAbs(url, postdata, success, true, "json");
}

function jsonPostSync(url, postdata, success)
{
	return ajaxPostAbs(url, postdata, success, false, "json");
}

function ajaxPostSync(url, postdata, success)
{
	return jQuery.ajax({
		type: "POST",
		url: url,
		data: postdata,
		success: success,
		async: false,
		timeout: 4000
	});
}

function ajaxPost(url, postdata, success)
{
	return jQuery.ajax({
		type: "POST",
		url: url,
		data: postdata,
		success: success,
		async: true,
		timeout: 4000
	});
}
