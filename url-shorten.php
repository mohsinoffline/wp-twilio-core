<?php

/**
 * Shortens URLs in the message if the option is enabled
 * @param  string $message Message to be formatted
 * @param  array $args Send message arguments
 * @return string Message with/without short URLs
 */
function twl_filter_message_urls( $message, $args ) {
	global $bitly_token, $group_guid;
	if( $args['url_shorten_bitly'] && $args['url_shorten_bitly_token'] ) {
		$bitly_token = apply_filters( 'twl_bitly_token', $args['url_shorten_bitly_token'] );
		$result = wp_remote_get( 'https://api-ssl.bitly.com/v4/groups', array(
			'headers' => array( 'Authorization' => "Bearer $bitly_token", 'Content-Type' => 'application/json' ),
		) );
		
		if ( !is_wp_error( $result ) ) {
			$result = json_decode( $result['body'] );
			if( isset( $result->groups ) && is_array( $result->groups ) ) {
				$group = reset( $result->groups );
				if( isset( $group->guid ) ) {
					$group_guid = $group->guid;
					//echo $group_guid;
					$regex = '"\b(https?://\S+)"';
					$message = preg_replace_callback( $regex, function( $url ) {
						return twl_url_shorten_bitly( $url[0] );
					}, $message );
				}
			}
		}
		
	}

	if( $args['url_shorten'] && $args['url_shorten_api_key'] ) {
		$regex = '"\b(https?://\S+)"';
		$message = preg_replace_callback( $regex, function( $url ) {
			return twl_url_shorten_google( $url[0] );
		}, $message );
	}

	// remove http:// or https:// since many providers block it
	// $message = preg_replace( '"\b(https?://)"', '', $message );
	return $message;
}
add_filter( 'twl_sms_message', 'twl_filter_message_urls', 9999, 2 );

/**
 * Process URL via Google URL Shortener API
 * @param  string $url URL to be shortened
 * @return string Shortened URL in http://goo.gl/xxx format
 */
function twl_url_shorten_google( $url ) {
	$options = twl_get_options();

	$result = wp_remote_post( add_query_arg( 'key', apply_filters( 'twl_google_api_key', $options['url_shorten_api_key'] ), 'https://www.googleapis.com/urlshortener/v1/url' ), array(
		'body' => json_encode( array( 'longUrl' => esc_url_raw( $url ) ) ),
		'headers' => array( 'Content-Type' => 'application/json' ),
	) );

	// Return the URL if the request got an error.
	if ( is_wp_error( $result ) )
		return $url;

	$result = json_decode( $result['body'] );
	$shortlink = $result->id;
	if ( $shortlink )
		return $shortlink;

	return $url;
}

/**
 * Process URL via Bit.ly URL Shortener API
 * @param  string $url URL to be shortened
 * @return string Shortened URL in http://bit.ly/xxx format
 */
function twl_url_shorten_bitly( $url ) {
	global $bitly_token, $group_guid;

	$options = twl_get_options();

	$result = wp_remote_post( 'https://api-ssl.bitly.com/v4/shorten', array(
		'body' => json_encode( array( 'group_guid' => $group_guid, 'long_url' => esc_url_raw( $url ) ) ),
		'headers' => array( 'Authorization' => "Bearer $bitly_token", 'Content-Type' => 'application/json' ),
	) );

	// Return the URL if the request got an error.
	if ( is_wp_error( $result ) )
		return $url;

	$result = json_decode( $result['body'] );
	if ( isset( $result->link ) )
		return $result->link;

	return $url;
}