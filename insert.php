<?php
/*
This script is the target endpoint for add.php form submissions.
*/

require_once 'config/db_connect.php';
$conn = db_connect();

require_once 'functions/restoreActivity.php';
require_once 'functions/Database.php';
use Database\Database;


// Get unescaped values from POST.

$_POST = array_map('trim', $_POST);

$topic = $_POST['topic'] ?? null;
$subject = $_POST['subject'] ?? null;
$targetP = $_POST['targetP'] ?? null;

if (!isset($topic, $subject, $targetP)) {
    $params = $_SERVER['QUERY_STRING'];
    error_log("Invalid submission $params");
    exit("Missing required form parameters.");
}

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
$topic_trimmed = trim($topic);
$flagged_id = $_POST['claimIDFlagged'] ?? null;

if (isset($flaggingOrSupporting) && !isset($flagged_id)) {
    $params = $_SERVER['QUERY_STRING'];
    error_log("Invalid submission $params");
    exit("Missing required form parameters.");
}

// START TRANSACTION
Database::$conn->begin_transaction();
try {
    switch ($flaggingOrSupporting) {
        case "flagging":
            // Insert flag's thesis claim
            $active = ('Thesis Rival' != $flagType); // thesis rivals are contested by default
            $thesis_id = Database::insertThesis($topic_trimmed, $subject, $targetP, $active);
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
            Database::insertSupport($topic_trimmed, $thesis_id, $subject, $targetP, $supportMeans, $reason, $example, $url, $citation, $transcription, $vidtimestamp);
            break;
        case "supporting":
            // Insert support.
            Database::insertSupport($topic_trimmed, $flagged_id, $subject, $targetP, $supportMeans, $reason, $example, $url, $citation, $transcription, $vidtimestamp);
            break;
        default:
            // Insert thesis claim
            $thesis_id = Database::insertThesis($topic_trimmed, $subject, $targetP);
            // Insert support for thesis
            Database::insertSupport($topic_trimmed, $thesis_id, $subject, $targetP, $supportMeans, $reason, $example, $url, $citation, $transcription, $vidtimestamp);
    }
    // END TRANSACTION
    Database::$conn->commit();
    if (isset($thesis_id)) {
        restoreActivity($thesis_id);
    }
    if (isset($flagged_id)) {
        restoreActivity($flagged_id);
    }
    if ($flagType === "Thesis Rival") {
        restoreActivityRIVAL($thesis_id);
        restoreActivityRIVAL($flagged_id);
    }
} catch (mysqli_sql_exception $ex) {
    error_log($ex->getMessage());
    Database::$conn->rollback();
    exit("Database error occured, insertion failed.");
}
?>
Redirecting...
<script>
    window.location.href = "topic.php?topic=" + "<?php htmlspecialchars($topic_trimmed) ?>";
</script>