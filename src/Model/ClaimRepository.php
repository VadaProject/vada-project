<?php
namespace Vada\Model;

use ParagonIE\EasyDB\EasyDB;

/**
 * This class handles backend CRUD operations for claims. 
 * TODO: it returns Claim objects as a stdClass.
 */
class ClaimRepository
{
    private EasyDB $db;
    public function __construct(EasyDB $db)
    {
        $this->db = $db;
    }

    /**
     * @param int|null $claim_id
     * @return object|null A claim object, or null if claim_id is an error.
     */
    public function getClaimByID(int|null $claim_id)
    {
        if (empty($claim_id)) {
            return null;
        }
        // inject an extra column, with the display_id
        $row = $this->db->row(
            'SELECT * FROM ClaimDisplayID WHERE id = ?',
            $claim_id
        );
        if (empty($row)) {
            return null;
        }
        $claim = (object) $row;
        return $claim ? $claim : null;
    }
    /**
     * @param int $claim_id
     * @return bool Whether the claim with the given ID is acrtive.
     */
    public function isClaimActive(int $claim_id)
    {
        $res = $this->db->cell(
            'SELECT active FROM Claim WHERE id = ?',
            $claim_id
        );
        return $res ?? false;
    }
    /**
     * @param int $claim_id
     * @param bool $active
     */
    public function setClaimActive(int $claim_id, bool $active)
    {
        $this->db->update(
            'Claim',
            ['active' => $active],
            ['id' => $claim_id]
        );
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
        return $this->db->col(
            "SELECT DISTINCT id 
            FROM Claim
            WHERE flagged_id = ?",
            0,
            $claim_id
        );
    }
    /**
     * Gets all claims that flag the current claim and aren't Supporting.
     *
     * @param int $claim_id Current claim ID
     * @return int[] List of claim IDs
     */
    public function getFlagsAndRivals(int $claim_id)
    {
        return $this->db->col("SELECT DISTINCT id
        from Claim WHERE (flagged_id = ? OR rival_id = ?)
        and flag_type NOT LIKE 'supporting'", 0, $claim_id, $claim_id);
    }
    /**
     * Gets the list of claims which support this claim.
     *
     * @param int $claim_id Current claim ID
     * @return int[] List of claim IDs
     */
    public function getSupports(int $claim_id)
    {
        return $this->db->col(
            "SELECT DISTINCT id
            FROM Claim
            WHERE 
                flagged_id = ?
                AND flag_type LIKE 'supporting'",
            0,
            $claim_id
        );
    }
    /**
     * Gets the id of each claim which flags the claim. This is specifically flags inserted via the "Flag Claim" UI. NOT including "supports" or thesis rivals.
     * @return int[] The claim_id of each flag.
     */
    public function getFlags(int $claim_id)
    {
        return $this->db->col(
            "SELECT DISTINCT id
            FROM Claim
            WHERE flagged_id = ?
                AND flag_type NOT LIKE 'Thesis Rival'
                AND flag_type NOT LIKE 'supporting'",
            0,
            $claim_id,
        );
    }
    /**
     * Checks if an individual claim has any active supports
     *
     * @param int $claim_id the ID of the claim to check
     * @return bool True if the claim has at least one active support
     */
    public function hasActiveSupports(int $claim_id)
    {
        return (bool) $this->db->cell("SELECT EXISTS (SELECT id
            FROM Claim
            WHERE flag_type = 'supporting'
            AND flagged_id = ?
            AND active = true)", $claim_id);
    }
    /**
     * Gets the set of non-rival root claims for the current topic.
     *
     * @param int $topic_id Topic string
     * @return int[] List of claim IDs
     */
    public function getRootClaimsByTopic(int $topic_id)
    {
        return $this->db->col(
            'SELECT DISTINCT id from Claim
            WHERE topic_id = ?
                AND flagged_id IS NULL
                AND rival_id IS NULL',
            0,
            $topic_id
        );
    }
    /**
     * Gets the set of claims for a given topic which have isRootRival set.
     *
     * @param int $topic_id
     * @return int[] List of claim IDs
     */
    public function getRootRivals(int $topic_id)
    {
        return $this->db->col('SELECT DISTINCT Claim.id from Claim
        WHERE topic_id = ? AND isRootRival = true', 0, $topic_id);
    }
    /**
     * Gets the set of claims for a given topic which are thesis rivals
     *
     * @param int $topic_id
     * @return int[] List of claim IDs
     */
    public function getAllThesisRivals(int $topic_id)
    {
        return $this->db->col('SELECT DISTINCT id from Claim
        WHERE topic_id = ? AND rival_id IS NOT NULL', 0, $topic_id);
    }
    // DATABASE CLAIM INSERTION
    public function insertThesis(
        int $topic_id, string $subject, string $targetP, bool $active = true
    ) {
        $id = $this->db->insertReturnId(
            "Claim",
            [
                "topic_id" => $topic_id,
                "subject" => $subject,
                "targetP" => $targetP,
                "active" => $active,
                "cos" => "claim",
            ]
        );
        return intval($id);
    }
    public function insertFlag(
        int $topic_id, string $subject, string $targetP, int $flagged_id, string $flag_type
    ) {
        $id = $this->db->insertReturnId(
            "Claim",
            [
                "topic_id" => $topic_id,
                "subject" => $subject,
                "targetP" => $targetP,
                "active" => true,
                "cos" => "claim",
                "flagged_id" => $flagged_id,
                "flag_type" => $flag_type
            ]
        );
        return intval($id);
    }
    public function insertRival(
        int $topic_id, string $subject, string $targetP, int $rival_id, bool $is_root_rival = false
    ) {
        $id = $this->db->insertReturnId(
            "Claim",
            [
                "topic_id" => $topic_id,
                "subject" => $subject,
                "targetP" => $targetP,
                "active" => false,
                "cos" => "claim",
                "rival_id" => $rival_id,
                "flag_type" => "Thesis Rival",
                "isRootRival" => $is_root_rival
            ]
        );
        // update the rivaled claim
        $this->db->update(
            "Claim",
            [
                "rival_id" => $id,
                "isRootRival" => $is_root_rival
            ],
            [
                "id" => $rival_id
            ],
        );
        return intval($id);
    }
    public function insertSupport(
        int $topic_id, int $flagged_id, string $subject, string $targetP, string $supportMeans, string $reason = null, string $example = null, string $url = null, string $citation = null, string $transcription = null, string $vidtimestamp = null
    ) {
        $support_id = $this->db->insertReturnId(
            "Claim",
            [
                "topic_id" => $topic_id,
                "subject" => $subject,
                "targetP" => $targetP,
                "supportMeans" => $supportMeans,
                "example" => $example,
                "url" => $url,
                "reason" => $reason,
                "vidtimestamp" => $vidtimestamp,
                "citation" => $citation,
                "transcription" => $transcription,
                "cos" => "support",
                "flagged_Id" => $flagged_id,
                "flag_type" => "supporting"
            ]
        );
        return intval($support_id);
    }
    /**
     * A claim is a rootClaim if it is not set to flag any other claim.
     * 
     * @return bool Whether the given claim is a root claim */
    public function isRootClaim(int $claim_id)
    {
        return (bool) $this->db->cell(
            'SELECT EXISTS (
                SELECT DISTINCT id FROM Claim
                WHERE id = ?
                AND flagged_id IS NULL
            )',
            $claim_id
        );
    }
    /**
     * @return bool Whether the given claim has an active flag or rival against it
     */
    public function hasActiveFlagsOrRivals(int $claim_id)
    {
        return (bool) $this->db->cell(
            "SELECT EXISTS (
                SELECT DISTINCT id FROM Claim
                WHERE (flagged_id = ? OR rival_id = ?)
                AND active = TRUE
                AND flag_type NOT LIKE 'supporting'
            )",
            $claim_id,
            $claim_id
        );
    }
    /**
     * Checks if there are any thesis flags against a claim that AREN’T rivals.
     *
     * @param int $claim_id The ID of a claim
     * @return bool true if
     */
    public function hasActiveFlags(int $claim_id)
    {
        return (bool) $this->db->cell(
            "SELECT EXISTS (
                SELECT DISTINCT id FROM Claim
                WHERE flagged_id = ?
                AND active = TRUE
                AND flag_type NOT LIKE 'supporting'
            )",
            $claim_id
        );
    }
}