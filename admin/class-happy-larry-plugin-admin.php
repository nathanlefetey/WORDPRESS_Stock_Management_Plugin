<?php

class Happy_Larry_Plugin_Admin {


	private string $happy_larry_plugin;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties
	 * @param      string    $happy_larry_plugin       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $happy_larry_plugin, $version ) {

		$this->plugin_name = $happy_larry_plugin;
		$this->version = $version;

        add_action('admin_menu', array($this, 'add_plugin_admin_menu_page'));
        add_action('wp_ajax_update_product_stock_and_category', array($this, 'update_product_stock_and_category'));


    }

    /**
     * Register the administration menu for this plugin on the Wordpress Dashboard menu
     */
    public function add_plugin_admin_menu_page(){
        add_menu_page('Happy Larry', 'Happy Larry', 'manage_options', $this->plugin_name, array($this, 'display_plugin_setup_page'), 'dashicons-slides', 9);
        add_submenu_page($this->plugin_name, 'Happy Larry', 'Happy Larry', 'manage_options', $this->plugin_name, array($this, 'display_plugin_setup_page'));
        add_submenu_page($this->plugin_name, 'Stocks Produit', 'Stock Produit', 'manage_options', 'stock_product', array($this, 'display_plugin_stock_product_page'));
    }

    public function display_plugin_setup_page()
    {
        include_once('partials/happy-larry-admin-display.php');
    }

    public function display_plugin_stock_product_page()
    {
        include_once('partials/happy-larry-stock-product-display.php');
    }

    /**
     * Change the stock status of a simple product to out of stock
     */
    public function put_simple_product_out_of_stock($product) {
        $product->set_manage_stock(true);
        $product->set_stock_quantity(0);
        $product->save();
    }

    /**
     * Change the stock status of a simple product to instock
     */
    public function put_simple_product_instock($product) {
        $product->set_manage_stock(false);
        $product->set_stock_status('instock');
        $product->save();
    }

    /**
     * Function to update product stock after submit
     */
    public function update_product_stock($product_array) {
        foreach ($product_array as $id => $number_of_stock) {
            $product = wc_get_product($id);
            if (!$product) {
                continue;
            }

            if ($number_of_stock === "" ) {
                $this->put_simple_product_instock($product);
                continue;
            }

            if($number_of_stock == "0"){
                $this->put_simple_product_out_of_stock($product);
                continue;
            }

            $product->set_manage_stock(true);
            $product->set_stock_quantity($number_of_stock);
            $product->save();
        }
    }

    /**
     * Function to update product categories after submit
     */
    public function update_product_categories($product_array) {
        $uncategorized = 15;
        $restock_auto = 17;
        $restockj1 = 18;
        $rent_indiv = 19;
        $rent_bundle = 20;

        $swap_pairs = [
            $rent_indiv => $rent_bundle,
            $rent_bundle => $rent_indiv,
            $restock_auto => $restockj1,
            $restockj1 => $restock_auto
        ];

        foreach ($product_array as $id => $category) {
            $product = wc_get_product($id);
            if (!$product || $category === "") {
                continue;
            }

            // get current product categories
            $current_categories = $product->get_category_ids();

            // Check if category is part of the swap pairs and remove counterpart if necessary
            if (array_key_exists($category, $swap_pairs) && in_array($swap_pairs[$category], $current_categories)) {
                unset($current_categories[array_search($swap_pairs[$category], $current_categories)]);
            }

            if ($category === 'restock_none') {
                if (($key = array_search($restock_auto, $current_categories)) !== false) {
                    unset($current_categories[$key]);
                }
                if (($key = array_search($restockj1, $current_categories)) !== false) {
                    unset($current_categories[$key]);
                }
            } elseif ($category === 'rent_none') {
                if (($key = array_search($rent_indiv, $current_categories)) !== false) {
                    unset($current_categories[$key]);
                }
                if (($key = array_search($rent_bundle, $current_categories)) !== false) {
                    unset($current_categories[$key]);
                }
            }

            // if the category is not already set, add it
            if (!in_array($category, $current_categories)) {
                $current_categories[] = $category;
                if (($key = array_search($uncategorized, $current_categories)) !== false) {
                    unset($current_categories[$key]);
                }
            }

            // set categories to the product
            $product->set_category_ids($current_categories);
            $product->save();
        }
    }




    /**
     * Ajax function to pass a product out of stock for a period
     */
    public function update_product_stock_and_category() {
        $allRestockCategories = $_POST['allRestockCategories'];
        $allRentCategories = $_POST['allRentCategories'];
        $allHorsStockProductSimple = $_POST['allHorsStockProductSimple'];
        $allTimePeriod = $_POST['allTimePeriod'];

        $this->update_product_categories($allRestockCategories);
        $this->update_product_categories($allRentCategories);
        $this->update_product_stock($allHorsStockProductSimple);

        foreach ($allTimePeriod as $product_id => $time) {
            $product = wc_get_product($product_id);
            if (!$product) {
                continue;
            }

            if ($time === '') {
                update_post_meta($product_id, 'rent_limit_time_nex_day', '');
                continue;
            }

            update_post_meta($product_id, 'rent_limit_time_nex_day', $time);
        }

        wp_send_json_success();
    }

	/**
	 * Register the stylesheets for the admin area.
	 *
	 */
	public function enqueue_styles() {
        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/happy-larry-plugin-admin.css', array(), mt_rand());
    }

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
        wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/happy-larry-plugin-admin.js', array('jquery'), mt_rand());
        wp_localize_script($this->plugin_name, 'ajax_object', array('ajax_url' => admin_url('admin-ajax.php')));
	}

}