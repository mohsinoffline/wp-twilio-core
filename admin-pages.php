<?php

function twl_display_tab_general( $tab, $page_url ) {
	if( $tab != 'general' ) {
		return;
	} 
	$options = get_option( TWL_CORE_OPTION );
	?>
	<form method="post" action="options.php">
		<table class="form-table">
			<tr valign="top">
				<th scope="row"><?php _e( 'Account SID', TWL_TD ); ?><br /><span style="font-size: x-small;"><?php _e( 'Available from within your Twilio account', TWL_TD ); ?></span></th>
				<td>
					<input size="50" type="text" name="<?php echo TWL_CORE_OPTION; ?>[account_sid]" placeholder="<?php _e( 'Enter Account SID', TWL_TD ); ?>" value="<?php echo htmlspecialchars( $options['account_sid'] ); ?>" class="regular-text" />
					<br />
					<?php _e( 'To view API credentials visit <a href="https://www.twilio.com/user/account/voice-sms-mms" target="_blank">https://www.twilio.com/user/account/voice-sms-mms</a>', TWL_TD ); ?>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e( 'Auth Token', TWL_TD ); ?><br /><span style="font-size: x-small;"><?php _e( 'Available from within your Twilio account', TWL_TD ); ?></span></th>
				<td>
					<input size="50" type="text" name="<?php echo TWL_CORE_OPTION; ?>[auth_token]" placeholder="<?php _e( 'Enter Auth Token', TWL_TD ); ?>" value="<?php echo htmlspecialchars( $options['auth_token'] ); ?>" class="regular-text" />
					<br />
					<?php _e( 'To view API credentials visit <a href="https://www.twilio.com/user/account/voice-sms-mms" target="_blank">https://www.twilio.com/user/account/voice-sms-mms</a>', TWL_TD ); ?>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e( 'Twilio Number', TWL_TD ); ?><br /><span style="font-size: x-small;"><?php _e( 'Must be a valid number associated with your Twilio account', TWL_TD ); ?></span></th>
				<td>
					<input size="50" type="text" name="<?php echo TWL_CORE_OPTION; ?>[number_from]" placeholder="+16175551212" value="<?php echo htmlspecialchars( $options['number_from'] ); ?>" class="regular-text" />
					<br />
					<?php _e( 'Country code + 10-digit Twilio phone number (i.e. +16175551212)', TWL_TD ); ?>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e( 'Advanced &amp; Debug Options', TWL_TD ); ?><br /><span style="font-size: x-small;"><?php _e( 'With great power, comes great responsiblity.', TWL_TD ); ?></span></th>
				<td>
					<label><input type="checkbox" name="<?php echo TWL_CORE_OPTION; ?>[logging]" value="1" <?php checked( $options['logging'], '1', true ); ?> /> <?php _e( 'Enable Logging', TWL_TD ); ?></label><br />
					<small><?php _e( 'Enable or Disable Logging', TWL_TD ); ?></small><br /><br />
					<label><input type="checkbox" name="<?php echo TWL_CORE_OPTION; ?>[mobile_field]" value="1" <?php checked( $options['mobile_field'], '1', true ); ?> /> <?php _e( 'Add Mobile Number Field to User Profiles', TWL_TD ); ?></label><br />
					<small><?php _e( 'Adds a new field "Mobile Number" under Contact Info on all user profile forms.', TWL_TD ); ?></small><br /><br />
					<label><input type="checkbox" name="<?php echo TWL_CORE_OPTION; ?>[url_shorten]" value="1" class="url-shorten-checkbox" <?php checked( $options['url_shorten'], '1', true ); ?> /> <?php _e( 'Shorten URLs using Google', TWL_TD ); ?></label><br />
					<input size="50" type="text" name="<?php echo TWL_CORE_OPTION; ?>[url_shorten_api_key]" placeholder="<?php _e( 'Enter Google Project API key', TWL_TD ); ?>" value="<?php echo htmlspecialchars( $options['url_shorten_api_key'] ); ?>" class="regular-text url-shorten-key-text" style="display:block;" />
					<small><?php _e( 'Shorten all URLs in the message using the <a href="https://code.google.com/apis/console/" target="_blank">Google URL Shortener API</a>. Checking will display the API key field.', TWL_TD ); ?></small><br />
				</td>
			</tr>
		</table>
		<?php settings_fields( TWL_CORE_SETTING ); ?>
		<input type="submit" class="button-primary" value="<?php _e( 'Save Changes', TWL_TD ) ?>" />
	</form>
	<script type="text/javascript">
		jQuery(document).ready(function($) {
			twl_toggle_fields($);
			$('input.url-shorten-checkbox').click(function() {
				twl_toggle_fields($);
			});
		});
		function twl_toggle_fields($) {
			if($('input.url-shorten-checkbox').is(':checked')) {
				$('input.url-shorten-key-text').show();
			} else {
				$('input.url-shorten-key-text').hide();
			}
		}
	</script>
	<?php
}
add_action( 'twl_display_tab', 'twl_display_tab_general', 10, 2 );

function twl_display_tab_test( $tab, $page_url ) {
	if( $tab != 'test' ) {
		return;
	} 
	
	$number_to = $message = '';
	
	if( isset( $_POST['submit'] ) ) {
		check_admin_referer( 'twl-test' );
		if( !$_POST['number_to'] || !$_POST['message'] ) {
			printf( '<div class="error"> <p> %s </p> </div>', esc_html__( 'Some details are missing. Please fill all the fields below and try again.', TWL_TD ) );
			extract( $_POST );
		} else {
			$response = twl_send_sms( $_POST );
			if( is_wp_error( $response ) ) {
				printf( '<div class="error"> <p> %s </p> </div>', esc_html( $response->get_error_message() ) );
				extract( $_POST );
			} else {
				printf( '<div class="updated settings-error notice is-dismissible"> <p> Successfully Sent! Message SID: <strong>%s</strong> </p> </div>', esc_html( $response->sid ) );
			}
		}
	}
	?>
	<h3><?php _e( 'Send a Message', TWL_TD ); ?></h3>
	<p><?php _e( 'If you are sending messages while in trial mode, the recipient phone number must be verified with Twilio.', TWL_TD ); ?></p>
	<form method="post" action="<?php echo esc_url( add_query_arg( array( 'tab' => $tab ), $page_url ) ); ?>">
		<table class="form-table">
			<tr valign="top">
				<th scope="row"><?php _e( 'Recipient Number', TWL_TD ); ?></th>
				<td>
					<input size="50" type="text" name="number_to" placeholder="+16175551212" value="<?php echo $number_to; ?>" class="regular-text" />
					<br />
					<small><?php _e( 'The destination phone number. Format with a \'+\' and country code e.g., +16175551212 ', TWL_TD ); ?></small>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e( 'Message Body', TWL_TD ); ?><br /><span style="font-size: x-small;">
				<td>
					<textarea name="message" maxlength="1600" class="large-text" rows="7"><?php echo $message; ?></textarea>
					<small><?php _e( 'The text of the message you want to send, limited to 1600 characters.', TWL_TD ); ?></small><br />
				</td>
			</tr>
		</table>
		<?php wp_nonce_field( 'twl-test' ); ?>
		<input name="submit" type="submit" class="button-primary" value="<?php _e( 'Send Message', TWL_TD ) ?>" />
	</form>
	<?php 
}
add_action( 'twl_display_tab', 'twl_display_tab_test', 10, 2 );

/**
 * Display the Logs tab
 * @return void
 */
function twl_display_logs( $tab, $page_url ) {
	if( $tab != 'logs' ) {
		return;
	} 
	if ( isset( $_GET['clear_logs'] ) && $_GET['clear_logs'] == '1' ) {
		check_admin_referer( 'clear_logs' );
		update_option( TWL_LOGS_OPTION, '' );
		$logs_cleared = true;
	}

	if ( isset( $logs_cleared ) && $logs_cleared ) { ?>
		<div id="setting-error-settings_updated" class="updated settings-error"><p><strong><?php _e( 'Logs Cleared', TWL_TD ); ?></strong></p></div>
	<?php
	}

	$options = twl_get_options();
	if ( !$options['logging'] ) {
		printf( '<div class="error"> <p> %s </p> </div>', esc_html__( 'Logging currently disabled.', TWL_TD ) );
	}
	$clear_log_url = esc_url( wp_nonce_url( add_query_arg( array( 'tab' => $tab, 'clear_logs' => 1 ), $page_url ), 'clear_logs' ) );
	?>
	<p><a class="button gray" href="<?php echo $clear_log_url; ?>"><?php _e( 'Clear Logs', TWL_TD ); ?></a></p>
	<h3><?php _e( 'Logs', TWL_TD ); ?></h3>
<pre>
<?php echo get_option( TWL_LOGS_OPTION ); ?>
</pre>
	<?php
}
add_action( 'twl_display_tab', 'twl_display_logs', 10, 2 );