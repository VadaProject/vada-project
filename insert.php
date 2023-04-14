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

$db = Database::connect();
$claimRepository = new ClaimRepository($db);
$topicRepository = new \Vada\Model\TopicRepository($db);
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
$flag_type = $_POST['flagType'] ?? null;
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

$thesis_id = null;
$support_id = null;
try {
    switch ($flaggingOrSupporting) {
        case "flagging":
            $thesis_id = null;
            if ($flag_type == 'Thesis Rival') {
                $rivaled_claim = $claimRepository->getClaimByID($rival_id);
                $has_rival = $rivaled_claim->rival_id ? true : false;
                if ($has_rival) {
                    $display_id = $rivaled_claim->display_id;
                    $error = "Error: claim #$display_id already has a rival. A new one cannot be added at this time.";
                    return false;
                }
                // iff the flagged claim is a root claim, this is a rootRival.
                $is_root_rival = $claimRepository->isRootClaim($flagged_id);
                // Insert rival.
                $thesis_id = $claimRepository->insertRival($topic_id, $subject, $targetP, $flagged_id, $is_root_rival);
            } else {
                // Insert regular flag.
                $thesis_id = $claimRepository->insertFlag($topic_id, $subject, $targetP, $flagged_id, $flag_type);
            }
            $active = ('Thesis Rival' != $flag_type); // thesis rivals are contested by default
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
    $new_claim = $claimRepository->getClaimByID($thesis_id ?? $support_id);
    $topic_url_part = urlencode($topic_trimmed);
    header("Location: {$topic->getURL()}#{$new_claim->display_id}");
} catch (Exception $ex) {
    error_log($ex->getMessage());
    $error = "A database error occured, insertion failed: " . $ex->getMessage();
} finally {
    $activityController->restoreActivityTopic($topic_id);
}
return true;