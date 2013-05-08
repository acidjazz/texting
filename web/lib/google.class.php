<?

class google {

  public static $client_id = false;
  public static $client_secret = false;
  public static $redirect_uri = false;
  
  public static $access_token = false;

  public static $url_auth = 'https://accounts.google.com/o/oauth2/auth';
  public static $url_token = 'https://accounts.google.com/o/oauth2/token';

  public static $url_scope = 'https://www.googleapis.com/auth/';
  public static $scope = ['userinfo.email', 'userinfo.profile'];

  public static $url_api = 'https://www.googleapis.com/oauth2/v1/';

  public function __construct($client_id=G_CLIENT_ID, $client_secret=G_SECRET, $redirect_uri=G_REDIRECT_URI) {
    self::$client_id = $client_id;
    self::$client_secret = $client_secret;
    self::$redirect_uri = $redirect_uri;
  }

  public function authURL() {

    $params = [
      'response_type' => 'code',
      'client_id' => self::$client_id,
      'redirect_uri' => self::$redirect_uri,
      'scope' => self::scopes(),
      //'state' => 'parameter' // google roudntrips this 
      //'login_hint' => 'acidjazz@gmail.com' // hint at which account we want
    ];

    return self::$url_auth.'?'.http_build_query($params);

  }

  public static function scopes() {

    $return = [];
    foreach (self::$scope as $permission) {
      $return[] = self::$url_scope.$permission;
    }

    return implode(' ', $return);

  }

  public function codeVerify($code) {

    $params = [
      'code' => $code,
      'client_id' => self::$client_id,
      'client_secret' => self::$client_secret,
      'redirect_uri' => self::$redirect_uri,
      'grant_type' => 'authorization_code'
    ];

    $results = json_decode(self::get(self::$url_token, $params, 'post'), true);

    if (isset($results['access_token']) && !empty($results['access_token'])) {
      self::$access_token = $results['access_token'];
    }

    return $results;

  }

  public function api($url, $params=array(), $type='get') {
    hpr(array_merge(['access_token' => self::$access_token], $params));
    return json_decode(self::get(self::$url_api.$url, array_merge(['access_token' => self::$access_token], $params), $type), true);
  }


  public static function get($url, $params, $type='get') {

    $handler = curl_init();

    curl_setopt($handler, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($handler, CURLOPT_USERAGENT, 'facebook-php-2.0');

    if ($type == 'post') {

      curl_setopt($handler, CURLOPT_POST, true);
      curl_setopt($handler, CURLOPT_POSTFIELDS, $params);

    } elseif ($type == 'delete') {
      curl_setopt($handler, CURLOPT_CUSTOMREQUEST, "DELETE");
      curl_setopt($handler, CURLOPT_POSTFIELDS, $params);

    } else {

      $url .= '?'.http_build_query($params, null, '&');

    }

    curl_setopt($handler, CURLOPT_URL, $url);

    return curl_exec($handler);


  }



}
