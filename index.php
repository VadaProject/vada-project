<?php
/** @file Vada Project homepag. */

namespace Vada;

require "vendor/autoload.php";

$db = Model\Database::connect();
$topicRepository = new Model\TopicRepository($db);
?>
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
    <?php
    $topics = $topicRepository->getAllTopics();
    $topicsList = new View\TopicsList($topics);
    $topicsList->render();
    ?>
</main>
<?php require 'includes/page_bottom.php'; ?>