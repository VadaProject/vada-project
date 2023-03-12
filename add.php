<?php require_once 'functions/supportingForm.php';
use SupportingForm\SupportingForm;
?>
<?php
$PAGE_TITLE = "Add claim";
include 'includes/page_top.php';
?>
<main class="page-container">
    <form method="POST" id="myForm" action="insert.php">
        <div>
        <?php
        $supportingForm = new SupportingForm();
        $topic = $_GET["topic"] ?? null;
        $supportingForm->topicInput($topic); ?>
        <!-- Subject and target property input -->
        <?php $supportingForm->subjectTargetInput(); ?>
        <?php $supportingForm->supportMeansInput(); ?>
        <button type="submit" id="submit">Submit</button>
        </div>
    </form>
    <script src="assets/scripts/add.js?timestamp=20230219"></script>
</main>

<?php include 'includes/page_bottom.php'; ?>