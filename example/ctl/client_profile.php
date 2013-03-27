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

if(post('edit')){
	try {
		Client::update(post('client_id'),post());
		alert('client profile updated successfully',true,true);
		redirect(Url::profile());
	} catch (Exception $e){
		alert($e->getMessage(),false);
	}
}

$params = array_merge(Client::get(ClientSession::get('client_id')),post());
Tpl::_get()->output('client_profile',$params);
