<?php
require_once 'functions/supportingForm.php';
require_once 'functions/Database.php';
use Database\Database;
?>
<!DOCTYPE html>
<html>
<head>
    <?php require 'includes/head-tag-contents.php'; ?>
    <style>
        body {
            background-color: white;
            padding: 0;
            padding-bottom: 1rem;
        }
    </style>
    <link href="assets/stylesheets/add.css" rel="stylesheet" />
</head>
<body lang="en-US">
<?php
    $claim_id = $_GET["id"];
    if (!isset($claim_id)) {
        echo "<h2>Error: no claim ID given.</h2>";
        return;
    }
    $claim = Database::getClaim($claim_id);
    if (is_null($claim)) {
        echo "<h2>Error: a claim with the ID #$claim_id does not exist.</h2>";
        return;
    }
    if ($claim->COS == "support") {
        echo "<h2>Error: claim #$claim_id is a support and cannot be supported.</h2>";
        return;
    }
?>
<h2>Supporting claim #<?php echo $claim_id; ?></h2>
<form method="POST" id="myForm" action="insert.php">

    <input type="hidden" name="FOS" value="supporting">
    <input type="hidden" name="claimIDFlaggedINSERT" value="<?php echo $claim_id; ?>">
    <?php topicInput($claim->topic, /* hidden */ true); ?>
    <?php subjectTargetInput($claim->subject, $claim->targetP); ?>
    <?php supportMeans(); ?>
    <div>
        <button type="submit" id="submit">Submit</button>
    </div>
    <script src="assets/scripts/add.js"></script>
</form>
</body>
</html>
