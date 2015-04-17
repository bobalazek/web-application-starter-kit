var Application = function () {
	var initialized = false;

	return {
		initialize: function()
		{
			// Some stuff here
			jQuery(document).ready( function() {
				Application.tooltipsInitialize();
				Application.timeAgoInitialize();

                jQuery('#preloader').fadeOut(); // Hide preloader, when everything is ready...

                initialized = true;
                console.log('Application Initialized');
            });
		},
		tooltipsInitialize: function() {
			jQuery('[data-toggle="tooltip"]').tooltip();
		},
		timeAgoInitialize: function() {
			function updateTime() {
				var now = moment();

				jQuery('time.time-ago').each( function() {
					var time = moment(jQuery(this).attr('datetime'));

					jQuery(this).text(time.from(now));
				});
			}

			updateTime();

			setInterval(updateTime, 10000);
		},
	}
}();
