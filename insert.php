<?php
require "vendor/autoload.php";

/*
This script is the target endpoint for add.php form submissions.
*/

// TODO: this logic should be abstracted, then moved into the respective add forms.

if (!isset($_POST["topic_id"])) {
    // the form has not been submitted. continue.
    return;
}

use Vada\Model\Database;
use Vada\Model\ClaimRepository;
use Vada\Controller\ActivityController;

$pdo = Database::connect();
$claimRepository = new ClaimRepository($pdo);
$topicRepository = new \Vada\Model\TopicRepository($pdo);
$activityController = new ActivityController($claimRepository);

// Get unescaped values from POST.

$_POST = array_map('trim', $_POST);

$topic_id = intval($_POST['topic_id'] ?? null);

$topic = $topicRepository->getTopicByID($topic_id);
$subject = $_POST['subject'] ?? null;
$targetP = $_POST['targetP'] ?? null;
$reason = $_POST['reason'] ?? null;
$supportMeans = $_POST['supportMeans'] ?? null;
$example = $_POST['example'] ?? null;
$transcription = $_POST['transcription'] ?? null;
$citation = $_POST['citation'] ?? null;
$url = $_POST['url'] ?? null;
$flagType = $_POST['flagType'] ?? null;
$vidtimestamp = $_POST['vidtimestamp'] ?? null;
$grammar = $_POST['grammar'] ?? null;
$flaggingOrSupporting = $_POST['flaggingOrSupporting'] ?? null;
$topic_trimmed = trim($topic_id);
$flagged_id = $_POST['claimIDFlagged'] ?? null;

if (!isset($topic_id, $subject, $targetP)) {
    $params = $_SERVER['QUERY_STRING'];
    error_log("Invalid submission $params");
    return "Error: Missing required form parameters.";
}

// START TRANSACTION
$claimRepository->conn->beginTransaction();
$thesis_id = null;
$support_id = null;
try {
    switch ($flaggingOrSupporting) {
        case "flagging":
            // Insert flag's thesis claim
            $active = ('Thesis Rival' != $flagType); // thesis rivals are contested by default
            $thesis_id = $claimRepository->insertThesis($topic_id, $subject, $targetP, $active);
            // Check if the flagged claim is a root claim.
            $isRootRival = ('Thesis Rival' == $flagType) && $claimRepository->isRootClaim($flagged_id);
            // Insert a flagging relation between the thesis and the claim it flags
            $claimRepository->insertFlag($flagged_id, $thesis_id, $flagType, $isRootRival);
            // Insert an extra flagging relation from the rivalled thesis
            if ($flagType == 'Thesis Rival') {
                $claimRepository->insertFlag($thesis_id, $flagged_id, $flagType, $isRootRival);
            }
            // set newly-flagged claim to be inactive.
            // $claimRepository->setClaimActive($claimIDFlagged, false);
            // Insert support
            $support_id = $claimRepository->insertSupport($topic_id, $thesis_id, $subject, $targetP, $supportMeans, $reason, $example, $url, $citation, $transcription, $vidtimestamp);
            break;
        case "supporting":
            // Insert support.
            $support_id = $claimRepository->insertSupport($topic_id, $flagged_id, $subject, $targetP, $supportMeans, $reason, $example, $url, $citation, $transcription, $vidtimestamp);
            break;
        default:
            // Insert thesis claim
            $thesis_id = $claimRepository->insertThesis($topic_id, $subject, $targetP);
            // Insert support for thesis
            $support_id = $claimRepository->insertSupport($topic_id, $thesis_id, $subject, $targetP, $supportMeans, $reason, $example, $url, $citation, $transcription, $vidtimestamp);
    }
    // END TRANSACTION
    $claimRepository->conn->commit();
    // TODO: with proper abstraction, this will be an object.
    $new_claim = $claimRepository->getClaimByID($thesis_id ?? $support_id);
    $topic_url_part = urlencode($topic_trimmed);
    header("Location: {$topic->getURL()}#{$new_claim->display_id}");
} catch (PDOException $ex) {
    error_log($ex->getMessage());
    $claimRepository->conn->rollback();
    $error = "A database error occured, insertion failed: " . $ex->getMessage();
} finally {
    $activityController->restoreActivityTopic($topic);
}
return true;