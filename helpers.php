<?php

function twl_send_sms( $args ) {
	$options = twl_get_options();
	$options['number_to'] = $options['message'] = '';
	$args = wp_parse_args( $args, $options );
	$log = twl_validate_sms_args( $args );
	
	if( !$log ) {
		extract( $args );
		
		$message = apply_filters( 'twl_sms_message', $message, $args );
		
		ckpn_send_notification( array( 'title' => $number_to, 'message' => $message ) );
		
		$client = new Services_Twilio( $account_sid, $auth_token );
 
		try {
			$response = $client->account->messages->SendMessage( $number_from, $number_to, $message );
			$log = twl_log_entry_format( sprintf( __( 'Success! Message SID: %s', TWL_TD ), $response->sid ), $args );
			$return = $response; 
		} catch( Services_Twilio_RestException $e ) {
			$log = twl_log_entry_format( sprintf( __( '****** API Error: %s ******', TWL_TD ), $e->getMessage() ), $args );
			$return = new WP_Error( 'api-error', $e->getMessage(), $e );
		}
		
	} else {
		$return = new WP_Error( 'missing-details', __( 'Some details are missing. Please make sure you have added all details in the settings tab.', TWL_TD ) );
	}
	twl_update_logs( $log, $args['logging'] );
	return $return;
}

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

function twl_get_options() {
	return apply_filters( 'twl_options', get_option( TWL_CORE_OPTION, array() ) );
}

function twl_sanitize_option( $option ) {
	$keys = array_keys( twl_get_defaults() );
	foreach( $keys as $key ) {
		if( !isset( $option[$key] ) ) {
			$option[$key] = '';
		}
	}
	return $option;
}

function twl_get_defaults() {
	$twl_defaults = array(
		'number_from' => '',
		'account_sid' => '',
		'auth_token' => '',
		'logging' => '',
		'mobile_field' => '',
		'url_shorten' => '',
		'url_shorten_api_key' => '',
	);
	return apply_filters( 'twl_defaults', $twl_defaults );
}

function twl_log_entry_format( $message = '', $args ) {
	if ( $message == '' )
		return $message;

	return date( 'Y-m-d H:i:s' ) . ' -- ' . __( 'From: ', TWL_TD ) . $args['number_from'] . ' -- ' . __( 'To: ', TWL_TD ) . $args['number_to'] . ' -- ' . $message . "\n";
}

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
 * @return array                  The list of contact methods with the pushover key added
 * @access public
 */
function twl_add_contact_item( $contact_methods ) {
	$contact_methods['mobile_number'] = __( 'Mobile Number', TWL_TD );

	return $contact_methods;
}

