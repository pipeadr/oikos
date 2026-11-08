<?php
/**
 * Página genérica.
 *
 * @package oikos
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();
	?>
	<article id="post-<?php the_ID(); ?>" <?php post_class( 'oikos-page' ); ?>>
		<h1 class="oikos-page__title"><?php the_title(); ?></h1>
		<div class="oikos-page__content"><?php the_content(); ?></div>
	</article>
	<?php
endwhile;

get_footer();
