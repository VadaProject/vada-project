<?php include 'includes/page_top.php'; ?>
<link href="assets/stylesheets/add.css" rel="stylesheet" />
<main class="page-container">
    <form method="POST" id="myForm" action="insert.php">
        <?php include 'includes/supporting_form.php'; ?>
        <div>
            <button type="submit" id="submit">Submit</button>
        </div>
    </form>
</main>

<?php include 'includes/page_bottom.php'; ?>
