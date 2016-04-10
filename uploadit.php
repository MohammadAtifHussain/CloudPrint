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

echo "heee";
echo $id;

	
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
	
	echo $filenametodelete;
	sleep(2);
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
