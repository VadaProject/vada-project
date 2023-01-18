
<?php include 'includes/page_top.php'; ?>
<main class="page-container">
<?php
/*
This displays the argument in full detail and pushes any user interaction/submissions to add.php.
*/
require_once 'config/db_connect.php';
require_once 'functions/flagging.php';
require_once 'functions/Database.php';
use Database\Database;
$conn = db_connect();

$claim_id = $_GET['id']; // get claim id from URL search tags
$claim = Database::getClaim($claim_id);

if (is_null($claim)) {
    echo "<h2>Error: a claim with the ID #$claim_id does not exist.</h2>";
    return;
}

$claim_id = $claim->claimID;
$topic = $claim->topic;
?>
<h2><?php echo "Claim #$claim_id" ?></h2>
<p><label>Support Means:</label> <?php echo $claim->supportMeans; ?></p>
<?php
if ($claim->COS == 'claim') { ?>
    <span class="subject-display"><?php echo $claim->subject ?></span>
    <span class="target-display"><?php echo $claim->targetP ?></span>.
    
    <?php }
    if ($claim->COS == 'support') { ?>
        <?php
        // INFERENCE
        if ('Inference' == $claim->supportMeans) {
            $FOS = 'flagging';
            $claimIDFlagged = Database::getFlaggedClaim($claim_id);
            $flaggedClaim = Database::getClaim($claimIDFlagged);
            
            if (isset($flaggedClaim)) { ?>
                
                <!-- <div class="card d-inline-block">
                <label><?php echo "Thesis Statement of Claim #$claimIDFlagged:"?></label>
                <p>
                <span class="subject-display"><?php echo $flaggedClaim->subject; ?></span>
                <span class="target-display" ><?php echo $flaggedClaim->targetP; ?></span>
                </p>
                </div> -->
                <?php }
                ?>
                <table>
                <tr>
                <th>Thesis Statement (<?php echo "<a href='?id=$claimIDFlagged'>#$claimIDFlagged</a>"?>)</th>
                <td>
                <span class="subject-display"> <?php echo $flaggedClaim->subject; ?></span>
                <span class="target-display"><?php echo $flaggedClaim->targetP; ?></span>.
                </td>
                <td><?php echo "<a class='btn' href='?id=$claimIDFlagged'>Flag Thesis</a>"?></td>
                </tr>
                <tr>
                <th>Reason Statement</th>
                <td>
                <span class="subject-display"> <?php echo $claim->subject; ?></span>
                <span class="reason-display"><?php echo $claim->reason; ?></span>.
                </td>
                <td rowspan="2">
                <button class="openmodal myBtn">Flag Inference</button>
                </td>
                </tr>
                <tr>
                <th>Rule & Example Statement</th>
                <td>
                Whomever/Whatever
                <span class="reason-display"><?php echo $claim->reason; ?></span>
                <span class="target-display"><?php echo $flaggedClaim->targetP; ?></span>,
                as in the case of
                <span class="example-display"><?php echo $claim->example; ?></span>.
                </td>
                </tr>
                </table>
                
                <p></p>
                </p>
                <!-- Trigger/Open The Modal -->
                
                <!-- The Modal -->
                <div class="modal myModal">
                <!-- Modal content -->
                <div class="modal-content">
                <span class="close">&times;</span>
                <form method="POST" id="myForm" action="insert.php">
                <input name="FOS" value="<?php echo htmlspecialchars($FOS); ?>">
                <?php $_POST['FOS'] = 'flagging'; ?>
                
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
                <?php $claim_id = $claim->claimID; ?>
                <input name="claimID" value="<?php echo htmlspecialchars(
                    $claim_id
                ); ?>">
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
                <?php flagging($claim_id); ?>
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
            if ('Tarka' == $claim->supportMeans) { ?>
                <p>ⓘ <i>Tarka</i> (<b>philosophical argument</b>) allows for supplementary free-form discussion.</p>
                <p>Please explain argument in the Disqus comments section below.</p>
                <?php
            }
            // PERCEPTION
            if ('Perception' == $claim->supportMeans) { ?>
                <p><label>Citation:</label>
                <?php echo $claim->citation; ?></p>
                <p><label>URL:</label>
                <?php echo "<a target='_blank' href='$claim->URL'?>$claim->URL</a>"?></p>
                <p><label>Timestamp:</label> <?php echo $claim->vidtimestamp; ?></p>
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
                ); ?>">
                
                <p style="color:#000000" ;>
                <br>What are you flagging it for?<br>
                <?php
                $claim_id = $claim->claimID;
                ?> <input name="claimID" value="<?php echo htmlspecialchars(
                    $claim_id
                ); ?>"> <?php  ?>
                <br><u>Perception Flags</u><br>
                <select name="flagType" id="flagType" value="flagType">
                <option value="" selected>Select...</option>
                <option value="No Sense Object Contact">No Sense-Object Contact</option>
                <option value="Depends On Words">Depends on Words</option>
                <option value="Errant">Errant</option>
                <option value="Ambiguous">Ambiguous</option>
                </select><br>
                <?php flagging($claim_id); ?>
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
                if ('Testimony' == $claim->supportMeans) { ?>
                    
                    <p><label>Transcription:</label>
                    <!-- TODO: style this -->
                    <textarea readonly style="display: block; max-width: 100%; min-width: 100%; height: auto"><?php echo $claim->transcription; ?></textarea>
                    <p><label>Citation:</label> <?php echo $claim->citation; ?></p>
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
                    ); ?>"> <?php
                    $claim_id = $claim->claimID;
                    ?> <input name="claimID" value="<?php echo htmlspecialchars(
                        $claim_id
                    ); ?>">
                    
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
                    <?php flagging($claim_id); ?>
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
                } else {
                    ?>
                    <br> <button class="openmodal myBtn">Support or Flag Claim</button>
                    <!-- The Modal -->
                    <div class="modal myModal">
                    <!-- Modal content -->
                    <div class="modal-content">
                    <span class="close">&times;</span>
                    <form method="POST" id="myForm" action="insert.php">
                    
                    <p style="color:#000000" ;>
                    <input type="hidden" name="claimID"
                    value="<?php echo htmlspecialchars($claim_id); ?>" readonly>
                    <?php
                    // echo '<script type="text/javascript">alert("a LERT: ' . $claim_id . '");</script>';
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
                    <?php flagging($claim_id); ?>
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
                }
                
                // end of else statement
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
                <style>
                .card {
                    border: 3px double black;
                    padding-inline: 1rem;
                    margin-bottom: 1rem;
                }
                label {
                    font-weight: bold;
                    display: inline;
                }
                
                table {
                    background: white;
                }
                
                th {
                    text-decoration: underline;
                    text-align: left;
                }
                
                td, th {
                    padding: 1rem;
                    border: 1px solid;
                }
                </style>
                