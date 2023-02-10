<!-- <?php require_once 'config/db_connect.php'; ?> -->
<?php
require_once 'functions/sortClaims.php';
require_once 'functions/doesThesisFlag.php';
require_once 'functions/noSupports.php';
require_once 'functions/restoreActivity.php';
require_once 'functions/Database.php';
use Database\Database;

$conn = db_connect();
$topic = $_GET['topic'];
if (!isset($topic)) {
    echo "<h1>Error: topic is not defined. <a href='index.php'>Home</a></h1>";
    return;
}
$PAGE_TITLE = htmlspecialchars("Topic: \"$topic\"");
// end isset check
?>
<?php require 'includes/page_top.php'; ?>
<style>
    footer,
    .topnav {
        position: fixed;
    }
</style>

<div class="wrapper">
    <h2>Topic:
        <?php echo $topic; ?>
    </h2>
    <p>
        <a class="btn btn-primary"
            href="add.php?topic=<?php echo $topic; ?>">Add New Claim</a>
    </p>
    <ul>
        <?php
        $root_claim = Database::getAllRootClaimIDs($topic);
        foreach ($root_claim as $claim_id) {
            restoreActivity($claim_id);
        }
        foreach ($root_claim as $claim_id) {
            sortclaims($claim_id);
        }
        $root_rivals = Database::getRootRivals($topic);
        foreach ($root_rivals as $claim_id) {
            restoreActivityRIVAL($claim_id);
        }
        $thesis_rivals = Database::getAllThesisRivals($topic);
        foreach ($thesis_rivals as $claim_id) {
            restoreActivityRIVAL($claim_id);
        }
        foreach ($root_rivals as $claim_id) {
            sortclaimsRIVAL($claim_id);
        }
        ?>
    </ul>
</div>
<?php include 'includes/page_bottom.php'; ?>
<script src="assets/scripts/ajaxindex.js"></script>
