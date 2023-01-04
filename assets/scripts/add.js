/**
 * This script handles dynamic form validation and submission.
 */

// supportMeansSelect
const supportMeansSelect = document.getElementById('supportMeansSelect')
function handleSupportMeansChange() {
  let selected = this.options[this.selectedIndex].value
  this.dataset.selected = selected

  $('.perception-only, .inference-only, .testimony-only, .tarka-only').each(
    function () {
      $(this).hide()
      $(this)
        .children('input, textarea')
        .each(function () {
          $(this).prop('disabled', true)
        })
    },
  )
  let $displayEls = $()
  switch (selected) {
    case 'Inference':
      $displayEls = $('.inference-only')
      break
    case 'Perception':
      $displayEls = $('.perception-only')
      break
    case 'Testimony':
      $displayEls = $('.testimony-only')
      break
    case 'Tarka':
      $displayEls = $('.tarka-only')
      break
    default:
      break
  }
  $displayEls.each(function () {
    $(this).show()
    $(this)
      .children('input, textarea')
      .each(function () {
        $(this).prop('disabled', false)
      })
  })
}
supportMeansSelect?.addEventListener('change', handleSupportMeansChange)
// call the event listener now, in case the page autofilled
handleSupportMeansChange.apply(supportMeansSelect)

// ===========================================================================

// Copy subject input to output
const subjectInput = document.querySelector('#subjectInput')
function handleSubjectInput() {
  document.querySelectorAll('.subject-display').forEach((el) => {
    el.innerHTML = this.value
  })
}
subjectInput?.addEventListener('input', handleSubjectInput)
handleSubjectInput.apply(subjectInput)

// Copy target input to output
const targetInput = document.querySelector('#targetInput')
function handleTargetInput() {
  document.querySelectorAll('.target-display').forEach((el) => {
    el.innerHTML = this.value
  })
}
targetInput?.addEventListener('input', handleTargetInput)
handleTargetInput.apply(targetInput)

// Reason input
const reasonInput = document.querySelector('#reasonInput')
function handleReasonInput() {
  document.querySelectorAll('.reason-display').forEach((el) => {
    el.innerHTML = this.value
  })
}
reasonInput?.addEventListener('input', handleReasonInput)
handleReasonInput.apply(reasonInput)

// Reason input
const exampleInput = document.querySelector('#exampleInput')
function handleExampleInput() {
  document.querySelectorAll('.example-display').forEach((el) => {
    el.innerHTML = this.value
  })
}
exampleInput.addEventListener('input', handleExampleInput)
handleExampleInput.apply(exampleInput)

// ===========================================================================

function handleSubmit(event) {
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
        window.location.assign('topic.php?topic=' + topic.trim());
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
