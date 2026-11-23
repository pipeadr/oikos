<?php
/**
 * Componente: badges (sellos de confianza en tarjetas arena).
 *
 * Hasta 3 frases cortas ("Psicólogos especializados…", "100% confidencial…",
 * "Presencial en Medellín…") en fila centrada; apiladas en móvil. Es una
 * lista sin encabezado propio: va dentro de otra sección (en la plantilla
 * Servicio, justo debajo de los pasos).
 *
 * No lee ACF: la plantilla le pasa los textos, por lo general con
 * oikos_get_badges( $page_id ) (los de la página o, si no hay, los globales).
 *
 * Args:
 * - items (string[]) Textos (texto plano). Sin textos no se imprime nada.
 * - class (string)   Clases extra del contenedor.
 *
 * @package oikos
 */

defined( 'ABSPATH' ) || exit;

$oikos_args = wp_parse_args(
	isset( $args ) && is_array( $args ) ? $args : array(),
	array(
		'items' => array(),
		'class' => '',
	)
);

$oikos_items = array_values( array_filter( array_map( 'trim', array_map( 'strval', (array) $oikos_args['items'] ) ), 'strlen' ) );

if ( ! $oikos_items ) {
	return;
}

$oikos_classes = trim( 'oikos-badges oikos-badges--count-' . count( $oikos_items ) . ' ' . $oikos_args['class'] );
?>
<div class="<?php echo esc_attr( $oikos_classes ); ?>">
	<ul class="oikos-badges__list">
		<?php foreach ( $oikos_items as $oikos_item ) : ?>
			<li class="oikos-badges__item"><?php echo esc_html( $oikos_item ); ?></li>
		<?php endforeach; ?>
	</ul>
</div>
