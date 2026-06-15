<?php
/**
 * REST API protection.
 *
 * @package Full_Page_Password_Protect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Removes protected data from REST API responses.
 */
class FPPP_REST {
	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'rest_api_init', array( $this, 'register_filters' ) );
	}

	/**
	 * Register REST filters for public REST-enabled post types.
	 *
	 * @return void
	 */
	public function register_filters() {
		$post_types = get_post_types(
			array(
				'public'       => true,
				'show_in_rest' => true,
			),
			'names'
		);

		unset( $post_types['attachment'] );

		foreach ( $post_types as $post_type ) {
			add_filter( "rest_prepare_{$post_type}", array( $this, 'filter_response' ), 99, 3 );
			add_filter( "rest_{$post_type}_query", array( $this, 'filter_collection_query' ), 10, 2 );
		}
	}

	/**
	 * Exclude protected posts from REST collections when archive mode excludes them.
	 *
	 * @param array           $args    Query arguments.
	 * @param WP_REST_Request $request REST request.
	 * @return array
	 */
	public function filter_collection_query( $args, $request ) {
		unset( $request );

		if ( ! FPPP_Plugin::is_enabled() || 'exclude' !== FPPP_Plugin::get_archive_mode() ) {
			return $args;
		}

		$post_type = $this->get_post_type_from_current_filter();

		if ( ! in_array( $post_type, FPPP_Plugin::get_target_post_types(), true ) ) {
			return $args;
		}

		$args['has_password'] = false;

		return $args;
	}

	/**
	 * Strip protected fields from REST API responses.
	 *
	 * @param WP_REST_Response $response REST response.
	 * @param WP_Post          $post     Post object.
	 * @param WP_REST_Request  $request  REST request.
	 * @return WP_REST_Response
	 */
	public function filter_response( $response, $post, $request ) {
		unset( $request );

		if ( ! $response instanceof WP_REST_Response || ! FPPP_Plugin::should_protect_post( $post ) ) {
			return $response;
		}

		// Allow editors/admins to edit password-protected posts in wp-admin (block editor uses REST).
		if ( is_user_logged_in() && current_user_can( 'edit_post', $post->ID ) ) {
			return $response;
		}

		$data = $response->get_data();

		$data['content']        = array(
			'rendered'  => '',
			'protected' => true,
		);
		$data['excerpt']        = array(
			'rendered'  => '',
			'protected' => true,
		);
		$data['featured_media'] = 0;
		$data['meta']           = array();

		if ( isset( $data['acf'] ) ) {
			$data['acf'] = array();
		}

		if ( 'title_only' !== FPPP_Plugin::get_archive_mode() && isset( $data['title'] ) ) {
			$data['title'] = array(
				'rendered' => '',
			);
		}

		if ( method_exists( $response, 'remove_link' ) ) {
			$response->remove_link( 'wp:featuredmedia' );
		}

		$response->set_data( $data );

		return $response;
	}

	/**
	 * Get the post type from the current REST query filter name.
	 *
	 * @return string
	 */
	private function get_post_type_from_current_filter() {
		$filter = current_filter();

		if ( preg_match( '/^rest_(.+)_query$/', $filter, $matches ) ) {
			return sanitize_key( $matches[1] );
		}

		return '';
	}
}
