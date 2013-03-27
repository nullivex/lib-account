openlss/lib-account
====

Account management abstract library

The Account class is a base abstraction for Account features
such as Staff, Clients, Members, Subscribers, etc.

It is not intended to be used by itself, however the functions
are documented here as they are inherited by extenders.

Check example/lib/clients.php for an example of extension (this can be used as-is)

Check example/lib/client_contact.php for an example of extending lib/contact.php
which is also available with this package.

SQL files are included as well
  * example/clients.sql		The schema the clients extension works with
  * example/contacts.sql	The schema the client_contact extension works with

The account suite also interfaces quite nicely with openlss/lib-session for a complete working interface
to show this the following examples are also included
  * example/lib/client_session.php		The session extension
   * Requires
    * openlss/lib-session
    * openlss/lib-url
  * example/init/90_client_session.php	An init handler for starting and checking the session
   * Requires
    * openlss/func-ui 
    * openlss/lib-config
    * openlss/lib-url
  * example/url/50_client_session.php	URL definitions to work with the sessions
   * Requires
    * openlss/lib-url
  * example/client_session.sql			The schema the client_session extension uses
  * example/ctl/client_login.php		Example login controller
   * Requires
    * openlss/func-mda-glob
    * openlss/func-ui
    * openlss/lib-tpl
    * openlss/lib-url
  * example/ctl/client_logout.php		Example logout controller
   * Requires
    * openlss/func-ui
    * openlss/lib-url
  * example/ctl/client_profile.php		Example profile manager
   * Requires
    * openlss/func-ui
    * openlss/lib-tpl
    * openlss/lib-url
  * example/theme/client_login.xhtml	Example Login Template
  * example/theme/client_profile.xhtml	Example Profile Template
  * example/theme/account_profile.xhtml	Example account profile parent template

Usage
====

```php
use \LSS\Db;

//connect
Db::_get()->setConfig($dbconfig)->connect();

//execute a fetch
$result = Db::_get()->fetch('SELECT * FROM `table` WHERE `col` = ?',array($col));
```

Reference
====

Call to PDO
----
Any functions not shown in the reference are passed directly to PDO

Singleton Information
----
Db can be and is recommended to be used as a singleton to reuse the same PDO instance.

If multiple connections are needed use a custom method of maintaining the instances.

### ($this) Db::setConfig($config)
Sets the config of the database system.

Takes an array with the following structure
```php
$config = array(
	 'driver'		=>	'mysql'
	,'database'		=>	'database_name'
	,'host'			=>	'server_host'
	,'port'			=>	'server_port'
	,'user'			=>	'username'
	,'password'		=>	'password'
);
```

### ($this) Db::connect()
Will use the current configuration and connect

### (int) Db::getQueryCount()
Returns the current query count

### (bool) Db::close()
Close the open PDO istance (if any)

### (array) Db::prepWhere($pairs,$type='WHERE')
Prepares WHERE strings to be used in queries
  * $pairs	array of clauses which can be in 4 formats
   * 'field-name'	=>	array($bool='AND',$operator='=',$value)
   * 'field-name'	=>	array($operator='=',$value) //bool defaults to AND
   * 'field-name'	=>	array($operator) //bool defaults to AND, value defaults to NULL
   * 'field-name'	=>	$value //bool defaults to AND, operator defaults to =
   * NOTE: use Db::IS_NULL and Db::IS_NOT_NULL for null value operators
  * $type	specify the start of the string defaults to 'WHERE'
  * returns an array, with members:
   * [0] <string> the resulting WHERE clause; compiled for use with PDO::prepare including leading space (ready-to-use)
   * [n] <array>  the values array; ready for use with PDO::execute

### (int) Db::insert($table,$params=array(),$update_if_exists=false)
Insert into a table with given parameters

When $update_if_exists is set to TRUE it will perform an INSERT OR UPDATE query.

### (bool) Db::update($table,$keys=array(),$params=array())
Updates a record in the database
  * $table	The table to be updates
  * $keys	Pairs compatible with prepWhere
  * $params	Array of name=>value pairs to update with

### (result) Db::fetch($stmt,$params=array(),$throw_exception=Db::NO_EXCEPTIONS,$except_code=null,$flatten=Db::NO_FLATTEN)
Fetches a single row from a query and returns the result
  * $stmt				The SQL query
  * $params				Parameters to be bound to the query
  * $throw_exception	When set to DB::EXCEPTIONS will throw an exception on result not found
  * $except_cde			Code to be throw with the exception
  * $flatten			When set to DB::FLATTEN will return an array of values from a specific column


Protected internal macro functions
----
These are to be used internally by the extending class, and are used internally within the
methods in the Account parent abstract class, if the methods are not extended.

### (array of results) Account::_all($pairs=array())
Protected internal macro function;
Optionally restricted by the raw $pairs which are passed direct to Db::prepWhere()
Returns Db::fetchAll() from the selected target tables, augmented via addMacroFields().

### (int or bool) Account::_create($data,$contact_extra=array(),$account_extra=array())
Protected internal macro function;
This creates entries in both the Contacts table and the Accounts table.
Returns the (int)Account_ID of the resulting new Account, or (bool)false on any error.

	protected static function _createParams($extra=array())
	protected static function _getByEmail($pairs=array(),$except)
	protected static function _get($pairs=array())
### (array result) protected static function addMacroFields($c)
	protected static function getQuery()
	final public static function auth($password,&$c)
	final public static function contactDrop($account_id=null,$value=null,$name='contact_id')
	final public static function deactivate($id,$contact=true)
	final public static function delete($id,$contact=true)
	final public static function getContacts($account_id)
	final public static function update($account_id,$data)
	final public static function updateLastLogin(&$c)
	final public static function validate($data,$password=true)
	public static function all();
	public static function create($data);
	public static function createParams();
	public static function get($account_id);
	public static function getByContact($contact_id);
	public static function getByEmail($email,$except=false);
	public static function register($data);
