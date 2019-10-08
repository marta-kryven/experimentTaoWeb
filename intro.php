<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Instructions</title>
<style>
#ex2_container { text-align:left; font-size:120%;}
#ex1_container { text-align:left; font-size:120%;}
</style>
</head>

<?php

$browser = $_SERVER['HTTP_USER_AGENT'];
$validbrowser = 1;

if ( strpos($browser, "Safari") ) {
	$validbrowser = 2;
}

?>
 
<body onload="loadEventHandler()">
<div id="ex2_container">
<br><br><br>
Welcome to our study!<br><br>
<font color='red'><b>IMPORTANT!</b> This study runs on a desktop/laptop.<br><br> 
The study will <b>NOT </b> run on a mobile device.</font><br><br>

<b>Brief description </b> 
<br><br>In this study you will see videos of people reaching for coloured objects.<br> 
Your task is to guess the object that the person is reaching for. <br>
We are interested in how good people are at guessing intentions of others. <br>
Try to respond as quickly and accurately as you can. <br><br>
The study is expected to take 15 minutes.<br><br>
Thanks for participating!
<br><br>

<p><font size='2'>
Informed Consent <br>
By answering the following questions, you are participating in a study performed by researchers at MIT and UCLA. 
Your participation in this research is voluntary. You may decline to answer any or all of the following questions. You may decline further participation, at any time, without adverse consequences. Your anonymity is assured; the researchers who have requested your participation will not receive
 any personal identifying information about you. By clicking 'I agree' you indicate your consent to participate in this study.
</font></p> <br>

</div>

<div id="ex1_container">
<form name="frm" action="instructions.php" method="post" onsubmit="return validateForm()">
  Your age: <input type="text" name="age"><br>
  Your gender: <input type="text" name="gender"><br>
  <input type="text" name="UID" hidden><input type="text" name="showEndNext" hidden><br>
  <input type="submit" value="I agree"/>
</form>

</div>

<script>

var task;
var browserok = <?php global  $validbrowser; echo  $validbrowser; ?>;
var bs = "<?php global  $browser; echo  $browser; ?>";

function loadEventHandler() {
 
// Uncomment to disable Safari, for example when playing videos
/*
  if (browserok == 2) {
      s  = document.getElementById("ex2_container").innerHTML;
      document.getElementById("ex1_container").innerHTML = "<br><br><font color='red'>UNFORTUNATELY YOUR BROWSER IS NOT SUPPORTED.</font>";
  }
*/
}


function validateForm() {
/*    if (browserok == 2) {
       alert("Unfortunately your browser is not supported.");
       return false;
     }
*/
    var UID = "S";
    UID = UID+Math.floor((Math.random() * 100000000) + 1); 

    // generate subject ID
    document.forms["frm"]["UID"].value = UID;
    var x = document.forms["frm"]["age"].value;
    if (x == null || x == "" || x == 0) {
        alert("Please enter your age.");
        return false;
    }

    x = document.forms["frm"]["gender"].value;
    if (x == null || x == "" || x == 0) {
        alert("Please enter your gender.");
        return false;
    }
    
    document.forms["frm"]["task"].value = task;
    document.forms["frm"]["showEndNext"].value = "false";
    return true;  
}

</script>
</body>
</html>
