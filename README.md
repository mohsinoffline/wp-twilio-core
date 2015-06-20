# WP Twilio Core

WP Twilio core is a simple plugin to add SMS capability to your website using the Twilio API. 

If you've ever wanted to add text messaging functionality to your website or app, [Twilio](https://www.twilio.com/) is one of the best solutions on the market. They're reasonably priced and have an excellent API. 


How does it work?
-

The plugin primarily allows a WordPress developer to extend its settings and functionality and integrate it into any type of site. For example, it can easily be extended to send a text message on virtually any WordPress action.

The plugin also includes functionality to directly send a text message to any permissible number from the plugin settings page. You can use it to SMS any of your users or just for testing purposes

 Here's a list of what the plugin provides out of the box:


- Custom function to easily send SMS messages to any number (including international ones)
- Functionality to directly send a text message to any permissible number from the plugin settings page
- Hooks to add additional tabs on the plugin settings page to allow managing all SMS related settings from the same page
- Basic logging capability to keep track of up to 100 entries
- Custom filter to modify the response WordPress gives when a user texts your Twilio number (TODO)
- Mobile Phone User Field added to each profile (TODO)

<h3>twl_send_sms( $args )</h3>
<p>Sends a standard text message from your Twilio Number when arguments are passed in an array format. Description of each array key is given below.</p>
Array Key | Type | Description
------------- | ------------- | ----
number_to | string | The mobile number that will be texted. Must be formatted as country code + 10-digit number (i.e. +13362522164).
message | string | The message that will be sent to the recipient.
number_from *(optional)* | string | Override the Twilio Number from settings. Must be associated with Account SID and Auth Token
account_sid *(optional)* | string | Override the Twilio Account SID from settings. Must be associated with Twilio number and Auth Token.
auth_token *(optional)* | string | Override the Auth Token from settings. Must be associated with Twilio number and Account SID.
logging *(optional)* | integer (1 or 0) | Override the logging option set from the settings page. Requires the digit '1' to enable.

Returns an array with response from Twilio's servers on success of a *WP_Error* object on failure.
<h5>Example</h5>

```php
$args = array( 
	'number_to' => '+13362522164',
	'message' = 'Hello Programmer!',
); 
twl_send_sms( $args );	
```

<h3>Extending the Settings page</h3>
<p>It is very easy to add your own tab to the plugin settings page. Please see the example below:</p>

```php
// Registering a new tab name
function add_new_settings_tab( $tabs ) {
	$tabs['my_shop'] = 'My Shop';
	return $tabs;
}
add_filter( 'twl_settings_tabs', 'add_new_settings_tab' );

// Adding form to that new tab
function add_my_shop_tab_content( $tab, $page_url ) {
	if( $tab != 'my_shop' ) {
		return;
	} 
	// Add my settings form here!
}
```
	
<h5>Copyright</h5>
Plugin created by <a href="http://themebound.com/shop/wp-twilio-core/">Themebound</a>. 

Disclaimer: This plugin is not directly supported by Twilio,Inc. Please do not contact them for support as they will not be able to help you with it. All logos and trademarks are the property of their respective owners.
