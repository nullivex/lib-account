<?php

class Contact {

	public static function allByClient($client_id){
		return Db::_get()->fetchAll(
			 'SELECT * FROM contacts WHERE client_id = ? AND contact_is_active = ?'
			,array($client_id,1)
		);
	}
	
	public static function get($contact_id){
		return Db::_get()->fetch('SELECT * FROM contacts WHERE contact_id = ?',array($contact_id));
	}
	
	public static function createParams(){
		return array(
			 'first_name'		=>	''
			,'last_name'		=>	''
			,'email'			=>	''
			,'phone'			=>	''
			,'address_name'		=>	''
			,'address_1'		=>	''
			,'address_2'		=>	''
			,'city'				=>	''
			,'state'			=>	''
			,'zip'				=>	''
			,'country'			=>	'US'
		);
	}
	
	public static function create($client_id,$data){
		return Db::_get()->insert(
			 'contacts'
			,array(
				 'client_id'		=>	$client_id
				,'first_name'		=>	mda_get($data,'first_name')
				,'last_name'		=>	mda_get($data,'last_name')
				,'email'			=>	mda_get($data,'email')
				,'contact_password'	=>	bcrypt(mda_get($data,'password'))
				,'contact_is_active'=>	1
				,'contact_created'	=>	time()
				,'phone'			=>	mda_get($data,'phone')
				,'address_name'		=>	mda_get($data,'address_name')
				,'address_1'		=>	mda_get($data,'address_1')
				,'address_2'		=>	mda_get($data,'address_2')
				,'city'				=>	mda_get($data,'city')
				,'state'			=>	mda_get($data,'state')
				,'zip'				=>	mda_get($data,'zip')
				,'country'			=>	mda_get($data,'country')
			)
		);
	}
	
	public static function update($contact_id,$data){
		$params = array(
			 'first_name'		=>	mda_get($data,'first_name')
			,'last_name'		=>	mda_get($data,'last_name')
			,'email'			=>	mda_get($data,'email')
			,'phone'			=>	mda_get($data,'phone')
			,'address_name'		=>	mda_get($data,'address_name')
			,'address_1'		=>	mda_get($data,'address_1')
			,'address_2'		=>	mda_get($data,'address_2')
			,'city'				=>	mda_get($data,'city')
			,'state'			=>	mda_get($data,'state')
			,'zip'				=>	mda_get($data,'zip')
			,'country'			=>	mda_get($data,'country')
		);
		//add the password if it changed
		if(mda_get($data,'password')) $params['password'] = bcrypt(mda_get($data,'password'));
		return Db::_get()->update(
			 'contacts'
			,'contact_id'
			,$contact_id
			,$params
		);
	}
	
	public static function deactivate($contact_id){
		return Db::_get()->update('contacts','contact_id',$contact_id,array('contact_is_active'=>0));
	}
	
}
