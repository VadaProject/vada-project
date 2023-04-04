<?php
/*
This script is the target endpoint for add.php form submissions.
*/

if (!isset($_POST)) {
    // the form has not been submitted. continue.
    return;
}

require_once 'config/db_connect.php';
$conn = db_connect();

require_once 'functions/restoreActivity.php';
require_once 'functions/Database.php';
use Database\Database;


// Get unescaped values from POST.

$_POST = array_map('trim', $_POST);

$topic_id = intval($_POST['topic_id'] ?? null);
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
Database::$conn->begin_transaction();
$thesis_id = null;
$support_id = null;
try {
    switch ($flaggingOrSupporting) {
        case "flagging":
            // Insert flag's thesis claim
            $active = ('Thesis Rival' != $flagType); // thesis rivals are contested by default
            $thesis_id = Database::insertThesis($topic_id, $subject, $targetP, $active);
            // Check if the flagged claim is a root claim.
            $isRootRival = ('Thesis Rival' == $flagType) && Database::isRootClaim($flagged_id);
            // Insert a flagging relation between the thesis and the claim it flags
            Database::insertFlag($flagged_id, $thesis_id, $flagType, $isRootRival);
            // Insert an extra flagging relation from the rivalled thesis
            if ($flagType == 'Thesis Rival') {
                Database::insertFlag($thesis_id, $flagged_id, $flagType, $isRootRival);
            }
            // set newly-flagged claim to be inactive.
            // Database::setClaimActive($claimIDFlagged, false);
            // Insert support
            $support_id = Database::insertSupport($topic_id, $thesis_id, $subject, $targetP, $supportMeans, $reason, $example, $url, $citation, $transcription, $vidtimestamp);
            break;
        case "supporting":
            // Insert support.
            $support_id = Database::insertSupport($topic_id, $flagged_id, $subject, $targetP, $supportMeans, $reason, $example, $url, $citation, $transcription, $vidtimestamp);
            break;
        default:
            // Insert thesis claim
            $thesis_id = Database::insertThesis($topic_id, $subject, $targetP);
            // Insert support for thesis
            $support_id = Database::insertSupport($topic_id, $thesis_id, $subject, $targetP, $supportMeans, $reason, $example, $url, $citation, $transcription, $vidtimestamp);
    }
    // END TRANSACTION
    Database::$conn->commit();
    if ($thesis_id) {
        echo "Successfully inserted claim #$thesis_id";
    }
    if ($support_id) {
        echo "Successfully inserted claim #$thesis_id";
    }
    $topic_url_part = urlencode($topic_trimmed);
    header("Location: topic.php?id={$topic_id}#{$support_id}");
} catch (mysqli_sql_exception $ex) {
    error_log($ex->getMessage());
    Database::$conn->rollback();
    $error = "A database error occured, insertion failed: " . $ex->getMessage();
} finally {
    restoreActivityTopic($topic_id);
}
return true;