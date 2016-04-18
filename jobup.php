<?php

session_start();

ini_set('display_startup_errors',1);
ini_set('display_errors',1);
error_reporting(-1);
require 'Config.php';
require_once 'GoogleCloudPrint.php';
$gcp = new GoogleCloudPrint();
$refreshTokenConfig['refresh_token'] = '1/LO9NCtL5PjoNYrC0Z-vyPnEQiRde8_2Ts2Q6YN8gdQ0';

$token = $gcp->getAccessTokenByRefreshToken($urlconfig['refreshtoken_url'],http_build_query($refreshTokenConfig));

$gcp->setAuthToken($token);


include("connection.php");
$selectquery = "SELECT * FROM `jobs`";
$selectqueryresult = mysql_query($selectquery);
while ($row = mysql_fetch_assoc($selectqueryresult)) {
	# code...
	$jobid = $row['jobid'];
	$printerid = $row['printerid'];
	$totalpages = $row['pages'];
	$status  = $gcp->jobStatus($row['jobid']);
	$done = "DONE";
	echo $status;
	echo "<br/>";
	if (strcmp($status, $done) == 0) {
		# code...
		$deletequery = "DELETE FROM `jobs` WHERE printerid = '$printerid'";
		if (mysql_query($deletequery)) {
			# code...
			echo "job deleted ";
			$updatepagesquery = "UPDATE `printers` SET pages = pages - '$totalpages' WHERE printerid = '$printerid'";
			if (mysql_query($updatepagesquery)) {
				# code...
				echo "pages updated";
			}
			

		}
	}

}

?>