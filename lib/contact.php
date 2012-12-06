<?php

class Contact {

	const SHIP = 'ship';
	const BILL = 'bill';
	const BOTH = 'both';

	protected static function _fetch(&$pairs,$all=false){
		$select = 'SELECT * FROM `contacts`';
		$where = Db::prepwhere($pairs);
		$func = ($all?'fetchAll':'fetch');
		return Db::_get()->$func($select.array_shift($where),$where);
	}
	
	protected static function fetch($pairs=array()){
		return self::_fetch(&$pairs,false);
	}

	protected static function fetchAll($pairs=array()){
		return self::_fetch(&$pairs,true);
	}

	public static function allByAccount($account_id){
		return self::fetchAll(array(
			 'contact_account_id'	=> $account_id
			,'contact_is_active'	=> 1
			)
		);
	}

	public static function allByAccountAndType($account_id,$contact_type){
		return self::fetchAll(array(
			 'contact_account_id'	=> $account_id
			,'contact_type'			=> $contact_type
			,'contact_is_active'	=> 1
			)
		);
	}
	
	public static function get($contact_id){
		return self::fetch(array('contact_id'=>$contact_id));
	}
	
	public static function createParams(){
		return array(
			 'first_name'		=> ''
			,'last_name'		=> ''
			,'email'			=> ''
			,'phone'			=> ''
			,'address_name'		=> ''
			,'address_1'		=> ''
			,'address_2'		=> ''
			,'city'				=> ''
			,'state'			=> ''
			,'zip'				=> ''
			,'country'			=> 'US'
		);
	}
	
	public static function create($account_id,$data){
		return Db::_get()->insert(
			 'contacts'
			,array(
				 'contact_account_id'	=> $account_id
				,'first_name'			=> mda_get($data,'first_name')
				,'last_name'			=> mda_get($data,'last_name')
				,'email'				=> mda_get($data,'email')
				,'contact_password'		=> bcrypt(mda_get($data,'password'))
				,'contact_is_active'	=> 1
				,'contact_created'		=> time()
				,'phone'				=> mda_get($data,'phone')
				,'address_name'			=> mda_get($data,'address_name')
				,'address_1'			=> mda_get($data,'address_1')
				,'address_2'			=> mda_get($data,'address_2')
				,'city'					=> mda_get($data,'city')
				,'state'				=> mda_get($data,'state')
				,'zip'					=> mda_get($data,'zip')
				,'country'				=> mda_get($data,'country')
			)
		);
	}
	
	public static function update($contact_id,$data){
		$params = array(
			 'first_name'		=> mda_get($data,'first_name')
			,'last_name'		=> mda_get($data,'last_name')
			,'email'			=> mda_get($data,'email')
			,'phone'			=> mda_get($data,'phone')
			,'address_name'		=> mda_get($data,'address_name')
			,'address_1'		=> mda_get($data,'address_1')
			,'address_2'		=> mda_get($data,'address_2')
			,'city'				=> mda_get($data,'city')
			,'state'			=> mda_get($data,'state')
			,'zip'				=> mda_get($data,'zip')
			,'country'			=> mda_get($data,'country')
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

	public static function contactDrop($account_id=null,$contact_type=null,$value=null,$name='contact_id'){
		lib('form_drop');
		$arr = array();
		foreach(self::allByAccountAndType($account_id,$contact_type) as $contact)
			$arr[$contact['contact_id']] =
				 ((strlen($contact['first_name'])>0)?$contact['first_name'].' ':'')
				.((strlen($contact['last_name'])>0)?$contact['last_name']:'')
				.((strlen($contact['email'])>0)?' <'.$contact['email'].'>':'')
			;
		$drop = FormDrop::_get()->setOptions($arr);
		$drop->setName($name);
		$drop->setValue($value);
		return $drop;
	}

	public static function formatAddressLong(&$data){
		$address = (mda_get($data,'address_name'))?mda_get($data,'address_name').PHP_EOL:'';
		$address .= (mda_get($data,'address_1'))?mda_get($data,'address_1').PHP_EOL:'';
		$address .= (mda_get($data,'address_2'))?mda_get($data,'address_2').PHP_EOL:'';
		$address .= (mda_get($data,'city'))?mda_get($data,'city').', ':'';
		$address .= (mda_get($data,'state'))?mda_get($data,'state').'  ':'';
		$address .= (mda_get($data,'zip'))?mda_get($data,'zip').' ':'';
		$address .= (mda_get($data,'country'))?mda_get($data,'country'):'';
		$address .= (strlen($address)>0)?PHP_EOL:'';
		return $address;
	}

	
}
