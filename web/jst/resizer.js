var resizer = {

  mdown: false,
  mdrag: false,
  origin: false,
  el: false,

  i: function() {

    $('.resizer').mousedown(resizer.down);
    $(document).mouseup(resizer.up);
    $(document).mousemove(resizer.move);
    $('.resizer').click(resizer.dbl);
    $('.contacts .minimize').click(resizer.dbl);

    resizer.el= $($('.resizer').data('for'));

  },

  down: function(event) {
    
    resizer.mdown = true;
    $('.body').addClass('noselect');
    resizer.origin = event.clientX;

  },

  up: function(event) {

    if (resizer.mdown && resizer.mdrag) {
      var size = resizer.el.width() + event.clientX-resizer.origin
      resizer.el.css({'width': size + 'px'});
      $('.boxes').css({'margin-left': (size) + 'px'});
    }
    resizer.mdown = false;
    $('.body').removeClass('noselect');
    $('.ghost').css({'left': '0px'});
    resizer.mdrag = false;

  },

  move: function(event) {

    if (resizer.mdown) {
      $('.ghost').css({'left': (event.clientX-resizer.el.width()) + 'px'});
      resizer.mdrag = true;
    }


  },

  dbl: function() {

    if (resizer.el.width() == '7') {
      $('.contacts .body, .contacts .search').show();
      resizer.el.css('width', '200px');
      $('.boxes').css({'margin-left': '200px'});
      resizer.el.addClass('scrollable');
    } else {
      $('.contacts .body, .contacts .search').hide();
      resizer.el.css('width', '7px');
      $('.boxes').css({'margin-left': '7px'});
      resizer.el.removeClass('scrollable');
      contacts.search.close();
    }


  }

}
