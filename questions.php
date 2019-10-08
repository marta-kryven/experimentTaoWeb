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
$block = "0";
$respReceived = 0;
$receivedX = 0;
$receivedY = 0;

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

}

?>
 
<body onload="loadEventHandler()">
<div id="ex2_container">

<br>Thanks! We are almost done.<br><br>
Please answer the following question:<br><br>

<form name='frm' action='theend.php' method='post' onsubmit='return validateForm()'>
How did you make your decisions?<br><br>
<textarea name='decision' rows='5' cols='70'></textarea><br><br>
Did everything run smoothly (e.g. no interuptions, connection issues, skipping video frames)? 
<br><br>
<textarea name='technical' rows='5' cols='70'></textarea><br><br>
<br>
<input type='text' name='UID' hidden><br>
<input type='submit' value='Submit'/>
</form>

</div>

<script>
var uid =  "<?php global $UID; echo $UID; ?>";
var validrequest =  <?php global $validrequest; echo $validrequest; ?>;

function loadEventHandler() {
  if (!validrequest) {
     document.getElementById("ex2_container").innerHTML = "<h1>Bad Request...</h1>";    
  }
}


function validateForm() {
      document.forms["frm"]["UID"].value = uid;
      var x = document.forms["frm"]["decision"].value;
      if (x == null || x == "" || x == 0) {
          alert("Please answer the questions to proceed." );
          return false;
      }
    
      var x = document.forms["frm"]["technical"].value;
      if (x == null || x == "" || x == 0) {
          alert("Please answer the questions to proceed." );
          return false;
      }
 
      return true;
}

</script>
</body>
</html>
