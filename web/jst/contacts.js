

var contacts = {

  pulling: false,
  interval: false,
  list: {},

  handlers: function() {
    $('.contacts .contact').dblclick(contacts.select);
  },

  import: function() {

    _.s('Importing Contacts', 'retrieving progress', '0');

    $.get('/api/contactsImport', function(response) {

      if (response.success) {
        contacts.loop = true;
        contacts.interval = setInterval(contacts.progress, 100);
      } 

      if (response.error) {

        // probably a token refresh
        if (response.url) {
          location.href = response.url;
          return true;
        }

        _.s('Error Importing Contacts', response.error);

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
        contacts.load();
        _.s();
      }

      contacts.pulling = false;

    });

  },

  load: function() {

    _.n('loading contacts..', true);

    $.get('/api/contactsList', function(response) {

      if (response.success) {

        $('.contacts').removeClass('loading').addClass('scrollable');
        $('.contacts .body').html(response.html).removeClass('loading');
        contacts.list = response.contacts;
        contacts.handlers();
        _.n('contacts loaded', 1);

      }

    });

  },

  select: function() {


  }


}
