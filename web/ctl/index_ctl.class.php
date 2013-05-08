<?

class index_ctl {

  public function index() {

    $auth = (new google())->authURL();

    jade::c('index', ['auth' => $auth]); 

  }

}
