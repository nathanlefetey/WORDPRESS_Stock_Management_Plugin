<?php
$user_id = get_current_user_id();
$key = 'user_calendar_selected_date';
$selected_date = get_user_meta($user_id, $key, true);

if (!$selected_date || !strtotime($selected_date)) {
    // Traitez l'erreur ou fournissez une valeur par défaut
    $formattedDate = '';
    $selected_date_timestamp = false;
} else {
    // Assurez-vous de définir la locale avant d'utiliser strftime
    setlocale(LC_TIME, 'fr_FR.UTF8', 'fr.UTF8', 'fr_FR.UTF-8', 'fr.UTF-8');
    $formattedDate = strftime("%d %B %Y", strtotime($selected_date));
    $selected_date_timestamp = strtotime($selected_date);
}

$batch_size = 100;  // Nombre de commandes à traiter à la fois
$paged = 1;         // Page initiale

$all_orders_booking_data = array();

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
        $contains_booking_item = false; // Pour vérifier si la commande contient un élément de réservation

        foreach ($order->get_items() as $item) {
            $item_data = $item->get_data();
            $order_id = $item_data['order_id'];
            $product_name = $item_data['name'];
            $quantity = $item_data['quantity'];
            $product_id = $item_data['product_id'];

            $booking_data = $item->get_meta('yith_booking_data');

            // Si les données de réservation sont trouvées, cela signifie que c'est un produit de location.
            if ($booking_data) {
                $contains_booking_item = true;

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

        // Si la commande contient au moins un élément de réservation, ajoutez-la à votre tableau de données.
        if ($contains_booking_item) {
            $all_orders_booking_data[] = $order_booking_data;
        }
    }

    // Incrémenter la page pour le prochain lot de commandes
    $paged++;
}




// Créez un tableau pour agréger la quantité de chaque produit loué.
$all_products_data = [];

foreach ($all_orders_booking_data as $product_data) {
    if ($selected_date_timestamp <=$product_data['_booking-from'] || $product_data['_booking-to'] <= $selected_date_timestamp) {
        continue;
    }
    $product_name = $product_data['_product_name'];
    $product_id = $product_data['_product_id'];
    $stock = get_post_meta($product_id, '_yith_booking_max_per_block', true);
    $quantity = $product_data['_quantity'];


    // Si le produit n'est pas encore dans le tableau, ajoutez-le.
    if (!isset($all_products_data[$product_id])) {
        $all_products_data[$product_id] = [
            'name' => $product_name,
            'quantity' => $quantity,
            'stock' => $stock
        ];
    } else {
        // Si le produit est déjà dans le tableau, mettez à jour sa quantité.
        $all_products_data[$product_id]['quantity'] += $quantity;
    }
}

?>
<div class="wrap">
    <div class="main_title">
        <h1>Stock produits par jours</h1>
    </div>
    <div class="calendar-date-header">
        <h2><?php echo $formattedDate ?></h2>
        <div class="calendar-date-picker">
            <input type="date" class="date-selector" name="date-selector">
            <button id="send-date" class="save_buttons">Voir les locations</button>
        </div>
    </div>
    <div class="main_content">
        <table class="table table-striped">
            <thead>
            <tr>
                <th scope="col">Nom</th>
                <th scope="col">Nombre de produits loués</th>
                <th scope="col">Nombre de produits restants</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($all_products_data as $product_id => $product_data) :
                $edit_link = get_edit_post_link($product_id);
                $product_name = $product_data['name'];
                $products_rent = $product_data['quantity'];
                $product_stock = ($product_data['stock'] - $product_data['quantity']);
                ?>
                <tr>
                    <td><a class="calendar-product-link" href="<?php echo $edit_link ?>" target="_blank"><?php echo $product_name ?></a></td>
                    <td class="display-rent-products"><?php echo $products_rent ?></td>
                    <td class="display-instock-products"><?php echo $product_stock ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>