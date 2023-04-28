<?php
require __DIR__ . "/../vendor/autoload.php";

use \Vada\Model\Database;

$db = Database::connect();
$claimRepository = new Vada\Model\ClaimRepository($db);
$topicRepository = new Vada\Model\TopicRepository($db);
$activityController = new Vada\Controller\ActivityController($claimRepository);

if (empty($_GET['tid'])) {
    exit("Error: url param `tid` not set.");
}
$topic_id = intval($_GET['tid']);
$topic = $topicRepository->getTopicByID($topic_id);
if (empty($topic)) {
    exit("Error: topic #$topic_id does not exist");
}

$PAGE_TITLE = "Topic: {$topic->name}";
?>
<?php require __DIR__ . '/../includes/page_top.php'; ?>
<style>
    footer,
    .topnav {
        position: fixed;
    }
</style>

<div class="wrapper">
    <h2 style="margin-bottom: 0.5rem;">Topic:
        <?=htmlspecialchars($topic->name)?>
    </h2>
    <p style='max-width: 50rem; margin-inline: auto;'><b>Description:</b>
        <?=$topic->getDescriptionHTML()?>
    </p>
    <p>
        <a class="btn btn-primary"
            href="add.php?tid=<?=$topic->id?>">Add New Claim</a>
    </p>
    <?php
    $activityController->restoreActivityTopic($topic->id);
    $claimTreeController = new Vada\Controller\ClaimTreeController($claimRepository, $topic->id);
    $claimTreeController->displayClaimTree();
    ?>
</div>
<?php require __DIR__ . '/../includes/page_bottom.php'; ?>
<script src="assets/scripts/ajaxindex.js"></script>