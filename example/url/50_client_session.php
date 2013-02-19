<?php

Url::register('staff',Url::home().'?act=staff');
Url::register('staff_create',Url::staff().'&do=create');
Url::register('staff_manage',Url::staff().'&do=manage&staff_id=$staff_id',array('staff_id'));
Url::register('profile',Url::staff().'&do=profile');
Url::register('login',Url::staff().'&do=login');
Url::register('logout',Url::staff().'&do=logout');
