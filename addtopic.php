<?php
require_once 'functions/supportingForm.php';
require_once 'functions/Database.php';
use SupportingForm\SupportingForm;
use Database\Database;

$conn = db_connect();

if (isset($_POST["name"])) {
    try {
        $name = $_POST["name"];
        $description = $_POST["description"] ?? null;
        echo "Inserted!";
        $topic_id = Database::createNewTopic($name, $description);
        header("Location: topic.php?id={$topic_id}");
        exit;
    } catch (Exception $e) {
        error_log($e->getMessage());
        echo "Internal server error.";
        exit;
    }
}

$PAGE_TITLE = "New Topic";
require 'includes/page_top.php';
?>
<main class="page-container">
    <h2>New Topic</h2>
    <p>Flavor text here!</p>
    <form method="POST" target="_parent">
        <div>
            <div>
                <label for="topic">Topic</label>
                <input class="w-100" type="text" id="topicInput" name="name" placeholder="Enter topic name..." required>
                <label for="description">Description (optional)</label>
                <textarea id="description" name="description" placeholder="Enter a description of the topic..."></textarea>
            </div>
            <button class="btn-primary" type="submit" id="submit">Submit</button>
        </div>
    </form>
    <script src="assets/scripts/add.js?timestamp=20230219"></script>
</main>

<?php include 'includes/page_bottom.php'; ?>