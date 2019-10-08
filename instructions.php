<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Instructions</title>
<style>
#ex2_container { text-align:left; font-size:120%;}
</style>
</head>
 
<body>

<?php

// form parametres
function test_input($data) {
   $data = trim($data);
   $data = stripslashes($data);
   $data = htmlspecialchars($data);
   return $data;
}

$dir = "data";
$age = $gender = $UID =  "";
$ip=$_SERVER['REMOTE_ADDR'];
$date = date('d/F/Y h:i:s'); 
$browser = $_SERVER['HTTP_USER_AGENT'];
$browser = str_replace(' ', '_', $browser);
$validrequest = 0;

if (!is_writable($dir)) {
    echo 'The directory is not writable ' . $dir . '<br>';
}

if ($_SERVER["REQUEST_METHOD"] == "GET") {

        $UID = "dodgyuser";
        $s = $dir . "/" . $UID . ".txt";
        $f = fopen($s, "a") or die("Unable to open file!" . $s);
        fwrite($f, $ip . " ". $date . " " . $browser . "Err: GET request \n");
        fclose($f);
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

   if (!empty($_POST["gender"]) && !empty($_POST["age"])) {
      $gender = test_input($_POST["gender"]);
      $age = test_input($_POST["age"]);

      $txt = $ip . " " . $date . " " . $browser . " Age: " . $age . "\tGender: " . $gender .  "\n";
      fwrite($f, $txt);
   }

   if (!empty($_POST["quizAnswer"])) {
          $quiz = test_input($_POST["quizAnswer"]);
          fwrite($f, $ip . " " . $date . " " . $browser . " " . $UID . " quiz:" . $quiz . "\n");
   }

   fclose($f);
}

?>

<div id="ex2_container">
<br><b>INSTRUCTIONS (PLEASE READ CAREFULLY)</b><br><br>
You will see a video of a person reaching for one of the coloured objects <b>played in slow motion</b>.<br><br> A still frame looks like this:<br><br>
<img src="webfile/7233852318_crop.png"/><br><br>
Press SPACE to pause the video <b>as soon as you can guess</b> which object the person intends to pick up.<br><br>
After you pause the video <b>select the intended object by clicking on it.</b><br><br> 
If you pressed SPACE by mistake press SPACE again to resume the video.<br><br>
<b>We will start with 6 practice videos</b>.<br><br>
During practice you will see the remainder of the video to see if you picked the correct object. <br><br>
It is ok to make mistakes during practice, but during the experiment you have to be correct on at least 50&#37; of the trials.<br><br>
<form name="frm" action="practice.php" method="post" onsubmit="return validateForm()">
  <input type="text" name="UID" hidden>
  <input type="text" name="firsttrial" hidden>
  <input type="submit" value="Let&#39;s practice!"/>
</form>

</div>
</body>

<script>
var quiz = "<?php global  $quiz; echo  $quiz; ?>";

function validateForm() {
    var u_id = "<?php global  $UID; echo  $UID; ?>";
    document.forms["frm"]["UID"].value = u_id;
    document.forms["frm"]["firsttrial"].value = "true";
    return true;  
}

</script>
</html>
