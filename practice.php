<!DOCTYPE html>
<html>
<head>
<title>
Experiment</title>
<style>

#immarker {
  position: absolute;
  top: -100px;
  left: -100px;
}

#redmarker {
  position: absolute;
  top: -100px;
  left: -100px;
}

#whitemarker {
  position: absolute;
  top: -100px;
  left: -100px;
}

#bluemarker {
  position: absolute;
  top: -100px;
  left: -100px;
}

#greenmarker {
  position: absolute;
  top: -100px;
  left: -100px;
}

#jamarker {
  position: absolute;
  top: -100px;
  left: -200px;
}

#neinmarker {
  position: absolute;
  top: -100px;
  left: -200px;
}

#main_image{
    width: 100%;
    height: 100%;  
  background: white;
}

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

#message { align:center; text-align: center; font-size:120%}
#ex1_container { position: relative; align:center; text-align: center;}
#ex2_container { position: relative; align:center; text-align: center;}
#debug_messages { position: relative; align:center; text-align: center;}

</style>
</head>
<body onload="loadEventHandler()" >

<?php

// form parametres
function test_input($data) {
   $data = trim($data);
   $data = stripslashes($data);
   $data = htmlspecialchars($data);
   return $data;
}

// defining variables 

$dir = "data";                               // the directory where we will save all subject's responses
$randTrialNo = 0;                            // randomised trial number. trials are shown in a random order
$UID =  "";                                  // subject ID, a unique subject identifier
$ip=$_SERVER['REMOTE_ADDR'];                 // subject IP address. When processing data we check for duplicate IP addresses  
$date = date('d/F/Y h:i:s');                 // request date and time formated this way: 29/May/2011 2512:20:03
$browser = $_SERVER['HTTP_USER_AGENT'];      // browser version
$browser = str_replace(' ', '_', $browser);

$validrequest = 0;                           // is this a valid request? set to 1 if the request is well formed
$trialno = 0;                                // trial number 
$respReceived = 0;                           // did the subject respond to the trial? 1 -- responded, 0 -- timed out
$receivedX = 0;                              // x,y coordinates where the subject has clicked on the image 
$receivedY = 0;
$practice_dir = "webfile/stimuli/practice/"; // location of the stimuli on server disk 
$practice_names = scandir($practice_dir);    // the list of all stimus
$num_practice = count($practice_names) - 2;  // number of stimuli in the practice block

// error checking, making sure you have PHP writing permissions
 
if (!is_writable($dir)) {
    echo 'The directory is not writable ' . $dir . '<br>';
}

// we process only POST requests, and so any GET request is considered invalid

if ($_SERVER["REQUEST_METHOD"] == "GET") {

        $UID = "dodgyuser";
        $s = $dir . "/" . $UID . ".txt";
        $f = fopen($s, "a") or die("Unable to open file!" . $s);
	fwrite($f, $ip . " ". $date . " " . $browser . " GET request to practice.php \n");
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

   if (!empty($_POST["trialno"])) {
     $trialno = test_input($_POST["trialno"]);
   }
  
   if( !empty($_POST["firsttrial"]) ) {
      $trialno = 0;

      $first = test_input($_POST["firsttrial"]);

      // generate a random sequence of trials for this user

      $m = array();
      for ($x = 0; $x < $num_practice; $x++) {
        $m[] = $x;
      }

      // permute randomly
      for ($x = 0; $x < count($m); $x++) {
        $pickone = rand(0, count($m)-1);
        if ($pickone <> $x) { 
    		$temp = $m[$x];
        	$m[$x] = $m[$pickone];
        	$m[$pickone] = $temp;
        }
      } 

      // save to file

      $f = fopen($dir . "/" . $UID . "practice_sequence.txt", "a") or die("Unable to open file!" . $UID . "practice_sequence");
      for ($x = 0; $x < count($m); $x++) {
         fwrite($f, $m[$x] . "\n");
      }
      fclose($f);
      $randomisedTrial = $m[0];

   }
   else {
        $s = $dir . "/" . $UID . ".txt";
        $f = fopen($s, "a") or die("101 Unable to open file!" . $s);

        if (!empty($_POST["respReceived"])) {
          $respReceived = test_input($_POST["respReceived"]);
        }

        if (!empty($_POST["xcoord"])) {
          $receivedX = test_input($_POST["xcoord"]);
        } else {
          //echo " receivedX is empty<br> ";
        }

        if (!empty($_POST["ycoord"])) {
          $receivedY = test_input($_POST["ycoord"]);
        }

        $block = "practice";
        if (!empty($_POST["block"])) {
          $block = test_input($_POST["block"]);
        }

        $frame = "NA";
        if (!empty($_POST["frameno"])) {
           $frame = test_input($_POST["frameno"]);
        }

        $objName = "NA";
        if (!empty($_POST["objectName"])) {
           $objName = test_input($_POST["objectName"]);
        }

        $trialid = test_input($_POST["trialID"]);
        $time = test_input($_POST["time"]);
        $stimulusname = test_input($_POST["name"]);
 
        fwrite($f, $ip . " " . $date . " " . $browser . " " . $UID . " " . $trialno . " " .  $stimulusname . " " . $time . " " . 
                   $respReceived . " " . $frame . " " . $receivedX . " " . $receivedY  . " " . $objName . " " . $block . "\n");
        fclose($f);
        advanceTrial();
   }
}

// -------------------------------------------------------------------------------------------------------
//
//  loading the stimulus data for this trial
//  todo: in the future the files should be hosted on cloudify and loaded from cloudify
//
// -------------------------------------------------------------------------------------------------------

$stimname = $practice_names[$randomisedTrial + 2 ];     // this is the name of the frames file for this trial
$stimulusdir = $practice_dir . $stimname;               // append the relative path to the stimulus name 
$frame_files = file($stimulusdir, FILE_IGNORE_NEW_LINES); //a list of all frame URLs of the stimulus 
$num_frames = count($frame_files);                  // the length of the video in frames


// -------------------------------------------------------------------------------------------------------
//
//  checking is we received a correctly formed POST request
//
// -------------------------------------------------------------------------------------------------------

if ($validrequest == 1) {
  if ($trialno < $num_practice) {
    $txt = $trialno+1 . " of " . $num_practice;
    $txt = "Practice " . $txt;
    echo "<p  align='left'>Let&#39;s practice! Guess the object that the person is reaching for before the video ends.";
  } 

  echo "<h2>$txt</h2>\n";
} else {
  echo "<h2>Err: Invalid Request</h2>\n";
}


// -------------------------------------------------------------------------------------------------------
//
//  Reading the coordinates of the correct response for this trial to track the subject's accuracy
//
// -------------------------------------------------------------------------------------------------------

// read the file with correct responses

$correct_X = 0;
$correct_Y = 0;
$correctObjectName = "";
 
$answers_file = "webfile/answers_practice.csv";
$f = fopen($answers_file, "r") or die("102: Unable to open file! " . $answers_file);

$trialname = substr($stimname,0,-4); // remove .txt from $stimname 

while(!feof($f)) {

  $s = fgets($f); 
  $this_trial_line = explode(" ",$s);

  if ($this_trial_line[0] == $trialname) {
    
     //echo "found " . $s . "<br>"; 
     $this_trial_line = explode(" ",$s);
     $correct_X = intval($this_trial_line[1]);
     $correct_Y = intval($this_trial_line[2]);
     $correctObjectName = $this_trial_line[3];
     break; 
  } 
}

if ($correct_X == 0 && $correct_Y == 0) {
  echo "Could not read correct position for trial " . $trialname . " Is it in " . $answers_file . "?<br>";
} else {
  // making sure the right thing is read
  // echo "Correct object " . $correct_X . "," . $correct_Y . "," . $correctObjectName . "<br>";
}

fclose($f);


// -------------------------------------------------------------------------------------------------------
//
//  Reading tooltip coordinates 
//
// -------------------------------------------------------------------------------------------------------

$redarray_X = array();
$redarray_Y = array();
$bluearray_X = array();
$bluearray_Y = array();
$greenarray_X = array();
$greenarray_Y = array();
$whitearray_X = array();
$whitearray_Y = array();

$f = fopen("webfile/object_positions.csv", "r") or die("102: Unable to open file! " . $answers_file);

while(!feof($f)) {

  $s = fgets($f);
  $this_line = explode(" ",$s);

  if (trim($this_line[3]) == "R") {
     $redarray_X[] = intval($this_line[1]);
     $redarray_Y[] = intval($this_line[2]);
  }

  if (trim($this_line[3]) == "B") {
     $bluearray_X[] = intval($this_line[1]);
     $bluearray_Y[] = intval($this_line[2]);
  }

  if (trim($this_line[3]) == "W") {
     $whitearray_X[] = intval($this_line[1]);
     $whitearray_Y[] = intval($this_line[2]);
  }
  
  if (trim($this_line[3]) == "G") {
     $greenarray_X[] = intval($this_line[1]);
     $greenarray_Y[] = intval($this_line[2]);
  }
}

fclose($f);


// -------------------------------------------------------------------------------------------------------
//
// PHP functions 
//
// -------------------------------------------------------------------------------------------------------

// get the index of the next trial's stimulus, we need to do this because the order of stimuli is randomised
 
function advanceTrial() {
    global $trialno, $randomisedTrial, $UID, $dir;
    $trialno = $trialno + 1;
    $randomisedTrial = $trialno;

    $s = $dir . "/" . $UID . "practice_sequence.txt";
    $f = fopen($s, "r") or die("102: Unable to open file! " . $s);
    for ($x = 0; $x <= $trialno; $x++) {
           $randomisedTrial = intval(fgets($f));
    }
    fclose($f);
}

?>

<script>


var trialno = <?php global $trialno; echo $trialno; ?>;
var rmn = <?php global $randomisedTrial; echo $randomisedTrial; ?>;
var savedtime = "";
var num_practice = <?php global $num_practice;  echo $num_practice; ?>;
var pathToStimulus = "<?php global $stimulusdir;  echo $stimulusdir; ?>";
var framearr = <?php global $frame_files; echo json_encode($frame_files); ?>;
var frame_index = 0;
var num_frames = <?php global $num_frames; echo json_encode($num_frames); ?>;
var frameTiming = 120;    // time im milliseconds between frames


// here we read all frames into an image array
var imgArray = new Array();
var loadCount = 0;

var respReceived = 0; // set to 1 if responce received (the subject clicked on an object)

var correctX = <?php global $correct_X; echo $correct_X; ?>;
var correctY = <?php global $correct_Y; echo $correct_Y; ?>;
var resp_X = 0; // todo: coordinates of the mouse click on the image
var resp_Y = 0;

// positions of coloured pegs on the image
var redarray_X = <?php global $redarray_X; echo json_encode($redarray_X); ?>;
var redarray_Y = <?php global $redarray_Y; echo json_encode($redarray_Y); ?>;
var bluearray_X = <?php global $bluearray_X; echo json_encode($bluearray_X); ?>;
var bluearray_Y = <?php global $bluearray_Y; echo json_encode($bluearray_Y); ?>;
var greenarray_X = <?php global $greenarray_X; echo json_encode($greenarray_X); ?>;
var greenarray_Y = <?php global $greenarray_Y; echo json_encode($greenarray_Y); ?>;
var whitearray_X = <?php global $whitearray_X; echo json_encode($whitearray_X); ?>;
var whitearray_Y = <?php global $whitearray_Y; echo json_encode($whitearray_Y); ?>;

var videoStarted = 0;  // is the video started? do not allow clicking if the video has not been played
var spacePressed = 0;  // has SPACE been pressed? if yes, move to the selection mode

var acceptableRadius = 10;  // how close in pixels they have to be to the target object to score correctly? 
var ObjName = "";
var clearFeedback = 0;

function distance(x1, y1, x2, y2) {
  var dist = (x1-x2)*(x1-x2) + (y1-y2)*(y1-y2);
  return Math.sqrt(dist);
}


function imagemousemoved ( event ) {
     
    if (spacePressed == 1) {
       var size = acceptableRadius*2;
       var offx = document.getElementById("ex1_container").offsetLeft;
       var offy = document.getElementById("ex1_container").offsetTop;
       var image_offx = document.getElementById("frameimg").offsetLeft;
       var image_offy = document.getElementById("frameimg").offsetTop;
       var cursor_X = event.clientX - offx - image_offx;
       var cursor_Y = event.clientY - offy - image_offy;
       
       // debugging - where does the webpage think the cursor is?
       // document.getElementById("debug_messages1").innerHTML = "(" + cursor_X + "," + cursor_Y + ")";

       var idx = findblue(cursor_X, cursor_Y);
       if (idx != -1) {
           var x = bluearray_X[i] + image_offx  - size/2;
           var y = bluearray_Y[i] + image_offy  - size/2;
           document.getElementById("bluemarker").style.left = x  + "px";
           document.getElementById("bluemarker").style.top = y + "px";
           return;
       } else {
           document.getElementById("bluemarker").style.left = "-100px";
           document.getElementById("bluemarker").style.top = "-100px";
       }

       // same thing for the damn lousy green pegs

       idx = findgreen(cursor_X, cursor_Y);
       if (idx != -1) {
          var x = greenarray_X[i] + image_offx  - size/2;
          var y = greenarray_Y[i] + image_offy  - size/2;
          document.getElementById("greenmarker").style.left = x  + "px";
          document.getElementById("greenmarker").style.top = y + "px";
          return;
       } else {
         document.getElementById("greenmarker").style.left = "-100px";
         document.getElementById("greenmarker").style.top = "-100px";
       }

       // same thing for the cretino-idiotic white pegs
       idx = findwhite(cursor_X, cursor_Y);
       if (idx != -1) {
          var x = whitearray_X[i] + image_offx  - size/2;
          var y = whitearray_Y[i] + image_offy  - size/2;
          document.getElementById("whitemarker").style.left = x  + "px";
          document.getElementById("whitemarker").style.top = y + "px";
          return;
       } else {
          document.getElementById("whitemarker").style.left = "-100px";
          document.getElementById("whitemarker").style.top = "-100px";
       }
 
       // same thing for the shitty red pegs
       idx = findred(cursor_X, cursor_Y);
       if (idx != -1) {
          var x = redarray_X[i] + image_offx  - size/2;
          var y = redarray_Y[i] + image_offy  - size/2;
          document.getElementById("redmarker").style.left = x  + "px";
          document.getElementById("redmarker").style.top = y + "px";
          return;
       } else {
          document.getElementById("redmarker").style.left = "-100px";
          document.getElementById("redmarker").style.top = "-100px";
       } 
    }
}


// ---------------------------------------------------------------------------------------
//
//   does the cursor position match one of the stupid blue pegs?
//   return the index of the peg it matches or -1 if no stupid blue peg found
//
// ---------------------------------------------------------------------------------------

function findblue(cursor_X, cursor_Y) {

     var idx = -1;

     for ( i = 0; i < bluearray_X.length; i++ ) {
          var d = distance(cursor_X, cursor_Y, bluearray_X[i], bluearray_Y[i]);
          if (d < acceptableRadius ) {
             idx = i;
             break;
          }
      }

      return idx;
}

function findwhite(cursor_X, cursor_Y) {

     var idx = -1;

     for ( i = 0; i < whitearray_X.length; i++ ) {
          var d = distance(cursor_X, cursor_Y, whitearray_X[i], whitearray_Y[i]);
          if (d < acceptableRadius ) {
             idx = i;
             break;
          }
      }

      return idx;
}

     
function findgreen(cursor_X, cursor_Y) {

     var idx = -1;

     for ( i = 0; i < greenarray_X.length; i++ ) {
          var d = distance(cursor_X, cursor_Y, greenarray_X[i], greenarray_Y[i]);
          if (d < acceptableRadius ) {
             idx = i;
             break;
          }
      }

      return idx;
}

function findred(cursor_X, cursor_Y) {

     var idx = -1;

     for ( i = 0; i < redarray_X.length; i++ ) {
          var d = distance(cursor_X, cursor_Y, redarray_X[i], redarray_Y[i]);
          if (d < acceptableRadius ) {
             idx = i;
             break;
          }
      }

      return idx;
}

function imageclicked(event) {

 //document.getElementById("debug_messages").innerHTML = document.getElementById("debug_messages").innerHTML + "clicked!<br>";

 if (videoStarted == 0) {
   alert("Please start the video before responding.");
   return;
 }

 if (! respReceived && frame_index < num_frames - 1) {
 
     var image_offx = document.getElementById("frameimg").offsetLeft;
     var image_offy = document.getElementById("frameimg").offsetTop;
     var offx = document.getElementById("ex1_container").offsetLeft;
     var offy = document.getElementById("ex1_container").offsetTop;

     resp_X = event.clientX - offx - image_offx;
     resp_Y = event.clientY - offy - image_offy;

     var size = 18; // the size of the marker circle

     var markerx = event.clientX-offx - size/2;
     var markery = event.clientY-offy - size/2;

     // position the marker over where the subject has clicked
     document.getElementById("immarker").style.left = markerx + "px";
     document.getElementById("immarker").style.top = markery + "px";

     // do we count this as clicking on one of the stupid pegs?
     var idx = findblue(resp_X, resp_Y);
     if (idx == -1) {
       idx = findred(resp_X, resp_Y);
       if (idx == -1) {
          idx = findwhite(resp_X, resp_Y);
          if (idx == -1) {
             idx = findgreen(resp_X, resp_Y);
             if (idx != -1) ObjName = "G"; 
          } else {
             ObjName = "W";
          }
       } else {
          ObjName = "R";
       }
     } else {
       ObjName = "B";
     }

     if (idx != -1) {

        var objIdx = idx + 1;
        ObjName = objIdx + ObjName; 
        
        respReceived = 1; 
        var d = distance(resp_X, resp_Y, correctX, correctY); 
      
        markerx= 523/2 - 126/2 + image_offx;
        markery= 364/2 - 109/2 + image_offy;
        clearFeedback = 0;

        if ( d < acceptableRadius ) {
          document.getElementById("message").innerHTML = "Correct!"
          document.getElementById("jamarker").style.left = markerx + "px";
          document.getElementById("jamarker").style.top = markery + "px";
        } else {
          document.getElementById("message").innerHTML = "Wrong!"
          document.getElementById("neinmarker").style.left = markerx + "px";
          document.getElementById("neinmarker").style.top = markery + "px";   
        }

        spacePressed = 0;
        setTimeout(nextFrame, frameTiming);
    } else {
        alert("Please press SPACE to pause the video, then click on one of the objects" );
         document.getElementById("immarker").style.left = "-200px";
        document.getElementById("immarker").style.top =  "-200px";
    } 
  }
   
}


function loadEventHandler() {
   //alert("loadform");

   if (trialno < num_practice-1 ) {
     document.forms["frm"].action = "practice.php";
   } else {
     document.forms["frm"].action = "quiz.php";
   }
 
   var now= new Date(),
   h= now.getHours(),
   m= now.getMinutes(),
   s= now.getSeconds();
   ms = now.getMilliseconds();

   var times = "t(" + h + "," + m + "," + s + "," + ms + ");";
   savedtime += times;

   var im = document.getElementById("frameimg");
   //im.src = framearr[frame_index]; 

   document.addEventListener('keydown', function(event) {
    if (event.keyCode == 32) {
        if (videoStarted == 0) {
          alert("You have to start the video to be able to pause it.");
          return;
        }

        if (respReceived == 0) {
          if (spacePressed == 0) {
            spacePressed = 1;
            document.getElementById("message").innerHTML = "Paused. Please select the intended object or perss SPACE to resume.";
          } else {
            spacePressed = 0;
            document.getElementById("message").innerHTML = "Playing. Press SPACE as soon as you guess the intended object.";
            setTimeout(nextFrame, frameTiming);
         }
        }
    } 
   }, true);


   document.getElementById("message").innerHTML = "Loading..";
   for (i=0; i< framearr.length; i++ ) {
     imgArray[i] = new Image();
     imgArray[i].src = framearr[i];
     imgArray[i].onload = notifyimLoaded;
   }
   document.getElementById("message").innerHTML = "";
   im.src = imgArray[0].src;
}

function notifyimLoaded() {
   if (loadCount < num_frames - 1) {
     document.getElementById("message").innerHTML = "Loading... " + loadCount;
     loadCount = loadCount+1;
   } else {
     document.getElementById("message").innerHTML = "Press <b>Start Video</b> to play the video.";
     document.getElementById("start").disabled = false;
   }
}


function startclicked() {
  videoStarted = 1;
  setTimeout(nextFrame, frameTiming);
  document.getElementById("start").disabled = true;
  document.getElementById("message").innerHTML = "Playing. Press SPACE as soon as you guess the intended object."; 
}


function nextFrame() {

 if (frame_index >= num_frames - 1) {
   if (!respReceived) {
       alert("Too late! Please try to select the goal object before the video ends.");
   }
   document.getElementById("sub").disabled = false;
   submitForm();
   document.forms["frm"].submit();   
 }

 if ( frame_index < num_frames - 1 && spacePressed != 1) {
    frame_index  = frame_index + 1;
    var nextframe = framearr[frame_index];
    var im = document.getElementById("frameimg");
    im.src = nextframe;
    setTimeout(nextFrame, frameTiming);

 }

 if (clearFeedback == 3) {
   document.getElementById("jamarker").style.left = "-200px";
   document.getElementById("jamarker").style.top = "-100px";
   document.getElementById("neinmarker").style.left = "-200px";
   document.getElementById("neinmarker").style.top = "-100px";
 }
 
 clearFeedback = clearFeedback + 1;
}

function submitForm() {
       var now= new Date(),
       h= now.getHours(),
       m= now.getMinutes(),
       s= now.getSeconds();
       ms = now.getMilliseconds();

       times = "t(" + h + "," + m + "," + s + "," + ms + ");";
       savedtime += times;

       document.forms["frm"]["UID"].value = "<?php global  $UID; echo  $UID; ?>";
       document.forms["frm"]["trialno"].value = "<?php global $trialno; echo $trialno; ?>";
       document.forms["frm"]["trialID"].value = "<?php global $randomisedTrial; echo $randomisedTrial; ?>";
       document.forms["frm"]["time"].value = savedtime;
       document.forms["frm"]["name"].value = "<?php global $stimname;  echo  $stimname; ?>"; 
       document.forms["frm"]["block"].value = "practice";
       document.forms["frm"]["xcoord"].value = resp_X;
       document.forms["frm"]["ycoord"].value = resp_Y;
       document.forms["frm"]["frameno"].value = frame_index;
       document.forms["frm"]["respReceived"].value = respReceived;
       document.forms["frm"]["objectName"].value = ObjName;
       return true;
}

</script>

<div id="message">
Loading...
</div>
<br>
<div id="ex1_container">
<div id='main_image'><img src='' width=523px; height=364px; onclick='imageclicked(event)' onmousemove='imagemousemoved(event)' id = 'frameimg' /></div>
<div id='redmarker'><img src='webfile/red_marker.png' width = '20px' onclick='imageclicked(event)'/></div>
<div id='bluemarker'><img src='webfile/blue_marker.png' width = '20px' onclick='imageclicked(event)'/></div>
<div id='greenmarker'><img src='webfile/green_marker.png' width = '20px' onclick='imageclicked(event)'/></div>
<div id='whitemarker'><img src='webfile/white_marker.png' width = '20px' onclick='imageclicked(event)'/></div>
<div id='immarker'><img src='webfile/grey_marker.png' width = '18px' /></div>
<div id='jamarker'><img src='webfile/ja.png'/></div>
<div id='neinmarker'><img src='webfile/nein.png'/></div>
</div>

<div id="ex2_container">
<br>
<button id='start' onclick='startclicked()' disabled>Start Video </button>
<br>
<form name='frm' action='' method='post' onsubmit='return submitForm()'>
<fieldset style='border:0'>
<input type='text' name='trialno' hidden>
<input type='text' name='UID' hidden>
<input type='text' name='name' hidden>
<input type='text' name='block' hidden>
<input type='text' name='xcoord' hidden>
<input type='text' name='ycoord' hidden>
<input type='text' name='respReceived' hidden>
<input type='text' name='time' hidden>
<input type='text' name='trialID' hidden>
<input type='text' name='frameno' hidden>
<input type='text' name='objectName' hidden>
<input type='submit' id = 'sub' value='Next Video' hidden/>
</fieldset>
</form>
</div>

<div id="debug_messages1">
</div>
<div id="debug_messages">
</div>

</body>
</html> 
