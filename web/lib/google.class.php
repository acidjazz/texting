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

  public static $json_certs = '{"b55344bc52e40221cb6899431fcc293d3c7b529a": "-----BEGIN CERTIFICATE-----\nMIICITCCAYqgAwIBAgIIcnxObuDDBLAwDQYJKoZIhvcNAQEFBQAwNjE0MDIGA1UE\nAxMrZmVkZXJhdGVkLXNpZ25vbi5zeXN0ZW0uZ3NlcnZpY2VhY2NvdW50LmNvbTAe\nFw0xMzA1MDcxMTQzMzRaFw0xMzA1MDkwMDQzMzRaMDYxNDAyBgNVBAMTK2ZlZGVy\nYXRlZC1zaWdub24uc3lzdGVtLmdzZXJ2aWNlYWNjb3VudC5jb20wgZ8wDQYJKoZI\nhvcNAQEBBQADgY0AMIGJAoGBAKFRoo3jDNKTQSqJHeg8vAm7MbNZnMwpj1JG6yLQ\nZw3YPDsYV3lg/nEKWPXJjfijjEhCYunRe5YDLa8+nQ6yHyqfjE5BDLWLAq3kR8kS\ne4Y+jXbV83oOr7f58ARJBoIHAIlN55DJ8E0fpUno7sqA+F2eadqUNFto5QLJxZt9\nyfMRAgMBAAGjODA2MAwGA1UdEwEB/wQCMAAwDgYDVR0PAQH/BAQDAgeAMBYGA1Ud\nJQEB/wQMMAoGCCsGAQUFBwMCMA0GCSqGSIb3DQEBBQUAA4GBAIYgWpn4xdxfvpGH\nEZqNYYzoGwXk2dZdcru6A8Qj7COILZ7rfeIRriZJR3yQVgqdLFCTw7kzWNCCiEL0\nPw72O9O0ntC/RsfP1+N1CnMWqBn1c9mrLmTpPXecJisDHN5lq7Qs/lXjOOuZXYsF\n+4FzVxAAjZt8CcKmS4xOUzHR3kux\n-----END CERTIFICATE-----\n","81e0a6eaca3d9d2e03c8f01c6bee4cb5130c293a": "-----BEGIN CERTIFICATE-----\nMIICITCCAYqgAwIBAgIIG10PG61kdJ0wDQYJKoZIhvcNAQEFBQAwNjE0MDIGA1UE\nAxMrZmVkZXJhdGVkLXNpZ25vbi5zeXN0ZW0uZ3NlcnZpY2VhY2NvdW50LmNvbTAe\nFw0xMzA1MDgxMTI4MzRaFw0xMzA1MTAwMDI4MzRaMDYxNDAyBgNVBAMTK2ZlZGVy\nYXRlZC1zaWdub24uc3lzdGVtLmdzZXJ2aWNlYWNjb3VudC5jb20wgZ8wDQYJKoZI\nhvcNAQEBBQADgY0AMIGJAoGBANDXU7hzi48EZSMgVFzU5/pGLUttbr1MvQI7kPOa\njtIYDekjwVbM5UEMSE9UnezDfgi0u+y1fQ+ARCbtuCwtqKO5rhoVrX79Z0L050Wh\nZyBZyQxvEOY8HoOu/cnzjzEw+TUeYNR4OGO61ERPgGY/SAYNo9r8fz/7fuRHR1/b\nN5KpAgMBAAGjODA2MAwGA1UdEwEB/wQCMAAwDgYDVR0PAQH/BAQDAgeAMBYGA1Ud\nJQEB/wQMMAoGCCsGAQUFBwMCMA0GCSqGSIb3DQEBBQUAA4GBAJkOTybAXjksnzjL\nsSmLfWjIKOLV+i4BRD/xW5V59F0o5pQ5Dfb5k6ui90OlkC64PM7g4n2sNcKctghT\nIW8e4iI4sExKUd/8AdPQCsdQTQHAwJtKcrQNnEDjaxIpnFyfpL6j7Nf9XnTkdG40\n8SyGRAq28K29jIJFAoINS22QFDQv\n-----END CERTIFICATE-----\n"}';
  public static $certs = [];

  public function __construct($client_id=G_CLIENT_ID, $client_secret=G_SECRET, $redirect_uri=G_REDIRECT_URI) {

    self::$client_id = $client_id;
    self::$client_secret = $client_secret;
    self::$redirect_uri = $redirect_uri;

    self::$certs = json_decode(self::$json_certs, true);

  }


  public function authURL() {

    $params = [
      'response_type' => 'code',
      'client_id' => self::$client_id,
      'redirect_uri' => self::$redirect_uri,
      'scope' => self::scopes(),
      //'state' => 'parameter' // google roundtrips this 
      //'login_hint' => 'xxx@gmail.com' // hint at which account we want
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
    } else {
      return false;
    }

    if (isset($results['id_token']) && !empty($results['id_token'])) {
      $results['jwt'] = self::decode($results['id_token']);
    } else {
      return false;
    }

    return $results;

  }

  public function api($url, $params=array(), $type='get') {
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

  public static function decode($jwt, $verify=true) {

    $tks = explode('.', $jwt);

    if (count($tks) != 3) {
      throw new UnexpectedValueException('Wrong number of segments');
    }

    list($headb64, $payloadb64, $cryptob64) = $tks;
    if (null === ($header = json_decode(self::urlb64decode($headb64)))) {
      throw new UnexpectedValueException('Invalid head segment encoding');
    }

    if (null === $payload = json_decode(self::urlb64decode($payloadb64), true)) {
      throw new UnexpectedValueException('Invalid payload segment encoding');
    }

    $sig = self::urlb64decode($cryptob64);

    if ($verify) {

      if (empty($header->alg)) {
        throw new DomainException('Empty algorithm');
      }

      $verified = false;

      foreach (self::$certs as $keyname=>$pem) {

        $parsed = openssl_x509_read($pem);

        $status = openssl_verify($headb64.'.'.$payloadb64, $sig, $parsed, "sha256");

        openssl_x509_free($parsed);

        if ($status == 1) {
          $verified = true;
        }

      }

      if ($verified != true) {
        throw new UnexpectedValueException('Signature verification failed');
      }


    }

    return $payload;


  }

  public static function urlb64decode($input) {

    $remainder = strlen($input) % 4;

    if ($remainder) {
      $padlen = 4 - $remainder;
      $input .= str_repeat('=', $padlen);
    }

    return base64_decode(strtr($input, '-_', '+/'));
  }


}
