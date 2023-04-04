<?php
/*
This displays the argument in full detail and pushes any user interaction/submissions to add.php.
*/
require_once 'functions/Database.php';
require_once 'functions/sortClaims.php';
use Database\Database;

function flagModalButton(string $supportMeans)
{
    ?>
    <button class="openmodal myBtn" onclick="showFlagModal();">
        <?php echo htmlspecialchars("Flag $supportMeans"); ?>
    </button>
<?php
}

function makeFlaggingModal(int $claim_id)
{
    ?>
    <div class="modal myModal" id="flagModal">
        <div class="modal-content">
            <span class="close" onclick="closeModal(this);">&times;</span>
            <iframe src="<?php echo "addflag.php?id=$claim_id"; ?>"></iframe>
        </div>
    </div>
<?php }
function makeSupportingModal(int $claim_id)
{
    ?>
    <div class="modal myModal" id="supportModal">
        <div class="modal-content">
            <span class="close" onclick="closeModal(this);">&times;</span>
            <iframe src="<?php echo "addsupport.php?id=$claim_id"; ?>"></iframe>
        </div>
    </div>
<?php }

function displayInference(object $claim)
{
    $FOS = 'flagging';
    $claimIDFlagged = Database::getFlaggedClaim($claim->claimID);
    $flaggedClaim = Database::getClaim($claimIDFlagged);
    if (!isset($claimIDFlagged)) {
        echo "<h2>Error: claim #{$claim->claimID} has no flagging relation set. This is probably a bug, please contact an administrator.</h2>";
        return;
    }
    if (!isset($flaggedClaim)) {
        echo "<h2>Error: claim #{$claim->claimID} flags claim #{$claimIDFlagged}, but that claim does not exist. This is probably a bug, please contact an administrator.</h2>";
        return;
    }
    ?>
    <table>
        <tr>
            <th>Thesis Statement (
                <?php echo "<a href='?id=$claimIDFlagged'>#$claimIDFlagged</a>" ?>)
            </th>
            <td>
                <span class="subject-display">
                    <?php echo $flaggedClaim->subject; ?>
                </span>
                <span class="target-display">
                    <?php echo $flaggedClaim->targetP; ?>
                </span>.
            </td>
            <td>
                <?php echo "<a class='btn' href='?id=$claimIDFlagged'>Flag Thesis</a>" ?>
            </td>
        </tr>
        <tr>
            <th>Reason Statement</th>
            <td>
                <span class="subject-display">
                    <?php echo $claim->subject; ?>
                </span>
                <span class="reason-display">
                    <?php echo $claim->reason; ?>
                </span>.
            </td>
            <td rowspan="2">
                <?php flagModalButton($claim->supportMeans); ?>
            </td>
        </tr>
        <tr>
            <th>Rule & Example Statement</th>
            <td>
                Whomever/Whatever
                <span class="reason-display">
                    <?php echo $claim->reason; ?>
                </span>
                <span class="target-display">
                    <?php echo $flaggedClaim->targetP; ?>
                </span>,
                as in the case of
                <span class="example-display">
                    <?php echo $claim->example; ?>
                </span>.
            </td>
        </tr>
    </table>
<?php
}

function displayTarka(object $claim)
{ ?>
    <p>ⓘ <i>Tarka</i> (<b>philosophical argument</b>) allows for supplementary
        free-form discussion.</p>
    <p>Please explain argument in the Disqus comments section below.</p>
<?php
}

function makeLinkAnchor(string $href = '') {
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

function displayTestimony(object $claim)
{
    $url = $claim->URL ?? $claim->citationURL ?? "";
    ?>
    <p><label>Transcription:</label>
    <!-- TODO: style this -->
    <textarea readonly
    style="display: block; max-width: 100%; min-width: 100%; height: auto"><?php echo $claim->transcription; ?></textarea>
    <p><label>Citation:</label>
    <?php echo $claim->citation; ?>
    </p>
    <?php makeLinkAnchor($url); ?>
</p>
<?php }

function displayPerception(object $claim)
{
    $url = $claim->URL ?? $claim->citationURL ?? "";
    ?>
    <p><label>Citation:</label>
        <?php echo $claim->citation; ?>
    </p>
    <?php makeLinkAnchor($url); ?>
    <p><label>Timestamp:</label>
        <?php echo $claim->vidtimestamp; ?>
    </p>
<?php }

function displaySupport(object $claim)
{
    echo '<h3>' . get_image('support') . '</h3>';
    echo "<p><label>Support Means:</label> {$claim->supportMeans}</p>";
    switch ($claim->supportMeans) {
        case 'Inference':
            displayInference($claim);
            break;
        case 'Tarka':
            displayTarka($claim);
            break;
        case 'Perception':
            displayPerception($claim);
            break;
        case 'Testimony':
            displayTestimony($claim);
            break;
        default:
            echo "<h3>Error: Claim #{$claim->claimID} has an invalid support means.<h3>";
            return;
    }
    makeFlaggingModal($claim->claimID);
    if ($claim->supportMeans !== "Inference" && $claim->supportMeans !== "Tarka") {
        flagModalButton($claim->supportMeans);
    }
}

function displayNotSupport(object $claim)
{
    ?>
    <div>
        <button class="btn" onclick="showFlagModal();">Flag Claim</button>
        <button class="btn" onclick="showSupportModal();">Support Claim</button>
    </div>
    <?php makeFlaggingModal($claim->claimID); ?>
    <?php makeSupportingModal($claim->claimID); ?>
<?php
}
?>
<?php
$claim_id = $_GET['id']; // get claim id from URL search tags
$claim = Database::getClaim($claim_id);
$PAGE_TITLE = "Claim #$claim_id";
include 'includes/page_top.php'; ?>
<main class="page-container">
    <?php

    if (is_null($claim)) {
        echo "<h2>Error: a claim with the ID #$claim_id does not exist.</h2>";
        return;
    }
    $topic_id = htmlspecialchars($claim->topic);
    ?>
    <h2>
        <?php echo "Claim #$claim_id" ?>
    </h2>
    <?php
    if ($claim->COS == 'claim') { ?>
        <span class="subject-display">
            <?php echo $claim->subject ?>
        </span>
        <span class="target-display">
            <?php echo $claim->targetP ?>
        </span>.
    <?php }
    if ($claim->COS == 'support') {
        displaySupport($claim);
    } else {
        displayNotSupport($claim);
    }
    ?>
    <script>
        $(document).ready(function () {
            $("#submit").click(function () {
                alert("AAAA");
                window.alert("Submitted!");
                window.location.assign("topic.php?topic=<?php echo $topic_id; ?>");
                $.post($("#flagForm").attr("action"),
                    $("#flagForm :input").serializeArray(),
                    function (info) {
                        $("#result").html(info);
                    });
                clearInput();
            });
            $("#flagForm").submit(function () {
                return false;
            });

            function clearInput() {
                $("#flagForm :input").each(function () {
                    $(this).val('');
                });
            }
        });
    </script>
    </div>
    </div>
    <script>
        function showFlagModal() {
            $(".modal#flagModal").show();
        }
        function showSupportModal() {
            $(".modal#supportModal").show();
        }
        function closeModal(el) {
            $(el).parents(".modal").hide();
        }

        function iframeResize(el) {
            if (el) {
                // here you can make the height, I delete it first, then I make it again
                const height = el.contentWindow.document.body.scrollHeight;
                el.style.height = `${height + 20}px`;
            }
        }
        for (const iframe of document.querySelectorAll("iframe")) {
            iframe.addEventListener("load", () => iframeResize(iframe));
            window.addEventListener("resize", () => iframeResize(iframe));
            iframe.contentWindow.addEventListener("resize", () => {
                iframeResize(iframe)
                console.log("yum");
            });
            // setInterval(() => iframeResize(iframe), 500);
        }

    </script>
    <div class="x">
        <div id="disqus_thread"></div>
        <script>
            /**
            *  RECOMMENDED CONFIGURATION VARIABLES: EDIT AND UNCOMMENT THE SECTION BELOW TO INSERT DYNAMIC VALUES FROM YOUR PLATFORM OR CMS.
            *  LEARN WHY DEFINING THESE VARIABLES IS IMPORTANT: https://disqus.com/admin/universalcode/#configuration-variables    */
            var disqus_config = function () {
                this.page.url = document.write(window.location.href); // Replace PAGE_URL with your page's canonical URL variable
                this.page.identifier = document.write(window.location.href); // Replace PAGE_IDENTIFIER with your page's unique identifier variable
            };
            (function () { // DON'T EDIT BELOW THIS LINE
                var d = document,
                    s = d.createElement('script');
                s.src = 'https://vadaproject.disqus.com/embed.js';
                s.setAttribute('data-timestamp', +new Date());
                (d.head || d.body).appendChild(s);
            })();
        </script>
        <noscript>Please enable JavaScript to view the <a
                href="https://disqus.com/?ref_noscript">comments powered by
                Disqus.</a></noscript>
    </div>
</main>
<?php include 'includes/page_bottom.php'; ?>
<style>
    .card {
        border: 3px double black;
        padding-inline: 1rem;
        margin-bottom: 1rem;
    }

    label {
        font-weight: bold;
        display: inline;
    }

    table {
        background: white;
    }

    th {
        text-decoration: underline;
        text-align: left;
    }

    td,
    th {
        padding: 1rem;
        border: 1px solid;
    }

    iframe {
        display: block;
        border: none;
        width: 100%;
        min-height: 80vh;
        max-height: 80vh !important;
    }
</style>
