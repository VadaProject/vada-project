<?php

require_once "Database.php";
use Database\Database;
require_once __DIR__ . '/../config/db_connect.php';

/*
This function displays each individual claim in a recursive manner.
Each recursion is a series of tracking relationships between the claims (found in the Flabsdb).
*/

//////////////////////////////////////////////
// HTML
//////////////////////////////////////////////
function make_label_el($claim_id, $claim, $flag_type)
{
    ?>
    <label for="<?php echo $claim_id; ?>">
    <?php
    switch ($flag_type) {
        case 'supporting':
            echo "<img height = '45' width = '32' src='assets/img/support.png'> <br>";
            echo $claim['supportMeans'] . '<br>';
            if ($claim['supportMeans'] == 'Inference') {
                echo 'Reason: ' .
                    $claim['subject'] .
                    ' ' .
                    $claim['reason'] .
                    ', as in the case of ' .
                    $claim['example'] .
                    '<BR>';
            }
            if (
                $claim['supportMeans'] == 'Testimony' ||
                $claim['supportMeans'] == 'Perception'
            ) {
                echo 'Citation: ' . $claim['citation'] . '<BR>';
            }
            break;
        case '':
            echo '<h1>Thesis</h1>';
            echo '<br>' . $claim['subject'] . ' ' . $claim['targetP'] . '<br>';
            break;
        default:
            echo "<img src='assets/img/flag.png'> <br>";
            echo '<br> Flagged: ' . $flag_type . '<br>';
            echo '<h1>Thesis</h1>';
            echo '<br>' . $claim['subject'] . ' ' . $claim['targetP'] . '<br>';
    }
    echo '#' . $claim_id . '<br>';

    // add is subject person or object to inference div

    // FONT CHANGING
    if ($claim['active'] != 1) {
        echo "<img src='assets/img/alert.png'> <br>";
    }
    createModal($claim_id);?> </label> <?php
}

// starts two chains of recursion. one with normal root claims.
// the other with root rivals. the rivals, of course, are put into the rival recursion.
function sortclaims($claimID)
{
    $conn = db_connect();
    $claim = Database::getClaim($claimID);
    if (!$claim) {
        return;
    }
    $flags = Database::getRivalFlags($claimID);
    $resultFlagType = $claimIDFlagger = $claimIDFlagged = '';
    foreach ($flags as $f) {
        $resultFlagType = $f['flagType'];
        $claimIDFlagger = $f['claimIDFlagger'];
        $claimIDFlagged = $f['claimIDFlagged'];
    }
    if ($resultFlagType == 'Thesis Rival') {
        // echo ' <br> The flag ' . $claimIDFlagger . ' has a rival!: ' . '<br>';
        // for THIS claimID - check for flaggers that aren't rival .. sort claim those
        sortclaimsRival($claimIDFlagger);
        // for the CORRESPONDING claimID - check for flaggers that aren't rival .. sort claim those.
        sortclaimsRival($claimIDFlagged);
        return;
    }
    ?>
    <li><input id="<?php echo $claimID; ?>" type="checkbox">
        <?php make_label_el($claimID, $claim, $resultFlagType); ?>
        <ul><span class="more">• • •</span>

        <?php
        // IF A CLAIM IS FLAGGED IT obtains flaggers that aren't rivals
        // if its a thesis rival it will show up in the query above
        // this is when the claim is the flagged. this is what gets pushed in the recursion.
        // continue recursion
        $result1 = Database::getNonRivalFlags($claimID); // get the mysqli result
        foreach ($result1 as $id) {
            sortclaims($id);
        }?></ul><?php
}

/*
This function has the same functionality as the sortClaims, but for rivals.
The key difference is handling the “mutualistic flagging” relationship that is unique to rivals (that is, they flag each other equally).
It breaks an infinite loop that would otherwise occur if a rival was handled recursively in sortClaims().
*/

function sortclaimsRIVAL($claimID)
{
    $conn = db_connect();
    // get the info for the claim being flagged
    $claim = Database::getClaim($claimID);
    // look for normal non-rival flags for this rivaling claim.
    $result1 = Database::getFlaggedRivals($claimID);
    foreach ($result1 as $flagID) {
        $rivaling = $flagID;
    }
    ?>

        <li> <label style="background:#FFFFE0" for="<?php echo $claimID; ?>">
        <?php
        echo "<img width='100' height='20' src='assets/img/rivals.png'> <br><br>";

        if ($claim['active'] != 1) {
            echo "<img src='assets/img/alert.png'> <br>";
        }

        echo '<h4>Contests #' . $rivaling . '</h4>';
        echo '<h1>Thesis</h1>';
        echo $claim['subject'] . ' ' . $claim['targetP'];
        echo '<BR>' . $claimID;
        createModal($claimID);
        ?>

        </label><input id="<?php echo $claimID; ?>" type="checkbox">
        <ul> <span class="more">&hellip;</span>
            <!--</font>-->
                <?php
                $result1 = Database::getNonRivalFlags($claimID);
                foreach ($result1 as $flagID) {
                    sortclaims($flagID);
                }?>
        </ul><?php
} // end of rivalfunction
?>
