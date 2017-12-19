// call this from the developer QuironJS and you can control both instances
var calendars = {};

$(document).ready( function() {

    // assuming you've got the appropriate language files,
    // clndr will respect whatever moment's language is set to.
    // moment.lang('ru');

    // here's some magic to make sure the dates are happening this month.
    var thisMonth = moment().format('YYYY-MM');

    var eventArray = [
        { startDate: thisMonth + '-10', endDate: thisMonth + '-14', title: 'Multi-Day Event' },
        { startDate: thisMonth + '-21', endDate: thisMonth + '-23', title: 'Another Multi-Day Event' }
    ];

    // the order of the click handlers is predictable.
    // direct click action callbacks come first: click, nextMonth, previousMonth, nextYear, previousYear, or today.
    // then onMonthChange (if the month changed).
    // finally onYearChange (if the year changed).

    calendars.clndr1 = $('.cal1').clndr({
        events: eventArray,
        // constraints: {
        //   startDate: '2013-11-01',
        //   endDate: '2013-11-15'
        // },
        clickEvents: {
            click: function(target) {
                if(typeof cargarCitas != "undefined"){
                    cargarCitas(target);
                }
            },
            nextMonth: function() {
                QuironJS.log('next month.');
            },
            previousMonth: function() {
                QuironJS.log('previous month.');
            },
            onMonthChange: function() {
                QuironJS.log('month changed.');
            },
            nextYear: function() {
                QuironJS.log('next year.');
            },
            previousYear: function() {
                QuironJS.log('previous year.');
            },
            onYearChange: function() {
                QuironJS.log('year changed.');
            }
        },
        multiDayEvents: {
            startDate: 'startDate',
            endDate: 'endDate'
        },
        showAdjacentMonths: true,
        adjacentDaysChangeMonth: false
    });

    // bind both clndrs to the left and right arrow keys
    $(document).keydown( function(e) {
        if(e.keyCode == 37) {
            // left arrow
            calendars.clndr1.back();
        }
        if(e.keyCode == 39) {
            // right arrow
            calendars.clndr1.forward();
        }
    });

});