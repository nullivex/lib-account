<?php

abstract class Contact {

	const SHIP = 'ship';
	const BILL = 'bill';
	const BOTH = 'both';

	static $accounts_table = 'clients';
	static $contacts_table = 'contacts';

	final protected static function _fetch(&$pairs,$all=false){
		$select = 'SELECT * FROM `'.static::$contacts_table.'`';
		$where = Db::prepwhere($pairs);
		$func = ($all?'fetchAll':'fetch');
		return Db::_get()->$func($select.array_shift($where),$where);
	}
	
	final protected static function fetch($pairs=array()){
		return self::_fetch(&$pairs,false);
	}

	final protected static function fetchAll($pairs=array()){
		return self::_fetch(&$pairs,true);
	}

	final public static function allByAccount($account_id){
		return self::fetchAll(array(
			 'contact_account_id'		=> $account_id
			,'contact_account_table'	=> static::$accounts_table
			,'contact_is_active'		=> 1
			)
		);
	}

	final public static function allByAccountAndType($account_id,$contact_type){
		if(!is_array($contact_type)) $contact_type = array($contact_type);
		return self::fetchAll(array(
			 'contact_account_id'		=> $account_id
			,'contact_account_table'	=> static::$accounts_table
			,'contact_type'				=> array('AND',Db::IN,$contact_type)
			,'contact_is_active'		=> 1
			)
		);
	}
	
	final public static function get($contact_id){
		return self::fetch(array('contact_id'=>$contact_id));
	}
	
	final public static function createParams(){
		return array(
			 'first_name'	=> ''
			,'last_name'	=> ''
			,'email'		=> ''
			,'phone'		=> ''
			,'address_name'	=> ''
			,'address_1'	=> ''
			,'address_2'	=> ''
			,'city'			=> ''
			,'state'		=> ''
			,'zip'			=> ''
			,'country'		=> 'US'
		);
	}
	
	final public static function create($account_id,$data){
		return Db::_get()->insert(
			 static::$contacts_table
			,array(
				 'contact_account_id'		=> $account_id
				,'contact_account_table'	=> static::$accounts_table
				,'first_name'				=> mda_get($data,'first_name')
				,'last_name'				=> mda_get($data,'last_name')
				,'email'					=> mda_get($data,'email')
				,'contact_password'			=> bcrypt(mda_get($data,'password'))
				,'contact_is_active'		=> 1
				,'contact_created'			=> time()
				,'phone'					=> mda_get($data,'phone')
				,'address_name'				=> mda_get($data,'address_name')
				,'address_1'				=> mda_get($data,'address_1')
				,'address_2'				=> mda_get($data,'address_2')
				,'city'						=> mda_get($data,'city')
				,'state'					=> mda_get($data,'state')
				,'zip'						=> mda_get($data,'zip')
				,'country'					=> mda_get($data,'country')
			)
		);
	}
	
	final public static function update($contact_id,$data){
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
			 static::$accounts_table
			,'contact_id'
			,$contact_id
			,$params
		);
	}
	
	final public static function deactivate($contact_id){
		return Db::_get()->update(static::$accounts_table,'contact_id',$contact_id,array('contact_is_active'=>0));
	}

	final public static function contactDrop($account_id=null,$contact_type=null,$value=null,$name='contact_id'){
		lib('form_drop');
		$arr = array();
		foreach(self::allByAccountAndType($account_id,array($contact_type,self::BOTH)) as $contact)
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

	final public static function formatPhone(&$data){
		$p = mda_get($data,'phone');
		if(($p === '')||($p===null)) return '';
		return sprintf('(%03d)%03d-%04d',substr($p,0,3),substr($p,3,3),substr($p,6));
	}

	final public static function formatAddressLong(&$data){
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
