<?php
// This is 1st script

session_start();

$source = $_GET['source'];
$userId = $_GET['uid'];
$marker = $_GET['marker'];
$sign   = $_GET['sign'];

include 'ongamez/ongamez-api.php';

if( OnGamezAPI::validateLoginSign($source,$userId,$marker,$sign) == false )
{
	echo 'Access deny (step 1)';
	die();
}

//--------------------------------------
// Validate marker value and get UserInfo

$result = OnGamezAPI::getUserInfo($source,$userId,$marker);

if( $result == false )
{
	echo 'Error on get server-side data';
	die();
}

if( $result->status=='ERROR' )
{
	echo 'Access deny (step 2):';
	var_dump($result);
	die();
}



echo "Validation success! User info:";
var_dump($result);
echo "<hr />";

// Save required data in session for future usage
$_SESSION['OnGamezData.source'] = $source;
$_SESSION['OnGamezData.uid'] = $userId;

// Go to next demo page
echo "<a href='shop.php'>Go to shop page</a>";
