
var _ = {

  loop: false,

  i: function() {

    console.log('_ initiation');

    // initiate contact import if we haven't yet
    if (user.loggedin && !user.contacts_import) {
      contacts.import();
    } else {
      contacts.load();
    }

  },

  // overlay status/progress modal
  s: function(title, detail, progress) {
    if (!title) {
      $('.smodal').addClass('off').removeClass('on');
      $('.body').removeClass('focus');
      $('.overlay').removeClass('on').addClass('off');
      return true;
    }

    if (!$('.smodal').hasClass('on')) {
      $('.smodal').removeClass('off').addClass('on');
      $('.body').addClass('focus');
      $('.overlay').removeClass('off').addClass('on');
    }

    $('.smodal .title').html(title);
    if (detail) {
      $('.smodal .detail').html(detail);
    }

    if (progress) {
      $('.smodal .progress .copy').html(progress + '%');

      $('.smodal .progress span').css({'width':(350*progress/100) + 'px'});
      if (!$('.progress').hasClass('on')) {
        $('.progress').removeClass('off').addClass('on');
      }
    } else {
      if ($('.progress').hasClass('on')) {
        $('.progress').removeClass('on').addClass('off');
      }

    }

    return true;

  },

  n: function(copy) {

    if (!copy) {
      $('.notice').removeClass('on').addClass('off');
      return true  
    }

    if (!$('.notice').hasClass('on')) {
      $('.notice').removeClass('off').addClass('on');
    }

    $('.notice .copy').html(copy);

    return true;

  }

}


