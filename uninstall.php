<?php
/**
 * Plugin uninstall cleanup.
 *
 * @package Full_Page_Password_Protect
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

delete_option( 'fppp_enabled' );
delete_option( 'fppp_post_types' );
delete_option( 'fppp_bypass_admin' );
delete_option( 'fppp_archive_mode' );
delete_option( 'fppp_message' );
