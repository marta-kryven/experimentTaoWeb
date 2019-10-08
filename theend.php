<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Thank you!</title>
<style>
#ex2_container { text-align:left; font-size:120%;}
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

// set this to one when comment submitted
$dir = "data";

$ip=$_SERVER['REMOTE_ADDR'];
$date = date('d/F/Y h:i:s'); // date of the visit that will be formated this way: 29/May/2011 2512:20:03
$browser = $_SERVER['HTTP_USER_AGENT'];
$browser = str_replace(' ', '_', $browser);
$validrequest = 0;

if ($_SERVER["REQUEST_METHOD"] == "GET") {

        $UID = "dodgyuser";
        $s = $dir . "/" . $UID . ".txt";

        $f = fopen($s, "a") or die("102: Unable to open file!" . $s);
        fwrite($f, $ip . " ". $date . " " . $browser . "Get request to generalquestions.php\n");
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


    if ( $validrequest == 0 ) {
       $s = $dir . "/" . $UID . ".txt";
       $f = fopen($s, "a") or die("Unable to open file!");
       fwrite($f, $ip . " ". $date . " " . $browser . " Malformed POST request to theend.php with bad uid\n");
       fclose($f);
    } else {  
       
       $s = $dir . "/" . $UID . ".txt";
       $f = fopen($s, "a") or die("Unable to open file!");
          
       $decision = test_input($_POST["decision"]);
       $technical = test_input($_POST["technical"]);

       if (!empty($decision) ) {
            fwrite($f,  $ip . " ". $date . " " . $browser . " " . $UID . " technicalproblems " . $technical . "\n");
            fwrite($f,  $ip . " ". $date . " " . $browser . " " . $UID . " decision " . $decision . "\n");
       } else {
            fwrite($f,  $ip . " ". $date . " " . $browser . " " . $UID . " decision n/a" . "\n");
       }
 
       fclose($f);
     }
  }

?>
 
<body onload="loadEventHandler()">
<div id="ex2_container">
</div>
<p><small>  Thank you for participating in our experiment! <br>
The experiment is now over, but if you are interested in more information please read on.<br>
 This experiment studies how people infer intentions and goals of other humans. <br>
We will analyze your responses and decision times to come up with theoretical models that predict when goal inference is easy and when it is hard.<br>
<br> If you are interested in more information about of this study please contact XXX.</small></p>

<script>
var uid =  "<?php global $UID; echo $UID; ?>";

function loadEventHandler() {
var body = "<p><font size = '4'>Please paste the following code into the original HIT before submitting it:<br>" +  uid + "</font></p>";   
  document.getElementById("ex2_container").innerHTML = body; 
}

</script>
</body>
</html>
