<?php
/** @file Vada Project homepag. */

namespace Vada;

require __DIR__ . "/../vendor/autoload.php";
require __DIR__ . "/../config/.env.php";

$db = Model\Database::connect();
$groupRepository = new Model\GroupRepository($db);
$userAuthenticator = new Model\UserAuthenticator($groupRepository, new Model\UserSessionManager("VadaGroups"));
$topicRepository = new Model\TopicRepository($db);

$accessCode = $_POST["accessCode"] ?? null;
if (isset($accessCode)) {
    try {
        $group_id = $userAuthenticator->tryJoinGroup($accessCode);
        $group = $groupRepository->getGroupByID($group_id);
        $group_name_escaped = htmlspecialchars($group->name);
        $success_msg = "<span class='success'>Successfully joined \"$group_name_escaped\"</span>";
    } catch (\Exception $e) {
        $error = "<span class='error'>" . $e->getMessage() . "</span>";
    }
}
if (isset($_GET["leaveGroup"])) {
    $group_id = intval($_GET["leaveGroup"]);
    $userAuthenticator->leaveGroup($group_id);
    header("Location: index.php");
}

require __DIR__ . '/../includes/page_top.php';
?>
<main class="page-container">
    <section>
        <h2>Welcome!</h2>
        <p>This is a website for discussion and learning that is based on
            Sanskritic
            traditions of epistemology, logic and debate. Users can create
            new
            claims using protocols of <i>vāda</i> and <i>pramāṇavāda</i>, or
            participate in and observe pre-existing debates.</p>
    </section>
    <div
        style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); grid-gap: 1rem;">
        <form method="POST">
            <h2>Join a group</h2>
            <div>
                <label>Group code</label>
                <input type="text" id="accessCode" name="accessCode"
                    placeholder="ABCDEF123" required>
                <input type="submit" class="btn btn-primary" value="Submit">
            </div>
            <div>
                <output class="success">
                    <?php echo $success_msg ?? null ?>
                </output>
                <output class="error">
                    <?php echo $error ?? null ?>
                </output>
            </div>
        </form>
        <div>
            <h2>Create a group</h2>
            <p>Create a new discussion group to get a group code.</p>
            <p><a class="btn btn-primary" href="creategroup.php">New group</a></p>
        </div>
    </div>
    </div>
    <h2>Your groups</h2>
    <?php
    if (count($userAuthenticator->getAllActiveGroups()) == 0) {
        echo "<p>You are not currently in a group. To obtain an access code,
        please contact a system administrator.</p>";
    }
    foreach ($userAuthenticator->getAllActiveGroups() as $group) { ?>
        <article>
            <h3>
                <?php echo htmlspecialchars($group->name) ?>
                (<code><?php echo htmlspecialchars($group->access_code) ?></code>)
            </h3>
            <p>
                <b>Description:</b>
                <?=htmlspecialchars($group->description ?: "(no description)")?>
            </p>
            <p>
                <a class="btn btn-primary"
                    href="addtopic.php?gid=<?=$group->id?>">Add topic</a>
                <a class="btn" href="?leaveGroup=<?=$group->id?>">Leave
                    group</a>
            </p>
            <?php
            $topics = $groupRepository->getTopicsOfGroup($group->id);
            $topics = array_map(fn($tid) => $topicRepository->getTopicByID($tid), $topics);
            $topicsList = new View\TopicsList($topics);
            $topicsList->render();
            ?>
        </article>
    <?php } ?>
    <!-- <h3>Want to start a new debate?</h3>
    <a class="btn btn-primary" href="addtopic.php">Add New Topic</a>
    <h3>Public topics:</h3>
    <?php
    $topics = $topicRepository->getAllTopics();
    $topicsList = new View\TopicsList($topics);
    $topicsList->render();
    ?> -->
</main>
<?php require __DIR__ . '/../includes/page_bottom.php'; ?>