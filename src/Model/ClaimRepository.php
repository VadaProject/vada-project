<?php
/**
 * 
 */

namespace Vada\Model;

use Vada\Model\Claim;
use PDO;

class ClaimRepository
{
    // TODO: this needs to be private. currently we make it public so insert.php can do transactions. this is bad.
    public PDO $conn;
    public function __construct(PDO $conn)
    {
        $this->conn = $conn;
    }

    /**
     * @param int|null $claim_id
     * @return object|null A claim object
     */
    public function getClaimByID(int|null $claim_id)
    {
        if (!isset($claim_id)) {
            return null;
        }
        // TODO: make this more readable. 
        // inject an extra column, with the display_id
        $stmt = $this->conn->prepare('SELECT
        c.*,
        display.display_id
    FROM
        Claim c
        JOIN (
            SELECT
                topic_id,
                id,
                ROW_NUMBER() OVER (
                    PARTITION BY
                        topic_id
                    ORDER BY
                        id
                ) AS display_id
            FROM
                Claim
        ) display ON c.id = display.id
    WHERE
        c.id = ?;');
        $stmt->bindParam(1, $claim_id);
        if (!$stmt->execute()) {
            error_log($this->conn->error);
            $this->conn->rollback();
            exit(htmlspecialchars("A database error occured while querying claim #$claim_id."));
        }
        $claim = $stmt->fetchObject();
        return $claim ? $claim : null;
    }
    /**
     * @param int $claim_id
     * @return bool Whether the claim with the given ID is acrtive.
     */
    public function isClaimActive(int $claim_id)
    {
        $stmt = $this->conn->prepare(
            'SELECT active from Claim where id = ?'
        );
        $stmt->bindParam(1, $claim_id);
        $stmt->execute();
        $res = $stmt->fetchColumn();
        return $res ?? false;
    }
    /**
     * @param int $claim_id
     * @param bool $active
     */
    public function setClaimActive(int $claim_id, bool $active)
    {
        $stmt = $this->conn->prepare(
            'UPDATE Claim SET active = :active WHERE id = :id;'
        );
        $stmt->bindValue(":active", $active, PDO::PARAM_BOOL);
        $stmt->bindValue(":id", $claim_id, PDO::PARAM_INT);
        if (!$stmt->execute()) {
            error_log($this->conn->error);
            exit(htmlspecialchars("A database error occured while updating claim #$claim_id."));
        }
    }
    // look for normal non-rival flags for this rivaling claim.
    /**
     * Returns the ID of each non-rival claim which flags $claim_id
     *
     * @param int $claim_id
     * @return int[]
     */
    public function getFlagsAndSupports(int $claim_id)
    {
        $query = "SELECT DISTINCT id FROM Claim WHERE flagged_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $claim_id);
        if (!$stmt->execute()) {
            error_log($this->conn->error);
            exit(htmlspecialchars("A database error occured while querying claim #$claim_id."));
        }
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
    /**
     * Gets all claims that flag the current claim and aren't Supporting.
     *
     * @param int $claim_id Current claim ID
     * @return int[] List of claim IDs
     */
    public function getFlagsAndRivals(int $claim_id)
    {
        $query = "SELECT DISTINCT id
        from Claim WHERE (flagged_id = :id OR rival_id = :id)
        and flag_type NOT LIKE 'supporting'";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $claim_id);
        if (!$stmt->execute()) {
            error_log($this->conn->error);
            exit(htmlspecialchars("A database error occured while querying claim #$claim_id."));
        }
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
    /**
     * Gets the list of claims which support this claim.
     *
     * @param int $claim_id Current claim ID
     * @return int[] List of claim IDs
     */
    public function getSupports(int $claim_id)
    {
        $query = "SELECT DISTINCT id
        from Claim WHERE flagged_id = ?
        and flag_type LIKE 'supporting'";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $claim_id);
        if (!$stmt->execute()) {
            error_log($this->conn->error);
            exit(htmlspecialchars("A database error occured while querying claim #$claim_id."));
        }
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
    /**
     * Gets the id of each claim which flags the claim. This is specifically flags inserted via the "Flag Claim" UI. NOT including "supports" or thesis rivals.
     * @return int[] The claim_id of each flag.
     */
    public function getFlags(int $claim_id)
    {
        $query = "SELECT DISTINCT id
        from Claim
        WHERE flagged_id = ? and flag_type NOT LIKE 'Thesis Rival' and flag_type NOT LIKE 'supporting'";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $claim_id);
        if (!$stmt->execute()) {
            error_log($this->conn->error);
            exit(htmlspecialchars("A database error occured while querying claim #$claim_id."));
        }
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
    /**
     * Checks if an individual claim has any active supports
     *
     * @param int $claim_id the ID of the claim to check
     * @return bool True if the claim has at least one active support
     */
    public function hasActiveSupports(int $claim_id)
    {
        $query = "SELECT EXISTS (SELECT id
            FROM Claim
            WHERE flag_type = 'supporting'
            AND flagged_id = ?
            AND active = true)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $claim_id);
        if (!$stmt->execute()) {
            error_log($this->conn->error);
            exit(htmlspecialchars("A database error occured while querying claim #$claim_id."));
        }
        return (bool) $stmt->fetchColumn();
    }
    /**
     * Gets the set of non-rival root claims for the current topic.
     *
     * @param int $topic_id Topic string
     * @return int[] List of claim IDs
     */
    public function getRootClaimsByTopic(Topic $topic)
    {
        $query = 'SELECT DISTINCT id from Claim
        WHERE topic_id = ? AND flagged_id IS NULL AND rival_id IS NULL';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $topic->id);
        if (!$stmt->execute()) {
            error_log($this->conn->error);
            exit(htmlspecialchars("A database error occured while querying topic."));
        }
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
    /**
     * Gets the set of claims for a given topic which have isRootRival set.
     *
     * @param int $topic_id
     * @return int[] List of claim IDs
     */
    public function getRootRivals(Topic $topic)
    {
        $query = 'SELECT DISTINCT Claim.id from Claim
        WHERE topic_id = ? AND isRootRival = true';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $topic->id);
        if (!$stmt->execute()) {
            error_log($this->conn->error);
            exit(htmlspecialchars("A database error occured while querying topic."));
        }
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
    /**
     * Gets the set of claims for a given topic which are thesis rivals
     *
     * @param int $topic_id
     * @return int[] List of claim IDs
     */
    public function getAllThesisRivals(Topic $topic)
    {
        $query = 'SELECT DISTINCT id from Claim
        WHERE topic_id = ? AND rival_id IS NOT NULL';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $topic->id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    // DATABASE CLAIM INSERTION
    public function insertThesis(
        int $topic_id, string $subject, string $targetP, bool $active = true
    ) {
        $stmt = $this->conn->prepare("INSERT INTO Claim(topic_id, subject, targetP, active, COS) VALUES(:topic_id, :subject, :targetP, :active, :cos)");
        $stmt->bindParam(":topic_id", $topic_id);
        $stmt->bindParam(":subject", $subject);
        $stmt->bindParam(":targetP", $targetP);
        $stmt->bindParam(":active", $active, PDO::PARAM_BOOL);
        $stmt->bindValue(":cos", "claim");
        if (!$stmt->execute()) {
            echo 'query error: ' . ($this->conn);
        } else {
            return intval($this->conn->lastInsertId());
        }
    }
    public function insertFlag(
        int $flagged_id, int $flagging_id, string $flagType, bool $isRootRival = false
    ) {
        $flag_stmt = $this->conn->prepare(
            "UPDATE Claim
            SET flag_type = :flag_type,
            flagged_id = :flagged_id,
            isRootRival = :isRootRival
            WHERE id = :flagging_id;"
        );
        $flag_stmt->bindValue(":flagged_id", $flagged_id, PDO::PARAM_INT);
        $flag_stmt->bindValue(":flagging_id", $flagging_id, PDO::PARAM_INT);
        $flag_stmt->bindValue(":flag_type", $flagType, PDO::PARAM_STR);
        $flag_stmt->bindValue(":isRootRival", $isRootRival, PDO::PARAM_BOOL);
        if (!$flag_stmt->execute()) { // fail
            echo 'query error: ' . $flag_stmt->errorInfo()[2];
            exit("Database error creating a flag relation.");
        }
    }
    public function insertSupport(
        int $topic_id, int $flagged_id, string $subject, string $targetP, string $supportMeans, string $reason = null, string $example = null, string $url = null, string $citation = null, string $transcription = null, string $vidtimestamp = null
    ) {
        $support_stmt = $this->conn->prepare(
            "INSERT INTO Claim(topic_id, subject, targetP, supportMeans, example, URL, reason,  vidtimestamp, citation, transcription, COS)
            VALUES(:topic_id, :subject, :targetP, :supportMeans, :example, :url, :reason, :vidtimestamp, :citation, :transcription, :cos)"
        );
        $support_stmt->bindValue(":topic_id", $topic_id, PDO::PARAM_INT);
        $support_stmt->bindValue(":subject", $subject, PDO::PARAM_STR);
        $support_stmt->bindValue(":targetP", $targetP, PDO::PARAM_STR);
        $support_stmt->bindValue(":supportMeans", $supportMeans, PDO::PARAM_STR);
        $support_stmt->bindValue(":example", $example, PDO::PARAM_STR);
        $support_stmt->bindValue(":url", $url, PDO::PARAM_STR);
        $support_stmt->bindValue(":reason", $reason, PDO::PARAM_STR);
        $support_stmt->bindValue(":vidtimestamp", $vidtimestamp, PDO::PARAM_STR);
        $support_stmt->bindValue(":citation", $citation, PDO::PARAM_STR);
        $support_stmt->bindValue(":transcription", $transcription, PDO::PARAM_STR);
        $support_stmt->bindValue(":cos", "support", PDO::PARAM_STR);
        if (!$support_stmt->execute()) { // fail
            echo 'query error: ' . $support_stmt->errorInfo()[2];
            return false;
        }
        $support_id = intval($this->conn->lastInsertId());
        self::insertFlag($flagged_id, $support_id, 'supporting', false);
        return $support_id;
    }
    /**
     * A claim is a rootClaim if it is not set to flag any other claim.
     * 
     * @return bool Whether the given claim is a root claim */
    public function isRootClaim(int $claim_id)
    {
        $is_root = false;
        $stmt5 = $this->conn->prepare(
            'SELECT EXISTS(SELECT DISTINCT id FROM Claim WHERE id = ? AND id NOT IN (SELECT DISTINCT flagged_id FROM Claim));'
        );
        $stmt5->bindParam(1, $claim_id);
        $stmt5->bindColumn(1, $is_root);
        $stmt5->execute();
        return $stmt5->fetchColumn();
    }

    public function hasActiveFlags(int $claim_id)
    {
        // TODO: this operation could probably be written as a single database query, using joins...
        $result = $this->getFlagsAndRivals($claim_id);
        foreach ($result as $flaggerID) {
            $flagger = $this->getClaimByID($flaggerID);
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
    public function hasActiveFlagsNonRival(int $claim_id)
    {
        $flaggers = $this->getFlags($claim_id);
        foreach ($flaggers as $flaggerID) {
            $flagger = $this->getClaimByID($flaggerID);
            if ($flagger && $flagger->active == 1) {
                return true;
            }
        }
        return false;
    }
}