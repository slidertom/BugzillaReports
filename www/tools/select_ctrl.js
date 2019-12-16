function bind_select_change(select_id, callback)
{
	let select_ctrl = document.getElementById(select_id);
    if ( !select_ctrl ) {
        return;
    }
    select_ctrl.addEventListener('change', (event) => {
        callback();
    });
}

function select_set_value(SelectName, Value)  
{
	let obj = document.getElementById(SelectName);
	for (index = 0;  index < obj.length; ++index)  {
		if( obj[index].value == Value) {
			obj.selectedIndex = index;
			return true;
		}
	}
	return false;
}
