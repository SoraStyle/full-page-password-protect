<?php
/**
 * Full page password protection for singular posts.
 *
 * @package Full_Page_Password_Protect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Replaces protected singular templates with a password form screen.
 */
class FPPP_Protector {
	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'template_redirect', array( $this, 'maybe_render_password_screen' ), 0 );
	}

	/**
	 * Render the password screen instead of the theme template when required.
	 *
	 * @return void
	 */
	public function maybe_render_password_screen() {
		if ( is_admin() || wp_doing_ajax() || ! is_singular() ) {
			return;
		}

		global $post;

		$post = get_queried_object();

		if ( ! $post instanceof WP_Post || ! FPPP_Plugin::should_protect_post( $post ) ) {
			return;
		}

		setup_postdata( $post );

		nocache_headers();
		status_header( 200 );

		$fppp_post    = $post;
		$fppp_message = FPPP_Plugin::get_option( 'fppp_message' );
		$template     = FPPP_PLUGIN_DIR . 'templates/password-form.php';

		include $template;
		exit;
	}
}
