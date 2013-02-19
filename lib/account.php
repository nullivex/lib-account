<?php

interface AccountInterface {
	public static function createParams();
	public static function create($data);
	public static function all();
	public static function get($account_id);
	public static function getByContact($contact_id);
	public static function getByEmail($email,$except=false);
	public static function register($data);
}
						     
abstract class Account {

	static $accounts_table = NULL;
	static $contacts_table = 'contacts';
	static $account_key = NULL;

	//
	// INTERNAL MACROS: for use by extenders of this class
	//
	protected static function _createParams($extra=array()){
		return array_merge(array(
			 'email'		=> ''
			,'company'		=> ''
			,'first_name'	=> ''
			,'last_name'	=> ''
			)
			,$extra
		);
	}

	protected static function _create($data,$contact_extra=array(),$account_extra=array()){
		$time = time();
		$contact_id = Db::_get()->insert(
			static::$contacts_table
			,array_merge(
				array(
					'first_name'	=>	mda_get($data,'first_name')
					,'last_name'	=>	mda_get($data,'last_name')
					,'email'		=>	mda_get($data,'email')
				)
				,array(
					 'contact_password'			=> bcrypt(mda_get($data,'password'))
					,'contact_created'			=> $time
				)
				,$contact_extra
			)
		);
		if(is_numeric($contact_id)){
			$account_id = Db::_get()->insert(
				static::$accounts_table
				,array_merge(
					array(
						'company'		=>	mda_get($data,'company')
					)
					,array(
						 'primary_contact_id'	=> $contact_id
						,'password'				=> bcrypt(mda_get($data,'password'))
						,'created'				=> $time
					)
					,$account_extra
				)
			);
			if(is_numeric($account_id))
				Db::_get()->update(
					 static::$contacts_table
					 ,'contact_id'
					 ,$contact_id
					 ,array(static::$account_key=>$account_id)
				);
		} else
			$account_id = false;
		return $account_id;
	}

	protected static function getQuery(){
		return
			 'SELECT `'.static::$accounts_table.'`.*,`'.static::$contacts_table.'`.*'
			.' FROM `'.static::$accounts_table.'`'
			.' LEFT JOIN `'.static::$contacts_table.'`'
			.' ON `'.static::$contacts_table.'`.`contact_id`=`'.static::$accounts_table.'`.`primary_contact_id`'
		;
	}

	protected static function _all($pairs=array()){
		$where = Db::prepwhere($pairs);
		$result = Db::_get()->fetchAll(self::getQuery().array_shift($where),$where);
		foreach($result as &$row) self::addMacroFields($row);
		return $result;
	}

	protected static function _get($pairs=array()){
		$where = Db::prepwhere($pairs);
		return self::addMacroFields(Db::_get()->fetch(
			 self::getQuery().array_shift($where)
			,$where
			)
		);
	}

	protected static function _getByEmail($pairs=array(),$except){
		$where = Db::prepwhere($pairs);
		return self::addMacroFields(Db::_get()->fetch(
			 self::getQuery().array_shift($where)
			,$where
		));
	}

	//
	// General methods (common functions which won't be extended)
	//
	final public static function validate($data,$password=true){
		Validate::prime($data);
		if($password) Validate::go('password')->not('blank')->min(6)->max(20)->type('password');
		Validate::go('email')->not('blank')->type('email');
		Validate::paint();
	}

	final public static function update($account_id,$data){
		$update = array('company'=>mda_get($data,'company'));
		if(mda_get($data['password'])) $update['password'] = bcrypt(mda_get($data,'password'));
		return Db::_get()->update(static::$accounts_table,static::$account_key,$account_id,$update);
	}

	final public static function updateLastLogin(&$c){
		$time = time();
		if((mda_get($c,'__auth') & 1)
			&&
			(Db::_get()->update(static::$contacts_table,'contact_id',mda_get($c,'contact_id'),array('contact_last_login'=>$time)))
		)
			$c['contact_last_login'] = $time;
		if((mda_get($c,'__auth') & 2)
			&&
			Db::_get()->update(static::$accounts_table,static::$account_key,mda_get($c,'account_id'),array('last_login'=>$time))
		)
			$c['last_login'] = $time;
		//reprocess display vars, etc
		self::addMacroFields($c);
		return array_merge($c);
	}

	final public static function deactivate($id,$contact=true){
		if($contact)
			return Db::_get()->update(static::$contacts_table,'contact_id',$id,array('contact_is_active'=>0));
		else
			return Db::_get()->update(static::$accounts_table,static::$account_key,$id,array('is_active'=>0));
	}

	final public static function delete($id,$contact=true){
		if($contact)
			return Db::_get()->run('DELETE FROM `'.static::$contacts_table.'` WHERE `contact_id`=?',array('contact_id'=>$id));
//		else
			//return Db::_get()->run('DELETE FROM `'.static::$accounts_table.'` WHERE `account_id`=?',array('account_id'=>$id));
	}

	final public static function auth($password,&$c){
		//check password(s)
		$auth = 0;
		$auth += (bcrypt_check($password,mda_get($c,'contact_password')))?1:0;
		$auth += (mda_get($c,'__is_account') && bcrypt_check($password,mda_get($c,'password')))?2:0;
		$c['__auth'] = $auth;
		//reprocess display vars, etc
		self::addMacroFields($c);
		return $auth;
	}

	final public static function getContacts($account_id){
		$pairs[static::$contacts_table.'.contact_account_table'] = static::$accounts_table;
		$where = Db::prepwhere(array(static::$account_key=>$account_id));
		return Db::_get()->fetchAll(
			 'SELECT * FROM `'.static::$contacts_table.'`'
		    .array_shift($where)
			,$where
		);
	}

	final public static function contactDrop($account_id=null,$value=null,$name='contact_id'){
		if(is_null($account_id)) return false;
		lib('form_drop');
		$arr = array();
		foreach(self::getContacts($account_id) as $contact) $arr[$contact['contact_id']] = $contact['first_name'].' '.$contact['last_name'].' <'.$contact['email'].'>';
		$drop = FormDrop::_get()->setOptions($arr);
		$drop->setName($name);
		$drop->setValue($value);
		return $drop;
	}


	final protected static function addMacroFields($c){
		if(!is_array($c)) return false;
		//inject some value-added calculated pseudofields (rather than dupe this logic in multiple controllers)
		//add account_id for legacy purposes and generic purposes
		$c['account_id'] = $c[static::$account_key];
		//check if this contact has a account_id (is an account)
		$c['__is_account'] = (is_numeric(mda_get($c,'account_id')))?true:false;
		//generate standard displayed contact and account_id
		$c['contact_id_display']			= sprintf('%08d',mda_get($c,'contact_id'));
		$c['account_id_display']			= mda_get($c,'__is_account') ? sprintf('%08d',mda_get($c,'account_id')) : '(none)';
		//generate standard displayed dates
		$c['created_display']				= date(Config::get('date','standard_format'),mda_get($c,'created'));
		$c['contact_created_display']		= date(Config::get('date','standard_format'),mda_get($c,'contact_created'));
		$c['last_login_display']			= (mda_get($c,'last_login')>0)
												? date(Config::get('date','standard_format'),mda_get($c,'last_login'))
												: '(never)'
												;
		$c['contact_last_login_display']	= (mda_get($c,'contact_last_login')>0)
												? date(Config::get('date','standard_format'),mda_get($c,'contact_last_login'))
												: '(never)'
												;
		$c['header_last_login'] = mda_get($c,'contact_last_login_display');
		if(mda_get($c,'__auth') & 2)
			$c['header_last_login'] = mda_get($c,'last_login_display');
		return array_merge($c);
	}

}
