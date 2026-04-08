# Tickera Order Ticket Fetcher Addon
![GitHub Release](https://img.shields.io/github/v/release/distool-de/Tickera-Order-Ticket-Fetcher-Addon)

## Description
This WordPress plugin serves as an addon for Tickera, enabling the querying of ticket instances based on order information.

## Installation
1. Download the plugin zip file.
2. Upload the plugin directory to the `/wp-content/plugins/` directory.
3. Activate the plugin through the 'Plugins' menu in WordPress.

## Usage
This plugin adds a function to the `init` hook, allowing you to retrieve ticket instances based on order information via a custom endpoint. To query ticket instances, you need to provide the `order_id` and `order_key` parameters in the URL. 

### Parameters
- `order_id`: The ID of the order.
- `order_key`: The key associated with the order.

### Example Usage
```
https://yourwebsite.com/?order_id=123&order_key=your_order_key
```

## Requirements
- WordPress version: 6.8.1 or higher
- PHP version: 8.0 or higher
- Required Plugins: tickera-event-ticketing-system, WooCommerce 8.2+

## Author
- Name: Distool.de
- Website: [Distool.de](https://distool.de/)

## Version
0.9.2

## Text Domain
tcotf

## Update URI
[https://distool.de/tickera](https://distool.de/tickera)

## Disclaimer
The authors and distributors of this plugin do not take any responsibility for any consequences resulting from the use of this software. Use at your own risk.



## Changelog
- **Version 0.9.2**
  - Add new way to calc. hash

- **Version 0.9.1:**
  - WooCommerce HPOS compatibility: replaced `get_posts(shop_order)` with `wc_get_order()` (required for WC 8.2+).
  - Fixed fatal PHP error when an invalid order ID was passed (null check now happens before property access).
  - Fixed missing `require_once` for `class.tc-pos-ticket-template.php` — Tickera admin template selector was never loaded.
  - Replaced custom timestamp-based order key validation with `$order->get_order_key()`.
  - Added nonce verification (`woocommerce_meta_nonce`) to the WooCommerce product save handler.
  - Removed debug `print_r` / `print` statements from production code.
  - Initialized `$response = []` to prevent undefined variable warning on empty orders.
  - Fixed text domain `'woocommerce'` → `'tcotf'`.

- **Version 1.0.0:**
  - Initial release.
  - Key features:
    - Added functionality to query ticket instances based on order information.
