<?php

lib('staff');
lib('staff_session');

if(post('login')){
	try {
		//get the staff member
		$staff = Staff::getByEmail(post('email'));
		if(!$staff) throw new Exception('Staff member doesnt exist');
		//check password
		if(!bcrypt_check(post('password'),$staff['password']))
			throw new Exception('Password is invalid');
		//generate token and setup session
		$token = StaffSession::tokenCreate($staff['staff_id'],server('REMOTE_ADDR'),server('HTTP_USER_AGENT'));
		StaffSession::startSession($token);
		//update last login
		Staff::updateLastLogin($staff['staff_id']);
		//redirect request
		if(session('login_referrer') && strpos(session('login_referrer'),Url::login()) === false)
			redirect(session('login_referrer'));
		else redirect(Url::home());
	} catch(Exception $e){
		alert($e->getMessage(),false);
	}
}

session('login_referrer',server('HTTP_REFERER'));
page_header_admin(false);
Tpl::_get()->parse('login','page');
page_footer_admin();
Tpl::_get()->addCss(Config::get('tpl','theme_path').'/css/maruti-login.css');
Tpl::_get()->addJs('/js/maruti.login.js');
output(Tpl::_get()->output());
