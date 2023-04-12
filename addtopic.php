<?php
require "vendor/autoload.php";


use Vada\Model\TopicRepository;
use Vada\Model\Database;
use Vada\Model\Topic;

$pdo = Database::connect();
$topicRepository = new TopicRepository($pdo);

if (isset($_POST["name"])) {
    try {
        $topic = new Topic(
            id: -1,
            name: $_POST["name"],
            description: $_POST["description"] ?? null
        );
        $topicRepository->insert($topic);
        echo "Inserted!";
        header("Location: topic.php?tid={$topic->id}");
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
    <h2>Add New Topic</h2>
    <p>This will create a new public topic.</p>
    <form method="POST" target="_parent">
        <div>
            <div>
                <label for="topic">Topic</label>
                <input class="w-100" type="text" id="topicInput" name="name"
                    placeholder="Enter topic name..." required maxlength="100">
                <label for="description">Description (optional)</label>
                <input class="w-100" id="description" name="description"
                    placeholder="Enter a short description of the topic..."
                    maxlength="255">
            </div>
            <button class="btn-primary" type="submit"
                id="submit">Submit</button>
        </div>
    </form>
    <script src="assets/scripts/add.js?timestamp=20230219"></script>
</main>

<?php include 'includes/page_bottom.php'; ?>