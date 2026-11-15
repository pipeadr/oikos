<?php
/**
 * Sección: bloque de blog ("Ideas para relaciones y vidas más conscientes").
 *
 * Una entrada grande a la izquierda y hasta dos horizontales a la derecha.
 * Con menos entradas se adapta sin huecos; sin entradas no se imprime.
 *
 * No lee ACF: la plantilla le pasa los datos por $args.
 *
 * Args:
 * - title          (string)     Título de la sección (obligatorio).
 * - heading_level  (int)        2–4. Los títulos de tarjeta van un nivel más abajo.
 * - posts          (int[])      Entradas elegidas a mano (opcional; se completan con recientes).
 * - category       (int|string) Categoría a priorizar (ID o slug; páginas de servicio).
 * - exclude        (int[])      Entradas a excluir (ej. la actual).
 * - link           (array)      url, title, target del botón. Vacío → página de entradas.
 * - track_location (string)     data-track-location de tarjetas y botón.
 *
 * @package oikos
 */

defined( 'ABSPATH' ) || exit;

$oikos_args = wp_parse_args(
	isset( $args ) && is_array( $args ) ? $args : array(),
	array(
		'title'          => '',
		'heading_level'  => 2,
		'posts'          => array(),
		'category'       => 0,
		'exclude'        => array(),
		'link'           => array(),
		'track_location' => '',
	)
);

$oikos_title = trim( (string) $oikos_args['title'] );

$oikos_posts = oikos_get_block_posts(
	array(
		'posts'    => (array) $oikos_args['posts'],
		'category' => $oikos_args['category'],
		'exclude'  => (array) $oikos_args['exclude'],
		'count'    => 3,
	)
);

if ( '' === $oikos_title || ! $oikos_posts ) {
	return;
}

// Botón: el del campo o, si está vacío, la página de entradas.
$oikos_link = is_array( $oikos_args['link'] ) ? $oikos_args['link'] : array();
if ( empty( $oikos_link['url'] ) ) {
	$oikos_blog_id = (int) get_option( 'page_for_posts' );
	$oikos_link    = $oikos_blog_id ? array(
		'url'    => get_permalink( $oikos_blog_id ),
		'title'  => __( 'Explora el blog', 'oikos' ),
		'target' => '',
	) : array();
} elseif ( empty( $oikos_link['title'] ) ) {
	$oikos_link['title'] = __( 'Explora el blog', 'oikos' );
}

$oikos_level    = min( 4, max( 2, (int) $oikos_args['heading_level'] ) );
$oikos_title_id = wp_unique_id( 'oikos-posts-block-' );
$oikos_location = (string) $oikos_args['track_location'];
$oikos_featured = array_shift( $oikos_posts );
$oikos_count    = 1 + count( $oikos_posts );
?>
<section class="<?php echo esc_attr( 'oikos-section oikos-posts-block oikos-posts-block--count-' . $oikos_count ); ?>" aria-labelledby="<?php echo esc_attr( $oikos_title_id ); ?>">
	<div class="oikos-container oikos-posts-block__inner">
		<?php
		printf(
			'<h%1$d id="%2$s" class="oikos-posts-block__title oikos-text-heading">%3$s</h%1$d>',
			(int) $oikos_level,
			esc_attr( $oikos_title_id ),
			esc_html( $oikos_title )
		);
		?>

		<div class="oikos-posts-block__grid">
			<div class="oikos-posts-block__featured">
				<?php
				get_template_part(
					'template-parts/components/post-card',
					null,
					array(
						'post_id'        => $oikos_featured,
						'variant'        => 'featured',
						'heading_level'  => $oikos_level + 1,
						'track_location' => $oikos_location,
					)
				);
				?>
			</div>

			<?php if ( $oikos_posts ) : ?>
				<div class="oikos-posts-block__side">
					<?php
					foreach ( $oikos_posts as $oikos_post_id ) {
						get_template_part(
							'template-parts/components/post-card',
							null,
							array(
								'post_id'        => $oikos_post_id,
								'variant'        => 'horizontal',
								'heading_level'  => $oikos_level + 1,
								'track_location' => $oikos_location,
							)
						);
					}
					?>
				</div>
			<?php endif; ?>
		</div>

		<?php if ( ! empty( $oikos_link['url'] ) ) : ?>
			<a
				class="oikos-btn oikos-btn--primary oikos-btn--block-mobile oikos-posts-block__cta"
				href="<?php echo esc_url( $oikos_link['url'] ); ?>"
				<?php echo ( isset( $oikos_link['target'] ) && '_blank' === $oikos_link['target'] ) ? 'target="_blank" rel="noopener"' : ''; ?>
				data-track="ver-blog"
				data-track-location="<?php echo esc_attr( $oikos_location ); ?>"
			><?php echo esc_html( $oikos_link['title'] ); ?></a>
		<?php endif; ?>
	</div>
</section>
