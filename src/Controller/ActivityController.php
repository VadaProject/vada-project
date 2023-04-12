<?php
namespace Vada\Controller;

use PDO;
use Vada\Model\ClaimRepository;
use Vada\Model\Topic;

/** 
 * This class implements business logic for processing whether a Claim is considered "contested" (active = false) or "uncontested" (active = true).
 * This is run after every Database insertion.
 */
class ActivityController
{
    private ClaimRepository $claimRepository;
    public function __construct(ClaimRepository $claimRepository)
    {
        $this->claimRepository = $claimRepository;
    }

    /**
     * For the given topic_id, recalculate claim activity.
     */
    public function restoreActivityTopic(Topic $topic)
    {
        $topic_id = $topic->id;
        // Recalculate activity relationships
        $root_claim = $this->claimRepository->getRootClaimsByTopic($topic);
        $thesis_rivals = $this->claimRepository->getAllThesisRivals($topic);
        $root_rivals = $this->claimRepository->getRootRivals($topic);
        for ($i = 0; $i < 2; $i++) {
            // NOTE: we run this whole routine twice because some flagging relationships take two iterations to fully resolve.
            // weird issue that should be fixed.
            foreach ($root_claim as $claim_id) {
                $this->restoreActivity($claim_id);
            }
            foreach ($root_rivals as $claim_id) {
                $this->restoreActivityRIVAL($claim_id);
            }
            foreach ($thesis_rivals as $claim_id) {
                $this->restoreActivityRIVAL($claim_id);
            }
        }
    }

    /// TODO: rewrite these functions to be less recursive and TESTABLE.

    /**
     * Checks the flagging relationships of each claim, and
     *   determines whether it is contested or not.
     *
     * @param int $claim_id The ID of the root claim to start at.
     * @return void
     */
    private function restoreActivity($claim_id)
    {
        // grabs supports for initial claim NUMBER ONE ON DIAGRAM, RED
        $supports = $this->claimRepository->getSupportingClaims($claim_id);
        if (count($supports) == 0) {
            if (
                !$this->claimRepository->hasActiveFlags($claim_id) &&
                !$this->claimRepository->hasRival($claim_id)
            ) {
                $this->claimRepository->setClaimActive($claim_id, true);
            }
        }
        $hasActiveSupport = false;
        foreach ($supports as $support_id) {
            // $claim_id is the original claim. $support_id is the support.
            // check to see if all the supports are inactive.
            // OR if ONE support is active.

            // is this support active? if so, reactivate it.
            // we only need one to reactivate the claim.
            if (
                $this->claimRepository->isClaimActive($support_id) &&
                !$this->claimRepository->hasActiveFlags($claim_id) &&
                !$this->claimRepository->hasRival($claim_id)
            ) {
                // $this->claimRepository->setClaimActive($claim_id, true);
                $hasActiveSupport = true;
            }
            $this->restoreActivity($support_id);

            // /////////////////////////////////////////////////////// NUMBER TWO ON DIAGRAM, ORANGE
            // below grabs all flaggers for the support and JUST the support. not the claims.  - act3, s3, activity3

            // this is for rivals
            foreach ($this->claimRepository->getThesisRivals($support_id) as $rival_id) {
                $this->restoreActivityRIVAL($rival_id);
                // below should get the companion rival
                $companion_rivals = $this->claimRepository->getThesisRivals($rival_id);
                foreach ($companion_rivals as $companion_rivals) {
                    $this->restoreActivityRIVAL($companion_rivals);
                }
            }
            $non_rivaling_flags = $this->claimRepository->getNonRivalFlags($support_id);
            foreach ($non_rivaling_flags as $active_flag_id) {
                $this->restoreActivity($active_flag_id);

                // If the flag is active, then the support is inactive.
                if ($this->claimRepository->isClaimActive($active_flag_id)) {
                    $this->claimRepository->setClaimActive($support_id, false);
                }
            }
        }
        $isSupport = $this->claimRepository->getClaimByID($claim_id)->COS == "support";

        // this needs to be checking thesis flags for root claims
        // GRABS ALL FLAGS OF ORIGINAL CLAIM ---------------------------- BLUE ON DIAGRAM, 3
        // grabs all flaggers for non-rival root claims
        // all tooearly or toolate //$activity
        // *AND* all support flags because while it doesn't occur for the first run through, when a support is put into the parameters, it'll check all reason/rule flags
        $flags = $this->claimRepository->getThesisFlagsNotRival($claim_id);
        $hasActiveFlag = false;
        foreach ($flags as $flag_id) {
            $this->restoreActivity($flag_id);
            if ($this->claimRepository->isClaimActive($flag_id)) {
                $this->claimRepository->setClaimActive($claim_id, false);
                $hasActiveFlag = true;
            }
            foreach ($this->claimRepository->getThesisRivals($claim_id) as $thesis_rival_id) {
                $this->restoreActivityRIVAL($thesis_rival_id);
            }
        }
        if (!$this->claimRepository->hasRival($claim_id)) {
            $this->claimRepository->setClaimActive(
                $claim_id,
                ($isSupport || $hasActiveSupport) && !$hasActiveFlag
            );
        }
    }
    /**
     * This function has the same functionality as restoreActivity, but for rivals. The key difference is it must account for the “mutualistic flagging" relationship that is unique to rivals (that is, they flag each other equally). This function determines when one of the rival claims may reach an uncontested state (as the typical state for a rivals pair is equal contestation).
     *
     * @param int $claim_id The root claim ID to check
     */
    private function restoreActivityRIVAL($claim_id)
    {
        // Finds the flagger, and continues the recursion by invoking
        // restoreActivity
        // set of all too-early and too-late
        // looks for normal non-rival flags for this rivaling claim.
        foreach ($this->claimRepository->getNonRivalFlags($claim_id) as $non_rival_flag_id) {
            $this->restoreActivity($non_rival_flag_id);
        }
        // check active status of flagging claims OF RIVAL COMPANION
        // finds the companion
        $rivaling = '';
        foreach ($this->claimRepository->getThesisRivals($claim_id) as $thesis_rival_id) {
            // found rival pair!
            $rivaling = $thesis_rival_id;
            // $rivaling is Rival B.
        }
        // recurse. run restoreActivity on all flags and supports (not thesis rivals).
        foreach ($this->claimRepository->getNonRivalFlags($rivaling) as $rivals_flag_id) {
            $this->restoreActivity($rivals_flag_id);
        }
        // rivalA : supportless --> rivalb should be active. does rivalb have active TE/TL?
        // rivalB : needs to be active AND it doesn't have a too early / too late AND needs at least one support itself
        $isChallengedThis = !$this->claimRepository->hasActiveSupports($claim_id) || $this->claimRepository->hasActiveFlagsNonRival($claim_id);
        $isChallengedRival = !$this->claimRepository->hasActiveSupports($rivaling) || $this->claimRepository->hasActiveFlagsNonRival($rivaling);

        if ($isChallengedThis === $isChallengedRival) {
            $this->claimRepository->setClaimActive($claim_id, false);
            $this->claimRepository->setClaimActive($rivaling, false);
        } elseif (!$isChallengedThis) {
            $this->claimRepository->setClaimActive($claim_id, true);
            $this->claimRepository->setClaimActive($rivaling, false);
        } else {
            $this->claimRepository->setClaimActive($claim_id, false);
            $this->claimRepository->setClaimActive($rivaling, true);
        }
    }
}

/*
IF a claim is a claim/flag that is NOT rivalling another:
- it is active iff:
- it has at least one support
- AND it has no active flags
IF a claim is a support:
- it is active iff:
- it has no active flags
IF a claim rivals another claim:
- it is active iff:
- AND it has at least one active support
- AND it has no active flags
- AND its rival should be inactive
- (i.e. it has no active support/or it has active flags.)
Clarity: if both rivals are supported and unflagged (i.e. they should be active), they are both inactive. otherwise they follow normal activity rules.
claim IDs loosely order the tree, so we can just iterate backwards 
across those instead of recursing.
there should be a separate function that simply determines true/false if a 
claim should be active, based on its children and rivals.
rivals currently need to be resolved together. wonder what to do about this.
*/