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

/* OLD templating stuff
page_header_admin(false);
$params = array('url_login'=>htmlentities(Url::login()));
Tpl::_get()->parse('login','page',$params);
page_footer_admin(false);
Tpl::_get()->parse('login','page_end');
Tpl::_get()->addCss(Config::get('tpl','theme_path').'/css/maruti-login.css');
Tpl::_get()->addJs('/js/maruti.login.js');
output(Tpl::_get()->output());
 */

//load new templating engine
require_once(ROOT.'/usr/phptal/PHPTAL.php');

$script_stats = 'Execution: 0.00613 | Queries: 0 | Memory: 1.58MB';

$tpl = new PHPTAL(ROOT_GROUP.'/theme/default/login.xhtml');
$tpl->theme_uri = '/theme/default';
$tpl->site_title = Config::get('info','site_name').' Admin Login';
$tpl->site_name = Config::get('info','site_name');
$tpl->url_login = Url::login();
$tpl->copyright = '© '.date('Y').' '.Config::get('info','site_name');
$tpl->script_info = sprintf('Vidcache: v%s | OpenLSS: v%s | %s',VERSION,LSS_VERSION,$script_stats);
$out = $tpl->execute();

//cleanup the output
$tidy = new tidy();
$tidy->parseString($out,array('indent'=>true,'output-xml'=>true,'numeric-entities'=>true,'preserve-entities'=>true,'wrap'=>300),'utf8');
$tidy->cleanRepair();

//output
echo $tidy;
