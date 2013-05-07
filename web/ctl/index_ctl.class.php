<?

class index_ctl {

  public function index() {

    $_GET = kdebug::array_generate();


    jade::c('index'); 

  }

}
