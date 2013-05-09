<?

class auth_ctl {

  public function callback() {

    if (!isset($_REQUEST['code']) && empty($_REQUEST['code'])) {
      return false;
    }

    $goo = new google();

    if ($results = $goo->codeVerify($_REQUEST['code'])) {

      $user = user::i(user::findOne(array('id' => $results['jwt']['id'])));

      if (!$user->exists()) {

        $userinfo = $goo->api('userinfo');

        foreach ($userinfo as $key=>$value) {
          $user->$key = $value;
        }

        //hpr('user does not exist, storeing info');

      } else {

        //hpr('user exists, no need to store info');

      }

      $user->access_token = $goo::$access_token;
      $user->access_token_expires = $results['expires_in'] + time();

      // create our cookie
      $user->sessions = summon::set($user->id, $user->sessions);
      
      $user->save();

      //hpr('saving user data and refreshing cookie');

      //hpr($results);
      Header('Location: /');

    } else {

      hpr('code is invalid');

    }

  }

  public function logout() {

    if ($user = index_ctl::loggedIn()) {
      $user->sessions = summon::remove($user->sessions);
      $user->save();
      Header('Location: /');
    }

  }

}
