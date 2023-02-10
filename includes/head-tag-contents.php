<?php
switch ($_SERVER['SCRIPT_NAME']) {
    case '/directory/about.php':
        $CURRENT_PAGE = 'About';
        $PAGE_TITLE = 'About';
        break;

    case '/directory/userguide.php':
        $CURRENT_PAGE = 'User Guide';
        $PAGE_TITLE = 'User Guide';
        break;
    case '/directory/index.php':
        $CURRENT_PAGE = 'Index';
        $PAGE_TITLE = 'Home';
}
?>
<title><?php print $PAGE_TITLE; ?> - Vāda Project</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="assets/stylesheets/global.css">
<link rel="icon" href="assets/img/icon.svg"/>
<script src="assets/scripts/jquery-3.3.1.min.js" type="text/javascript"></script>
