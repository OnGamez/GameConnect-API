<?php
// This is 2nd script

session_start();

include 'ongamez/ongamez-api.php';

$source = $_SESSION['OnGamezData.source'];
$userId = $_SESSION['OnGamezData.uid'];

$folderPath = $_SERVER['SCRIPT_NAME'];
	$folderPath = explode('/',$folderPath);
	array_pop($folderPath);
	$folderPath = join('/',$folderPath);

$backUrl = 'http://'.$_SERVER['HTTP_HOST'].$folderPath.'/shop-result.php';

echo 'Links to shop:
	<a href="'.OnGamezAPI::getUrlShopCoins( $source, $userId, 1, $backUrl ).'">buy 1 coin</a>
	<a href="'.OnGamezAPI::getUrlShopCoins( $source, $userId, 10, $backUrl ).'">buy 10 coins</a>
	<a href="'.OnGamezAPI::getUrlShopCoins( $source, $userId, 100, $backUrl ).'">buy 100 coins</a>
	<a href="'.OnGamezAPI::getUrlShopCoins( $source, $userId, 1000, $backUrl ).'">buy 1000 coins</a>
';
