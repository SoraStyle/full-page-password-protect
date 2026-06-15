<?php
/**
 * Admin settings screen.
 *
 * @package Full_Page_Password_Protect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Plugin settings page.
 */
class FPPP_Settings {
	/**
	 * Settings page slug.
	 *
	 * @var string
	 */
	const PAGE_SLUG = 'fppp-settings';

	/**
	 * Settings group.
	 *
	 * @var string
	 */
	const SETTINGS_GROUP = 'fppp_settings';

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_settings_page' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
	}

	/**
	 * Add settings page under Settings.
	 *
	 * @return void
	 */
	public function add_settings_page() {
		add_options_page(
			__( 'Full Page Password Protect', 'full-page-password-protect' ),
			__( 'Full Page Password', 'full-page-password-protect' ),
			'manage_options',
			self::PAGE_SLUG,
			array( $this, 'render_page' )
		);
	}

	/**
	 * Register plugin settings.
	 *
	 * @return void
	 */
	public function register_settings() {
		register_setting(
			self::SETTINGS_GROUP,
			'fppp_enabled',
			array(
				'type'              => 'integer',
				'sanitize_callback' => array( $this, 'sanitize_checkbox' ),
				'default'           => 1,
			)
		);

		register_setting(
			self::SETTINGS_GROUP,
			'fppp_post_types',
			array(
				'type'              => 'array',
				'sanitize_callback' => array( $this, 'sanitize_post_types' ),
				'default'           => array( 'post', 'page' ),
			)
		);

		register_setting(
			self::SETTINGS_GROUP,
			'fppp_archive_mode',
			array(
				'type'              => 'string',
				'sanitize_callback' => array( $this, 'sanitize_archive_mode' ),
				'default'           => 'exclude',
			)
		);

		register_setting(
			self::SETTINGS_GROUP,
			'fppp_message',
			array(
				'type'              => 'string',
				'sanitize_callback' => 'sanitize_textarea_field',
				'default'           => FPPP_Plugin::get_defaults()['fppp_message'],
			)
		);

		add_settings_section(
			'fppp_main',
			__( 'Protection Settings', 'full-page-password-protect' ),
			'__return_false',
			self::PAGE_SLUG
		);

		add_settings_field(
			'fppp_enabled',
			__( 'Enable plugin', 'full-page-password-protect' ),
			array( $this, 'render_enabled_field' ),
			self::PAGE_SLUG,
			'fppp_main'
		);

		add_settings_field(
			'fppp_post_types',
			__( 'Protected post types', 'full-page-password-protect' ),
			array( $this, 'render_post_types_field' ),
			self::PAGE_SLUG,
			'fppp_main'
		);

		add_settings_field(
			'fppp_archive_mode',
			__( 'Archive display mode', 'full-page-password-protect' ),
			array( $this, 'render_archive_mode_field' ),
			self::PAGE_SLUG,
			'fppp_main'
		);

		add_settings_field(
			'fppp_message',
			__( 'Password message', 'full-page-password-protect' ),
			array( $this, 'render_message_field' ),
			self::PAGE_SLUG,
			'fppp_main'
		);
	}

	/**
	 * Sanitize a checkbox value.
	 *
	 * @param mixed $value Input value.
	 * @return int
	 */
	public function sanitize_checkbox( $value ) {
		return empty( $value ) ? 0 : 1;
	}

	/**
	 * Sanitize selected post types.
	 *
	 * @param mixed $value Input value.
	 * @return string[]
	 */
	public function sanitize_post_types( $value ) {
		$value        = is_array( $value ) ? $value : array();
		$public_types = array_keys( FPPP_Plugin::get_public_post_types() );
		$value        = array_map( 'sanitize_key', $value );

		return array_values( array_intersect( $value, $public_types ) );
	}

	/**
	 * Sanitize archive mode.
	 *
	 * @param mixed $value Input value.
	 * @return string
	 */
	public function sanitize_archive_mode( $value ) {
		$value = sanitize_key( (string) $value );

		return in_array( $value, array( 'exclude', 'title_only' ), true ) ? $value : 'exclude';
	}

	/**
	 * Render enabled checkbox.
	 *
	 * @return void
	 */
	public function render_enabled_field() {
		?>
		<input type="hidden" name="fppp_enabled" value="0">
		<label>
			<input type="checkbox" name="fppp_enabled" value="1" <?php checked( FPPP_Plugin::get_option( 'fppp_enabled' ), 1 ); ?>>
			<?php esc_html_e( 'Enable full page password protection.', 'full-page-password-protect' ); ?>
		</label>
		<?php
	}

	/**
	 * Render protected post types checkboxes.
	 *
	 * @return void
	 */
	public function render_post_types_field() {
		$selected   = FPPP_Plugin::get_target_post_types();
		$post_types = FPPP_Plugin::get_public_post_types();
		?>
		<input type="hidden" name="fppp_post_types[]" value="">
		<?php foreach ( $post_types as $post_type ) : ?>
			<label style="display:block;margin:0 0 6px;">
				<input type="checkbox" name="fppp_post_types[]" value="<?php echo esc_attr( $post_type->name ); ?>" <?php checked( in_array( $post_type->name, $selected, true ) ); ?>>
				<?php echo esc_html( $post_type->labels->singular_name ); ?>
				<code><?php echo esc_html( $post_type->name ); ?></code>
			</label>
		<?php endforeach; ?>
		<p class="description"><?php esc_html_e( 'Only public post types can be protected.', 'full-page-password-protect' ); ?></p>
		<?php
	}

	/**
	 * Render archive mode select.
	 *
	 * @return void
	 */
	public function render_archive_mode_field() {
		$mode = FPPP_Plugin::get_archive_mode();
		?>
		<select name="fppp_archive_mode">
			<option value="exclude" <?php selected( $mode, 'exclude' ); ?>><?php esc_html_e( 'Exclude protected posts', 'full-page-password-protect' ); ?></option>
			<option value="title_only" <?php selected( $mode, 'title_only' ); ?>><?php esc_html_e( 'Show title only', 'full-page-password-protect' ); ?></option>
		</select>
		<p class="description"><?php esc_html_e( 'Title only mode hides excerpts, content, and featured images in listings.', 'full-page-password-protect' ); ?></p>
		<?php
	}

	/**
	 * Render message textarea.
	 *
	 * @return void
	 */
	public function render_message_field() {
		$message = (string) FPPP_Plugin::get_option( 'fppp_message' );
		?>
		<textarea name="fppp_message" rows="5" class="large-text"><?php echo esc_textarea( $message ); ?></textarea>
		<?php
	}

	/**
	 * Render settings page.
	 *
	 * @return void
	 */
	public function render_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'Full Page Password Protect', 'full-page-password-protect' ); ?></h1>
			<form method="post" action="options.php">
				<?php
				settings_fields( self::SETTINGS_GROUP );
				do_settings_sections( self::PAGE_SLUG );
				submit_button();
				?>
			</form>
		</div>
		<?php
	}
}
