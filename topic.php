<?php
require "vendor/autoload.php";

use \Vada\Model\Database;

$pdo = Database::connect();
$claimRepository = new Vada\Model\ClaimRepository($pdo);
$topicRepository = new Vada\Model\TopicRepository($pdo);
$activityController = new Vada\Controller\ActivityController($claimRepository);

if (!isset($_GET['tid'])) {
    exit("Error: ID not given.");
}
$topic = $topicRepository->getTopicByID($_GET['tid']);
if (!isset($topic)) {
    exit("Error: ID does not exist");
}

$PAGE_TITLE = "Topic: {$topic->name}";
?>
<?php require 'includes/page_top.php'; ?>
<style>
    footer,
    .topnav {
        position: fixed;
    }
</style>

<div class="wrapper">
    <h2 style="margin-bottom: 0.5rem;">Topic:
        <?php echo htmlspecialchars($topic->name); ?>
    </h2>
    <?php
    if ($topic->hasDescription()) { ?>
        <p style='max-width: 50rem; margin-inline: auto;'><b>Description:</b>
        <?php echo htmlspecialchars($topic->description ?? "(no description)"); ?>
    </p>
    <?php
    }
    ?>
    <p>
        <a class="btn btn-primary"
        href="add.php?tid=<?php echo $topic->id; ?>">Add New Claim</a>
    </p>
    <?php
    $activityController->restoreActivityTopic($topic);
    $claimTreeController = new Vada\Controller\ClaimTreeController($claimRepository, $topic);
    $claimTreeController->displayClaimTree();
    ?>
</div>
<?php include 'includes/page_bottom.php'; ?>
<script src="assets/scripts/ajaxindex.js"></script>