<?php

require_once 'Database.php';
use Database\Database;

/**
 * @param int $claim_id The claim to check.
 * @return bool Returns true if a claim has an ACTIVE thesis rival
*/
function haveRival($claim_id)
{
    // TODO: if we do it a lot, this operation could probably be reduced to a SQL query
    $flaggers = Database::getFlaggedRivals($claim_id);
    foreach ($flaggers as $flag_id) {
        if (Database::isClaimActive($flag_id)) {
            return true;
        }
    }
    return false;
}

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
        // claimid is the original claim. supportsClaimIDFLAGGER is the
        // support. check to see if all the supports are inactive.
        // OR if ONE support is active!!!!!!!!!!!!!!!

        $new = 'SELECT DISTINCT active
  from claimsdb
  WHERE claimID = ?';
        $snew = $conn->prepare($new);
        $snew->bind_param('i', $supports['claimIDFlagger']);
        $snew->execute();
        $activitynew = $snew->get_result();
        $everyInactiveSupport = 'true';
        foreach ($activitynew as $SCHECK) {
            // are supports active? we only need one to reactivate the claim.
            if (
                '1' == $SCHECK['active'] &&
                !doesThesisFlag($claimid) &&
                !haveRival($claimid)
            ) {
                // i have a suspicion that this isn't working/triggering
                global $everyInactiveSupport;
                $everyInactiveSupport = 'false';

                Database::setClaimActive($claimid, true);
            }
        }
        if ('false' == doesThesisFlag($supports['claimIDFlagger'])) {
            Database::setClaimActive($supports['claimIDFlagger'], true);
        }
        if (!doesThesisFlag($claimid)) {
            noSupports($claimid);
        }
        if (0 == $nh2) {
        } else {
            restoreActivity($supports['claimIDFlagger']);
        }

        // /////////////////////////////////////////////////////// NUMBER TWO ON DIAGRAM, ORANGE
        // below grabs all flaggers for the support and JUST the support. not the claims.  - act3, s3, activity3

        // this is for rivals
        $a = "SELECT DISTINCT claimIDFlagger
from flagsdb
WHERE claimIDFlagged = ? and flagType LIKE 'Thesis Rival'";
        $si = $conn->prepare($a);
        $si->bind_param('i', $supports['claimIDFlagger']);
        $si->execute();
        $sim = $si->get_result();
        foreach ($sim as $mi) {
            restoreActivityRIVAL($mi['claimIDFlagger']);

            // this should get the companion rival
            $a2 = "SELECT DISTINCT claimIDFlagger
from flagsdb
WHERE claimIDFlagged = ? and flagType LIKE 'Thesis Rival'";
            $si2 = $conn->prepare($a2);
            $si2->bind_param('i', $mi['claimIDFlagger']);
            $si2->execute();
            $sim2 = $si2->get_result();
            foreach ($sim2 as $mi2) {
                restoreActivityRIVAL($mi2['claimIDFlagger']);
            }
        }
        $act3 = "SELECT DISTINCT claimIDFlagger
  from flagsdb
  WHERE claimIDFlagged = ? and flagType NOT LIKE 'Thesis Rival'
  ";
        $s3 = $conn->prepare($act3);
        $s3->bind_param('i', $supports['claimIDFlagger']);
        $s3->execute();
        $activity3 = $s3->get_result();
        $nh = mysqli_num_rows($activity3);

        foreach ($activity3 as $activeflags) {
            if (0 == $nh) {
            } else {
                restoreActivity($activeflags['claimIDFlagger']);
            }
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
            foreach ($res as $r) {
                if (1 == $r['active']) {
                    global $everyInactive;
                    $everyInactive = 'false';
                    Database::setClaimActive($supports['claimIDFlagger'], false);
                }
            }
        }
    }
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
        $h90 = 'SELECT DISTINCT active
  from claimsdb
        WHERE ? = claimID'; // SQL with parameters
        $noce90 = $conn->prepare($h90);
        $noce90->bind_param('i', $activestatus['claimIDFlagger']);
        $noce90->execute();
        $res90 = $noce90->get_result(); // get the mysqli result

        foreach ($res90 as $r90) {
            // grabs active status of all flaggers of original claim: is it active?
            if (1 == $r90['active']) {
                Database::setClaimActive($claimid, false);
            } else {
                Database::setClaimActive($claimid, true);
            }
        }
        // this is for rivals
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
    }
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
        }
        // the rival companion is pushed to restoreactivity at the bottom of this function.
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
    }

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
    }
    $statusA = '';
    $statusB = '';

    if (noSupportsRival($claimid) && !doesThesisFlagRival($claimid)) {
        $statusA = 'unchallenged';
    } else {
        $statusA = 'challenged';
    }

    if (noSupportsRival($rivaling) && !doesThesisFlagRival($rivaling)) {
        $statusB = 'unchallenged';
    } else {
        $statusB = 'challenged';
    }
    if (
        ('unchallenged' == $statusA && 'unchallenged' == $statusB) ||
        ('challenged' == $statusA && 'challenged' == $statusB)
    ) {
        Database::setClaimActive($claimid, false);
        Database::setClaimActive($rivaling, false);
    }
    // if its true, there are no flags.
    // if false, there are flags.
    if ('unchallenged' == $statusA && 'challenged' == $statusB) {
        Database::setClaimActive($claimid, true);
        Database::setClaimActive($rivaling, false);
    }

    if ('unchallenged' == $statusB && 'challenged' == $statusA) {
        Database::setClaimActive($claimid, false);
        Database::setClaimActive($rivaling, true);
    }
}

