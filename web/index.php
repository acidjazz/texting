<?

date_default_timezone_set("UTC");
require_once 'cfg/config.php';

(new kctl($_SERVER['REQUEST_URI']))->start();

