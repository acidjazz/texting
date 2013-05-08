<?

class auth_ctl {

  public function callback() {

    if (!isset($_REQUEST['code']) && empty($_REQUEST['code'])) {
      return false;
    }

    $goo = new google();
    $results = $goo->codeVerify($_REQUEST['code']);
    $info = $goo->api('userinfo');

    hpr($info);

  }

}
