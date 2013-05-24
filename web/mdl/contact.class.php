<?

class contact extends kcol {

  // restrict types of fields
  protected $_types = [
    'imported' => 'date'
  ];

  // specify your overrode fields
  protected $_ols = [
    'imported_readable',
    'imported_diff'
  ];

  public function __get($name) {

    switch ($name) {

      case 'imported_readable' :
        return date('Y-m-d h:i:s', parent::__get('imported')->sec);
        break;


      case 'imported_diff' :
        return clock::duration(parent::__get('imported')->sec);
        break;

    }

    return parent::__get($name);

  }

  public function save($data=false, $options=[]) {

    if (!$this->exists()) {
      $this->imported = new MongoDate();
    }

    parent::save($data,$options);

  }


}
