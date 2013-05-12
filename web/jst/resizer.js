var resizer = {

  mdown: false,
  mdrag: false,
  origin: false,
  el: false,

  i: function() {

    $('.resizer').mousedown(resizer.down);
    $(document).mouseup(resizer.up);
    $(document).mousemove(resizer.move);
    $('.resizer').dblclick(resizer.dbl);

    resizer.el= $($('.resizer').data('for'));

  },

  down: function(event) {
    
    resizer.mdown = true;
    resizer.origin = event.clientX;

  },

  up: function() {

    if (resizer.mdown && resizer.mdrag) {
      resizer.el.css({'width': (resizer.el.width() + event.clientX-resizer.origin) + 'px'});
    }
    resizer.mdown = false;
    $('.ghost').css({'left': '0px'});
    resizer.mdrag = false;

  },

  move: function() {

    if (resizer.mdown) {
      $('.ghost').css({'left': (event.clientX-resizer.el.width()) + 'px'});
      resizer.mdrag = true;
    }


  },

  dbl: function() {

    if (resizer.el.width() == '15') {
      resizer.el.css('width', '100px');
    } else {
      resizer.el.css('width', '15px');
    }


  }

}
