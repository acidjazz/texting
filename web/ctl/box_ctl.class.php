<?

class box_ctl {

  public $user = false;

  public function __construct() {
    define('KDEBUG_JSON', true);
    header('Content-type: application/json');
    $this->user = user::loggedIn();
  }

  private function loggedIn() {
    if (!$this->user) {
      echo json_encode(['error' => 'not logged in']);
      return false;
    }

    return true;
  }

  public function __call($method, $args) {

    if (!$this->loggedIn()) {
      echo json_encode(['error' => 'no auth code']);
      return false;
    }

    if (!method_exists($this, '_'.$method)) {
      echo json_encode(['error' => 'method restricted']);
      return false;
    }

    call_user_func_array([$this, '_'.$method], $args);

  }

  private function _content($id) {


    $contact = new contact($id);

    if (!$contact->exists() || $contact->_user_id != $this->user->id()) {
      echo json_encode(['error' => 'contact not found']);
      return false;
    }

    $html = jade::c('_box', ['contact' => $contact->data()], true);

    echo json_encode(['success' => true, 'html' => $html]);
    return true;


  }


}
