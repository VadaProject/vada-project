<!-- <?php require_once 'config/db_connect.php'; ?> -->
<?php
require_once 'functions/sortClaims.php';
require_once 'functions/doesThesisFlag.php';
require_once 'functions/restoreActivity.php';
require_once 'functions/Database.php';
use Database\Database;

$conn = db_connect();
$topic_id = $_GET['id'];
$topic_obj = Database::getTopic($topic_id);
$topic_name = htmlspecialchars($topic_obj->name ?? "undefined");
$topic_description = $topic_obj->description ? htmlspecialchars($topic_obj->description) : null;
$PAGE_TITLE = "Topic: \"$topic_name\"";
?>
<?php require 'includes/page_top.php'; ?>
<style>
    footer,
    .topnav {
        position: fixed;
    }
</style>

<div class="wrapper">
    <h2 style="margin-bottom: 0.5rem;">Topic:
        <?php echo $topic_name; ?>
    </h2>
    <?php
    if (isset($topic_description)) { ?>
        <p style='max-width: 50rem; margin-inline: auto;'><b>Description:</b>
        <?php echo $topic_description; ?>
        </p>
    <?php
    }
    if (!isset($topic_id)) {
        // header("Location: index.php");
        return;
    }
    if (!isset($topic_obj)) {
        // header("Location: index.php");
        return;
    }
    ?>
    <p>
        <a class="btn btn-primary"
            href="add.php?topic=<?php echo $topic_id; ?>">Add New Claim</a>
    </p>
    <?php
    restoreActivityTopic($topic_id);
    if (count(Database::getAllRootClaimIDs($topic_id)) == 0 && count(Database::getRootRivals($topic_id)) == 0) {
        echo "<p>Topic \"$topic_name\" is empty.</a></p>";
        return;
    }
    ?>
    <ul>
        <?php
        $root_claim = Database::getAllRootClaimIDs($topic_id);
        foreach ($root_claim as $claim_id) {
            sortclaims($claim_id);
        }
        $root_rivals = Database::getRootRivals($topic_id);
        foreach ($root_rivals as $claim_id) {
            sortclaimsRIVAL($claim_id);
        }

        ?>
    </ul>
</div>
<?php include 'includes/page_bottom.php'; ?>
<script src="assets/scripts/ajaxindex.js"></script>