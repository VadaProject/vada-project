<?php require_once 'config/db_connect.php'; ?>
<?php require 'includes/page_top.php'; ?>
<?php
require 'functions/sortClaims.php';
require 'functions/doesThesisFlag.php';
require 'functions/noSupports.php';
require 'functions/restoreActivity.php';
require 'functions/haveRival.php';


$conn = db_connect();
if (isset($_GET['topic'])) {
    $topic = mysqli_real_escape_string($conn, $_GET['topic']);
} // end isset check?>

<div class="wrapper">
    <ul>
        <li class="noline">
            <BR><BR>

            <span>
                <div class="notification">
                    <img alt="Contested claim icon"
                        src="assets/img/alert.png">
                    <p>A contested claim or support will have this symbol.<br>Rival
                        claims will be yellow.</p>
                </div>
                <p><a href="add.php?topic=<?php echo $topic ?>">Add New Claim To
                        This Topic</a></p>
            </span>
            <h3>TOPIC: <?php echo $topic; ?> <BR> </h3>


            </center>
            <center>

                <?php


        // changing the centers above is a fun change
        // this code finds ALL claims that are not flaggers (all root claims)
                $root12 = 'SELECT DISTINCT claimID
      from claimsdb, flagsdb
      WHERE claimID NOT IN (SELECT DISTINCT claimIDFlagger FROM flagsdb) AND topic = ?
        '; // SQL with parameters
                $stmt52 = $conn->prepare($root12);
                $stmt52->bind_param('s', $topic);
                $stmt52->execute();
                $rootresult12 = $stmt52->get_result(); // get the mysqli result
                $numhitsroot = mysqli_num_rows($rootresult12);
                while ($root2 = $rootresult12->fetch_assoc()) {
                    sortclaims($root2['claimID']);
                }

                $root1 = 'SELECT DISTINCT claimID
from claimsdb, flagsdb
WHERE claimID NOT IN (SELECT DISTINCT claimIDFlagger FROM flagsdb) AND topic = ?
        '; // SQL with parameters
                $stmt5 = $conn->prepare($root1);
                $stmt5->bind_param('s', $topic);
                $stmt5->execute();
                $rootresult1 = $stmt5->get_result(); // get the mysqli result
                $numhitsroot = mysqli_num_rows($rootresult1);

                while ($root = $rootresult1->fetch_assoc()) {
                    restoreActivity($root['claimID']);
                }

                $root2 = 'SELECT DISTINCT claimIDFlagger
from flagsdb
WHERE isRootRival = 1
        '; // SQL with parameters
                $stmt12 = $conn->prepare($root2);
// $stmt12->bind_param("s", $topic);
                $stmt12->execute();
                $rootresult2 = $stmt12->get_result(); // get the mysqli result
                $numhitsroot28 = mysqli_num_rows($rootresult2);

                while ($root2 = $rootresult2->fetch_assoc()) {
                    if ($numhitsroot28 > 0) {
                        $r = 'SELECT DISTINCT claimID, topic
    from claimsdb
    WHERE claimID = ?
        '; // SQL with parameters
                        $s = $conn->prepare($r);
                        $s->bind_param('i', $root2['claimIDFlagger']);
                        $s->execute();
                        $rres = $s->get_result(); // get the mysqli result

                        while ($results = $rres->fetch_assoc()) {
                            if ($results['topic'] == $topic) {
                                restoreActivityRIVAL($results['claimID']);
                            } // end of if topic = topic
                        } // end of while
                    } // end of if numhits
                } // end of while

// leafy tests below

                $root2 = "SELECT DISTINCT claimIDFlagger
from flagsdb
WHERE flagType LIKE 'Thesis Rival'
        "; // SQL with parameters
                $stmt12 = $conn->prepare($root2);
// $stmt12->bind_param("s", $topic);
                $stmt12->execute();
                $rootresult2 = $stmt12->get_result(); // get the mysqli result
                $numhitsroot28 = mysqli_num_rows($rootresult2);

                while ($root2 = $rootresult2->fetch_assoc()) {
                    if ($numhitsroot28 > 0) {
                        $r = 'SELECT DISTINCT claimID, topic
    from claimsdb
    WHERE claimID = ?
        '; // SQL with parameters
                        $s = $conn->prepare($r);
                        $s->bind_param('i', $root2['claimIDFlagger']);
                        $s->execute();
                        $rres = $s->get_result(); // get the mysqli result

                        while ($results = $rres->fetch_assoc()) {
                            if ($results['topic'] == $topic) {
                                restoreActivityRIVAL($results['claimID']);
                            } // end of if topic = topic
                        } // end of while
                    } // end of if numhits
                } // end of while

// leafy tests above

                $root22 = 'SELECT DISTINCT claimIDFlagger
  from flagsdb
  WHERE isRootRival = 1
        '; // SQL with parameters
                $stmt122 = $conn->prepare($root22);
// $stmt122->bind_param("s", $topic);
                $stmt122->execute();
                $rootresult22 = $stmt122->get_result(); // get the mysqli result
                $numhitsroot29 = mysqli_num_rows($rootresult22);

                while ($root22 = $rootresult22->fetch_assoc()) {
                    if ($numhitsroot29 > 0) {
                        $r2 = 'SELECT DISTINCT claimID, topic
    from claimsdb
    WHERE claimID = ?
        '; // SQL with parameters
                        $s2 = $conn->prepare($r2);
                        $s2->bind_param('i', $root22['claimIDFlagger']);
                        $s2->execute();
                        $rres2 = $s2->get_result(); // get the mysqli result

                        while ($results2 = $rres2->fetch_assoc()) {
                            if ($results2['topic'] == $topic) {
                                sortclaimsRIVAL($results2['claimID']);
                            } // end of if topic = topic
                        } // end of while
                    } // end of if numhits
                } // end of while
                ?>
            <!-- Modal -->
            <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-hidden="true">

                <div class="modal-dialog">
                    <div class="modal-content-a">
                        <b>Claim Details</b> <br>
                        <div class="modal-content-b">

                            <div class="modal-header">
                                <h4 class="modal-title">

                                </h4>

                                <!--            <button type = "button" class = "close" data-dismiss = "modal" aria-hidden = "true">
               ×
             </button> -->
                            </div>

                            <div id="modal-body">

                                Press ESC button to exit.

                                response.claimID
                            </div>
                        </div> <!-- modal-content-b -->

                        <!--         <BR> <a href="details.php?id=<?php echo $claimid; ?>" class = "button">FLAG THIS CLAIM! </a> </div>  -->

                        <!-- <button type = "button" class = "btn btn-default" data-dismiss = "modal">
               OK
             </button> -->

                    </div><!-- /.modal-content-a-->
                </div><!-- /.modal-dialog -->

            </div><!-- /.modal -->
</div>
<?php include 'includes/page_bottom.php'; ?>
<?php mysqli_close($conn); ?>
<script src="assets/scripts/ajaxindex.js"></script>
