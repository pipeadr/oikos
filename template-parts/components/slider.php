<?php
/**
 * Componente: slider (carrusel con CSS scroll-snap).
 *
 * Reutilizable: fotos del Inicio, carrusel de servicios, testimonios.
 * No lee ACF. Requiere los assets 'oikos-slider' (css/slider.css + js/slider.js)
 * en la plantilla que lo use (ver oikos_template_assets()).
 *
 * Sin JS sigue siendo un carrusel deslizable (táctil, trackpad, Shift + rueda).
 *
 * Args:
 * - slides     (string[]) HTML de cada diapositiva, YA escapado por quien llama.
 * - label      (string)   Nombre accesible del carrusel (obligatorio para lectores de pantalla).
 * - item_label (string)   Qué es cada diapositiva, para los indicadores: "Ir a la {foto} 2 de 3".
 * - arrows     (bool)     Mostrar flechas anterior/siguiente. Default false.
 * - class      (string)   Clases extra del contenedor (modificadores, p. ej. 'oikos-slider--portrait').
 *                         Modificadores: --portrait (fotos), --cards (varias visibles),
 *                         --dots-below (indicadores debajo), --on-light (fondo claro).
 *                         Ver css/slider.css.
 *
 * @package oikos
 */

defined( 'ABSPATH' ) || exit;

$oikos_args = wp_parse_args(
	isset( $args ) && is_array( $args ) ? $args : array(),
	array(
		'slides'     => array(),
		'label'      => '',
		'item_label' => __( 'foto', 'oikos' ),
		'arrows'     => false,
		'class'      => '',
	)
);

$oikos_slides = array_values( array_filter( array_map( 'strval', (array) $oikos_args['slides'] ), static fn( $html ) => '' !== trim( $html ) ) );
$oikos_total  = count( $oikos_slides );

if ( ! $oikos_total ) {
	return;
}

$oikos_multi   = $oikos_total > 1;
$oikos_classes = trim( 'oikos-slider ' . $oikos_args['class'] );
?>
<div
	class="<?php echo esc_attr( $oikos_classes ); ?>"
	<?php if ( $oikos_multi ) : ?>
		data-oikos-slider
		role="region"
		aria-roledescription="<?php esc_attr_e( 'carrusel', 'oikos' ); ?>"
	<?php endif; ?>
	<?php if ( '' !== $oikos_args['label'] ) : ?>
		aria-label="<?php echo esc_attr( $oikos_args['label'] ); ?>"
	<?php endif; ?>
>
	<div class="oikos-slider__track"<?php echo $oikos_multi ? ' tabindex="0"' : ''; ?>>
		<?php foreach ( $oikos_slides as $oikos_i => $oikos_slide ) : ?>
			<div
				class="oikos-slider__slide"
				<?php if ( $oikos_multi ) : ?>
					role="group"
					aria-roledescription="<?php esc_attr_e( 'diapositiva', 'oikos' ); ?>"
					aria-label="<?php echo esc_attr( sprintf( /* translators: 1: número de diapositiva, 2: total. */ __( '%1$d de %2$d', 'oikos' ), $oikos_i + 1, $oikos_total ) ); ?>"
				<?php endif; ?>
			>
				<?php echo $oikos_slide; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Quien llama entrega el HTML escapado. ?>
			</div>
		<?php endforeach; ?>
	</div>

	<?php if ( $oikos_multi && $oikos_args['arrows'] ) : ?>
		<button type="button" class="oikos-slider__arrow oikos-slider__arrow--prev" data-slider-prev aria-label="<?php esc_attr_e( 'Anterior', 'oikos' ); ?>">
			<?php oikos_the_icon( 'arrow-right' ); // Volteada por CSS. ?>
		</button>
		<button type="button" class="oikos-slider__arrow oikos-slider__arrow--next" data-slider-next aria-label="<?php esc_attr_e( 'Siguiente', 'oikos' ); ?>">
			<?php oikos_the_icon( 'arrow-right' ); ?>
		</button>
	<?php endif; ?>

	<?php if ( $oikos_multi ) : ?>
		<div class="oikos-slider__dots">
			<?php for ( $oikos_i = 0; $oikos_i < $oikos_total; $oikos_i++ ) : ?>
				<button
					type="button"
					class="oikos-slider__dot"
					data-slider-dot="<?php echo (int) $oikos_i; ?>"
					aria-label="<?php echo esc_attr( sprintf( /* translators: 1: tipo de elemento (foto, testimonio…), 2: número, 3: total. */ __( 'Ir a la %1$s %2$d de %3$d', 'oikos' ), $oikos_args['item_label'], $oikos_i + 1, $oikos_total ) ); ?>"
					<?php echo 0 === $oikos_i ? 'aria-current="true"' : ''; ?>
				></button>
			<?php endfor; ?>
		</div>
	<?php endif; ?>
</div>
