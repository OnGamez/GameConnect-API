<?php

class OnGamezAPI
{
	private static $RESOURCE_ID    = 1;
	private static $SECRET_KEY     = 'Secret Key Here';
	private static $SERVER_BASEURL = 'http://ongamez.ru/';

	public static function validateLoginSign( $source, $userId, $marker, $sign )
	{
		return $sign == md5( self::$RESOURCE_ID.$source.$userId.$marker.self::$SECRET_KEY );
	}

	public static function validateTransactionId( $transactionId, $transactionSign )
	{
		return $transactionSign == md5( self::$RESOURCE_ID.$transactionId.self::$SECRET_KEY );
	}
	
	public static function getUrlShopCoins( $source, $userId, $coinsAmount, $backUrl )
	{
		$squery = self::$RESOURCE_ID.'-'.$source.'-'.$userId.'-'.$coinsAmount.'-'.md5( self::$RESOURCE_ID.$source.$userId.$coinsAmount.self::$SECRET_KEY );
		$url    = self::$SERVER_BASEURL.'funds/spend?squery='.$squery.'&sbackurl='.urlencode( $backUrl );
		
		return $url;
	}

	public static function getUserInfo( $source, $userId, $marker )
	{
		$markerNew = $marker+1;
		$squery = self::$RESOURCE_ID.'-'.$source.'-'.$userId.'-'.$markerNew.'-'.md5( self::$RESOURCE_ID.$source.$userId.$markerNew.self::$SECRET_KEY );
		$url    = self::$SERVER_BASEURL.'api/userInfo?squery='.$squery;

		$data = @file_get_contents($url);
		
		if( $data === false )
			return false;
		
		return json_decode($data);
	}
	
	public static function getTransactionInfo( $transactionId )
	{
		$squery = self::$RESOURCE_ID.'-'.$transactionId.'-'.md5( self::$RESOURCE_ID.$transactionId.self::$SECRET_KEY );
		$url    = self::$SERVER_BASEURL.'api/transactionInfo?squery='.$squery;

		$data = @file_get_contents($url);
		
		if( $data === false )
			return false;
		
		return json_decode($data);
	}
}
