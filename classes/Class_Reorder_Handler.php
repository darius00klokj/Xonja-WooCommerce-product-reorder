<?php

class Reorder_Handler {

    public static $rh = null;

    public function __construct() {
        add_action('admin_enqueue_scripts', array($this, 'reorder_scripts'));
        add_action('admin_menu', array($this, 'reorder_add_page'));
        
        load_plugin_textdomain('xonja', FALSE, 'Xonja-WooCommerce-product-reorder/languages/');
    }

    function reorder_add_page() {
        add_submenu_page('woocommerce', 'Reorder Products', 'Reorder Products', 'manage_options', 'xonja_wc_reorder', 'xonja_wc_reorder_do_page');
    }

    /**
     * 
     * @return Reorder_Handler 
     */
    public static function init() {
        if (!Reorder_Handler::$rh) {
            Reorder_Handler::$rh = new Reorder_Handler();
        }
        return Reorder_Handler::$rh;
    }

    function reorder_scripts() {

        wp_enqueue_script('jquery');

        wp_enqueue_script('js-modernizr', XONJA_WC_PO_URL . 'js/modernizr.js', array('jquery'), '1.0.0', true);
        wp_enqueue_script('custom-jquery-ui', XONJA_WC_PO_URL . 'js/jquery-ui.js', array('jquery'), '1.0.0', true);
        wp_enqueue_script('isotope.pkgd.min.js', XONJA_WC_PO_URL . 'js/isotope.pkgd.min.js', array('jquery'), '1.0.0', true);
        wp_enqueue_script('jGravity', XONJA_WC_PO_URL . 'js/jGravity.js', array('jquery'), '1.0.0', true);
        wp_enqueue_script('products-reorder', XONJA_WC_PO_URL . 'js/index.js', array('jquery'), '1.0.0', true);

        wp_enqueue_style('products-reorder', XONJA_WC_PO_URL . 'css/style.css');
    }

    /**
     * 
     * @return \WP_Query
     */
    function get_products_loop() {
        $args = array(
            'post_type' => 'product',
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'orderby' => 'menu_order',
            'order' => 'ASC'
        );
        if ('yes' === get_option('woocommerce_hide_out_of_stock_items')) {
            $args['meta_query'] = array(
                array(
                    'key' => '_stock_status',
                    'value' => 'instock',
                    'compare' => '='
                )
            );
        }
        $loop = new WP_Query($args);
        return $loop;
    }

}

add_action('init', array('Reorder_Handler', 'init'));
