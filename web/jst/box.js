var box = {

  clear: '<div class="bc clear"></div>',


  i: function() {

    _.n('loading boxes..');

    setTimeout(function() {
      $('.boxes').removeClass('loading').html('');
      _.n();
    }, 1000);

    box.handlers();

  },


  handlers: function() {

    $('.boxes .box .label .controls .destroy').unbind('click', box.destroy).click(box.destroy);

  },

  destroy: function() {

    var t = $(this).closest('.box');
    t.addClass('closing');
    setTimeout(function() { t.remove(); }, 200);

  },

  spawn: function(id) {

    var cbox = $('#box_' + id);
    if (cbox.length > 0) {
      cbox.addClass('glow');
      setTimeout(function() { cbox.removeClass('glow'); }, 2000);
      return true;
    }

    _.n('loading..');

    $.get('/box/content/' + id, function(response) {

      if (response.success) {
        var reg = new RegExp(box.clear,'g');
        var html = $('.boxes').html().replace(reg, '');
        $('.boxes').html(html + response.html + box.clear);
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
        $('#box_' + id + ' .body').html(response.html);
        box.scroll();
      }

    });

  },

  scroll: function() {
    var body = $('.box .body');
    body.scrollTop(body[0].scrollHeight + body.height());
  }

}
