var box = {

  loading: false,
  index: 0,
  messageId: 0,
  sendHTML: 'Missing box.sendHTML',

  i: function() {

    box.handlers();

  },


  handlers: function() {
   
    var controls = '.boxes .box .label .controls ';
    // focus input on click.. need to event.stopPropagation() on any other click event
    $('.boxes .box').unbind('click', box.activate).click(box.activate);

    $(controls+'.destroy').unbind('click', box.destroy).click(box.destroy);
    $(controls+'.maximize, ' + controls+'.restore').unbind('click', box.maximize).click(box.maximize);



    $('.boxes .box input').unbind('keyup', box.typing).keyup(box.typing);
    $('.boxes .box .enter').unbind('click', box.enter).click(box.enter);

  },

  activate: function() {
    $(this).find('input').focus();
  },

  typing: function(event) {

    if (event.keyCode == 13) {
      var t = $(this);
      var copy = t.val();
      if (copy == '') {
        return true;
      }

      var current = t.closest('.box');
      box.send(current, copy, current.data('phone'), current.data('id'));
      t.val('');
    }

  },

  enter: function(event) {

    var t = $(this);
    var message = t.prev().val();
    if (message == '') {
      return true;
    }
    var current = t.closest('.box');
    box.send(current, message, current.data('phone'), current.data('id'));
    t.prev().val('');

    event.stopPropagation();

  },

  send: function(current, copy, phone, id) {

    var messageId = ++box.messageId;

    var html = box.sendHTML.replace(/{{copy}}/, copy).replace(/{{id}}/, messageId);
    current.find('.body').append(html);
    box.scroll(current);

    message.send(copy, phone, id, function(results) {
      if (results.success) {
        var date_div = $('#messageId_' + messageId + ' .date');
        date_div.html(
          '<abbr class="timestamp" data-stamp="'+results.date+'">loading</abbr');
        time.i(current.find('.timestamp'));
        box.scroll(current);
      }
    });

  },

  destroy: function(event) {

    var t = $(this).closest('.box');
    t.addClass('closing' + (Math.floor(Math.random() * 2) + 1));
    setTimeout(function() { t.remove(); }, 200);
    event.stopPropagation();

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
        $('#box_' + id).find('input').attr('disabled', false);
        time.i();
      }

    });

  },

  scroll: function(box) {

    if (box == undefined) {
      var body = $('.box .body');
    } else {
      var body = box.find('.body');
    }

    body.scrollTop(999999);
  }

}
