<?php

namespace Database;

class Database
{
/////////////////////////
// PREPARED STATEMENTS //
/////////////////////////

    /**
     * @param int $claimID
     * @param mysqli $conn the mysqli connection to use
     * @return array an associative array of strings representing the claim
     */
    public static function getClaim($claimID, $conn)
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
    public static function getRivalFlags($claimID, $conn)
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
    public static function getNonRivalFlags($claimID, $conn)
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
    public static function getFlaggedRivals($claimID, $conn)
    {
        $stmt = $conn->prepare("SELECT DISTINCT claimIDFlagger
            from claimsdb, flagsdb
            where claimIDFlagged = ?
            AND flagType LIKE 'Thesis Rival'");
        $stmt->bind_param('i', $claimID);
        $stmt->execute();
        return $stmt->get_result();
    }
}
