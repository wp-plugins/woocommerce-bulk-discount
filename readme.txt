=== WooCommerce Bulk Discount ===
Contributors: Rene Puchinger
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&amp;hosted_button_id=6VEQ8XXK6B3UE
Tags: woocommerce, bulk, discount
Requires at least: 3.5
Tested up to: 3.6
Stable tag: 2.0.10
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Apply fine-grained bulk discounts to items in the shopping cart. Enjoy version 2.0 with many new features!

== Description ==

WooCommerce Bulk Discount Plugin makes possible to apply fine-grained bulk discounts
to items in the shopping cart, dependently on ordered quantity and on concrete
product.

WooCommerce version 2.0+ required.

Let us examine some examples of usage.

*   You may want to feature such discount policy in your store that if customer
orders more than 5 items of some product, he/she would pay the price of this order
line lowered by 10%. 

*   Or you may want different policy, for example offering 5% discount if customer
orders more than 10 items of a product and 10% discount if he/she orders more than
20 items.

*   Bulk Discounts supports flat discount in currency units as well,
enabling you to handle scenarios like deducting fixed value of, say $10 from item subtotal.
For example, when customer orders more than 10 items (say, 15, 20, etc.), a discount of $10
will be applied only on the subtotal price.

The settings for discounts are simple yet extensive, allowing wide range of discount
policies to be adopted in your store.

Here is the list of main features:

*   Possibility of setting percentage bulk discount or flat (fixed) bulk discount in currency units.
*   Bulk discount for product variations supported to treat them as a whole when discounting. 
*   Discount is better visible and is present on several locations (see below).
*   Discount is visible on Checkout page
*   Discount is visible on Order Details page
*   Discount is visible in WooCommerce order e-mails and invoice as well.
*   Showing the applied discount on hovering the item price in the cart.   
*   Possibility of easily changing the styling of price before and after discount.

WooCommerce Bulk Discount has been localised to these languages:

*   English
*   Czech
*   Portuguese (translated by João)

== Installation ==

1. Download the latest version and extract it in the /wp-content/plugins/ directory
2. Activate the plugin through the 'Plugins' menu in WordPress

Once the plugin is activated, you can use it as follows:

1. First navigate to WooCommerce settings. Under the Bulk Discount tab, find the global
configuration for bulk discounts. Make sure "Bulk Discount enabled" is checked and optionally
fill information about discounts which will be visible on cart page. You can include HTML
markup in the text - you can, for instance, include a link to your page with discount
policy. In case you need the plugin to work well with product variations, make sure that the
"Treat product variations separately" option is unchecked. Since version 2.0 you
may choose to use flat discount applied to cart item subtotal. Optionally you may also
modify the CSS styles for old value and new value which is displayed in the cart.
Save the settings.

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

= Is only percentage discount implemented? =
Since version 2.0 another type fo discount is added, allowing to set fixed discount in currency units
for cart item subtotal.

= Will the discount be visible on WooCommerce e-mails and Order status as well? =
Yes. Since version 2.0, this feature has been implemented.

= Is it possible to handle discount for product variations as a whole? =
Yes, in case you have several product variations in your store and you need to apply discount
to all the purchased variations, please upgrade to the latest version of Bulk Discount.
This behaviour can be disabled in Bulk Discount settings.

= Is the plugin i18n ready? =
Yes, the plugin supports localization files. Currently English and Czech locales are
implemented. You can add support for your language as well.

= Can you provide an example of setting percentage bulk discount? =
Sure. Below is an example of setting a bulk discount for some product with three discount lines. 

1. Quantity (min.) = 3, Discount (%) = 5
2. Quantity (min.) = 8, Discount (%) = 10
3. Quantity (min.) = 15, Discount (%) = 15

If customer orders, say, 12 items of the product which costs $15 per item, the second
discount line will apply. The customer than pays 12 * 15 = 225 dollars in total minus
10%, which yields $202.5. Note that this discount policy only applies to a concrete product -- other
products may have their own (possibly different) discount policies.

= Can you provide an example of setting flat bulk discount? =
Example for flat discount follows:

1. Quantity (min.) = 10, Discount ($) = 10
2. Quantity (min.) = 30, Discount ($) = 20

If customer orders, say, 15 items of the product which costs $10 per item, the first discount
line will apply and the customer will pay (15 * 10) - 10 dollars. If the customers orders
50 items, the second dicount line will apply and the final price will be (50 * 10) - 20 dollars.
Setting bulk discounts couldn't have been easier.

== Screenshots ==

1. Bulk Discount Settings page.
2. Setting the discount lines (see FAQ for further explanation). Often only one discount line is sufficient.
3. Showing example of flat bulk discount visibility on cart page.
4. Bulk discount is visible on Checkout Page as well.
5. Bulk Discount is visible in WooCommerce e-mails.
6. Example of percentage bulk discount visibility on cart page.

== Changelog ==

= 2.0.10 =
* (10 Oct 2013) Fixing a bug which might have resulted in collision with few other WooCommerce plugins.

= 2.0.9 =
* (9 Oct 2013) Translations updated.
* Minor code formatting changes.

= 2.0.8 =
* (6 Oct 2013) Refined discount precision for percentage discounts with decimal point (no impact on integer discounts).
* Translations updated.

= 2.0.7 =
* (29 Sep 2013) Added configuration on which locations should the discount information be visible.

= 2.0.6 =
* (18 Sep 2013) Added Portuguese translation.

= 2.0.5 =
* (12 Sep 2013) Fixed displaying bug (for different scenario) which has no impact on discount computations.

= 2.0.4 =
* (11 Sep 2013) Fixed displaying bug (for different scenario) which has no impact on discount computations.

= 2.0.3 =
* (11 Sep 2013) Fixed displaying bug which has no impact on discount computations.

= 2.0.2 =
* (5 Sep 2013) Important maintenance release. Now the bulk discount metadata are stored to orders as well, making it possible to correctly display discounts for past orders. One can also change the plugin settings any time.
* Added quick link to settings on Wordpress > Plugins page.

= 2.0.1 =
* (3 Sep 2013) Added a warning for changing discount type on a site with existing orders (currently it is safe to change discount type only for the first time after installing the plugin or on fresh WooCommerce installation with no orders).

= 2.0 =
* (3 Sep 2013) Possibility of setting a flat (fixed) discount in currency units.
* Discount is better visible and is present on more places (see below).
* Discount is visible on Checkout page
* Discount is visible on Order Details page
* Discount is visible in WooCommerce order e-mails and invoice as well.
* Possibility of easily changing the styling of price before and adter discount.

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

= 2.0.10 =
Bugfix release.

= 2.0.9 =
Maintenance release.

= 2.0.8 =
Maintenance release.

= 2.0.7 =
Release with new features.

= 2.0.6 =
Minor maintenance release.

= 2.0.5 =
Bugfix release. Please update immediately.

= 2.0.4 =
Bugfix release. Please update immediately.

= 2.0.3 =
Bugfix release. Please update immediately.

= 2.0.2 =
Important maintenance release. Please update immediately.

= 2.0.1 =
Maintenance release.

= 2.0 =
Major release with new features.

= 1.2.2 =
Maintenance release.

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
