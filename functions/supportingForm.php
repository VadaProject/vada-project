<?php

namespace SupportingForm;

// There are three versions of this form used in the application
// add.php: The user inputs the topic, thesis, support unless set, in which case it's displayed and read only.
// Flagging: The user inputs a new thesis. The existing claim is hidden.
// Supporting: Inferences use a preset thesis and reason property flag.

/**
 * This class defines several templates for
 * These are used by the add, addflag, and addsupport pages.
 */
class SupportingForm
{
    /**
     * Echos the <input name="topic">.
     * @param $topic_escaped If defined, this is the input's value, and it is readonly.
     * @param $hidden If true, the input is invisible.
     */
    public function topicInput(string $topic = null, bool $hidden = false)
    {
        $topic_escaped = htmlspecialchars($topic);
        if ($hidden) {
            ?>
            <input type="hidden" id="topicInput"
                name="topic"
                value="<?php echo $topic_escaped ?? ''; ?>">
            <?php
        } else {
            ?>
            <div>
                <label for="topic">Topic</label>
                <input type="text" id="topicInput"
                    name="topic" placeholder="Enter topic name..." required
                    value="<?php echo $topic_escaped ?? ''; ?>" <?php echo $topic_escaped ? "readonly" : ""; ?>>
            </div>
            <?php
        }
    }

    /**
     * Echos an <input> for subject and targetP.
     *
     * @param $subject Preexisting subject value
     * @param $targetP Preexisting target property value
     */
    public function subjectTargetInput(string $subject = null, string $targetP = null)
    {
        if (isset($subject) && isset($targetP)) {
            ?>
            <input type="hidden" id="subjectInput" name="subject"
                value="<?php echo htmlspecialchars($subject); ?>">
            <input type="hidden" id="targetInput" name="targetP"
                value="<?php echo htmlspecialchars($targetP); ?>">
            <?php
        } else {
            ?>
            <div class="d-flex">
                <div class="flex-1">
                    <label for="subject"><u>Subject</u></label>
                    <textarea type="text" id="subjectInput" name="subject"
                        placeholder="Enter subject..." required rows="1"></textarea>
                </div>
                <div class="flex-1">
                    <label for="targetP"><u>Target Property</u></label>
                    <textarea type="text" id="targetInput" name="targetP"
                        placeholder="Enter target property..." required rows="1"></textarea>
                </div>
            </div>
            <?php
        }
        ?>
        <label for="thesisOutput">Thesis Statement</label>
            <output id="thesisOutput">
                <span data-target="#subjectInput" class="subject-display"></span>
                <span data-target="#targetInput" class="target-display"></span>.
        </output>
        <?php
    }

    /**
     * Echos the form for selecting a supportMeans.
     */
    public function supportMeansInput()
    {
        ?>
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
            <textarea type="text" class="reason" id="reasonInput" name="reason"
                placeholder="Enter reason property..." required rows="1"></textarea>
            <p>ⓘ Your reason statement should answer "Why?". You might think of the
                reason as what comes after "because ...".</p>
            <p>
                <label for="reasonStatementOutput">Reason Statement (preview)</label>
                <output id="reasonStatementOutput">
                    <span data-target="#subjectInput" class="subject-display"></span>
                    <span data-target="#reasonInput" class="reason-display"></span>.
                </output>
            </p>
            <div id="example" class="inference-only">
                <label for="exampleInput">Example</label>
                <textarea type="text" id="exampleInput" name="example" required
                    placeholder="Example" rows="1"></textarea>
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
        <div id="citation" class="perception-only testimony-only">
            <label for="citationInput">Citation</label>
            <textarea type="text" id="citationInput" name="citation" required
                placeholder="Author, title, publication, and date of publication."></textarea>
        </div>
        <div class="d-flex">
            <div id="url" class="testimony-only perception-only flex-1">
                <label for="urlInput">URL</label>
                <input type="url" id="urlInput" name="url"
                style="width: 100%; box-sizing: border-box;"
                    placeholder="https://example.com">
            </div>
            <div id="vidtimestamp" class="perception-only">
                <label for="vidtimestampInput">Video Timestamp</label>
                <input type="text" id="vidtimestampInput" name="vidtimestamp" required
                    placeholder="Moment occurs at 05:10">
            </div>
        </div>
        <!-- Tarka -->
        <p class="tarka-only">
            ⓘ <i>Tarka</i> (<b>philosophical argument</b>) allows for supplementary
            free-form discussion. To use a Tarka claim, submit this form, and use the
            Disqus comments feature on the resulting claim.</p>
        </div>
    <?php }
    /**
     * Echos a <select name="flagType">
     * Also generates tooltips.
     */
    public function flagTypeInput(string $supportMeans)
    {
        // TODO: these switches are really long.
        // Consider a data-driven approach.
        if ($supportMeans === 'Tarka') {
            return;
        }
        if ($supportMeans === 'NA') {
            $supportMeans = "Thesis";
        }
        ?>
        <div>
            <label for="flagType">
                <?php echo $supportMeans; ?> Flags
            </label>
            <select name="flagType" id="flagTypeSelect" required>
                <option value="" selected>Select...</option>
                <?php switch ($supportMeans) {
                    case 'Inference':
                        ?>
                        <option value="Unestablished Subject">Unestablished Subject</option>
                        <option value="Itself Unestablished">Itself Unestablished</option>
                        <option value="Hostile">Hostile</option>
                        <option value="Too Narrow">Too Narrow</option>
                        <option value="Too Broad (Counterexample)">Too Broad (Counterexample)
                        </option>
                        <option value="Too Broad (Unestablished Universal)">Too Broad
                            (Unestablished
                            Universal)</option>
                        <option value="Contrived Universal">Contrived Universal
                        </option>
                        <?php
                        break;
                    case 'Perception':
                        ?>
                        <option value="No Sense Object Contact">No Sense-Object
                            Contact</option>
                        <option value="Depends On Words">Depends on Words
                        </option>
                        <option value="Errant">Errant</option>
                        <option value="Ambiguous">Ambiguous</option>
                        <?php
                        break;
                    case 'Testimony':
                        ?>
                        <option value="No Direct Familiarity">No direct familiarity</option>
                        <option value="Errant Info">Errant information</option>
                        <option value="Ambiguous">Ambiguous</option>
                        <option value="Faithless">Faithless</option>
                        <option value="Misstatement">Misstatement</option>
                        <?php
                        break;
                    default: // Thesis flags
                        ?>
                        <option value="Thesis Rival">Has Rival</option>
                        <option value="Too Early">Too Early</option>
                        <option value="Too Late">Too Late</option>
                        <?php
                        break;
                } ?>
            </select>
            <!-- TODO: add tooltips here -->
            <div id="flagTooltips">
                <?php
                switch ($supportMeans) {
                    case 'Inference':
                        ?>
                        <p data-value="Unestablished Subject">ⓘ <i>Unestablished Subject</i>:
                    The subject is either ambiguous, in doubt, or non-existent.</p>
                        <p data-value="Itself Unestablished">ⓘ <i>Itself Unestablished</i>: The
                            reason property is not present, or is not known to be present, in
                            the subject.</p>
                        <p data-value="Hostile">ⓘ <i>Hostile</i>: The reason property
                            establishes that the target property is not present in the subject.
                        </p>
                        <p data-value="Too Narrow">ⓘ <i>Too Narrow</i>: ither (a) the example is
                            ambiguous, in doubt, or non-existent; or (b) the reason property is
                            not present, or is not known to be present, with the target property
                            in the example; or (c) the example is the same as, or is contained
                            within, the subject.</p>
                        <p data-value="Too Broad (Counterexample)">ⓘ <i>Too Broad
                                (Counterexample)</i>: The reason property is present without the
                            target property in a specifiable counterexample.</p>
                        <p data-value="Too Broad (Unestablished Universal)">ⓘ <i>Too Broad
                                (Unestablished Universal)</i>: There is no causal or conceptual
                            reason to assume a universal association between the reason property
                            and the target property; therefore, unspecifiable counterexamples
                            may exist.</p>
                        <p data-value="Contrived Universal">ⓘ <i>Contrived Universal</i>: The
                            universal association between the reason property and the target
                            property exists only because of the presence of an additional
                            property (upādhi).</p>
                        <?php
                        break;
                    case 'Perception':
                        ?>
                    <p data-value="No Sense Object Contact">ⓘ <i>No Sense-Object
                            Contact</i>:
                        The linked content does not present sights and/or sounds directly of
                        the subject of the claim. </p>
                    <p data-value="Depends On Words">ⓘ <i>Depends on Words</i>:
                        Either the linked content relies on words to support the claim, or
                        the claim depends on words or concepts that exceed the linked
                        content. </p>
                    <p data-value="Errant">ⓘ <i>Errant</i>:
                        The linked content presents perceptual evidence that is illusory or
                        false. </p>
                    <p data-value="Ambiguous">ⓘ <i>Ambiguous</i>:
                        The linked content does not clearly and unambiguously support the
                        claim. </p>
                        <?php
                        break;
                    case 'Testimony':
                        ?>
                <p data-value="No Direct Familiarity">ⓘ <i>No direct familiarity</i>:
                    The source either lacks direct familiarity, or is not known to have
                    direct familiarity, with the state of affairs that the claim is
                    about.
                </p>
                <p data-value="Errant Info">ⓘ <i>Errant information</i>: The information
                    the source conveys either is incorrect or is disputed by an equally
                    authoritative source. </p>
                <p data-value="Ambiguous">ⓘ <i>Ambiguous</i>: The transcribed words do
                    not unambiguously support the claim. </p>
                <p data-value="Faithless">ⓘ <i>Faithless</i>:

                    The source does not faithfully intend to communicate knowledge, but
                    is motivated, on this occasion, by another desire. </p>
                <p data-value="Misstatement">ⓘ <i>Misstatement</i>: The transcribed
                    words that support the claim are the result of a misstatement by the
                    source, or are an inaccurate transcription of the source. </p>
                        <?php
                        break;
                    default: // Thesis flags
                        ?>
                        <p data-value="Thesis Rival">ⓘ <i>Has Rival</i>: An unflagged rival
                    claim speaks or advocates for the antithesis.</p>
                <p data-value="Too Early">ⓘ <i>Too Early</i>: This claim is not
                    controversial; no party in the debate speaks or advocates for the
                    antithesis.</p>
                <p data-value="Too Late">ⓘ <i>Too Late</i>: This claim has already been
                    flagged or discredited.</p>
                        <?php
                        break;
                }
                ?>
            </div>
        </div>
        <?php
    }
}
