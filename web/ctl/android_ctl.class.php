<?

class android_ctl {

  public $user = false;

  public function __construct() {

    define('KDEBUG_JSON', true);
    header('Content-type: application/json');

     if ($results = $this->tokenVerify()) {
       $this->user = user::i(user::findOne(array('id' =>  $results['id'])));
     } else {
       return false;
     }
  }

  private function tokenVerify() {

    // invalid token
    if (!isset($_REQUEST['token']) || empty($_REQUEST['token'])) {
      return false;
    }

    $goo = new google();
   
    // invalid token decoding
    if (!$results = $goo->tokenVerify($_REQUEST['token'])) {
      return false;
    }

    return $results;

  }


  public function __call($method, $args) {

    if (!$this->user) {
      echo json_encode(['error' => 'invalid token']);
      return false;
    }

    if (!method_exists($this, '_'.$method)) {
      echo json_encode(['error' => 'method restricted']);
      return false;
    }

    call_user_func_array([$this, '_'.$method], $args);

  }

  private function _test($args) {

    hpr('successfully called test()');
    hpr($args);

  }


}

