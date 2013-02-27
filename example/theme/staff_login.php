<?php

//overrides for login page
$this->resetCss();
$this->resetJs();

//add css
$this->addCss('css/bootstrap.min.css');
$this->addCss('css/bootstrap-responsive.min.css');
$this->addCss('css/maruti-style.css');
$this->addCss('css/maruti-login.css');

//add js
$this->addJs('js/jquery.min.js');
$this->addJs('js/maruti.login.js');

//override page structure
$this->setStub('body',false);
