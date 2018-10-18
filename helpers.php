<?php

/**
 * Sends the actual SMS
 * @param  array $args Array of arguments described here: https://github.com/mohsinoffline/wp-twilio-core#twl_send_sms-args-
 * @return array Response object from Twilio PHP library on success or WP_Error object on failure
 */
function twl_send_sms( $args ) {
	$options = twl_get_options();
	$options['number_to'] = $options['message'] = '';
	$options = wp_parse_args( $options, twl_get_defaults() );
	$args = wp_parse_args( $args, $options );
	$log = twl_validate_sms_args( $args );

	if( !$log ) {
		extract( $args );

		$message = apply_filters( 'twl_sms_message', $message, $args );

		$client = new Twilio\Rest\Client( $account_sid, $auth_token );

		try {
			$response = $client->messages->create( $number_to, array( 'from' => $number_from, 'body' => $message ) );
			$log = twl_log_entry_format( sprintf( __( 'Success! Message SID: %s', TWL_TD ), $response->sid ), $args );
			$return = $response;
		} catch( \Exception $e ) {
			$log = twl_log_entry_format( sprintf( __( '****** API Error: %s ******', TWL_TD ), $e->getMessage() ), $args );
			$return = new WP_Error( 'api-error', $e->getMessage(), $e );
		}

	} else {
		$return = new WP_Error( 'missing-details', __( 'Some details are missing. Please make sure you have added all details in the settings tab.', TWL_TD ) );
	}
	twl_update_logs( $log, $args['logging'] );
	return $return;
}

/**
 * Update logs primarily from twl_send_sms() function
 * @param  string $log String of new-line separated log entries to be added
 * @param  int/boolean $enabled Whether to update logs or skip
 * @return void
 */
function twl_update_logs( $log, $enabled = 1 ) {
	$options = twl_get_options();
	if ( $enabled == 1 ) {
		$current_logs = get_option( TWL_LOGS_OPTION );
		$new_logs = $log . $current_logs;

		$logs_array = explode( "\n", $new_logs );
		if ( count( $logs_array ) > 100 ) {
			$logs_array = array_slice( $logs_array, 0, 100 );
			$new_logs = implode( "\n", $logs_array );
		}

		update_option( TWL_LOGS_OPTION, $new_logs );
	}
}

/**
 * Get saved options
 * @return array of saved options
 */
function twl_get_options() {
	return apply_filters( 'twl_options', get_option( TWL_CORE_OPTION, array() ) );
}

/**
 * Sanitizes option array before it gets saved
 * @param $array array of options to be saved
 * @return array of sanitized options
 */
function twl_sanitize_option( $option ) {
	$keys = array_keys( twl_get_defaults() );
	foreach( $keys as $key ) {
		if( !isset( $option[$key] ) ) {
			$option[$key] = '';
		}
	}
	return $option;
}

/**
 * Get default option array
 * @return array of default options
 */
function twl_get_defaults() {
	$twl_defaults = array(
		'number_from' => '',
		'account_sid' => '',
		'auth_token' => '',
		'logging' => '',
		'mobile_field' => '',
		'url_shorten' => '',
		'url_shorten_api_key' => '',
		'url_shorten_bitly' => '',
		'url_shorten_bitly_token' => '',
	);
	return apply_filters( 'twl_defaults', $twl_defaults );
}

/**
 * Format log message with more information
 * @param  string $message Message to be formatted
 * @param  array $args Send message arguments
 * @return string Formatted message entry
 */
function twl_log_entry_format( $message = '', $args ) {
	if ( $message == '' )
		return $message;

	return date( 'Y-m-d H:i:s' ) . ' -- ' . __( 'From: ', TWL_TD ) . $args['number_from'] . ' -- ' . __( 'To: ', TWL_TD ) . $args['number_to'] . ' -- ' . $message . "\n";
}

/**
 * Validates args before sending message
 * @param  array $args Send message arguments
 * @return string Log entries for invalid arguments
 */
function twl_validate_sms_args( $args ) {
	// Check that we have the required elements
	$log = '';

	if( !$args['number_from'] ) {
		$log .= twl_log_entry_format( __( '****** Missing Twilio Number ******', TWL_TD ), $args );
	}

	if( !$args['number_to'] ) {
		$log .= twl_log_entry_format( __( '****** Missing Recipient Number ******', TWL_TD ), $args );
	}

	if( !$args['message'] ) {
		$log .= twl_log_entry_format( __( '****** Missing Message ******', TWL_TD ), $args );
	}

	if( !$args['account_sid'] ) {
		$log .= twl_log_entry_format( __( '****** Missing Account SID ******', TWL_TD ), $args );
	}

	if( !$args['auth_token'] ) {
		$log .= twl_log_entry_format( __( '****** Missing Auth Token ******', TWL_TD ), $args );
	}

	return $log;
}

/**
 * Saves the User Profile Settings
 * @param  int $user_id The User ID being saved
 * @return void         Saves to Usermeta
 */
function twl_save_profile_settings( $user_id ) {
	if ( !current_user_can( 'edit_user', $user_id ) )
		return false;

	$user_key = sanitize_text_field( $_POST['mobile_number'] );
	update_user_meta( $user_id, 'mobile_number', $_POST['mobile_number'] );
}

/**
 * Add the Mobile Number field to the Profile page
 * @param  array $contact_methods List of contact methods
 * @return array The list of contact methods with the mobile field added
 */
function twl_add_contact_item( $contact_methods ) {
	$contact_methods['mobile_number'] = __( 'Mobile Number', TWL_TD );

	return $contact_methods;
}