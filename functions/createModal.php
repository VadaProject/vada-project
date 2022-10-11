<?php

function createModal($claimid)
{
    require __DIR__ . '/../config/db_connect.php';

    // Check if user has requested to get detail
    if (isset($_POST['get_data'])) {
        // Get the ID of customer user has selected
        $id = $_POST['id'];

        require __DIR__ . '/../config/db_connect.php';

        // Getting specific customer's detail
        $sql = "SELECT * FROM claimsdb WHERE claimID='{$id}'";
        $result = mysqli_query($conn, $sql);
        $row = mysqli_fetch_object($result);

        // Important to echo the record in JSON format
        echo json_encode($row);

        // Important to stop further executing the script on AJAX by following line
        exit();
    }

    // Connecting with database and executing query
    include 'config/db_connect.php';
    $sql = "SELECT * FROM claimsdb WHERE claimID = '{$claimid}'";
    $result = mysqli_query($conn, $sql);
    ?>

            <!-- Creating table heading -->
            <div class="container">

                <!-- Display dynamic records from database -->
            <?php while ($row = mysqli_fetch_object($result)) { ?>
                <button class="btn btn-primary" onclick="loadData(this.getAttribute('data-id'));"
                    data-id="<?php echo $row->claimID; ?>">
                    Details
                </button>
                </div>
            <?php }
}
