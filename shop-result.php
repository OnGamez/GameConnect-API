<?php
// This is 3rd script

session_start();

include 'ongamez/ongamez-api.php';

$source = $_SESSION['OnGamezData.source'];
$userId = $_SESSION['OnGamezData.uid'];

// Require to reset cache to load data with new values.
// For example: show new count of coins left.
OnGamezAPI::resetHeaders( $source, $userId );

//------------------------------------------------------------------------------

echo '<html>';
echo '<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head>';
echo '<body>';
echo OnGamezAPI::getHtmlHeader($source,$userId);
echo '<div style="padding:20px;">';

//------------------------------------------------------------------------------

$status = $_GET['status'];
echo "Complete with status: {$status}<hr />";

if( $status == 'success' )
{
	$transactionId   = $_GET['trid'];
	$transactionSign = $_GET['trsign'];

	// Validate responce
	if( OnGamezAPI::validateTransactionId( $transactionId, $transactionSign ) === true )
	{
		// Get transaction details
		$transactionItem = OnGamezAPI::getTransactionInfo($transactionId);
		
		if( $transactionItem == false )
			die('Error on get server-side data');

		echo 'Transcation details: ';
		var_dump($transactionItem);
		
		// Notice: 
		// In this place partner's script should show some message like:
		// "Good! You got 1 million $$$"
		// 
		// Logic for that transaction should execute in transaction_notify.php script
	}
	else
	{
		echo 'Bad incoming transaction id';
	}
}
elseif( $status == 'cancel' )
{
	// User cancel transaction
}
elseif( $status == 'failed' )
{
	// Something went wrong on server-side
}

//------------------------------------------------------------------------------

echo '</div>';
echo OnGamezAPI::getHtmlFooter($source,$userId);
echo '</body>';
echo '</html>';
