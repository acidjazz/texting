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

  up: function(event) {

    if (resizer.mdown && resizer.mdrag) {
      var size = resizer.el.width() + event.clientX-resizer.origin
      resizer.el.css({'width': size + 'px'});
      $('.boxes').css({'margin-left': (size+10) + 'px'});
    }
    resizer.mdown = false;
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

    if (resizer.el.width() == '9') {
      resizer.el.css('width', '200px');
      $('.boxes').css({'margin-left': '210px'});
      resizer.el.addClass('scrollable');
    } else {
      resizer.el.css('width', '9px');
      $('.boxes').css({'margin-left': '19px'});
      resizer.el.removeClass('scrollable');
    }


  }

}
