$(document).ready(function(){
  // set page length to 5
  $('#dataTable').DataTable({
    "pageLength": 5,
    // sort the table by the first column
    "order": [[ 0, "desc" ]],
    // remove the change length option
    "lengthChange": false,
  });
  // if first column name is "id" then hide it
    if ($('#dataTable th:first').text() === "ID") {
        $('#dataTable').DataTable().column(0).visible(false);
    }
  $('#dataTable2').DataTable({
    "pageLength": 5,
    // sort the table by the first column
    "order": [[ 0, "desc" ]],
    // remove the change length option
    "lengthChange": false,
  });
    // if first column name is "id" then hide it
    if ($('#dataTable2 th:first').text() === "ID") {
        $('#dataTable2').DataTable().column(0).visible(false);
    }
});
