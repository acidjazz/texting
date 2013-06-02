var box = {

  loading: false,

  i: function() {

    _.n('loading boxes..');

    setTimeout(function() {
      $('.boxes').removeClass('loading').html('');
      _.n();
    }, 1000);

    box.handlers();

  },


  handlers: function() {
   
    var controls = '.boxes .box .label .controls ';

    $(controls+'.destroy').unbind('click', box.destroy).click(box.destroy);
    $(controls+'.maximize, ' + controls+'.restore').unbind('click', box.maximize).click(box.maximize);

  },

  destroy: function() {

    var t = $(this).closest('.box');
    t.addClass('closing' + (Math.floor(Math.random() * 2) + 1));
    setTimeout(function() { t.remove(); }, 200);

  },

  maximize: function() {

    var t = $(this).closest('.box');

    if (t.hasClass('maximize')) {
      t.find('.maximize').show();
      t.find('.restore').hide();
      t.removeClass('maximize');
      return true;
    }

    t.find('.maximize').hide();
    t.find('.restore').show();
    t.addClass('maximize');
  },

  spawn: function(id) {

    var cbox = $('#box_' + id);

    if (id == box.loading) {
      return false;
    }

    box.loading = id;

    if (cbox.length > 0) {
      cbox.addClass('glow');
      cbox.find('input').focus();
      setTimeout(function() { cbox.removeClass('glow'); }, 2000);
      return true;
    }

    _.n('loading..');

    $.get('/box/content/' + id, function(response) {

      box.loading = false;

      if (response.success) {
        $('.boxes').append(response.html);
        box.scroll();
        _.n();
        box.handlers();
        box.history(id);
      } else {
        _.n('error loading contact : ' + response.error);
      }

    });

  },

  history: function(id) {

    $.get('/api/messageHistory/' + id, function(response) {

      if (response.success) {
        $('#box_' + id + ' .body').removeClass('loading').html(response.html);
        box.scroll();
      }

    });

  },

  scroll: function() {
    var body = $('.box .body');
    body.scrollTop(999999);
  }

}
