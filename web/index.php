<?

session_start();

require_once 'cfg/config.php';

$url = 'https://accounts.google.com/o/oauth2/auth';

//https://accounts.google.com/o/oauth2/auth?response_type=code&redirect_uri=http%3A%2F%2Fwww.texting.io%2Foauth2callback&client_id=630056235533.apps.googleusercontent.com&scope=https%3A%2F%2Fwww.googleapis.com%2Fauth%2Fplus.login&access_type=offline&approval_prompt=force

(new kctl($_SERVER['REQUEST_URI']))->start();

