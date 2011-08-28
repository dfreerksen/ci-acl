UNTIL FURTHER NOTICE, PLEASE DO NOT USE THIS LIBRARY. IT IS STILL UNDER DEVELOPMENT.

1) Autoload database library
2) Autoload ACL library
	No need to autoload Session library (ACL library takes care of that)
	Make sure encryption_key is set in config.php

acl_restricted config value is for controllers or controller/methods that you want to restrict access to. If you do not want to restrict access to a controller or controller/method then it doesn't not need to be specified

ACL also takes into account table prefixes (set in your application/config/database.php file)