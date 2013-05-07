<?

define('G_PATH', '/var/www/texting/');
define('LIB_PATHS', '/var/www/texting/klib/,'.G_PATH.'mdl/,'.G_PATH.'ctl/,'.G_PATH.'lib/');
define('G_URL', 'http://texting.io/');

/* kdebug */
define('KDEBUG', true);
define('KDEBUG_HANDLER', true);
define('KDEBUG_EGPCS', true);
define('KDEBUG_SQL', true);

/* mongo config */
define('MONGO_HOST','mongodb://localhost:27017/');
define('MONGO_DB','texting');
//define('MONGO_REPLICA_SET', 'replica3');
define('MONGO_DEBUG', true);

/* ignore past this line */


/* set our include path(s) */
set_include_path(get_include_path().PATH_SEPARATOR.G_PATH);


/* autoload libs/classes in their specific folders */
spl_autoload_register(function($class) { 

	foreach (explode(',', LIB_PATHS) as $libdir) {
  	foreach (array('.class.php','.interface.php') as $file) {
			if (is_file($libdir.$class.$file)) {
				return require_once $libdir.$class.$file;
			}
		}
	}

	return false;

});


/* load our debuger if turned on */
if (defined('KDEBUG') && KDEBUG == true && php_sapi_name() != 'cli') {
	if (!defined('KDEBUG_JSON') || KDEBUG_JSON == false) {
		register_shutdown_function(array('kdebug', 'init'));
		if (defined('KDEBUG_HANDLER') && KDEBUG_HANDLER == true) {
			set_error_handler(array('kdebug', 'handler'), E_ALL);
		}
	}
}


/* debuger function wrappers */
function hpr() { return call_user_func_array(array('k','hpr'), func_get_args()); }
function cpr() { return call_user_func_array(array('k','cpr'), func_get_args()); }
function highlight() { return call_user_func_array(array('k','highlight'), func_get_args()); }
function xmlindent() { return call_user_func_array(array('k','xmlindent'), func_get_args()); }

