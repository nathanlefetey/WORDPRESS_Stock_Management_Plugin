(function( $ ) {
    'use strict';

    $(function() {
        // OUT OF STOCK FOR PERIOD
        $('#send-date').on('click', function() {
            let selectedDate = $('.date-selector').val();
            console.log(selectedDate)
            $.ajax({
                url: ajax_object.ajax_url,
                type: 'POST',
                data: {
                    action: 'update_product_stock_calendar',
                    selectedDate
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
