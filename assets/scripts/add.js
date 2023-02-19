/**
 * This script handles dynamic form validation and submission.
 */

/**
 * Handles the form submit event
 * @param {SubmitEvent} event
 */
function handleSubmit(event) {
  // use HTML5 form validation but otherwise override submit action
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
  let $displayEls = $();
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

// Generalized copying inputs to outputs
$("[data-target]").each((i, el) => {
  const targetSelector = $(el).attr("data-target");
  const $target = $(targetSelector);
  if ($target.length == 0) {
    console.error(`Query $('${targetSelector}') returned no results.`)
    return;
  }
  $target.on('input', () => {
    $(el).text($target.val());
  })
  $(el).text($target.val());
})

/* Flagging Tooltips */


function showFlagToolTip(value) {
  $("#flagTooltips [data-value]").each((i, el) => {
    if ($(el).attr("data-value") === value) {
      $(el).show();
    } else {
      $(el).hide();
    }
  })
}
function handleFlagTypeSelectChange() {
  let selected = this.options[this.selectedIndex].value
  showFlagToolTip(selected);
};

$("#flagTypeSelect").on("change", handleFlagTypeSelectChange)
handleFlagTypeSelectChange.apply($("#flagTypeSelect")[0]);
