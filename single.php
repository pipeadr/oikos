<?php
/**
 * Entrada individual del blog.
 *
 * @package oikos
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();
	?>
	<article id="post-<?php the_ID(); ?>" <?php post_class( 'oikos-single' ); ?>>
		<?php oikos_breadcrumbs(); ?>

		<header class="oikos-single__header">
			<h1 class="oikos-single__title"><?php the_title(); ?></h1>
			<p class="oikos-single__meta">
				<time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time>
				<?php if ( has_category() ) : ?>
					<span class="oikos-single__categories"><?php the_category( ', ' ); ?></span>
				<?php endif; ?>
			</p>
		</header>

		<div class="oikos-single__content"><?php the_content(); ?></div>
	</article>
	<?php
endwhile;

get_footer();
