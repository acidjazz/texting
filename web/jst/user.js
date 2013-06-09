
var user = {

  loggedin: false,
  contacts_import: false,
  boxes: [],
  contacts: 200,

  saveInterval: false,

  i: function() {

    if (user.boxes && user.boxes.length > 0) {
      user.boxes = user.boxes.split(',').sort();
      user.loadboxes();
    } else  {
      $('.boxes').removeClass('loading').html('');
    }

    if (user.contacts != 200) {
      resizer.change(user.contacts);
    }

    user.handlers();

  },

  handlers: function() {

    if (user.saveInterval == false && _.focused == true) {
      user.saveInterval = setInterval(user.save, 5000);
    }

  },

  loadboxes: function() {

    _.n('loading boxes..');


    setTimeout(function() {

      $('.boxes').removeClass('loading').html('');

      for (var i = 0, len = user.boxes.length; i != len; i++) {
        box.spawn(user.boxes[i]);
      }

      _.n();

    }, 500);

  },

  save: function() {

    var boxes = [];
    $('.box').each(function(index, el) {
      boxes.push($(this).data('id'));
    });

    boxes.sort();

    // only save if we have a change in windows, f contacts size
    if (( boxes.length == 0 || boxes.equals(user.boxes) ) && resizer.width == user.contacts ) {
      return true;
    } 

    user.boxes = boxes;
    user.contacts = resizer.width;

    _.n('saving state..');
    $.get('/box/save', {boxes: user.boxes, contacts: resizer.width}, function(response) {
      _.n();

    }, 'json');

  },

  d: function() {
    clearInterval(user.saveInterval);
    user.saveInterval = false;
  }

}

