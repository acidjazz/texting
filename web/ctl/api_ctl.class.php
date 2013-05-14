<?

class api_ctl {

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
      return false;
    }

    if (!method_exists($this, '_'.$method)) {
      echo json_encode(['error' => 'method restricted']);
      return false;
    }

    call_user_func_array([$this, '_'.$method], $args);

  }

  private function _contactsList() {

    $contacts = [];
    foreach (contact::find(['_user_id' => $this->user->id()]) as $c) {
      $contact = contact::i($c);
      $contacts[$contact->id(true)] = $contact->data();
    }

    $html = jade::c('_contact', ['contacts' => $contacts], true);

    echo json_encode(['success' => true, 'contacts' => $contacts, 'html' => $html], JSON_PRETTY_PRINT);
    return true;

  }

  private function _contactsImport() {

    if (!$this->loggedIn()) {
      echo json_encode(['error' => 'user not logged in']);
      return false;
    }

    if ($this->user->tokenExpires() < 0) {

      $goo = new google();

      if (isset($this->user->refresh_token) && $this->user->refresh_token != null) {

        $results = $goo->refresh($this->user->refresh_token);
        $this->user->access_token = $results['access_token'];
        $this->user->access_token_expires = time() + $results['expires_in'];
        $this->user->save();

      } else {
        echo json_encode([
          'error' => 'new token required', 
          'url' => $goo->authURL('contactsImport', $this->user->email)
        ]);
        return true;
      }

    }

    if ($this->user->contacts_import_percent != null) {
      echo json_encode(['error' => 'import already in progress']);
      return false;
    }

    $this->user->contacts_import_percent = '0';
    $this->user->save();

    sleep(1);
    echo json_encode(['success' => 'contacts refresh initiated']);

    if ($pid = pcntl_fork()) {
      return;
    }

    ob_end_clean();

    if (posix_setsid() < 0) {
      return;
    }

    if ($pid = pcntl_fork()) {
      return;
    }

    // child process code
    $ci = new contactsImport($this->user);
    $ci->drop();
    $ci->import();

  }

  private function _contactsImportProgress() {

    sleep(1);

    if ($this->user->contacts_import_percent == null) {
      echo json_encode(['status' => 'complete', 'date' => $this->user->contacts_import]);
      return true;
    }

    echo json_encode([
      'status' => 'pending',
      'percent' =>$this->user->contacts_import_percent,
      'detail' => $this->user->contacts_import_detail
    ]);

    return true;

  }

  public function messageImport() {

    if (isset($_POST['user_id']) && isset($_POST['messages'])) {

      message::col()->remove(['user_id' => $_POST['user_id']]);

      $messages = json_decode($_POST['messages'], true);

      foreach ($messages as $message) {

        $mObj = new message();
        $mObj->user_id = $_POST['user_id'];

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

    echo json_encode(['error' => 'invalid parameters']);

  }

}

