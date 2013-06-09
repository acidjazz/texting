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

  public function tokenVerify($token) {

    if (!isset($_REQUEST['token']) || empty($_REQUEST['token'])) {
      echo json_entoken(['error' => true, 'message' => 'invalid token']);
      return false;
    }

    $goo = new google();
   
    echo json_encode($goo->tokenVerify($_REQUEST['token']), JSON_PRETTY_PRINT);
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

    if ($this->user->contacts_import_percent != null) {
      echo json_encode(['error' => 'import in progress', 'importAlready' => true]);
      return false;
    }

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
      echo json_encode(['error' => 'import in progress', 'importAlready' => true]);
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

  private function _send() {

    if (!isset($_REQUEST['copy']) || empty($_REQUEST['copy'])) {
      echo json_encode(['error' => true, 'status' => 'invalid copy']);
      return false;
    }

    if (!isset($_REQUEST['phone']) || empty($_REQUEST['phone'])) {
      echo json_encode(['error' => true, 'status' => 'invalid phone']);
      return false;
    }

    if (isset($_REQUEST['id']) && !empty($_REQUEST['id']) && kcol::validId($_REQUEST['id'])) {

      $contact = new contact(new MongoId($_REQUEST['id']));
      if (!$contact->exists() || $contact->_user_id != $this->user->_id) {
        echo json_encode(['error' => true, 'status' => 'invalid contact id']);
        return false;
      }

    }

    $message = new message();
    $message->_user_id = $this->user->_id;
    $message->phone = $_REQUEST['phone'];

    if (isset($contact)) {
      $message->_contact_id = $contact->_id;
      $message->contact_name = $contact->name;
    }

    $message->date = time();
    $message->copy = $_REQUEST['copy'];
    $message->status = 'pending';
    $message->which = 'to';
    $message->save();

    echo json_encode(['success' => true, 'date' => $message->date]);
    //sleep(rand(1,2));
    return true;

  }

  private function _messageHistory($id) {

    $contact = new contact($id);

    if (!$contact->exists() || $contact->_user_id != $this->user->id()) {
      echo json_encode(['error' => 'contact not found']);
      return false;
    }

    sleep(rand(1,2));

    $msgs = [];
    foreach (message::find(['_contact_id' => $contact->_id]) as $message) {
      $msg = message::i($message);
      if ($msg->which == 'to') {
        $msg->whichClass = 'to fromRight';
        $msg->picture = $this->user->picture;
      } else {
        $msg->whichClass = 'from fromLeft';
        $msg->picture = '/img/pictures/'.$contact->picture->{'$id'}.'-40-40.jpeg';
      }
      $msgs[] = $msg->data();
    }

    //$msgs = $this->randomMessages($contact);

    if (count($msgs) < 1) {
      $html = jade::c('_box_body_none', [], true);
    } else {
      $html = jade::c('_box_body', [
        'messages' => $msgs, 
        'user' => $this->user->data(),
        'contact' => $contact->data(),
        ], true);
    }

    echo json_encode(['success' => true, 'html' => $html]);

    return true;

  }

  private function randomMessages($contact) {

    $msgs = [];

    for ($m = rand(0,20); $m != 0; $m--) {

      $msgs[$m]['date'] = time()-rand(10000,1000000);

      $msgs[$m]['which'] = (rand(0,1) ? 'from fromLeft' : 'to fromRight');

      if (rand(0,1)) {
        $msgs[$m]['which'] = 'from fromLeft';

        if ($contact->picture != null) {
          $msgs[$m]['picture'] = '/img/pictures/'.$contact->picture->{'$id'}.'-40-40.jpeg';
        }

      } else {

        if ($this->user->picture != null) {
          $msgs[$m]['picture'] = $this->user->picture;
        }

        $msgs[$m]['which'] = 'to fromRight';

      }

      $words = [];

      for ($s = rand(1, 30); $s != 0; $s--) {
        $word = '';
        for ($w = rand(2,10); $w != 0; $w--) {
          $word .= chr(rand(97, 122));
        }
        $words[] = $word;
      }
      $msgs[$m]['copy'] = implode(' ', $words);

    }

    return $msgs;

  }

}

