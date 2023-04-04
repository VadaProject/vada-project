<?php require_once 'functions/Database.php';
use Database\Database; ?>
<?php require 'includes/page_top.php'; ?>
<main class="page-container">
    <h2>Welcome!</h2>
    <p>This is a website for discussion and learning that is based on Sanskritic
        traditions of epistemology, logic and debate. Users can create new
        claims using protocols of <i>vāda</i> and <i>pramāṇavāda</i>, or
        participate in and observe pre-existing debates.</p>
    <h3>Want to start a new debate?</h3>
    <a class="btn btn-primary" href="addtopic.php">Add New Topic</a>
    <h3>Topics to select from:</h3>
    <ul class="topics-list">
            <?php
            foreach (Database::getAllTopics() as $topic_row) {
                $id = $topic_row["id"];
                $name = htmlspecialchars($topic_row["name"]);
                $desc = htmlspecialchars($topic_row["description"] ?? "");
                $topic_url = "topic.php?id=$id";
                ?>
                <li>
                    <a class="btn topic-btn" href="<?php echo $topic_url; ?>">
                        <?php echo $name; ?>
                    </a>
                    <?php
                    if (strlen($desc) > 0) { ?>
                        – <span class="topic-description">
                            <?php
                            echo $desc;
                            ?>
                        </span>
                    <?php } ?>
                </li>
                <?php
            } ?>
        </ul>
</main>
<?php include 'includes/page_bottom.php'; ?>