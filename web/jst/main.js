
var _ = {

  loop: false,

  i: function() {

    console.log('_ initiation');

    // initiate contact import if we haven't yet
    if (user.loggedin && !user.contacts_import) {
      contacts.import();
    } 

    if (user.loggedin && user.contacts_import) {
      contacts.load();
    }

    setTimeout(function() { $('.profile .left').removeClass('loading'); }, 1000);

    // resizer our contact list to fit the screen
    _.size();

    $(window).resize(_.size);
   
  },

  size: function() {

    // resize our body
    var height = $(window).height() - ($('.header').outerHeight() + 30); 
    $('.contacts .body, .contacts .resizer, .boxes').css({height: height + 'px'});

  },

  // overlay status/progress modal
  s: function(title, detail, progress) {
    if (!title) {
      $('.smodal').addClass('off').removeClass('on');
      $('.body').removeClass('focus');
      $('.overlay').removeClass('on').addClass('off');
      $('.smodal .progress span').css({width: '0px'});
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

      $('.smodal .progress span').css({width: (350*progress/100) + 'px'});
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

  // notice
  n: function(copy, timeout) {

    if (!copy) {
      $('.notice').removeClass('on').addClass('off');
      return true  
    }

    if (!$('.notice').hasClass('on')) {
      $('.notice').removeClass('off').addClass('on');
    }

    if (timeout && timeout !== true) {
      setTimeout(_.n, timeout*1000);
    }

    $('.notice .copy').html(copy);

    return true;

  }

}


