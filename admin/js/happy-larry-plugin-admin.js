(function( $ ) {
	'use strict';

	function collectData(elements) {
		let result = {};
		elements.each(function() {
			let input = $(this);
			result[input.data('product-id')] = input.val() !== '' ? input.val() : "";
		});
		return result;
	}

	$(function() {
		// OUT OF STOCK FOR PERIOD
		$('#send_period').on('click', function() {
			let horsStockProductSimple = $('.ht_day');
			let restockCategories = $('.restock_category');
			let rentCategories = $('.rent_category');
			let timePeriod = $('.time_period');

			let allRestockCategories = collectData(restockCategories);
			let allRentCategories = collectData(rentCategories);
			let allHorsStockProductSimple = collectData(horsStockProductSimple);
			let allTimePeriod = collectData(timePeriod);

			$.ajax({
				url: ajax_object.ajax_url,
				type: 'POST',
				data: {
					action: 'update_product_stock_and_category',
					allRestockCategories,
					allRentCategories,
					allHorsStockProductSimple,
					allTimePeriod
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
