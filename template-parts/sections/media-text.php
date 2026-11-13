<?php
/**
 * Sección: foto + texto (reutilizable en Inicio, Nosotros, Servicios y El método).
 *
 * No lee ACF: la plantilla le pasa los datos por $args.
 *
 * Args:
 * - image_id       (int)    Foto (tamaño oikos-square, recorte cuadrado). No es LCP: va con lazy.
 * - image_position (string) 'left' | 'right'. En móvil la foto siempre va arriba.
 * - background     (string) 'white' | 'cream'.
 * - eyebrow        (string) Antetítulo opcional.
 * - title          (string) Título (obligatorio: sin título no se imprime nada).
 * - heading_level  (int)    2–4. El estilo siempre es Heading.
 * - text           (string) HTML simple (párrafos, br, strong, em, enlaces).
 * - link           (array)  url, title, target. Opcional.
 * - button_variant (string) 'primary' | 'secondary'.
 * - track          (string) data-track del botón.
 * - track_location (string) data-track-location del botón.
 *
 * @package oikos
 */

defined( 'ABSPATH' ) || exit;

$oikos_args = wp_parse_args(
	isset( $args ) && is_array( $args ) ? $args : array(),
	array(
		'image_id'       => 0,
		'image_position' => 'left',
		'background'     => 'white',
		'eyebrow'        => '',
		'title'          => '',
		'heading_level'  => 2,
		'text'           => '',
		'link'           => array(),
		'button_variant' => 'secondary',
		'track'          => '',
		'track_location' => '',
	)
);

$oikos_title = trim( (string) $oikos_args['title'] );

if ( '' === $oikos_title ) {
	return;
}

$oikos_position = 'right' === $oikos_args['image_position'] ? 'right' : 'left';
$oikos_bg       = 'cream' === $oikos_args['background'] ? 'cream' : 'white';
$oikos_variant  = 'primary' === $oikos_args['button_variant'] ? 'primary' : 'secondary';
$oikos_level    = min( 4, max( 2, (int) $oikos_args['heading_level'] ) );
$oikos_link     = is_array( $oikos_args['link'] ) ? $oikos_args['link'] : array();
$oikos_title_id = wp_unique_id( 'oikos-media-text-' );

$oikos_image = absint( $oikos_args['image_id'] ) ? wp_get_attachment_image(
	absint( $oikos_args['image_id'] ),
	'oikos-square',
	false,
	array(
		'class'    => 'oikos-media-text__img',
		'loading'  => 'lazy',
		'decoding' => 'async',
		// Columna: ancho del contenedor − márgenes (móvil) · ~38 % (tablet) · 523 px (desktop).
		'sizes'    => '(max-width: 47.9375rem) calc(100vw - 2.5rem), (max-width: 64rem) 38vw, 33rem',
	)
) : '';

$oikos_classes = sprintf(
	'oikos-section oikos-media-text oikos-media-text--image-%1$s oikos-media-text--bg-%2$s%3$s',
	$oikos_position,
	$oikos_bg,
	$oikos_image ? '' : ' oikos-media-text--no-image'
);
?>
<section class="<?php echo esc_attr( $oikos_classes ); ?>" aria-labelledby="<?php echo esc_attr( $oikos_title_id ); ?>">
	<div class="oikos-container oikos-media-text__inner">
		<?php if ( $oikos_image ) : ?>
			<figure class="oikos-media-text__media">
				<?php echo $oikos_image; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_get_attachment_image() escapa. ?>
			</figure>
		<?php endif; ?>

		<div class="oikos-media-text__content">
			<div class="oikos-media-text__heading">
				<?php if ( '' !== trim( (string) $oikos_args['eyebrow'] ) ) : ?>
					<p class="oikos-media-text__eyebrow oikos-text-eyebrow"><?php echo esc_html( $oikos_args['eyebrow'] ); ?></p>
				<?php endif; ?>

				<?php
				printf(
					'<h%1$d id="%2$s" class="oikos-media-text__title oikos-text-heading">%3$s</h%1$d>',
					(int) $oikos_level,
					esc_attr( $oikos_title_id ),
					esc_html( $oikos_title )
				);
				?>
			</div>

			<?php if ( '' !== trim( wp_strip_all_tags( (string) $oikos_args['text'] ) ) ) : ?>
				<div class="oikos-media-text__text oikos-text-body oikos-text-secondary">
					<?php
					echo wp_kses(
						$oikos_args['text'],
						array(
							'p'      => array(),
							'br'     => array(),
							'strong' => array(),
							'em'     => array(),
							'a'      => array(
								'href'   => true,
								'target' => true,
								'rel'    => true,
							),
						)
					);
					?>
				</div>
			<?php endif; ?>

			<?php if ( ! empty( $oikos_link['url'] ) && ! empty( $oikos_link['title'] ) ) : ?>
				<a
					class="<?php echo esc_attr( 'oikos-btn oikos-btn--' . $oikos_variant . ' oikos-btn--block-mobile oikos-media-text__cta' ); ?>"
					href="<?php echo esc_url( $oikos_link['url'] ); ?>"
					<?php echo ( isset( $oikos_link['target'] ) && '_blank' === $oikos_link['target'] ) ? 'target="_blank" rel="noopener"' : ''; ?>
					<?php if ( '' !== $oikos_args['track'] ) : ?>
						data-track="<?php echo esc_attr( $oikos_args['track'] ); ?>"
						data-track-location="<?php echo esc_attr( $oikos_args['track_location'] ); ?>"
					<?php endif; ?>
				><?php echo esc_html( $oikos_link['title'] ); ?></a>
			<?php endif; ?>
		</div>
	</div>
</section>
