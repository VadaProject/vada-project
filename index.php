<?php require_once 'config/db_connect.php'; ?>
<?php require 'includes/page_top.php'; ?>
<main class="page-container text-center">
    <h2>Welcome!</h2>
    <p>This is a website for discussion and learning that is based on Indian traditions of epistemology, logic and debate. Users can create new claims using protocols of <i>vāda</i> and <i>pramāṇavāda</i>, or participate in and observe pre-existing debates.</p>
    <h3>Topics to select from:</h3>
    <?php
    $conn = db_connect();
    $root12 = 'SELECT DISTINCT topic
        from claimsdb
        '; // SQL with parameters
    $stmt52 = $conn->prepare($root12);
    $stmt52->execute();
    $rootresult12 = $stmt52->get_result(); // get the mysqli result
    $numhitsroot = mysqli_num_rows($rootresult12);
    while ($root2 = $rootresult12->fetch_assoc()) {
        ?>
        <a class="btn" href="topic.php?topic=<?php echo $root2['topic']; ?>"><?php echo $root2['topic']; ?></a>
    <?php } ?>
    <p>Want to start a new topic?</p>
    <a class="btn btn-primary" href="add.php">Add New Claim</a>
</main>
<?php include 'includes/page_bottom.php'; ?>
