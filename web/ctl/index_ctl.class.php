<?

class index_ctl {

  public $user = false;

  public function __construct() {
    $this->user = user::loggedIn();
  }

  public function index() {

    if ($this->user == false) {
      jade::c('index', [ 'user' => false, 'authURL' => (new google())->authURL()]); 
      return true;
    } 

    jade::c('index', [
      'user' => $this->user->data(), 
      'authURL' => (new google())->authURL(),
      'sendHTML' => jade::c('_box_send', ['user' => $this->user->data()], true, '')
    ]); 

  }

  public function loader() {

    jade::c('_loader', [
      'user' => ($this->user == false) ? false : $this->user->data(), 
      'authURL' => (new google())->authURL()
    ]);

  }

}
