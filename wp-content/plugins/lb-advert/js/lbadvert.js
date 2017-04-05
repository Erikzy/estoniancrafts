var picker = new Pikaday({ 
    field: document.getElementById('lb-datepicker'),
    format: 'DD.MM.YYYY',
    firstDay: 1,
    minDate: new Date(),
    onSelect: function(date) {

        var link = document.getElementById('lb-advert-buy');
        var baseUrl = link.getAttribute('data-baseurl');

        link.href = baseUrl + '&lb-advert-date=' + this.getMoment().format('DD.MM.YYYY');
        link.style.display = 'block';


    },
    disableDayFn: function(date) {

        // console.log(moment(date).format('YYYY-MM-DD'));
        // console.log(lb_advert_disabled.indexOf(moment(date).format('YYYY-MM-DD')));

    	if( lb_advert_disabled.indexOf(moment(date).format('YYYY-MM-DD')) == -1 ){
    		return false;
    	}

    	return true;
    }
});
