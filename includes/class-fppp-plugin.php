<?php
/**
 * Core plugin bootstrap and shared helpers.
 *
 * @package Full_Page_Password_Protect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Main plugin class.
 */
class FPPP_Plugin {
	/**
	 * Singleton instance.
	 *
	 * @var FPPP_Plugin|null
	 */
	private static $instance = null;

	/**
	 * Plugin components.
	 *
	 * @var array
	 */
	private $components = array();

	/**
	 * Get singleton instance.
	 *
	 * @return FPPP_Plugin
	 */
	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Set default options on activation.
	 *
	 * @return void
	 */
	public static function activate() {
		self::load_textdomain();

		foreach ( self::get_defaults() as $option => $default ) {
			if ( false === get_option( $option, false ) ) {
				add_option( $option, $default );
			}
		}
	}

	/**
	 * Load plugin translations.
	 *
	 * @return void
	 */
	public static function load_textdomain() {
		load_plugin_textdomain(
			'full-page-password-protect',
			false,
			dirname( FPPP_PLUGIN_BASENAME ) . '/languages'
		);
	}

	/**
	 * Get the default password message.
	 *
	 * @return string
	 */
	public static function get_default_message() {
		return __(
			"This page is password protected.\nPlease enter the password to view it.",
			'full-page-password-protect'
		);
	}

	/**
	 * Get default option values.
	 *
	 * @return array
	 */
	public static function get_defaults() {
		return array(
			'fppp_enabled'      => 1,
			'fppp_post_types'   => array( 'post', 'page' ),
			'fppp_archive_mode' => 'exclude',
			'fppp_message'      => self::get_default_message(),
		);
	}

	/**
	 * Get an option with plugin defaults.
	 *
	 * @param string $option Option name.
	 * @return mixed
	 */
	public static function get_option( $option ) {
		$defaults = self::get_defaults();
		$default  = array_key_exists( $option, $defaults ) ? $defaults[ $option ] : null;

		return get_option( $option, $default );
	}

	/**
	 * Check whether the plugin is enabled.
	 *
	 * @return bool
	 */
	public static function is_enabled() {
		return (bool) absint( self::get_option( 'fppp_enabled' ) );
	}

	/**
	 * Get public post types available for protection settings.
	 *
	 * @return array<string,WP_Post_Type>
	 */
	public static function get_public_post_types() {
		$post_types = get_post_types(
			array(
				'public' => true,
			),
			'objects'
		);

		unset( $post_types['attachment'] );

		return $post_types;
	}

	/**
	 * Get protected post type names.
	 *
	 * @return string[]
	 */
	public static function get_target_post_types() {
		$selected     = self::get_option( 'fppp_post_types' );
		$selected     = is_array( $selected ) ? $selected : array();
		$public_types = array_keys( self::get_public_post_types() );

		$selected = array_map( 'sanitize_key', $selected );
		$selected = array_values( array_intersect( $selected, $public_types ) );

		return $selected;
	}

	/**
	 * Check whether a post belongs to a protected post type.
	 *
	 * @param WP_Post|int|null $post Post object or ID.
	 * @return bool
	 */
	public static function is_target_post( $post ) {
		$post = get_post( $post );

		if ( ! $post instanceof WP_Post ) {
			return false;
		}

		return in_array( $post->post_type, self::get_target_post_types(), true );
	}

	/**
	 * Check whether the current visitor has unlocked a password-protected post.
	 *
	 * Uses the same WordPress post password cookie as core, but does not rely on
	 * post_password_required() so logged-in users are not bypassed by filters.
	 *
	 * @param WP_Post|int|null $post Post object or ID.
	 * @return bool
	 */
	public static function is_post_password_unlocked( $post ) {
		$post = get_post( $post );

		if ( ! $post instanceof WP_Post || '' === $post->post_password ) {
			return true;
		}

		if ( ! defined( 'COOKIEHASH' ) ) {
			return false;
		}

		$cookie_name = 'wp-postpass_' . COOKIEHASH;

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only check of the WordPress core post password cookie.
		if ( empty( $_COOKIE[ $cookie_name ] ) ) {
			return false;
		}

		require_once ABSPATH . WPINC . '/class-phpass.php';

		$hasher = new PasswordHash( 8, true );
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only check of the WordPress core post password cookie.
		$hash = wp_unslash( $_COOKIE[ $cookie_name ] );

		if ( 0 !== strpos( $hash, '$P$B' ) ) {
			return false;
		}

		return $hasher->CheckPassword( $post->post_password, $hash );
	}

	/**
	 * Check whether a post should be protected from the current request.
	 *
	 * @param WP_Post|int|null $post Post object or ID.
	 * @return bool
	 */
	public static function should_protect_post( $post ) {
		$post = get_post( $post );

		if ( ! $post instanceof WP_Post ) {
			return false;
		}

		if ( ! self::is_enabled() || ! self::is_target_post( $post ) ) {
			return false;
		}

		if ( '' === $post->post_password ) {
			return false;
		}

		return ! self::is_post_password_unlocked( $post );
	}

	/**
	 * Allowed HTML for the core password form output.
	 *
	 * @return array<string, array<string, bool>>
	 */
	public static function get_password_form_allowed_html() {
		return array(
			'form'  => array(
				'action' => true,
				'method' => true,
				'class'  => true,
				'id'     => true,
			),
			'p'     => array(
				'class' => true,
			),
			'label' => array(
				'for'   => true,
				'class' => true,
			),
			'input' => array(
				'type'         => true,
				'name'         => true,
				'id'           => true,
				'class'        => true,
				'value'        => true,
				'size'         => true,
				'spellcheck'   => true,
				'autocomplete' => true,
			),
		);
	}

	/**
	 * Build password form markup for the plugin screen.
	 *
	 * @param WP_Post $post Post object.
	 * @return string
	 */
	public static function get_password_form_markup( $post ) {
		add_filter( 'the_password_form', array( __CLASS__, 'filter_password_form_markup' ), 10, 2 );
		add_filter( 'the_password_form_incorrect_password', array( __CLASS__, 'filter_incorrect_password_message' ), 10, 2 );
		$form = get_the_password_form( $post );
		remove_filter( 'the_password_form', array( __CLASS__, 'filter_password_form_markup' ), 10 );
		remove_filter( 'the_password_form_incorrect_password', array( __CLASS__, 'filter_incorrect_password_message' ), 10 );

		return (string) $form;
	}

	/**
	 * Suppress the core invalid password message; the plugin template renders its own.
	 *
	 * @param string  $text Invalid password message.
	 * @param WP_Post $post Post object.
	 * @return string
	 */
	public static function filter_incorrect_password_message( $text, $post ) {
		unset( $text, $post );

		return '';
	}

	/**
	 * Remove core password form messages from the markup.
	 *
	 * @param string  $output Password form HTML.
	 * @param WP_Post $post   Post object.
	 * @return string
	 */
	public static function filter_password_form_markup( $output, $post ) {
		unset( $post );

		$output = (string) preg_replace(
			'/<div class="post-password-form-invalid-password"[^>]*>.*?<\/div>\s*/is',
			'',
			$output
		);

		return (string) preg_replace( '/<p>.*?<\/p>\s*/s', '', $output, 1 );
	}

	/**
	 * Check whether the password form was submitted with a wrong password.
	 *
	 * @param WP_Post $post Post object.
	 * @return bool
	 */
	public static function has_invalid_password_cookie( $post ) {
		$post = get_post( $post );

		if ( ! $post instanceof WP_Post || self::is_post_password_unlocked( $post ) ) {
			return false;
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Missing -- WordPress core post password form field.
		if ( isset( $_POST['post_password'] ) ) {
			return true;
		}

		if ( empty( $post->ID ) || ! defined( 'COOKIEHASH' ) ) {
			return false;
		}

		$cookie_name = 'wp-postpass_' . COOKIEHASH;

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only check of the WordPress core post password cookie.
		if ( empty( $_COOKIE[ $cookie_name ] ) ) {
			return false;
		}

		// Match WordPress 6.8+ invalid password detection in get_the_password_form().
		if ( function_exists( 'wp_get_raw_referer' ) && wp_get_raw_referer() === get_permalink( $post->ID ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Get archive visibility mode.
	 *
	 * @return string
	 */
	public static function get_archive_mode() {
		$mode = self::get_option( 'fppp_archive_mode' );

		return in_array( $mode, array( 'exclude', 'title_only' ), true ) ? $mode : 'exclude';
	}

	/**
	 * Constructor.
	 */
	private function __construct() {
		add_action( 'init', array( __CLASS__, 'load_textdomain' ) );

		$this->components = array(
			'settings'  => new FPPP_Settings(),
			'protector' => new FPPP_Protector(),
			'rest'      => new FPPP_REST(),
			'archive'   => new FPPP_Archive(),
		);
	}
}
