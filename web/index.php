<?

session_start();

require_once 'cfg/config.php';

(new kctl($_SERVER['REQUEST_URI']))->start();

