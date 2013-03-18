<?php
ld('account');

abstract class Client extends Account implements AccountInterface {

	static $accounts_table = 'clients';
	static $account_key = 'client_id';

	public static function adminHeaderParams($client_id,$company){
		return array(
			 'client_id'				=> $client_id
			,'client_company'			=> $company
			,'url_client_ticket_create'	=> Url::client_ticket_create($client_id)
			,'url_client_ticket_list'	=> Url::client_ticket_list($client_id)
			,'url_client_file_list'		=> Url::client_file_list($client_id)
			,'url_client_embed_tpl_list'=> Url::client_embed_tpl_list($client_id)
			,'url_client_manage'		=> Url::client_manage($client_id)
			,'url_client_edit'			=> Url::client_edit($client_id)
			,'url_client_api'			=> Url::client_api($client_id)
		);
	}

	public static function createParams(){
		return self::_createParams(array(
			 'is_active'	=> 1
			)
		);
	}

	public static function create($data){
		return self::_create($data
			,array('contact_is_active' => 1)
			,array(
				  'is_active'		=> 1
			 )
		);
	}

	public static function all(){
		return self::_all(array(
			  static::$accounts_table.'.is_active'		=> 1
			 )
		);
	}

	public static function get($client_id){
		return self::_get(array(
			 static::$accounts_table.'.client_id'		=> $client_id
			)
		);
	}

	public static function getByContact($contact_id){
		return self::_get(array(
			 static::$contacts_table.'.contact_id'		=> $contact_id
			)
		);
	}

	public static function getByEmail($email,$except=false){
		return self::_getByEmail(array(
			 static::$contacts_table.'.email'			=> $email
			)
			,$except
		);
	}

	public static function register($data){
		return self::create($data);
	}

}
