
var time = {

  weekdays: ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'],
  months: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
  interval: false,

  obj: {},

  i: function(obj) {

    if (obj) {
      time.scrape(obj);
      return true;
    }

    time.scrape();

    if (time.interval == false && _.focused) {
      time.handlers();
    }

  },

  handlers: function() {
    time.interval = setInterval(time.inter, 5000);
  },

  inter: function() {
    time.scrape();
  },

  scrape: function(obj) {

    if (obj) {
      time.obj = obj;
    } else {
      time.obj = $('.timestamp');
    }

    time.obj.each(function(index, el) {

      var stamp = $(this).data('stamp');
      var date = new Date(stamp*1000);

      var diff = time.diff(stamp);

      $(this).html(time.difftext(diff));
      $(this).attr('title', time.timetext(date));

    });

  },

  diff: function(stamp) {

    var diff = Math.floor(new Date().getTime()/1000-stamp);
    var days = Math.floor(diff/60/60/24);
    var hours = Math.floor(diff/60/60);
    var minutes = Math.floor(diff/60);
    var seconds = Math.floor(diff - (minutes*60));

    var values =  {seconds: seconds, minutes: minutes, hours: hours, days: days};
    
    return values;

  },

  // Wednesday, April 24, 2013 at 5:54pm
  timetext: function(date) {

    var copy = '';

    var hour = date.getHours()%12;
    var minute = date.getMinutes();

    copy += time.weekdays[date.getDay()];
    copy += ', ';
    copy += time.months[date.getMonth()];
    copy += ' ';
    copy += date.getDate();
    copy += ', ';
    copy += date.getFullYear();
    copy += ' at ';
    copy += hour == 0 ? 12 : hour;
    copy += ':';
    copy += minute == 0 ? '00' : minute < 10 ? '0'+minute : minute;
    copy += date.getHours() > 12 ? 'pm' : 'am';

    return copy;

  },


  difftext: function(diff) {

    var copy = '';

    if (diff.minutes > 0) {
      var copy = diff.minutes + ' minute';
      if (diff.minutes > 1) {
        copy += 's'; 
      }
      copy += ' ';
    }


    if (diff.seconds > 0 && diff.minutes < 10) {
      copy += diff.seconds + ' second';
      if (diff.seconds > 1) {
        copy += 's'; 
      }
    }


    if (diff.hours > 0) {
      if (diff.hours < 2) {
        copy += diff.hours + ' hour';
      } else {
        copy = diff.hours + ' hour';
      }
      if (diff.hours > 1) {
        copy += 's'; 
      }
    }
    if (diff.days > 0) {
      copy = diff.days + ' day';
      if (diff.days > 1) {
        copy += 's'; 
      }
    }

    copy += ' ago';

    return copy;

  },


  d: function() {

    clearInterval(time.interval);
    time.interval == false;

  }

}
