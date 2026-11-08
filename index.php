<?php
/**
 * Plantilla de respaldo.
 *
 * @package oikos
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>

<section class="oikos-archive">
	<h1 class="oikos-archive__title">
		<?php
		if ( is_search() ) {
			/* translators: %s: término buscado. */
			printf( esc_html__( 'Resultados para: %s', 'oikos' ), esc_html( get_search_query() ) );
		} elseif ( is_archive() ) {
			echo wp_kses_post( get_the_archive_title() );
		} else {
			bloginfo( 'name' );
		}
		?>
	</h1>

	<?php if ( have_posts() ) : ?>
		<?php
		while ( have_posts() ) :
			the_post();
			?>
			<article id="post-<?php the_ID(); ?>" <?php post_class( 'oikos-entry' ); ?>>
				<h2 class="oikos-entry__title">
					<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
				</h2>
				<div class="oikos-entry__excerpt"><?php the_excerpt(); ?></div>
			</article>
		<?php endwhile; ?>

		<?php the_posts_pagination(); ?>
	<?php else : ?>
		<p><?php esc_html_e( 'No se encontró contenido.', 'oikos' ); ?></p>
	<?php endif; ?>
</section>

<?php
get_footer();
