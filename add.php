<?php require_once 'functions/supportingForm.php'; ?>
<?php
$PAGE_TITLE = "Add claim";
include 'includes/page_top.php';
?>
<main class="page-container">
    <form method="POST" id="myForm" action="insert.php">
        <div>
        <?php
        $topic = $_GET["topic"];
        topicInput($topic); ?>
        <!-- Subject and target property input -->
        <?php subjectTargetInput(); ?>
        <?php supportMeans(); ?>
        <button type="submit" id="submit">Submit</button>
        </div>
    </form>
    <script src="assets/scripts/add.js"></script>
</main>

<?php include 'includes/page_bottom.php'; ?>
