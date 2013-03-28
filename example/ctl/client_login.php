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
use \LSS\Account\Client;
use \LSS\Account\ClientSession;
use \LSS\Tpl;
use \LSS\Url;

if(post('login')){
	try {
		//get the client member
		$client = Client::fetchByEmail(post('email'));
		if(!$client) throw new Exception('Client member doesnt exist');
		//check password
		if(!bcrypt_check(post('password'),$client['password']))
			throw new Exception('Password is invalid');
		//generate token and setup session
		$token = ClientSession::tokenCreate($client['client_id'],server('REMOTE_ADDR'),server('HTTP_USER_AGENT'));
		ClientSession::startSession($token);
		//update last login
		Client::updateLastLogin($client['client_id']);
		//redirect request
		if(session('login_referrer') && strpos(session('login_referrer'),Url::login()) === false)
			redirect(session('login_referrer'));
		else redirect(Url::home());
	} catch(Exception $e){
		alert($e->getMessage(),false);
	}
}

session('login_referrer',server('HTTP_REFERER'));

$params = array();
$params['url_login'] = Url::login();
$params['page_title'] = Config::get('site_name').' - Admin Login';
Tpl::_get()->output('client_login',$params);
