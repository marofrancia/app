function downloadCSV() {
    
  var table = document.getElementById("myTable");


  var csvData = [];

  for (var i = 0; i < table.rows.length; i++) {
    var rowData = [];
    for (var j = 0; j < table.rows[i].cells.length; j++) {

      var cellValue = table.rows[i].cells[j].textContent;


      if (/^\d{2}-\d{2}$/.test(cellValue)) {
        var parts = cellValue.split("-");

    
        var formattedDate = new Date(2000, parts[0] - 1, parts[1]);
        var day = formattedDate.getDate().toString().padStart(2, "0");
        var month = (formattedDate.getMonth() + 1).toString().padStart(2, "0");
        var year = formattedDate.getFullYear().toString();

    
        cellValue = day + "/" + month + "/" + year;
      }


      rowData.push(cellValue);
    }

    
    csvData.push(rowData.join(","));
  }

  var csv = csvData.join("\n");

  
  var link = document.createElement("a");
  link.href = "data:text/csv;charset=utf-8," + encodeURI(csv);
  link.download = "table.csv";

  
  document.body.appendChild(link);
  link.click();
  document.body.removeChild(link);
}
