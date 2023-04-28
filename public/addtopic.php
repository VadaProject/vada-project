<?php
namespace Vada;

require __DIR__ . "/../vendor/autoload.php";

use Vada\Model\TopicRepository;
use Vada\Model\Database;

$db = Database::connect();
$topicRepository = new TopicRepository($db);
$groupRepository = new Model\GroupRepository($db);
$userAuthenticator = new Model\UserAuthenticator($groupRepository, new Model\CookieManager("VadaGroups"));

$group_name_escaped = "public";
if (isset($_GET["gid"])) {
    $group_id = intval($_GET["gid"]);
    if (!$userAuthenticator->isInGroup($group_id)) {
        exit("Access denied.");
    }
    $group = $groupRepository->getGroupByID($group_id);
    $group_name_escaped = "<b>" . htmlspecialchars($group->name) . "</b>";
}

if (isset($_POST["name"])) {
    try {
        $topic = new Model\Topic(
            id: -1,
            name: $_POST["name"],
            description: $_POST["description"] ?? null,
            ts: "now"
        );
        $topicRepository->insert($topic);
        if ($group_id) {
            $groupRepository->addTopicToGroup(topic_id: $topic->id, group_id: $group_id);
        }
        echo "Inserted!";
        header("Location: topic.php?tid={$topic->id}");
        exit;
    } catch (\Exception $e) {
        error_log($e->getMessage());
        echo "Internal server error.";
        exit;
    }
}

$PAGE_TITLE = "New Topic";
require __DIR__ . '/../includes/page_top.php';
?>
<main class="page-container">
    <h2>Add New Topic</h2>
    <p>This will create a new topic in
        <?php
        echo $group_name_escaped;
        ?>.
    </p>
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
</main>

<?php require __DIR__ . '/../includes/page_bottom.php'; ?>