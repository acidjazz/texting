
var message = {

  send: function(copy, phone, id, callback) {

    $.get('/api/send', {copy: copy, phone: phone, id: id}, function(response) {

      if (response.success) {
        callback({success: true, date: response.date});
      } else {
        callback({error: true, error: response.status});
      }

    }, 'json');

  }

}
