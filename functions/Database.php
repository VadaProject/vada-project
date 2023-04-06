<?php declare(strict_types=1);
namespace Database;

require_once __DIR__ . '/../config/db_connect.php';
class Database
{
    /**
     * @var \PDO $conn The mysqli connection
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
        // TODO: make this more readable. 
        // inject an extra column, with the display_id
        $stmt = self::$conn->prepare(
            'SELECT c.*, sub.display_id FROM Claim c JOIN ( SELECT topic_id, id, ROW_NUMBER() OVER (PARTITION BY topic_id ORDER BY id) AS display_id FROM Claim ) sub ON c.id = sub.id WHERE c.id = ?'
        );
        $stmt->bindParam(1, $claim_id);
        if (!$stmt->execute()) {
            error_log(self::$conn->error);
            self::$conn->rollback();
            exit(htmlspecialchars("A database error occured while querying claim #$claim_id."));
        }
        return $stmt->fetchObject();
    }
    /**
     * @param int $claim_id
     * @return bool Whether the claim with the given ID is acrtive.
     */
    public static function isClaimActive(int $claim_id)
    {
        $stmt = self::$conn->prepare(
            'SELECT active from Claim where id = ?'
        );
        $stmt->bindParam(1, $claim_id);
        if (!$stmt->execute()) {
            error_log(self::$conn->error);
            exit(htmlspecialchars("A database error occured while querying claim #$claim_id."));
        }
        $res = $stmt->fetchColumn();
        return $res ?? false;
    }
    /**
     * @param int $claim_id
     * @param bool $active
     */
    public static function setClaimActive(int $claim_id, bool $active)
    {
        $stmt = self::$conn->prepare(
            'UPDATE Claim SET active = :active WHERE id = :id;'
        );
        $stmt->bindValue(":active", $active, \PDO::PARAM_BOOL);
        $stmt->bindValue(":id", $claim_id, \PDO::PARAM_INT);
        if (!$stmt->execute()) {
            error_log(self::$conn->error);
            exit(htmlspecialchars("A database error occured while updating claim #$claim_id."));
        }
    }
    /**
     * @param int $claim_id
     * @return int|null The claim which is flagged by $claim_id
     */
    public static function getFlaggedClaim(int $claim_id)
    {
        $stmt = self::$conn->prepare(
            'SELECT DISTINCT * from Flag WHERE claimIDFlagger = ?'
        );
        $stmt->bindParam(1, $claim_id);
        if (!$stmt->execute()) {
            error_log(self::$conn->error);
            exit(htmlspecialchars("A database error occured while querying claim #$claim_id."));
        }
        foreach ($stmt as $row) {
            return $row["claimIDFlagged"];
        }
        return;
    }
    /**
     * Gets the set of claims which are flagged by the current claim.
     *
     * @param int $claim_id Current claim ID
     * @return array List of claim IDs
     */
    public static function getFlaggedClaims(int $claim_id)
    {
        $stmt = self::$conn->prepare(
            'SELECT DISTINCT * from Flag WHERE claimIDFlagger = ?'
        );
        $stmt->bindParam(1, $claim_id);
        if (!$stmt->execute()) {
            error_log(self::$conn->error);
            exit(htmlspecialchars("A database error occured while querying claim #$claim_id."));
        }
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
    // look for normal non-rival flags for this rivaling claim.
    /**
     * Returns the ID of each non-rival claim which flags $claim_id
     *
     * @param int $claim_id
     * @return int[]
     */
    public static function getNonRivalFlags(int $claim_id)
    {
        $query = "SELECT DISTINCT claimIDFlagger
        from Claim, Flag where claimIDFlagged = ?
        AND flagType NOT LIKE 'Thesis Rival'";
        $stmt = self::$conn->prepare($query);
        $stmt->bindParam(1, $claim_id);
        if (!$stmt->execute()) {
            error_log(self::$conn->error);
            exit(htmlspecialchars("A database error occured while querying claim #$claim_id."));
        }

        return $stmt->fetchAll(\PDO::FETCH_COLUMN);
    }
    /**
     * Gets the set of Thesis Rivals which flag the current claim.
     *
     * @param int $claim_id Current claim ID
     * @return int[] List of claim IDs
     */
    public static function getThesisRivals(int $claim_id)
    {
        $query = "SELECT DISTINCT claimIDFlagger
        from Flag where claimIDFlagged = ?
        AND flagType LIKE 'Thesis Rival'";
        $stmt = self::$conn->prepare($query);
        $stmt->bindParam(1, $claim_id);
        if (!$stmt->execute()) {
            error_log(self::$conn->error);
            exit(htmlspecialchars("A database error occured while querying claim #$claim_id."));
        }

        return $stmt->fetchAll(\PDO::FETCH_COLUMN);
    }
    /**
     * Gets all claims that flag the current claim and aren't Supporting.
     *
     * @param int $claim_id Current claim ID
     * @return int[] List of claim IDs
     */
    public static function getFlagsNotSupporting(int $claim_id)
    {
        $query = "SELECT DISTINCT claimIDFlagger
        from Flag WHERE claimIDFlagged = ?
        and flagType NOT LIKE 'supporting'";
        $stmt = self::$conn->prepare($query);
        $stmt->bindParam(1, $claim_id);
        if (!$stmt->execute()) {
            error_log(self::$conn->error);
            exit(htmlspecialchars("A database error occured while querying claim #$claim_id."));
        }

        return $stmt->fetchAll(\PDO::FETCH_COLUMN);
    }
    /**
     * Gets all Support claims that flag the current claim.
     *
     * @param int $claim_id Current claim ID
     * @return int[] List of claim IDs
     */
    public static function getSupportingClaims(int $claim_id)
    {
        $query = "SELECT DISTINCT claimIDFlagger
        from Flag WHERE claimIDFlagged = ?
        and flagType LIKE 'supporting'";
        $stmt = self::$conn->prepare($query);
        $stmt->bindParam(1, $claim_id);
        if (!$stmt->execute()) {
            error_log(self::$conn->error);
            exit(htmlspecialchars("A database error occured while querying claim #$claim_id."));
        }
        return $stmt->fetchAll(\PDO::FETCH_COLUMN);
    }
    /**
     * Gets all flags that aren't Supports or Thesis Rivals.
     * @return int[] The claim_id of each flag.
     */
    public static function getThesisFlagsNotRival(int $claim_id)
    {
        // TODO: what a stupid name
        $query = "SELECT DISTINCT claimIDFlagger
        from Flag
        WHERE claimIDFlagged = ? and flagType NOT LIKE 'Thesis Rival' and flagType NOT LIKE 'supporting'";
        $stmt = self::$conn->prepare($query);
        $stmt->bindParam(1, $claim_id);
        if (!$stmt->execute()) {
            error_log(self::$conn->error);
            exit(htmlspecialchars("A database error occured while querying claim #$claim_id."));
        }
        return $stmt->fetchAll(\PDO::FETCH_COLUMN);
    }
    /**
     * Checks if an individual claim has any active supports
     *
     * @param int $claim_id the ID of the claim to check
     * @return bool True if the claim has at least one active support
     */
    public static function hasActiveSupports(int $claim_id)
    {
        $query = "SELECT EXISTS (SELECT COUNT(*) as total FROM Flag WHERE claimIDFlagged = ? AND flagType = 'supporting')";
        $stmt = self::$conn->prepare($query);
        $stmt->bindParam(1, $claim_id);
        if (!$stmt->execute()) {
            error_log(self::$conn->error);
            exit(htmlspecialchars("A database error occured while querying claim #$claim_id."));
        }
        return (bool)$stmt->fetchColumn();
    }
    /**
     * Gets the set of root claims for the current topic.
     *
     * @param int $topic Topic string
     * @return int[] List of claim IDs
     */
    public static function getAllRootClaimIDs(int $topic)
    {
        $query = 'SELECT DISTINCT id from Claim, Flag
        WHERE topic_id = ? AND id NOT IN (SELECT DISTINCT claimIDFlagger FROM Flag)';
        $stmt = self::$conn->prepare($query);
        $stmt->bindParam(1, $topic);
        if (!$stmt->execute()) {
            error_log(self::$conn->error);
            exit(htmlspecialchars("A database error occured while querying topic $topic."));
        }
        return $stmt->fetchAll(\PDO::FETCH_COLUMN);
    }
    /**
     * Gets the set of claims for a given topic which have isRootRival set.
     *
     * @param int $topic_id
     * @return int[] List of claim IDs
     */
    public static function getRootRivals(int $topic_id)
    {
        $query = 'SELECT DISTINCT Claim.id from Claim
        JOIN Flag ON Flag.claimIDFlagger = Claim.id
        WHERE Claim.topic_id = ? AND Flag.isRootRival = 1';
        $stmt = self::$conn->prepare($query);
        $stmt->bindParam(1, $topic_id);
        if (!$stmt->execute()) {
            error_log(self::$conn->error);
            exit(htmlspecialchars("A database error occured while querying topic $topic_id."));
        }
        return $stmt->fetchAll(\PDO::FETCH_COLUMN);
    }
    /**
     * Gets the set of claims for a given topic which are thesis rivals
     *
     * @param int $topic_id
     * @return int[] List of claim IDs
     */
    public static function getAllThesisRivals(int $topic_id)
    {
        $query = 'SELECT DISTINCT Claim.id from Claim
        JOIN Flag ON Flag.claimIDFlagger = Claim.id
        WHERE Claim.topic_id = ? AND Flag.flagType LIKE "Thesis Rival"';
        $stmt = self::$conn->prepare($query);
        $stmt->bindParam(1, $topic_id);
        if (!$stmt->execute()) {
            error_log(self::$conn->error);
            exit(htmlspecialchars("A database error occured while querying topic $topic_id."));
        }
        return $stmt->fetchAll(\PDO::FETCH_COLUMN);
    }
    public static function getTopic(int $topic_id)
    {
        $stmt = self::$conn->prepare('SELECT * FROM Topic WHERE id = :ID LIMIT 1');
        $stmt->bindParam("ID", $topic_id);
        $stmt->execute();
        return $stmt->fetchObject();
    }
    /**
     * @return string[] The list of topic names
     */
    public static function getAllTopics()
    {
        $stmt = self::$conn->prepare('SELECT * FROM Topic');
        if (!$stmt->execute()) {
            error_log(self::$conn->error);
            exit(htmlspecialchars("A database error occured while getting topics."));
        }
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
    // DATABASE CLAIM INSERTION
    public static function insertThesis(
        int $topic_id, string $subject, string $targetP, bool $active = true
    ) {
        $stmt = self::$conn->prepare("INSERT INTO Claim(topic_id, subject, targetP, active, COS) VALUES(:topic_id, :subject, :targetP, :active, :cos)");
        $stmt->bindParam(":topic_id", $topic_id);
        $stmt->bindParam(":subject", $subject);
        $stmt->bindParam(":targetP", $targetP);
        $stmt->bindParam(":active", $active);
        $stmt->bindValue(":cos", "claim");
        if (!$stmt->execute()) {
            echo 'query error: ' . (self::$conn);
        } else {
            return intval(self::$conn->lastInsertId());
        }
    }
    public static function insertFlag(
        int $flagged_id, int $flagging_id, string $flagType, bool $isRootRival = false
    ) {
        $flag_stmt = self::$conn->prepare(
            "INSERT INTO Flag(claimIDFlagged, claimIDFlagger, flagType, isRootRival)
            VALUES(:flagged_id, :flagging_id, :flagType, :isRootRival)"
        );
        $flag_stmt->bindValue(":flagged_id", $flagged_id, \PDO::PARAM_INT);
        $flag_stmt->bindValue(":flagging_id", $flagging_id, \PDO::PARAM_INT);
        $flag_stmt->bindValue(":flagType", $flagType, \PDO::PARAM_STR);
        $flag_stmt->bindValue(":isRootRival", $isRootRival, \PDO::PARAM_BOOL);
        if (!$flag_stmt->execute()) { // fail
            echo 'query error: ' . $flag_stmt->errorInfo()[2];
            exit("Database error creating a flag relation.");
        }
    }
    public static function insertSupport(
        int $topic_id, int $flagged_id, string $subject, string $targetP, string $supportMeans, string $reason = null, string $example = null, string $url = null, string $citation = null, string $transcription = null, string $vidtimestamp = null
    ) {
        $support_stmt = self::$conn->prepare(
            "INSERT INTO Claim(topic_id, subject, targetP, supportMeans, example, URL, reason,  vidtimestamp, citation, transcription, COS)
            VALUES(:topic_id, :subject, :targetP, :supportMeans, :example, :url, :reason, :vidtimestamp, :citation, :transcription, :cos)"
        );
        $support_stmt->bindValue(":topic_id", $topic_id, \PDO::PARAM_INT);
        $support_stmt->bindValue(":subject", $subject, \PDO::PARAM_STR);
        $support_stmt->bindValue(":targetP", $targetP, \PDO::PARAM_STR);
        $support_stmt->bindValue(":supportMeans", $supportMeans, \PDO::PARAM_STR);
        $support_stmt->bindValue(":example", $example, \PDO::PARAM_STR);
        $support_stmt->bindValue(":url", $url, \PDO::PARAM_STR);
        $support_stmt->bindValue(":reason", $reason, \PDO::PARAM_STR);
        $support_stmt->bindValue(":vidtimestamp", $vidtimestamp, \PDO::PARAM_STR);
        $support_stmt->bindValue(":citation", $citation, \PDO::PARAM_STR);
        $support_stmt->bindValue(":transcription", $transcription, \PDO::PARAM_STR);
        $support_stmt->bindValue(":cos", "support", \PDO::PARAM_STR);
        if (!$support_stmt->execute()) { // fail
            echo 'query error: ' . $support_stmt->errorInfo()[2];
            return false;
        }
        $support_id = intval(self::$conn->lastInsertId());
        self::insertFlag($flagged_id, $support_id, 'supporting', false);
        return $support_id;
    }
    /**
     * A claim is a rootClaim if it is not set to flag any other claim.
     * 
     * @return bool Whether the given claim is a root claim */
    public static function isRootClaim(int $claim_id)
    {
        $is_root = false;
        $stmt5 = self::$conn->prepare('SELECT EXISTS(SELECT DISTINCT id FROM Claim WHERE id = ? AND id NOT IN (SELECT DISTINCT claimIDFlagger FROM Flag));');
        $stmt5->bindParam(1, $claim_id);
        $stmt5->bindColumn(1, $is_root);
        $stmt5->execute();
        return $stmt5->fetchColumn();
    }
    public static function createNewTopic(string $name, string $description)
    {
        $stmt = self::$conn->prepare("INSERT INTO Topic(name, description) VALUES (?, ?)");
        $stmt->bindValue(1, $name);
        $stmt->bindValue(2, $description);
        $stmt->execute();
        $topic_id = intval(self::$conn->lastInsertId());
        return $topic_id;
    }
}

Database::staticInit();