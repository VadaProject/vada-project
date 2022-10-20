<?php

/*
This function checks to see if an individual claim has any ACTIVE supports or not.
*/

function noSupports($claimid)
{
    require_once __DIR__ . '/../config/db_connect.php';
    $conn = db_connect();
    $result = 'no active supports';

    $act2 = "SELECT DISTINCT claimIDFlagger
        from flagsdb
        WHERE claimIDFlagged = ? and flagType LIKE 'supporting'";
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
        $everyInactiveSupport = 'true';

        while ($SCHECK = $activitynew->fetch_assoc()) {
            // echo '<script type="text/javascript">alert("active: ' . $SCHECK['active'] . '");</script>';

            if (1 == $SCHECK['active']) {
                $result = 'There is an active';
                // can you just break here?
            }
        } // end of second while loop
    } // end of first while loop

    if ('There is an active' != $result) {
        // echo '<script type="text/javascript">alert("ITS HAPPENING: ' . $result . '");</script>';

        $act = 'UPDATE claimsdb
        SET active = 0
        WHERE claimID = ?

    ';
        $upd = $conn->prepare($act);
        $upd->bind_param('i', $claimid);
        $upd->execute();
    } // end of if statement
}

/*
 * This function checks to see if an individual claim has any active supports, but for rivals.
 */

function noSupportsRival($claimidA)
{
    require_once __DIR__ . '/../config/db_connect.php';
    $conn = db_connect();
    $result = 'no active supports';

    $act2 = "SELECT DISTINCT claimIDFlagger
  from flagsdb
  WHERE claimIDFlagged = ? and flagType LIKE 'supporting'";
    $s2 = $conn->prepare($act2);
    $s2->bind_param('i', $claimidA);
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
        $everyInactiveSupport = 'true';

        while ($SCHECK = $activitynew->fetch_assoc()) {
            // echo '<script type="text/javascript">alert("active: ' . $SCHECK['active'] . '");</script>';

            if (1 == $SCHECK['active']) {
                $result = 'There is an active';

                return 'true';
                // can you just break here?
            }
        } // end of second while loop
    } // end of first while loop

    // rivalA : supportless --> rivalb should be active. does rivalb have active TE/TL?

    // rivalB : needs to be active AND it doesn't have a too early / too late AND needs at least one support itself

    if ('There is an active' != $result) {
        // echo '<script type="text/javascript">alert("ITS HAPPENING: ' . $result . '");</script>';

        return 'false';
    } // end of if statement
} // end of function
