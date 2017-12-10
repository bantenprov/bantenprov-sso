<?php

/**
 * @Author: jdi-juma
 * @Date:   2017-12-09 16:45:17
 * @Last Modified by:   jdi-juma
 * @Last Modified time: 2017-12-09 23:19:33
 */


namespace Bantenprov\BantenprovSso;


class BantenprovSso 
{

	public static $result;
	public static $profile_result;
	public static $status;
	public static $request;
	public static $fails;
	public static $check_login;
	public static $token_access;

	public function __construct()
	{
		Self::$fails   			= false;
		Self::$profile_fails   	= false;
		Self::$token_access     = false;
		$this->profile = '';
	}


	static function Attempt($post)
	{
		$post['appid']		= env('APPID');
		$post['token']		= env('TOKEN');
		$curl=curl_init();
		curl_setopt($curl,CURLOPT_RETURNTRANSFER,1);
		curl_setopt($curl,CURLOPT_URL,env('SSO_LOGIN'));
		curl_setopt($curl,CURLOPT_POST,1);
		curl_setopt($curl,CURLOPT_POSTFIELDS,http_build_query($post));
		curl_setopt($curl,CURLOPT_TIMEOUT,20);
		curl_setopt($curl,CURLOPT_HTTPHEADER, array(
		    'Accept: application/json')
		);
		$exec=curl_exec($curl);
		if(!$exec)
		{
			return BantenprovSso::$fails = true;
		}
		curl_close($curl);
		$result = json_decode($exec);
		BantenprovSso::$result = $result;
		return $result->status;
	}

	static function message()
	{
		return BantenprovSso::$result->message;
	}

	static function check_logout($post)
	{
		$post['appid']		= env('APPID');
		$post['token']		= env('TOKEN');
		$curl=curl_init();
		curl_setopt($curl,CURLOPT_RETURNTRANSFER,1);
		curl_setopt($curl,CURLOPT_URL,env('CHECK_LOGOUT'));
		curl_setopt($curl,CURLOPT_POST,1);
		curl_setopt($curl,CURLOPT_POSTFIELDS,http_build_query($post));
		curl_setopt($curl,CURLOPT_TIMEOUT,20);
		curl_setopt($curl,CURLOPT_HTTPHEADER, array(
		    'Accept: application/json')
		);
		$exec=curl_exec($curl);
		if(!$exec)
		{
			return false;
		}
		curl_close($curl);
		$result = json_decode($exec);
		return $result->status;
	}

	static function check_login($post)
	{
		$post['appid']		= env('APPID');
		$post['token']		= env('TOKEN');
		$curl=curl_init();
		curl_setopt($curl,CURLOPT_RETURNTRANSFER,1);
		curl_setopt($curl,CURLOPT_URL,env('CHECK_LOGIN'));
		curl_setopt($curl,CURLOPT_POST,1);
		curl_setopt($curl,CURLOPT_POSTFIELDS,http_build_query($post));
		curl_setopt($curl,CURLOPT_TIMEOUT,20);
		curl_setopt($curl,CURLOPT_HTTPHEADER, array(
		    'Accept: application/json')
		);
		$exec=curl_exec($curl);

		//dd($exec);

		if(!$exec)
		{
			return false;
		}
		curl_close($curl);
		$result = json_decode($exec);
		BantenprovSso::$check_login = $result;
		return $result->status;
	}

	static function check_login_data()
	{
		return BantenprovSso::$check_login->data;
	}

	static function data()
	{
		if( BantenprovSso::$result->status == false)
		{
			return BantenprovSso::$result->message;
		}
		return BantenprovSso::$result->data;

	}

	static function Logout($post)
	{
		$post['appid']		= env('APPID');
		$post['token']		= env('TOKEN');
		$curl=curl_init();
		curl_setopt($curl,CURLOPT_RETURNTRANSFER,1);
		curl_setopt($curl,CURLOPT_URL,env('SSO_LOGOUT'));
		curl_setopt($curl,CURLOPT_POST,1);
		curl_setopt($curl,CURLOPT_POSTFIELDS,http_build_query($post));
		curl_setopt($curl,CURLOPT_TIMEOUT,20);
		curl_setopt($curl,CURLOPT_HTTPHEADER, array(
		    'Accept: application/json')
		);
		$exec=curl_exec($curl);
		//dd($exec);
		if(!$exec)
		{
			return false;
		}
		curl_close($curl);
		$result = json_decode($exec);
		BantenprovSso::$check_login = $result;
		return $result->status;
	}

	static function InitAddress()
	{
		$ipaddress = '';
	    if (getenv('HTTP_CLIENT_IP'))
	        $ipaddress = getenv('HTTP_CLIENT_IP');
	    else if(getenv('HTTP_X_FORWARDED_FOR'))
	        $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
	    else if(getenv('HTTP_X_FORWARDED'))
	        $ipaddress = getenv('HTTP_X_FORWARDED');
	    else if(getenv('HTTP_FORWARDED_FOR'))
	        $ipaddress = getenv('HTTP_FORWARDED_FOR');
	    else if(getenv('HTTP_FORWARDED'))
	       $ipaddress = getenv('HTTP_FORWARDED');
	    else if(getenv('REMOTE_ADDR'))
	        $ipaddress = getenv('REMOTE_ADDR');
	    else
	        $ipaddress = 'UNKNOWN';
	    return $ipaddress;
	}

	static function init()
	{
		return 'package loaded';
	}

}