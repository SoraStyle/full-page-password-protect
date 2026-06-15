<?php
/**
 * Plugin Name: Full Page Password Protect
 * Plugin URI: https://sora-style.org/products/full-page-password-protect/
 * Description: Protects the full page with the WordPress standard password form, not only the post content.
 * Version: 1.0.0
 * Requires PHP: 7.4
 * Requires at least: 5.8
 * Author: Sora Style
 * Author URI: https://sora-style.org/
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: full-page-password-protect
 * Domain Path: /languages
 *
 * @package Full_Page_Password_Protect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'FPPP_VERSION', '1.0.0' );
define( 'FPPP_PLUGIN_FILE', __FILE__ );
define( 'FPPP_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'FPPP_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'FPPP_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

require_once FPPP_PLUGIN_DIR . 'includes/class-fppp-plugin.php';
require_once FPPP_PLUGIN_DIR . 'includes/class-fppp-protector.php';
require_once FPPP_PLUGIN_DIR . 'includes/class-fppp-settings.php';
require_once FPPP_PLUGIN_DIR . 'includes/class-fppp-rest.php';
require_once FPPP_PLUGIN_DIR . 'includes/class-fppp-archive.php';

register_activation_hook( __FILE__, array( 'FPPP_Plugin', 'activate' ) );

add_action( 'plugins_loaded', array( 'FPPP_Plugin', 'instance' ) );
