<?php
switch (basename($_SERVER['SCRIPT_NAME'])) {
    case 'about.php':
        $CURRENT_PAGE = 'About';
        $PAGE_TITLE = 'About';
        break;
    case 'userguide.php':
        $CURRENT_PAGE = 'User Guide';
        $PAGE_TITLE = 'User Guide';
        break;
    case 'index.php':
        $CURRENT_PAGE = 'Index';
        $PAGE_TITLE = 'Home';
}
?>
<title><?php echo htmlspecialchars($PAGE_TITLE) ?? ""; ?> - Vāda Project</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="assets/stylesheets/global.css?timestamp=20230219">
<link rel="icon" href="assets/img/icon.svg"/>
<script src="assets/scripts/jquery-3.3.1.min.js" type="text/javascript"></script>
