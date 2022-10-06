<?php
// Check if user has requested to get detail
if (isset($_POST['get_data'])) {
    // Get the ID of customer user has selected
    $id = $_POST['id'];
    include 'config/db_connect.php';
    // Getting specific customer's detail
    $sql = "SELECT * FROM claimsdb WHERE claimID='{$id}'";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_object($result);
    // Important to echo the record in JSON format
    echo json_encode($row);
    // Important to stop further executing the script on AJAX by following line
    exit;
}
?>
<script src="assets/scripts/adnanindex.js"></script>

<?php
// Connecting with database and executing query
include 'config/db_connect.php';
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
