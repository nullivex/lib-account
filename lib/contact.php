<?php

abstract class Contact {

	const SHIP = 'ship';
	const BILL = 'bill';
	const BOTH = 'both';
	
	//format constants
	const FA_LINE_SHORT 	= 'formatLineShort';
	const FA_LINE_LONG 		= 'formatLineLong';
	const FA_BLOCK 			= 'formatBlock';
	const FA_BLOCK_MEDIUM	= 'formatBlockMedium';
	const FA_BLOCK_SHORT	= 'formatBlockShort';
	const FA_NAME 			= 'formatName';
	const FA_NAME_EMAIL		= 'formatNameEmail';

	static $accounts_table	= NULL;
	static $contacts_table	= 'contacts';
	static $account_key		= NULL;

	final protected static function _fetch(&$pairs,$all=false){
		$select = 'SELECT * FROM `'.static::$contacts_table.'`';
		$where = Db::prepwhere($pairs);
		$func = ($all?'fetchAll':'fetch');
		return Db::_get()->$func($select.array_shift($where),$where);
	}
	
	final protected static function fetch($pairs=array()){
		return self::addMacroFields(self::_fetch(&$pairs));
	}

	final protected static function fetchAll($pairs=array()){
		return self::addMacroFields(self::_fetch(&$pairs,true),true);
	}

	final public static function allByAccount($account_id){
		return self::fetchAll(array(
			 static::$account_key			=> $account_id
			,'contact_is_active'		=> 1
			)
		);
	}

	final public static function allByAccountAndType($account_id,$contact_type){
		if(!is_array($contact_type)) $contact_type = array($contact_type);
		return self::fetchAll(array(
			 static::$account_key			=> $account_id
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
			,'fax'			=> ''
			,'address_name'	=> ''
			,'address_1'	=> ''
			,'address_2'	=> ''
			,'city'			=> ''
			,'state'		=> ''
			,'zip'			=> ''
			,'country'		=> 'US'
		);
	}
	
	final public static function digest(&$data){
		return hash('sha1'
			,mda_get($data,'phone')
			.mda_get($data,'fax')
			.mda_get($data,'address_name')
			.mda_get($data,'address_1')
			.mda_get($data,'address_2')
			.mda_get($data,'city')
			.mda_get($data,'state')
			.mda_get($data,'zip')
			.mda_get($data,'country')
		);
	}

	final public static function create($account_id,$data){
		$now = time();
		return Db::_get()->insert(
			 static::$contacts_table
			,array(
				 static::$account_key		=> $account_id
				,'first_name'				=> mda_get($data,'first_name')
				,'last_name'				=> mda_get($data,'last_name')
				,'email'					=> mda_get($data,'email')
				,'contact_password'			=> bcrypt(mda_get($data,'password'))
				,'contact_is_active'		=> 1
				,'contact_created'			=> $now
				,'contact_updated'			=> $now
				,'phone'					=> mda_get($data,'phone')
				,'fax'						=> mda_get($data,'fax')
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
		$params = array();
		$params['contact_updated'] = time();
		//crypt the password if it changed (and confirmed password matches)
		if(mda_get($data,'password') === mda_get($data,'confirm_password')) $params['contact_password'] = bcrypt(mda_get($data,'password'));
		//update the contact info only if something changed
		if(self::digest(self::get($contact_id)) !== self::digest($data)){
			$params = array_merge($params,array(
				 'first_name'		=> mda_get($data,'first_name')
				,'last_name'		=> mda_get($data,'last_name')
				,'email'			=> mda_get($data,'email')
				,'phone'			=> mda_get($data,'phone')
				,'fax'				=> mda_get($data,'fax')
				,'address_name'		=> mda_get($data,'address_name')
				,'address_1'		=> mda_get($data,'address_1')
				,'address_2'		=> mda_get($data,'address_2')
				,'city'				=> mda_get($data,'city')
				,'state'			=> mda_get($data,'state')
				,'zip'				=> mda_get($data,'zip')
				,'country'			=> mda_get($data,'country')
			));
		}
		if(count(array_keys($params))>1){
			return Db::_get()->update(
				 static::$contacts_table
				,'contact_id'
				,$contact_id
				,$params
			);
		}
		return null;
	}
	
	final public static function deactivate($contact_id){
		return Db::_get()->update(static::$accounts_table,'contact_id',$contact_id,array('contact_is_active'=>0));
	}

	final public static function contactDrop($account_id=null,$contact_type=null,$value=null,$name='contact_id',$format=self::FA_NAME){
		lib('form_drop');
		$arr = array();
		foreach(self::allByAccountAndType($account_id,array($contact_type,self::BOTH)) as $contact)
			$arr[$contact['contact_id']] = self::$format($contact);
		$drop = FormDrop::_get()->setOptions($arr);
		$drop->setName($name);
		$drop->setValue($value);
		return $drop;
	}

	final protected static function addMacroFields($arr,$all=false){
		if($all){
			foreach($arr as &$row) $row = self::addMacroFields($row);
			return $arr;
		}
		//add legacy account_id reference that also generalizes
		if(!is_null(static::$account_key))
			$arr['contact_account_id'] = $arr[static::$account_key];
		return $arr;
	}

	
	//---------------------------
	//FORMATTING FUNCTIONS
	//---------------------------
	final public static function formatPhone(&$contact){
		$p = mda_get($contact,'phone');
		if(($p === '')||($p===null)) return '';
		return sprintf('(%03d)%03d-%04d',substr($p,0,3),substr($p,3,3),substr($p,6));
	}
	
	final public static function formatName(&$contact){
		return
			 ( mda_get($contact,'first_name') 	? 	  mda_get($contact,'first_name') 		: '' )
			.( mda_get($contact,'last_name') 	? ' '.mda_get($contact,'last_name') 		: '' )
		;
	}
	
	final public static function formatNameEmail(&$contact){
		return
			 ( mda_get($contact,'first_name') 	? 	  mda_get($contact,'first_name') 		: '' )
			.( mda_get($contact,'last_name') 	? ' '.mda_get($contact,'last_name') 		: '' )
			.( mda_get($contact,'email') 		? '<'.mda_get($contact,'email').'>' 		: '' )
		;
	}
	
	final public static function formatLineShort(&$contact){
		return
			 ( mda_get($contact,'address_1') 	? '  '.mda_get($contact,'address_1') 		: '' )
			.( mda_get($contact,'address_2') 	? '  '.mda_get($contact,'address_2') 		: '' )
			.( mda_get($contact,'zip') 			? ', '.mda_get($contact,'zip') 				: '' )
			.( mda_get($contact,'country') 		? '  '.mda_get($contact,'country') 			: '' )
		;
	}

	final public static function formatLineLong(&$contact){
		return
			 ( mda_get($contact,'address_name') ? 	   mda_get($contact,'address_name').',' : '' )
			.( mda_get($contact,'address_1') 	? '  '.mda_get($contact,'address_1') 		: '' )
			.( mda_get($contact,'address_2') 	? '  '.mda_get($contact,'address_2') 		: '' )
			.( mda_get($contact,'city') 		? ', '.mda_get($contact,'city') 			: '' )
			.( mda_get($contact,'state') 		? ', '.mda_get($contact,'state') 			: '' )
			.( mda_get($contact,'zip') 			? '  '.mda_get($contact,'zip') 				: '' )
			.( mda_get($contact,'country') 		? '  '.mda_get($contact,'country') 			: '' )
		;
	}

	final public static function formatBlock(&$contact){
		return strlen(($address = 
			 ( mda_get($contact,'address_name') ? 	   mda_get($contact,'address_name')	.PHP_EOL	: '' )
			.( mda_get($contact,'address_1') 	?	   mda_get($contact,'address_1')	.PHP_EOL 	: '' )
			.( mda_get($contact,'address_2') 	?	   mda_get($contact,'address_2') 	.PHP_EOL	: '' )
			.( mda_get($contact,'city') 		?	   mda_get($contact,'city') 					: '' )
			.( mda_get($contact,'state') 		? ', '.mda_get($contact,'state') 					: '' )
			.( mda_get($contact,'zip') 			? '  '.mda_get($contact,'zip') 						: '' )
			.( mda_get($contact,'country') 		? '  '.mda_get($contact,'country') 		.PHP_EOL	: '' )
			.( mda_get($contact,'phone')		? 'Phone: '.mda_get($contact,'phone')	.PHP_EOL	: '' )
			.( mda_get($contact,'fax')			? 'Fax: '.mda_get($contact,'fax')		.PHP_EOL	: '' )
		)) > 0 ? $address.PHP_EOL : '';
	}

	final public static function formatBlockMedium(&$contact){
		return strlen(($address = 
			 ( mda_get($contact,'address_1') 	?	   mda_get($contact,'address_1')	.PHP_EOL 	: '' )
			.( mda_get($contact,'address_2') 	?	   mda_get($contact,'address_2') 	.PHP_EOL	: '' )
			.( mda_get($contact,'city') 		?	   mda_get($contact,'city') 					: '' )
			.( mda_get($contact,'state') 		? ', '.mda_get($contact,'state') 					: '' )
			.( mda_get($contact,'zip') 			? '  '.mda_get($contact,'zip') 						: '' )
			.( mda_get($contact,'country') 		? '  '.mda_get($contact,'country') 		.PHP_EOL	: '' )
			.( mda_get($contact,'phone')		? 'Phone: '.mda_get($contact,'phone')	.PHP_EOL	: '' )
			.( mda_get($contact,'fax')			? 'Fax: '.mda_get($contact,'fax')		.PHP_EOL	: '' )
		)) > 0 ? $address.PHP_EOL : '';
	}

	final public static function formatBlockShort(&$contact){
		return strlen(($address = 
			 ( mda_get($contact,'address_1') 	?	   mda_get($contact,'address_1')	.PHP_EOL 	: '' )
			.( mda_get($contact,'address_2') 	?	   mda_get($contact,'address_2') 	.PHP_EOL	: '' )
			.( mda_get($contact,'city') 		?	   mda_get($contact,'city') 					: '' )
			.( mda_get($contact,'state') 		? ', '.mda_get($contact,'state') 					: '' )
			.( mda_get($contact,'zip') 			? '  '.mda_get($contact,'zip') 						: '' )
			.( mda_get($contact,'country') 		? '  '.mda_get($contact,'country') 					: '' )
		)) > 0 ? $address.PHP_EOL : '';
	}

}
