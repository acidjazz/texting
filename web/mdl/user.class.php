<?

class user extends kcol {

  // restrict types of fields
  protected $_types = [
    'created' => 'date',
    'updated' => 'date'
  ];

  // specify your overrode fields
  protected $_ols = [
    'created_readable',
    'created_diff'
  ];

  public function __get($name) {

    switch ($name) {

      case 'created_readable' :
        return date('Y-m-d h:i:s', parent::__get('created')->sec);
        break;


      case 'created_diff' :
        return clock::duration(parent::__get('created')->sec);
        break;

    }

    return parent::__get($name);

  }

  public function save($data=false, $options=[]) {

    if (!$this->exists()) {
      $this->created = new MongoDate();
      $this->logins = 1;
      $this->contacts_import = false;
    } else {
      $this->logins++;
    }

    $this->updated = new MongoDate();

    parent::save($data,$options);

  }

  public static function loggedIn() {

    if ($data = summon::check()) {

      $user = user::i(user::findOne(array('id' => $data['user_id'])));
      
      if ($user->exists()) {
        return $user;
      }

    }

    return false;

  }

  public function tokenExpires() {

    if (!$this->exists()) {
      return false;
    }

    return $this->access_token_expires - time();

  }

}
