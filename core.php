<?php
/**
 * Plugin Name: WP Twilio Core
 * Plugin URI: http://themebound.com
 * Description: A simple plugin to add SMS capability to your website using the Twilio API. Allows developers to easily extend the settings page and built in functionality.
 * Version: 1.0.0
 * Author: Themebound.com
 * Author URI: http://themebound.com
 * License: GPLv2 or later
 */

define( 'TWL_CORE_VERSION', '1.0.0' );
define( 'TWL_CORE_OPTION', 'twl_option' );
define( 'TWL_CORE_OPTION_PAGE', 'twilio-options' );
define( 'TWL_CORE_SETTING', 'twilio-options' );
define( 'TWL_LOGS_OPTION', 'twl_logs' );

if( !defined( 'TWL_TD' ) ) {
	define( 'TWL_TD', 'twilio-core' );
}

if( !defined( 'TWL_PATH' ) ) {
	define( 'TWL_PATH', plugin_dir_path( __FILE__ ) );
}

require_once( 'twilio-php/Services/Twilio.php' );
require_once( 'helpers.php' );
if ( is_admin() ) {
	require_once( 'admin-pages.php' );
}

class WP_Twilio_Core {
	private static $instance;
	private $page_url;

	private function __construct() {
		$this->set_page_url();

		if ( is_admin() ) {
			
			/** Settings Pages **/
			add_action( 'admin_init', array( $this, 'register_settings' ), 1000 );
			add_action( 'admin_menu', array( $this, 'admin_menu' ), 1000 );
			
		}
	}

	/**
	 * Send SMS
	 * @param  array $args The arguments used by the Twilio API
	 * @return void
	 * @access public
	 */
	public function send_sms( $args ) {
		// Back compat
		twl_send_sms( $args );
	}

	/**
	 * Add the Pushover Notifications item to the Settings menu
	 * @return void
	 * @access public
	 */
	public function admin_menu() {
		add_options_page( __( 'Twilio', TWL_TD ), __( 'Twilio', TWL_TD ), 'administrator', TWL_CORE_OPTION_PAGE, array( $this, 'display_tabs' ) );
	}

	/**
	 * Determines what tab is being displayed, and executes the display of that tab
	 * @return void
	 * @access public
	 */
	public function display_tabs() {
		$options = $this->get_options();
		$tabs = $this->get_tabs();
		$current = ( !isset( $_GET['tab'] ) ) ? current( array_keys( $tabs ) ) : $_GET['tab'];
		?>
		<div class="wrap">
			<div id="icon-options-general" class="icon32"></div><h2><?php _e( 'Twilio for WordPress', TWL_TD ); ?></h2>
			<h2 class="nav-tab-wrapper"><?php
			foreach( $tabs as $tab => $name ) {
				$classes = array( 'nav-tab' );
				if( $tab == $current ) {
					$classes[] = 'nav-tab-active';
				}
				$href = esc_url( add_query_arg( 'tab', $tab, $this->page_url ) );
				echo '<a class="' . implode( ' ', $classes ) . '" href="' . $href . '">' . $name . '</a>';
			}
			?>
			</h2>
			
			<?php do_action( 'twl_display_tab', $current, $this->page_url ); ?>
		</div>
		<?php
	}
	
	/**
	 * Saves the URL of the plugin settings page into the class property
	 * @return void
	 * @access public
	 */
	public function set_page_url() {
		$base = '';
		$this->page_url = add_query_arg( 'page',  TWL_CORE_OPTION_PAGE, $base );
	}
	
	/**
	 * Returns an array of settings tabs, extensible via a filter
	 * @return void
	 * @access public
	 */
	public function get_tabs() {
		$default_tabs = array(
			'general' => __( 'Settings', TWL_TD ),
			'logs' => __( 'Logs', TWL_TD ),
			'test' => __( 'Test', TWL_TD ),
			//'extensions' => __( 'Get Extensions', TWL_TD ),
		);
		return apply_filters( 'twl_settings_tabs', $default_tabs );
	}

	/**
	 * Register/Whitelist our settings on the settings page, allow extensions and other plugins to hook into this
	 * @return void
	 * @access public
	 */
	public function register_settings() {
		register_setting( TWL_CORE_SETTING, TWL_CORE_OPTION, 'twl_sanitize_option' );
		do_action( 'twl_register_additional_settings' );
	}

	/**
	 * Original get_options unifier
	 * @return array List of options
	 * @access public
	 */
	public function get_options() {
		return twl_get_options();
	}
	
	/**
	 * Get the singleton instance of our plugin
	 * @return class The Instance
	 * @access public
	 */
	public static function get_instance() {
		if ( !self::$instance ) {
			self::$instance = new WP_Twilio_Core();
		}

		return self::$instance;
	}
	
	/**
	 * Adds the options to the options table
	 * @return void
	 * @access public
	 */
	public static function plugin_activated() {
		add_option( TWL_CORE_OPTION, twl_get_defaults() );
		add_option( TWL_LOGS_OPTION, '' );
	}
	
	/**
	 * Deletes the options to the options table
	 * @return void
	 * @access public
	 */
	public static function plugin_uninstalled() {
		delete_option( TWL_CORE_OPTION );
		delete_option( TWL_LOGS_OPTION );
	}

}

$twl_instance = WP_Twilio_Core::get_instance();
register_activation_hook( __FILE__, array( 'WP_Twilio_Core', 'plugin_activated' ) );
register_uninstall_hook( __FILE__, array( 'WP_Twilio_Core', 'plugin_uninstalled' ) );