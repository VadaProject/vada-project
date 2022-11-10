<?php

/*
This function checks the status of each claim individually.
It observes surrounding relationships to determine if the claim is contested or not.
*/

function restoreActivity($claimid)
{
    require_once __DIR__ . '/../config/db_connect.php';
    $conn = db_connect();

    // grabs supports for initial claim NUMBER ONE ON DIAGRAM, RED
    $act2 = "SELECT DISTINCT claimIDFlagger
from flagsdb
WHERE claimIDFlagged = ? and flagType LIKE 'supporting'";
    $s2 = $conn->prepare($act2);
    $s2->bind_param('i', $claimid);
    $s2->execute();
    $activity2 = $s2->get_result();
    $nh2 = mysqli_num_rows($activity2);
    foreach ($activity2 as $supports) {
        // claimid is the original claim. supportsClaimIDFLAGGER is the support. check to see if all the supports are inactive. OR if ONE support is active!!!!!!!!!!!!!!!

        $new = 'SELECT DISTINCT active
  from claimsdb
  WHERE claimID = ?';
        $snew = $conn->prepare($new);
        $snew->bind_param('i', $supports['claimIDFlagger']);
        $snew->execute();
        $activitynew = $snew->get_result();
        $everyInactiveSupport = 'true';
        foreach ($activitynew as $SCHECK) {
            //  echo '<script type="text/javascript">alert("active: ' . $SCHECK['active'] . "support number" . $supports['claimIDFlagger'] . '");</script>';

            // are supports active? we only need one to reactivate the claim.
            if (
                '1' == $SCHECK['active'] &&
                !doesThesisFlag($claimid) &&
                !haveRival($claimid)
            ) {
                // i have a suspicion that this isn't working/triggering
                // THIS IS TRIGGERED FOR 1383

                global $everyInactiveSupport;
                $everyInactiveSupport = 'false';

                $act = 'UPDATE claimsdb
      SET active = 1
      WHERE claimID = ?
'; // SQL with parameters
                $upd = $conn->prepare($act);
                $upd->bind_param('i', $claimid);
                $upd->execute();
            } // end of if
        } // end of while loop

        // are all supports inactive? claim is inactive.
        /*  if($everyInactiveSupport == 'true')
                          {
                            $act = "UPDATE claimsdb
                            SET active = 0
                            WHERE claimID = ?
                        "; // SQL with parameters
                        $upd = $conn->prepare($act);
                        $upd->bind_param("i", $claimid);
                        $upd->execute();
                         } // end of second if statement
                        */

        if ('false' == doesThesisFlag($supports['claimIDFlagger'])) {
            $act = 'UPDATE claimsdb
            SET active = 1
            WHERE claimID = ?
        '; // SQL with parameters
            $upd = $conn->prepare($act);
            $upd->bind_param('i', $supports['claimIDFlagger']);
            $upd->execute();
        }

        if (!doesThesisFlag($claimid)) {
            noSupports($claimid);
        }

        if (0 == $nh2) {
        } else {
            restoreActivity($supports['claimIDFlagger']);
        }
        // supports get pushed into the recursive process. every time.

        // $SUPPORTS ENDED HERE BEFORE

        // also, for all supports, if they have ONE (active) flag, then they're inactive. THIS IS ALREADY DONE.
        // for all supports, if theres a flag but its inactive, the support is active. !!!!!!!!!!!!! THIS IS THE CODE BELOW

        // /////////////////////////////////////////////////////// NUMBER TWO ON DIAGRAM, ORANGE
        // below grabs all flaggers for the support and JUST the support. not the claims.  - act3, s3, activity3

        // below is for rivals

        $a = "SELECT DISTINCT claimIDFlagger
from flagsdb
WHERE claimIDFlagged = ? and flagType LIKE 'Thesis Rival'";
        $si = $conn->prepare($a);
        $si->bind_param('i', $supports['claimIDFlagger']);
        $si->execute();
        $sim = $si->get_result();
        foreach ($sim as $mi) {
            restoreActivityRIVAL($mi['claimIDFlagger']);

            // below should get the companion rival

            $a2 = "SELECT DISTINCT claimIDFlagger
from flagsdb
WHERE claimIDFlagged = ? and flagType LIKE 'Thesis Rival'";
            $si2 = $conn->prepare($a2);
            $si2->bind_param('i', $mi['claimIDFlagger']);
            $si2->execute();
            $sim2 = $si2->get_result();
            foreach ($sim2 as $mi2) {
                restoreActivityRIVAL($mi2['claimIDFlagger']);
            } // end of first while
        } // end of second while
        // echo '<script type="text/javascript">alert("claim id: ' . $claimid . "support number" . $supports['claimIDFlagger'] . "mi" . $mi['claimIDFlagger'] .  '");</script>';

        // above is for rivals

        $act3 = "SELECT DISTINCT claimIDFlagger
  from flagsdb
  WHERE claimIDFlagged = ? and flagType NOT LIKE 'Thesis Rival'
  ";
        $s3 = $conn->prepare($act3);
        $s3->bind_param('i', $supports['claimIDFlagger']);
        $s3->execute();
        $activity3 = $s3->get_result();
        $nh = mysqli_num_rows($activity3);

        // echo '<script type="text/javascript">alert("alert: ' . $supports['claimIDFlagger'] . '");</script>';

        foreach ($activity3 as $activeflags) {
            if (0 == $nh) {
            } else {
                restoreActivity($activeflags['claimIDFlagger']);
            }

            // /////////////////////////////////////////////////////////////////////////////////

            $h = 'SELECT DISTINCT active
                                            from claimsdb
                                            WHERE ? = claimID'; // SQL with parameters
            $noce = $conn->prepare($h);
            $noce->bind_param('i', $activeflags['claimIDFlagger']);
            $noce->execute();
            $res = $noce->get_result(); // get the mysqli result
            $numh = mysqli_num_rows($res);
            // checks the active status of the flagger

            $everyInactive = 'false';
            // echo $everyInactive;
            foreach ($res as $r) {
                if (1 == $r['active']) {
                    global $everyInactive;
                    $everyInactive = 'false';
                    //    echo $everyInactive;
                    $act = 'UPDATE claimsdb
                                              SET active = 0
                                              WHERE claimID = ?
                                    '; // SQL with parameters
                    $upd = $conn->prepare($act);
                    $upd->bind_param('i', $supports['claimIDFlagger']);
                    $upd->execute();
                } // end of if
            } // end of while loop

            /* if($everyInactive == 'true')
                             {

                                    //echo "ANSWER" . $everyInactive;
                                    // BELOW CHANGES THE ACTIVE STATE OF OTHER CLAIMS
                               $act = "UPDATE claimsdb
                               SET active = 1
                               WHERE claimID = ?
                                    "; // SQL with parameters
                                    $upd = $conn->prepare($act);
                                    $upd->bind_param("i", $supports['claimIDFlagger']);
                                    $upd->execute();
                            } // end of second if statement */
        } // end of while loop

        // //////////////////////////////////////////////////////////////////////////////////////////////////////////////
    } // end of while loop

    // this needs to be checking thesis flags for root claims

    /*
                      while($TETL = $activity ->fetch_assoc())
                      {

                    // grabs all active statuses for all the supports of the claim
                    $act37 = "SELECT DISTINCT active
                            from claimsdb
                            WHERE claimID = ?";
                    $s37 = $conn->prepare($act37);
                    $s37->bind_param("i", $TETL['claimIDFlagger']);
                    $s37->execute();
                    $activity37 = $s37->get_result();
                    $nh = mysqli_num_rows($activity37);

                         while($ChAc = $activity37->fetch_assoc())
                      {

                    $allSupportsInactive = '';

                    if($ChAc['active'] = '1')
                    {
                    $allSupportsInactive = 'false';
                    }
                    else{
                      $allSupportsInactive = 'true';

                    }// end of else

                     if($allSupportsInactive == 'true')
                      {

                    //echo "ANSWER" . $everyInactive;
                    // BELOW CHANGES THE ACTIVE STATE OF OTHER CLAIMS
                    $act = "UPDATE claimsdb
                    SET active = 0
                    WHERE claimID = ?
                    "; // SQL with parameters
                    $upd = $conn->prepare($act);
                    $upd->bind_param("i", $claimid);
                    $upd->execute();
                     } // end of second if statement

                    */

    // } // end of while for active

    // check for if there is at least one active support for root claims

    //  }// end of while for the flaggers

    //  }//end of while for the supports to get their flaggers

    // above grabs all flaggers for the support  - act3, s3, activity3

    // GRABS ALL FLAGS OF ORIGINAL CLAIM ---------------------------- BLUE ON DIAGRAM, 3
    $act90 = "SELECT DISTINCT claimIDFlagger
 from flagsdb
 WHERE claimIDFlagged = ? and flagType NOT LIKE 'Thesis Rival' and flagType NOT LIKE 'supporting'";
    $s90 = $conn->prepare($act90);
    $s90->bind_param('i', $claimid);
    $s90->execute();
    $activity90 = $s90->get_result();
    $nh90 = mysqli_num_rows($activity90);

    // above grabs all flaggers for non-rival root claims
    // all tooearly or toolate //$activity
    // *AND* all support flags because while it doesn't occur for the first run through, when a support is put into the parameters, it'll check all reason/rule flags

    foreach ($activity90 as $activestatus) {
        if (0 == $nh90) {
        } else {
            restoreActivity($activestatus['claimIDFlagger']);
        }

        // echo '<script type="text/javascript">alert("active: ' . $activestatus['claimIDFlagger'] .  '");</script>';

        // ////////////////////////////////////////// COME BACK
        $h90 = 'SELECT DISTINCT active
  from claimsdb
        WHERE ? = claimID'; // SQL with parameters
        $noce90 = $conn->prepare($h90);
        $noce90->bind_param('i', $activestatus['claimIDFlagger']);
        $noce90->execute();
        $res90 = $noce90->get_result(); // get the mysqli result

        foreach ($res90 as $r90) {
            // grabs active status of all flaggers of original claim: is it active?
            // $activestatus['claimiDflagger'] <--- flagtype like "suppporting"

            if (1 == $r90['active']) {
                $act = 'UPDATE claimsdb
          SET active = 0
          WHERE claimID = ?
'; // SQL with parameters
                $upd = $conn->prepare($act);
                $upd->bind_param('i', $claimid);
                $upd->execute();
            }
            // end of if
            else {
                $act = 'UPDATE claimsdb
          SET active = 1
          WHERE claimID = ?
'; // SQL with parameters
                $upd = $conn->prepare($act);
                $upd->bind_param('i', $claimid);
                $upd->execute();
            }

            // if($everyInactive == 'true')
            // {
            // we don't want to set this back to active, even if there's no thesis flags, because it may still have no ACTIVE support. restoration should happen earlier in the code. if not, it should probably go here.
            // } // end of second if statement
        } // end of while statement

        // below is for rivals

        $a = "SELECT DISTINCT claimIDFlagger
from flagsdb
WHERE claimIDFlagged = ? and flagType LIKE 'Thesis Rival'";
        $si = $conn->prepare($a);
        $si->bind_param('i', $claimid);
        $si->execute();
        $sim = $si->get_result();
        foreach ($sim as $mi) {
            restoreActivityRIVAL($mi['claimIDFlagger']);
        }

        // above is for rivals
    } // end while loop
}

/*
This function has the same functionality as restoreActivity, but for rivals.
The key difference is it must account for the “mutualistic flagging” relationship that is unique to rivals (that is, they flag each other equally).
This function determines when one of the rival claims may reach an uncontested state (as the typical state for a rivals pair is equal contestation).
*/

function restoreActivityRIVAL($claimid)
{
    require_once __DIR__ . '/../config/db_connect.php';
    $conn = db_connect();

    // below finds the flagger and continues the recursion by pushing it back to normal restore activity function
    // IN ADDITION below is to check active status of flagging claims OF INITIAL RIVAL

    $everyInactiveA = 'true';

    $everyInactiveB = 'true';

    //  echo '<script type="text/javascript">alert("active: ' . $claimid . '");</script>';

    //  noSupports($claimid);

    // noSupports($nodeFlaggers, $rivaling);

    // set of all too-early and too-late
    $sql188 = "SELECT DISTINCT claimIDFlagger
from claimsdb, flagsdb
where ? = claimIDFlagged AND flagType NOT LIKE 'Thesis Rival'
";
    $stmt188 = $conn->prepare($sql188);
    $stmt188->bind_param('i', $claimid);
    $stmt188->execute();
    $result188 = $stmt188->get_result();
    $numhits1 = mysqli_num_rows($result188);
    // above looks for normal non-rival flags for this rivaling claim.
    foreach ($result188 as $user) {
        $nodeFlaggers = $user['claimIDFlagger'];
        if (0 == $numhits1) {
        } else {
            restoreActivity($nodeFlaggers);
        } // end of  restoreactivity push FOR THIS SIDE OF THE RIVAL PAIR. the rival companion is pushed to restoreactivity at the bottom of this function.
    }
    // above it finds rival A's flaggers.

    // below is to check active status of flagging claims OF RIVAL COMPANION
    $rivaling = '';
    // finds the companion
    $sql12 = "SELECT DISTINCT claimIDFlagger
from claimsdb, flagsdb
where ? = claimIDFlagged AND flagType LIKE 'Thesis Rival'
";
    $stmt12 = $conn->prepare($sql12);
    $stmt12->bind_param('i', $claimid);
    $stmt12->execute();
    $result12 = $stmt12->get_result();
    $numhits1 = mysqli_num_rows($result12);
    // found rival pair!
    foreach ($result12 as $user) {
        $rivaling = $user['claimIDFlagger']; // $rivaling is Rival B.
    } // end while loop

    // above finds rival A's companion, aka rival b.

    // above is to check active status of flagging claims OF RIVAL COMPANION

    // this is finding the flaggers for rival B
    $sql167 = "SELECT DISTINCT claimIDFlagger
from claimsdb, flagsdb
where ? = claimIDFlagged AND flagType NOT LIKE 'Thesis Rival'
";
    $stmt167 = $conn->prepare($sql167);
    $stmt167->bind_param('i', $rivaling);
    $stmt167->execute();
    $result167 = $stmt167->get_result();
    $numhits167 = mysqli_num_rows($result167);
    // above looks for normal non-rival flags for this rivaling claim.
    foreach ($result167 as $userRIVALING) {
        if (0 == $numhits167) {
        } else {
            restoreActivity($userRIVALING['claimIDFlagger']);
        }
    } // end of outer while loop

    // /////////////////////////////////////////// start of checking for supports and then putting the results in recursion

    /*

      $rivalsupports = "SELECT DISTINCT claimIDFlagger
      from claimsdb, flagsdb
      where ? = claimIDFlagged AND flagType LIKE 'supporting'
      ";
      $stmtsupports = $conn->prepare($rivalsupports);
      $stmtsupports->bind_param("i", $rivaling);
      $stmtsupports->execute();
      $resultsupports = $stmtsupports->get_result();
      $numhitsSupports = mysqli_num_rows($resultsupports);
    //above looks for normal non-rival flags for this rivaling claim.
      while($rivalsupporting = $resultsupports->fetch_assoc())
      {
    echo '<script type="text/javascript">alert("active: ' . $rivalsupporting['claimIDFlagger'] .  '");</script>';

        if($numhitsSupports == 0)
          { }
        else {restoreActivity($rivalsupporting['claimIDFlagger']); }

    } // end of while

      $rivalsupports2 = "SELECT DISTINCT claimIDFlagger
      from claimsdb, flagsdb
      where ? = claimIDFlagged AND flagType LIKE 'supporting'
      ";
      $stmtsupports2 = $conn->prepare($rivalsupports2);
      $stmtsupports2->bind_param("i", $claimid);
      $stmtsupports2->execute();
      $resultsupports2 = $stmtsupports2->get_result();
      $numhitsSupports2 = mysqli_num_rows($resultsupports2);
    //above looks for normal non-rival flags for this rivaling claim.
      while($rivalsupporting2 = $resultsupports2->fetch_assoc())
      {

                echo '<script type="text/javascript">alert("active: ' . $rivalsupporting2['claimIDFlagger'] .  '");</script>';

        if($numhitsSupports2 == 0)
          { }
        else {restoreActivity($rivalsupporting2['claimIDFlagger']); }

    } // end of while
    */
    // /////////////////////////////////////////// end of checking for supports and then putting the results in recursion

    $statusA = '';
    $statusB = '';
    if ('true' == noSupportsRival($claimid) && !doesThesisFlagRival($claimid)) {
        $statusA = 'unchallenged';
    } else {
        $statusA = 'challenged';
    }

    if (
        'true' == noSupportsRival($rivaling) &&
        !doesThesisFlagRival($rivaling)
    ) {
        $statusB = 'unchallenged';
    } else {
        $statusB = 'challenged';
    }

    // echo "CLAIM ID:" . $claimid . noSupportsRival($claimid) . doesThesisFlagRival($claimid) . "<BR> ACTIVE B: " . $rivaling . noSupportsRival($rivaling) . doesThesisFlagRival($rivaling) . "<BR><BR><BR><BR><BR><BR><BR><BR>";

    //  echo "CLAIM ID:" . $claimid . $rivaling . "<BR> ACTIVE B: " . $statusB . "<BR> ACTIVE A: " . $statusA . "<BR><BR><BR><BR><BR><BR><BR><BR>";

    if (
        ('unchallenged' == $statusA && 'unchallenged' == $statusB) ||
        ('challenged' == $statusA && 'challenged' == $statusB)
    ) {
        $act = 'UPDATE claimsdb
SET active = 0
WHERE claimID = ?
'; // SQL with parameters
        $upd = $conn->prepare($act);
        $upd->bind_param('i', $claimid);
        $upd->execute();

        $act = 'UPDATE claimsdb
SET active = 0
WHERE claimID = ?
'; // SQL with parameters
        $upd = $conn->prepare($act);
        $upd->bind_param('i', $rivaling);
        $upd->execute();
    }

    // if its true, there are no flags.
    // if false, there are flags.
    if ('unchallenged' == $statusA && 'challenged' == $statusB) {
        $act = 'UPDATE claimsdb
SET active = 1
WHERE claimID = ?
'; // SQL with parameters
        $upd = $conn->prepare($act);
        $upd->bind_param('i', $claimid);
        $upd->execute();

        $act = 'UPDATE claimsdb
SET active = 0
WHERE claimID = ?
'; // SQL with parameters
        $upd = $conn->prepare($act);
        $upd->bind_param('i', $rivaling);
        $upd->execute();
    }

    if ('unchallenged' == $statusB && 'challenged' == $statusA) {
        $act = 'UPDATE claimsdb
SET active = 0
WHERE claimID = ?
'; // SQL with parameters
        $upd = $conn->prepare($act);
        $upd->bind_param('i', $claimid);
        $upd->execute();

        $act = 'UPDATE claimsdb
SET active = 1
WHERE claimID = ?
'; // SQL with parameters
        $upd = $conn->prepare($act);
        $upd->bind_param('i', $rivaling);
        $upd->execute();
    }
}
