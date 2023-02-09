/**
 * This script handles dynamic form validation and submission.
 */


// ===========================================================================

function handleSubmit(event) {

  console.log("Hi!");
  // use HTML5 form validation but otherwise
  const topic = $("#topicInput").val();
  if (this.checkValidity()) {
    const url = $(this).attr('action');
    const data = $(this).find(":input").serializeArray();
    $.post(
      url,
      data,
      function success(info) {
        $('#result').html(info);
        alert("Submitted!");
        console.debug(info);
        window.parent.location.href = 'topic.php?topic=' + topic.trim();
      },
    );
    $("#submit").prop('disabled', true);
    setTimeout(() => {
      $("#submit").prop('enabled', true);
    }, 1000);
  }
  event?.preventDefault();
}

$('#myForm').submit(handleSubmit);
