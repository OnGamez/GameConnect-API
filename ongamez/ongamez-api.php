<?php

class OnGamezAPI
{
	private static $RESOURCE_ID    = 1;
	private static $SECRET_KEY     = 'Secret Key Here';
	private static $SERVER_BASEURL = 'http://ongamez.ru';

	public static $lastErrorMessage = null;
	
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
		$url    = self::$SERVER_BASEURL.'/funds/spend?squery='.$squery.'&sbackurl='.urlencode( $backUrl );
		
		return $url;
	}

	public static function getUserInfo( $source, $userId, $marker )
	{
		$markerNew = $marker+1;
		$squery = self::$RESOURCE_ID.'-'.$source.'-'.$userId.'-'.$markerNew.'-'.md5( self::$RESOURCE_ID.$source.$userId.$markerNew.self::$SECRET_KEY );
		$url    = self::$SERVER_BASEURL.'/api/userInfo?squery='.$squery;

		$data = @file_get_contents($url);
		
		if( $data === false )
			return false;
		
		return json_decode($data);
	}
	
	public static function getTransactionInfo( $transactionId )
	{
		$squery = self::$RESOURCE_ID.'-'.$transactionId.'-'.md5( self::$RESOURCE_ID.$transactionId.self::$SECRET_KEY );
		$url    = self::$SERVER_BASEURL.'/api/transactionInfo?squery='.$squery;

		$data = @file_get_contents($url);
		
		if( $data === false )
			return false;
		
		return json_decode($data);
	}
	
	//--------------------------------------------------------------------------
	// PUBLIC: Get header & footer portal
	
	public static function getHtmlHeader( $source, $userId )
	{
		$objHeaders = self::getStaticDataHeaders( $source, $userId );

		if( $objHeaders == false )
			return false;

		return $objHeaders->header;
	}

	public static function getHtmlFooter( $source, $userId )
	{
		$objHeaders = self::getStaticDataHeaders( $source, $userId );

		if( $objHeaders == false )
			return false;

		return $objHeaders->footer;
	}

	public static function resetHeaders( $source, $userId )
	{
		$cacheKey = 'staticData.headers'.md5($source.':'.$userId);

		return self::setValueToStore( $cacheKey, null );
	}
	
	//--------------------------------------------------------------------------
	// PROTECTED: Get header & footer portal 

	protected static function getStaticDataHeaders( $source, $userId )
	{
		try
		{
			$cacheKey = 'staticData.headers'.md5($source.':'.$userId);

			//--- [1] Check data in session-cache ------------------------------

			$data = self::getValueFromStore( $cacheKey );
			
			if( $data != null )
			{
				if( isset($data->_timestamp_expired) && $data->_timestamp_expired > time() )
					return $data;
				
				// Clean cache!
				self::setValueToStore( $cacheKey, null );
			}
			
			//--- [2] Load data from OnGamez platform && decode it -------------
			
			$url = self::$SERVER_BASEURL.'/staticdata/'.
					'?type=headers'.
					'&rid='.self::$RESOURCE_ID.
					'&uid='.$userId.
					'&source='.urlencode($source).
					'&crc='.md5( self::$RESOURCE_ID.$source.$userId.self::$SECRET_KEY );

			$data = @file_get_contents($url);
			
			if( $data === false )
				throw new Exception('Failed on load "staticData.headers" content');
			
			$data = @json_decode( $data );
			
			if( $data === false )
				throw new Exception('Failed on json_decode "staticData.headers" content');
			
			if( $data->status !== true )
			{
				if( $data->errorMessage )
					throw new Exception($data->errorMessage);
				else
					throw new Exception('Bad response "staticData.headers" content');
			}

			//--- [3] Save to cache (if required) ------------------------------

			if( $data->lifetime > 0 )
			{
				$data->_timestamp_expired = time() + $data->lifetime;

				// Save to session cache
				self::setValueToStore( $cacheKey, $data );
			}
		}
		catch( Exception $e )
		{
			self::$lastErrorMessage = $e->getMessage();
			return false;
		}
		
		return $data;
	}
	
	protected static function getValueFromStore( $key )
	{
		if( session_id() == false )
			session_start();
		
		if( isset($_SESSION['OnGamezData.store'][$key]) == false )
			return null;
		
		return $_SESSION['OnGamezData.store'][$key];
	}

	protected static function setValueToStore( $key, $value )
	{
		if( session_id() == false )
			session_start();
		
		$_SESSION['OnGamezData.store'][$key] = $value;

		return true;
	}
}
