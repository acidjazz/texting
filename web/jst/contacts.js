

var contacts = {

  pulling: false,
  interval: false,
  list: {},

  handlers: function() {
    $('.contacts .contact').click(contacts.select);
    $('.contacts .search .close').click(contacts.search.close);
    $('.contacts .search input').keyup(contacts.search.keyup);
    $('.header .profile .tools .re-import').click(contacts.import);
    contacts.search.close();
  },

  search: {

    keyup: function(e) {

      var val = $(this).val();
      var reg = new RegExp(val, 'ig');

      var matches = 0;

      contacts.search.reset();

      if (val == '') {
        $('.search .close').removeClass('on');
        return true;
      }
      $('.search .close').addClass('on');

      $('.contact').each(function(i, obj) {

        var ndiv = $(obj).find('.name');

        if (!$(obj).data('name').match(reg)) {
          $(obj).hide();
        } else {
          ndiv.html(ndiv.html().replace(reg, '<span>' + val + '</span>'));
          matches++;
        }
      });

      if (matches == 0) {
        $('.contacts .body .none').show();
        $('.contacts .body .none').unbind('hover').hover(contacts.showimport, contacts.hideimport);
      } else {
        $('.contacts .body .none').hide();
      }

    },

    reset: function() {

      // reset .. show them all and remove the highlights
      $('.contact').show().each(function(i, obj) {
        $(obj).find('.name').html($(obj).data('name'));
      });

    },

    close: function() {

      $('.contacts .search input').val('');
      contacts.search.reset();
      $(this).removeClass('on');

    }

  },

  showimport: function() { $('.header .profile').addClass('showimport'); },
  hideimport: function() { $('.header .profile').removeClass('showimport'); },

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

        // possible import already in progress
        if (response.importAlready) {
          contacts.loop = true;
          contacts.interval = setInterval(contacts.progress, 100);
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
        box.i();

      }

      if (response.error && response.importAlready) {
        _.s('import in progress..');
        contacts.loop = true;
        contacts.interval = setInterval(contacts.progress, 100);
      }

    });

  },

  select: function() {

    box.spawn($(this).data('id'));

  }


}
