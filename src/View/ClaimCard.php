<?php
namespace Vada\View;


class ClaimCard
{
    private object $claim;
    private object|null $rival_claim;
    private string|null $flag_type;

    public function __construct(object $claim, string $flag_type = null, object $rival_claim = null)
    {
        $this->claim = $claim;
        $this->rival_claim = $rival_claim;
        $this->flag_type = $flag_type;
    }


    public function render()
    {
        ?>
        <span class="stem"></span>
        <input id="<?php echo $this->claim->display_id; ?>" type="checkbox"
            name="active_claim">
        <label class="claim" <?php if ($this->rival_claim) { ?>style="background:#FFFFE0" <?php } ?>
            for="<?php echo $this->claim->display_id; ?>">
            <?php

            switch ($this->flag_type) {
                case 'supporting': // Support
                    echo Icon::Support->getHeading("h3");
                    ;
                    echo '<div class="">' .
                        htmlspecialchars($this->claim->supportMeans) .
                        '</div>';
                    if ($this->claim->supportMeans == 'Inference') {
                        $reason =
                            htmlspecialchars("{$this->claim->subject} {$this->claim->reason}.");
                        $rule =
                            htmlspecialchars("Whatever/Whomever {$this->claim->reason}, {$this->claim->targetP} as in the case of {$this->claim->example}.");
                        echo '<div class="claim_body text-left">';
                        echo "<p><b>Reason:</b> $reason</p>";
                        echo "<p><b>Rule & Example:</b> $rule";
                        echo "</div>";
                    }
                    if (
                        $this->claim->supportMeans == 'Testimony' ||
                        $this->claim->supportMeans == 'Perception'
                    ) {
                        echo '<div class="claim_body text-left"><b>Citation:</b> ' .
                            htmlspecialchars($this->claim->citation) .
                            '</div>';
                    }
                    break;
                case '': // Thesis
                    if ($this->rival_claim) {
                        echo Icon::Rival->getHeading("h3");
                        echo "<h4>Contests #{$this->rival_claim->display_id}</h4>";
                    }
                    echo Icon::Thesis->getHeading("h2");
                    echo '<div class="claim_body text-left">' .
                        htmlspecialchars($this->claim->subject) .
                        ' ' .
                        htmlspecialchars($this->claim->targetP) .
                        '</div>';
                    break;
                default:
                    echo Icon::Flag->getHeading("h3");
                    ;
                    echo htmlspecialchars($this->flag_type);
                    echo '<div class="claim_body">';
                    echo Icon::Thesis->getHeading("p");
                    echo '<div class="claim_body text-left">';
                    echo '<p>' . $this->claim->subject . ' ' . $this->claim->targetP . '</p>';
                    echo '</div>';
                    echo '</div>';
            }
            if ($this->claim->active != 1) {
                echo Icon::Contested->getHeading("p");
            }
            echo "<div>#{$this->claim->display_id}</div>";

            $url = "details.php?cid={$this->claim->id}";
            ?>
            <a class="btn btn-primary" href="<?php echo $url; ?>">
                Details
            </a>
        </label>
        <?php
    }
}