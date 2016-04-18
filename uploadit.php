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

$printerid = "";
$id = $_POST['printerid'];

//echo $id;

include("connection.php");
/*$selectquery = "SELECT `jbshort`, `loadunbal` FROM `values`";
$selectqueryresult = mysql_query($selectquery);

$selectqueryrow = mysql_fetch_assoc($selectqueryresult);
echo $selectqueryrow;
$jbshort = $selectqueryrow['jbshort'];
$loadunbal = $selectqueryrow['loadunbal'];

echo $jbshort."and".$loadunbal;
*/
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
if ($totalpages <= $jbshort) {
	echo "entered short jobs";
	# code...
	$minquery = "SELECT printerid, pages from printers where pages = ( select MIN(pages) from printers )";
	$result = mysql_query($minquery);
	$row = mysql_fetch_assoc($result);

	$printerid = $row['printerid'];
	$pages = $row['pages'];
	echo $printerid;
	echo "printer id is".$printerid;
	echo "<br/>";
	echo "pages are".$pages;
	echo "<br/>";


	$resarray = $gcp->sendPrintToPrinter($printerid, basename($_FILES["userfile"]["name"]), "./uploads/".basename($_FILES["userfile"]["name"]), "application/pdf");
	
	if($resarray['status']==true) {
		
		echo "Document has been sent to min queue printer and should print shortly.";
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
else{

	$maxquery = "SELECT printerid, pages from printers where pages = ( select MAX(pages) from printers )";
	$maxqueryresult = mysql_query($maxquery);
	$maxqueryrow  = mysql_fetch_assoc($maxqueryresult);
	$maxprinterid = $maxqueryrow['printerid'];
	$maxpages = $maxqueryrow['pages'];

	$minquery = "SELECT printerid, pages from printers where pages = ( select MIN(pages) from printers )";
	$minqueryresult = mysql_query($minquery);
	$minqueryrow  = mysql_fetch_assoc($minqueryresult);
	$minprinterid = $minqueryrow['printerid'];
	$minpages = $minqueryrow['pages'];

	$lbquery = "SELECT printerid, MAX(pages) from printers where (pages + 50 ) < (50 + ( select MIN(pages) from printers ))";

	if (($maxpages - $minpages) <= $loadunbal ) {
		# code...
	$resarray = $gcp->sendPrintToPrinter($maxprinterid, basename($_FILES["userfile"]["name"]), "./uploads/".basename($_FILES["userfile"]["name"]), "application/pdf");
	
	if($resarray['status']==true) {
		
		echo "Document has been sent to max queue printer and should print shortly.";
		$jobid = $resarray['id'];
		echo $jobid;	
		$query = "INSERT INTO `jobs`(`rollno`, `printerid`, `jobid`, `pages`) VALUES ('1234','$maxprinterid','$jobid','$totalpages')";
			if(mysql_query($query)){
				echo "Success storing jobid and pages into jobs";
			}
	}
	else {
		echo "An error occured while printing the doc. Error code:".$resarray['errorcode']." Message:".$resarray['errormessage'];
	}

	$updatequery = "UPDATE printers SET pages = pages + '$totalpages' where printerid = '$maxprinterid'";
	if (mysql_query($updatequery)) {
		# code...
		echo "pages updated";
	}		


	}
	else{
		$lb = mysql_query($lbquery);
		$row =  mysql_fetch_assoc($lb);
		print_r($row);
		$isnull = $row['printerid'];
		echo $row['printerid'];
		echo $isnull."is null";
		if ($isnull == "") {
			# code...
			echo " zero";
			$minqueryresult = mysql_query($minquery);
			$minqueryrow  = mysql_fetch_assoc($minqueryresult);
			$minprinterid = $minqueryrow['printerid'];
			$minpages = $minqueryrow['pages'];

			$resarray = $gcp->sendPrintToPrinter($minprinterid, basename($_FILES["userfile"]["name"]), "./uploads/".basename($_FILES["userfile"]["name"]), "application/pdf");
	
			if($resarray['status']==true) {
				
				echo "Document has been sent to min queue printer and should print shortly.";
				$jobid = $resarray['id'];
				echo $jobid;	
				$query = "INSERT INTO `jobs`(`rollno`, `printerid`, `jobid`, `pages`) VALUES ('1234','$minprinterid','$jobid','$totalpages')";
					if(mysql_query($query)){
						echo "Success storing jobid and pages into jobs";
					}
			}
			else {
				echo "An error occured while printing the doc. Error code:".$resarray['errorcode']." Message:".$resarray['errormessage'];
			}

			$updatequery = "UPDATE printers SET pages = pages + '$totalpages' where printerid = '$minprinterid'";
			if (mysql_query($updatequery)) {
				# code...
				echo "pages updated";
			}

			$maxquery = "SELECT printerid, pages from printers where pages = ( select MAX(pages) from printers )";
			$maxqueryresult = mysql_query($maxquery);
			$maxqueryrow  = mysql_fetch_assoc($maxqueryresult);
			$maxprinterid = $maxqueryrow['printerid'];
			$maxpages = $maxqueryrow['pages'];

			$minquery = "SELECT printerid, pages from printers where pages = ( select MIN(pages) from printers )";
			$minqueryresult = mysql_query($minquery);
			$minqueryrow  = mysql_fetch_assoc($minqueryresult);
			$minprinterid = $minqueryrow['printerid'];
			$minpages = $minqueryrow['pages'];

			$loadunbal = $maxpages - $minpages;
			$loadunbalupdatequery = "UPDATE values SET loadunbal = $loadunbal";
			if (mysql_query($loadunbalupdatequery)) {
				# code...
				echo "loadunbal updated";
			}

		}
		else {
			# code...
			echo "not zero";

			$resarray = $gcp->sendPrintToPrinter($isnull, basename($_FILES["userfile"]["name"]), "./uploads/".basename($_FILES["userfile"]["name"]), "application/pdf");
	
			if($resarray['status']==true) {
				
				echo "Document has been sent to max from array of printers and should print shortly.";
				$jobid = $resarray['id'];
				echo $jobid;	
				$query = "INSERT INTO `jobs`(`rollno`, `printerid`, `jobid`, `pages`) VALUES ('1234','$isnull','$jobid','$totalpages')";
					if(mysql_query($query)){
						echo "Success storing jobid and pages into jobs";
					}
			}
			else {
				echo "An error occured while printing the doc. Error code:".$resarray['errorcode']." Message:".$resarray['errormessage'];
			}

			$updatequery = "UPDATE printers SET pages = pages + '$totalpages' where printerid = '$isnull'";
			if (mysql_query($updatequery)) {
				# code...
				echo "pages updated";
			}

		}
	}

}











/*
	//$printerid = $printers[0]['id']; // Pass id of any printer to be used for print
	$printerid = $id;
	// Send document to the printer
	$resarray = $gcp->sendPrintToPrinter($printerid, basename($_FILES["userfile"]["name"]), "./uploads/".basename($_FILES["userfile"]["name"]), "application/pdf");
	
	if($resarray['status']==true) {
		
		echo "Document has been sent to printer and should print shortly.";
	}
	else {
		echo "An error occured while printing the doc. Error code:".$resarray['errorcode']." Message:".$resarray['errormessage'];
	}
	$jobid = $resarray['id'];
	echo $jobid;
	include("connection.php");	
	$query = "INSERT into jobstatus values('1234','$jobid')";
		if(mysql_query($query)){
			echo "Success storing into db";
		}
	echo $filenametodelete;
	*/
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
