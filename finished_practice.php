<!DOCTYPE html>
<html>
<head>
<title>
Experiment</title>
<style>
table, tr, th, td {
    border:1px solid black;
    border-collapse: collapse;
    width:absolute;
    height:absolute;
}
h1 {
    text-align: center;
}

h2{
    text-align: center;
}

#ex1_container { font-size:120%; align:center; text-align: center;}

</style>
</head>
<body onload="loadEventHandler()">

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

if (!is_writable($dir)) {
    echo 'The directory is not writable ' . $dir . '<br>';
}

if ($_SERVER["REQUEST_METHOD"] == "GET") {

        $UID = "dodgyuser";
        $s = $dir . "/" . $UID . ".txt";
        $f = fopen($s, "a") or die("Unable to open file!" . $s);
	fwrite($f, $ip . " ". $date . " " . $browser . " GET request to finished_practice.php \n");
        fclose($f);
        echo "Err: Get request received.<br>";
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

   $s = $dir . "/" . $UID . ".txt";
   $f = fopen($s, "a") or die("101 Unable to open file!" . $s);

   /*
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

   fwrite($f, $ip . " " . $date . " " . $browser . " " . $UID . " " . $trialno . " " .  $stimulusname . " " . $time . " " .
                   $respReceived . " " .  $frame . " " . $receivedX . " " . $receivedY  . " " . $block . "\n");
        

   */

   if (!empty($_POST["quizAnswer"])) {
          $quiz = test_input($_POST["quizAnswer"]);
          fwrite($f, $ip . " " . $date . " " . $browser . " " . $UID . " quiz:" . $quiz . "\n");
   }

   fclose($f); 
}

if ($validrequest == 0) {
  echo "<h2>Err: Invalid Request</h2>\n";
}


?>

<script>

var u_id = "<?php global $UID; echo $UID; ?>";


function loadEventHandler() {
}


function submitForm() {
       document.forms["frm"]["block"].value = "1";
       document.forms["frm"]["UID"].value = u_id;
       document.forms["frm"]["firsttrial"].value = "true";
       return true;
}

</script>

<div id="ex1_container">
<p  align='left'>Congratulations! You have finished the practice. <br><br>
From now on you <b>won&#39;t see the correct response</b>, that is you <b>won&#39;t see which object the person ultimately reaches for</b>. <br><br> 
In the experiment you will see 2 blocks of videos, lasting 8 minutes each.  <br><br> 
<b>You will see feedback on your accuracy between the blocks.</b> <br><br>
Please try to respond as quickly and accurately as you can. <br><br>  
<br><form name='frm' action='test.php' method='post' onsubmit='return submitForm()'>
<fieldset style='border:0'>
<input type='text' name='firsttrial' hidden>
<input type='text' name='block' hidden>
<input type='text' name='UID' hidden>
<input type='submit' id = 'sub' value='Next'/>
</fieldset>
</form>
</div>

</body>
</html> 
