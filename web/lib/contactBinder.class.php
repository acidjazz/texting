<?

class contactBinder {

  public $user = false;
  public $contacts = [];

  public function __construct($user) {
    $this->user = $user;
    $this->_pullContacts();
  }

  public function _pullContacts() {

    foreach (contact::find(['_user_id' => $this->user->_id]) as $ct) {
      $contact = contact::i($ct);
      $phones = $contact->phones;

      foreach ($phones as $key=>$phone) {
        $phones[$key] = $this->strip($phone);
      }

      $this->contacts[$contact->id(true)] = $phones;

    }


  }

  public function strip($phone) {

    $stripped = str_replace(['-','+',' ','(', ')'], ['','','', '', ''], $phone);

    if ($stripped{0} == 1) {
      $stripped = substr($stripped, 1);
    }

    return $stripped;

  }

  public function search($number) {

    $number = $this->strip($number);

    foreach ($this->contacts as $id=>$phones) {
      foreach ($phones as $phone) {
        if ($phone == $number) {
          return $id;
        }
      }
    }

    return false;

  }

}


