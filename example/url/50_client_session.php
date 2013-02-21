<?php

Url::_register('staff',Url::home().'?act=staff');
Url::_register('staff_create',Url::staff().'&do=create');
Url::_register('staff_manage',Url::staff().'&do=manage&staff_id=$1');
Url::_register('profile',Url::staff().'&do=profile');
Url::_register('login',Url::staff().'&do=login');
Url::_register('logout',Url::staff().'&do=logout');
