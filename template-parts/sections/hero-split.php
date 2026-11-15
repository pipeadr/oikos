<?php
/**
 * Sección: hero foto + texto (variante "split") para páginas internas.
 *
 * Foto cuadrada a la izquierda y texto a la derecha, centrado en vertical.
 * El header no flota sobre la foto: la página usa el padding del <main>.
 *
 * No lee ACF: la plantilla le pasa los datos por $args.
 *
 * Args:
 * - image_id       (int)    Foto (LCP: sin lazy, fetchpriority="high").
 * - image_position (string) 'left' (único valor por ahora).
 * - title          (string) Texto del H1 (la plantilla aplica el respaldo al título de la página).
 * - text           (string) Párrafo (admite <br>).
 * - link           (array)  url, title, target.
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
		'title'          => '',
		'text'           => '',
		'link'           => array(),
		'track_location' => 'hero',
	)
);

$oikos_position = in_array( $oikos_args['image_position'], array( 'left' ), true ) ? $oikos_args['image_position'] : 'left';
$oikos_link     = is_array( $oikos_args['link'] ) ? $oikos_args['link'] : array();
$oikos_picture  = oikos_hero_picture(
	$oikos_args['image_id'],
	0,
	'oikos-hero-split__media',
	array(
		'size'  => 'oikos-square',
		// Columna: contenedor − márgenes (móvil) · 2/5 del contenedor (tablet) ·
		// contenedor − texto 411 − gap, máx. 643 (desktop).
		'sizes' => '(max-width: 47.9375rem) calc(100vw - 2.5rem), (max-width: 63.9375rem) 36vw, min(40.25rem, calc(100vw - 36rem))',
	)
);

$oikos_classes = 'oikos-hero-split oikos-hero-split--image-' . $oikos_position . ( $oikos_picture ? '' : ' oikos-hero-split--no-media' );
?>
<section class="<?php echo esc_attr( $oikos_classes ); ?>" aria-labelledby="oikos-hero-title">
	<div class="oikos-container oikos-hero-split__inner">
		<?php echo $oikos_picture; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escapado en oikos_hero_picture() / wp_get_attachment_image(). ?>

		<div class="oikos-hero-split__content">
			<h1 id="oikos-hero-title" class="oikos-hero-split__title oikos-text-display-sm"><?php echo esc_html( $oikos_args['title'] ); ?></h1>

			<?php if ( '' !== trim( (string) $oikos_args['text'] ) ) : ?>
				<p class="oikos-hero-split__text oikos-text-body oikos-text-secondary"><?php echo wp_kses( $oikos_args['text'], array( 'br' => array() ) ); ?></p>
			<?php endif; ?>

			<?php if ( ! empty( $oikos_link['url'] ) && ! empty( $oikos_link['title'] ) ) : ?>
				<a
					class="oikos-btn oikos-btn--primary oikos-btn--block-mobile oikos-hero-split__cta"
					href="<?php echo esc_url( $oikos_link['url'] ); ?>"
					<?php echo ( isset( $oikos_link['target'] ) && '_blank' === $oikos_link['target'] ) ? 'target="_blank" rel="noopener"' : ''; ?>
					data-track="agenda-cita"
					data-track-location="<?php echo esc_attr( $oikos_args['track_location'] ); ?>"
				><?php echo esc_html( $oikos_link['title'] ); ?></a>
			<?php endif; ?>
		</div>
	</div>
</section>
