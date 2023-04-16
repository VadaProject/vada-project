<?php
require "vendor/autoload.php";
use Vada\Model\ClaimRepository;
use Vada\Model\TopicRepository;
use Vada\Model\Database;
use Vada\View\SupportingForm;

$db = Database::connect();
$claimRepository = new ClaimRepository($db);
$topicRepository = new TopicRepository($db);

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
    if (!isset($_GET["cid"])) {
        echo "<h2>Error: no claim ID given.</h2>";
        return;
    }
    // the claim_id to support is read from a URL param.
    $claim_id = intval($_GET["cid"]);
    $claim = $claimRepository->getClaimByID($claim_id);
    if (is_null($claim)) {
        echo "<h2>Error: a claim with the ID #$claim_id does not exist.</h2>";
        return;
    }
    $supportingForm = new SupportingForm();
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
