<?php

include 'ongamez/ongamez-api.php';

try
{
	if( $_GET['status'] != 'success' )
		throw new Exception('Transaction not successed');

	$transactionId   = $_GET['trid'];
	$transactionSign = $_GET['trsign'];
	
	// Validate incoming params
	if( OnGamezAPI::validateTransactionId( $transactionId, $transactionSign ) == false )
		throw new Exception('Bad incoming params'); // Try to hack? :) Do nothing! 
	
	// Get transaction details
	$transactionItem = OnGamezAPI::getTransactionInfo($transactionId);

	if( $transactionItem == false )
		throw new Exception('Error on get server-side data'); // Do nothing
	
	// Transcation details is var {$transactionItem}
	// Do some logic here...
}
catch( Exception $e )
{
	// Log error if require
}

// Response should always be 'DONE'
echo 'DONE';
