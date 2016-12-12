function getTimeRemaining(endtime) {
    var t = Date.parse(endtime) - Date.parse(new Date());
    var seconds = Math.floor((t / 1000) % 60);
    var minutes = Math.floor((t / 1000 / 60) % 60);
    var hours = Math.floor((t / (1000 * 60 * 60)) % 24);
    var days = Math.floor(t / (1000 * 60 * 60 * 24));

    if (parseInt(seconds, 10) < 10) {
        seconds = '0' + seconds;
    }
    if (parseInt(minutes, 10) < 10) {
        minutes = '0' + minutes;
    }
    if (parseInt(hours, 10) < 10) {
        hours = '0' + hours;
    }

    return {
        'total': t,
        'days': days,
        'hours': hours,
        'minutes': minutes,
        'seconds': seconds
    };
}

function runCountdown(options) {
    var timeInterval;
    var t;
    var timeRemaining = $('#timediv');
    var endTime = options.endTime;
    var redirectUrl = options.redirectUrl;

    function updateCounter() {
        t = getTimeRemaining(endTime);
        timeRemaining.text(t.hours + ':' + t.minutes + ':' + t.seconds);
        if (t.total <= 0) {
            clearInterval(timeInterval);
            timeInterval = null;
            window.location = redirectUrl;
        }
    }
    updateCounter(); // run function once at first to avoid delay
    timeInterval = setInterval(updateCounter, 1000);
}

