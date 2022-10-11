<?php

function haveRival($claimid)
{
    require __DIR__ . '/../config/db_connect.php';
    $answer = 'false';

    $act2 = "SELECT DISTINCT claimIDFlagger
  from flagsdb
  WHERE claimIDFlagged = ? AND flagType LIKE 'Thesis Rival'
  ";
    $s2 = $conn->prepare($act2);
    $s2->bind_param('i', $claimid);
    $s2->execute();
    $activity2 = $s2->get_result();
    $nh2 = mysqli_num_rows($activity2);
    while ($supports = $activity2->fetch_assoc()) {
        $answer = 'true';
    } // end of while

    return $answer;
}
