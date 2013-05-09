<?

class user extends kcol {

  // restrict types of fields
  protected $_types = [
    'created' => 'date'
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
    } else {
      $this->logins++;
    }

    $this->updated = new MongoDate();

    parent::save($data,$options);

  }

}
