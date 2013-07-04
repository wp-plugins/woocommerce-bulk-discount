<?php
/*
Plugin Name: WooCommerce Bulk Discount
Plugin URI: http://www.tools4me.net/wordpress/woocommerce-bulk-discount-plugin
Description: Apply fine-grained discounts to items in the shopping cart, dependently on ordered quantity and on concrete product.
Author: Rene Puchinger
Version: 1.0
Author URI: http://www.renepuchinger.com
*/

class Woo_Bulk_Discount_Plugin {

    public function install_plugin() {
    }

    private function get_discounted_coeff($prodId, $price, $quantity) {
        $q = array(0.0);
        $d = array(0.0);
        for ($i = 1; $i <= 5; $i++) {
            array_push($q, get_post_meta($prodId, "_bulkdiscount_quantity_$i", true));
            array_push($d, get_post_meta($prodId, "_bulkdiscount_discount_$i", true));
            if ($quantity >= $q[$i] && $q[$i] > $q[0]) {
                $q[0] = $q[$i];
                $d[0] = $d[$i];
            }
        }
        return (100.0 - $d[0]) / 100.0;
    }

    public function filter_item_price($price, $values) {
        $_product = $values['data'];
        $coeff = $this->get_discounted_coeff($_product->id, $_product->get_price(), $values['quantity']);
        $orig_price = $_product->get_price() / $coeff;
        $discount_info = ($coeff < 1.0) ? woocommerce_price($orig_price) : "";
        return "<span class='discount_price_info'><span>$discount_info</span></span>$price";
    }

    public function action_before_calculate(WC_Cart $cart) {
        $_SESSION['woocommerce_discount_cart'] = $cart;
        if ( sizeof( $cart->cart_contents ) > 0) {
            foreach ( $cart->cart_contents as $cart_item_key => $values ) {
                $_product = $values['data'];
                $row_base_price = $_product->get_price() * $this->get_discounted_coeff($_product->id, $_product->get_price(), $values['quantity']);
                $values['data']->set_price($row_base_price);
            }
        }
    }

    public function action_after_calculate() {
        $cart = $_SESSION['woocommerce_discount_cart'];
        if ( sizeof( $cart->cart_contents ) > 0) {
            foreach ( $cart->cart_contents as $cart_item_key => $values ) {
                $_product = $values['data'];
                $row_base_price = $_product->get_price() / $this->get_discounted_coeff($_product->id, $_product->get_price(), $values['quantity']);
                $values['data']->set_price($row_base_price);
            }
        }
    }

    public function before_cart_table() {
        echo "<div class='cart-show-discounts'>";
        echo get_option('woocommerce_t4m_cart_info');
        echo "</div>";
    }

    public function single_product_summary() {
        global $thepostid, $post;
        if (!$thepostid) $thepostid = $post->ID;
        echo "<div class='productinfo-show-discounts'>";
        echo get_post_meta($thepostid, '_bulkdiscount_text_info', true);
        echo "</div>";
    }

    public function action_product_write_panel_tabs() {
        echo '<li class="bulkdiscount_tab bulkdiscount_options"><a href="#bulkdiscount_product_data">Bulk Discounts</a></li>';
    }

    public function action_product_write_panels() {
        global $thepostid, $post;
        if (!$thepostid) $thepostid = $post->ID;
        ?>
        <script type="text/javascript">
            jQuery(document).ready( function () {
                <?php
                for($i = 1; $i <= 5; $i++) :
                ?>
                        jQuery('#bulkdiscount_product_data').find('.block<?php echo $i; ?>').hide();
                        jQuery('#bulkdiscount_product_data').find('.options_group<?php echo max($i, 2); ?>').hide();
                        jQuery('#bulkdiscount_product_data').find('#add_discount_line<?php echo max($i, 2); ?>').hide();
                        jQuery("#bulkdiscount_product_data").find('#add_discount_line<?php echo $i; ?>').click(function() {
                            jQuery('#bulkdiscount_product_data').find('.block<?php echo $i; ?>').show(600);
                            jQuery('#bulkdiscount_product_data').find('.options_group<?php echo min($i+1, 5); ?>').show(600);
                            jQuery('#bulkdiscount_product_data').find('#add_discount_line<?php echo min($i+1, 5); ?>').show(600);
                            jQuery('#bulkdiscount_product_data').find('#add_discount_line<?php echo $i; ?>').hide(600);
                        });
                <?php
                endfor;
                for ($i = 1; $i <= 5; $i++) {
                    if (get_post_meta($thepostid, "_bulkdiscount_quantity_$i", true)) {
                        ?>
                        jQuery('#bulkdiscount_product_data').find('.block<?php echo $i; ?>').show();
                        jQuery('#bulkdiscount_product_data').find('.options_group<?php echo $i; ?>').show();
                        jQuery("#bulkdiscount_product_data").find('#add_discount_line<?php echo $i; ?>').hide();
                        jQuery("#bulkdiscount_product_data").find('.options_group<?php echo min($i+1,5); ?>').show();
                        jQuery("#bulkdiscount_product_data").find('#add_discount_line<?php echo min($i+1,5); ?>').show();
                        <?php
                    }
                }
                ?>
            });
        </script>

        <div id="bulkdiscount_product_data" class="panel woocommerce_options_panel">

			<div class="options_group">
                <?php
                    woocommerce_wp_textarea_input(  array( 'id' => "_bulkdiscount_text_info", 'label' => 'Bulk discount info in product description', 'description' => 'Optionally enter bulk discount information that will be visible on the product page.', 'desc_tip' => 'yes', 'class' => 'fullWidth') );
                ?>
            </div>

            <?php
            for($i = 1; $i <= 5; $i++) :
            ?>

            <div class="options_group<?php echo $i; ?>">
                <a id="add_discount_line<?php echo $i; ?>" class="button-secondary" href="#block<?php echo $i; ?>">Add discount line</a>
                <div class="block<?php echo $i; ?>">
                    <?php
                        woocommerce_wp_text_input(  array( 'id' => "_bulkdiscount_quantity_$i", 'label' => __('Quantity (min.)', 'woobulkdiscount'), 'description' => __('Enter up minimal quantity for which the discount applies.', 'woocommerce') ) );
                        woocommerce_wp_text_input(  array( 'id' => "_bulkdiscount_discount_$i", 'label' => __('Discount (%)', 'woobulkdiscount'), 'description' => __('Enter the discount in percents (Allowed values: 0 to 100).', 'woocommerce') ) );
                    ?>
                </div>
            </div>
		
            <?php
            endfor;
            ?>

			<br />
			
		</div>
		
		<?php
    }

    public function action_enqueue_dependencies() {
        wp_register_style('woocommercebulkdiscount-style', plugins_url('style.css', __FILE__));
        wp_enqueue_style('woocommercebulkdiscount-style');
        wp_enqueue_script('jquery');
    }

    public function action_enqueue_dependencies_admin() {
        wp_register_style('woocommercebulkdiscount-style-admin', plugins_url('admin.css', __FILE__));
        wp_enqueue_style('woocommercebulkdiscount-style-admin');
        wp_enqueue_script('jquery');
    }

    public function action_process_meta($post_id) {
        if (isset($_POST['_bulkdiscount_text_info'])) update_post_meta( $post_id, '_bulkdiscount_text_info', esc_attr($_POST['_bulkdiscount_text_info']) );
        for ($i = 1; $i <= 5; $i++) {
            if (isset($_POST["_bulkdiscount_quantity_$i"])) update_post_meta( $post_id, "_bulkdiscount_quantity_$i", stripslashes($_POST["_bulkdiscount_quantity_$i"]) );
            if (isset($_POST["_bulkdiscount_discount_$i"])) update_post_meta( $post_id, "_bulkdiscount_discount_$i", stripslashes($_POST["_bulkdiscount_discount_$i"]) );
        }
    }

    public function action_general_settings($settings) {
        array_push ($settings, array(	'name' => __( 'Bulk Discounts', 'woocommerce' ), 'type' => 'title','desc' => __('The following options are specific to product bulk discounts.', 'woocommerce'), 'id' => 't4m_bulk_discounts_options' ));
        array_push ($settings, array(
                'name' => __('Bulk discounts globally enabled', 'woocommerce'),
                'id' 		=> 'woocommerce_t4m_enable_bulk_discounts',
                'desc' => 'Enable bulk discounts',
                'std' 		=> 'yes',
                'type' 		=> 'checkbox'
            )
        );
        array_push ($settings, array(
                'name' => __('Optionally enter information about discounts visible on cart page', 'woocommerce'),
                'id' 		=> 'woocommerce_t4m_cart_info',
                'type' 		=> 'textarea',
                'css' 		=> 'width:100%; height: 75px;'
            )
        );
        array_push ($settings, array( 'type' => 'sectionend', 'id' => 't4m_bulk_discounts_options' ));
        return $settings;
    }

}

$woo_bulk_discount_plugin = new Woo_Bulk_Discount_Plugin();
register_activation_hook(__FILE__, array($woo_bulk_discount_plugin, 'install_plugin'));

if (get_option('woocommerce_t4m_enable_bulk_discounts') == 'yes') {
    add_action('woocommerce_before_calculate_totals', array($woo_bulk_discount_plugin, 'action_before_calculate'));
    add_action('woocommerce_calculate_total', array($woo_bulk_discount_plugin, 'action_after_calculate'));
    add_action('woocommerce_before_cart_table', array($woo_bulk_discount_plugin, 'before_cart_table'));
    add_action('woocommerce_single_product_summary', array($woo_bulk_discount_plugin, 'single_product_summary'), 45);
    add_filter('woocommerce_cart_item_price_html', array($woo_bulk_discount_plugin, 'filter_item_price'), 10, 2);
    add_filter('woocommerce_product_write_panel_tabs', array($woo_bulk_discount_plugin, 'action_product_write_panel_tabs'));
    add_filter('woocommerce_product_write_panels', array($woo_bulk_discount_plugin, 'action_product_write_panels'));
    add_action('woocommerce_process_product_meta', array($woo_bulk_discount_plugin, 'action_process_meta'));
}

add_filter('woocommerce_general_settings', array($woo_bulk_discount_plugin, 'action_general_settings'));
add_action('admin_enqueue_scripts', array($woo_bulk_discount_plugin, 'action_enqueue_dependencies_admin'));
add_action('wp_head', array($woo_bulk_discount_plugin, 'action_enqueue_dependencies'));
