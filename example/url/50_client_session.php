<?php

//register Url::home() if not already
if(!Url::_isCallable('home'))
	Url::_register('home','/index.php')i;

Url::_register('client',Url::home().'?act=client');
Url::_register('profile',Url::client().'&do=profile');
Url::_register('login',Url::client().'&do=login');
Url::_register('logout',Url::client().'&do=logout');
