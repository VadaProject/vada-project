<?php
namespace Vada\Controller;

use Vada\Model\ClaimRepository;
use Vada\Model\Topic;
use Vada\View\ClaimCard;

class ClaimTreeController
{
    private ClaimRepository $claimRepository;
    private int $topic_id;

    public function __construct(ClaimRepository $claimRepository, int $topic_id)
    {
        $this->claimRepository = $claimRepository;
        $this->topic_id = $topic_id;
    }
    // TODO: this logic should be split into separate "claim tree getter" and claim tree renderer.
    public function displayClaimTree() {
        $root_claims = $this->claimRepository->getRootClaimsByTopic($this->topic_id);
        $root_rivals = $this->claimRepository->getRootRivals($this->topic_id);
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
    private function sortClaims(int $claim_id)
    {
        // starts two chains of recursion. one with normal root claims.
        // the other with root rivals. the rivals, of course, are put into the rival recursion.
        $claim = $this->claimRepository->getClaimByID($claim_id);
        if (!$claim) {
            return;
        }
        // has rival?
        if (isset($claim->rival_id)) {
            $this->sortClaimsRival($claim->id);
            $this->sortClaimsRival($claim->rival_id);
            return;
        }
        echo "<li>";
        $claimCard = new ClaimCard($claim, $claim->flag_type);
        $claimCard->render();
        // IF A CLAIM IS FLAGGED IT obtains flaggers that aren't rivals
        // if its a thesis rival it will show up in the query above
        // this is when the claim is the flagged. this is what gets pushed in the recursion.
        // continue recursion
        $result1 = $this->claimRepository->getFlagsAndSupports($claim_id); // get the mysqli result

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
    private function sortClaimsRIVAL(int $claim_id)
    {
        // get the info for the claim being flagged
        $claim = $this->claimRepository->getClaimByID($claim_id);
        // look for normal non-rival flags for this rivaling claim.
        $rival_claim = $this->claimRepository->getClaimByID($claim->rival_id);
        echo '<li>';
        $claimCard = new ClaimCard($claim, null, $rival_claim);
        $claimCard->render();
        $flags_and_supports = $this->claimRepository->getFlagsAndSupports($claim_id);
        if (count($flags_and_supports) > 0) {
            echo '<span class="stem"></span>';
            echo '<ul>';
            foreach ($flags_and_supports as $flag_id) {
                $this->sortClaims($flag_id);
            }
            echo '</ul>';
        }
    }
}