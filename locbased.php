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
    echo "Sorry, file already exists.";
    $uploadOk = 0;
}
// Check file size
if ($_FILES["userfile"]["size"] > 500000) {
    echo "Sorry, your file is too large.";
    $uploadOk = 0;
}
// Allow certain file formats
/*if($imageFileType != "pdf" ) {
    echo "Sorry, only PDF files are allowed.";
    $uploadOk = 0;
}*/

// Check if $uploadOk is set to 0 by an error
if ($uploadOk == 0) {
    echo "Sorry, your file was not uploaded.";
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
$gcp->setAuthToken($_SESSION['accessToken']);

//$id = $_POST['printerid'];
$nearuserprinter = $_POST['nearuserprinter'];
echo $nearuserprinter."is near";
/*$array = array(
		array(0,1,2,5,6),
		array(5,3,1,6,7),
		array(5,3,1,3,1),
		array(5,8,5,0,1),
		array(5,7,3,3,0),
);*/
$array = array(
		array(0,4,9,5),
		array(4,0,3,11),
		array(9,3,0,7),
		array(5,11,7,0),
);

$arraymin = array(

);

include("connection.php");

$selectquery = "SELECT `jbshort`, `loadunbal` FROM `values`";

$selectqueryresult = mysql_query($selectquery);
echo $selectqueryresult;
$selectqueryrow = mysql_fetch_assoc($selectqueryresult);

echo "string";
echo $selectqueryrow;
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
	echo $i;
	while ($currentpageloadqueryrow = mysql_fetch_array($currentpageloadqueryresult)) {
		$arraymin[$i][0] = $currentprintjob + $currentpageloadqueryrow['pages'] + $array[$i][$nearuserprinter];
		$arraymin[$i][1] = $currentpageloadqueryrow['printerid'];
		$i++;
	}
	
	$minprinter = $arraymin[0][0];
	$minindex = 0;
	for ($i=0; $i < 4 ; $i++) { 
		for ($j=0; $j< 1; $j++) { 
			if ($arraymin[$i][$j] < $minprinter) {
				$minprinter = $arraymin[$i][$j];
				$minindex = $i;
			}
			//echo $arraymin[$i][$j]." ";

		}
		echo "<br/>";
	}
	echo $arraymin[$minindex][0]." is pages";
	echo "<br/>";
	echo $arraymin[$minindex][1]." is printerd id";
	echo "<br/>";
	$printerid = $arraymin[$minindex][1];
	$resarray = $gcp->sendPrintToPrinter($printerid, basename($_FILES["userfile"]["name"]), "./uploads/".basename($_FILES["userfile"]["name"]), "application/pdf");
	
	if($resarray['status']==true) {
		
		echo "Document has been sent to printer and should print shortly.";
		$jobid = $resarray['id'];
		echo $jobid;	
		$query = "INSERT INTO `jobs`(`rollno`, `printerid`, `jobid`, `pages`) VALUES ('1234','$printerid','$jobid','$totalpages')";
			if(mysql_query($query)){
				echo "Success storing jobid and pages into jobs";
			}
	}
	else {
		echo "An error occured while printing the doc. Error code:".$resarray['errorcode']." Message:".$resarray['errormessage'];
	}

	$updatequery = "UPDATE printers SET pages = pages + '$totalpages' where printerid = '$printerid'";
	if (mysql_query($updatequery)) {
		# code...
		echo "pages updated";
	}

}







	exec("rm -rf \"$filenametodelete\""); 
}
?>

<!DOCTYPE html>
<html>
<head>
	<title></title>
</head>
<body>

	
</body>
</html>
