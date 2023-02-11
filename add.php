<?php
$PAGE_TITLE = "Add claim";
include 'includes/page_top.php'; ?>
<?php require_once 'functions/supportingForm.php'; ?>
<main class="page-container">
    <form method="POST" id="myForm" action="insert.php">
        <div>
        <?php topicInput($topic); ?>
        <!-- Subject and target property input -->
        <?php subjectTargetInput(); ?>
        <?php supportMeans(); ?>
        <button type="submit" id="submit">Submit</button>
        </div>
    </form>
    <script src="assets/scripts/add.js"></script>
</main>

<?php include 'includes/page_bottom.php'; ?>
