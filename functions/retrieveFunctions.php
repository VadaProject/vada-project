<?php
function retrieveTargetP($claimIDFlaggedINSERT)
{
    require __DIR__ . '/../config/db_connect.php';
    $act = 'SELECT * FROM claimsdb WHERE claimID = ?'; // SQL with parameters
    $s = $conn->prepare($act);
    $s->bind_param('i', $claimIDFlaggedINSERT);
    $s->execute();
    $activity = $s->get_result(); // get the mysqli result
    while ($details = $activity->fetch_assoc()) {
        echo $details['targetP'];
    }
}
function retrieveSubject($claimIDFlaggedINSERT)
{
    require __DIR__ . '/../config/db_connect.php';
    $act = 'SELECT * FROM claimsdb WHERE claimID = ?'; // SQL with parameters
    $s = $conn->prepare($act);
    $s->bind_param('i', $claimIDFlaggedINSERT);
    $s->execute();
    $activity = $s->get_result(); // get the mysqli result
    while ($details = $activity->fetch_assoc()) {
        echo $details['subject'];
    }
}
function retrieveCOS($claimIDFlaggedINSERT)
{
    require __DIR__ . '/../config/db_connect.php';
    $act = 'SELECT * FROM claimsdb WHERE claimID = ?'; // SQL with parameters
    $s = $conn->prepare($act);
    $s->bind_param('i', $claimIDFlaggedINSERT);
    $s->execute();
    $activity = $s->get_result(); // get the mysqli result
    while ($details = $activity->fetch_assoc()) {
        return $details['COS'];
    }
}
?>
