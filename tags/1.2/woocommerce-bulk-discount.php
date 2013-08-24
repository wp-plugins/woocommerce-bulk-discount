<?php
/*
Plugin Name: WooCommerce Bulk Discount
Plugin URI: http://www.tools4me.net/wordpress/woocommerce-bulk-discount-plugin
Description: Apply fine-grained discounts to items in the shopping cart, dependently on ordered quantity and on concrete product.
Author: Rene Puchinger
Version: 1.2
Author URI: http://www.renepuchinger.com
License: GPL3


    Copyright (C) 2013  Rene Puchinger

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.

*/

if (!defined('ABSPATH')) exit; // Exit if accessed directly

if (!in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) return; // Check if WooCommerce is active

if (!class_exists('Woo_Bulk_Discount_Plugin_t4m')) {

    class Woo_Bulk_Discount_Plugin_t4m
    {

        var $discount_coeffs;

        public function __construct()
        {

            $this->current_tab = (isset($_GET['tab'])) ? $_GET['tab'] : 'general';

            $this->settings_tabs = array(
                'bulk_discount' => __('Bulk Discount', 'wc_bulk_discount')
            );

            add_action('admin_enqueue_scripts', array($this, 'action_enqueue_dependencies_admin'));
            add_action('wp_head', array($this, 'action_enqueue_dependencies'));

            add_action('woocommerce_settings_tabs', array($this, 'add_tab'), 10);

            // Run these actions when generating the settings tabs.
            foreach ($this->settings_tabs as $name => $label) {
                add_action('woocommerce_settings_tabs_' . $name, array($this, 'settings_tab_action'), 10);
                add_action('woocommerce_update_options_' . $name, array($this, 'save_settings'), 10);
            }

            // Add the settings fields to each tab.
            add_action('woocommerce_bulk_discount_settings', array($this, 'add_settings_fields'), 10);

            if (get_option('woocommerce_t4m_enable_bulk_discounts', 'yes') == 'yes') {
                add_action('woocommerce_before_calculate_totals', array($this, 'action_before_calculate'), 10, 1);
                add_action('woocommerce_calculate_totals', array($this, 'action_after_calculate'), 10, 1);
                add_action('woocommerce_before_cart_table', array($this, 'before_cart_table'));
                add_action('woocommerce_single_product_summary', array($this, 'single_product_summary'), 45);
                add_filter('woocommerce_cart_item_price_html', array($this, 'filter_item_price'), 10, 2);
                add_filter('woocommerce_product_write_panel_tabs', array($this, 'action_product_write_panel_tabs'));
                add_filter('woocommerce_product_write_panels', array($this, 'action_product_write_panels'));
                add_action('woocommerce_process_product_meta', array($this, 'action_process_meta'));
                add_filter('woocommerce_cart_product_subtotal', array($this, 'filter_cart_product_subtotal'), 10, 4);
            }

        }

        /**
         * For given product, and quantity return the price modifying factor.
         *
         * @param $product_id
         * @param $quantity
         * @return float
         */
        protected function get_discounted_coeff($product_id, $quantity)
        {
            $q = array(0.0);
            $d = array(0.0);
            /* Find the appropriate discount coefficient by looping through up to the five discount settings */
            for ($i = 1; $i <= 5; $i++) {
                array_push($q, get_post_meta($product_id, "_bulkdiscount_quantity_$i", true));
                array_push($d, get_post_meta($product_id, "_bulkdiscount_discount_$i", true));
                if ($quantity >= $q[$i] && $q[$i] > $q[0]) {
                    $q[0] = $q[$i];
                    $d[0] = $d[$i];
                }
            }
            return (100.0 - $d[0]) / 100.0; // convert the resulting discount from % to the multiplying coefficient
        }

        /**
         * Filter product price so that the discount is visible.
         *
         * @param $price
         * @param $values
         * @return string
         */
        public function filter_item_price($price, $values)
        {
            global $woocommerce;
            $_product = $values['data'];
            if (empty($this->discount_coeffs) || !isset($this->discount_coeffs[$this->get_actual_id($_product)])
                || !isset($this->discount_coeffs[$this->get_actual_id($_product)]['orig_price']) || !isset($this->discount_coeffs[$this->get_actual_id($_product)]['coeff']))
            {
                $this->gather_discount_coeffs();
            }
            $coeff = $this->discount_coeffs[$this->get_actual_id($_product)]['coeff'];
            if ($coeff == 1.0) {
                return $price;
            }
            $discprice = woocommerce_price($_product->get_price() * $coeff);
            $oldprice = woocommerce_price($this->discount_coeffs[$this->get_actual_id($_product)]['orig_price']);
            return "<span class='discount-info' title='".sprintf(__('Eligible for %d%% discount!', 'wc_bulk_discount'),  round((1 - $coeff) * 100 ))."'><span class='old-price'>$oldprice</span><span class='new-price'>$discprice</span></span>";
        }

        /**
         * Gather discount information to the array $this->discount_coefs
         */
        protected function gather_discount_coeffs() {
            global $woocommerce;
            $cart = $woocommerce->cart;
            $this->discount_coeffs = array();
            if (sizeof($cart->cart_contents) > 0) {
                foreach ($cart->cart_contents as $cart_item_key => $values) {
                    $_product = $values['data'];
                    $quantity = 0;
                    if (get_option('woocommerce_t4m_variations_separate', 'no') == 'no' && $_product instanceof WC_Product_Variation && $_product->parent) {
                        $parent = $_product->parent;
                        foreach ($cart->cart_contents as $valuesInner) {
                            $p = $valuesInner['data'];
                            if ($p instanceof WC_Product_Variation && $p->parent && $p->parent->id == $parent->id) {
                                $quantity += $valuesInner['quantity'];
                                $this->discount_coeffs[$_product->variation_id]['quantity'] = $quantity;
                            }
                        }
                    } else {
                        $quantity = $values['quantity'];
                    }
                    $this->discount_coeffs[$this->get_actual_id($_product)]['coeff'] = $this->get_discounted_coeff($_product->id, $quantity);
                    $this->discount_coeffs[$this->get_actual_id($_product)]['orig_price'] = $_product->get_price();
                }
            }
        }

        /**
         * Hook to woocommerce_before_calculate_totals action.
         *
         * @param WC_Cart $cart
         */
        public function action_before_calculate(WC_Cart $cart)
        {

            $this->gather_discount_coeffs();

            if (sizeof($cart->cart_contents) > 0) {
                foreach ($cart->cart_contents as $cart_item_key => $values) {
                    $_product = $values['data'];
                    $row_base_price = $_product->get_price() * $this->discount_coeffs[$this->get_actual_id($_product)]['coeff'];
                    $values['data']->set_price($row_base_price); // TODO SETPRICE
                }
            }
        }

        protected function get_actual_id($product) {
            if ($product instanceof WC_Product_Variation) {
                return $product->variation_id;
            } else {
                return $product->id;
            }
        }

        /**
         * Hook to woocommerce_calculate_totals.
         *
         * @param WC_Cart $cart
         */
        public function action_after_calculate(WC_Cart $cart)
        {
            if (sizeof($cart->cart_contents) > 0) {
                foreach ($cart->cart_contents as $cart_item_key => $values) {
                    $_product = $values['data'];
                    $values['data']->set_price($this->discount_coeffs[$this->get_actual_id($_product)]['orig_price']); // TODO SETPRICE
                }
            }
        }

        /**
         * Show discount info in cart.
         */
        public function before_cart_table()
        {
            echo "<div class='cart-show-discounts'>";
            echo get_option('woocommerce_t4m_cart_info');
            echo "</div>";
        }

        /**
         * Hook to woocommerce_cart_product_subtotal filter.
         *
         * @param $subtotal
         * @param $_product
         * @param $quantity
         * @param WC_Cart $cart
         * @return string
         */
        public function filter_cart_product_subtotal($subtotal, $_product, $quantity, $cart)
        {
            $coeff = $this->discount_coeffs[$this->get_actual_id($_product)]['coeff'];
            $newsubtotal = woocommerce_price($_product->get_price() * $quantity * $coeff);
            return $newsubtotal;
        }

        /**
         * Display discount information in Product Detail.
         */
        public function single_product_summary()
        {
            global $thepostid, $post;
            if (!$thepostid) $thepostid = $post->ID;
            echo "<div class='productinfo-show-discounts'>";
            echo get_post_meta($thepostid, '_bulkdiscount_text_info', true);
            echo "</div>";
        }

        /**
         * Add entry to Product Settings.
         */
        public function action_product_write_panel_tabs()
        {
            echo '<li class="bulkdiscount_tab bulkdiscount_options"><a href="#bulkdiscount_product_data">Bulk Discount</a></li>';
        }

        /**
         * Add entry content to Product Settings.
         */
        public function action_product_write_panels()
        {
            global $thepostid, $post;
            if (!$thepostid) $thepostid = $post->ID;
            ?>
            <script type="text/javascript">
                jQuery(document).ready(function () {
                    <?php
                    for($i = 1; $i <= 5; $i++) :
                    ?>
                    jQuery('#bulkdiscount_product_data').find('.block<?php echo $i; ?>').hide();
                    jQuery('#bulkdiscount_product_data').find('.options_group<?php echo max($i, 2); ?>').hide();
                    jQuery('#bulkdiscount_product_data').find('#add_discount_line<?php echo max($i, 2); ?>').hide();
                    jQuery("#bulkdiscount_product_data").find('#add_discount_line<?php echo $i; ?>').click(function () {
                        jQuery('#bulkdiscount_product_data').find('.block<?php echo $i; ?>').show(400);
                        jQuery('#bulkdiscount_product_data').find('.options_group<?php echo min($i+1, 5); ?>').show(400);
                        jQuery('#bulkdiscount_product_data').find('#add_discount_line<?php echo min($i+1, 5); ?>').show(400);
                        jQuery('#bulkdiscount_product_data').find('#add_discount_line<?php echo $i; ?>').hide(400);
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
                    woocommerce_wp_textarea_input(array('id' => "_bulkdiscount_text_info", 'label' => 'Bulk discount info in product description', 'description' => 'Optionally enter bulk discount information that will be visible on the product page.', 'desc_tip' => 'yes', 'class' => 'fullWidth'));
                    ?>
                </div>

                <?php
                for ($i = 1; $i <= 5; $i++) :
                    ?>

                    <div class="options_group<?php echo $i; ?>">
                        <a id="add_discount_line<?php echo $i; ?>" class="button-secondary"
                           href="#block<?php echo $i; ?>">Add discount line</a>

                        <div class="block<?php echo $i; ?>">
                            <?php
                            woocommerce_wp_text_input(array('id' => "_bulkdiscount_quantity_$i", 'label' => __('Quantity (min.)', 'woobulkdiscount'), 'description' => __('Enter up minimal quantity for which the discount applies.', 'woocommerce')));
                            woocommerce_wp_text_input(array('id' => "_bulkdiscount_discount_$i", 'label' => __('Discount (%)', 'woobulkdiscount'), 'description' => __('Enter the discount in percents (Allowed values: 0 to 100).', 'woocommerce')));
                            ?>
                        </div>
                    </div>

                <?php
                endfor;
                ?>

                <br/>

            </div>

        <?php
        }

        /**
         * Enqueue frontend dependencies.
         */
        public function action_enqueue_dependencies()
        {
            wp_register_style('woocommercebulkdiscount-style', plugins_url('style.css', __FILE__));
            wp_enqueue_style('woocommercebulkdiscount-style');
            wp_enqueue_script('jquery');
        }

        /**
         * Enqueue backend dependencies.
         */
        public function action_enqueue_dependencies_admin()
        {
            wp_register_style('woocommercebulkdiscount-style-admin', plugins_url('admin.css', __FILE__));
            wp_enqueue_style('woocommercebulkdiscount-style-admin');
            wp_enqueue_script('jquery');
        }

        /**
         * Updating post meta.
         *
         * @param $post_id
         */
        public function action_process_meta($post_id)
        {
            if (isset($_POST['_bulkdiscount_text_info'])) update_post_meta($post_id, '_bulkdiscount_text_info', esc_attr($_POST['_bulkdiscount_text_info']));
            for ($i = 1; $i <= 5; $i++) {
                if (isset($_POST["_bulkdiscount_quantity_$i"])) update_post_meta($post_id, "_bulkdiscount_quantity_$i", stripslashes($_POST["_bulkdiscount_quantity_$i"]));
                if (isset($_POST["_bulkdiscount_discount_$i"])) update_post_meta($post_id, "_bulkdiscount_discount_$i", stripslashes($_POST["_bulkdiscount_discount_$i"]));
            }
        }

        /**
         * @access public
         * @return void
         */
        public function add_tab()
        {
            foreach ($this->settings_tabs as $name => $label) {
                $class = 'nav-tab';
                if ($this->current_tab == $name)
                    $class .= ' nav-tab-active';
                echo '<a href="' . admin_url('admin.php?page=woocommerce&tab=' . $name) . '" class="' . $class . '">' . $label . '</a>';
            }
        }

        /**
         * @access public
         * @return void
         */
        public function settings_tab_action()
        {
            global $woocommerce_settings;

            // Determine the current tab in effect.
            $current_tab = $this->get_tab_in_view(current_filter(), 'woocommerce_settings_tabs_');

            // Hook onto this from another function to keep things clean.
            do_action('woocommerce_bulk_discount_settings');

            // Display settings for this tab (make sure to add the settings to the tab).
            woocommerce_admin_fields($woocommerce_settings[$current_tab]);
        }

        /**
         * Save settings in a single field in the database for each tab's fields (one field per tab).
         */
        public function save_settings()
        {
            global $woocommerce_settings;

            // Make sure our settings fields are recognised.
            $this->add_settings_fields();

            $current_tab = $this->get_tab_in_view(current_filter(), 'woocommerce_update_options_');
            woocommerce_update_options($woocommerce_settings[$current_tab]);
        }

        /**
         * Get the tab current in view/processing.
         */
        public function get_tab_in_view($current_filter, $filter_base)
        {
            return str_replace($filter_base, '', $current_filter);
        }


        /**
         * Add settings fields for each tab.
         */
        public function add_settings_fields()
        {
            global $woocommerce_settings;

            // Load the prepared form fields.
            $this->init_form_fields();

            if (is_array($this->fields))
                foreach ($this->fields as $k => $v)
                    $woocommerce_settings[$k] = $v;
        }

        /**
         * Prepare form fields to be used in the various tabs.
         */
        public function init_form_fields()
        {
            global $woocommerce;

            // Define settings
            $this->fields['bulk_discount'] = apply_filters('woocommerce_bulk_discount_settings_fields', array(

                array('name' => __('Bulk Discount', 'wc_bulk_discount'), 'type' => 'title', 'desc' => __('The following options are specific to product bulk discount. <br /><br/><strong><i>After changing the settings, it is recommended to clear all sessions in WooCommerce &gt; System Status &gt; Tools.</i></strong>', 'wc_bulk_discount'), 'id' => 't4m_bulk_discounts_options'),

                array(
                    'name' => __('Bulk Discount enabled', 'wc_bulk_discount'),
                    'id' => 'woocommerce_t4m_enable_bulk_discounts',
                    'desc' => __('', 'wc_bulk_discount'),
                    'std' => 'yes',
                    'type' => 'checkbox',
                    'default' => 'yes'
                ),

                array(
                    'name' => __('Treat product variations separately', 'wc_bulk_discount'),
                    'id' => 'woocommerce_t4m_variations_separate',
                    'desc' => __('You need to have this option unchecked to handle discounts on product variations as a whole.', 'wc_bulk_discount'),
                    'std' => 'yes',
                    'type' => 'checkbox',
                    'default' => 'no'
                ),

                array(
                    'name' => __('Optionally enter information about discounts visible on cart page', 'wc_bulk_discount'),
                    'id' => 'woocommerce_t4m_cart_info',
                    'type' => 'textarea',
                    'css' => 'width:100%; height: 75px;'
                ),

                array('type' => 'sectionend', 'id' => 't4m_bulk_discounts_options')

            )); // End settings

            $woocommerce->add_inline_js("
					jQuery('#woocommerce_t4m_enable_bulk_discounts').change(function(){

						jQuery('#woocommerce_t4m_cart_info, #woocommerce_t4m_variations_separate').closest('tr').hide();

						if ( jQuery(this).attr('checked') ) {
							jQuery('#woocommerce_t4m_cart_info').closest('tr').show();
							jQuery('#woocommerce_t4m_variations_separate').closest('tr').show();
						}

					}).change();
				");
        }

    }

    $woo_bulk_discount_plugin = new Woo_Bulk_Discount_Plugin_t4m();

}