<!-- This form is shown when the subject has finished a block of trials, here we can show them how well they are doing so far in terms of percent correct -->

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Thank you!</title>
<style>
#ex2_container { text-align:center; font-size:120%;}
</style>
</head>

<?php
// form parametres
function test_input($data) {
   $data = trim($data);
   $data = stripslashes($data);
   $data = htmlspecialchars($data);
   return $data;
}

$dir = "data";

$ip=$_SERVER['REMOTE_ADDR'];
$date = date('d/F/Y h:i:s'); // date of the visit that will be formated this way: 29/May/2011 2512:20:03
$browser = $_SERVER['HTTP_USER_AGENT'];
$browser = str_replace(' ', '_', $browser);
$validrequest = 0;
$block = 0;
$respReceived = 0;
$receivedX = 0;
$receivedY = 0;
$accuracy = 0;

if ($_SERVER["REQUEST_METHOD"] == "GET") {

        $UID = "dodgyuser";
        $s = $dir . "/" . $UID . ".txt";

        $f = fopen($s, "a") or die("102: Unable to open file!" . $s);
        fwrite($f, $ip . " ". $date . " " . $browser . "Get request to thanks.php\n");
        fclose($f);
        $validrequest = 0;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
 
    $validrequest = 1;
    
    if (!empty($_POST["UID"])) {
      $UID = test_input($_POST["UID"]);
      if ( !strcmp($UID, "dodgyuser") ) {
        $validrequest = 0;
      }
    } else {
      $validrequest = 0;
      $UID = "dodgyuser";
    }

    if (!empty($_POST["trialno"])) {
      $trialno = test_input($_POST["trialno"]);
    }

    if (!empty($_POST["time"])) {
      $time = test_input($_POST["time"]);
    }
    
    if (!empty($_POST["name"])) {
      $stimulus = test_input($_POST["name"]);
    }

    if (!empty($_POST["respReceived"])) {
          $respReceived = test_input($_POST["respReceived"]);
    }

    if (!empty($_POST["correctAnswersSoFar"])) {
      $accuracy = test_input($_POST["correctAnswersSoFar"]);
    }

    if (!empty($_POST["xcoord"])) {
          $receivedX = test_input($_POST["xcoord"]);
    }

    if (!empty($_POST["ycoord"])) {
          $receivedY = test_input($_POST["ycoord"]);
    }

    if (!empty($_POST["block"])) {
          $block = test_input($_POST["block"]);
          echo "Block " .$block . "<br><br>";
    }

    $frame = "NA";
    if (!empty($_POST["frameno"])) {
           $frame = test_input($_POST["frameno"]);
    }

    if ( $validrequest == 0 ) {
       $s = $dir . "/" . $UID . ".txt";
       $f = fopen($s, "a") or die("Unable to open file!");
       fwrite($f, $ip . " ". $date . " " . $browser . " Malformed POST request to questions.php with bad uid\n");
       fclose($f);
    } else {  
       
       $s = $dir . "/" . $UID . ".txt";
       $f = fopen($s, "a") or die("Unable to open file!");

       if (!empty($stimulus)) {

          fwrite($f, $ip . " " . $date . " " . $browser . " " . $UID . " " . $trialno . " " .  $stimulus . " " . $time . " " .
                   $respReceived . " " . $frame . " " . $receivedX . " " . $receivedY  . " " . $block . "\n");

       } else {
          echo "<br> Err: invalid parameters received <br>"; 
       }
       fclose($f);
    }

}


?>

<body onload="loadEventHandler()">
<div id="ex1_container">
Your accuracy so far: XXX&#37; correct <br><br>  
Thanks! You are half-way there. You may take a short break if you wish.
</div>
<div id="ex2_container">
<form name='frm' action='test.php' method='post' onsubmit='return validateForm()'>
<input type='text' name='UID' hidden><br>
<input type='text' name='block' hidden><br>
<input type='text' name='trialno' hidden><br>
<input type='text' name='resumingAfterBreak' hidden><br>
<input type='submit' value='Continue'/>
</form>
</div>

<script>
var uid =  "<?php global $UID; echo $UID; ?>";
var block =  <?php global $block; echo $block; ?>;
var validrequest =  <?php global $validrequest; echo $validrequest; ?>;
var accuracy = <?php global $accuracy; echo $accuracy; ?>;

function loadEventHandler() {
  if (!validrequest) {
     document.getElementById("ex1_container").innerHTML = "";
     document.getElementById("ex2_container").innerHTML = "<h1>Bad Request...</h1>";    
  }

  var accuracy = <?php global $accuracy; echo $accuracy; ?>;
  //accuracy = Math.round( accuracy/16);

  if (block == 2) {
    document.forms["frm"].action = "questions.php";
    document.getElementById("ex1_container").innerHTML = "Your accuracy in the second half: " + accuracy + " out of 16 correct";
  } else {
    document.getElementById("ex1_container").innerHTML = "Thanks! We are half-way there.<br><br>" + 
                                                          "Your accuracy so far: " + accuracy + " out of 16 correct";
    block = block+1;
  }
}

function validateForm() {
      document.forms["frm"]["UID"].value = uid;
      document.forms["frm"]["block"].value = block;
      document.forms["frm"]["trialno"].value = "<?php global $trialno; echo $trialno; ?>";
      document.forms["frm"]["resumingAfterBreak"].value = "true";
      return true;
}

</script>
</body>
</html>
