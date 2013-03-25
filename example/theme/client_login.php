<?php
/**
 *  OpenLSS - Lighter Smarter Simpler
 *
 *	This file is part of OpenLSS.
 *
 *	OpenLSS is free software: you can redistribute it and/or modify
 *	it under the terms of the GNU Lesser General Public License as
 *	published by the Free Software Foundation, either version 3 of
 *	the License, or (at your option) any later version.
 *
 *	OpenLSS is distributed in the hope that it will be useful,
 *	but WITHOUT ANY WARRANTY; without even the implied warranty of
 *	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *	GNU Lesser General Public License for more details.
 *
 *	You should have received a copy of the 
 *	GNU Lesser General Public License along with OpenLSS.
 *	If not, see <http://www.gnu.org/licenses/>.
*/

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
