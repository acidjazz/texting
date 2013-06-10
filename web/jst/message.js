
var message = {

  send: function(body, phone, id, callback) {

    $.get('/api/send', {body: body, phone: phone, id: id}, function(response) {

      if (response.success) {
        callback({success: true, id: response.id});
      } else {
        callback({error: true, error: response.status});
      }

    }, 'json');

  }

}
