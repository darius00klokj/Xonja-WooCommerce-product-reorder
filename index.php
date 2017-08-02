<?php
/*
  Plugin Name: Xonja WooCommerce Product Reorder
  Description: Will allow the admin to view all products and reorder them as he wishes by drag and drop
  Version: 1.00
  Author: Darius Karremans
  Author URI: https://valfi.fi/
  Text Domain: xonja
  Domain Path: /languages/
 */

if (!defined('ABSPATH')) {
    die('-1');
}

define('XONJA_WC_PO_URL', plugin_dir_url(__FILE__));
include 'classes/Class_Reorder_Handler.php';

function xonja_wc_reorder_do_page() {
    ?>
    <div class="wrap">
        <div class="fixed-holder">
            <p id="after-order-saved"></p>
            <a class="button button-primary" href="<?php echo get_post(woocommerce_get_page_id('shop'))->guid; ?>" target="_blank"><?php echo __('Preview', 'xonja') ?></a>
            <button id="save_order" class="button button-primary"><?php echo __('Save order', 'xonja') ?></button>
        </div>
        <div id="inner">
            <?php
            $loop = Reorder_Handler::init()->get_products_loop();
            if ($loop->have_posts()) {
                while ($loop->have_posts()) :
                    $loop->the_post();
                    $menu_order = $loop->post->menu_order;
                    ?>
                    <div class="gravity" data-order="<?= $menu_order ?>" data-pid="<?= $loop->post->ID ?>">
                        <?php
                        the_post_thumbnail('thumbnail');
                        echo '<h4>' . get_the_title() . '</h4>';
                        ?>
                        <a href="<?= get_edit_post_link($loop->post->ID) ?>" class="edit_product" target="_blank"><?php echo __('Edit product', 'xonja') ?></a>
                    </div>
                    <?php
                endwhile;
            } else {
                echo __('No products found', 'xonja');
            }
            wp_reset_postdata();
            ?>
        </div>
    </div>
    <?php
}

function wp_ajax_xonja_wc_order() {

    if (defined('DOING_AJAX')) {
        global $wpdb;
        $items = $_POST['obj'];
        try {
            foreach ($items as $item) {
                $data = array(
                    'menu_order' => $item["menu_order"]
                );
                $data = apply_filters('post-types-order_save-ajax-order', $data, $item["menu_order"], $item["ID"]);
                $wpdb->update($wpdb->posts, $data, array('ID' => $item["ID"]));
            }
        } catch (Exception $e) {
            echo json_encode(['msg' => __('There was an error saving the order', 'xonja') . ': ' . $e->getMessage(), 'error' => true]);
            die();
        }
        echo json_encode(['msg' => __('The order has been saved at', 'xonja') . ': ' . date('H:i:s'), 'error' => false]);
        do_action('xonja_wc_after_page_reorder');
    }
    die();
}

add_action('wp_ajax_nopriv_wc_order', 'wp_ajax_xonja_wc_order');
add_action('wp_ajax_wc_order', 'wp_ajax_xonja_wc_order');
