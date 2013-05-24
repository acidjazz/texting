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
        _.n();
        box.handlers();
      } else {
        _.n('error loading contact : ' + response.error);
      }


    });

  }


}
