<?php

/*
This function checks to see if there are any thesis flags against the claim.
*/

function doesThesisFlag($claimid)
    {
    require __DIR__ . '/../config/db_connect.php';
    $answer = 'false';
    $act2 = "SELECT DISTINCT claimIDFlagger
    from flagsdb
    WHERE claimIDFlagged = ? and flagType NOT LIKE 'supporting'";
                    $s2 = $conn->prepare($act2);
                    $s2->bind_param('i', $claimid);
                    $s2->execute();
                    $activity2 = $s2->get_result();
                    $nh2 = mysqli_num_rows($activity2);
                    while ($supports = $activity2->fetch_assoc()) {
                        $new = 'SELECT DISTINCT active
    from claimsdb
    WHERE claimID = ?';
                        $snew = $conn->prepare($new);
                        $snew->bind_param('i', $supports['claimIDFlagger']);
                        $snew->execute();
                        $activitynew = $snew->get_result();
                        while ($SCHECK = $activitynew->fetch_assoc()) {
                            if (1 == $SCHECK['active']) {
                                $answer = 'true';
                            } // end of if
                        } // end of while
                    } // end of while
                                   //       echo '<script type="text/javascript">alert("active: ' . $claimid . "support number" . $answer . '");</script>';

                    return $answer;
                }


/*
This function checks to see if there are any thesis flags against the claim that AREN’T rivals.
*/

function doesThesisFlagRival($claimid)
{
                    include 'config/db_connect.php';
                    $answer = 'false';

                    $act2 = "SELECT DISTINCT claimIDFlagger
                  from flagsdb
                  WHERE claimIDFlagged = ? AND (flagType LIKE 'Too Early' OR flagType LIKE 'Too Late')
                  ";
                    $s2 = $conn->prepare($act2);
                    $s2->bind_param('i', $claimid);
                    $s2->execute();
                    $activity2 = $s2->get_result();
                   $nh2 = mysqli_num_rows($activity2);
                    while ($supports = $activity2->fetch_assoc()) {
                        $new = 'SELECT DISTINCT active
                    from claimsdb
                    WHERE claimID = ?';
                            $snew = $conn->prepare($new);
                            $snew->bind_param('i', $supports['claimIDFlagger']);
                            $snew->execute();
                            $activitynew = $snew->get_result();
                            while ($SCHECK = $activitynew->fetch_assoc()) {
                                if (1 == $SCHECK['active']) {
                                    $answer = 'true';
                                } // end of if
                        } // end of while
                    } // end of while

                    return $answer;
}
?>
