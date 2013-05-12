

var contacts = {

  pulling: false,
  interval: false,

  import: function() {

    _.s('Importing Contacts', 'retrieving progress', '0');

    $.get('/api/contactsImport', function(response) {

      if (response.success) {
        contacts.loop = true;
        contacts.interval = setInterval(contacts.progress, 100);
      } 

      if (response.error) {
        _.s('Error Imprting Contacts', response.error);
      }

    });

  },

  progress: function() {

    if (contacts.pulling) {
      return true;
    }

    contacts.pulling = true;

    $.get('/api/contactsImportProgress', function(response) {

      if (response.status && response.status == 'pending') {
        _.s('Importing Contacts', response.detail, response.percent);
      } else {
        user.contacts_import = response.date;
        clearInterval(contacts.interval);
        contacts.loop = false;
        _.s();
      }

      contacts.pulling = false;

    });

  }

}
