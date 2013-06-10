<?

class android_ctl {

  public $user = false;

  public function __construct() {

    define('KDEBUG_JSON', true);
    header('Content-type: application/json');

    if  ($user = $this->gcmVerify()) {
      $this->user = $user;
    }

    if ($this->user == false) {
      return false;
    }

    // globally allow deivce info updating
    if (isset($_REQUEST['device'])) {
      $device = json_decode($_REQUEST['device']);
      $this->user->device = json_decode($_REQUEST['device']);
    }

  }

  public function __call($method, $args) {


    if (!$this->user && isset($_REQUEST['token']) && !$this->tokenVerify()) {
      echo json_encode(['error' => 'invalid token']);
      return false;
    }

    if (!$this->user) {
      echo json_encode(['error' => 'invalid regid']);
      return false;
    }

    if (!method_exists($this, '_'.$method)) {
      echo json_encode(['error' => 'method restricted']);
      return false;
    }

    call_user_func_array([$this, '_'.$method], $args);

  }


  // verify a passed regid
  private function gcmVerify() {

    // invalid regid
    if (!isset($_REQUEST['regid']) || empty($_REQUEST['regid'])) {
      return false;
    }

    $user = user::i(user::findOne(['regid' =>  $_REQUEST['regid']]));
    if (!$user->exists()) {
      return false;
    }

    return $user;

  }

  // verify a passed token
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


  // initialization.. store 
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


  private function _confirm() {

    if (!isset($_REQUEST['messages'])) {
      echo json_encode(['error' => true, 'status' => 'no messages specified']);
      return false;
    }

    $msgs = json_decode($_REQUEST['messages'], true);

    if ($msgs == null) {
      echo json_encode(['error' => true, 'status' => 'invalid json format']);
      return false;
    }

    foreach ($msgs as $msg) {

      $message = new message(new MongoId($msg['$id']));

      if (!$message->exists()) {
        echo json_encode(['error' => true, 'status' => 'message not found: ' . $msg['$id']]);
        return false;
      }

      if ($message->_user_id != $this->user->_id) {
        echo json_encode(['error' => true, 'status' => 'message ownership error']);
        return false;
      }


      $message->status = 'confirmed';
      $message->date = $msg['date'];
      $message->save();
    }

    echo json_encode(['success' => true, 'status' => 'messages confirmed']);
    return false;

  }

  public function test() {

  }

  private function _import($args) {


    if (!isset($_REQUEST['messages'])) {
      echo json_encode(['error' => true, 'status' => 'no messages specified']);
      return false;
    }

    $msgs = json_decode($_REQUEST['messages'], true);

    if ($msgs == null || $msgs == false || count($msgs) < 1) {
      echo json_encode(['error' => true, 'status' => 'no messages specified']);
      return false;
    }

    // wipe all existing messages .. for now
    message::col()->remove(['_user_id' => $this->user->_id]);

    $binder = new contactBinder($this->user);

    foreach ($msgs as $msg) {

      $message = new message();
      $message->_user_id = $this->user->_id;

      if (isset($msg['address'])) {

        if ($_contact_id = $binder->search($msg['address'])) {
          $message->_contact_id = new MongoId($_contact_id);
        }

      }

      if (isset($msg['type']) && $msg['type'] == 2) {
        $message->which = 'to';
      }  else {
        $message->which = 'from';
      }

      foreach ($msg as $k=>$v) {

        switch ($k) {

          case 'date' :
          case 'date_sent' :
            $message->$k = floatval($v);
            break;
          case 'body' :
            $message->$k = utf8_encode($v);
            break;
          default :
            $message->$k = $v;
            break;
        }

      }

      $message->save();
    }

    echo json_encode(
      ['success' => true, 
      'status' => 'imported '.count($msgs).' messages']
    );

    return true;

  }


}

