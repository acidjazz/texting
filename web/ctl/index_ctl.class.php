<?

class index_ctl {

  public $user = false;

  public function __construct() {
    $this->user = user::loggedIn();
  }

  public function index() {

    jade::c('index', [
      'user' => ($this->user == false) ? false : $this->user->data(), 
      'authURL' => (new google())->authURL()
    ]); 

  }

}
