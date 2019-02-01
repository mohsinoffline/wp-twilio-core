=== WP Twilio Core ===
Contributors: mohsinoffline
Donate link: https://wpsms.io
Tags: twilio, sms, text message
Requires at least: 4.2
Tested up to: 5.0.3
Stable tag: 1.2.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

WP Twilio core is a simple plugin to add SMS capability to your website using the Twilio API. 

== Description ==

If you've ever wanted to add text messaging functionality to your website or app, [Twilio](https://www.twilio.com/) is one of the best solutions on the market. They're reasonably priced and have an excellent API. 

The plugin primarily allows a WordPress developer to extend its settings and functionality and integrate it into any type of site. For example, it can easily be extended to send a text message on virtually any WordPress action.

The plugin also includes functionality to directly send a text message to any permissible number from the plugin settings page. You can use it to SMS any of your users or just for testing purposes

Here's a list of what the plugin provides out of the box:


* Custom function to easily send SMS messages to any number (including international ones)
* Functionality to directly send a text message to any permissible number from the plugin settings page
* Hooks to add additional tabs on the plugin settings page to allow managing all SMS related settings from the same page
* Basic logging capability to keep track of up to 100 entries
* Mobile Phone User Field added to each profile (optional)
* Shorten URLs using Bit.ly or Google URL Shortener API (optional)

Extend, Contribute, Integrate
-------

Visit the [plugin page](https://wpsms.io/) for full integration details. Contributors are welcome to send pull requests via [GitHub repository](https://github.com/mohsinoffline/wp-twilio-core).

For custom integration with your WordPress website, please [contact us here](https://wpsms.io/).

Disclaimer: This plugin is not affiliated with or supported by Twilio, Inc. All logos and trademarks are the property of their respective owners. 

== Installation ==

1. Extract and upload the folder `wp-twilio-core` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Go to `Settings -> Twilio` in WordPress and enter API details and Twilio number.

== Frequently Asked Questions ==

Please make sure you read through the [SMS FAQs](https://www.twilio.com/help/faq/sms) on the Twilio website.

= Is this service chargeable? =

Yes, you will need to signup on [Twilio](https://www.twilio.com/), and obtain a number with SMS capability. However, they have trial accounts available which should have enough credit for you to try out the plugin!

== Screenshots ==

1. Send text messages from your WordPress website to any number using Twilio!
2. Settings page in the WordPress admin back end.

== Changelog ==

= 1.2.1 =
* Added contact and add-on menus

= 1.2.0 =
* Added URL shortening option via Bit.ly URL Shortener API
* Update Twilio PHP helper library to 5.24.1

= 1.1.0 =
* Update Twilio PHP helper library to 5.7.0

= 1.0.3 =
* Fixed backslashes in test message

= 1.0.2 =
* Made "Mobile Number" field available on front end

= 1.0.1 =
* Added URL shortening option via Google URL Shortener API (goo.gl)

= 1.0.0 =
* Initial release version