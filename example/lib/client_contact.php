<?php
ld('contact');

abstract class ClientContact extends Contact {

	static $accounts_table	= 'clients';
	static $account_key		= 'client_id';

}
