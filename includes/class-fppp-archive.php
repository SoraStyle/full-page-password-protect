<?php
/**
 * Archive and search protection.
 *
 * @package Full_Page_Password_Protect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Controls protected posts in listings.
 */
class FPPP_Archive {
	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'pre_get_posts', array( $this, 'maybe_exclude_protected_posts' ) );
		add_filter( 'posts_where', array( $this, 'filter_password_posts_where' ), 10, 2 );
		add_filter( 'get_the_excerpt', array( $this, 'maybe_hide_excerpt' ), 99, 2 );
		add_filter( 'the_excerpt', array( $this, 'maybe_hide_excerpt_output' ), 99 );
		add_filter( 'the_content', array( $this, 'maybe_hide_content' ), 99 );
		add_filter( 'post_thumbnail_html', array( $this, 'maybe_hide_featured_image' ), 99, 5 );
	}

	/**
	 * Exclude password-protected posts from listing queries.
	 *
	 * @param WP_Query $query Query object.
	 * @return void
	 */
	public function maybe_exclude_protected_posts( $query ) {
		if ( is_admin() || ! $query instanceof WP_Query || ! $query->is_main_query() ) {
			return;
		}

		if ( ! FPPP_Plugin::is_enabled() || 'exclude' !== FPPP_Plugin::get_archive_mode() ) {
			return;
		}

		if ( ! $this->is_listing_query( $query ) || ! $this->query_includes_target_post_type( $query ) ) {
			return;
		}

		$query->set( 'fppp_exclude_passwords', true );
	}

	/**
	 * Exclude password-protected target post types without affecting other post types.
	 *
	 * @param string   $where SQL where clause.
	 * @param WP_Query $query Query object.
	 * @return string
	 */
	public function filter_password_posts_where( $where, $query ) {
		global $wpdb;

		if ( is_admin() || ! $query instanceof WP_Query || ! $query->get( 'fppp_exclude_passwords' ) ) {
			return $where;
		}

		$target_post_types = FPPP_Plugin::get_target_post_types();

		if ( empty( $target_post_types ) ) {
			return $where;
		}

		$prepared_types = array();

		foreach ( $target_post_types as $post_type ) {
			$prepared_types[] = $wpdb->prepare( '%s', $post_type );
		}

		$in_clause = implode( ', ', $prepared_types );

		// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- Each post type value is prepared individually above.
		return $where . " AND NOT ({$wpdb->posts}.post_type IN ({$in_clause}) AND {$wpdb->posts}.post_password <> '')";
	}

	/**
	 * Hide generated excerpts in title-only mode.
	 *
	 * @param string       $excerpt Excerpt text.
	 * @param WP_Post|null $post    Post object.
	 * @return string
	 */
	public function maybe_hide_excerpt( $excerpt, $post = null ) {
		if ( $this->should_hide_listing_fields( $post ) ) {
			return '';
		}

		return $excerpt;
	}

	/**
	 * Hide excerpt output in title-only mode.
	 *
	 * @param string $excerpt Excerpt HTML.
	 * @return string
	 */
	public function maybe_hide_excerpt_output( $excerpt ) {
		if ( $this->should_hide_listing_fields( get_post() ) ) {
			return '';
		}

		return $excerpt;
	}

	/**
	 * Hide content output in listing contexts for title-only mode.
	 *
	 * @param string $content Content HTML.
	 * @return string
	 */
	public function maybe_hide_content( $content ) {
		if ( $this->should_hide_listing_fields( get_post() ) ) {
			return '';
		}

		return $content;
	}

	/**
	 * Hide featured image HTML in title-only mode.
	 *
	 * @param string       $html              Featured image HTML.
	 * @param int          $post_id           Post ID.
	 * @param int|string   $post_thumbnail_id Thumbnail ID.
	 * @param string|array $size              Image size.
	 * @param string|array $attr              Image attributes.
	 * @return string
	 */
	public function maybe_hide_featured_image( $html, $post_id, $post_thumbnail_id, $size, $attr ) {
		unset( $post_thumbnail_id, $size, $attr );

		if ( $this->should_hide_listing_fields( $post_id ) ) {
			return '';
		}

		return $html;
	}

	/**
	 * Check whether a query is a public listing context.
	 *
	 * @param WP_Query $query Query object.
	 * @return bool
	 */
	private function is_listing_query( $query ) {
		return $query->is_home()
			|| $query->is_archive()
			|| $query->is_search()
			|| $query->is_author()
			|| $query->is_category()
			|| $query->is_tag()
			|| $query->is_tax();
	}

	/**
	 * Check whether a query can include a protected target post type.
	 *
	 * @param WP_Query $query Query object.
	 * @return bool
	 */
	private function query_includes_target_post_type( $query ) {
		$target_post_types = FPPP_Plugin::get_target_post_types();

		if ( empty( $target_post_types ) ) {
			return false;
		}

		$query_post_type = $query->get( 'post_type' );

		if ( empty( $query_post_type ) && $query->is_search() ) {
			$query_post_type = 'any';
		}

		if ( empty( $query_post_type ) ) {
			$query_post_type = array( 'post' );
		}

		if ( 'any' === $query_post_type ) {
			return true;
		}

		$query_post_type = (array) $query_post_type;

		return (bool) array_intersect( $target_post_types, $query_post_type );
	}

	/**
	 * Check whether protected listing fields should be hidden.
	 *
	 * @param WP_Post|int|null $post Post object or ID.
	 * @return bool
	 */
	private function should_hide_listing_fields( $post ) {
		if ( is_admin() || 'title_only' !== FPPP_Plugin::get_archive_mode() || ! $this->is_current_request_listing() ) {
			return false;
		}

		return FPPP_Plugin::should_protect_post( $post );
	}

	/**
	 * Check whether the current request is a listing page.
	 *
	 * @return bool
	 */
	private function is_current_request_listing() {
		return is_home() || is_archive() || is_search() || is_author() || is_category() || is_tag() || is_tax();
	}
}
