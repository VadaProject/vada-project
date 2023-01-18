<?php require_once 'config/db_connect.php'; ?>
<?php require 'includes/page_top.php'; ?>
<?php
require_once 'functions/sortClaims.php';
require_once 'functions/doesThesisFlag.php';
require_once 'functions/noSupports.php';
require_once 'functions/restoreActivity.php';
require_once 'functions/Database.php';
use Database\Database;
$conn = db_connect();
if (isset($_GET['topic'])) {
    $topic = mysqli_real_escape_string($conn, $_GET['topic']);
}

// end isset check
?>

<div class="wrapper">
    <h2>Topic: <?php echo $topic; ?></h2>
    <p>
        <a class="btn btn-primary" href="add.php?topic=<?php echo $topic; ?>">Add New Claim</a>
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
<?php mysqli_close($conn); ?>
<script src="assets/scripts/ajaxindex.js"></script>
