<?php
/**
 *  OpenLSS - Lighter Smarter Simpler
 *
 *	This file is part of OpenLSS.
 *
 *	OpenLSS is free software: you can redistribute it and/or modify
 *	it under the terms of the GNU Lesser General Public License as
 *	published by the Free Software Foundation, either version 3 of
 *	the License, or (at your option) any later version.
 *
 *	OpenLSS is distributed in the hope that it will be useful,
 *	but WITHOUT ANY WARRANTY; without even the implied warranty of
 *	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *	GNU Lesser General Public License for more details.
 *
 *	You should have received a copy of the 
 *	GNU Lesser General Public License along with OpenLSS.
 *	If not, see <http://www.gnu.org/licenses/>.
 */
namespace LSS\Account;

use \LSS\Url;

abstract class ClientSession extends \LSS\Session {
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
