<?php  ?>
<?php include 'includes/page_top.php'; ?>
<main class="page-container">
<?php

/*
This displays the argument in full detail and pushes any user interaction/submissions to add.php.
*/
require_once 'config/db_connect.php';
require_once 'functions/flagging.php';

$conn = db_connect();

if (isset($_GET['id'])) {
    // escape sql chars
    $claimID = mysqli_real_escape_string($conn, $_GET['id']); // make sql
    $act = 'SELECT * FROM claimsdb WHERE claimID = ?'; // SQL with parameters
    $s = $conn->prepare($act);
    $s->bind_param('i', $claimID);
    $s->execute();
    $activity = $s->get_result(); // get the mysqli result
} // end of get request
while ($details = $activity->fetch_assoc()) {
    $claimIDFlaggedINSERT = $details['claimID'];
    $topic = $details['topic'];
    ?>
    <p><b>Claim ID:</b> <?php echo $details['claimID']; ?></p>
    <?php
    if ('claim' == $details['COS']) { ?>
        <p><b>Claim:</b> <?php echo $details['subject'] .
            ' ' .
            $details['targetP']; ?></p>
    <?php }
    if ('support' == $details['COS']) {
        // ----------------------------------------------------------------------------------- INFERENCE
        if ('Inference' == $details['supportMeans']) {
            $FOS = 'flagging';
            $oldclaim = 'SELECT claimIDFlagged
FROM claimsdb, flagsdb
WHERE claimIDFlagger = ?';
            // SQL with parameters
            $oc = $conn->prepare($oldclaim);
            $oc->bind_param('i', $claimID);
            $oc->execute();
            $results = $oc->get_result();
            // get the mysqli result
            while ($data = $results->fetch_assoc()) {
                $claimIDFlagged = $data['claimIDFlagged'];
            }
            ?>
            <table>
                <tr>
                    <td>
                        <style>
                            table,
                            th,
                            td {
                                border: 1px solid black;
                            }
                        </style>
                    <?php
                    echo '<p><b>THESIS FROM CLAIM #' . $claimIDFlagged . ': </b></p>';
                    $oldclaim2 = 'SELECT subject, targetP
FROM claimsdb
WHERE claimID = ?';
            // SQL with parameters
                    $oc2 = $conn->prepare($oldclaim2);
                    $oc2->bind_param('i', $claimIDFlagged);
                    $oc2->execute();
                    $results2 = $oc2->get_result();
            // get the mysqli result
                    while ($data = $results2->fetch_assoc()) {
                        $subject = $data['subject'];
                        $dTargetP = $data['targetP'];
                        ?>
                            <p><b>Thesis Statement:</b> <span style="color:red;"><?php echo $data[
                                'subject'
                            ]; ?><sup>(subject)</sup></span>
                            <span style="color:blue;"><?php echo $dTargetP; ?><sup style="color:blue;">(target property)</sup></span></p>
                    </td>
                </tr>
            </table>
                                                        <?php
                    }
                    ?>
        <p><b>Support Means:</b> <?php echo $details['supportMeans']; ?></p>
        <p><b>Reason Statement:</b>
        <span style="color:red;"> <?php echo $details['subject']; ?><sup>(subject)</sup></span>
        <span style="color:darkorange;"><?php echo $details['reason']; ?><sup>(reason property)</sup></span>
                </p>
        <p>
        <b>Rule & Example Statement:</b> Whomever/Whatever <span style="color:darkorange;"> <?php echo $details[
            'reason'
        ]; ?></span> <span style="color:blue;"><?php echo $dTargetP; ?></span>, as in the case of <span style="color:purple;"><?php echo $details[
        'example'
    ]; ?><sup>(example)</sup></span>
        </p>
        <!-- Trigger/Open The Modal -->
        <button class="openmodal myBtn">Flag Inference</button>
        <!-- The Modal -->
        <div class="modal myModal">
            <!-- Modal content -->
            <div class="modal-content">
                <span class="close">&times;</span>
                <form method="POST" id="myForm" action="insert.php">
                    <input name="FOS" value="<?php echo htmlspecialchars(
                        $FOS
                    ); ?>"></input>
                    <?php $_POST['FOS'] = 'flagging'; ?>
                    <html>
                    <p style="color:#000000" ;>
                        <!--
<br>Are you flagging the Reason property or the Rule and Example property?<br>
  <select name="tre" id="tre" value="tre">
        <option value="" selected>Select...</option>
        <option value="reason">Reason</option>
        <option value="rule">Rule</option></select>
-->
                        <br>How are you flagging this inference? <br>
                        <select name="flagType" id="flagType" value="flagType">
                            <option value="" selected>Select...</option>
                            <option value="Unestablished Subject">Unestablished Subject</option>
                            <option value="Itself Unestablished">Itself Unestablished</option>
                            <option value="Hostile">Hostile</option>
                            <option value="Too Narrow">Too Narrow</option>
                            <option value="Too Broad (Counterexample)">Too Broad (Counterexample)</option>
                            <option value="Too Broad (Unestablished Universal)">Too Broad (Unestabilshed Universal)</option>
                            <option value="Contrived Universal">Contrived Universal</option>
                        </select>
                        <br>
                        <?php $claimIDFlaggedINSERT = $details['claimID']; ?>
                        <input name="claimIDFlaggedINSERT" value="<?php echo htmlspecialchars(
                            $claimIDFlaggedINSERT
                                                                  ); ?>"></input> <?php $_POST[
    'claimIDFlaggedINSERT'
] = $claimIDFlaggedINSERT;
            // echo '<script type="text/javascript">alert("a LERT: ' . $claimIDFlaggedINSERT . '");</script>';
?>
                        <!-- //------------------------- -->
                        <script type="text/javascript">
                            /*
var union = document.getElementById('tre');
union.onchange = checkOtherUnion;
union.onchange();
function checkOtherUnion() {
    var union = this;
    var reason = document.getElementById('flagTypeR');
    var example = document.getElementById('flagTypeE');
if (union.options[union.selectedIndex].value === 'reason') {
        reason.style.display = '';
    } else {
        reason.style.display = 'none';
    }
if (union.options[union.selectedIndex].value === 'rule') {
        example.style.display = '';
    } else {
        example.style.display = 'none';
    }
} */
                        </script>
                        <?php flagging($claimIDFlaggedINSERT); ?>
                        <!-- //------------------------- -->
                    <div class="center">
                        <button onclick="setTimeout(myFunction, 5000)" id="submit">Submit</button>
                    </div>
                    </p>
                </form>
            </div>
        </div>
        <!-------------------------------------------------------------------------------------------------------------------------TARKA-->
            <?php
        }
        // end inference check
        if ('Tarka' == $details['supportMeans']) { ?>
        <p><b>Support Means:</b> <?php echo $details['supportMeans']; ?></p>
        <BR><br><?php echo 'Tarka is an element of conversation used to discuss errors in debate form and communication with moderators.<br><br>'; ?>
        <b>Claim:</b><br> <?php
        echo $details['subject'];
            echo ' ' . $details['targetP'];
            echo '<br><br><br>Please explain argument in the comments section below.';
        } // extra comment // ------------------------------------------------------------------------------------------------------------------------------- PERCEPTION
        if ('Perception' == $details['supportMeans']) { ?>
        <p><b>Support Means:</b> <?php echo $details['supportMeans']; ?></p>
        <p><b>Url of perception:</b> <?php echo $details['URL']; ?></p>
        <button class="openmodal myBtn">Flag Perception</button>
        <!-- The Modal -->
        <div class="modal myModal">
            <!-- Modal content -->
            <div class="modal-content">
                <span class="close">&times;</span>
                <form method="POST" id="myForm" action="insert.php">
                <?php $FOS =
                    'flagging'; ?> <input name="FOS" value="<?php echo htmlspecialchars(
                        $FOS
                    ); ?>"></input> <?php $_POST['FOS'] = 'flagging'; ?>
                    <html>
                    <p style="color:#000000" ;>
                        <br>What are you flagging it for?<br>
                        <?php
                                           $claimIDFlaggedINSERT = $details['claimID'];
                        $_POST['claimIDFlaggedINSERT'] = $claimIDFlaggedINSERT;
                        ?> <input name="claimIDFlaggedINSERT" value="<?php echo htmlspecialchars(
                            $claimIDFlaggedINSERT
                        ); ?>"></input> <?php  ?>
                        <br><u>Perception Flags</u><br>
                        <select name="flagType" id="flagType" value="flagType">
                            <option value="" selected>Select...</option>
                            <option value="No Sense Object Contact">No Sense-Object Contact</option>
                            <option value="Depends On Words">Depends on Words</option>
                            <option value="Errant">Errant</option>
                            <option value="Ambiguous">Ambiguous</option>
                        </select><br>
                    <?php flagging($claimIDFlaggedINSERT); ?>
                    <div class="center">
                        <button onclick="setTimeout(myFunction, 5000)" id="submit">Submit</button>
                    </div>
                    </p>
                </form>
            </div>
        </div>
        <!-------------------------------------------------------------------------------------------------------------------------TESTIMONY-->
        <?php }
        // end perception check
        // ------------- THREE
        if ('Testimony' == $details['supportMeans']) { ?>
        <p><b>Support Means:</b> <?php echo $details['supportMeans']; ?></p>
        <br><br>
        <p><b>Transcription:</b> <?php echo $details['transcription']; ?>
            <br><br>
        <p><b>Citation:</b> <?php echo $details['citation']; ?></p>
        <button class="openmodal myBtn">Flag Testimony</button>
        <!-- The Modal -->
        <div class="modal myModal">
            <!-- Modal content -->
            <div class="modal-content">
                <span class="close">&times;</span>
                <form method="POST" id="myForm" action="insert.php">
                <?php $FOS =
                    'flagging'; ?> <input name="FOS" value="<?php echo htmlspecialchars(
                        $FOS
                    ); ?>"></input> <?php
$_POST['FOS'] = 'flagging';
            $claimIDFlaggedINSERT = $details['claimID'];
?> <input name="claimIDFlaggedINSERT" value="<?php echo htmlspecialchars(
    $claimIDFlaggedINSERT
); ?>"></input> <?php $_POST['claimIDFlaggedINSERT'] = $claimIDFlaggedINSERT;
            // / echo '<script type="text/javascript">alert("a LERT: ' . $claimIDFlaggedINSERT . '");</script>';
?>
                    <html>
                    <p style="color:#000000" ;>
                        <br>What are you flagging it for?<br>
                        <br><u>Testimony Flags</u><br>
                        <select name="flagType" id="flagType" value="flagType">
                            <option value="" selected>Select...</option>
                            <option value="No Direct Familiarity">No direct familiarity</option>
                            <option value="Errant Info">Errant information</option>
                            <option value="Ambiguous">Ambiguous</option>
                            <option value="Faithless">Faithless</option>
                            <option value="Misstatement">Misstatement</option>
                        </select><br>
                    <?php flagging($claimIDFlaggedINSERT); ?>
                    <div class="center">
                        <button onclick="setTimeout(myFunction, 5000)" id="submit">Submit</button>
                    <?php
            /* // if submit, then
    if(empty($supportMeans))
{
  header("Location: ../directory/details.php?id=" . $claimIDFlagged ."?sf=empty");
  exit();
} else {
  header("Location: ../directory/details.php?id=" . $claimIDFlagged ."?sf=success");
} */
                    ?>
                    </div>
                    </p>
                </form>
            </div>
        </div>
        <?php }
        // end testimony check
    }
    // end check for flagtype supporting
    else {
        ?>
    <br> <button class="openmodal myBtn">Support or Flag Claim</button>
    <!-- The Modal -->
    <div class="modal myModal">
        <!-- Modal content -->
        <div class="modal-content">
            <span class="close">&times;</span>
            <form method="POST" id="myForm" action="insert.php">
                <html>
                <p style="color:#000000" ;>
        <?php $_POST[
           'claimIDFlaggedINSERT'
        ] = $claimIDFlaggedINSERT; ?> <input name="claimIDFlaggedINSERT" value="<?php echo htmlspecialchars(
            $claimIDFlaggedINSERT
        ); ?>"></input> <?php
               // echo '<script type="text/javascript">alert("a LERT: ' . $claimIDFlaggedINSERT . '");</script>';
?>
                    <br>Are you flagging or supporting this claim?<br>
                    <select name="FOS" id="FOS" value="FOS">
                        <option value="" selected>Select...</option>
                        <option value="flagging">flagging</option>
                        <option value="supporting">supporting</option>
                    </select>
                <div id="flaggingDiv">
                    <br>Thesis Flags<br>
                    <select name="flagTypeT" id="flagTypeT" value="flagType">
                        <option value="" selected>Select...</option>
                        <option value="Thesis Rival">Has Rival</option>
                        <option value="Too Early">Too Early</option>
                        <option value="Too Late">Too Late</option>
                    </select>
                    <br>
                </div>
                <?php flagging($claimIDFlaggedINSERT); ?>
                <div class="center">
                    <button onclick="setTimeout(myFunction, 5000)" id="submit">Submit</button>
                    <script>
                        var union1 = document.getElementById('FOS');
                        union1.onchange = checkOtherUnion1;
                        union1.onchange();

                        function checkOtherUnion1() {
                            var flaggingDiv = document.getElementById('flaggingDiv');
                            var supportingDropCheck;
                            flaggingDiv.style.display = 'none';
                            var hideThesis = document.getElementById('hideThesis');
                            hideThesis.style.display = 'hideThesis';
                            var union1 = this;
                            if (union1.options[union1.selectedIndex].value === 'flagging') {
                                //  window.alert("the flagging option is chosen");
                                flaggingDiv.style.display = '';
                                hideThesis.style.display = '';
                            } else {
                                flaggingDiv.style.display = 'none';
                                hideThesis.style.display = 'none';
                            }
                            if (union1.options[union1.selectedIndex].value === 'supporting') {
                                //window.alert("Please input your support for the claim below");
                                hideThesis.style.display = 'none';
                                flaggingDiv.style.display = 'none';
                            }
                        }
                    </script>
                    <?php
        /* // if submit, then
    if(empty($supportMeans))
{
  header("Location: ../directory/details.php?id=" . $claimIDFlagged ."?sf=empty");
  exit();
} else {
  header("Location: ../directory/details.php?id=" . $claimIDFlagged ."?sf=success");
} */
                    ?>
                </div>
                </p>
            </form>
        </div>
    </div>
        <?php
    } // end of else statement
}

// end while loop
?>
<script>
    // BELOW IS WHERE SUBMIT BUTTON DISABLED HAPPENS
    /*jQuery("#submit").prop('disabled', true);
        var card = document.getElementById("union");
    //support means = union
    //var toValidate = jQuery('#subject');
    //var toValidateP = jQuery('#targetP');
    //below is all that is needed to restore the textarea checking. it just doesnt work with supporting, only with flagging!
     var toValidate = jQuery('#subject, #targetP');
        validTextArea = false;
    toValidate.keyup(function () {
        if (jQuery(this).val().length > 0) {
            jQuery(this).data('valid', true);
        } else {
            jQuery(this).data('valid', false);
        }
            toValidate.each(function () {
            if (jQuery(this).data('valid') == true) {
                validTextArea = true;
            } else {
                validTextArea = false;
            }
    if (validTextArea == true && validDropDown == true) {
            jQuery("#submit").prop('disabled', false);
        } else {
            jQuery("#submit").prop('disabled', true);
          }
        });
    });
    //var clientCode = document.querySelector("#clientCode");
    //clientCode.addEventListener("change", clientChangeHandler. false);
    var toValidate2 = jQuery('#union, #grammar');
        validDropDown = false;
    toValidate2.change(function () {
      if (jQuery(this)[0].selectedIndex == 1 || jQuery(this)[0].selectedIndex == 2 || jQuery(this)[0].selectedIndex == 3 || jQuery(this)[0].selectedIndex == 4|| jQuery(this)[0].selectedIndex == 5) {
            jQuery(this).data('valid', true);
        } else {
            jQuery(this).data('valid', false);
        }
            toValidate2.each(function () {
            if (jQuery(this).data('valid') == true) {
                validDropDown = true;
            } else {
                validDropDown = false;
       //           window.alert(jQuery(this)[0].selectedIndex);
            }
    if (validTextArea == true && validDropDown == true) {
            jQuery("#submit").prop('disabled', false);
        } else {
            jQuery("#submit").prop('disabled', true);
          }
        });
    if (validTextArea == true && validDropDown == true) {
            jQuery("#submit").prop('disabled', false);
        } else {
            jQuery("#submit").prop('disabled', true);
          }
    });
    */
    // above IS WHERE SUBMIT BUTTON DISABLED HAPPENS
    $(document).ready(function() {
        $("#submit").click(function() {
            window.alert("Submitted!");
            window.location.assign("topic.php?topic=<?php echo $topic; ?>");
            $.post($("#myForm").attr("action"),
                $("#myForm :input").serializeArray(),
                function(info) {
                    $("#result").html(info);
                });
            clearInput();
        });
        $("#myForm").submit(function() {
            return false;
        });

        function clearInput() {
            $("#myForm :input").each(function() {
                $(this).val('');
            });
        }
    });
</script>
</div>
</div>
<script>
    var modals = document.getElementsByClassName('modal');
    // Get the button that opens the modal
    var btns = document.getElementsByClassName("openmodal");
    var spans = document.getElementsByClassName("close");
    for (let i = 0; i < btns.length; i++) {
        btns[i].onclick = function() {
            modals[i].style.display = "block";
        }
    }
    for (let i = 0; i < spans.length; i++) {
        spans[i].onclick = function() {
            modals[i].style.display = "none";
        }
    }
</script>

<script type="text/javascript">
    var union = document.getElementById('union');
    union.onchange = checkOtherUnion;
    union.onchange();

    function checkOtherUnion() {
        var union = this;
        var reason = document.getElementById('reason');
        var example = document.getElementById('example');
        var url = document.getElementById('url');
        var rd = document.getElementById('rd');
        var hiddenRule = document.getElementById('hiddenRule');
        var hiddenURL = document.getElementById('hiddenURL');
        var hiddenTS = document.getElementById('hiddenTS');
        var hiddenCitation = document.getElementById('hiddenCitation');
        var hiddenTranscription = document.getElementById('hiddenTranscription');
        var perceptionHint = document.getElementById('perceptionHint');
        reason.style.display = 'none';
        example.style.display = 'none';
        citation.style.display = 'none';
        author.style.display = 'none';
        title.style.display = 'none';
        publication.style.display = 'none';
        date.style.display = 'none';
        citationURL.style.display = 'none';
        url.style.display = 'none';
        vidtimestamp.style.display = 'none';
        transcription.style.display = 'none';
        hiddenRule.style.display = 'none';
        hiddenURL.style.display = 'none';
        hiddenTS.style.display = 'none';
        hiddenCitation.style.display = 'none';
        hiddenTranscription.style.display = 'none';
        perceptionHint.style.display = 'none';
        if (union.options[union.selectedIndex].value === '') {
            citation.style.display = 'none';
            author.style.display = 'none';
            title.style.display = 'none';
            publication.style.display = 'none';
            date.style.display = 'none';
            citationURL.style.display = 'none';
        }
        if (union.options[union.selectedIndex].value === 'Inference') {
            reason.style.display = '';
            example.style.display = '';
            hiddenRule.style.display = '';
            citation.style.display = 'none';
            author.style.display = 'none';
            title.style.display = 'none';
            publication.style.display = 'none';
            date.style.display = 'none';
            citationURL.style.display = 'none';
        }
        if (union.options[union.selectedIndex].value === 'Perception') {
            perceptionHint.style.display = '';
            url.style.display = '';
            vidtimestamp.style.display = '';
            citation.style.display = '';
            author.style.display = '';
            title.style.display = '';
            publication.style.display = '';
            date.style.display = '';
            citationURL.style.display = 'none';
            hiddenURL.style.display = '';
            hiddenTS.style.display = '';
            hiddenCitation.style.display = '';
        }
        if (union.options[union.selectedIndex].value === 'Testimony') {
            transcription.style.display = '';
            citation.style.display = '';
            author.style.display = '';
            title.style.display = '';
            publication.style.display = '';
            date.style.display = '';
            citationURL.style.display = '';
            hiddenCitation.style.display = '';
            hiddenTranscription.style.display = '';
        }
        if (union.options[union.selectedIndex].value === 'Tarka') {
            window.alert("A requirement of Tarka is to use the comments feature in the Tarka claim following submission.");
            citation.style.display = 'none';
            author.style.display = 'none';
            title.style.display = 'none';
            publication.style.display = 'none';
            date.style.display = 'none';
        }
    }
</script>
<!--
     <div id="hyvor-talk-view"></div>
<script type="text/javascript">
    var HYVOR_TALK_WEBSITE = 3313; // DO NOT CHANGE THIS
    var HYVOR_TALK_CONFIG = {
        url: false,
        id: false
    };
</script>
<script async type="text/javascript" src="//talk.hyvor.com/web-api/embed"></script>
-->
<div class="x">
    <div id="disqus_thread"></div>
    <script>
        /**
         *  RECOMMENDED CONFIGURATION VARIABLES: EDIT AND UNCOMMENT THE SECTION BELOW TO INSERT DYNAMIC VALUES FROM YOUR PLATFORM OR CMS.
         *  LEARN WHY DEFINING THESE VARIABLES IS IMPORTANT: https://disqus.com/admin/universalcode/#configuration-variables    */
        var disqus_config = function() {
            this.page.url = document.write(window.location.href); // Replace PAGE_URL with your page's canonical URL variable
            this.page.identifier = document.write(window.location.href); // Replace PAGE_IDENTIFIER with your page's unique identifier variable
        };
        (function() { // DON'T EDIT BELOW THIS LINE
            var d = document,
                s = d.createElement('script');
            s.src = 'https://vadaproject.disqus.com/embed.js';
            s.setAttribute('data-timestamp', +new Date());
            (d.head || d.body).appendChild(s);
        })();
    </script>
    <noscript>Please enable JavaScript to view the <a href="https://disqus.com/?ref_noscript">comments powered by Disqus.</a></noscript>
</div>
</main>
<?php include 'includes/page_bottom.php'; ?>
