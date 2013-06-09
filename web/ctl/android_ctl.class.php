<?

class android_ctl {

  public $user = false;

  public function __construct() {

    define('KDEBUG_JSON', true);
 //   header('Content-type: application/json');

     if ($results = $this->tokenVerify()) {
       $this->user = user::i(user::findOne(array('id' =>  $results['id'])));
     } else {
       return false;
     }
  }

  private function gcmVerify() {

    // invalid regid
    if (!isset($_REQUEST['regid']) || empty($_REQUEST['regid'])) {
      return false;
    }

    $user = user::i(user::findOne(array('regid' =>  $_REQUEST['regid'])));
    if (!$user->exists()) {
      return false;
    }

    return $user;

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


  public function init() {

    $goo = new google();

    if ($results = $this->tokenVerify()) {
      $user = user::i(user::findOne(array('id' =>  $results['id'])));
    } else {
      echo json_encode(['error' => true, 'status' => 'invalid regid']);
    }

    if (!isset($_REQUEST['regid']) || empty($_REQUEST['regid'])) {
      echo json_encode(['error' => true, 'status' => 'invalid regid']);
      return false;
    }

    if (!isset($_REQUEST['phone']) || empty($_REQUEST['phone'])) {
      echo json_encode(['error' => true, 'status' => 'invalid phone']);
      return false;
    }

    if (!isset($_REQUEST['device']) || empty($_REQUEST['device'])) {
      echo json_encode(['error' => true, 'status' => 'invalid device']);
      return false;
    }

    $user->id = $results['id'];
    $user->email = $results['email'];

    $user->regid = $_REQUEST['regid'];
    $user->phone = $_REQUEST['phone'];
    $user->device = json_decode($_REQUEST['device']);

    $user->save();

    echo json_encode(['success' => true, 'status' => 'initialization successful']);
    return true;

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

  private function _messageImport($args) {

    /*
    echo json_encode([['copy' => 'bob'],['copy' => 'suzy']]);
    return true;
    */

    if (!isset($_REQUEST['messages'])) {
      echo json_encode(['error' => 'no messages specified']);
      return false;
    }

    $messages = json_decode($_REQUEST['messages'], true);

    if ($messages == null || $messages == false || count($messages) < 1) {
      echo json_encode(['error' => 'no messages specified']);
      return false;
    }

    // wipe all existing messages .. for now
    message::col()->remove(['user_id' => $this->user->id]);

    foreach ($messages as $message) {

      $mObj = new message();
      $mObj->user_id = $this->user->id;

      foreach ($message as $k=>$v) {
        $mObj->$k = $v;
      }

      $mObj->save();
    }

    echo json_encode(
      ['success' => true, 
      'status' => 'imported '.count($messages).' messages']
    );

    return true;

  }


}

