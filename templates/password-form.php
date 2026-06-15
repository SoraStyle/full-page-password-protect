<?php
/**
 * Password form screen.
 *
 * @package Full_Page_Password_Protect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$fppp_post        = isset( $fppp_post ) && $fppp_post instanceof WP_Post ? $fppp_post : get_post();
$fppp_message     = isset( $fppp_message ) ? (string) $fppp_message : (string) FPPP_Plugin::get_option( 'fppp_message' );
$fppp_form        = $fppp_post instanceof WP_Post ? FPPP_Plugin::get_password_form_markup( $fppp_post ) : '';
$fppp_show_error  = $fppp_post instanceof WP_Post && FPPP_Plugin::has_invalid_password_cookie( $fppp_post );
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="robots" content="noindex, nofollow">
	<title><?php esc_html_e( 'Protected Page', 'full-page-password-protect' ); ?></title>
	<?php
	wp_enqueue_style( 'fppp-frontend', FPPP_PLUGIN_URL . 'assets/css/frontend.css', array(), FPPP_VERSION );
	wp_print_styles( 'fppp-frontend' );
	?>
</head>
<body class="fppp-password-protected">
	<main class="fppp-password-screen" role="main">
		<section
			class="fppp-password-card"
			aria-label="<?php esc_attr_e( 'Password protected content', 'full-page-password-protect' ); ?>"
		>
			<?php if ( '' !== trim( $fppp_message ) ) : ?>
				<div class="fppp-password-message">
					<?php echo wp_kses_post( wpautop( esc_html( $fppp_message ) ) ); ?>
				</div>
			<?php endif; ?>

			<?php if ( $fppp_show_error ) : ?>
				<p class="fppp-password-error" role="alert">
					<?php esc_html_e( 'The password is incorrect. Please try again.', 'full-page-password-protect' ); ?>
				</p>
			<?php endif; ?>

			<?php if ( '' !== $fppp_form ) : ?>
				<div class="fppp-password-form">
					<?php
					echo wp_kses(
						$fppp_form,
						FPPP_Plugin::get_password_form_allowed_html()
					);
					?>
				</div>
			<?php endif; ?>
		</section>
	</main>
</body>
</html>
