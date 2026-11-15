<?php
/**
 * Componente: etiqueta de categoría (píldora de color).
 *
 * Es texto, no enlace: dentro de las tarjetas de entrada el único enlace es
 * el título.
 *
 * Args:
 * - label (string) Texto de la etiqueta (obligatorio).
 * - color (string) 'crema' | 'arena' | 'sage' (ver oikos_tag_colors()).
 * - class (string) Clases extra.
 *
 * @package oikos
 */

defined( 'ABSPATH' ) || exit;

$oikos_args = wp_parse_args(
	isset( $args ) && is_array( $args ) ? $args : array(),
	array(
		'label' => '',
		'color' => 'crema',
		'class' => '',
	)
);

$oikos_label = trim( (string) $oikos_args['label'] );

if ( '' === $oikos_label ) {
	return;
}

$oikos_color = in_array( $oikos_args['color'], oikos_tag_colors(), true ) ? $oikos_args['color'] : 'crema';
?>
<span class="<?php echo esc_attr( trim( 'oikos-tag oikos-tag--' . $oikos_color . ' ' . $oikos_args['class'] ) ); ?>"><?php echo esc_html( $oikos_label ); ?></span>
