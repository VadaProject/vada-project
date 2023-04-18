<?php
require __DIR__ . "/../vendor/autoload.php";

use Vada\View\SupportingForm;
use Vada\Model\ClaimRepository;
use Vada\Model\TopicRepository;
use Vada\Model\Database;

$db = Database::connect();
$claimRepository = new ClaimRepository($db);
$topicRepository = new TopicRepository($db);

// handle database insertion, then render page.
require "insert.php";

$PAGE_TITLE = "Add claim";
require __DIR__ . '/../includes/page_top.php';

$topic_id = intval($_GET["tid"]) ?? null;
if (empty($topic_id)) {
    echo "Invalid URL params.";
    return;
}
$topic = $topicRepository->getTopicByID($topic_id);
if (empty($topic)) {
    echo "Error: No topic with id $topic_id exists.";
    return;
}

?>
<main class="page-container">
    <h3>Adding new thesis to "<?php echo htmlspecialchars($topic->name) ?>"</h3>
    <form method="POST" id="myForm" target="_parent">
        <div>
            <?php
        $supportingForm = new SupportingForm();
        $supportingForm->showError($error ?? null); // does nothing if null
        $supportingForm->topicInput($topic_id); ?>
        <!-- Subject and target property input -->
        <?php $supportingForm->subjectTargetInput(); ?>
        <?php $supportingForm->supportMeansInput(); ?>
        <button type="submit" id="submit">Submit</button>
        </div>
    </form>
    <script src="assets/scripts/add.js?timestamp=20230219"></script>
</main>

<?php require __DIR__ . '/../includes/page_bottom.php'; ?>