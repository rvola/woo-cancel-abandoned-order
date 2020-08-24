# Changelog

## [1.8.1](https://github.com/rvola/woo-cancel-abandoned-order/tree/1.8.1) - 2020-08-24
[Full Changelog](https://github.com/rvola/woo-cancel-abandoned-order/compare/1.8.0...1.8.1)

* ✔︎ Compatibility WOO 4.4
* ✔︎ Compatibility WP 5.5

## [1.8.0](https://github.com/rvola/woo-cancel-abandoned-order/tree/1.8.0) - 2020-04-11
[Full Changelog](https://github.com/rvola/woo-cancel-abandoned-order/compare/1.7.2...1.8.0)

* NEW / Filter 'woo_cao_message_cancel_order' to modify the order note for cancellation. Useful if you use the filter 'woo_cao_before_cancel_order'
* MOVE / filter #7 and rename clean + add WC_Order class in filter (more possibility)
* woo_cao_order_id filter added by Pexle Chris

## [1.7.2](https://github.com/rvola/woo-cancel-abandoned-order/tree/1.7.2) - 2020-03-09
[Full Changelog](https://github.com/rvola/woo-cancel-abandoned-order/compare/1.7.1...1.7.2)

* ✔︎ Compatibility WOO 4.0
* ✔︎ Compatibility WP 5.4

## [1.7.1](https://github.com/rvola/woo-cancel-abandoned-order/tree/1.7.1) - 2020-01-22
[Full Changelog](https://github.com/rvola/woo-cancel-abandoned-order/compare/1.7.0...1.7.1)

* ✔︎ Compatibility WOO 3.8

## [1.7.0](https://github.com/rvola/woo-cancel-abandoned-order/tree/1.7.0) - 2019-10-23
[Full Changelog](https://github.com/rvola/woo-cancel-abandoned-order/compare/1.6.1...1.7.0)

* ✔︎ Compatibility WP 5.3
* ✔︎ Compatibility WOO 3.8
* PHPDock missing
* Added an icon in the order notes to identify the author (WOOCAO) - not retroactive
* Escape i18n html
* DELETED / Restock option / WooCommerce the management since June 2018 ...


## [1.6.1](https://github.com/rvola/woo-cancel-abandoned-order/tree/1.6.1) - 2019-06-04
[Full Changelog](https://github.com/rvola/woo-cancel-abandoned-order/compare/1.6.0...1.6.1)

* FIX / Incorrect date format with time cancellation.

## [1.6.0](https://github.com/rvola/woo-cancel-abandoned-order/tree/1.6.0) - 2019-05-07
[Full Changelog](https://github.com/rvola/woo-cancel-abandoned-order/compare/1.5.1...1.6.0)

* NEW / Order status hook for the cancel process.
* MINOR / code style call Class external.

## [1.5.1](https://github.com/rvola/woo-cancel-abandoned-order/tree/1.5.1) - 2019-04-03
[Full Changelog](https://github.com/rvola/woo-cancel-abandoned-order/compare/1.5.0...1.5.1)

* ✔︎ Compatibility WP 5.2
* ✔︎ Compatibility WOO 3.6

## [1.5.0](https://github.com/rvola/woo-cancel-abandoned-order/tree/1.5.0) - 2018-10-22
[Full Changelog](https://github.com/rvola/woo-cancel-abandoned-order/compare/1.4.1...1.5.0)

* NEW / Filter 'woo_cao_date_order'. Change the calculation date for pending orders.
* ✔︎ Compatibility WP 5.0
* CHECK / End of support PHP 5.6 http://php.net/supported-versions.php
* USELESS / Options 'value' in field checkbox.

## [1.4.1](https://github.com/rvola/woo-cancel-abandoned-order/tree/1.4.1) - 2018-10-03
[Full Changelog](https://github.com/rvola/woo-cancel-abandoned-order/compare/1.4.0...1.4.1)

* UPDATED / Rename file updater
* FIX / Updater crash with older PHP < 7.0

## [1.4.0](https://github.com/rvola/woo-cancel-abandoned-order/tree/1.4.0) - 2018-10-02
[Full Changelog](https://github.com/rvola/woo-cancel-abandoned-order/compare/1.3.2...1.4.0)

* NEW / Class Updater for modifications
* NEW / The plugin can work in hours
* NEW / Method 'required' for class WP
* UPDATED / Explain in admin for restock
* NEW / Load assets by file
* ✔︎ Compatibility WooCommerce 3.5

## [1.3.2](https://github.com/rvola/woo-cancel-abandoned-order/tree/1.3.2) - 2018-06-01
[Full Changelog](https://github.com/rvola/woo-cancel-abandoned-order/compare/1.3.1...1.3.2)

* Minor / change requires version format
* New / translate file es_AR
* Fix / call translate files

## [1.3.1](https://github.com/rvola/woo-cancel-abandoned-order/tree/1.3.1) - 2018-05-23
[Full Changelog](https://github.com/rvola/woo-cancel-abandoned-order/compare/1.3.0...1.3.1)

* ✔︎ Compatibility WooCommerce 3.4.0

## [1.3.0](https://github.com/rvola/woo-cancel-abandoned-order/tree/1.3.0) - 2018-03-01
[Full Changelog](https://github.com/rvola/woo-cancel-abandoned-order/compare/1.2.1...1.3.0)

* NEW restock
* NEW hook do_action ‘woo_cao_cancel_order’
* NEW method ‘cancel_order’
* Rename var ‘orders_id’ by’ ‘orders’
* Rename method ‘cancel_order’ by ‘check_order’
* Delete notice deprecated hook
* Refactor class
* Refactor new instance
* Changelog WP

## [1.2.1](https://github.com/rvola/woo-cancel-abandoned-order/tree/1.2.1) - 2018-02-24
[Full Changelog](https://github.com/rvola/woo-cancel-abandoned-order/compare/1.2.0...1.2.1)

* Rename class + namespace
* Update change methode ‘load’ > ‘intance'
* FIX / plugin_row_meta (class in includes)
* MINOR / delete link github plugin_row_meta
* MINOR / update readme

## [1.2.0](https://github.com/rvola/woo-cancel-abandoned-order/tree/1.2.0) - 2018-02-22
[Full Changelog](https://github.com/rvola/woo-cancel-abandoned-order/compare/1.1.3...1.2.0)

* Deprecated hook `woo_cao-gateways` by `woo_cao_gateways`
* Deprecated hook `woo_cao-default_days` by `woo_cao_default_days`
* Wordpress conventions
* Move Class in includes`

## [1.1.3](https://github.com/rvola/woo-cancel-abandoned-order/tree/1.1.3) - 2018-01-31
[Full Changelog](https://github.com/rvola/woo-cancel-abandoned-order/compare/1.1.2...1.1.3)

* ✔︎ Compatibility WooCommerce 3.3.0

## [1.1.2](https://github.com/rvola/woo-cancel-abandoned-order/tree/1.1.2) - 2017-10-30
[Full Changelog](https://github.com/rvola/woo-cancel-abandoned-order/compare/1.0.0...1.1.2)

* Add extension licence files
* Rename path plugin (WP)
* Move additional link (plugin_row_meta)
* Fix translate domain WP
* Fix translate domain WP (folder)

## [1.0.0](https://github.com/rvola/woo-cancel-abandoned-order/tree/1.0.0) - 2017-10-29

* Launch
