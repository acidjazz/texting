
var user = {

  loggedin: false,
  contacts_import: false,
  boxes: false,

  saveInterval: false,

  i: function() {

    if (user.boxes.length > 0) {

      user.boxes = user.boxes.split(',').sort();
      user.loadboxes();

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

    $('.boxes').removeClass('loading').html('');
    for (var i = 0, len = user.boxes.length; i != len; i++) {
      box.spawn(user.boxes[i]);
    }

    _.n();

  },

  save: function() {

    var boxes = [];
    $('.box').each(function(index, el) {
      boxes.push($(this).data('id'));
    });

    boxes.sort();

    if (boxes.length == 0 || boxes.compare(user.boxes)) {
      return true;
    } 

    user.boxes = boxes;

    $.get('/box/save', {boxes: user.boxes}, function(response) {

    }, 'json');

  },

  d: function() {
    clearInterval(user.saveInterval);
    user.saveInterval = false;
  }

}

