
var resizer = {

  mdown: false,
  mdrag: false,
  origin: false,
  width: 200,
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
      resizer.change(size);
    }
    resizer.mdown = false;
    $('.body').removeClass('noselect');
    $('.ghost').css({'left': '0px'});
    resizer.mdrag = false;

  },

  change: function(size) {
    resizer.width = size;
    resizer.el.css({'width': size + 'px'});
    $('.boxes').css({'margin-left': (size) + 'px'});
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
      resizer.change(200);
      resizer.el.addClass('scrollable');
    } else {
      $('.contacts .body, .contacts .search').hide();
      resizer.change(7);
      resizer.el.removeClass('scrollable');
      contacts.search.close();
    }


  }

}

