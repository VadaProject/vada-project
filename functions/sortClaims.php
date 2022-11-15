<?php

require_once 'Database.php';
use Database\Database;

/*
This function displays each individual claim in a recursive manner.
Each recursion is a series of tracking relationships between the claims (found in the Flabsdb).
*/

function get_image($name)
{
    return '<img class="icon--' .
        $name .
        '" src="assets/img/' .
        $name .
        '.png">';
}

//////////////////////////////////////////////
// HTML
//////////////////////////////////////////////
function make_label_el($claim_id, $claim, $flag_type, $rivalling = '')
{
    ?>
    <input id="<?php echo $claim_id; ?>" type="checkbox" name="active_claim">
    <label class="claim"
        <?php if ($rivalling) { ?>style="background:#FFFFE0"<?php } ?>
        for="<?php echo $claim_id; ?>">
    <?php
    switch ($flag_type) {
        case 'supporting':
            echo get_image('support');
            echo '<div class="">' .
                htmlspecialchars($claim->supportMeans) .
                '</div>';
            if ($claim->supportMeans == 'Inference') {
                $reason =
                    htmlspecialchars($claim->subject) .
                    ' ' .
                    htmlspecialchars($claim->reason);
                $rule =
                    'Whatever/Whomever ' .
                    htmlspecialchars($claim->reason) .
                    ', ' .
                    htmlspecialchars($claim->targetP) .
                    ' as in the case of ' .
                    htmlspecialchars($claim->example) .
                    '';
                echo '<div class="claim_body"><b>Reason:</b> ' .
                    $reason .
                    '</div>';
                echo '<div class="claim_body"><b>Rule & Example:</b> ' .
                    $rule .
                    '</div>';
            }
            if (
                $claim->supportMeans == 'Testimony' ||
                $claim->supportMeans == 'Perception'
            ) {
                echo '<div class="claim_body">Citation: ' .
                    htmlspecialchars($claim->citation) .
                    '</div>';
            }
            break;
        case '':
            if ($rivalling) {
                echo get_image('rivals');
                echo '<h4>Contests #' . $rivalling . '</h4>';
            }
            echo '<h1>Thesis</h1>';
            echo '<div class="claim_body">' .
                $claim->subject .
                ' ' .
                $claim->targetP .
                '</div>';
            break;
        default:
            echo get_image('flag');
            echo '<div class="claim_body">';
            echo 'Flagged: ' . $flag_type . '';
            echo '<h1 class="claim_body">Thesis</h1>';
            echo '<p>' . $claim->subject . ' ' . $claim->targetP . '</p>';
            echo '</div>';
    }
    echo '<div>#' . $claim_id . '</div>';

    // add is subject person or object to inference div

    // FONT CHANGING
    if ($claim->active != 1) {
        echo get_image('alert');
    }
    ?>
    <div class="claim_body">
        <a class="btn btn-primary"
        href="details.php?id=<?php echo $claim_id; ?>">
        Details
        </a>
    </div>
    </label>
    <?php
}

// starts two chains of recursion. one with normal root claims.
// the other with root rivals. the rivals, of course, are put into the rival recursion.
function sortClaims($claimID)
{
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
        // echo 'The flag ' . $claimIDFlagger . ' has a rival!: ' . '';
        // for THIS claimID - check for flaggers that aren't rival .. sort claim those
        sortClaimsRival($claimIDFlagger);
        // for the CORRESPONDING claimID - check for flaggers that aren't rival .. sort claim those.
        sortClaimsRival($claimIDFlagged);
        return;
    }
    ?>
    <li>
        <?php make_label_el($claimID, $claim, $resultFlagType); ?>
        <ul>
        <?php
        // IF A CLAIM IS FLAGGED IT obtains flaggers that aren't rivals
        // if its a thesis rival it will show up in the query above
        // this is when the claim is the flagged. this is what gets pushed in the recursion.
        // continue recursion
        $result1 = Database::getNonRivalFlags($claimID); // get the mysqli result
        foreach ($result1 as $id) {
            sortclaims($id);
        }?>
    </ul><?php
}

/*
This function has the same functionality as the sortClaims, but for rivals.
The key difference is handling the “mutualistic flagging” relationship that is unique to rivals (that is, they flag each other equally).
It breaks an infinite loop that would otherwise occur if a rival was handled recursively in sortClaims().
*/

function sortClaimsRIVAL($claimID)
{
    // get the info for the claim being flagged
    $claim = Database::getClaim($claimID);
    // look for normal non-rival flags for this rivaling claim.
    $result1 = Database::getFlaggedRivals($claimID);
    foreach ($result1 as $flagID) {
        $rivaling = $flagID;
    }
    ?>

        <li>
        <?php make_label_el($claimID, $claim, '', $rivaling); ?>
        <ul>
            <!--</font>-->
                <?php
                $result1 = Database::getNonRivalFlags($claimID);
                foreach ($result1 as $flagID) {
                    sortClaims($flagID);
                }?>
        </ul><?php
}
// end of rivalfunction
?>
