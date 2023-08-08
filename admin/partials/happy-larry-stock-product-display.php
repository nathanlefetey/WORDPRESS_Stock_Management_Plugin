<?php
    $products = wc_get_products(array(
        'limit' => -1,
        'orderby' => 'title',
        'order' => 'ASC',
        'return' => 'objects',
        'status' => 'publish',
    ));

    ?>
    <div class="wrap">
        <div class="main_title">
            <h1>Stock Produit</h1>
            <p>
                "Nom" : Affiche le nom du produit et ses variations.
                <br>
                "Stock journalier" : Gère les quantités : nombre, 0 pour "Out of Stock", vide pour "En stock" sans nombre.
                <br>
                "Catégories Restock" : Choix entre "J+1" ou "Jour même" pour réapprovisionnement.
                <br>
                "Catégories Location" : Choix "Individuelle" ou "Bundle" pour produits de location.
            </p>
        </div>
        <div class="main_content">
            <table class="table table-striped">
                <thead>
                <tr>
                    <th scope="col">Nom</th>
                    <th scope="col">Stock journalier</th>
                    <th scope="col">Catégorie Restock</th>
                    <th scope="col">Catégorie Location</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($products as $product) :
                    $uncategorized = 15; /* $restock_auto = 17; */ $restockj1 = 18; /* $location_indiv = 19; */ $location_bundle = 20;

                    $edit_link = get_edit_post_link($product->get_id());
                    $number_of_stock = get_post_meta($product->get_id(), 'rent_available_stock', true);
                    $product_categories = $product->get_category_ids();
                    $product_id = $product->get_id();
//                $restock_class = in_array($restock_auto, $product_categories) ? 'restock-auto' : (in_array($restockj1, $product_categories) ? 'restock-j1' : '');
//                $rent_class = in_array($location_indiv, $product_categories) ? 'location-indiv' : (in_array($location_bundle, $product_categories) ? 'location-bundle' : '');
                    $rent_limit_time = get_post_meta($product_id, 'rent_limit_time_nex_day', true);
//                echo "<pre>"; print_r(get_post_meta($product_id)); echo "<pre>";
                    ?>
                    <tr>
                        <td><a href="<?php echo $edit_link; ?>" target="_blank"><?php echo $product->get_name(); ?></a></td>
                        <td><input type="number" name="ht_day" class="ht_day" data-product-id="<?php echo $product_id ?>" value="<?php echo $number_of_stock !== '' ? $number_of_stock : "" ?>"></td>
                        <td>
                            <!--Select for the Restocking Category-->
                            <select name="restock_option" class="restock_category <?php /* echo $restock_class; */ ?>" data-product-id="<?php echo $product_id ?>" >
                                <option value="" disabled >Sélectionnez une option</option>
                                <option value="restock_none" <?php echo in_array($uncategorized, $product_categories) ? "selected" : "" ; ?>>Réinitialiser</option>
                                <option value="<?php echo $restockj1 ?>" <?php echo in_array($restockj1, $product_categories) ? "selected" : "" ; ?>>Restock J+1</option>
                            </select>
                        </td>
                        <td>
                            <!--Select for the Location Type Category-->
                            <select name="rent_type" class="rent_category <?php /* echo $rent_class; */ ?>" data-product-id="<?php echo $product_id ?>" >
                                <option value="" disabled >Sélectionnez une option</option>
                                <option value="rent_none" <?php echo in_array($uncategorized, $product_categories) ? "selected" : "" ; ?>>Réinitialiser</option>
                                <option value="<?php echo $location_bundle ?>" <?php echo in_array($location_bundle, $product_categories) ? "selected" : "" ; ?>>Location bundle</option>
                            </select>
                        </td>
                    </tr>
                    <?php if ($product->is_type('variable')) :
                    $variations = $product->get_available_variations();

                    foreach ($variations as $variation) :
                        $variation_id = $variation['variation_id'];
                        $variation_number_stock =  $variation['max_qty'] ?>
                        <tr>
                            <td>
                                <a class="variation_name_tag" href="<?php echo $edit_link; ?>" target="_blank">
                                    <?php
                                    $last_attribute = end($variation['attributes']);
                                    foreach ($variation['attributes'] as $name => $value) :
                                        echo $value;
                                        if ($value !== $last_attribute) {
                                            echo ' - ';
                                        }
                                    endforeach; ?>
                                </a>
                            </td>
                            <td><input type="number" name="ht_day" class="ht_day" data-product-id="<?php echo $variation_id ?>" value="<?php echo $variation_number_stock ?: "" ?>"></td>
                        </tr>
                    <?php endforeach;
                endif;
                endforeach; ?>
                </tbody>
            </table>
            <button id="send_period" class="save_buttons">Sauver les changements</button>
        </div>