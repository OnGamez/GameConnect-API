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

//------------------------------------------------------------------------------
// Save required data in session for future usage

$_SESSION['OnGamezData.source'] = $source;
$_SESSION['OnGamezData.uid'] = $userId;

//------------------------------------------------------------------------------

echo '<html>';
echo '<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head>';
echo '<body>';
echo OnGamezAPI::getHtmlHeader($source,$userId);
echo '<div style="padding:20px;">';

	echo "Validation success! User info:";
	var_dump($result);
	echo "<hr />";

	// Go to next demo page
	echo "<a href='shop.php'>Go to shop page</a>";
	
echo '</div>';
echo OnGamezAPI::getHtmlFooter($source,$userId);
echo '</body>';
echo '</html>';
