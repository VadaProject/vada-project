<?php
require_once 'functions/supportingForm.php';
require_once 'functions/Database.php';
use Database\Database;

// handle database insertion, then render page.
require "insert.php";
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
    // the claim_id to support is read from a URL param.
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
        <?php echo $claim->display_id; ?>
    </h2>
    <form method="POST" id="myForm" target="_parent">
        <?php $supportingForm->showError($error ?? null); ?>
        <input type="hidden" name="flaggingOrSupporting" value="flagging">
        <input type="hidden" name="claimIDFlagged"
            value="<?php echo $claim_id; ?>">
        <h3>What are you flagging it for?</h3>
        <?php 
        $supportingForm->topicInput($claim->topic_id); 
        $supportingForm->flagTypeInput($claim->supportMeans);
        ?>
        <h3>Enter your new thesis.</h3>
        <?php
        $supportingForm->subjectTargetInput(); 
        $supportingForm->supportMeansInput(); 
        ?>
        <div>
            <button type="submit" id="submit">Submit</button>
        </div>
        <script src="assets/scripts/add.js?timestamp=20230219"></script>
    </form>
</body>
