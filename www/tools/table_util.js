function filter_table(table_id, filter_value) 
{
    let filter = filter_value.toUpperCase();
    let table = document.getElementById(table_id);
    if (!table) {
        return;
    }

    if (table.length == 0) {
        return;
    }

    let tr = table.getElementsByTagName("tr");
    for (let i = 0; i < tr.length; ++i) {
        let td = tr[i].getElementsByTagName("td");
        for (let j = 0; j < td.length; ++j) {
            let cell = tr[i].getElementsByTagName("td")[j];
            if (cell) {
                let txtValue = cell.textContent || cell.innerText;
                if (txtValue.toUpperCase().indexOf(filter) > -1) {
                    tr[i].style.display = "";
                    break;
                } 
                else {
                    tr[i].style.display = "none";
                }
            }       
        }
    }
}

function bind_key_up_event(elem_str, callback_func)
{
    jQuery(document).off('keyup', elem_str);
    jQuery(document).on('keyup',  elem_str, callback_func);
}