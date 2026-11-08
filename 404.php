<?php
/**
 * Página no encontrada.
 *
 * @package oikos
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>

<section class="oikos-404">
	<h1 class="oikos-404__title"><?php esc_html_e( 'Página no encontrada', 'oikos' ); ?></h1>
	<p><?php esc_html_e( 'La página que buscas no existe o cambió de dirección.', 'oikos' ); ?></p>

	<?php get_search_form(); ?>

	<p>
		<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Volver al inicio', 'oikos' ); ?></a>
	</p>
</section>

<?php
get_footer();
