<?php
require __DIR__ . "/../vendor/autoload.php";
// handle database insertion, then render page.

require "insert.php";


use Vada\Model\ClaimRepository;
use Vada\Model\TopicRepository;
use Vada\Model\Database;
use Vada\View\SupportingForm;

$db = Database::connect();
$claimRepository = new ClaimRepository($db);
$topicRepository = new TopicRepository($db);

?>
<!DOCTYPE html>
<html>
<head>
    <?php require __DIR__ . '/../includes/head-tag-contents.php'; ?>
    <style>
        body {
            background-color: white;
            padding: 0;
            padding-bottom: 1rem;
        }
    </style>
</head>
<body lang="en-US">
<?php
if (empty($_GET["cid"])) {
    echo "<h2>Error: no claim ID given.</h2>";
    return;
}
$claim_id = $_GET["cid"];
$claim = $claimRepository->getClaimByID($claim_id);
if (empty($claim)) {
    echo "<h2>Error: a claim with the ID #$claim_id does not exist.</h2>";
    return;
}
if ($claim->COS == "support") {
    echo "<h2>Error: claim $claim_id is a support and cannot be supported.</h2>";
    return;
}
$supportingForm = new SupportingForm();
?>
<h2>Supporting claim #
    <?php echo $claim->display_id; ?>
</h2>
<form method="POST" id="myForm" target="_parent">
    <?php
    $supportingForm->showError($error ?? null); // does nothing if null
    ?>
    <input type="hidden" name="flaggingOrSupporting" value="supporting">
    <input type="hidden" name="claimIDFlagged"
        value="<?php echo $claim_id; ?>">
    <?php $supportingForm->topicInput($claim->topic_id); ?>
    <?php $supportingForm->subjectTargetInput($claim->subject, $claim->targetP); ?>
    <?php $supportingForm->supportMeansInput(); ?>
    <div>
        <button type="submit" id="submit">Submit</button>
    </div>
    <script src="assets/scripts/add.js?timestamp=20230219"></script>
</form>
</body>

</html>
