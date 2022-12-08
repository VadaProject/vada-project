<?php

require_once 'Database.php';
use Database\Database;

/**
 * Checks if an individual claim has any active supports. If a claim has no
 *     active supports, this function sets it to inactive.
 *
 * @param int $claim_id
 * @return bool True if the claim has at least one active support
 */
function noSupports($claim_id)
{
    $has_active_supports = false;
    foreach (Database::getFlagsSupporting($claim_id) as $flag_id) {
        if (Database::isClaimActive($flag_id)) {
            $has_active_supports = true;
            return true;
        }
    }
    if (!$has_active_supports) {
        Database::setClaimActive($claim_id, false);
        return false;
    }
}
/**
 * Checks if an individual claim has any active supports
 *
 * @param int $claim_id the ID of the claim to check
 * @return bool True if the claim has at least one active support
 */
function noSupportsRival($claim_id)
{
    foreach (Database::getFlagsSupporting($claim_id) as $flag_id) {
        if (Database::isClaimActive($flag_id)) {
            return true;
        }
    }
    return false;
}
