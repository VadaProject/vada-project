<?php
require_once 'functions/supportingForm.php';
require_once 'functions/Database.php';
use Database\Database;

function flagTypeInput(string $supportMeans)
{
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
            <p data-value="Thesis Rival">ⓘ <i>Has Rival</i>: An unflagged rival
                claim speaks or advocates for the antithesis.</p>
            <p data-value="Too Early">ⓘ <i>Too Early</i>: This claim is not
                controversial; no party in the debate speaks or advocates for the
                antithesis.</p>
            <p data-value="Too Late">ⓘ <i>Too Late</i>: This claim has already been
                flagged or discredited.</p>
        </div>
        <script>
            function showToolTip(value) {
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
                showToolTip(selected);
            };
            $("#flagTypeSelect").on("change", handleFlagTypeSelectChange)
            handleFlagTypeSelectChange.apply($("#flagTypeSelect")[0]);
        </script>
    </div>
<?php
}
?>
<!DOCTYPE html>
<html>

<head>
    <?php require 'includes/head-tag-contents.php'; ?>
    <style>
        body {
            background-color: white;
            padding: 0;
            padding-bottom: 1rem;
        }
    </style>
</head>

<body lang="en-US">
    <?php
    $claim_id = $_GET["id"];
    if (!isset($claim_id)) {
        echo "<h2>Error: no claim ID given.</h2>";
        return;
    }
    $claim = Database::getClaim($claim_id);
    if (is_null($claim)) {
        echo "<h2>Error: a claim with the ID #$claim_id does not exist.</h2>";
        return;
    }
    ?>
    <h2>Flagging claim #
        <?php echo $claim_id; ?>
    </h2>
    <form method="POST" id="myForm" action="insert.php">
        <input type="hidden" name="FOS" value="flagging">
        <input type="hidden" name="claimIDFlaggedINSERT"
            value="<?php echo $claim_id; ?>">
        <h3>What are you flagging it for?</h3>
        <?php topicInput($claim->topic, /* hidden */true); ?>
        <?php flagTypeInput($claim->supportMeans); ?>
        <h3>Enter your new thesis.</h3>
        <?php subjectTargetInput(); ?>
        <?php supportMeans(); ?>
        <div>
            <button type="submit" id="submit">Submit</button>
        </div>
        <script src="assets/scripts/add.js"></script>
    </form>
</body>
