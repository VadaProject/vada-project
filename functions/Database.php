<?php declare(strict_types=1);

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
     * @param int $claim_id
     * @return object|null A claim object
     */
    public static function getClaim(int $claim_id)
    {
        $stmt = self::$conn->prepare(
            'SELECT DISTINCT * from claimsdb where claimID = ?'
        );
        $stmt->bind_param('i', $claim_id);
        if (!$stmt->execute()) {
            error_log(self::$conn->error);
            exit(htmlspecialchars("A database error occured while querying claim #$claim_id."));
        };
        return $stmt->get_result()->fetch_object();
    }

    /**
     * @param int $claim_id
     * @return bool Whether the claim with the given ID is acrtive.
     */
    public static function isClaimActive(int $claim_id)
    {
        $stmt = self::$conn->prepare(
            'SELECT active from claimsdb where claimID = ?'
        );
        $stmt->bind_param('i', $claim_id);
        if (!$stmt->execute()) {
            error_log(self::$conn->error);
            exit(htmlspecialchars("A database error occured while querying claim #$claim_id."));
        };
        $res = $stmt->get_result()->fetch_column(0);
        if (!isset($res)) {
            return false;
        }
        return $res;
    }

    /**
     * @param int $claimID
     * @param bool $active
     */
    public static function setClaimActive(int $claimID, bool $active)
    {
        $stmt = self::$conn->prepare(
            'UPDATE claimsdb SET active = ? WHERE claimID = ?'
        );
        $stmt->bind_param('ii', $active, $claimID);
        if (!$stmt->execute()) {
            error_log(self::$conn->error);
            exit(htmlspecialchars("A database error occured while updating claim #$claimID."));
        };
    }

    /**
     * @param int $claim_id
     * @return int|null The claim which is flagged by claimID
     */
    public static function getFlaggedClaim(int $claim_id)
    {
        $stmt = self::$conn->prepare(
            'SELECT DISTINCT * from flagsdb WHERE claimIDFlagger = ?'
        );
        $stmt->bind_param('i', $claim_id);
        if (!$stmt->execute()) {
            error_log(self::$conn->error);
            exit(htmlspecialchars("A database error occured while querying claim #$claim_id."));
        };
        foreach ($stmt->get_result() as $row) {
            return $row["claimIDFlagged"];
        }
        return;
    }

    /**
     * Gets the set of claims which are flagged by the current claim.
     *
     * @param int $claimID Current claim ID
     * @return array List of claim IDs
     */
    public static function getFlaggedClaims(int $claimID)
    {
        $stmt = self::$conn->prepare(
            'SELECT DISTINCT * from flagsdb WHERE claimIDFlagger = ?'
        );
        $stmt->bind_param('i', $claimID);
        if (!$stmt->execute()) {
            error_log(self::$conn->error);
            exit(htmlspecialchars("A database error occured while querying claim #$claimID."));
        };
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // look for normal non-rival flags for this rivaling claim.
    /**
     * Returns the ID of each non-rival claim which flags $claimID
     *
     * @param int $claimID
     * @return int[]
     */
    public static function getNonRivalFlags(int $claimID)
    {
        $query = "SELECT DISTINCT claimIDFlagger
        from claimsdb, flagsdb where claimIDFlagged = ?
        AND flagType NOT LIKE 'Thesis Rival'";
        $stmt = self::$conn->prepare($query);
        $stmt->bind_param('i', $claimID);
        if (!$stmt->execute()) {
            error_log(self::$conn->error);
            exit(htmlspecialchars("A database error occured while querying claim #$claimID."));
        }
        $res = $stmt->get_result();
        return self::getColumnAsIntArray($res);
    }

    /**
     * Gets the set of Thesis Rivals which flag the current claim.
     *
     * @param int $claimID Current claim ID
     * @return int[] List of claim IDs
     */
    public static function getThesisRivals(int $claimID)
    {
        $query = "SELECT DISTINCT claimIDFlagger
        from flagsdb where claimIDFlagged = ?
        AND flagType LIKE 'Thesis Rival'";
        $stmt = self::$conn->prepare($query);
        $stmt->bind_param('i', $claimID);
        if (!$stmt->execute()) {
            error_log(self::$conn->error);
            exit(htmlspecialchars("A database error occured while querying claim #$claimID."));
        };
        $res = $stmt->get_result();
        return self::getColumnAsIntArray($res);
    }

    /**
     * Gets all claims that flag the current claim and aren't Supporting.
     *
     * @param int $claimID Current claim ID
     * @return int[] List of claim IDs
     */
    public static function getFlagsNotSupporting(int $claimID)
    {
        $query = "SELECT DISTINCT claimIDFlagger
        from flagsdb WHERE claimIDFlagged = ?
        and flagType NOT LIKE 'supporting'";
        $stmt = self::$conn->prepare($query);
        $stmt->bind_param('i', $claimID);
        if (!$stmt->execute()) {
            error_log(self::$conn->error);
            exit(htmlspecialchars("A database error occured while querying claim #$claimID."));
        }
        $res = $stmt->get_result();
        return self::getColumnAsIntArray($res);
    }

    /**
     * Gets all Support claims that flag the current claim.
     *
     * @param int $claimID Current claim ID
     * @return int[] List of claim IDs
     */
    public static function getSupportingClaims(int $claimID)
    {
        $query = "SELECT DISTINCT claimIDFlagger
        from flagsdb WHERE claimIDFlagged = ?
        and flagType LIKE 'supporting'";
        $stmt = self::$conn->prepare($query);
        $stmt->bind_param('i', $claimID);
        if (!$stmt->execute()) {
            error_log(self::$conn->error);
            exit(htmlspecialchars("A database error occured while querying claim #$claimID."));
        }
        $res = $stmt->get_result();
        return self::getColumnAsIntArray($res);
    }

    /**
     * Gets all flags that aren't Supports or Thesis Rivals.
     */
    public static function getThesisFlagsNotRival(int $claimID)
    {
        // TODO: what a stupid name
        $query = "SELECT DISTINCT claimIDFlagger
        from flagsdb
        WHERE claimIDFlagged = ? and flagType NOT LIKE 'Thesis Rival' and flagType NOT LIKE 'supporting'";
        $stmt = self::$conn->prepare($query);
        $stmt->bind_param('i', $claimID);
        if (!$stmt->execute()) {
            error_log(self::$conn->error);
            exit(htmlspecialchars("A database error occured while querying claim #$claimID."));
        }
        $res = $stmt->get_result();
        return self::getColumnAsIntArray($res);
    }

    /**
     * Gets the set of root claims for the current topic.
     *
     * @param string $topic Topic string
     * @return int[] List of claim IDs
     */
    public static function getAllRootClaimIDs(string $topic)
    {
        $query = 'SELECT DISTINCT claimID from claimsdb, flagsdb
        WHERE topic = ? AND claimID NOT IN (SELECT DISTINCT claimIDFlagger FROM flagsdb)';
        $stmt = self::$conn->prepare($query);
        $stmt->bind_param('s', $topic);
        if (!$stmt->execute()) {
            error_log(self::$conn->error);
            exit(htmlspecialchars("A database error occured while querying topic $topic."));
        }
        $res = $stmt->get_result();
        return self::getColumnAsIntArray($res);
    }

    /**
     * Gets the set of claims for a given topic which have isRootRival set.
     *
     * @param string $topic
     * @return int[] List of claim IDs
     */
    public static function getRootRivals(string $topic)
    {
        $query = 'SELECT DISTINCT claimsdb.claimID from claimsdb
        JOIN flagsdb ON flagsdb.claimIDFlagger = claimsdb.claimID
        WHERE claimsdb.topic = ? AND flagsdb.isRootRival = 1';
        $stmt = self::$conn->prepare($query);
        $stmt->bind_param('s', $topic);
        if (!$stmt->execute()) {
            error_log(self::$conn->error);
            exit(htmlspecialchars("A database error occured while querying topic $topic."));
        }
        $res = $stmt->get_result();
        return self::getColumnAsIntArray($res);
    }

    /**
     * Gets the set of claims for a given topic which are thesis rivals
     *
     * @param string $topic
     * @return int[] List of claim IDs
     */
    public static function getAllThesisRivals(string $topic)
    {
        $query = 'SELECT DISTINCT claimsdb.claimID from claimsdb
        JOIN flagsdb ON flagsdb.claimIDFlagger = claimsdb.claimID
        WHERE claimsdb.topic = ? AND flagsdb.flagType LIKE "Thesis Rival"';
        $stmt = self::$conn->prepare($query);
        $stmt->bind_param('s', $topic);
        if (!$stmt->execute()) {
            error_log(self::$conn->error);
            exit(htmlspecialchars("A database error occured while querying topic $topic."));
        }
        $res = $stmt->get_result();
        return self::getColumnAsIntArray($res);
    }
    /**
     * Helper function: iterate a SQL result and collect it as a single array
     * @param \mysqli_result $result A mysqli result object to iterate
     * @param int $column The column to get. (default 0)
     * @return int[] The list of values
     */
    private static function getColumnAsIntArray(\mysqli_result $result, int $column_i = 0)
    {
        // fetch all rows from the result object as an array
        $rows = $result->fetch_all();
        // use array_column to extract the first column as an array
        $column_array = array_column($rows, $column_i); // get nth column
        return array_map('intval', $column_array); // to int
    }

    /**
     * @return string[] The list of topic names
     */
    public static function getAllTopics() {
        $query = 'SELECT DISTINCT topic from claimsdb';
        $stmt = self::$conn->prepare($query);
        if (!$stmt->execute()) {
            error_log(self::$conn->error);
            exit(htmlspecialchars("A database error occured while getting topics."));
        }
        $result = $stmt->get_result();
        return array_column(mysqli_fetch_all($result,  MYSQLI_NUM), 0);
    }
}

Database::staticInit();
