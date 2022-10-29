<?php
require_once "functions/Database.php";
use Database\Database;

// TODO: rename this file. it handles AJAX responses.
// turns out it's named after a guy called adnan.

// Check if user has requested to get detail
if (isset($_POST['get_data'])) {
    // Get the ID of customer user has selected
    $claim = Database::getClaim($_POST['id']);
    // Important to echo the record in JSON format
    echo json_encode($claim);

    // Important to stop further executing the script on AJAX by following line
    exit();
}

?>
<script src="assets/scripts/adnanindex.js"></script>

<?php
// Connecting with database and executing query
$conn = db_connect();
$sql = 'SELECT * FROM claimsdb';
$result = mysqli_query($conn, $sql);
?>

<!-- Creating table heading -->
<div class="container">
    <table class="table">
        <tr>
            <th>ID</th>
            <th>Name</th>
        </tr>

        <!-- Display dynamic records from database -->
        <?php while ($row = mysqli_fetch_object($result)) { ?>
        <tr>
            <td><?php echo $row->claimID; ?></td>

            <!--Button to display details -->
            <td>
                <button class="btn btn-primary"
                    onclick="loadData(this.getAttribute('data-id'));"
                    data-id="<?php echo $row->claimID; ?>">
                    Details
                </button>
            </td>
        </tr>
        <?php } ?>
    </table>
</div>

<script>

</script>

<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog"
    aria-hidden="true">

    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <h4 class="modal-title">
                    Customer Detail
                </h4>

                <button type="button" class="close" data-dismiss="modal"
                    aria-hidden="true">
                    ×
                </button>
            </div>

            <div id="modal-body">
                Press ESC button to exit.
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-default"
                    data-dismiss="modal">
                    OK
                </button>
            </div>

        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->

</div><!-- /.modal -->
