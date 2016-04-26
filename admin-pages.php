<?php
/**
 * Display the General settings tab
 * @return void
 */
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
					<small><?php _e( 'To view API credentials visit <a href="https://www.twilio.com/user/account/voice-sms-mms" target="_blank">https://www.twilio.com/user/account/voice-sms-mms</a>', TWL_TD ); ?></small>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e( 'Auth Token', TWL_TD ); ?><br /><span style="font-size: x-small;"><?php _e( 'Available from within your Twilio account', TWL_TD ); ?></span></th>
				<td>
					<input size="50" type="text" name="<?php echo TWL_CORE_OPTION; ?>[auth_token]" placeholder="<?php _e( 'Enter Auth Token', TWL_TD ); ?>" value="<?php echo htmlspecialchars( $options['auth_token'] ); ?>" class="regular-text" />
					<br />
					<small><?php _e( 'To view API credentials visit <a href="https://www.twilio.com/user/account/voice-sms-mms" target="_blank">https://www.twilio.com/user/account/voice-sms-mms</a>', TWL_TD ); ?></small>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e( 'Twilio Number', TWL_TD ); ?><br /><span style="font-size: x-small;"><?php _e( 'Must be a valid number associated with your Twilio account', TWL_TD ); ?></span></th>
				<td>
					<input size="50" type="text" name="<?php echo TWL_CORE_OPTION; ?>[number_from]" placeholder="+16175551212" value="<?php echo htmlspecialchars( $options['number_from'] ); ?>" class="regular-text" />
					<br />
					<small><?php _e( 'Country code + 10-digit Twilio phone number (i.e. +16175551212)', TWL_TD ); ?></small>
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

/**
 * Display the Test SMS tab
 * @return void
 */
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
			$response = twl_send_sms( stripslashes_deep( $_POST ) );
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

/**
 * Display the Verify Settings tab
 * @return void
 */
// Registering a new tab name
function add_verify_settings_tab( $tabs ) {
    $tabs['verify_settings'] = 'Twilio Verify Settings';
    return $tabs;
}
add_filter( 'twl_settings_tabs', 'add_verify_settings_tab' );
 
// Adding form to that new tab
function add_my_verifysettings_tab_content( $tab, $page_url ) {
	global $wpdb;
	$options = get_option( TWL_CORE_OPTION );
	if( $tab != 'verify_settings' ) {
		return;
	} 
	$verify_url = esc_url( wp_nonce_url( add_query_arg( array( 'tab' => $tab, 'verify_settings' => 1 ), $page_url ), 'verify_settings' ) );
	$optionsnew = get_option( 'twl_verify' );
	$arr = array();
    
	if($_GET['verify_tbl'] == 'wp_usermeta' ||  $optionsnew['verifytbl'] == 'wp_usermeta'){
		$settings_qry = "SELECT DISTINCT meta_key FROM wp_usermeta;";
		$results = $wpdb->get_results($settings_qry, ARRAY_A);
	    	foreach( $results as $key => $row ){
	    		$arr[$row['meta_key']] = $row['meta_key'];
	    		
	    	}
	}else{
		$tblname = $optionsnew['verifytbl'];
				foreach($wpdb->get_col("DESC " . $tblname, 0 ) as $column_name){
			$arr[$column_name] = $column_name;
			
		}
	}  
 
    	$tbls_qry = "SHOW TABLES LIKE '%'";
    	$tblresults = $wpdb->get_results($tbls_qry, ARRAY_A);
  
    ?>
    	<form method="post" action="options.php">
    		
		<table class="form-table">
			<tr valign="top">
				<th scope="row"><?php _e( 'Query', TWL_TD ); ?><br /><span style="font-size: x-small;">
				<td>
					<textarea name="twl_verify[querystr]" maxlength="1600" class="large-text" placeholder ="SELECT * FROM wp_usermeta WHERE meta_key IN ('public_phone', 'phone', 'house_number', 'street_direction', 'street', 'street_type') ORDER BY user_id, meta_key;" rows="7"><?php echo $optionsnew['querystr']; ?></textarea>
					<small><?php _e( 'The text of the message you want to send, limited to 1600 characters.', TWL_TD ); ?></small><br />
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e( 'Tables', TWL_TD ); ?></th>
				<td>
					<label>Table choices</label><br />
					<?php 
						foreach($tblresults as $ind => $val){
							foreach($val as $tblname){
								echo $tblname.'<br />';
							}
						}
					?>
					<input size="50" type="text" name="twl_verify[verifytbl]" placeholder="wp_usermeta" value="<?php echo $optionsnew['verifytbl']; ?>" class="regular-text" />
					<br />
					<small><?php _e( 'The table you want to pull numbers from.', TWL_TD ); ?></small>
				</td>
			</tr>
			<tr><td><p><a class="button gray" href="<?php echo $verify_url; ?>"><?php _e( 'Refresh', TWL_TD ); ?></a></p></td></tr>
			
			<tr valign="top">
				<th scope="row"><?php _e( 'Twilio Phone fields', TWL_TD ); ?><br /><span style="font-size: x-small;">
				<td>
					<select multiple="multiple" name="twl_verify[twiliofields][]">
					<option value="">-----------------</option>
					<?php 
					if(isset($arr)){
						foreach($arr as $key => $value){
							echo '<option value="'.$key.'">'.$value.'</option>'; //close your tags!!
						}
					}
					?>
					</select>
					<small><?php _e( 'Fields you want to return the caller-name of.', TWL_TD ); ?></small><br />
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e( 'Metadata fields', TWL_TD ); ?><br /><span style="font-size: x-small;">
				<td>
					<select multiple="multiple" name="twl_verify[metadata][]">
					<option value="">-----------------</option>
					<?php 
					if(isset($arr)){
						foreach($arr as $key => $value){
							echo '<option value="'.$key.'">'.$value.'</option>'; 
						}
					}
					?>
					</select>
					<small><?php _e( 'Additional fields you want listed in the table.', TWL_TD ); ?></small><br />
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e( 'Twilio Phone fields', TWL_TD ); ?><br /><span style="font-size: x-small;">
				<td>
					<select multiple="multiple" name="twl_verify[saveflds][]">
					<option value="">-----------------</option>
					<?php 
					if(isset($arr)){
						foreach($arr as $key => $value){
							echo '<option value="'.$key.'">'.$value.'</option>'; //close your tags!!
						}
					}
					?>
					</select>
					<small><?php _e( 'Fields you want to return the caller-name of.', TWL_TD ); ?></small><br />
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e( 'Twilio Lookup Field (Primary Key)', TWL_TD ); ?><br /><span style="font-size: x-small;">
				<td>
					<select name="twl_verify[pk]">
					<option value="">-----------------</option>
					<?php 
					if(isset($arr)){
						foreach($arr as $key => $value){
							echo '<option value="'.$key.'">'.$value.'</option>'; //close your tags!!
						}
					}
					?>
					</select>
					<small><?php _e( 'Fields you want to return the caller-name of.', TWL_TD ); ?></small><br />
				</td>
			</tr>
		</table>
		<?php settings_fields( 'twl-verify' ); ?>
		<input name="submit" type="submit" class="button-primary" value="<?php _e( 'Save Settings', TWL_TD ) ?>" />
	</form>
<?php
}
add_action( 'twl_display_tab', 'add_my_verifysettings_tab_content', 10, 2 );


function add_new_verify_tab( $tabs ) {
    $tabs['verify'] = 'Verify Numbers';
    return $tabs;
}


add_filter( 'twl_settings_tabs', 'add_new_verify_tab' );

/**
 * Add additional settings
 * @return void
 */

function twl_verify_add_settings(){
	register_setting( 'twl-verify' , 'twl_verify');	
}
add_action('twl_register_additional_settings', 'twl_verify_add_settings', 10, 2);

/**
 * Display the Verify tab
 * @return void
 */ 
// Adding form to that new tab
function add_my_verify_content( $tab, $page_url ) {
	global $wpdb;
	if( $tab != 'verify' ) {
		return;
	} 
	$options = get_option( TWL_CORE_OPTION );
	$optionsnew = get_option( 'twl_verify' );
	if($optionsnew['verifytbl']=='wp_usermeta'){
		if($optionsnew['querystr']==''){
			$qry = "SELECT * FROM wp_usermeta WHERE meta_key IN ('public_phone', 'phone', 'house_number', 'street_direction', 'street', 'street_type') ORDER BY user_id, meta_key;";
		}else{
			$qry = $optionsnew['querystr'];
		}
	}else{
		if(isset($optionsnew['querystr'])){
			$qry = $optionsnew['querystr'];
		}else{
			$qry = "SELECT ".implode(",", array_merge($optionsnew['metadata'],$optionsnew['twiliofields']))." FROM ".$optionsnew['verifytbl'];
		}
	}
	$results = $wpdb->get_results($qry, ARRAY_A);
	if(is_array($optionsnew['metadata']) && is_array($optionsnew['twiliofields'])){
		$cols = array_merge($optionsnew['metadata'],$optionsnew['twiliofields']);
	}else{
		$cols = array();
	}
	if ( isset( $_GET['run_verify'] ) && $_GET['run_verify'] == '1' ) {
		$id ='';
		$usermetaarray = array();
		echo '<table>';
		
		$lookupstr = '';
		require_once('../wp-content/plugins/wp-twilio-cnam/twilio-php/Services/Twilio.php'); // Loads the library
		$sid = $options['account_sid'];
		$token = $options['auth_token'];
		if($optionsnew['verifytbl']=='wp_usermeta'){	
			foreach( $results as $key => $row ){
			
				if($usermetaarray[$row['user_id']] == $id || $id = ''){
					$usermetaarray[$row['user_id']][$row['meta_key']] = $row['meta_value'];
					echo '<td>'.$row['meta_value'].'</td>';
					$id = $usermetaarray[$row['user_id']];
				}else{
				
					$usermetaarray[$row->user_id] = array($row['meta_key'] => $row['meta_value']);
					echo '<td>'.$lookupstr.'</td></tr><tr><td>'.$row['meta_value'].'</td>';
					$id = $usermetaarray[$row['user_id']];
					$lookupstr = '';
				}
				if(in_array($row['meta_key'], $optionsnew['twiliofields'])){
					//test here
					$client = new Lookups_Services_Twilio($sid, $token);
					$number = $client->phone_numbers->get($row['meta_value'], array("CountryCode" => "US", "Type" => "caller-name"));
					$callername = $number->caller_name->caller_name;
					$lookupstr = $lookupstr . $callername . ' ';
				} 
				
			}
		}else{
			foreach( $results as $key => $row ){
			echo '<tr>';
			if(!empty($cols)){
				foreach( $cols as $col ){
					echo '<td>'.$row[$col].'</td>';
					if(in_array($col, $optionsnew['twiliofields'] && $row[$col] != '')){
						$client = new Lookups_Services_Twilio($sid, $token);
						$number = $client->phone_numbers->get($row[$col], array("CountryCode" => "US", "Type" => "caller-name"));
						$callername = $number->caller_name->caller_name;
						$lookupstr = $lookupstr . $callername . ' ';
					}		
				}
			}else{
				foreach( $row as $key => $val ){
					echo '<td>'.$val.'</td>';
					if(in_array($key, $optionsnew['twiliofields'] && $row[$col] != '')){
						$client = new Lookups_Services_Twilio($sid, $token);
						$number = $client->phone_numbers->get($val, array("CountryCode" => "US", "Type" => "caller-name"));
						$callername = $number->caller_name->caller_name;
						$lookupstr = $lookupstr . $callername . ' ';
					}		
				}
			}
			echo '<td>'.$lookupstr.'</td></tr>';
		}
		}
		echo '</tr></table>';
		$verified = true;
	}
	
    	$options = twl_get_options();
    	if ( !$options['logging'] ) {
		printf( '<div class="error"> <p> %s </p> </div>', esc_html__( 'Logging currently disabled.', TWL_TD ) );
    	}
    	
    	echo 'Query:'.$qry;
	echo '<table>';
	if($optionsnew['verifytbl']=='wp_usermeta'){
		$id = '';
		foreach( $results as $key => $row ){
			if($usermetaarray[$row['user_id']] == $id || $id = ''){
				$usermetaarray[$row['user_id']][$row['meta_key']] = $row['meta_value'];
				echo '<td>'.$row['meta_value'].'</td>';
				$id = $usermetaarray[$row['user_id']];
			}else{
				$usermetaarray[$row->user_id] = array($row['meta_key'] => $row['meta_value']);
				echo '</tr><tr><td>'.$row['meta_value'].'</td>';	
			}	
		}
	}else{
		foreach( $results as $key => $row ){
			echo '<tr>';
			if(!empty($cols)){
				foreach( $cols as $col ){
					echo '<td>'.$row[$col].'</td>';		
				}
			}else{
				foreach( $row as $key => $val ){
					echo '<td>'.$val.'</td>';		
				}
			}
			echo '</tr>';
		}
	}
	echo '</table>';
    $verify_url = esc_url( wp_nonce_url( add_query_arg( array( 'tab' => $tab, 'run_verify' => 1 ), $page_url ), 'run_verify' ) );
    ?>
    <p><a class="button gray" href="<?php echo $verify_url; ?>"><?php _e( 'Verify', TWL_TD ); ?></a></p>
<?php 
}
add_action( 'twl_display_tab', 'add_my_verify_content', 10, 2 );