<?php

// There are three versions of this form used in the application
// add.php: The user inputs the topic, thesis, support unless set, in which case it's displayed and read only.
// Flagging: The user inputs a new thesis. The existing claim is hidden.
// Supporting: Inferences use a preset thesis and reason property flag.

function topicInput(string $topic = null, bool $hidden = false)
{
    if ($hidden) { ?>
    <input type="hidden" id="topicInput"
            name="topic"
            value="<?php echo $topic ?? ''; ?>">
    <?php } else { ?>
        <div>
            <label for="topic">Topic</label>
            <input type="text" id="topicInput"
                name="topic" placeholder="Enter topic name..." required
                pattern="^[\w\s]+$" value="<?php echo $topic ?? ''; ?>" <?php echo $topic ? "readonly" : ""; ?>>
        </div>
    <?php
    }
}

function subjectTargetInput(string $subject = null, string $targetP = null)
{
    if (isset($subject) && isset($targetP)) { ?>
        <input type="hidden" id="subjectInput" name="subject"
            value="<?php echo htmlspecialchars($subject); ?>">
        <input type="hidden" id="targetInput" name="targetP"
            value="<?php echo htmlspecialchars($targetP); ?>">
    <?php } else { ?>
        <div>
            <div class="d-inline-block">
                <label for="subject"><u>Subject</u></label>
                <input type="text" id="subjectInput" name="subject"
                    placeholder="Enter subject..." required></textarea>
            </div>
            <div class="d-inline-block">
                <label for="targetP"><u>Target Property</u></label>
                <input type="text" id="targetInput" name="targetP"
                    placeholder="Enter target property..." required>
            </div>
        </div>
    <?php } ?>
    <label for="thesisOutput">Thesis Statement</label>
        <output id="thesisOutput">
            <span data-target="#subjectInput" class="subject-display"></span>
            <span data-target="#targetInput" class="target-display"></span>.
    </output>
<?php }

function supportMeans()
{ ?>
    <div>
    <label for="supportMeansSelect">What is your Support Means?</label>
    <select name="union" id="supportMeansSelect" required>
        <option value="">Choose One</option>
        <option value="Inference">Inference</option>
        <option value="Testimony">Testimony</option>
        <option value="Perception">Perception</option>
        <option value="Tarka">Tarka</option>
    </select>
    <br>
    <p></p>
    <!-- elements with the .[testimony/inference/etc]-only class are shown conditionally -->
    <p class="inference-only">
        ⓘ <a href="userguide.php#Inference" target="_blank">Inference (<i>anumāna</i>)</a> asserts
        that the claim’s subject is known to possess a <b>reason property</b>
        (<dfn>hetu</dfn>) that is invariably present with the claim’s <b>target
            property</b>. This invariance must be demonstrated with an
        <b>example</b> that has the reason property and the target property.
    </p>

    <p class="perception-only">
        ⓘ <a href="userguide.php#Perception" target="_blank">Perception (<i>pratyakṣa</i>)</a>
        supports a claim through <b>sensory evidence</b>: that is, evidence which
        (1) directly represents the subject, (2) does not depend on language, (3) is
        inerrant, and (4) definitive. You must cite an audio/video source that
        supports the claim.
    <div id="reason" class="inference-only">
        <label for="reasonInput">Reason Property</label>
        <input type="text" class="reason" id="reasonInput" name="reason"
            placeholder="Enter reason property..." required>
        <p>ⓘ Your reason statement should answer "Why?". You might think of the
            reason as what comes after "because ...".</p>
        <p>
            <label for="reasonStatementOutput">Reason Statement (preview)</label>
            <output id="reasonStatementOutput">
                <span data-target="#subjectInput" class="subject-display"></span>
                <span data-target="#reasonInput" class="reason-display"></span>.
            </output>
        </p>
        <div id="example" class="inference-only d-inline-block">
            <label for="exampleInput">Example</label>
            <input type="text" id="exampleInput" name="example" required
                placeholder="Example">
        </div>
        <p>
            <label>Rule and Example Statement</label>
            <span>Whatever/Whomever</span>
            <span data-target="#reasonInput" class="reason-display">reason</span>,
            <span data-target="#targetInput" class="target-display">target</span>,
            as in the case of <span data-target="#exampleInput"
                class="example-display"></span>.
        </p>
    </div>
    <p class="testimony-only">
        ⓘ <a href="userguide.php#Testimony" target="_blank">Testimony (<i>śabda</i>)</a> supports a
        claim by citing a <b>trustworthy authority</b> (<i>āpta</i>): a source that
        knows something directly, desires to communicate it faithfully as it is
        known, and fulfills this aim. You must transcribe a textual or verbal source
        that supports the claim.</p>
    </p>
    <div id="transcription" class="testimony-only">
        <label for="transcriptionInput">Transcription</label>
        <textarea id="transcriptionInput" name="transcription" required
            placeholder="Transcription"
            style="width: 100%; height: 5rem;"></textarea>
    </div>
    <div id="citation" class="perception-only testimony-only d-inline-block">
        <label for="citationInput">Citation</label>
        <input type="text" id="citationInput" name="citation" required
            placeholder="Author, title, publication, and date of publication.">
    </div>
    <div id="url" class="testimony-only perception-only d-inline-block">
        <label for="urlInput">URL</label>
        <input type="url" id="urlInput" name="url"
            placeholder="https://example.com">
    </div>
    <div id="vidtimestamp" class="perception-only d-inline-block">
        <label for="vidtimestampInput">Video Timestamp</label>
        <input type="text" id="vidtimestampInput" name="vidtimestamp" required
            placeholder="Moment occurs at 05:10">
    </div>
    <!-- Tarka -->
    <p class="tarka-only">
        ⓘ <i>Tarka</i> (<b>philosophical argument</b>) allows for supplementary
        free-form discussion. To use a Tarka claim, submit this form, and use the
        Disqus comments feature on the resulting claim.</p>
    </div>
    <script>
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

    </script>
<?php }
