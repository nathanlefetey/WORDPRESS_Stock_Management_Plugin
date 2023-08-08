<?php
    $batch_size = 100;  // Nombre de commandes à traiter à la fois
    $paged = 1;         // Page initiale

    while (true) {
        $orders = wc_get_orders(array(
            'limit' => $batch_size,
            'paged' => $paged,
        ));

        // Si aucune commande n'est trouvée, sortir de la boucle
        if (empty($orders)) {
            break;
        }



        foreach ($orders as $order) {
            foreach ($order->get_items() as $item) {
                $item_data = $item->get_data();
                $order_id = $item_data['order_id'];
                $product_name = $item_data['name'];
                $quantity = $item_data['quantity'];
                $product_id = $item_data['product_id'];
//                echo '<pre>'; print_r(get_post_meta($product_id)); echo '</pre>';
//                echo '<pre>'; print_r($item->get_data()); echo '</pre>';
                $booking_data = $item->get_meta('yith_booking_data');
                if ($booking_data) {
                    $from = $booking_data['from'] ?? null;
                    $to = $booking_data['to'] ?? null;

                    $order_booking_data = array(
                        '_product_name' => $product_name,
                        '_product_id' => $product_id,
                        '_quantity' => $quantity,
                        '_booking-from' => $from,
                        '_booking-to' => $to
                    );
                }


            }
            $all_orders_booking_data[] = $order_booking_data;
        }


        // Incrémenter la page pour le prochain lot de commandes
        $paged++;
    }

// Initialisation d'un tableau pour stocker les produits loués par date
$products_rented_per_day = [];

foreach ($all_orders_booking_data as $order_booking_data) {
    for ($date = $order_booking_data['_booking-from']; $date <= $order_booking_data['_booking-to']; $date += 86400) { // 86400 est le nombre de secondes dans une journée
        $formatted_date = date('Y-m-d', $date);
        if (!isset($products_rented_per_day[$formatted_date])) {
            $products_rented_per_day[$formatted_date] = [];
        }
        if (!isset($products_rented_per_day[$formatted_date][$order_booking_data['_product_name']])) {
            $products_rented_per_day[$formatted_date][$order_booking_data['_product_name']] = 0;
        }
        $products_rented_per_day[$formatted_date][$order_booking_data['_product_name']] += $order_booking_data['_quantity'];
    }
}

?>

<div id="calendar"></div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        let calendarEl = document.getElementById('calendar');

        let calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            eventContent: function(arg) {
                let innerHtml = '';
                for (const [product, count] of Object.entries(arg.event.extendedProps.products)) {
                    innerHtml += `${product}: ${count} loués<br>`;
                }
                return {
                    html: innerHtml
                };
            },
            events: <?php echo json_encode(array_map(function($date, $products) {
                return [
                    'title' => '', // Pas de titre, car nous personnalisons le contenu
                    'start' => $date,
                    'products' => $products
                ];
            }, array_keys($products_rented_per_day), $products_rented_per_day)); ?>
        });

        calendar.render();
    });
</script>
