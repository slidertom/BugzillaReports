function filter_table(table_id, filter_value) {
  var filter = filter_value.toUpperCase();
  var table = document.getElementById(table_id);
  var tr = table.getElementsByTagName("tr");
  for (var i = 0; i < tr.length; ++i) {
    var td = tr[i].getElementsByTagName("td");
    for (var j = 0; j < td.length; ++j) {
        var cell = tr[i].getElementsByTagName("td")[j];
        if (cell) {
          var txtValue = cell.textContent || cell.innerText;
          if (txtValue.toUpperCase().indexOf(filter) > -1) {
            tr[i].style.display = "";
            break;
          } else {
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