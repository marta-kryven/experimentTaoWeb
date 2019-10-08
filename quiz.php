<!DOCTYPE html>
<html>
<title>
Instructions quiz</title>
<style>
h2{
    text-align: center;
}
#ex1_container { align:center; text-align: center;}
</style>
<body>

<?php

// form parametres
function test_input($data) {
   $data = trim($data);
   $data = stripslashes($data);
   $data = htmlspecialchars($data);
   return $data;
}

// define variables and set to empty values
$dir = "data";
$ip=$_SERVER['REMOTE_ADDR'];
$date = date('d/F/Y h:i:s'); // date of the visit that will be formated this way: 29/May/2011 2512:20:03
$browser = $_SERVER['HTTP_USER_AGENT'];
$browser = str_replace(' ', '_', $browser);
$validrequest = 0;
$respReceived = 0;
$receivedX = 0;
$receivedY = 0;

if ($_SERVER["REQUEST_METHOD"] == "GET") {

     $s = $dir . "/" . $UID . ".txt";        
     $f = fopen($s, "a") or die("Unable to open file!" . $s);
     fwrite($f, $ip . "\t". $date . "\t" . $browser . "\tQUIZGETREQUEST\n");
     fclose($f);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
   $UID = test_input($_POST["UID"]);

   if(empty($UID)) {
        $UID = "dodgyuser";
   } else {
        if (!is_writable($dir)) {
          echo 'The directory is not writable ' . $dir . '<br>';
        }
   }

   if (!empty($_POST["respReceived"])) {
          $respReceived = test_input($_POST["respReceived"]);
   }

   if (!empty($_POST["xcoord"])) {
          $receivedX = test_input($_POST["xcoord"]);
   }

   if (!empty($_POST["ycoord"])) {
          $receivedY = test_input($_POST["ycoord"]);
   }

   $block = "practice";
   if (!empty($_POST["block"])) {
          $block = test_input($_POST["block"]);
   }

   $trialno = test_input($_POST["trialno"]);
   $trialid = test_input($_POST["trialID"]);
   $time = test_input($_POST["time"]);
   $stimulusname = test_input($_POST["name"]);
   $respReceived = test_input($_POST["respReceived"]);

   $frame = "NA";
   if (!empty($_POST["frameno"])) {
           $frame = test_input($_POST["frameno"]);
   }

   $s = $dir . "/" . $UID . ".txt";
   $f = fopen($s, "a") or die("101 Unable to open file!" . $s);

   fwrite($f, $ip . " " . $date . " " . $browser . " " . $UID . " " . $trialno . " " .  $stimulusname . " " . $time . " " .
                   $respReceived . " " .  $frame . " " . $receivedX . " " . $receivedY  . " " . $block . "\n");


   fclose($f);

}

?>

<div id="ex1_container">

<p  align='center'> 
Please answer the questions to proceed with the experiment. <br>
</p>


<p  align='center'> 
<font color='red' size=4>Question 1: My task is to  .. </font>
<form name="frm" style='border:0' action="finished_practice.php" method="post" onsubmit="return validateForm()">
        <fieldset style='border:0'>
            <input style='border:0' type='radio' id = 'id2' name='objective' value='2' /> respond as quickly as possible regardless of accuracy.</><br>
            <input style='border:0' type='radio' id = 'id3' name='objective' value='3' /> correctly identify the goal object in each video.</><br>
            <input style='border:0' type='radio' id = 'id1' name='objective' value='1' /> guess which object the person is going to pick up before they touch it.</><br>
            <input style='border:0' type='radio' id = 'id4' name='objective' value='4' /> click on a random object as fast as I can.</><br><br>
        </fieldset>
            <br>
            <br><font color='red' size=4>Question 2: The person in a video is ...</font><br>
        <br> 
        <fieldset style='border:0'>
            <input style='border:0' type='radio' id = 'b1' name='bonus' value='1' /> trying to trick me.</><br>
            <input style='border:0' type='radio' id = 'b2' name='bonus' value='2' /> reaching for coloured objects.</><br> 
            <input style='border:0' type='radio' id = 'b3' name='bonus' value='3' /> doing gymnastics.</><br>
            <input style='border:0' type='radio' id = 'b4' name='bonus' value='4' /> neither of the above.</>
        </fieldset> 
        <br>
        <br>
        <fieldset style='border:0'> 
            <input type='text' name='UID' hidden>
            <input type='text' name='quizAnswer' hidden>
            <input type="submit" value="Submit" onclick='submitQuizClicked()'/>
        </fieldset>
</form>
 </p>

</div>
</body>

<script>

var validInput=0;

function submitQuizClicked() {

    var x = null;
    if (document.getElementById('id1').checked) {
      x="1";
    } else if (document.getElementById('id2').checked) {
      x="2";
    } else if (document.getElementById('id3').checked) {
      x="3";
    } else if (document.getElementById('id4').checked) {
      x="4";
    }

    var b = null;

    if (document.getElementById('b1').checked) {
      b="1";
    } else if (document.getElementById('b2').checked) {
      b="2";  
    } if (document.getElementById('b3').checked) {
      b="3"; 
    } else if (document.getElementById('b4').checked) {
      b="4";
    }

 
    if (x == null || x == "" || b ==null ) {
        alert("Please answer all questions.");
        validInput = 0;
        return false;
    } else {
        validInput = 1;
        var u_id = "<?php global $UID;  echo $UID ?>";
 
        if (x!="1" || b!="2" ) {
           alert("Incorrect, please read the instructions carefully.");
           document.forms["frm"].action="instructions.php";
        } else {
           document.forms["frm"].action="finished_practice.php";
        }

        document.forms["frm"]["quizAnswer"].value = "Q1:" + x + "_Q2:" + b;
        document.forms["frm"]["UID"].value = u_id;
        return true;
    }

return true;
}


function validateForm() {
    return (validInput==1);
}

</script>
</html>
