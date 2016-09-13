<?php
echo('
	<div class="wrap">
		<h2>'.__('Donations for WooCommerce', 'hm-wc-donations').'</h2>
		<h3>'.__('Usage Instructions', 'hm-wc-donations').'</h3>
		<p>'.esc_html__('Simply create a new WooCommerce product for each type of donation you would like to accept. Under Product Data, set the product type to "Donation". Optionally, set the default donation amount in the General section. You\'ll probably also want to ensure that product reviews are disabled in the Advanced section. That\'s all!', 'hm-wc-donations').'</p>
		<h3 style="margin-bottom: 0;">'.__('Settings', 'hm-wc-donations').'</h3>
		<form action="" method="post">
			<input type="hidden" name="save" value="1" />
			<table class="form-table" style="margin-bottom: 30px;">
				<tr valign="top">
					<th scope="row">
						<label>'.__('Checkout', 'hm-wc-donations').':</label>
					</th>
					<td>
						<label>
							<input type="checkbox" name="disable_cart_amount_field"'.(hm_wcdon_get_option('disable_cart_amount_field') ? ' checked="checked"' : '').' />
							'.__('Disable donation amount field in cart', 'hm-wc-donations').'
						</label>
					</td>
				</tr>
				<tr valign="top">
					<th colspan="2">
						<button type="submit" class="button-primary">'.__('Save Settings', 'hm-wc-donations').'</button>
					</th>
				</tr>
			</table>
		</form>
');
$potent_slug = 'donations-for-woocommerce';
include(__DIR__.'/plugin-credit.php');
echo('
	</div>
');
?>