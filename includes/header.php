<header class="topnav">
    <h1><img src="assets/svg/logo.svg" width="250" alt="Vāda Project"></h1>
    <div>
        <a <?php
        $CURRENT_PAGE = $CURRENT_PAGE ?? "";
        if ('Index' == $CURRENT_PAGE) {
            echo 'class="active"';
        } ?>
            href="index.php">Home & Topics</a>
        <a <?php
        if ('About' == $CURRENT_PAGE) {
            echo 'class="active"';
        } ?>
            href="about.php">About</a>
        <a <?php
        if ('User Guide' == $CURRENT_PAGE) {
            echo 'class="active"';
        } ?>
            href="userguide.php">User Guide</a>
        <div>
</header>
