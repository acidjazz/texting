<?

class contactsImport {

  public $user = false;
  public function __construct($user) {
    $this->user = $user;
  }

  public function drop() {

    contact::drop(array('_user_id' => $this->user->id()));
    contact::grid()->drop(array('_user_id' => $this->user->id()));
    return true;

  }

  public function update($percent, $detail) {

    $this->user->contacts_import_percent = round($percent);
    $this->user->contacts_import_detail = $detail;
    $this->user->save();

  }

  public function complete() {

    unset($this->user->contacts_import_percent);
    unset($this->user->contacts_import_detail);
    //$this->user->contacts_import = time();
    $this->user->save();

    return true;

  }

  public function import() {

    $goo = new google($this->user->access_token);

    $groups = $goo->api('https://www.google.com/m8/feeds/groups/default/full/',
      ['alt' => 'json', 'v' => '2']);

    if (!isset($groups['feed']['entry'])) {
      return false;
    }

    $totalContacts = 0;
    $currentContacts = 0;

    foreach ($groups['feed']['entry'] as $grp) {

      $groupname = false;

      if (isset($grp['gContact$systemGroup'])) {
        $groupname = $grp['gContact$systemGroup']['id'];
      } else {
        $groupname = $grp['title']['$t'];
      }

      $i = 1;

      while (true) {

        $contacts = $goo->api('https://www.google.com/m8/feeds/contacts/default/full/',
          [
            'alt' => 'json', 
            'max-results' => 50,
            'group' => $grp['id']['$t'], 
            'start-index' => $i
          ]
        );


        if (!isset($contacts['feed']['entry'])) {
          $this->complete();
          break;
        }

        if (isset($contacts['feed']['openSearch$totalResults']['$t'])) {
          if ($contacts['feed']['openSearch$totalResults']['$t'] > $totalContacts) {
            $totalContacts = $contacts['feed']['openSearch$totalResults']['$t'];
          }
        }

        foreach($contacts['feed']['entry'] as $entry) {

          // not sure this is needed.. not really storing anything
          $contact = contact::i(contact::findOne(array('id' => $entry['id']['$t'])));

          if ($contact->exists()) {
            // check for more groups
            $groups = $contact->groups;
            if (!in_array($groupname, $groups)) {
              array_push($groups, $groupname);
              $contact->groups = $groups;
              $contact->save();
            }
            break;
          }

          if ($currentContacts%2) {
            $this->update($currentContacts*100/$totalContacts, 
              'importing '.$entry['title']['$t']);
          }

          $contact->_user_id = $this->user->id();
          $contact->id = $entry['id']['$t'];
          $contact->name = $entry['title']['$t'];
          $contact->groups = [$groupname];
          
          $emails = [];
          if (isset($entry['gd$email'])) {
            foreach ($entry['gd$email'] as $email) {
              $emails[] = $email['address'];
            }
            $contact->emails = $emails;
          }

          $phones = [];
          if (isset($entry['gd$phoneNumber'])) {
            foreach ($entry['gd$phoneNumber'] as $phone) {
              $phones[] = $phone['$t'];
            }
            $contact->phones = $phones;
          }

          $grid = contact::grid();
          foreach ($entry['link'] as $link) {

            if (isset($contact->picture)) {
              continue;
            }

            if (strpos($link['type'], 'image') !== false) {
              $image = $goo->api($link['href'], [], 'get', 'raw');
              if ($image != 'Photo not found') {
                $contact->picture = $grid->storeBytes($image, [
                  '_contact_id' => $contact->id(),
                  '_user_id' => $this->user->id()
                ]);
              }
            }
          }

          $contact->save();
          $currentContacts++;

        }

        $i += 50;

      }

    }

    return true;

  }


}
