function loadData(id) {
  console.log(id);
  $.ajax({
      url: "adnanindex.php",
      method: "POST",
      data: {
          get_data: 1,
          id: id
      },
      success: function(response) {
          response = JSON.parse(response);
          console.log(response);
          var html = "";

          // Displaying city
          html += "<div class='row'>";
          html += "<div class='col-md-6'>SupportMeans</div>";
          html += "<div class='col-md-6'>" + response
              .supportMeans + "</div>";
          html += "</div>";

          // And now assign this HTML layout in pop-up body
          $("#modal-body").html(html);

          $("#myModal").modal();
      }
  });
}
