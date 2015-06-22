<?php

function twl_filter_message_urls( $message, $args ) {
	$regex = '"\b(https?://\S+)"';
	$message = preg_replace_callback( $regex, function( $url ) { 
		return twl_url_shorten( $url[0] );
	}, $message );
	
	// remove http:// or https:// since many providers block it
	$message = preg_replace( '"\b(https?://)"', '', $message );
	
	return $message;
}
add_filter( 'twl_sms_message', 'twl_filter_message_urls', 9999, 2 );

function twl_url_shorten( $url ) {
	$result = wp_remote_post( add_query_arg( 'key', apply_filters( 'googl_api_key', 'AIzaSyBEPh-As7b5US77SgxbZUfMXAwWYjfpWYg' ), 'https://www.googleapis.com/urlshortener/v1/url' ), array(
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