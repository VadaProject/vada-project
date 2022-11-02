<?php

require_once 'Database.php';
use Database\Database;

/**
 * Checks if there are any thesis flags against a claim.
 *
 * @param int $claimid The ID of a claim
 * @return bool true if there are any active thesis flags against the claim
 */
function doesThesisFlag($claimid)
{
    // TODO: this operation could probably be written as a single database query, using joins...
    $result = Database::getFlagsNotSupporting($claimid);
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
 * @param int $claimid The ID of a claim
 * @return bool true if
 */
function doesThesisFlagRival($claimid)
{
    $flaggers = Database::getThesisFlagsNotRivals($claimid);
    foreach ($flaggers as $flaggerID) {
        $flagger = Database::getClaim($flaggerID);
        if ($flagger && $flagger->active == 1) {
            return true;
        }
    }
    return false;
}
