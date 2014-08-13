<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


/**
 * Pootlepress_BBPress Class
 *
 * Base class for the Pootlepress BBPress.
 *
 * @package WordPress
 * @subpackage Pootlepress_BBPress
 * @category Core
 * @author Pootlepress
 * @since 1.0.0
 *
 * TABLE OF CONTENTS
 *
 * public $token
 * public $version
 * 
 * - __construct()
 * - add_theme_options()
 * - load_localisation()
 * - check_plugin()
 * - load_plugin_textdomain()
 * - activation()
 * - register_plugin_version()
 */
class Pootlepress_BBPress {
	public $token = 'pootlepress-bbpress';
	public $version;
	private $file;

    private $enabled;

	/**
	 * Constructor.
	 * @param string $file The base file of the plugin.
	 * @access public
	 * @since  1.0.0
	 * @return  void
	 */
	public function __construct ( $file ) {
		$this->file = $file;
		$this->load_plugin_textdomain();
		add_action( 'init', 'check_main_heading', 0 );
		add_action( 'init', array( &$this, 'load_localisation' ), 0 );

		// Run this on activation.
		register_activation_hook( $file, array( &$this, 'activation' ) );

		// Add the custom theme options.
		add_filter( 'option_woo_template', array( &$this, 'add_theme_options' ) );

        add_filter( 'template_include', array($this, 'woo_custom_maybe_load_bbpress_tpl'), 99 );

        add_filter( 'bbp_get_template_stack', array($this, 'woo_custom_deregister_bbpress_template_stack') );


        $this->enabled = get_option('pp-bbp-enabled', 'true') === 'true';

	} // End __construct()

    function woo_custom_maybe_load_bbpress_tpl ( $tpl ) {
        if ($this->enabled && function_exists( 'is_bbpress' ) && is_bbpress() ) {
            $tpl = plugin_dir_path($this->file) . 'templates/bbpress.php';
        }
        return $tpl;
    } // End woo_custom_maybe_load_bbpress_tpl()


    function woo_custom_deregister_bbpress_template_stack ( $stack ) {
        if ($this->enabled && 0 < count( $stack ) ) {
            $stylesheet_dir = get_stylesheet_directory();
            $template_dir = get_template_directory();
            foreach ( $stack as $k => $v ) {
                if ( $stylesheet_dir == $v || $template_dir == $v ) {
                    unset( $stack[$k] );
                }
            }
        }
        return $stack;
    } // End woo_custom_deregister_bbpress_template_stack()


    /**
	 * Add theme options to the WooFramework.
	 * @access public
	 * @since  1.0.0
	 * @param array $o The array of options, as stored in the database.
	 */
	public function add_theme_options ( $o ) {

		$o[] = array(
				'name' => __( 'BBPress', 'pp-bbp' ),
				'type' => 'subheading'
		);
        $o[] = array(
            "id" => "pp-bbp-enabled",
            "name" => __( 'Enable BBPress', 'pp-bbp' ),
            "desc" => __( 'Enable BBPress.', 'pp-bbp' ),
            "type" => "checkbox",
            'std' => 'true'
        );

        return $o;
	} // End add_theme_options()

	/**
	 * Load the plugin's localisation file.
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */
	public function load_localisation () {
		load_plugin_textdomain( $this->token, false, dirname( plugin_basename( $this->file ) ) . '/lang/' );
	} // End load_localisation()

	/**
	 * Load the plugin textdomain from the main WordPress "languages" folder.
	 * @access public
	 * @since  1.0.0
	 * @return  void
	 */
	public function load_plugin_textdomain () {
	    $domain = $this->token;
	    // The "plugin_locale" filter is also used in load_plugin_textdomain()
	    $locale = apply_filters( 'plugin_locale', get_locale(), $domain );
	 
	    load_textdomain( $domain, WP_LANG_DIR . '/' . $domain . '/' . $domain . '-' . $locale . '.mo' );
	    load_plugin_textdomain( $domain, FALSE, dirname( plugin_basename( $this->file ) ) . '/lang/' );
	} // End load_plugin_textdomain()

	/**
	 * Run on activation.
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */
	public function activation () {
		$this->register_plugin_version();
	} // End activation()

	/**
	 * Register the plugin's version.
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */
	private function register_plugin_version () {
		if ( $this->version != '' ) {
			update_option( $this->token . '-version', $this->version );
		}
	} // End register_plugin_version()

} // End Class


