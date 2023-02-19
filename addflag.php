<?php
require_once 'functions/supportingForm.php';
require_once 'functions/Database.php';
use Database\Database;

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
    $supportingForm = new SupportingForm\SupportingForm();
    ?>
    <h2>Flagging claim #
        <?php echo $claim_id; ?>
    </h2>
    <form method="POST" id="myForm" action="insert.php">
        <input type="hidden" name="FOS" value="flagging">
        <input type="hidden" name="claimIDFlaggedINSERT"
            value="<?php echo $claim_id; ?>">
        <h3>What are you flagging it for?</h3>
        <?php $supportingForm->topicInput($claim->topic, /* hidden */true); ?>
        <?php $supportingForm->flagTypeInput($claim->supportMeans); ?>
        <h3>Enter your new thesis.</h3>
        <?php $supportingForm->subjectTargetInput(); ?>
        <?php $supportingForm->supportMeansInput(); ?>
        <div>
            <button type="submit" id="submit">Submit</button>
        </div>
        <script src="assets/scripts/add.js?timestamp=20230219"></script>
    </form>
</body>
