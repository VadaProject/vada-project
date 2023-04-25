<?php

namespace Vada\View;

use Vada\View\Icon;

class ClaimDetails
{

    private object $claim;
    private object|null $parent_claim;
    public function __construct(object $claim, object $parent_claim = null)
    {
        $this->claim = $claim;
        $this->parent_claim = $parent_claim;
    }

    public function render()
    {
        // this URL focuses the 
        $topic_url = "topic.php?tid={$this->claim->topic_id}#{$this->claim->display_id}";
        ?>
        <p><a href='<?=$topic_url?>'>Back to topic</a></p>
        <h2>
            <?php echo "Claim #{$this->claim->display_id}" ?>
        </h2>
        <?php
        if ($this->claim->COS == 'claim') {
            $this->displayThesisStatement($this->claim);
        }
        if ($this->claim->COS == 'support') {
            $this->displaySupport();
        } else {
            $this->displayNotSupport();
        }
    }

    public function flagModalButton()
    {
        ?>
        <button class="openmodal myBtn" onclick="showFlagModal();">
            <?=htmlspecialchars("Flag {$this->claim->supportMeans}")?>
        </button>
        <?php
    }
    function makeFlaggingModal()
    {
        ?>
        <div class="modal myModal" id="flagModal">
            <div class="modal-content">
                <span class="close" onclick="closeModal(this);">&times;</span>
                <iframe
                    src="<?="addflag.php?cid={$this->claim->id}"?>"></iframe>
            </div>
        </div>
    <?php }
    function makeSupportingModal()
    {
        ?>
        <div class="modal myModal" id="supportModal">
            <div class="modal-content">
                <span class="close" onclick="closeModal(this);">&times;</span>
                <iframe
                    src="<?="addsupport.php?cid={$this->claim->id}"?>"></iframe>
            </div>
        </div>
    <?php }

    private static function displayThesisStatement(object $claim)
    {
        ?>
        <span class="subject-display">
            <?=htmlspecialchars($claim->subject)?>
        </span>
        <span class="target-display">
            <?=htmlspecialchars($claim->targetP)?>
        </span>.
        <?php
    }
    private static function displayReasonStatement(object $claim)
    {
        ?>
        <span class="subject-display">
            <?=htmlspecialchars($claim->subject)?>
        </span>
        <span class="reason-display">
            <?=htmlspecialchars($claim->reason)?>
        </span>.
        <?php
    }

    private static function displayRuleAndExampleStatement(object $claim)
    {
        ?>
        Whomever/Whatever
        <span class="reason-display">
            <?=htmlspecialchars($claim->reason)?>
        </span>
        <span class="target-display">
            <?=htmlspecialchars($claim->targetP)?>
        </span>,
        as in the case of
        <span class="example-display">
            <?=htmlspecialchars($claim->example)?>
        </span>.
        <?php
    }

    function displayInference()
    {
        $FOS = 'flagging';
        if (empty($this->parent_claim)) {
            echo "<h2>Error: claim has no flagging relation set. This is probably a bug, please contact an administrator.</h2>";
            return;
        }
        ?>
        <table>
            <tr>
                <th>Thesis Statement (
                    <?php echo "<a href='?cid={$this->parent_claim->id}'>#{$this->parent_claim->display_id}</a>" ?>)
                </th>
                <td>
                    <?=self::displayThesisStatement($this->claim)?>
                </td>
                <td>
                    <?php echo "<a class='btn' href='?cid={$this->parent_claim->id}'>Flag Thesis</a>" ?>
                </td>
            </tr>
            <tr>
                <th>Reason Statement</th>
                <td>
                    <?php echo self::displayReasonStatement($this->claim) ?>
                </td>
                <td rowspan="2">
                    <?php $this->flagModalButton(); ?>
                </td>
            </tr>
            <tr>
                <th>Rule & Example Statement</th>
                <td>
                    <?php self::displayRuleAndExampleStatement($this->claim) ?>
                </td>
            </tr>
        </table>
        <?php
    }

    function displayTarka()
    { ?>
        <p>ⓘ <i>Tarka</i> (<b>philosophical argument</b>) allows for supplementary
            free-form discussion.</p>
        <p>Please explain argument in the Disqus comments section below.</p>
        <?php
    }

    private static function makeLinkAnchor(string $href = '')
    {
        if (!isset($href) || strlen($href) == 0) {
            return;
        }
        if ($href == "Enter URL" || $href == "NA") {
            // legacy placeholder values
            return;
        }
        echo "<p>";
        echo "<label>URL:</label> ";
        echo "<a target='_blank' href='$href'?>$href</a>";
        echo "</p>";
    }

    private function displayTestimony()
    {
        $url = $this->claim->URL ?? $this->claim->citationURL ?? "";
        ?>
        <p><label>Transcription:</label>
            <!-- TODO: style this -->
            <textarea readonly
                style="display: block; max-width: 100%; min-width: 100%; height: auto"><?=$this->claim->transcription?></textarea>
        <p><label>Citation:</label>
            <?=$this->claim->citation?>
        </p>
        <?php self::makeLinkAnchor($url); ?>
        </p>
    <?php }

    function displayPerception()
    {
        $url = $this->claim->URL ?? $this->claim->citationURL ?? "";
        ?>
        <p><label>Citation:</label>
            <?=$this->claim->citation?>
        </p>
        <?php self::makeLinkAnchor($url); ?>
        <p><label>Timestamp:</label>
            <?=$this->claim->vidtimestamp?>
        </p>
    <?php }

    function displaySupport()
    {
        echo Icon::Support->getHeading("h3");
        echo "<p><label>Support Means:</label> {$this->claim->supportMeans}</p>";
        switch ($this->claim->supportMeans) {
            case 'Inference':
                $this->displayInference();
                break;
            case 'Tarka':
                $this->displayTarka();
                break;
            case 'Perception':
                $this->displayPerception();
                break;
            case 'Testimony':
                $this->displayTestimony();
                break;
            default:
                echo "<h3>Error: Claim #{$this->claim->id} has an invalid support means.<h3>";
                return;
        }
        $this->makeFlaggingModal();
        if ($this->claim->supportMeans !== "Inference" && $this->claim->supportMeans !== "Tarka") {
            $this->flagModalButton();
        }
    }

    function displayNotSupport()
    {
        ?>
        <div>
            <button class="btn" onclick="showFlagModal();">Flag Claim</button>
            <button class="btn" onclick="showSupportModal();">Support Claim</button>
        </div>
        <?php $this->makeFlaggingModal(); ?>
        <?php $this->makeSupportingModal(); ?>
    <?php
    }
}