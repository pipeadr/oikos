<?php
/**
 * Componente: tarjeta de entrada del blog.
 *
 * Variantes:
 * - featured:   imagen ancha arriba; etiqueta, título y autores debajo.
 * - horizontal: imagen cuadrada a la izquierda; etiqueta, título y autores a la derecha.
 *
 * Un solo enlace (el título); su ::after cubre toda la tarjeta, así que se
 * hace clic en cualquier parte pero el teclado y los lectores de pantalla
 * encuentran un único enlace con el título como nombre. La imagen es
 * decorativa (alt="") porque el enlace ya dice a dónde lleva.
 *
 * Args:
 * - post_id        (int)    Entrada (obligatorio).
 * - variant        (string) 'featured' | 'horizontal'.
 * - heading_level  (int)    2–6 (3 por defecto).
 * - track_location (string) data-track-location del enlace.
 * - image_loading  (string) 'lazy' | 'eager'.
 * - sizes          (string) Atributo sizes de la imagen (opcional; cada variante trae uno).
 *
 * @package oikos
 */

defined( 'ABSPATH' ) || exit;

$oikos_args = wp_parse_args(
	isset( $args ) && is_array( $args ) ? $args : array(),
	array(
		'post_id'        => 0,
		'variant'        => 'featured',
		'heading_level'  => 3,
		'track_location' => '',
		'image_loading'  => 'lazy',
		'sizes'          => '',
	)
);

$oikos_post_id = absint( $oikos_args['post_id'] );

if ( ! $oikos_post_id || 'publish' !== get_post_status( $oikos_post_id ) ) {
	return;
}

$oikos_variant = in_array( $oikos_args['variant'], array( 'featured', 'horizontal' ), true ) ? $oikos_args['variant'] : 'featured';
$oikos_level   = min( 6, max( 2, (int) $oikos_args['heading_level'] ) );
$oikos_title   = get_the_title( $oikos_post_id );
$oikos_term    = oikos_post_category( $oikos_post_id );
$oikos_authors = wp_list_pluck( oikos_post_authors( $oikos_post_id ), 'name' );

if ( 'horizontal' === $oikos_variant ) {
	$oikos_size  = 'oikos-square-sm';
	$oikos_sizes = '(min-width: 64rem) 200px, (min-width: 48rem) 160px, 112px';
} else {
	$oikos_size  = 'large';
	$oikos_sizes = '(min-width: 72rem) 624px, (min-width: 64rem) 56vw, calc(100vw - 2.5rem)';
}

if ( '' !== (string) $oikos_args['sizes'] ) {
	$oikos_sizes = (string) $oikos_args['sizes'];
}

$oikos_image = has_post_thumbnail( $oikos_post_id ) ? get_the_post_thumbnail(
	$oikos_post_id,
	$oikos_size,
	array(
		'class'    => 'oikos-post-card__img',
		'alt'      => '',
		'sizes'    => $oikos_sizes,
		'loading'  => 'eager' === $oikos_args['image_loading'] ? false : 'lazy',
		'decoding' => 'async',
	)
) : '';
?>
<article class="<?php echo esc_attr( 'oikos-post-card oikos-post-card--' . $oikos_variant ); ?>">
	<div class="oikos-post-card__media<?php echo '' === $oikos_image ? ' oikos-post-card__media--empty' : ''; ?>">
		<?php echo $oikos_image; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- HTML de WordPress. ?>
	</div>

	<div class="oikos-post-card__body">
		<?php
		if ( $oikos_term ) {
			get_template_part(
				'template-parts/components/tag',
				null,
				array(
					'label' => $oikos_term->name,
					'color' => oikos_category_color( $oikos_term->term_id ),
					'class' => 'oikos-post-card__tag',
				)
			);
		}

		printf(
			'<h%1$d class="oikos-post-card__title"><a class="oikos-post-card__link" href="%2$s" data-track="blog-card" data-track-location="%3$s">%4$s</a></h%1$d>',
			(int) $oikos_level,
			esc_url( get_permalink( $oikos_post_id ) ),
			esc_attr( (string) $oikos_args['track_location'] ),
			esc_html( $oikos_title )
		);
		?>

		<?php if ( $oikos_authors ) : ?>
			<p class="oikos-post-card__authors">
				<span class="screen-reader-text"><?php echo esc_html( _n( 'Autor:', 'Autores:', count( $oikos_authors ), 'oikos' ) ); ?></span>
				<?php
				$oikos_last = count( $oikos_authors ) - 1;
				foreach ( $oikos_authors as $oikos_i => $oikos_author ) {
					echo '<span>' . esc_html( $oikos_author ) . '</span>';
					if ( $oikos_i < $oikos_last ) {
						echo '<span class="oikos-post-card__sep" aria-hidden="true">·</span><span class="screen-reader-text">, </span>';
					}
				}
				?>
			</p>
		<?php endif; ?>
	</div>
</article>
