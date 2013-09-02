=== WooCommerce Bulk Discount ===
Contributors: Rene Puchinger
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&amp;hosted_button_id=6VEQ8XXK6B3UE
Tags: woocommerce, bulk, discount
Requires at least: 3.5.0
Tested up to: 3.6
Stable tag: 1.2.2
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Apply fine-grained bulk discounts to items in the shopping cart, dependently on ordered quantity and on concrete product.

== Description ==

WooCommerce Bulk Discount Plugin makes possible to apply fine-grained bulk discounts
to items in the shopping cart, dependently on ordered quantity and on concrete
product.

The plugin is designed for WooCommerce versions 2.0.x.

Let us examine some examples of usage.

*   You may want to feature such discount policy in your store that if a customer
orders more than 5 items of some product, he/she would pay the price of this order
line lowered by 10%. 

*   Or you may want different policy, for example offering 5% discount if customer
orders more than 10 items of a product and 10% discount if he/she orders more than
20 items.

The settings for discounts are simple yet extensive, allowing wide range of discount
policies to be adopted across your store.

== Installation ==

1. Download the latest version and extract it in the /wp-content/plugins/ directory
2. Activate the plugin through the 'Plugins' menu in WordPress

Once the plugin is activated, you can use it as follows:

1. First navigate to WooCommerce settings. Under the Bulk Discount tab, find the global
configuration for bulk discounts. Make sure "Bulk Discount enabled" is checked and optionally
fill information about discounts which will be visible on cart page. You can include HTML
markup in the text - you can, for instance, include a link to your page with discount
policy. In case you need the plugin to work well with product variations, make sure that the
"Treat product variations separately" option is unchecked. Save the settings.

2. Navigate to Products and choose a product for which you want to create discount policy.
In Product Data panel, click Bulk Dicounts and optionally fill information about discount
which will be visible in product description.

3. Click "Add discount line" button to create a policy. Quantity (min.) means minimal
number of ordered items so that the (second textbox) Discount applies. It is possible to
add up to five discount lines to fine-tune the discount setting.

== Frequently Asked Questions ==

= Are multiple discounts supported? How many levels of discounting may be applied? =

Yes, multiple discounts (related to single product) are supported. Currently one may set up
to 5 discount lines. That should be enough for reasonable fine-tuning of the discount.

= Is it possible to handle discount for product variations as a whole? =
Yes, in case you have several product variations in your store and you need to apply discount
to all the purchased variations, please upgrade to version 1.2 (or newer) of this plugin.
This behaviour can be disabled in Bulk Discount settings.

= Is the plugin i18n ready? =
Yes, the plugin supports localization files. Currently English and Czech locales are
implemented. You can add support for your language as well.

== Screenshots ==

1. Enabling the plugin and setting information about discounts policy.
2. Setting the discount lines. Often only one line is sufficient.

== Changelog ==

= 1.2.2 =
* (2 Sep 2013) Changing default state of product variation setting checkbox for default behaviour of previous versions.

= 1.2.1 =
* (26 Aug 2013) Making the plugin i18n ready, currently there are English and Czech locales.

= 1.2 =
* (24 Aug 2013) Possibility to treat product variations as a whole when discounting.
* CSS changes.
* Show the applied discount in percents on hovering the item price in the cart.

= 1.1.1 =
* (21 Aug 2013) Plugin settings moved to separate tab under WooCommerce > Settings.
* CSS refined.
* code cleanup.
* more code comments added.

= 1.1 =
* (7 Jul 2013) resolved major issue of incorrect discount application in some cases.
* code optimization.
* cleaned up some code.

= 1.0.1 =
* (5 Jul 2013) cleaned up some code.

= 1.0 =
* Stable version.

== Upgrade Notice ==

= 1.2.2 =
Maintenance release

= 1.2.1 =
Release with i18n feature.

= 1.2 =
Release with new features.

= 1.1.1 =
Maintenance release.

= 1.1 =
Important bugfix release. Upgrading recommended as soon as possible.

= 1.0.1 =
This version has minor change in code without any noticeable impact.

= 1.0 =
N/A.

== Example of setting a discount ==

Below is an example of setting a multiple discount for some product with three discount lines. 

Example:

1. Quantity (min.) = 3, Discount (%) = 5
2. Quantity (min.) = 8, Discount (%) = 10
3. Quantity (min.) = 15, Discount (%) = 15

If a customer ordered, say, 12 items of the product which costs $15 per item, the second
discount line would apply. The customer than pays 12 * 15 = 225 dollars in total minus
10%, which yields $202.5. Note that this discount policy only applies to a concrete product - other
products may have their own (possibly different) discount policies.
