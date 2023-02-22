/**
 * This script handles dynamic form validation and submission.
 */

/**
 * Handles the form submit event
 *
 * @param event {JQuery.SubmitEvent}
 */
function handleSubmit(event) {
  // use HTML5 form validation but otherwise override submit action
  /** @type HTMLInputElement | null */
  const topicInput = document.querySelector("#topicInput");
  const topic = topicInput?.value ?? "";
  const destinationURL = 'topic.php?topic=' + topic.trim();
  const actionURL = $(this).attr('action');
  if (this.checkValidity()) {
    if (!actionURL) {
      console.error(`Form ${this} has no [action] parameter set`);
      return;
    }
    const data = $(this).find(":input").serializeArray();
    $.post(
      actionURL,
      data,
      function success(info) {
        $('#result').html(info);
        alert("Submitted!");
        console.debug(info);
        window.parent.location.href = destinationURL;
      },
    );
    $("#submit").prop('disabled', true);
    setTimeout(() => {
      $("#submit").prop('disabled', false);
    }, 1000);
  }
  event?.preventDefault();
}

$('#myForm').on("submit", handleSubmit);

// supportMeansSelect
const supportMeansSelect = document.getElementById('supportMeansSelect');
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
  if (targetSelector === undefined) {
    console.error(`Element `, el, ` has data-target defined but no value.`);
    return;
  }
  const $target = $(targetSelector);
  const value = $target.val()?.toString() ?? "";
  if ($target.length == 0) {
    console.error(`Query $('${targetSelector}') returned no results.`)
    return;
  }
  $target.on('input', () => {
    const value = $target.val()?.toString() ?? "";
    $(el).text(value);
  })
  $(el).text(value);
})

/* Flagging Tooltips */

if ($("#flagTypeSelect").length > 0) {
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

}
