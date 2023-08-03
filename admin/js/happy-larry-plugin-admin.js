(function( $ ) {
	'use strict';

	$(function() {
		// OUT OF STOCK FOR PERIOD
		$('#save_settings').on('click', function() {
			let timePeriod = $('#time_period').val();

			$.ajax({
				url: ajax_object.ajax_url,
				type: 'POST',
				data: {
					action: 'update_admin_plugin_settings',
					timePeriod
				},
				success: function(response) {
					if(response.success){
						location.reload();
					}
				},
				error: function() {
					alert('une erreur est survenue, merci de contacter votre webmaster')
				}
			});
		});
	});

})( jQuery );
