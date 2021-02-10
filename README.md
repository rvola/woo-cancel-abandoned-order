# WooCommerce Cancel Abandoned Order 
[![Donate](https://img.shields.io/badge/Donate-PayPal-green.svg)](https://www.paypal.me/rvola)

Cancel "on hold" orders after a certain number of days or by hours

![banner](/.github/banner.jpg)

## Description

**WooCommerce Cancel Abandoned Order** allows you to add a small option that will take care of dealing with "abandoned" commands.

If you have check or transfer type orders for example, you will be able to set a maximum number of days or by hours to receive the payment.

WooCommerce Cancel Abandoned Order, will take care of checking this and change the status of the order to "Cancel" if you have not received payment on time.

## Installation

This section describes how to install the plugin and get it working.

1. Upload the plugin files to the `/wp-content/plugins/woo-cancel-abandoned-order` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress
3. By default you can control the orders on the payment gateways: Check and BACS. Go to the options of the payment pages on WooCommerce.

*To add another payment gateway, simply use the **woo_cao_gateways** filters, more information on the [Wiki](https://github.com/rvola/woo-cancel-abandoned-order/wiki)*

## Requirement

* PHP minimal: **7.0**
* WordPress minimal: **4.0**
* WooCommerce minimal : **2.2**

## Hooks
_Action_

* **woo_cao_cancel_order** ($order_id) : After cancel order.

_Filters_

* **woo_cao_gateways** : Adds a payment gateway for the control.
* **woo_cao_before_cancel_order** : Before canceling the order, send the order_id and the WC_Order if you do not want to cancel this order. Expect an exact return of true.
* **woo_cao_message_cancel_order** : Allows you to modify the note when canceling the order. Handy if you use the 'woo_cao_before_cancel_order' filter.
* **woo_cao_date_order** ($old_date, $gateway, $mode) : Change the calculation date for pending orders.
* **woo_cao_default_hours** : Default value of the number of hours for order processing.
* **woo_cao_default_days** : Default value of the number of days for order processing.
* **woo_cao_statustocancel** ($status) : Allows you to add or change which WooCommerce order status the plugin should cancel.

## Wiki
* [A help section on the code is available here](https://github.com/rvola/woo-cancel-abandoned-order/wiki)

## Frequently Asked Questions

#### Does this plugin use Action Scheduler

Yes we have started the migration to Action Scheduler (recurring action) since version 1.9.0

#### What does the plugin do?

Depending on the options defined in the payment gateway options page, the system will cancel orders whose payments have not been received.

#### Mode
You can cancel orders in hours or days.
For example, if I put the mode "Hourly", I can cancel orders pending after 2 hours.
Another example, in mode "Daily", I can cancel orders that I have not received payment within 7 days.

The execution of the cleaning is done like this:
Mode **"hourly"**: every hour to 00 minutes
Mode **"Daily"**: every day at 0:00

_Since version 1.9.0, the action is no longer executed at XXh00. Refer to the "Scheduled Actions" tab of the "Status" page of WooCommerce._

#### I would like to cancel orders pending payment

Follow the [tutorial here](https://github.com/rvola/woo-cancel-abandoned-order/wiki/Change-the-status-type-for-the-cancellation-process) to change the status of orders to cancel. By default the "wc-on-hold" commands are canceled.

## Links

* [**Changelog**](https://github.com/rvola/woo-cancel-abandoned-order/blob/master/CHANGELOG.md)
* [**Download on WordPress**](https://wordpress.org/plugins/woo-cancel-abandoned-order/)
* [**WIKI**](https://github.com/rvola/woo-cancel-abandoned-order/wiki)
