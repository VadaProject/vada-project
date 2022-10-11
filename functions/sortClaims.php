<?php

require_once __DIR__ . '/../config/db_connect.php';

/*
This function displays each individual claim in a recursive manner.
Each recursion is a series of tracking relationships between the claims (found in the Flabsdb).
*/

/////////////////////////
// PREPARED STATEMENTS //
/////////////////////////

/**
 * @param int $claimID
 * @param mysqli $conn the mysqli connection to use
 * @return array an associative array of strings representing the claim
 */
function get_claim($claimID, $conn)
{
    $stmt = $conn->prepare('SELECT DISTINCT * from claimsdb where claimID = ?');
    $stmt->bind_param('i', $claimID);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

/**
 * @param int $claimID
 * @param mysqli $conn the mysqli connection to use
 * @return
 */
function get_rival_flags($claimID, $conn)
{
    $stmt = $conn->prepare(
        'SELECT DISTINCT * from flagsdb WHERE claimIDFlagger = ?'
    );
    $stmt->bind_param('i', $claimID);
    $stmt->execute();
    return $stmt->get_result(); // get the mysqli result
}

// look for normal non-rival flags for this rivaling claim.
/**
 * Returns the claims which flag $claimID and are not Thesis rivals.
 *
 * @param int $claimID
 * @param mysqli $conn the mysqli connection to use
 * @return mysqli_result|false
 */
function get_non_flag_rivals($claimID, $conn)
{
    $stmt = $conn->prepare("SELECT DISTINCT claimIDFlagger
        from claimsdb, flagsdb
        where claimIDFlagged = ?
        AND flagType NOT LIKE 'Thesis Rival'");
    $stmt->bind_param('i', $claimID);
    $stmt->execute();
    return $stmt->get_result();
}

/**
 * Returns the claims which flag $claimID and are not Thesis rivals.
 *
 * @param int $claimID
 * @param mysqli $conn the mysqli connection to use
 * @return mysqli_result|false
 */
function get_flagged_rivals($claimID, $conn)
{
    $stmt = $conn->prepare("SELECT DISTINCT claimIDFlagger
        from claimsdb, flagsdb
        where claimIDFlagged = ?
        AND flagType LIKE 'Thesis Rival'");
    $stmt->bind_param('i', $claimID);
    $stmt->execute();
    return $stmt->get_result();
}

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
    $claim = get_claim($claimID, $conn);
    if (!$claim) {
        return;
    }
    // IF THIS CLAIM IS A FLAGGER this obtains the FLAGGER'S flagtype's and flagged.
    // this is to find rival claims..this is literally JUST used for rivals.
    // rivals have to be flaggers and flagged.
    $flags = get_rival_flags($claimID, $conn);
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
        $result1 = get_non_flag_rivals($claimID, $conn); // get the mysqli result
        foreach ($result1 as $user) {
            sortclaims($user['claimIDFlagger']);
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
    $claim = get_claim($claimID, $conn);
    // look for normal non-rival flags for this rivaling claim.
    $result1 = get_flagged_rivals($claimID, $conn);
    foreach ($result1 as $user) {
        $rivaling = $user['claimIDFlagger'];
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
                $result1 = get_non_flag_rivals($claimID, $conn);
                while ($user = $result1->fetch_assoc()) {
                    sortclaims($user['claimIDFlagger']);
                }?>
        </ul><?php
} // end of rivalfunction
?>
