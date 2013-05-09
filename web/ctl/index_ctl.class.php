<?

class index_ctl {

  public $user = false;

  public function __construct() {

    if ($user = self::loggedIn()) {
      $this->user = $user->data();
    }

  }

  public function index() {

    jade::c('index', ['user' => $this->user, 'authURL' => (new google())->authURL()]); 

    hpr($this->user);
    $goo = new google();
    $goo::$access_token = $this->user['access_token'];
    hpr($goo->api('userinfo'));


  }

  public static function loggedIn() {

    if ($data = summon::check()) {

      $user = user::i(user::findOne(array('id' => $data['user_id'])));
      
      if ($user->exists() && isset($user->sessions[$data['hash']])) {
        return $user;
      }

    }

    return false;

  }

}
