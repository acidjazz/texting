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

  private function _save() {

    if (isset($_REQUEST['contacts']) && is_numeric($_REQUEST['contacts']) 
      && isset($_REQUEST['boxes']) && is_array($_REQUEST['boxes'])) {

      $ids = [];
      foreach ($_REQUEST['boxes'] as $box_id) {

        if (!kcol::validId($box_id)) {
          echo json_encode(['error' => true, 'status' => 'invalid id']);
          return false;
        }
        $ids[] = $box_id;

      }

      $this->user->state = ['contacts' => $_REQUEST['contacts'], 'boxes' => $ids];
      $this->user->save();
      echo json_encode(['success' => true, 'status' => 'save successful']);
      return true;

    }

    echo json_encode(['error' => true, 'status' => 'no boxes specified']);

  }

}
