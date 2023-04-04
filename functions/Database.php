<?php declare(strict_types=1);

namespace Database;

require_once __DIR__ . '/../config/db_connect.php';

class Database
{
    /**
     * @var \mysqli $conn The mysqli connection
     */
    public static $conn;

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
            'SELECT DISTINCT * from Claim where claimID = ?'
        );
        $stmt->bind_param('i', $claim_id);
        if (!$stmt->execute()) {
            error_log(self::$conn->error);
            self::$conn->rollback();
            exit(htmlspecialchars("A database error occured while querying claim #$claim_id."));
        }
        ;
        return $stmt->get_result()->fetch_object();
    }

    /**
     * @param int $claim_id
     * @return bool Whether the claim with the given ID is acrtive.
     */
    public static function isClaimActive(int $claim_id)
    {
        $stmt = self::$conn->prepare(
            'SELECT active from Claim where claimID = ?'
        );
        $stmt->bind_param('i', $claim_id);
        if (!$stmt->execute()) {
            error_log(self::$conn->error);
            exit(htmlspecialchars("A database error occured while querying claim #$claim_id."));
        }
        ;
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
            'UPDATE Claim SET active = ? WHERE claimID = ?'
        );
        $stmt->bind_param('ii', $active, $claimID);
        if (!$stmt->execute()) {
            error_log(self::$conn->error);
            exit(htmlspecialchars("A database error occured while updating claim #$claimID."));
        }
        ;
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
        }
        ;
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
        }
        ;
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
        from Claim, flagsdb where claimIDFlagged = ?
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
        }
        ;
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
     * Checks if an individual claim has any active supports
     *
     * @param int $claim_id the ID of the claim to check
     * @return bool True if the claim has at least one active support
     */
    public static function hasActiveSupports(int $claim_id)
    {
        $query = "SELECT COUNT(*) as total FROM flagsdb WHERE claimIDFlagged = ? AND flagType = 'supporting'";
        $stmt = self::$conn->prepare($query);
        $stmt->bind_param('i', $claimID);
        if (!$stmt->execute()) {
            error_log(self::$conn->error);
            exit(htmlspecialchars("A database error occured while querying claim #$claimID."));
        }
        $stmt->bind_result($total);
        return $total > 0;
    }

    /**
     * Gets the set of root claims for the current topic.
     *
     * @param int $topic Topic string
     * @return int[] List of claim IDs
     */
    public static function getAllRootClaimIDs(int $topic)
    {
        $query = 'SELECT DISTINCT claimID from Claim, flagsdb
        WHERE topic_id = ? AND claimID NOT IN (SELECT DISTINCT claimIDFlagger FROM flagsdb)';
        $stmt = self::$conn->prepare($query);
        $stmt->bind_param('i', $topic);
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
     * @param int $topic
     * @return int[] List of claim IDs
     */
    public static function getRootRivals(int $topic)
    {
        $query = 'SELECT DISTINCT Claim.claimID from Claim
        JOIN flagsdb ON flagsdb.claimIDFlagger = Claim.claimID
        WHERE Claim.topic = ? AND flagsdb.isRootRival = 1';
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
     * @param int $topic
     * @return int[] List of claim IDs
     */
    public static function getAllThesisRivals(int $topic)
    {
        $query = 'SELECT DISTINCT Claim.claimID from Claim
        JOIN flagsdb ON flagsdb.claimIDFlagger = Claim.claimID
        WHERE Claim.topic = ? AND flagsdb.flagType LIKE "Thesis Rival"';
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
    public static function getAllTopics()
    {
        $query = 'SELECT DISTINCT topic from Claim';
        $stmt = self::$conn->prepare($query);
        if (!$stmt->execute()) {
            error_log(self::$conn->error);
            exit(htmlspecialchars("A database error occured while getting topics."));
        }
        $result = $stmt->get_result();
        return array_column(mysqli_fetch_all($result, MYSQLI_NUM), 0);
    }

    // DATABASE CLAIM INSERTION
    public static function insertThesis(
        int $topic, string $subject, string $targetP, bool $active = true
    ) {
        $stmt = self::$conn->prepare("INSERT INTO Claim(subject, targetP, topic, active, COS) VALUES(?, ?, ?, ?, 'claim')");
        $stmt->bind_param("sssi", $subject, $targetP, $topic, $active);
        if (!$stmt->execute()) {
            echo 'query error: ' . mysqli_error(self::$conn);
        } else {
            return self::$conn->insert_id;
        }
    }
    public static function insertFlag(
        int $flagged_id, int $flagging_id, string $flagType, bool $isRootRival = false
    ) {
        $flag_stmt = self::$conn->prepare(
            "INSERT INTO flagsdb(claimIDFlagged, claimIDFlagger, flagType, isRootRival)
            VALUES(?, ?, ?, ?)"
        );
        $flag_stmt->bind_param("iisi", $flagged_id, $flagging_id, $flagType, $isRootRival);
        if (!$flag_stmt->execute()) { // fail
            echo 'query error: ' . mysqli_error(self::$conn);
            exit("Database error creating a flag relation.");
        }
    }
    public static function insertSupport(
        int $topic, int $flagged_id, string $subject, string $targetP, string $supportMeans, string $reason = null, string $example = null, string $url = null, string $citation = null, string $transcription = null, string $vidtimestamp = null
    ) {
        $support_stmt = self::$conn->prepare(
            "INSERT INTO Claim(subject, targetP, supportMeans, example, URL, reason, topic, vidtimestamp, citation, transcription, COS)
            VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'support')"
        );
        $support_stmt->bind_param("ssssssssss", $subject, $targetP, $supportMeans, $example, $url, $reason, $topic, $vidtimestamp, $citation, $transcription);
        if (!$support_stmt->execute()) { // fail
            echo 'query error: ' . mysqli_error(self::$conn);
            return false;
        }
        $support_id = self::$conn->insert_id;
        self::insertFlag($flagged_id, $support_id, 'supporting', false);
        return $support_id;
    }
    /** Returns whether a given claim is a root claim, i.e. whether it is not 
     * flagging any other claim */
    public static function isRootClaim(int $claim_id)
    {
        $stmt5 = self::$conn->prepare('SELECT DISTINCT claimID from Claim, flagsdb
    WHERE claimID = ? AND claimID NOT IN (SELECT DISTINCT claimIDFlagger FROM flagsdb)');
        $stmt5->bind_param('i', $claim_id);
        $stmt5->execute();
        $rootresult1 = $stmt5->get_result(); // get the mysqli result
        return mysqli_num_rows($rootresult1) > 0;
    }
}

Database::staticInit();