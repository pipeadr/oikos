<?php
/**
 * Página de inicio.
 *
 * @package oikos
 */

defined( 'ABSPATH' ) || exit;

// Si en Ajustes → Lectura la portada muestra las últimas entradas, usar el archivo del blog.
if ( ! is_page() ) {
	require get_home_template();
	return;
}

get_header();

while ( have_posts() ) :
	the_post();
	?>
	<article id="post-<?php the_ID(); ?>" <?php post_class( 'oikos-front' ); ?>>
		<h1 class="oikos-front__title"><?php the_title(); ?></h1>
		<div class="oikos-front__content"><?php the_content(); ?></div>
	</article>
	<?php
endwhile;

get_footer();
