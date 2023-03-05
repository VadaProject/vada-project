<?php require_once 'functions/Database.php';
use Database\Database; ?>
<?php require 'includes/page_top.php'; ?>
<main class="page-container text-center">
    <h2>Welcome!</h2>
    <p>This is a website for discussion and learning that is based on Sanskritic traditions of epistemology, logic and debate. Users can create new claims using protocols of <i>vāda</i> and <i>pramāṇavāda</i>, or participate in and observe pre-existing debates.</p>
    <h3>Topics to select from:</h3>
    <div class="topics-list">
        <?php
        foreach (Database::getAllTopics() as $topic) {
            $topic_escaped = str_replace(" ", "+", htmlspecialchars($topic));
            $topic_url = "topic.php?topic=$topic_escaped";
            ?>
            <a class="btn topic-btn" href="<?php echo $topic_url; ?>">
                <?php echo htmlspecialchars($topic); ?>
            </a>
            <?php
        } ?>
    </div>
    <p>Want to start a new topic?</p>
    <a class="btn btn-primary" href="add.php">Add New Claim</a>
</main>
<?php include 'includes/page_bottom.php'; ?>