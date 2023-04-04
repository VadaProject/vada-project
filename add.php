<?php
require_once 'functions/supportingForm.php';
require_once 'functions/Database.php';
use SupportingForm\SupportingForm;
use Database\Database;

// handle database insertion, then render page.
require "insert.php";

$PAGE_TITLE = "Add claim";
require 'includes/page_top.php';

$topic_id = intval($_GET["topic"]) ?? null;
if (!isset($topic_id) || $topic_id == 0 || !isset($topic)) {
    echo "Invalid URL params. Redirecting...";
    header("Location: index.php");
}
if (!isset())
$topic = Database::getTopic($topic_id);
echo "Error: "
return

?>
<main class="page-container">
    <h3>Adding a new thesis to </h3>
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

<?php include 'includes/page_bottom.php'; ?>