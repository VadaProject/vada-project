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
     * @param int $claim_id
     * @return object|null A claim object
     */
    public function getClaimByID(int $claim_id)
    {
        // TODO: make this more readable. 
        // inject an extra column, with the display_id
        $stmt = $this->conn->prepare(
            'SELECT c.*, sub.display_id FROM Claim c JOIN ( SELECT topic_id, id, ROW_NUMBER() OVER (PARTITION BY topic_id ORDER BY id) AS display_id FROM Claim ) sub ON c.id = sub.id WHERE c.id = ?'
        );
        $stmt->bindParam(1, $claim_id);
        if (!$stmt->execute()) {
            error_log($this->conn->error);
            $this->conn->rollback();
            exit(htmlspecialchars("A database error occured while querying claim #$claim_id."));
        }
        return $stmt->fetchObject();
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
    /**
     * @param int $claim_id
     * @return object|null The claim which is flagged by $claim_id
     */
    public function getFlaggedClaim(int $claim_id)
    {
        $stmt = $this->conn->prepare(
            'SELECT DISTINCT * from Flag WHERE claimIDFlagger = ?'
        );
        $stmt->bindParam(1, $claim_id);
        $stmt->execute();
        $claim_id = $stmt->fetchColumn();
        if (!isset($claim_id)) {
            return;
        }
        $claim = $this->getClaimByID($claim_id);
        if (!$claim) {
            return;
        }
        return $claim;
    }
    /**
     * Gets the set of claims which are flagged by the current claim.
     *
     * @param int $claim_id Current claim ID
     * @return array List of claim IDs
     */
    public function getFlaggedClaims(int $claim_id)
    {
        $stmt = $this->conn->prepare(
            'SELECT DISTINCT * from Flag WHERE claimIDFlagger = ?'
        );
        $stmt->bindParam(1, $claim_id);
        if (!$stmt->execute()) {
            error_log($this->conn->error);
            exit(htmlspecialchars("A database error occured while querying claim #$claim_id."));
        }
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    // look for normal non-rival flags for this rivaling claim.
    /**
     * Returns the ID of each non-rival claim which flags $claim_id
     *
     * @param int $claim_id
     * @return int[]
     */
    public function getNonRivalFlags(int $claim_id)
    {
        $query = "SELECT DISTINCT claimIDFlagger
        from Claim, Flag where claimIDFlagged = ?
        AND flagType NOT LIKE 'Thesis Rival'";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $claim_id);
        if (!$stmt->execute()) {
            error_log($this->conn->error);
            exit(htmlspecialchars("A database error occured while querying claim #$claim_id."));
        }

        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
    /**
     * Gets the set of Thesis Rivals which flag the current claim.
     *
     * @param int $claim_id Current claim ID
     * @return int[] List of claim IDs
     */
    public function getThesisRivals(int $claim_id)
    {
        $query = "SELECT DISTINCT claimIDFlagger
        from Flag where claimIDFlagged = ?
        AND flagType LIKE 'Thesis Rival'";
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
    public function getFlagsNotSupporting(int $claim_id)
    {
        $query = "SELECT DISTINCT claimIDFlagger
        from Flag WHERE claimIDFlagged = ?
        and flagType NOT LIKE 'supporting'";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $claim_id);
        if (!$stmt->execute()) {
            error_log($this->conn->error);
            exit(htmlspecialchars("A database error occured while querying claim #$claim_id."));
        }

        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
    /**
     * Gets all Support claims that flag the current claim.
     *
     * @param int $claim_id Current claim ID
     * @return int[] List of claim IDs
     */
    public function getSupportingClaims(int $claim_id)
    {
        $query = "SELECT DISTINCT claimIDFlagger
        from Flag WHERE claimIDFlagged = ?
        and flagType LIKE 'supporting'";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $claim_id);
        if (!$stmt->execute()) {
            error_log($this->conn->error);
            exit(htmlspecialchars("A database error occured while querying claim #$claim_id."));
        }
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
    /**
     * Gets all flags that aren't Supports or Thesis Rivals.
     * @return int[] The claim_id of each flag.
     */
    public function getThesisFlagsNotRival(int $claim_id)
    {
        // TODO: what a stupid name
        $query = "SELECT DISTINCT claimIDFlagger
        from Flag
        WHERE claimIDFlagged = ? and flagType NOT LIKE 'Thesis Rival' and flagType NOT LIKE 'supporting'";
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
        $query = "SELECT EXISTS (SELECT COUNT(*) as total FROM Flag WHERE claimIDFlagged = ? AND flagType = 'supporting')";
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
        $query = 'SELECT DISTINCT id from Claim, Flag
        WHERE topic_id = ? AND id NOT IN (SELECT DISTINCT claimIDFlagger FROM Flag)';
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
        JOIN Flag ON Flag.claimIDFlagger = Claim.id
        WHERE Claim.topic_id = ? AND Flag.isRootRival = 1';
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
        $query = 'SELECT DISTINCT Claim.id from Claim
        JOIN Flag ON Flag.claimIDFlagger = Claim.id
        WHERE Claim.topic_id = ? AND Flag.flagType LIKE "Thesis Rival"';
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
            "INSERT INTO Flag(claimIDFlagged, claimIDFlagger, flagType, isRootRival)
            VALUES(:flagged_id, :flagging_id, :flagType, :isRootRival)"
        );
        $flag_stmt->bindValue(":flagged_id", $flagged_id, PDO::PARAM_INT);
        $flag_stmt->bindValue(":flagging_id", $flagging_id, PDO::PARAM_INT);
        $flag_stmt->bindValue(":flagType", $flagType, PDO::PARAM_STR);
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
        $stmt5 = $this->conn->prepare('SELECT EXISTS(SELECT DISTINCT id FROM Claim WHERE id = ? AND id NOT IN (SELECT DISTINCT claimIDFlagger FROM Flag));');
        $stmt5->bindParam(1, $claim_id);
        $stmt5->bindColumn(1, $is_root);
        $stmt5->execute();
        return $stmt5->fetchColumn();
    }

    public function hasActiveFlags(int $claim_id)
    {
        // TODO: this operation could probably be written as a single database query, using joins...
        $result = $this->getFlagsNotSupporting($claim_id);
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
        $flaggers = $this->getThesisFlagsNotRival($claim_id);
        foreach ($flaggers as $flaggerID) {
            $flagger = $this->getClaimByID($flaggerID);
            if ($flagger && $flagger->active == 1) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param int $claim_id The claim to check.
     * @return bool Returns true if a claim has an ACTIVE thesis rival
     */
    public function hasRival($claim_id)
    {
        $thesis_rivals = $this->getThesisRivals($claim_id);
        return count($thesis_rivals) > 0;
    }
}