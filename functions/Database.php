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
     * @return \array an associative array of strings representing the claim
     */
    public static function getClaim($claimID)
    {
        $stmt = self::$conn->prepare(
            'SELECT DISTINCT * from claimsdb where claimID = ?'
        );
        $stmt->bind_param('i', $claimID);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    /**
     * @param int $claimID
     * @return
     */
    public static function getRivalFlags($claimID)
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
     * Returns the ID of each claim which flags $claimID is a Rival.
     *
     * @param int $claimID
     * @return \int[]
     */
    public static function getFlaggedRivals($claimID)
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
     * @param int $claimID
     * @return \int[] The IDs of each claim which flags $claimID and is not a Support.
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
     * @param int $claimID
     * @return \int[] The IDs of claims which flag $claimID and aren't type Too Early or Too Late
     */
    public static function getFlagsNotRivals($claimID)
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
     * Helper function
     * @param \mysqli_result $result
     * @param \string $column
     * @return \array
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
