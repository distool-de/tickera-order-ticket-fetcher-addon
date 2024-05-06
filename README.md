# Tickera Order Ticket Fetcher Addon

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
- WordPress version: 6.5.2 or higher
- PHP version: 8.2.18 or higher
- Required Plugins: tickera-event-ticketing-system

## Author
- Name: Distool.de
- Website: [Distool.de](https://distool.de/)

## Version
1.0.0

## Text Domain
tcotf

## Update URI
[https://distool.de/tickera](https://distool.de/tickera)

## Disclaimer
The authors and distributors of this plugin do not take any responsibility for any consequences resulting from the use of this software. Use at your own risk.



## Changelog
- **Version 1.0.0:**
  - Initial release.
  - Key features:
    - Added functionality to query ticket instances based on order information.
