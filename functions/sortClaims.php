<?php
// starts two chains of recursion. one with normal root claims.
// the other with root rivals. the rivals, of course, are put into the rival recursion.

function sortclaims($claimid)
{
    require __DIR__ . '/../config/db_connect.php';

    // THIS IS SIMPLY FOR DISPLAY OF SUBJECT/TARGETP BELOW

    $dis = 'SELECT DISTINCT subject, targetP, active, supportMeans, reason, example, citation
    from claimsdb
    where ? = claimID

        '; // SQL with parameters
    $st = $conn->prepare($dis);
    $st->bind_param('i', $claimid);
    $st->execute();
    $disp = $st->get_result(); // get the mysqli result

    // SIMPLY FOR DISPLAY ABOVE THIS POINT

    // below is for rivals
    $flag = 'SELECT DISTINCT flagType, claimIDFlagger, claimIDFlagged
        from flagsdb
        WHERE ? = claimIDFlagger'; // SQL with parameters
    $stmt4 = $conn->prepare($flag);
    $stmt4->bind_param('i', $claimid);
    $stmt4->execute();
    $result2 = $stmt4->get_result(); // get the mysqli result
    $numhitsflag = mysqli_num_rows($result2);
    // IF THIS CLAIM IS A FLAGGER this obtains the FLAGGER'S flagtype's and flagged.
    // this is to find rival claims..this is literally JUST used for rivals.
    // rivals have to be flaggers and flagged.

    $resultFlagType = $r = $d = '';
    while ($flagge = $result2->fetch_assoc()) {
        $resultFlagType = $flagge['flagType'];
        $r = $flagge['claimIDFlagger'];

        $d = $flagge['claimIDFlagged'];
    }
    // above is for rivals

    if ('Thesis Rival' == $resultFlagType) {
        echo ' <br> The flag ' . $r . ' has a rival!: ' . '<br>';
        // ECHO "START 1";
        sortclaimsRival($r);
        // ECHO "END 1";
        // for THIS claimid - check for flaggers that aren't rival .. sort claim those

        // ECHO "START 2";
        sortclaimsRival($d);
        // ECHO "END 2";
        // for the CORRESPONDING claimid - check for flaggers that aren't rival .. sort claim those.
    } else {
        ?>
        <li> <label for="<?php echo $claimid; ?>"><?php while (
            $d = $disp->fetch_assoc()
            ) {
                if ('supporting' == $resultFlagType) {
                    echo "<img height = '45' width = '32' src='assets/img/support.png'> <br>";
                    echo $d['supportMeans'] . '<br>';
                    if ('Inference' == $d['supportMeans']) {
                        echo 'Reason: ' .
                        $d['subject'] .
                        ' ' .
                        $d['reason'] .
                        ', as in the case of ' .
                        $d['example'] .
                        '<BR>';
                    }

                    if (
                        'Testimony' == $d['supportMeans'] ||
                        'Perception' == $d['supportMeans']
                        ) {
            echo 'Citation: ' . $d['citation'] . '<BR>';
        }
    } elseif ('' == $resultFlagType) {
        echo '<h1>Thesis</h1>';
        echo '<br>' . $d['subject'] . ' ' . $d['targetP'] . '<br>';
    } else {
        echo "<img src='assets/img/flag.png'> <br>";

        echo '<br> Flagged: ' . $resultFlagType . '<br>';
        echo '<h1>Thesis</h1>';
        echo '<br>' . $d['subject'] . ' ' . $d['targetP'] . '<br>';
    }
    echo '#' . $claimid . '<br>';

    // add is subject person or object to inference div

    // FONT CHANGING
    if (1 == $d['active']) {
        // $font = 'seagreen';
        // echo "<img width='50' height='50' src='assets/img/check_mark.php'> <br>! This claim is uncontested. <br>";
    } else {
        // $font = '#B7B802';
        echo "<img src='assets/img/alert.png'> <br>";
    }

    // ------------------------- BELOW is modal code
    createModal($claimid);
    // ------------------------- ABOVE is modal code
}
// end of while statement
?> </label><input id="<?php echo $claimid; ?>" type="checkbox">
            <ul> <span class="more">&hellip;</span>

            <?php
                        // below is to continue recursion
                        $sql1 = "SELECT DISTINCT claimIDFlagger
  from claimsdb, flagsdb
        WHERE ? = claimIDFlagged AND flagType NOT LIKE 'Thesis Rival'"; // SQL with parameters
                        $stmt1 = $conn->prepare($sql1);
                        $stmt1->bind_param('i', $claimid);
                        $stmt1->execute();
                        $result1 = $stmt1->get_result(); // get the mysqli result
                        $numhits1 = mysqli_num_rows($result1);
                        // IF A CLAIM IS FLAGGED IT obtains flaggers that aren't rivals
                        // if its a thesis rival it will show up in the query above
                        // this is when the claim is the flagged. this is what gets pushed in the recursion.

                        while ($user = $result1->fetch_assoc()) {
                            if (0 == $numhits1) {
                            } else {
                                sortclaims($user['claimIDFlagger']);
                            }
                        }

        // end while loop
        // recursion finished here
        ?></ul><?php
    } // end of else statement
} // end of function

function sortclaimsRIVAL($claimid)
{
    require __DIR__ . '/../config/db_connect.php';

    // BELOW IS SIMPLY FOR DISPLAY OF SUBJECT/TARGETP
    // these aren't passed through the function so they must be obtained every interation
    $dis = 'SELECT DISTINCT subject, targetP, active, supportMeans, reason, example
  from claimsdb
  where ? = claimID
  ';
    $st = $conn->prepare($dis);
    $st->bind_param('i', $claimid);
    $st->execute();
    $disp = $st->get_result();
    // ABOVE IS SIMPLY FOR DISPLAY

    // BELOW IS JUST TO DISPLAY THE RIVAL PAIR
    $sql1 = "SELECT DISTINCT claimIDFlagger
  from claimsdb, flagsdb
  where ? = claimIDFlagged AND flagType LIKE 'Thesis Rival'
  ";
    $stmt1 = $conn->prepare($sql1);
    $stmt1->bind_param('i', $claimid);
    $stmt1->execute();
    $result1 = $stmt1->get_result();
    $numhits1 = mysqli_num_rows($result1);
    // above looks for normal non-rival flags for this rivaling claim.
    while ($user = $result1->fetch_assoc()) {
        $rivaling = $user['claimIDFlagger'];
    }
    // end while loop
    // ABOVE IS JUST TO DISPLAY RIVAL PAIR
    ?>

        <li> <label style="background:#FFFFE0" for="<?php echo $claimid; ?>"><?php while (
    $d = $disp->fetch_assoc()
) {
    echo "<img width='100' height='20' src='assets/img/rivals.png'> <br><br>";

    if (1 == $d['active']) {
        // echo "<img width='50' height='50' src='assets/img/check_mark.php'> <br>! This claim is uncontested. <br>";
    } else {
        echo "<img src='assets/img/alert.png'> <br>";
    }

    echo '<h4>Contests #' . $rivaling . '</h4>';
    echo '<h1>Thesis</h1>';
    echo $d['subject'] . ' ' . $d['targetP'];
    echo '<BR>' . $claimid;
    // --------------------------- BELOW is modal code
    createModal($claimid);
    // --------------------------- ABOVE is modal code
}// end of display fetching loop
    ?>

        </label><input id="<?php echo $claimid; ?>" type="checkbox">
        <ul> <span class="more">&hellip;</span>
            <!--</font>-->
                <?php
                // below finds the flagger and continues the recursion by pushing it back to sortclaims recursion
                $sql1 = "SELECT DISTINCT claimIDFlagger
from claimsdb, flagsdb
where ? = claimIDFlagged AND flagType NOT LIKE 'Thesis Rival'
";
                $stmt1 = $conn->prepare($sql1);
                $stmt1->bind_param('i', $claimid);
                $stmt1->execute();
                $result1 = $stmt1->get_result();
                $numhits1 = mysqli_num_rows($result1);
                // above looks for normal non-rival flags for this rivaling claim.
                while ($user = $result1->fetch_assoc()) {
                    if (0 == $numhits1) {
                    } else {
                        sortclaims($user['claimIDFlagger']);
                    }
                }// it's pushed - now the function is finished!
    // end while loop
    ?>
        </ul><?php
} // end of rivalfunction
?>
