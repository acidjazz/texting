<?

class auth_ctl {

  public $user = false;

  public function __construct() {
    $this->user = user::loggedIn();
  }

  public function callback() {

    if (!isset($_REQUEST['code']) && empty($_REQUEST['code'])) {
      return false;
    }

    $goo = new google();

    if ($results = $goo->codeVerify($_REQUEST['code'])) {

      $user = user::i(user::findOne(array('id' => $results['jwt']['id'])));

      if (!$user->exists()) {

        $userinfo = $goo->api('https://www.googleapis.com/oauth2/v2/userinfo');

        foreach ($userinfo as $key=>$value) {
          $user->$key = $value;
        }

      }

      $user->access_token = $goo::$access_token;
      if (isset($results['refresh_token'])) {
        $user->refresh_token = $results['refresh_token'];
      }
      $user->access_token_expires = $results['expires_in'] + time();

      // create our cookie
      $user->sessions = summon::set($user->id, $user->sessions);
      
      $user->save();

      Header('Location: /');

    } else {

      hpr('code is invalid');

    }

  }

  public function logout() {

    if ($this->user) {
      $this->user->sessions = summon::remove($user->sessions);
      $this->user->save();
      Header('Location: /');
    }

  }

}
