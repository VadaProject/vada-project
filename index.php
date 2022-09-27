<?php include 'config/db_connect.php'; ?>
<?php include 'includes/page_top.php'; ?>
<main class="page-container text-center">
    <h2>Welcome!</h2>
    <p>This is a website for discussion and learning that is based on Indian traditions of epistemology, logic and debate. Users can create new claims using protocols of <i>vāda</i> and <i>pramāṇavāda</i>, or participate in and observe pre-existing debates.</p>
    <h3>Topics to select from:</h3>
    <?php
    $root12 = 'SELECT DISTINCT topic
        from claimsdb
        '; // SQL with parameters
    $stmt52 = $conn->prepare($root12);
    $stmt52->execute();
    $rootresult12 = $stmt52->get_result(); // get the mysqli result
    $numhitsroot = mysqli_num_rows($rootresult12);
    while ($root2 = $rootresult12->fetch_assoc()) {
        ?>
        <a href="ajaxindex.php?topic=<?php echo $root2['topic']; ?>"><button> <?php echo $root2['topic']; ?></button></a>
    <?php } ?>
    <p>Want to start a new topic?</p>
    <a class="brand-text" href="add.php"><button>Add New Claim</button></a>
</main>
<?php include 'includes/page_bottom.php'; ?>
