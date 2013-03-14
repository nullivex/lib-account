<?php

lib('session');

abstract class ClientSession extends Session {
	public static function init(){
		self::$config_name		= 'client';
		self::$session_name		= 'client_token';
		self::$session_table	= 'client_session';
		self::$user_primary_key	= 'contact_id';
		self::$urls_nologin		= array(Url::login(),Url::signup());
	}
}

//overrides the parent vars
ClientSession::init();
