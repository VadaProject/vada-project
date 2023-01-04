<!-- Topic input -->
<!-- If the URL has ?topic set, this input is readonly -->
<div>
    <label for="topic">Topic</label>
    <input type="text" id="topicInput" name="topic"
    placeholder="Enter topic name..." required
    pattern="^[\w\s]+$"
    value="<?php echo $_GET['topic'] ?? ''; ?>"
    <?php if (isset($_GET['topic'])) { ?>readonly<?php } ?>
    >
</div>
<!-- Subject and target property input -->
<div>
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
    <p></p>
    <label for="thesisOutput">Thesis Statement (preview)</label>
    <output id="thesisOutput">
        <span class="subject-display" id="subjectOutput" ></span>
        <span class="target-display" id="targetOutput"></span>
    </output>
</div>
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
    ⓘ <a href="userguide.php#Inference">Inference (<i>anumāna</i>)</a> asserts that the claim’s subject is known to possess a <b>reason property</b> (<dfn>hetu</dfn>) that is invariably present with the claim’s <b>target property</b>. This invariance must be demonstrated with an <b>example</b>.</p>

<p class="perception-only">
    ⓘ <a href="userguide.php#Perception">Perception (<i>pratyakṣa</i>)</a> supports a claim through <b>sensory evidence</b>: that is, evidence which (1) directly represents the subject, (2) does not depend on language, (3) is inerrant, and (4) definitive. You must cite an audio/video source that supports the claim.
<div id="reason" class="inference-only">
    <label for="reasonInput">Reason Property</label>
    <input type="text" class="reason" id="reasonInput" name="reason"
    placeholder="Enter reason property..." required>
    <p>ⓘ Your reason statement should answer "Why?". You might think of the reason as what comes after "because ...".</p>

<p>
    <label for="reasonStatementOutput">Reason Statement (preview)</label>
    <output id="reasonStatementOutput">
        <span id="subjectOutput2" class="subject-display"></span>
        <span id="reasonOutput" class="reason-display"></span>.
    </output>
</p>
<div id="example" class="inference-only d-inline-block">
    <label for="exampleInput">Example</label>
    <input type="text" id="exampleInput" name="example"
    required placeholder="Example">
</div>
<p>
    <label>Rule and Example Statement</label>
    <span>Whatever/Whomever</span>
    <span class="reason-display" id="reasonOutput2">reason</span>,
    <span class="target-display" id="targetOutput2">target</span>,
    as in the case of <span class="example-display"></span>.
</p>
</div>
<p class="testimony-only">
    ⓘ <a href="userguide.php#Testimony">Testimony (<i>śabda</i>)</a> supports a claim by citing a <b>trustworthy authority</b> (<i>āpta</i>): a source that knows something directly, desires to communicate it faithfully as it is known, and fulfills this aim. You must transcribe a textual or verbal source that supports the claim.</p></p>
<div id="transcription" class="testimony-only">
    <label for="transcriptionInput">Transcription</label>
    <textarea id="transcriptionInput" name="transcription" required
    placeholder="Transcription"
    ></textarea>
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
    <input type="text" id="vidtimestampInput" name="vidtimestamp"
    required placeholder="Moment occurs at 05:10">
</div>
<!-- Tarka -->
<p class="tarka-only">
    ⓘ <i>Tarka</i> (<b>philosophical argument</b>) allows for supplementary free-form discussion. To use a Tarka claim, submit this form, and use the Disqus comments feature on the resulting claim.</p>

<script src="assets/scripts/add.js"></script>
