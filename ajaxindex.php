<?php require_once 'config/db_connect.php'; ?>
<?php require 'includes/page_top.php'; ?>
<?php
require_once 'functions/sortClaims.php';
require_once 'functions/doesThesisFlag.php';
require_once 'functions/noSupports.php';
require_once 'functions/restoreActivity.php';
require_once 'functions/haveRival.php';
require_once 'functions/Database.php';
use Database\Database;
$conn = db_connect();
if (isset($_GET['topic'])) {
    $topic = mysqli_real_escape_string($conn, $_GET['topic']);
}

// end isset check
?>

<div class="wrapper">
    <ul>
        <li class="noline">
            <span>
                <p><a href="add.php?topic=<?php echo $topic; ?>">Add New Claim To
                        This Topic</a></p>
            </span>
            <h3>TOPIC: <?php echo $topic; ?></h3>


            </center>
            <center>

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
            <!-- Modal -->
            <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-hidden="true">

                <div class="modal-dialog">
                    <div class="modal-content-a">
                        <b>Claim Details</b> <br>
                        <div class="modal-content-b">

                            <div class="modal-header">
                                <h4 class="modal-title"></h4>
                            </div>
                            <div id="modal-body">Press ESC button to exit.</div>
                        </div> <!-- modal-content-b -->
                    </div><!-- /.modal-content-a-->
                </div><!-- /.modal-dialog -->

            </div>
</div>
<?php include 'includes/page_bottom.php'; ?>
<?php mysqli_close($conn); ?>
<script src="assets/scripts/ajaxindex.js"></script>
