<?

class gcm {


  public static $url = 'https://android.googleapis.com/gcm/send';
  public static $apikey = false;


  // assign our app-specific information
  public function __construct($apikey=G_SERVERKEY) {
    self::$apikey = $apikey;
  }

  public function send($regids=[], $data) {

    if (!is_array($regids)) {
      $regids = [$regids];
    }

    return json_decode($this->post($regids, $data), true);

  }

  public static function post($regids, $data) {

    $handler = curl_init();

    $headers = array(
      'Authorization: key=' . self::$apikey,
      'Content-Type: application/json'
    );

    $params = [
      'registration_ids' => $regids,
      'data' => $data
    ];

    curl_setopt($handler, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($handler, CURLOPT_RETURNTRANSFER, true);

    curl_setopt($handler, CURLOPT_POST, true);
    curl_setopt($handler, CURLOPT_POSTFIELDS, json_encode($params));

    curl_setopt($handler, CURLOPT_URL, self::$url);

    return curl_exec($handler);

  }


}
