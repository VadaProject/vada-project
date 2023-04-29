<?php
/**
 * This page displays the argument in full detail.
 * It also shows the addflag and addsupport modals.
 * Lastly it contains the Disqus integration. TODO: abstract that.
 */
namespace Vada;

require __DIR__ . "/../vendor/autoload.php";

use Vada\Model\ClaimRepository;
use Vada\Model\TopicRepository;
use Vada\Model\Database;
use Vada\View\ClaimDetails;

// TODO: ugly bad per-page dependency injection.
$db = Database::connect();
$claimRepository = new ClaimRepository($db);
$topicRepository = new TopicRepository($db);
$userAuthenticator = new Model\UserAuthenticator(new Model\GroupRepository($db), new Model\CookieManager("VadaGroups"));
?>
<?php
if (empty($_GET['cid'])) {
    exit("Error: missing URL param 'cid'.");
}
$claim_id = intval($_GET['cid']); // get claim id from URL search tags
$claim = $claimRepository->getClaimByID($claim_id);
if (empty($claim)) {
    exit("Error: claim $claim_id does not exist.");
}
if (!$userAuthenticator->canAccessTopic($claim->topic_id)) {
    exit("Error: Access denied. Please join a group with access to this topic.");
}
$parent_claim = $claimRepository->getClaimByID($claim->flagged_id ?? null);
$PAGE_TITLE = "Claim #{$claim->display_id}";
$claimDetails = new ClaimDetails($claim, $parent_claim);

require __DIR__ . '/../includes/page_top.php'; ?>
<main class="page-container">
    <?php
    $claimDetails->render();
    ?>
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
<?php require __DIR__ . '/../includes/page_bottom.php'; ?>
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
