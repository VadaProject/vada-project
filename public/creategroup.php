<?php
// TODO: in a future revision this needs to be an admin only feature.
namespace Vada;

require __DIR__ . "/../vendor/autoload.php";

use Vada\Model\TopicRepository;
use Vada\Model\Database;

$db = Database::connect();
$topicRepository = new TopicRepository($db);
$groupRepository = new Model\GroupRepository($db);
$userAuthenticator = new Model\UserAuthenticator($groupRepository, new Model\UserSessionManager("VadaGroups"));

$group_name_escaped = "public";
if (isset($_GET["gid"])) {
    $group_id = intval($_GET["gid"]);
    if (!$userAuthenticator->isInGroup($group_id)) {
        exit("Access denied.");
    }
    $group = $groupRepository->getGroupByID($group_id);
    $group_name_escaped = "<b>" . htmlspecialchars($group->name) . "</b>";
}

$groupName = $_POST["groupName"] ?? null;
if (isset($_POST["name"])) {
    try {
        $group = [
            "name" => $_POST["name"],
            "description" => $_POST["description"] ?? null,
        ];
        $accessCode = $groupRepository->insertGroup(...$group);
        $userAuthenticator->tryJoinGroup($accessCode);
        echo "Inserted!";
        header("Location: index.php");
        exit;
    } catch (\Exception $e) {
        $error = htmlspecialchars($e->getMessage());
        error_log($e->getMessage());
        echo "Internal server error.";
        exit;
    }
}

$PAGE_TITLE = "New Topic";
require __DIR__ . '/../includes/page_top.php';
?>
<main class="page-container">
    <h2>Create group</h2>
    <p>This will create a new group.</p>
    <form method="POST" target="_parent">
        <div>
            <div>
                <label for="topic">Group</label>
                <input class="w-100" type="text" id="topicInput" name="name"
                    placeholder="Enter group name..." required maxlength="100">
                <label for="description">Description (optional)</label>
                <input class="w-100" id="description" name="description"
                    placeholder="Enter a short description of the topic..."
                    maxlength="255">
            </div>
            <p>
            <button class="btn-primary" type="submit"
                id="submit">Submit</button>
</p>
        </div>
    </form>
</main>

<?php require __DIR__ . '/../includes/page_bottom.php'; ?>