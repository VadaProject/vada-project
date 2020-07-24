<div class="wrapper">
    <ul>
      <li class="noline">


<?php include('config/db_connect.php');
include('templates/header.php');?>

<link rel="stylesheet" href="./style.css"> 
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>






<BR><BR>
    
<a class="brand-text" href="add.php" style=" color : #fff;">Add New Claim</a><br><br>

Claims displayed as a <font color = "seagreen"> green font </font> mean that they are currently active. <br> 
Claims displayed as a <font color = "#FFFF99"> yellow font </font> mean that the are currently inactive. <br>

</center>
<center>
      <?php   //changing the centers above is a fun change
        //this code finds ALL claims that are not flaggers (all root claims)
$root12 = "SELECT DISTINCT claimID
        from claimsdb, flagsdb 
        WHERE claimID NOT IN (SELECT DISTINCT claimIDFlagger FROM flagsdb)
        "; // SQL with parameters
$stmt52 = $conn->prepare($root12); 
$stmt52->execute();
$rootresult12 = $stmt52->get_result(); // get the mysqli result
$numhitsroot = mysqli_num_rows($rootresult12);
 while($root2 = $rootresult12->fetch_assoc())
  {
         sortclaims($root2['claimID']);

  }
//duplicate

  $root1 = "SELECT DISTINCT claimID
        from claimsdb, flagsdb 
        WHERE claimID NOT IN (SELECT DISTINCT claimIDFlagger FROM flagsdb)
        "; // SQL with parameters
$stmt5 = $conn->prepare($root1); 
$stmt5->execute();
$rootresult1 = $stmt5->get_result(); // get the mysqli result
$numhitsroot = mysqli_num_rows($rootresult1);

 while($root = $rootresult1->fetch_assoc())
  {
         restoreActivity($root['claimID']);
  }






$root2 = "SELECT DISTINCT claimIDFlagger
        from flagsdb 
            WHERE isRootRival = 1;
        "; // SQL with parameters
$stmt12 = $conn->prepare($root2); 
$stmt12->execute();
$rootresult2 = $stmt12->get_result(); // get the mysqli result
$numhitsroot2 = mysqli_num_rows($rootresult2);

while($root2 = $rootresult2->fetch_assoc())
  {      restoreActivityRIVAL($root2['claimIDFlagger']);
       //  echo "RIVAL";
  }



$root22 = "SELECT DISTINCT claimIDFlagger
        from flagsdb 
            WHERE isRootRival = 1;
        "; // SQL with parameters
$stmt122 = $conn->prepare($root22); 
$stmt122->execute();
$rootresult22 = $stmt122->get_result(); // get the mysqli result
$numhitsroot2 = mysqli_num_rows($rootresult22);

while($root22 = $rootresult22->fetch_assoc())
  { 

         sortclaimsRIVAL($root22['claimIDFlagger']);
        
  }

//duplicate

//sort by topic on each page 
  //thesisrival can only be flagged once 


/*
$root2 = "SELECT DISTINCT claimIDFlagger
        from flagsdb 
            WHERE isRootRival = 1;
        "; // SQL with parameters
$stmt12 = $conn->prepare($root2); 
$stmt12->execute();
$rootresult2 = $stmt12->get_result(); // get the mysqli result
$numhitsroot2 = mysqli_num_rows($rootresult2);

while($root2 = $rootresult2->fetch_assoc())
  { 
         restoreActivity($root2['claimIDFlagger']);
       //  echo "RIVAL";
  }
*/

//starts two chains of recursion. one with normal root claims. the other with root rivals. the rivals, of course, are put into the rival recursion.

function sortclaims($claimid)
{

include('config/db_connect.php');
 
// THIS IS SIMPLY FOR DISPLAY OF SUBJECT/TARGETP BELOW

$dis = "SELECT DISTINCT subject, targetP, active
        from claimsdb
        where ? = claimID
         
        "; // SQL with parameters
$st = $conn->prepare($dis); 
$st->bind_param("i", $claimid);
$st->execute();
$disp = $st->get_result(); // get the mysqli result


// SIMPLY FOR DISPLAY ABOVE THIS POINT



//below is for rivals
$flag = "SELECT DISTINCT flagType, claimIDFlagger, claimIDFlagged
        from flagsdb
        WHERE ? = claimIDFlagger"; // SQL with parameters
$stmt4 = $conn->prepare($flag); 
$stmt4->bind_param("i", $claimid);
$stmt4->execute();
$result2 = $stmt4->get_result(); // get the mysqli result
$numhitsflag = mysqli_num_rows($result2);
//IF THIS CLAIM IS A FLAGGER this obtains the FLAGGER'S flagtype's and flagged. 
//this is to find rival claims..this is literally JUST used for rivals. 
//rivals have to be flaggers and flagged. 


$resultFlagType = $r = $d = '';
while($flagge = $result2->fetch_assoc())
  {
    $resultFlagType = $flagge['flagType'];
    $r = $flagge['claimIDFlagger'];
    $d = $flagge['claimIDFlagged'];
  }
//above is for rivals

?>

  <li> <label for="<?php echo $claimid; ?>"><?php 
while($d = $disp->fetch_assoc())
{
  // FONT CHANGING
 if($d['active'] == 1)
{ $font = 'seagreen'; }
else 
{ $font = '#FFFF99'; } ?>

<font color = "<?php echo $font; ?>"> 
<?php    // END FONT CHANGING

echo $claimid . "<br>" . $d['subject'] . ' ' . $d['targetP'] . "<br> Flag type: " . $resultFlagType;

// ------------------------- BELOW is modal code
createModal($claimid);
// ------------------------- ABOVE is modal code

}


 ?> </label><input id="<?php echo $claimid; ?>" type="checkbox"> <ul> <span class="more">&hellip;</span>



<?php




 if($resultFlagType == "thesisRival")
      {
      sortclaimsRival($r);
      // for THIS claimid - check for flaggers that aren't rival .. sort claim those
      sortclaimsRival($d);
      // for the CORRESPONDING claimid - check for flaggers that aren't rival .. sort claim those.
      }

//below is to continue recursion
 $sql1 = "SELECT DISTINCT claimIDFlagger
        from claimsdb, flagsdb
        WHERE ? = claimIDFlagged AND flagType NOT LIKE 'thesisRival'"; // SQL with parameters
$stmt1 = $conn->prepare($sql1); 
$stmt1->bind_param("i", $claimid);
$stmt1->execute();
$result1 = $stmt1->get_result(); // get the mysqli result
$numhits1 = mysqli_num_rows($result1);
//IF A CLAIM IS FLAGGED IT obtains flaggers that aren't rivals
//if its a thesis rival it will show up in the query above
//this is when the claim is the flagged. this is what gets pushed in the recursion.

while($user = $result1->fetch_assoc())
{
 if($numhits1 == 0)
  { }
   
   else { sortclaims($user['claimIDFlagger']); }
    
} // end while loop
// recursion finished here

?></ul><?php
} // end of function





function sortclaimsRIVAL($claimid)
{
include('config/db_connect.php');

// BELOW IS SIMPLY FOR DISPLAY OF SUBJECT/TARGETP
//these aren't passed through the function so they must be obtained every interation
$dis = "SELECT DISTINCT subject, targetP, active
        from claimsdb
        where ? = claimID
        "; 
$st = $conn->prepare($dis); 
$st->bind_param("i", $claimid);
$st->execute();
$disp = $st->get_result(); 
// ABOVE IS SIMPLY FOR DISPLAY

//BELOW IS JUST TO DISPLAY THE RIVAL PAIR
 $sql1 = "SELECT DISTINCT claimIDFlagger
        from claimsdb, flagsdb
        where ? = claimIDFlagged AND flagType LIKE 'thesisRival'
        ";
$stmt1 = $conn->prepare($sql1); 
$stmt1->bind_param("i", $claimid);
$stmt1->execute();
$result1 = $stmt1->get_result(); 
$numhits1 = mysqli_num_rows($result1);
//above looks for normal non-rival flags for this rivaling claim.
while($user = $result1->fetch_assoc())
{ $rivaling = $user['claimIDFlagger']; 
 } // end while loop
//ABOVE IS JUST TO DISPLAY RIVAL PAIR

?>


 <li> <label for="<?php echo $claimid; ?>"><?php 
while($d = $disp->fetch_assoc())
{
  // FONT CHANGING
 if($d['active'] == 1)
{ $font = 'seagreen'; }
else 
 { $font = '#FFFF99';} 

?>
<font color = "<?php echo $font; ?>"> 
<?php    // END FONT CHANGING

 echo $claimid;
echo nl2br("\r\n");     
echo "RIVALS #" . $rivaling . "!";
echo nl2br("\r\n");      
  echo $d['subject'] . ' ';
  echo $d['targetP'];
 echo nl2br("\r\n");

//--------------------------- BELOW is modal code
createModal($claimid);
//--------------------------- ABOVE is modal code

} // end of display fetching loop


 ?>

 </label><input id="<?php echo $claimid; ?>" type="checkbox"><ul> <span class="more">&hellip;</span>

<?php
//below finds the flagger and continues the recursion by pushing it back to sortclaims recursion
  $sql1 = "SELECT DISTINCT claimIDFlagger
        from claimsdb, flagsdb
        where ? = claimIDFlagged AND flagType NOT LIKE 'thesisRival'
        ";
$stmt1 = $conn->prepare($sql1); 
$stmt1->bind_param("i", $claimid);
$stmt1->execute();
$result1 = $stmt1->get_result(); 
$numhits1 = mysqli_num_rows($result1);
//above looks for normal non-rival flags for this rivaling claim.
while($user = $result1->fetch_assoc())
{
if($numhits1 == 0)
  { }
   else { sortclaims($user['claimIDFlagger']); }
 } // end while loop

//it's pushed - now the function is finished!

?></font></ul><?php 

} // end of rivalfunction









function restoreActivity ($claimid)
{
include('config/db_connect.php');
$act = "SELECT DISTINCT claimIDFlagger
        from flagsdb
        WHERE claimIDFlagged = ? and flagType NOT LIKE 'thesisRival'"; 
$s = $conn->prepare($act); 
$s->bind_param("i", $claimid);
$s->execute();
$activity = $s->get_result(); 
$nh = mysqli_num_rows($activity);

//above grabs all flaggers 



//below is for rivals

$a = "SELECT DISTINCT claimIDFlagger
        from flagsdb
        WHERE claimIDFlagged = ? and flagType LIKE 'thesisRival'"; 
$si = $conn->prepare($a); 
$si->bind_param("i", $claimid);
$si->execute();
$sim = $si->get_result(); 


while($mi = $sim->fetch_assoc())
  { 
      restoreActivityRIVAL($mi['claimIDFlagger']);
  }

//above is for rivals



while($activestatus = $activity->fetch_assoc())
  { 


    $h = "SELECT DISTINCT active
        from claimsdb
        WHERE ? = claimID"; // SQL with parameters
      $noce = $conn->prepare($h); 
      $noce->bind_param("i", $activestatus['claimIDFlagger']);
      $noce->execute();
      $res = $noce->get_result(); // get the mysqli result
      $numh = mysqli_num_rows($res);
      //checks the active status of the flagger

    
  //    echo nl2br("\r\n");
    //  echo $activestatus['claimIDFlagger'];
      //echo nl2br("\r\n");

$everyInactive = 'true';
//echo $everyInactive;
  while($r = $res->fetch_assoc())
  {  
    
    if($r['active'] == 1)
    {
      global $everyInactive;
      $everyInactive = 'false';
  //    echo $everyInactive;
      $act = "UPDATE claimsdb 
SET active = 0
WHERE claimID = ? 
"; // SQL with parameters
$upd = $conn->prepare($act); 
$upd->bind_param("i", $claimid);
$upd->execute();
    } // end of if
  } //end of while loop


  if($everyInactive == 'true')
  {
  
//echo "ANSWER" . $everyInactive;
// BELOW CHANGES THE ACTIVE STATE OF OTHER CLAIMS
$act = "UPDATE claimsdb 
SET active = 1
WHERE claimID = ? 
"; // SQL with parameters
$upd = $conn->prepare($act); 
$upd->bind_param("i", $claimid);
$upd->execute();
 } // end of second if statement



 if($nh == 0)
  { }
   else { restoreActivity($activestatus['claimIDFlagger']); }
    
     } //end of outer while loop

//THIS ABOVE CHANGES THE ACTIVE STATE OF OTHER CLAIMS
  




  }  // end function


function restoreActivityRIVAL ($claimid)
{
//below finds the flagger and continues the recursion by pushing it back to normal restore activity function 
  // IN ADDITION below is to check active status of flagging claims OF INITIAL RIVAL 

 
$everyInactiveA = 'true';

$everyInactiveB = 'true';

include('config/db_connect.php');

  $sql1 = "SELECT DISTINCT claimIDFlagger
        from claimsdb, flagsdb
        where ? = claimIDFlagged AND flagType NOT LIKE 'thesisRival'
        ";
$stmt1 = $conn->prepare($sql1); 
$stmt1->bind_param("i", $claimid);
$stmt1->execute();
$result1 = $stmt1->get_result(); 
$numhits1 = mysqli_num_rows($result1);
//above looks for normal non-rival flags for this rivaling claim.
while($user = $result1->fetch_assoc())
{

  $nodeFlaggers = $user['claimIDFlagger'];
if($numhits1 == 0)
  { }
   else { restoreActivity($nodeFlaggers); } //end of  restoreactivity push FOR THIS SIDE OF THE RIVAL PAIR. the rival companion is pushed to restoreactivity at the bottom of this function.

//above it finds rival A's flaggers.


//below is to check active status of flagging claims OF RIVAL COMPANION
$rivaling = '';

$sql12 = "SELECT DISTINCT claimIDFlagger
        from claimsdb, flagsdb
        where ? = claimIDFlagged AND flagType LIKE 'thesisRival'
        ";
$stmt12 = $conn->prepare($sql12); 
$stmt12->bind_param("i", $claimid);
$stmt12->execute();
$result12 = $stmt12->get_result(); 
$numhits1 = mysqli_num_rows($result12);
//found rival pair!
while($user = $result12->fetch_assoc())
{ $rivaling = $user['claimIDFlagger'];  //$rivaling is Rival B.
 } // end while loop

//above finds rival A's companion, aka rival b.

 //above is to check active status of flagging claims OF RIVAL COMPANION






//this is the flaggers for rival A. 

$h = "SELECT DISTINCT active
        from claimsdb
        WHERE ? = claimID"; // SQL with parameters
      $noce = $conn->prepare($h); 
      $noce->bind_param("i", $nodeFlaggers);
      $noce->execute();
      $res = $noce->get_result(); // get the mysqli result
      $numh = mysqli_num_rows($res);
      //checks the active status of the flagger

    
  //    echo nl2br("\r\n");
    //  echo $activestatus['claimIDFlagger'];
      //echo nl2br("\r\n");

$everyInactiveA = 'true';
//echo $everyInactive;
  while($r = $res->fetch_assoc())
  {  
    
    if($r['active'] == 1)
    {
      global $everyInactiveA;
      $everyInactiveA = 'false';
  //    echo $everyInactive;
      /*$act = "UPDATE claimsdb 
SET active = 0
WHERE claimID = ? 
"; // SQL with parameters
$upd = $conn->prepare($act); 
$upd->bind_param("i", $claimid);
$upd->execute(); */
    } // end of if
  } //end of while loop


  if($everyInactiveA == 'true')
  {
  
//echo "ANSWER" . $everyInactive;
// BELOW CHANGES THE ACTIVE STATE OF OTHER CLAIMS
/*$act = "UPDATE claimsdb 
SET active = 1
WHERE claimID = ? 
"; // SQL with parameters
$upd = $conn->prepare($act); 
$upd->bind_param("i", $claimid);
$upd->execute();
*/
 } // end of second if statement









 } // end while loop

//above is to check active status of flagging claims OF INITIAL RIVAL

//it's pushed - now the function attends to the rival companion.


 // ---------------------------------------------------------------
//echo $rivaling . "MEOW MEOW MEOW MEOW MEOW";
//now that we have the rival pair, let's push it through to the normal sortclaims so it doesn't get stuck in a loop and it continues into normal restore activity

//this is finding the flaggers for rival B
$sql1 = "SELECT DISTINCT claimIDFlagger
        from claimsdb, flagsdb
        where ? = claimIDFlagged AND flagType NOT LIKE 'thesisRival'
        ";
$stmt1 = $conn->prepare($sql1); 
$stmt1->bind_param("i", $rivaling);
$stmt1->execute();
$result1 = $stmt1->get_result(); 
$numhits1 = mysqli_num_rows($result1);
//above looks for normal non-rival flags for this rivaling claim.
while($userRIVALING = $result1->fetch_assoc())
{
if($numhits1 == 0)
  { }
   else {restoreActivity($userRIVALING['claimIDFlagger']); }
 






$h = "SELECT DISTINCT active
        from claimsdb
        WHERE ? = claimID"; // SQL with parameters
      $noce = $conn->prepare($h); 
      $noce->bind_param("i", $userRIVALING['claimIDFlagger']);
      $noce->execute();
      $res = $noce->get_result(); // get the mysqli result
      $numh = mysqli_num_rows($res);
      //checks the active status of the flagger

    
  //    echo nl2br("\r\n");
    //  echo $activestatus['claimIDFlagger'];
      //echo nl2br("\r\n");

//echo $everyInactive;
  while($r = $res->fetch_assoc())
  {  
    
    if($r['active'] == 1)
    {
      global $everyInactiveB;
      $everyInactiveB = 'false';
  //    echo $everyInactive;
      /*$act = "UPDATE claimsdb 
SET active = 0
WHERE claimID = ? 
"; // SQL with parameters
$upd = $conn->prepare($act); 
$upd->bind_param("i", $claimid);
$upd->execute(); */
//THIS MAKES IT INACTIVE

    } // end of if
  } //end of while loop

/*
  if($everyInactive == 'true')
  {
  
//echo "ANSWER" . $everyInactive;
// BELOW CHANGES THE ACTIVE STATE OF OTHER CLAIMS
$act = "UPDATE claimsdb 
SET active = 1
WHERE claimID = ? 
"; // SQL with parameters
$upd = $conn->prepare($act); 
$upd->bind_param("i", $claimid);
$upd->execute();


$act = "UPDATE claimsdb 
SET active = 0
WHERE claimID = ? 
"; // SQL with parameters
$upd = $conn->prepare($act); 
$upd->bind_param("i", $rivaling);
$upd->execute();
 

 } // end of second if statement
*/





 } // end while loop

if($everyInactiveA == 'true' && $everyInactiveB == 'true' || $everyInactiveA == 'false' && $everyInactiveB == 'false')
{
$act = "UPDATE claimsdb 
SET active = 0
WHERE claimID = ? 
"; // SQL with parameters
$upd = $conn->prepare($act); 
$upd->bind_param("i", $claimid);
$upd->execute();


$act = "UPDATE claimsdb 
SET active = 0
WHERE claimID = ? 
"; // SQL with parameters
$upd = $conn->prepare($act); 
$upd->bind_param("i", $rivaling);
$upd->execute();

}




if($everyInactiveA == 'true' && $everyInactiveB == 'false')
{
$act = "UPDATE claimsdb 
SET active = 1
WHERE claimID = ? 
"; // SQL with parameters
$upd = $conn->prepare($act); 
$upd->bind_param("i", $claimid);
$upd->execute();


$act = "UPDATE claimsdb 
SET active = 0
WHERE claimID = ? 
"; // SQL with parameters
$upd = $conn->prepare($act); 
$upd->bind_param("i", $rivaling);
$upd->execute();

}

if($everyInactiveA == 'false' && $everyInactiveB == 'true')
{
$act = "UPDATE claimsdb 
SET active = 0
WHERE claimID = ? 
"; // SQL with parameters
$upd = $conn->prepare($act); 
$upd->bind_param("i", $claimid);
$upd->execute();


$act = "UPDATE claimsdb 
SET active = 1
WHERE claimID = ? 
"; // SQL with parameters
$upd = $conn->prepare($act); 
$upd->bind_param("i", $rivaling);
$upd->execute();

}





 } //end of function



function createModal($claimid)
{
include('config/db_connect.php');

    // Check if user has requested to get detail
    if (isset($_POST["get_data"]))
    {
        // Get the ID of customer user has selected
        $id = $_POST["id"];

         include('config/db_connect.php');

        // Getting specific customer's detail
        $sql = "SELECT * FROM claimsdb WHERE claimID='$id'";
        $result = mysqli_query($conn, $sql);
        $row = mysqli_fetch_object($result);

        // Important to echo the record in JSON format
        echo json_encode($row);

        // Important to stop further executing the script on AJAX by following line
        exit();
    }

    // Connecting with database and executing query
    include('config/db_connect.php');
     $sql = "SELECT * FROM claimsdb WHERE claimID = '$claimid'";
    $result = mysqli_query($conn, $sql);
?>

<!-- Include bootstrap & jQuery 
<link rel="stylesheet" href="bootstrap.css" />-->
<script src="jquery-3.3.1.min.js"></script> 
<script src="bootstrap.js"></script>

<!-- Creating table heading -->
<div class="container">
  
  
        <!-- Display dynamic records from database -->
        <?php while ($row = mysqli_fetch_object($result)) { ?>
            <button class = "btn btn-primary" onclick="loadData(this.getAttribute('data-id'));" data-id="<?php echo $row->claimID; ?>">
                Details
            </button>
        <?php } ?>
  
</div>

<script>
    function loadData(id) {
        console.log(id);
        $.ajax({
            url: "adnanindex.php",
            method: "POST",
            data: {get_data: 1, id: id},
            success: function (response) {
                response = JSON.parse(response);
                console.log(response);
                var html = "";

                // Displaying city
//                html += "<div class='row'>";
 //                   html += "<div class='col-md-6'></div>";
                    html += "<div class='col-md-6'><p style=\"color:black\">" + "Support Means: " + response.supportMeans + "<BR> ClaimID: " + response.claimID + "</div><BR><p style=\"color:black\">";

                if(response.supportMeans == 'Testimony')
                {
                  html += "<BR> Subject: " + response.subject + "<BR> Target Property: " + response.targetP + "URL: " + response.URL + " <BR> Research doc: " + response.rd;
                }

                if(response.supportMeans == 'Perception')
                {
                 html += "<BR> Subject: " + response.subject + "<BR> Target Property: " + response.targetP + "Summary: " + response.summary;
                }
                
                if(response.supportMeans == 'Inference')
                {
                  html += "Thesis: " + response.thesisST + " <BR> Reason: " + response.reasonST + "<BR> Rule & Example: " + response.ruleST;
               
                }

                html += " <BR> <a href=\"details.php?id=" + response.claimID + "\" class = \"button\">  FLAG THIS CLAIM! </a> </div>";

                // add author as additional column "Source/Name"
                // combine summary/descrption
                // for perception: description, the url, 
                //subject targetp on details page not just popup
                // And now assign this HTML layout in pop-up body
                $("#modal-body").html(html);

                $("#myModal").modal();



            }
        });
    }
</script>

<!-- Modal -->
<div class = "modal fade" id = "myModal" tabindex = "-1" role = "dialog" aria-hidden = "true">
   
   <div class = "modal-dialog">
      <div class = "modal-content">
         
         <div class = "modal-header">
            <h4 class = "modal-title">
               Claims Details
            </h4>

<!--            <button type = "button" class = "close" data-dismiss = "modal" aria-hidden = "true">
               ×
            </button> -->
         </div>
         
         <div id = "modal-body">
            Press ESC button to exit.

            response.claimID
         </div>
         
         <div class = "modal-footer">
           <!-- <button type = "button" class = "btn btn-default" data-dismiss = "modal">
               OK
            </button> -->
         </div>
         
      </div><!-- /.modal-content -->
   </div><!-- /.modal-dialog -->
   
</div><!-- /.modal -->



<?php

} 

 // end of function


//account for null instances in the active/inactive setting and updating algorithm 
// reimpleplement recursion independently for active/inactive
// reexamine root rivals and troubleshoot multiple instances of it

//input by topic and filter through unique urls
//add recursion to activity function
//validate all inputs for injection


//add flagtype 
//add tarka into a comments section or dialogue ability. 
?>





<!-- partial 
  <script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js'></script>
  <script  src="./script.js"></script>-->
 </body>
</html>
<?php mysqli_close($conn); ?>
