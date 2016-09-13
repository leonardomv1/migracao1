<?php
/**
 * Plugin Name: Donations for WooCommerce
 * Description: Easily accept donations of varying amounts through your WooCommerce store.
 * Version: 1.0.6
 * Author: Potent Plugins
 * Author URI: http://potentplugins.com/?utm_source=donations-for-woocommerce&utm_medium=link&utm_campaign=wp-plugin-author-uri
 * License: GNU General Public License version 2 or later
 * License URI: http://www.gnu.org/licenses/old-licenses/gpl-2.0.en.html
 */

// Add Instructions link in plugins list
add_filter('plugin_action_links_'.plugin_basename(__FILE__), 'hm_wcdon_action_links');
function hm_wcdon_action_links($links) {
	array_unshift($links, '<a href="'.esc_url(get_admin_url(null, 'admin.php?page=hm_wcdon')).'">'.esc_html__('Instructions & Settings', 'hm-wc-donations').'</a>');
	return $links;
}

// Add admin page
add_action('admin_menu', 'hm_wcdon_admin_menu');
function hm_wcdon_admin_menu() {
	add_submenu_page('woocommerce', __('Donations for WooCommerce', 'hm-wc-donations'), __('Donations Settings', 'hm-wc-donations'), 'manage_woocommerce', 'hm_wcdon', 'hm_wcdon_admin_page');
}

// Display admin page
function hm_wcdon_admin_page() {
	if (!empty($_POST)) {
		$checkboxFields = array('disable_cart_amount_field');
		foreach ($checkboxFields as $cbField) {
			$_POST[$cbField] = (empty($_POST[$cbField]) ? 0 : 1);
		}
		hm_wcdon_set_options($_POST);
		echo('<div class="notice notice-success"><p>'.__('The settings have been saved.', 'hm-wc-donations').'</p></div>');
	}

	include(dirname(__FILE__).'/admin.php');
}

// Disable price display in frontend for Donation products
add_filter('woocommerce_get_price_html', 'hm_wcdon_get_price_html', 10, 2);
function hm_wcdon_get_price_html($price, $product) {
	if ($product->product_type == 'donation')
		return (is_admin() ? 'Variable' : '');
	else
		return $price;
}

// Add amount field before add to cart button
add_action('woocommerce_before_add_to_cart_button', 'hm_wcdon_before_add_to_cart_button');
function hm_wcdon_before_add_to_cart_button() {
	global $product;
	if ($product->product_type == 'donation') {
		echo('<span class="wc-donation-amount">
				<label for="donation_amount_field">'.__('Amount', 'hm-wc-donations').':</label>
				<input type="number" name="donation_amount" id="donation_amount_field" size="5" min="0" step="0.01" value="'.number_format($product->price, 2, '.', '').'" />
			</span>');
	}
}

// Add Donation product type option
add_filter('product_type_selector', 'hm_wcdon_product_type_selector');
function hm_wcdon_product_type_selector($productTypes) {
	$productTypes['donation'] = 'Donation';
	return $productTypes;
}

// Hide all but the General and Advanced product data tabs for Donation products
add_filter('woocommerce_product_data_tabs', 'hm_wcdon_product_data_tabs', 10, 1);
function hm_wcdon_product_data_tabs($tabs) {
	foreach ($tabs as $tabId => $tabData) {
		if ($tabId != 'general' && $tabId != 'advanced') {
			$tabs[$tabId]['class'][] = 'hide_if_donation';
		}
	}
	return $tabs;
}

// Create the WC_Product_Donation class
add_filter('plugins_loaded', 'hm_wcdon_create_product_type');
function hm_wcdon_create_product_type() {
	if (!class_exists('WC_Product_Simple'))
		return;
	class WC_Product_Donation extends WC_Product_Simple {
		function __construct($product) {
			parent::__construct($product);
			$this->product_type = 'donation';
		}
		function is_sold_individually() { return true; }
		function is_taxable() { return false; }
		function needs_shipping() { return false; }
		function is_virtual() { return true; }
		function add_to_cart_text() { return __('Donate', 'hm-wc-donations'); }
		function single_add_to_cart_text() { return __('Donate', 'hm-wc-donations'); }
		function add_to_cart_url() { return get_permalink($this->id); }
	}
}

// Disable AJAX add to cart for Donation products
add_filter('woocommerce_loop_add_to_cart_link', 'hm_wcdon_add_to_cart_link', 10, 2);
function hm_wcdon_add_to_cart_link($linkHtml, $product) {
	return ($product->product_type == 'donation' ? str_replace('ajax_add_to_cart', '', $linkHtml) : $linkHtml);
}


// Add the default amount field to the General product data tab
add_filter('woocommerce_product_options_general_product_data', 'hm_wcdon_product_options_general');
function hm_wcdon_product_options_general() {
	global $thepostid;
	echo('<div class="options_group show_if_donation">');
	woocommerce_wp_text_input(array('id' => 'donation_default_amount', 'label' => __('Default amount', 'hm-wc-donations'), 'value' => get_post_meta($thepostid, '_price', true), 'data_type' => 'price'));
	echo('</div>');
}

// Save the default donation amount
add_action('woocommerce_process_product_meta_donation', 'hm_wcdon_process_product_meta');
function hm_wcdon_process_product_meta($productId) {
	$price = ($_POST['donation_default_amount'] === '') ? '' : wc_format_decimal($_POST['donation_default_amount']);
	update_post_meta($productId, '_price', $price);
	update_post_meta($productId, '_regular_price', $price);
}

// Process donation amount when a Donation product is added to the cart
add_filter('woocommerce_add_cart_item', 'hm_wcdon_add_cart_item');
function hm_wcdon_add_cart_item($item) {
	if ($item['data']->product_type == 'donation') {
		if (isset($_POST['donation_amount']) && is_numeric($_POST['donation_amount']) && $_POST['donation_amount'] > 0)
			$item['donation_amount'] = $_POST['donation_amount']*1;
		else
			$item['donation_amount'] = 0;
		$item['data']->price = $item['donation_amount'];
	}
	return $item;
}

// Use the Simple product type's add to cart button for Donation products
add_action('woocommerce_donation_add_to_cart', 'hm_wcdon_add_to_cart_template');
function hm_wcdon_add_to_cart_template() {
	do_action('woocommerce_simple_add_to_cart');
}

// Set Donation product price when loading the cart
add_filter('woocommerce_get_cart_item_from_session', 'hm_wcdon_get_cart_item_from_session');
function hm_wcdon_get_cart_item_from_session($session_data) {
	if ($session_data['data']->product_type == 'donation' && isset($session_data['donation_amount']))
			$session_data['data']->price = $session_data['donation_amount'];
	return $session_data;
}

// Add the donation amount field to the cart display
add_filter('woocommerce_cart_item_price', 'hm_wcdon_cart_item_price', 10, 3);
function hm_wcdon_cart_item_price($price, $cart_item, $cart_item_key) {
	return (($cart_item['data']->product_type == 'donation' && !hm_wcdon_get_option('disable_cart_amount_field')) ? 
				'<input type="number" name="donation_amount_'.$cart_item_key.'" size="5" min="0" step="0.01" value="'.$cart_item['data']->price.'" />' :
				$price);
}

// Process donation amount fields in cart updates
add_filter('woocommerce_update_cart_action_cart_updated', 'hm_wcdon_update_cart');
function hm_wcdon_update_cart($cart_updated) {
	if (hm_wcdon_get_option('disable_cart_amount_field')) {
		return $cart_updated;
	}
	foreach (WC()->cart->get_cart() as $key => $cartItem) {
		if ($cartItem['data']->product_type == 'donation' && isset($_POST['donation_amount_'.$key])
				&& is_numeric($_POST['donation_amount_'.$key]) && $_POST['donation_amount_'.$key] > 0 && $_POST['donation_amount_'.$key] != $cartItem['data']->price) {
			$cartItem['donation_amount'] = $_POST['donation_amount_'.$key]*1;
			$cartItem['data']->price = $cartItem['donation_amount'];
			WC()->cart->cart_contents[$key] = $cartItem;
			$cart_updated = true;
		}
	}
	return $cart_updated;
}

// Get default plugin options
function hm_wcdon_default_options() {
	global $hm_wcdon_default_options;
	if (!isset($hm_wcdon_default_options)) {
		$hm_wcdon_default_options = array(
			'disable_cart_amount_field' => 0
		);
	}
	return $hm_wcdon_default_options;
}

// Retrieve plugin options
function hm_wcdon_get_option($option) {
	global $hm_wcdon_options;
	if (!isset($hm_wcdon_options)) {
		$hm_wcdon_options = array_merge(hm_wcdon_default_options(), get_option('hm_wcdon_options', array()));
	}
	return (isset($hm_wcdon_options[$option]) ? $hm_wcdon_options[$option] : null);
}

// Save plugin options
function hm_wcdon_set_options($options) {
	$defaultOptions = hm_wcdon_default_options();
	return update_option('hm_wcdon_options', array_merge($defaultOptions, array_intersect_key($options, $defaultOptions)), false);
}


/* Review/donate notice */

register_activation_hook(__FILE__, 'hm_wcdon_first_activate');
function hm_wcdon_first_activate() {
	$pre = 'hm_wcdon';
	$firstActivate = get_option($pre.'_first_activate');
	if (empty($firstActivate)) {
		update_option($pre.'_first_activate', time());
	}
}
if (is_admin() && get_option('hm_wcdon_rd_notice_hidden') != 1 && time() - get_option('hm_wcdon_first_activate') >= (14*86400)) {
	add_action('admin_notices', 'hm_wcdon_rd_notice');
	add_action('wp_ajax_hm_wcdon_rd_notice_hide', 'hm_wcdon_rd_notice_hide');
}
function hm_wcdon_rd_notice() {
	$pre = 'hm_wcdon';
	$slug = 'donations-for-woocommerce';
	echo('
		<div id="'.$pre.'_rd_notice" class="updated notice is-dismissible"><p>Do you use the <strong>Donations for WooCommerce</strong> plugin?
		Please support our free plugin by <a href="https://wordpress.org/support/view/plugin-reviews/'.$slug.'" target="_blank">writing a review</a> and/or <a href="https://potentplugins.com/donate/?utm_source='.$slug.'&amp;utm_medium=link&amp;utm_campaign=wp-plugin-notice-donate-link" target="_blank">making a donation</a>!
		Thanks!</p></div>
		<script>jQuery(document).ready(function($){$(\'#'.$pre.'_rd_notice\').on(\'click\', \'.notice-dismiss\', function(){jQuery.post(ajaxurl, {action:\'hm_wcdon_rd_notice_hide\'})});});</script>
	');
}
function hm_wcdon_rd_notice_hide() {
	$pre = 'hm_wcdon';
	update_option($pre.'_rd_notice_hidden', 1);
}
?>