                                       _
		______   ____    _________| |   _______	   _______      _______	   ______
		|   _ \_/_   | 	|           |  |   __  \   |   ____|  /   ____|   /  __  \
		|  | |  | |  | 	|           |  |  |  |  |  |       |  |       |  |  |  |
		|  | |  | |  | 	| /\	/ \ |  |  |__|  |  |  |____   |  |____   |  |__|  |
		|  | |  | |  | 	|/  \  /   \|  |   ____/   |   ____|  |____   |  |   __   |
		|  | |  | |  | 	|\   \/	   /|  |  |        |  |            |  |  |  |  |  |
		|  | |  | |  | 	| \	  / |  |  |        |  |____    ____|  |  |  |  |  |
		|__| |__| |__|	|  \____/   |  |__|        |_______|  |_______/  |__|  |__|
				|	    |
				|           |
				|___________|
						
# MPESA For WooCommerce
WordPress Plugin that extends WordPress and WooCommerce functionality to integrate MPESA for making payments, remittances, checking account balance transaction status and reversals. It also adds Kenyan Counties to the WooCommerce states list.
![alt text](https://user-images.githubusercontent.com/14233942/39403773-68d33f0e-4b8c-11e8-9cf4-d664a4486332.png)

## Installation
Getting started with MPESA for WooCommerce is very easy. All configuration is done in the WooCommerce settings in the WordPress admin dashboard.

### Requirements
Your site/app MUST be running over https for the MPESA Instant Payment Notification (IPN) to work.

### Auto-installation
* In your WordPress admin, navigate to Plugins and search for MPESA for WooCommerce.
* Click the install button and the plugin will be installed. Once installed, activate the plugin and configure it at http://yoursite.com/wp-admin/admin.php?page=wc-settings&tab=checkout&section=mpesa

### Manual Installation 
* First, you need to download the latest release of the plugin from [the WordPress plugin repository](https://plugins.wordpress.org/osen-wc-mpesa) or [the github repo](https://github.com/osenco/osen-wc-mpesa/archive/v0.7.8.zip).
* Using an FTP program, or your hosting control panel, upload the plugin folder (wc-mpesa) to your WordPress installation’s wp-content/plugins/ directory.
* Activate the plugin from the Plugins menu within the WordPress admin

That is all. You are now ready to receive and send money using MPESA on your WordPress and WooCommerce powered site.

## Contributing
* Fork the repo, do your magic and make a pull request.

### Integration Cost
* Our team of developers are on hand to provide assistance for when you wish to move from Sandbox(test) to Live(production) environment. This assistance is charged a fiat fee of `KSH 4000/$40`

## Acknowledgements
* MPESA and the MPESA Logo are registered trademarks of Safaricom Ltd
* WordPress and the WordPress logo are registered trademarks of Automattic Inc.
* WooCommerce and the WooCommerce logo are registered trademarks of Automattic Inc.
