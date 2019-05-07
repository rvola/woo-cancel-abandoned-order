=== WooCommerce Cancel Abandoned Order ===
Contributors: rvola
Donate link: https://www.paypal.me/rvola
Tags: woocommerce, cancel, order, pending, on hold, gateway
Requires PHP: 7.0
Requires at least: 4.0
Tested up to: 5.2
Stable tag: 1.6.0
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.html

Cancel "on hold" orders after a certain number of days or by hours

== Description ==

**WooCommerce Cancel Abandoned Order** allows you to add a small option that will take care of dealing with "abandoned" commands.

If you have check or transfer type orders for example, you will be able to set a maximum number of days or by hours to receive the payment.

WooCommerce Cancel Abandoned Order, will take care of checking this and change the status of the order to "Cancel" if you have not received payment on time.

[**GitHub**](https://github.com/rvola/woo-cancel-abandoned-order) | [**Donate**](https://www.paypal.me/rvola)

== Installation ==

This section describes how to install the plugin and get it working.

1. Upload the plugin files to the `/wp-content/plugins/woo-cancel-abandoned-order` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress
3. By default you can control the orders on the payment gateways: Check and BACS. Go to the options of the payment pages on WooCommerce.

*To add another payment gateway, simply use the **woo_cao_gateways** filters, more information on the [Wiki](https://github.com/rvola/woo-cancel-abandoned-order/wiki)*

== Requirement ==

* PHP minimal: **7.0**
* WordPress minimal: **4.0**
* WooCommerce minimal : **2.2**

== Hooks ==
_Action_

* **woo_cao_cancel_order** ($order_id) : After cancel order.
* **woo_cao_restock_item** ($product_id, $old_stock, $new_stock, $order, $product ) : After restock product.

_Filters_

* **woo_cao_gateways** : Adds a payment gateway for the control.
* **woo_cao_date_order** ($old_date, $gateway, $mode) : Change the calculation date for pending orders.
* **woo_cao_default_hours** : Default value of the number of hours for order processing.
* **woo_cao_default_days** : Default value of the number of days for order processing.
* **woo_cao_statustocancel** ($status) : Allows you to add or change which WooCommerce order status the plugin should cancel.


== Wiki ==
* [A help section on the code is available here](https://github.com/rvola/woo-cancel-abandoned-order/wiki)

== Frequently Asked Questions ==

= What does the plugin do? =

Depending on the options defined in the payment gateway options page, the system will cancel orders whose payments have not been received.

= Mode =
You can cancel orders in hours or days.
For example, if I put the mode "Hourly", I can cancel orders pending after 2 hours.
Another example, in mode "Daily", I can cancel orders that I have not received payment within 7 days.

The execution of the cleaning is done like this:
Mode **"Hourly"**: every hour to 00 minutes
Mode **"Daily"**: every day at 0:00

= Restock =

If you checked the box to enable this feature in the gateway, the system will restock each product line of the abandoned order.

= I would like to cancel orders pending payment =
Follow the [tutorial here](https://github.com/rvola/woo-cancel-abandoned-order/wiki/Change-the-status-type-for-the-cancellation-process) to change the status of orders to cancel. By default the "on-hold" commands are canceled.

= I want to make suggestions =
We’re glad you want to help us improve **WooCommerce Cancel Abandoned Order**!
The GIT repository is available here [https://github.com/rvola/woo-cancel-abandoned-order](https://github.com/rvola/woo-cancel-abandoned-order)

== Changelog ==

= 1.6.0 / 2019-05-07 =
* NEW / Order status hook for the cancel process.
* MINOR / code style call Class external.

= 1.5.1 / 2019-04-03 =
* ✔︎ Compatibility WP 5.2
* ✔︎ Compatibility WOO 3.6

= 1.5.0 / 2018-10-22 =
* NEW / Filter 'woo_cao_date_order'. Change the calculation date for pending orders.
* ✔︎ Compatibility WP 5.0
* CHECK / End of support PHP 5.6 http://php.net/supported-versions.php
* USELESS / Options 'value' in field checkbox.

= 1.4.1 / 2018-10-03 =
* UPDATED / Rename file updater
* FIX / Updater crash with older PHP < 7.0

= 1.4.0 / 2018-10-02 =
* NEW / Class Updater for modifications
* NEW / The plugin can work in hours
* NEW / Method 'required' for class WP
* UPDATED / Explain in admin for restock
* NEW / Load assets by file
* ✔︎ Compatibility WooCommerce 3.5

= 1.3.1 / 2018-05-23 =
* ✔︎ Compatibility WooCommerce 3.4

= 1.3.0 / 2018-03-01 =
* NEW restock
* NEW hook do_action ‘woo_cao_cancel_order’
* NEW method ‘cancel_order’
* Rename var ‘orders_id’ by’ ‘orders’
* Rename method ‘cancel_order’ by ‘check_order’
* Delete notice deprecated hook
* Refactor class
* Refactor new instance
* Changelog WP

= 1.2.1 / 2018-02-24 =
* Rename class + namespace
* Update change methode ‘load’ > ‘intance'
* FIX / plugin_row_meta (class in includes)
* MINOR / delete link github plugin_row_meta
* MINOR / update readme

= 1.2.0 / 2018-02-22 =
* Deprecated hook 'woo_cao-gateways' by 'woo_cao_gateways'
* Deprecated hook 'woo_cao-default_days' by 'woo_cao_default_days'
* Wordpress conventions
* Move Class in includes

= 1.1.3 / 2018-01-31 =
* ✔︎ Compatibility WooCommerce 3.3.0

= 1.1.2 / 2017-10-30 =
* Fix translate domain WP (folder)

= 1.1.1 / 2017-10-30 =
* Fix translate domain WP

= 1.1.0 / 2017-10-30 =
* Add extension licence files
* Rename path plugin (WP)
* Move additional link (plugin_row_meta)

= 1.0.0 / 2017-10-29 =
* Launch
