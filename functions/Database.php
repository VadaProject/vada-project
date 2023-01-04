<?php

namespace Database;

require_once __DIR__ . '/../config/db_connect.php';

class Database
{
    /**
     * @var \mysqli $conn The mysqli connection
     */
    private static $conn;

    public static function staticInit()
    {
        self::$conn = db_connect();
    }

    /**
     * @param int $claimID
     * @return \object|\null A claim object
     */
    public static function getClaim($claimID)
    {
        $stmt = self::$conn->prepare(
            'SELECT DISTINCT * from claimsdb where claimID = ?'
        );
        $stmt->bind_param('i', $claimID);
        $stmt->execute();
        return $stmt->get_result()->fetch_object();
    }

    public static function isClaimActive($claimID)
    {
        $stmt = self::$conn->prepare(
            'SELECT active from claimsdb where claimID = ?'
        );
        $stmt->bind_param('i', $claimID);
        $stmt->execute();
        return $stmt->get_result()->fetch_object()->active;
    }

    /**
     * @param int $claimID
     * @param bool $active
     */
    public static function setClaimActive($claimID, $active)
    {
        $active_int = $active ? 1 : 0; // turn bool param into int
        // TODO: the database stores this as an int but it should be a bool
        $stmt = self::$conn->prepare(
            'UPDATE claimsdb SET active = ? WHERE claimID = ?'
        );
        $stmt->bind_param('ii', $active_int, $claimID);
        $stmt->execute();
    }

    /**
     * @param int $claim_id
     * @return \int|null The claim which is flagged by claimID
     */
    public static function getFlaggedClaim($claim_id)
    {
        $stmt = self::$conn->prepare(
            'SELECT DISTINCT * from flagsdb WHERE claimIDFlagger = ?'
        );
        $stmt->bind_param('i', $claim_id);
        $stmt->execute();
        foreach ($stmt->get_result() as $row) {
            return $row["claimIDFlagged"];
        }
        return;
    }

    /**
     * Gets the set of claims which are flagged by the current claim.
     *
     * @param int $claimID Current claim ID
     * @return \int[] List of claim IDs
     */
    public static function getFlaggedClaims($claimID)
    {
        $stmt = self::$conn->prepare(
            'SELECT DISTINCT * from flagsdb WHERE claimIDFlagger = ?'
        );
        $stmt->bind_param('i', $claimID);
        $stmt->execute();
        return $stmt->get_result(); // get the mysqli result
    }

    // look for normal non-rival flags for this rivaling claim.
    /**
     * Returns the ID of each non-rival claim which flags $claimID
     *
     * @param int $claimID
     * @return \int[]
     */
    public static function getNonRivalFlags($claimID)
    {
        $query = "SELECT DISTINCT claimIDFlagger
        from claimsdb, flagsdb where claimIDFlagged = ?
        AND flagType NOT LIKE 'Thesis Rival'";
        $stmt = self::$conn->prepare($query);
        $stmt->bind_param('i', $claimID);
        $stmt->execute();
        $res = $stmt->get_result();
        return self::getColumnAsArray($res, 'claimIDFlagger');
    }

    /**
     * Gets the set of Thesis Rivals which flag the current claim.
     *
     * @param int $claimID Current claim ID
     * @return \int[] List of claim IDs
     */
    public static function getThesisRivals($claimID)
    {
        $query = "SELECT DISTINCT claimIDFlagger
        from flagsdb where claimIDFlagged = ?
        AND flagType LIKE 'Thesis Rival'";
        $stmt = self::$conn->prepare($query);
        $stmt->bind_param('i', $claimID);
        $stmt->execute();
        $res = $stmt->get_result();
        return self::getColumnAsArray($res, 'claimIDFlagger');
    }

    /**
     * Gets all claims that flag the current claim and aren't Supporting.
     *
     * @param int $claimID Current claim ID
     * @return \int[] List of claim IDs
     */
    public static function getFlagsNotSupporting($claimID)
    {
        $query = "SELECT DISTINCT claimIDFlagger
        from flagsdb WHERE claimIDFlagged = ?
        and flagType NOT LIKE 'supporting'";
        $stmt = self::$conn->prepare($query);
        $stmt->bind_param('i', $claimID);
        $stmt->execute();
        $res = $stmt->get_result();
        return self::getColumnAsArray($res, 'claimIDFlagger');
    }

    /**
     * Gets all Support claims that flag the current claim.
     *
     * @param int $claimID Current claim ID
     * @return \int[] List of claim IDs
     */
    public static function getSupportingClaims($claimID)
    {
        $query = "SELECT DISTINCT claimIDFlagger
        from flagsdb WHERE claimIDFlagged = ?
        and flagType LIKE 'supporting'";
        $stmt = self::$conn->prepare($query);
        $stmt->bind_param('i', $claimID);
        $stmt->execute();
        $res = $stmt->get_result();
        return self::getColumnAsArray($res, 'claimIDFlagger');
    }

    /**
     * Gets all Too Early and Too Late flags flagging the current claim.
     *
     * @param int $claimID Current claim ID
     * @return \int[] List of claim IDs
     */
    public static function getFlagsTooEarlyTooLate($claimID)
    {
        $query = "SELECT DISTINCT claimIDFlagger
        from flagsdb WHERE claimIDFlagged = ?
        and (flagType LIKE 'Too Early' OR flagType LIKE 'Too Late')";
        // TODO: are these really the only flagTypes we want?
        $stmt = self::$conn->prepare($query);
        $stmt->bind_param('i', $claimID);
        $stmt->execute();
        $res = $stmt->get_result();
        return self::getColumnAsArray($res, 'claimIDFlagger');
    }

    /**
     * Gets all flags that aren't Supports or Thesis Rivals.
     */
    public static function getFlagsNotThesisRivalNotSupporting($claimID)
    {
        // TODO: what a stupid name
        $query = "SELECT DISTINCT claimIDFlagger
        from flagsdb
        WHERE claimIDFlagged = ? and flagType NOT LIKE 'Thesis Rival' and flagType NOT LIKE 'supporting'";
        $stmt = self::$conn->prepare($query);
        $stmt->bind_param('i', $claimID);
        $stmt->execute();
        $res = $stmt->get_result();
        return self::getColumnAsArray($res, 'claimIDFlagger');
    }

    /**
     * Gets the set of root claims for the current topic.
     *
     * @param string $topic Topic string
     * @return \int[] List of claim IDs
     */
    public static function getAllRootClaimIDs($topic)
    {
        $query = 'SELECT DISTINCT claimID from claimsdb, flagsdb
        WHERE topic = ? AND claimID NOT IN (SELECT DISTINCT claimIDFlagger FROM flagsdb)';
        $stmt = self::$conn->prepare($query);
        $stmt->bind_param('s', $topic);
        $stmt->execute();
        $res = $stmt->get_result();
        return self::getColumnAsArray($res, 'claimID');
    }

    /**
     * Gets the set of claims for a given topic which have isRootRival set.
     *
     * @param string $topic
     * @return \int[] List of claim IDs
     */
    public static function getRootRivals($topic)
    {
        $query = 'SELECT DISTINCT claimsdb.claimID from claimsdb
        JOIN flagsdb ON flagsdb.claimIDFlagger = claimsdb.claimID
        WHERE claimsdb.topic = ? AND flagsdb.isRootRival = 1';
        $stmt = self::$conn->prepare($query);
        $stmt->bind_param('s', $topic);
        $stmt->execute();
        $res = $stmt->get_result();
        return self::getColumnAsArray($res, 'claimID');
    }

    /**
     * Gets the set of claims for a given topic which are thesis rivals
     *
     * @param string $topic
     * @return \int[] List of claim IDs
     */
    public static function getAllThesisRivals($topic)
    {
        $query = 'SELECT DISTINCT claimsdb.claimID from claimsdb
        JOIN flagsdb ON flagsdb.claimIDFlagger = claimsdb.claimID
        WHERE claimsdb.topic = ? AND flagsdb.flagType LIKE "Thesis Rival"';
        $stmt = self::$conn->prepare($query);
        $stmt->bind_param('s', $topic);
        $stmt->execute();
        $res = $stmt->get_result();
        return self::getColumnAsArray($res, 'claimID');
    }
    /**
     * Helper function: iterate a SQL result and collect it as a single array
     *
     * @param \mysqli_result $result A mysqli result object to iterate
     * @param \string $column The name of the column to get.
     * @return \array The list of values
     */
    private static function getColumnAsArray($result, $column)
    {
        $vals = [];
        foreach ($result as $row) {
            $vals[] = $row[$column];
        }
        return $vals;
    }
}

Database::staticInit();
