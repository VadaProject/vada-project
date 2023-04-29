<?php
/**
 * This page renders in an iframe and inserts a support.
 * Closely mirrors addsupport.php, to the point of duplication.
 * TODO: refactor them to be closer together.
 */
namespace Vada;

require __DIR__ . "/../vendor/autoload.php";

$db = Model\Database::connect();
$claimRepository = new Model\ClaimRepository($db);
$topicRepository = new Model\TopicRepository($db);
$userAuthenticator = new Model\UserAuthenticator(new Model\GroupRepository($db), new Model\CookieManager("VadaGroups"));

// handle database insertion, then render page.
require __DIR__ . "/../src/Model/insert.php";
?>
<!DOCTYPE html>
<html>
<head>
    <?php require __DIR__ . '/../includes/head-tag-contents.php'; ?>
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
    if (!$userAuthenticator->canAccessTopic($claim->topic_id)) {
        exit("Error: Access denied. Please join a group with access to this topic.");
    }
    if (is_null($claim)) {
        echo "<h2>Error: a claim with the ID #$claim_id does not exist.</h2>";
        return;
    }
    $supportingForm = new View\SupportingForm();
    ?>
    <h2>Flagging claim #
        <?=$claim->display_id?>
    </h2>
    <form method="POST" id="myForm" target="_parent">
        <?php $supportingForm->showError($error ?? null); ?>
        <input type="hidden" name="flaggingOrSupporting" value="flagging">
        <input type="hidden" name="claimIDFlagged"
            value="<?=$claim_id?>">
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
        <script src="assets/scripts/add.js?timestamp=20230428"></script>
    </form>
</body>
