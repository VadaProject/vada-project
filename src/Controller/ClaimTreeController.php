<?php
namespace Vada\Controller;

use Vada\Model\ClaimRepository;
use Vada\Model\Topic;
use Vada\View\ClaimCard;

class ClaimTreeController
{
    private ClaimRepository $claimRepository;
    private Topic $topic;

    public function __construct(ClaimRepository $claimRepository, Topic $topic)
    {
        $this->claimRepository = $claimRepository;
        $this->topic = $topic;
    }
    // TODO: this logic should be split into separate "claim tree getter" and claim tree renderer.
    public function displayClaimTree() {
        $root_claims = $this->claimRepository->getRootClaimsByTopic($this->topic);
        $root_rivals = $this->claimRepository->getRootRivals($this->topic);
        if (count($root_claims) == 0 && count($root_rivals) == 0) {
            echo "<p>Topic is empty.</a></p>";
            return;
        }
        echo "<ul>";
        foreach ($root_claims as $claim_id) {
            $this->sortclaims($claim_id);
        }
        foreach ($root_rivals as $claim_id) {
            $this->sortclaimsRIVAL($claim_id);
        }
        echo "</ul>";
    }

    
    /*
    This function displays each individual claim in a recursive manner.
    Each recursion is a series of tracking relationships between the claims (found in the Flabsdb).
    */
    private function sortClaims($claim_id)
    {
        // starts two chains of recursion. one with normal root claims.
        // the other with root rivals. the rivals, of course, are put into the rival recursion.
        $claim = $this->claimRepository->getClaimByID($claim_id);
        if (!$claim) {
            return;
        }
        // TODO: rework this database call into "getFlaggedClaim" so we can get the flag type.
        $flags = $this->claimRepository->getFlaggedClaims($claim_id);
        $flag_type = $flags[0]["flagType"] ?? null;
        $claim_id_flagged = $this->claimRepository->getFlaggedClaim($claim_id);
        // TODO: this should be made singular.
        if ($flag_type == "Thesis Rival") {
            $this->sortClaimsRival($claim_id_flagged);
            $this->sortClaimsRival($claim->id);
            return;
        }
        echo "<li>";
        $claimCard = new ClaimCard($claim, $flag_type);
        $claimCard->render();
        // IF A CLAIM IS FLAGGED IT obtains flaggers that aren't rivals
        // if its a thesis rival it will show up in the query above
        // this is when the claim is the flagged. this is what gets pushed in the recursion.
        // continue recursion
        $result1 = $this->claimRepository->getNonRivalFlags($claim_id); // get the mysqli result

        if (\count($result1) > 0) {
            echo '<span class="stem"></span>';
            echo '<ul>';
            foreach ($result1 as $flag_id) {
                $this->sortClaims($flag_id);
            }
            echo '</ul>';
        }
    }

    /*
    This function has the same functionality as the sortClaims, but for rivals.
    The key difference is handling the “mutualistic flagging” relationship that is unique to rivals (that is, they flag each other equally).
    It breaks an infinite loop that would otherwise occur if a rival was handled recursively in sortClaims().
    */
    private function sortClaimsRIVAL($claim_id)
    {
        // get the info for the claim being flagged
        $claim = $this->claimRepository->getClaimByID($claim_id);
        // look for normal non-rival flags for this rivaling claim.
        $result1 = $this->claimRepository->getThesisRivals($claim_id);
        $rivaling = 0;
        foreach ($result1 as $flag_id) {
            $rivaling = $flag_id;
        }
        $rival_claim = $this->claimRepository->getClaimByID($rivaling);
        echo '<li>';
        $claimCard = new ClaimCard($claim, null, $rival_claim);
        $claimCard->render();
        $result1 = $this->claimRepository->getNonRivalFlags($claim_id);
        if (\count($result1) > 0) {
            echo '<span class="stem"></span>';
            echo '<ul>';
            foreach ($result1 as $flag_id) {
                $this->sortClaims($flag_id);
            }
            echo '</ul>';
        }
    }
}