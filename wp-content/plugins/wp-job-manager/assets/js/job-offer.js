(function($) {
    
    var JOffer = function() {
        this.classnames = {
            deadline: '.single-job_listing .entry-content .make-offer .deadline-info .deadline',
            counter: 'counter'
        };
    };
    
    JOffer.prototype.init = function() {console.log('siia');
        this.initDeadlineCounter();
    };
    
    JOffer.prototype.initDeadlineCounter = function() {
        var self = this;
        if($(this.classnames.deadline).length > 0) {
            $(this.classnames.deadline).each(function() {
                self.createCounter($(this));
            });
        }
    };
    
    JOffer.prototype.createCounter = function($element) {
        var deadline = $element.data('date');
        this.updateCounter($element, deadline);
    };
    
    JOffer.prototype.updateCounter = function($element, deadline) {
        var self = this;
        var time = this.getTimeLeft(deadline);
        this.updateCounterElements($element, time);
        
        setTimeout(function() {
            self.updateCounter($element, deadline);
        }, 1000);
    };
    
    JOffer.prototype.updateCounterElements = function($element, time) {
        $element.data('time', time);
        $element.find('.days').html(time.days);
        $element.find('.hours').html(time.hours);
        $element.find('.minutes').html(time.minutes);
        $element.find('.seconds').html(time.seconds);
    };
    
    JOffer.prototype.getTimeLeft = function(deadline) {
        var deadlineArr = deadline.split(".");
        var deadlineDay = deadlineArr[0];
        var deadlineMonth = deadlineArr[1];
        var deadlineYear = deadlineArr[2];
        var date_now = new Date().getTime();
        var date_future = new Date(deadlineMonth + " " + deadlineDay + " " + deadlineYear).getTime();
        var delta = Math.abs(date_future - date_now) / 1000;
        var days = Math.floor(delta / 86400);
        delta -= days * 86400;
        var hours = Math.floor(delta / 3600) % 24;
        delta -= hours * 3600;
        var minutes = Math.floor(delta / 60) % 60;
        delta -= minutes * 60;
        var seconds = Math.round(delta % 60);
        
        return {
            deadline: deadline,
            days: days,
            hours: hours,
            minutes: minutes,
            seconds: seconds
        };
    };
    
    var jo = new JOffer();
    jo.init();
    
})(jQuery);