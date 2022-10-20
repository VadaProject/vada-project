<?php


function flagging($claimIDFlaggedINSERT)
{
    ?> <div class='scroll'> <?php $claimID = $temp = $result = $topic = $array = $claim_fk = $IclaimID = $thesisST = $reasonST = $ruleST = $NewOld = $oldClaim = $subject = $targetP = $supportMeans = $supportforID = $supportID = $example = $URL = $rd = $reason = $flagType = $flagType = $flagTypeT = $flagTypeR = $flagTypeE = $flagURL = $flagSource = $flagID = $inferenceIDFlagger = $grammar = $active =
     ''; ?>
        <html>
        <p style="color:#000000" ;>
            <?php
            global $topic;
            $topic = $topic;
            ?>
            <label>Topic (Read only)</label><br>
            <input type="text" name="topic" value="<?php echo htmlspecialchars(
                $topic
                                                   ); ?>" readonly><br>
        <div id="hideThesis">
            Enter your new claim.<br>
            <label><u>Subject</u> <u>Target Property</u></label><br>
            <textarea class="subject" type="text" id="subject" name="subject" value="<?php echo htmlspecialchars(
                $subject
                                                                                     ); ?>">Enter Subject</textarea>
            <textarea class="targetP" type="text" id="targetP" name="targetP" value="<?php echo htmlspecialchars(
                $targetP
                                                                                     ); ?>">Enter Target Property</textarea>
            <br>
            <u>
                <p style="color:grey">Thesis Statement
            </u><br>
            <span class="jsValue3"></span> <span class="jsValue4"></span>
            </p>
            </p>
            <p style="color:black"> Is the subject an object or a person?
                <select name="grammar" id="grammar" value="grammar">
                    <option value="">Choose One</option>
                    <option value="object">Object</option>
                    <option value="person">Person</option>
                </select> <br>
        </div>
        What is your Support Means?
        <select name="union" id="union" value="union">
            <option value="">Choose One</option>
            <option value="Inference">Inference</option>
            <option value="Testimony">Testimony</option>
            <option value="Perception">Perception</option>
            <option value="Tarka">Tarka</option>
        </select>
        </p>
        </p>
        <div id="hiddenRule">
            <div id="some-div">
                <img src="assets/img/question_mark.png">
                <span id="explain-element"> Hint: Your reason statement should answer "Why?". You might think of the reason as what comes after 'because....'.</span>
            </div>
            <p style="color:black"><u>Reason</u><br>
                <textarea class="reason" type="text" id="reason" name="reason" value="<?php echo htmlspecialchars(
                    $reason
                                                                                      ); ?>">Enter Reason Property</textarea>
            </p>
            <u> Reason Statement</u><br>
            <?php if ('claim' == retrieveCOS($claimIDFlaggedINSERT)) {
                retrieveSubject($claimIDFlaggedINSERT);
            } else {
                ?> <span class="jsValue5"></span>, <?php
            } ?>
            <span class="jsValue6"></span>
            <br><br>
            <div id="some-div">
                <img src="assets/img/question_mark.png">
                <span id="explain-element"> Hint: The example cannot be the same as the subject.</span>
            </div>
            <u> Rule and Example Statement</u><br>
            Whatever/Whomever
            <!-- Plain Javascript Example -->
            <span class="jsValue"></span>,
            <?php if ('claim' == retrieveCOS($claimIDFlaggedINSERT)) {
                retrieveTargetP($claimIDFlaggedINSERT);
            } else {
                ?> <span class="jsValue2"></span>, <?php
            } ?>
            as in the case of:
            <br>
            <textarea id="example" name="example" value="<?php echo htmlspecialchars(
                $example
                                                         ); ?>">Enter Example</textarea>
        </div>
        <div id="perceptionHint">
            <div id="some-div">
                <img src="assets/img/question_mark.png">
                <span id="explain-element"> Hint: Perception MUST be audio or video.</span>
            </div>
        </div>
        <div id="hiddenTranscription">
            <u>Transcription</u>
            <div id="some-div">
                <img src="assets/img/question_mark.png">
                <span id="explain-element"> Hint: The transcription MUST be a quotation from the source with no additional dialogue.</span>
            </div>
        </div>
        <textarea id="transcription" name="transcription" value="<?php echo htmlspecialchars(
            $transcription
                                                                 ); ?>">Transcription</textarea><br>
        <div id="hiddenCitation">
            <u>Citation</u>
            <?php $author = $title = $publication = $date = ''; ?>
            <div id="some-div">
                <img src="assets/img/question_mark.png">
                <span id="explain-element"> Please include as applicable: author, title, publication, and date of publication.</span>
                <br><textarea id="author" name="author" value="<?php echo htmlspecialchars(
                    $author
                                                               ); ?>">Author</textarea><br>
                <textarea id="title" name="title" value="<?php echo htmlspecialchars(
                    $title
                                                         ); ?>">Title</textarea><br>
                <textarea id="publication" name="publication" value="<?php echo htmlspecialchars(
                    $publication
                                                                     ); ?>">Publication</textarea><br>
                <textarea id="date" name="date" value="<?php echo htmlspecialchars(
                    $date
                                                       ); ?>">Date of Publication</textarea><br>
                <textarea id="citationURL" name="citationURL" value="<?php echo htmlspecialchars(
                    $citationURL
                                                                     ); ?>">URL of Citation</textarea><br>
            </div>
        </div>
        <textarea id="citation" name="citation" hidden="hidden" value="<?php echo htmlspecialchars(
            $citation
                                                                       ); ?>">Empty Citation</textarea><br>
        <div id="hiddenURL">
            <u>URL</u>
        </div>
        <textarea id="url" name="url" value="<?php echo htmlspecialchars(
            $url
                                             ); ?>">Enter URL</textarea><br>
        <div id="hiddenTS">
            <u>Timestamp of content</u>
        </div>
        <textarea id="vidtimestamp" name="vidtimestamp" value="<?php echo htmlspecialchars(
            $vidtimestamp
                                                               ); ?>">Enter timestamp of specified material</textarea>
        <script>
            // This is for reason
            var $jsReason = document.querySelector('.reason');
            var $jsValue = document.querySelector('.jsValue');
            $jsReason.addEventListener('input', function(event) {
                $jsValue.innerHTML = $jsReason.value;
            }, false);
            //-------------------------
            //This is for Targetp
            var $jsTargetP = document.querySelector('.targetP');
            var $jsValue2 = document.querySelector('.jsValue2');
            $jsTargetP.addEventListener('input', function(event) {
                $jsValue2.innerHTML = $jsTargetP.value;
            }, false);
            //-------------------------
            //This is for subject
            var $jsSubject = document.querySelector('.subject');
            var $jsValue3 = document.querySelector('.jsValue3');
            $jsSubject.addEventListener('input', function(event) {
                $jsValue3.innerHTML = $jsSubject.value;
            }, false);
            //-------------------------
            //This is for the second target property (4)
            var $jsTargetP2 = document.querySelector('.targetP');
            var $jsValue4 = document.querySelector('.jsValue4');
            $jsTargetP2.addEventListener('input', function(event) {
                $jsValue4.innerHTML = $jsTargetP2.value;
            }, false);
            //-------------------------
            //This is for the second subject property (5)
            var $jsSubject2 = document.querySelector('.subject');
            var $jsValue5 = document.querySelector('.jsValue5');
            $jsSubject2.addEventListener('input', function(event) {
                $jsValue5.innerHTML = $jsSubject2.value;
            }, false);
            //-------------------------
            //This is for the reason (6)
            var $jsReason2 = document.querySelector('.reason');
            var $jsValue6 = document.querySelector('.jsValue6');
            $jsReason2.addEventListener('input', function(event) {
                $jsValue6.innerHTML = $jsReason2.value;
            }, false);
        </script>
    </div>
    <?php
}

// end of flagging function
?>
