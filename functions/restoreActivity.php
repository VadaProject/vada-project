<?php

require_once 'Database.php';
require_once 'doesThesisFlag.php';
use Database\Database;

/**
 * @param int $claim_id The claim to check.
 * @return bool Returns true if a claim has an ACTIVE thesis rival
 */
function hasRival($claim_id)
{
    $thesis_rivals = Database::getThesisRivals($claim_id);
    return count($thesis_rivals) > 0;
}

/// TODO: rewrite these functions to be simpler and more efficient.
/*
IF a claim is a claim/flag that is NOT rivalling another:
- it is active iff:
- it has at least one support
- AND it has no active flags
IF a claim is a support:
- it is active iff:
- it has no active flags
IF a claim rivals another claim:
- it is active iff:
- AND it has at least one active support
- AND it has no active flags
- AND its rival should be inactive
- (i.e. it has no active support/or it has active flags.)

Clarity: if both rivals are supported and unflagged (i.e. they should be active), they are both inactive. otherwise they follow normal activity rules.

claim IDs loosely order the tree, so we can just iterate backwards 
across those instead of recursing.
there should be a separate function that simply determines true/false if a 
claim should be active, based on its children and rivals.
rivals currently need to be resolved together. wonder what to do about this.
*/

function restoreActivityTopic(string $topic)
{
    // Recalculate activity relationships
    $root_claim = Database::getAllRootClaimIDs($topic);
    $thesis_rivals = Database::getAllThesisRivals($topic);
    $root_rivals = Database::getRootRivals($topic);
    for ($i = 0; $i < 2; $i++) {
        // NOTE: we run this whole routine twice because some flagging relationships take two iterations to fully resolve.
        // weird issue that should be fixed.
        foreach ($root_claim as $claim_id) {
            restoreActivity($claim_id);
        }
        foreach ($root_rivals as $claim_id) {
            restoreActivityRIVAL($claim_id);
        }
        foreach ($thesis_rivals as $claim_id) {
            restoreActivityRIVAL($claim_id);
        }
    }
}

/**
 * Checks the flagging relationships of each claim, and
 *   determines whether it is contested or not.
 *
 * @param int $claim_id The ID of the root claim to start at.
 * @return void
 */
function restoreActivity($claim_id)
{
    // grabs supports for initial claim NUMBER ONE ON DIAGRAM, RED
    $supports = Database::getSupportingClaims($claim_id);
    if (count($supports) == 0) {
        if (
            !hasActiveFlags($claim_id) &&
            !hasRival($claim_id)
        ) {
            Database::setClaimActive($claim_id, true);
        }
    }
    $hasActiveSupport = false;
    foreach ($supports as $support_id) {
        // $claim_id is the original claim. $support_id is the support.
        // check to see if all the supports are inactive.
        // OR if ONE support is active.

        // is this support active? if so, reactivate it.
        // we only need one to reactivate the claim.
        if (
            Database::isClaimActive($support_id) &&
            !hasActiveFlags($claim_id) &&
            !hasRival($claim_id)
        ) {
            // Database::setClaimActive($claim_id, true);
            $hasActiveSupport = true;
        }
        restoreActivity($support_id);

        // /////////////////////////////////////////////////////// NUMBER TWO ON DIAGRAM, ORANGE
        // below grabs all flaggers for the support and JUST the support. not the claims.  - act3, s3, activity3

        // this is for rivals
        foreach (Database::getThesisRivals($support_id) as $rival_id) {
            restoreActivityRIVAL($rival_id);
            // below should get the companion rival
            $companion_rivals = Database::getThesisRivals($rival_id);
            foreach ($companion_rivals as $companion_rivals) {
                restoreActivityRIVAL($companion_rivals);
            }
        }
        $non_rivaling_flags = Database::getNonRivalFlags($support_id);
        foreach ($non_rivaling_flags as $active_flag_id) {
            restoreActivity($active_flag_id);

            // If the flag is active, then the support is inactive.
            if (Database::isClaimActive($active_flag_id)) {
                Database::setClaimActive($support_id, false);
            }
        }
    }
    $isSupport = Database::getClaim($claim_id)->COS == "support";

    // this needs to be checking thesis flags for root claims
    // GRABS ALL FLAGS OF ORIGINAL CLAIM ---------------------------- BLUE ON DIAGRAM, 3
    // grabs all flaggers for non-rival root claims
    // all tooearly or toolate //$activity
    // *AND* all support flags because while it doesn't occur for the first run through, when a support is put into the parameters, it'll check all reason/rule flags
    $flags = Database::getThesisFlagsNotRival($claim_id);
    $hasActiveFlag = false;
    foreach ($flags as $flag_id) {
        restoreActivity($flag_id);
        if (Database::isClaimActive($flag_id)) {
            Database::setClaimActive($claim_id, false);
            $hasActiveFlag = true;
        }
        foreach (Database::getThesisRivals($claim_id) as $thesis_rival_id) {
            restoreActivityRIVAL($thesis_rival_id);
        }
    }
    if (!hasRival($claim_id)) {
        Database::setClaimActive($claim_id, 
        ($isSupport || $hasActiveSupport) && !$hasActiveFlag);
    }
}
/**
 * This function has the same functionality as restoreActivity, but for rivals;
 *     The key difference is it must account for the “mutualistic flagging"
 *     relationship that is unique to rivals (that is, they flag each other
 *     equally). This function determines when one of the rival claims may
 *     reach an uncontested state (as the typical state for a rivals pair is
 *     equal contestation).
 *
 * @param int $claim_id The root claim ID to check
 */
function restoreActivityRIVAL($claim_id)
{
    // Finds the flagger, and continues the recursion by invoking
    // restoreActivity
    // set of all too-early and too-late
    // looks for normal non-rival flags for this rivaling claim.
    foreach (Database::getNonRivalFlags($claim_id) as $non_rival_flag_id) {
        restoreActivity($non_rival_flag_id);
    }
    // check active status of flagging claims OF RIVAL COMPANION
    // finds the companion
    $rivaling = '';
    foreach (Database::getThesisRivals($claim_id) as $thesis_rival_id) {
        // found rival pair!
        $rivaling = $thesis_rival_id;
        // $rivaling is Rival B.
    }
    // above finds rival A's companion, aka rival b.
    // above is to check active status of flagging claims OF RIVAL COMPANION
    // this is finding the flaggers for rival B
    // look for normal non-rival flags for this rivaling claim.
    // Checks active status of flagging claims OF INITIAL RIVAL
    foreach (Database::getNonRivalFlags($rivaling) as $rivals_flag_id) {
        restoreActivity($rivals_flag_id);
    }
    // rivalA : supportless --> rivalb should be active. does rivalb have active TE/TL?
    // rivalB : needs to be active AND it doesn't have a too early / too late AND needs at least one support itself
    $isChallengedThis = !Database::hasActiveSupports($claim_id) || hasActiveFlagsNonRival($claim_id);
    $isChallengedRival = !Database::hasActiveSupports($rivaling) || hasActiveFlagsNonRival($rivaling);

    if ($isChallengedThis === $isChallengedRival) {
        Database::setClaimActive($claim_id, false);
        Database::setClaimActive($rivaling, false);
    } elseif (!$isChallengedThis) {
        Database::setClaimActive($claim_id, true);
        Database::setClaimActive($rivaling, false);
    } else {
        Database::setClaimActive($claim_id, false);
        Database::setClaimActive($rivaling, true);
    }
}