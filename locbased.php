<?php

require_once 'GoogleCloudPrint.php';

session_start();

$target_dir = "uploads/";
$target_file = $target_dir . basename($_FILES["userfile"]["name"]);
$uploadOk = 1;
// Check if image file is a actual image or fake image
if(isset($_POST["upload"])) {

echo "entered if statement";

// Check if file already exists
if (file_exists($target_file)) {
    //echo "Sorry, file already exists.";
    $uploadOk = 0;
}
// Check file size
if ($_FILES["userfile"]["size"] > 500000) {
    //echo "Sorry, your file is too large.";
    $uploadOk = 0;
}
// Allow certain file formats
/*if($imageFileType != "pdf" ) {
    echo "Sorry, only PDF files are allowed.";
    $uploadOk = 0;
}*/

// Check if $uploadOk is set to 0 by an error
if ($uploadOk == 0) {
    //echo "Sorry, your file was not uploaded.";
// if everything is ok, try to upload file
} else {
    if (move_uploaded_file($_FILES["userfile"]["tmp_name"], $target_file)) {
        echo "The file ". basename( $_FILES["userfile"]["name"]). " has been uploaded.";
    } else {
        echo "Sorry, there was an error uploading your file.";
    }
}
//filename variable
$filenametodelete = "uploads/".basename($_FILES["userfile"]["name"]);

// Create object
$gcp = new GoogleCloudPrint();
$totalpages = $gcp->getPDFPages($filenametodelete);
echo $totalpages;
//echo "<br/>";
$gcp->setAuthToken($_SESSION['accessToken']);
echo $_SESSION['accessToken'];
//$id = $_POST['printerid'];
$nearuserprinter = $_POST['nearuserprinter'];
//echo $nearuserprinter."is near";
/*$array = array(
		array(0,1,2,5,6),
		array(5,3,1,6,7),
		array(5,3,1,3,1),
		array(5,8,5,0,1),
		array(5,7,3,3,0),
);*/
$array = array(
		array(0,4,9,6),
		array(4,0,3,11),
		array(9,3,0,7),
		array(6,11,7,0),
);

$printernames = array(
	"MTech Lab Printer 1","MTech Lab Printer 2","CSE Lab 1 Printer 1","CSE Lab 1 Printer 2",
	);

$arraymin = array(

);

include("connection.php");
$rollid = $_SESSION['google_data']['id'];
$selectquery = "SELECT `jbshort`, `loadunbal` FROM `values`";

$selectqueryresult = mysql_query($selectquery);
$selectqueryrow = mysql_fetch_assoc($selectqueryresult);

$jbshort = $selectqueryrow['jbshort'];
$loadunbal = $selectqueryrow['loadunbal'];
echo $jbshort;
echo $loadunbal;
if ($totalpages <= $loadunbal) {
	# code...
	//calculate optimum printer
	//add current print job + all printer current queue + distance between printers
	$currentprintjob  = $totalpages;
	$currentpageloadquery = "SELECT * FROM `printers`";
	$currentpageloadqueryresult = mysql_query($currentpageloadquery);
	$i = 0;
//	echo $i;
	while ($currentpageloadqueryrow = mysql_fetch_array($currentpageloadqueryresult)) {
		$arraymin[$i][0] = $currentprintjob + $currentpageloadqueryrow['pages'] + $array[$i][$nearuserprinter];
		$arraymin[$i][1] = $currentpageloadqueryrow['printerid'];
		echo $arraymin[$i][0]." is total load job for". $i;
		echo "<br/>";
		$i++;
	}
	
	$minprinter = $arraymin[0][0];
	$minindex = 0;
	for ($i=0; $i < 1 ; $i++) { 
		for ($j=0; $j< 1; $j++) { 
			if ($arraymin[$i][$j] < $minprinter) {
				$minprinter = $arraymin[$i][$j];
				$minindex = $i;
			}
			echo $arraymin[$i][$j]." ";

		}
//		echo "<br/>";
	}
	echo $arraymin[$minindex][0]." is pages";
	echo "<br/>";
	echo $arraymin[$minindex][1]." is printerd id";
	echo "<br/>";
	$printerid = $arraymin[$minindex][1];
	echo $filenametodelete;
	$resarray = $gcp->sendPrintToPrinter($printerid, basename($_FILES["userfile"]["name"]), "./uploads/".basename($_FILES["userfile"]["name"]), "application/pdf");
	
	if($resarray['status']==true) {
		
		echo "Document has been sent to printer and should print shortly.";
		$jobid = $resarray['id'];
		echo $jobid;	
		$query = "INSERT INTO `jobs`(`rollno`, `printerid`, `jobid`, `pages`) VALUES ('$rollid','$printerid','$jobid','$totalpages')";
		$queryjo = "INSERT INTO `jobstatus`(`rollno`, `jobid`, `pages`,`docname`) VALUES ('$rollid','$jobid','$totalpages','$filenametodelete')";
			if(mysql_query($query)){
				echo "Success storing jobid and pages into jobs";
			}
			if (mysql_query($queryjo)) {
				echo "Success with jobstatus";
			}
		$updatequery = "UPDATE printers SET pages = pages + '$totalpages' where printerid = '$printerid'";
		if (mysql_query($updatequery)) {
			# code...
			echo "pages updated";
		}
	}
	else {
		echo "An error occured while printing the doc. Error code:".$resarray['errorcode']." Message:".$resarray['errormessage'];
	}

	

}

	exec("rm -rf \"$filenametodelete\""); 
}
?>

 <!DOCTYPE html>
<html lang="en">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0"/>
  <title>Starter Template - Materialize</title>

  <!-- CSS  -->
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
  <link href="css/materialize.css" type="text/css" rel="stylesheet" media="screen,projection"/>
  <link href="css/style.css" type="text/css" rel="stylesheet" media="screen,projection"/>
</head>
<body>
  <nav class="waves-effect waves-light-blue lighten-1" role="navigation">
    <div class="nav-wrapper container"><a id="logo-container" href="#" class="brand-logo">Introducing...</a>
      <ul class="right hide-on-med-and-down">
        <li><a href="https://en.wikipedia.org/wiki/Google_Cloud_Print">Learn More</a></li>
      </ul>

      <ul id="nav-mobile" class="side-nav">
        <li><a href="https://en.wikipedia.org/wiki/Google_Cloud_Print">Learn More</a></li>
      </ul>
      <a href="#" data-activates="nav-mobile" class="button-collapse"><i class="material-icons">menu</i></a>
    </div>
  </nav>
  
    <div class="nav-wrapper container">
      <br>
      <h3 class="header center black-text">SMART PRINT</h3>
      <div class="row center">
        <h5 class="header center black-text">A CLOUD PRINT SYSTEM FOR ORGANIZATIONS</h5>
      </div>
 
      
    </div>
	</nav>

  
  	<div class="container">
	<div class="row">
        <div class="center">
          <div class="card blue-grey darken-1">
            <div class="card-content white-text">
              <span class="card-title">PRINT UNDER PROGRESS</span>
              
              <p>Thank You For The Request. Your have been allocated the printer at </p>
              <?php echo "<h3>".$printernames[$minindex]." </h3>";  ?> 
               <p> and will be completed shortly. You Can Check The Status Of Your Print Anytime. </p>
            </div>
            <div class="card-action">
              <a href="Home.html">HOME PAGE</a>
              <a href="Status.html">CHECK YOUR STATUS</a>
            </div>
          </div>
        </div>
      </div>
            
  
    <div class="footer-copyright">
      <div class="container">
      <h6 class="right light-blue-text text-lighten-1"> Designed by Shashank Gurudu and Mohammad Atif Hussain</h6>
      </div>
    </div>
  


  <!--  Scripts-->
  <script src="https://code.jquery.com/jquery-2.1.1.min.js"></script>
  <script src="js/materialize.js"></script>
  <script src="js/init.js"></script>

  </body
