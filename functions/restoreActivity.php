<?php

require_once 'Database.php';
use Database\Database;

/**
 * @param int $claim_id The claim to check.
 * @return bool Returns true if a claim has an ACTIVE thesis rival
 */
function hasActiveRival($claim_id)
{
    // TODO: if we do it a lot, this operation could probably be reduced to a SQL query
    $flaggers = Database::getThesisRivals($claim_id);
    foreach ($flaggers as $flag_id) {
        if (Database::isClaimActive($flag_id)) {
            return true;
        }
    }
    return false;
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
            !doesThesisFlag($claim_id) &&
            !hasActiveRival($claim_id)
        ) {
            Database::setClaimActive($claim_id, true);
        }
    }
    // else
    foreach ($supports as $support_id) {
        // $claim_id is the original claim. $support_id is the support.
        // check to see if all the supports are inactive.
        // OR if ONE support is active.

        // is this support active? if so, reactivate it.
        // we only need one to reactivate the claim.
        if (
            Database::isClaimActive($support_id) &&
            !doesThesisFlag($claim_id) &&
            !hasActiveRival($claim_id)
        ) {
            Database::setClaimActive($claim_id, true);
        }
        // are all supports inactive? claim is inactive.
        if (!doesThesisFlag($claim_id)) {
            noSupports($claim_id);
        }
        restoreActivity($support_id);

        // /////////////////////////////////////////////////////// NUMBER TWO ON DIAGRAM, ORANGE
        // below grabs all flaggers for the support and JUST the support. not the claims.  - act3, s3, activity3

        // this is for rivals
        foreach (Database::getThesisRivals($support_id) as $rival_id) {
            restoreActivityRIVAL($rival_id);
            // below should get the companion rival
            $companion_rival = Database::getThesisRivals($rival_id);
            restoreActivityRIVAL($companion_rival);
        }
        $non_rivalling_flags = Database::getNonRivalFlags($support_id);
        foreach ($non_rivalling_flags as $active_flag_id) {
            restoreActivity($active_flag_id);

            // If the flag is active, then the support is inactive.
            if (Database::isClaimActive($active_flag_id)) {
                Database::setClaimActive($support_id, false);
            }
        }
    }

    // this needs to be checking thesis flags for root claims
    // GRABS ALL FLAGS OF ORIGINAL CLAIM ---------------------------- BLUE ON DIAGRAM, 3
    // grabs all flaggers for non-rival root claims
    // all tooearly or toolate //$activity
    // *AND* all support flags because while it doesn't occur for the first run through, when a support is put into the parameters, it'll check all reason/rule flags
    $flags = Database::getFlagsNotThesisRivalNotSupporting($claim_id);
    foreach ($flags as $flag_id) {
        restoreActivity($flag_id);
        if (Database::isClaimActive($flag_id)) {
            Database::setClaimActive($claim_id, false);
        } else {
            Database::setClaimActive($claim_id, true);
        }
        foreach (Database::getThesisRivals($claim_id) as $thesis_rival_id) {
            restoreActivityRIVAL($thesis_rival_id);
        }
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
    foreach (Database::getNonRivalFlags($claim_id) as $nodeFlaggers) {
        restoreActivity($nodeFlaggers);
    }
    // check active status of flagging claims OF RIVAL COMPANION
    // finds the companion
    $rivaling = '';
    foreach (Database::getThesisRivals($claim_id) as $r) {
        // found rival pair!
        $rivaling = $r;
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

    $statusA = '';
    $statusB = '';

    // rivalA : supportless --> rivalb should be active. does rivalb have active TE/TL?
    // rivalB : needs to be active AND it doesn't have a too early / too late AND needs at least one support itself
    if (noSupportsRival($claim_id) && !doesThesisFlagRival($claim_id)) {
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
        Database::setClaimActive($claim_id, false);
        Database::setClaimActive($rivaling, false);
    }
    // if its true, there are no flags.
    // if false, there are flags.
    if ('unchallenged' == $statusA && 'challenged' == $statusB) {
        Database::setClaimActive($claim_id, true);
        Database::setClaimActive($rivaling, false);
    }
    if ('unchallenged' == $statusB && 'challenged' == $statusA) {
        Database::setClaimActive($claim_id, false);
        Database::setClaimActive($rivaling, true);
    }
}
