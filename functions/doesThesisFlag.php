<?php

require_once 'Database.php';
use Database\Database;

/**
 * Checks if there are any thesis flags against a claim.
 *
 * @param int $claim_id The ID of a claim
 * @return bool true if there are any active thesis flags against the claim
 */
function hasActiveFlags(int $claim_id)
{
    // TODO: this operation could probably be written as a single database query, using joins...
    $result = Database::getFlagsNotSupporting($claim_id);
    foreach ($result as $flaggerID) {
        $flagger = Database::getClaim($flaggerID);
        if ($flagger->active == 1) {
            return true;
        }
    }
    return false;
}

/**
 * Checks if there are any thesis flags against a claim that AREN’T rivals.
 *
 * @param int $claim_id The ID of a claim
 * @return bool true if
 */
function hasActiveFlagsNonRival(int $claim_id)
{
    $flaggers = Database::getThesisFlagsNotRival($claim_id);
    foreach ($flaggers as $flaggerID) {
        $flagger = Database::getClaim($flaggerID);
        if ($flagger && $flagger->active == 1) {
            return true;
        }
    }
    return false;
}
