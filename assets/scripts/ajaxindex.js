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
          //                html += "<div class='row'>";
          //                   html += "<div class='col-md-6'></div>";
          html += "<div class='col-md-6'><p style=\"color:black\">" + response.ts +
              "<BR> ClaimID: #" + response.claimID + "</div><BR><p style=\"color:black\">";

          if (response.supportMeans == 'NA') {
              html += "<BR> Claim: " + response.subject + " " + response.targetP;
          }

          if (response.supportMeans == 'Testimony') {
              html += "Transcription: " + response.transcription +
                  " <BR><br><br> Citation: " + response.citation;
          }

          if (response.supportMeans == 'Perception') {
              html += " <BR> URL: " + response.URL + " <BR> Timestamp: " + response
                  .timestamp + " <BR> Citation: " + response.citation;
          }

          if (response.supportMeans == 'Inference') {
              html += " <BR> Reason: " + response.subject + " " + response.reason +
                  "<BR> Rule & Example: Whatever/Whomever " + response.reason + ', ' +
                  response.targetP + " as in the case of " + response.example;

          }
          if (response.supportMeans == 'Tarka') {
              html +=
                  "Tarka is an element of conversation used to discuss errors in debate form and communication with moderators.";

          }

          html += " <BR> <div class = \"modal-content-a\"> <a href=\"details.php?id=" +
              response.claimID + "\" class = \"button\">  DETAILS PAGE </a> </div></div>";

          // And now assign this HTML layout in pop-up body
          $("#modal-body").html(html);

          $("#myModal").modal();

      }
  });
}
