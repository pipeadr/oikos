<?php
/**
 * Archivo del blog (página de entradas).
 *
 * @package oikos
 */

defined( 'ABSPATH' ) || exit;

get_header();

$oikos_blog_page_id = (int) get_option( 'page_for_posts' );
$oikos_blog_title   = $oikos_blog_page_id ? get_the_title( $oikos_blog_page_id ) : __( 'Blog', 'oikos' );
?>

<section class="oikos-blog">
	<h1 class="oikos-blog__title"><?php echo esc_html( $oikos_blog_title ); ?></h1>

	<?php if ( have_posts() ) : ?>
		<div class="oikos-blog__grid">
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
		</div>

		<?php the_posts_pagination(); ?>
	<?php else : ?>
		<p><?php esc_html_e( 'Aún no hay entradas publicadas.', 'oikos' ); ?></p>
	<?php endif; ?>
</section>

<?php
get_footer();
