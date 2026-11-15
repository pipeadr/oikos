<?php
/**
 * Componente: acordeón (patrón Accordion de WAI-ARIA).
 *
 * Cada pregunta es un encabezado con un <button aria-expanded aria-controls>
 * dentro: se puede navegar por encabezados y la respuesta siempre está en el
 * HTML (SEO). Se pueden abrir varios ítems a la vez; todos empiezan cerrados.
 * El JS (assets/js/accordion.js) solo alterna aria-expanded; el CSS anima a
 * partir de ese atributo. Sin JS (html.no-js) las respuestas se ven abiertas.
 *
 * Args:
 * - items         (array)  Lista de array{question:string, answer:string}.
 *                          answer = HTML del editor (se filtra con wp_kses_post).
 * - heading_level (int)    2–6. Nivel del encabezado de cada pregunta.
 * - id_prefix     (string) Prefijo de los id (se genera uno único si falta).
 * - label         (string) Nombre accesible opcional del grupo.
 *
 * @package oikos
 */

defined( 'ABSPATH' ) || exit;

$oikos_args = wp_parse_args(
	isset( $args ) && is_array( $args ) ? $args : array(),
	array(
		'items'         => array(),
		'heading_level' => 3,
		'id_prefix'     => '',
		'label'         => '',
	)
);

$oikos_items = array();
foreach ( (array) $oikos_args['items'] as $oikos_item ) {
	$oikos_question = isset( $oikos_item['question'] ) ? trim( (string) $oikos_item['question'] ) : '';
	$oikos_answer   = isset( $oikos_item['answer'] ) ? trim( (string) $oikos_item['answer'] ) : '';

	if ( '' !== $oikos_question && '' !== $oikos_answer ) {
		$oikos_items[] = array(
			'question' => $oikos_question,
			'answer'   => $oikos_answer,
		);
	}
}

if ( ! $oikos_items ) {
	return;
}

$oikos_level  = min( 6, max( 2, (int) $oikos_args['heading_level'] ) );
$oikos_prefix = '' !== $oikos_args['id_prefix'] ? sanitize_html_class( $oikos_args['id_prefix'] ) : wp_unique_id( 'oikos-accordion-' );
?>
<div class="oikos-accordion" data-oikos-accordion<?php echo '' !== $oikos_args['label'] ? ' role="group" aria-label="' . esc_attr( $oikos_args['label'] ) . '"' : ''; ?>>
	<?php foreach ( $oikos_items as $oikos_index => $oikos_item ) : ?>
		<?php
		$oikos_q_id = $oikos_prefix . '-q' . ( $oikos_index + 1 );
		$oikos_a_id = $oikos_prefix . '-a' . ( $oikos_index + 1 );
		?>
		<div class="oikos-accordion__item">
			<h<?php echo (int) $oikos_level; ?> class="oikos-accordion__heading">
				<button
					type="button"
					class="oikos-accordion__trigger"
					id="<?php echo esc_attr( $oikos_q_id ); ?>"
					aria-expanded="false"
					aria-controls="<?php echo esc_attr( $oikos_a_id ); ?>"
				>
					<span class="oikos-accordion__question"><?php echo esc_html( $oikos_item['question'] ); ?></span>
					<?php oikos_the_icon( 'chevron-down', 'oikos-accordion__icon' ); ?>
				</button>
			</h<?php echo (int) $oikos_level; ?>>
			<div class="oikos-accordion__panel" id="<?php echo esc_attr( $oikos_a_id ); ?>" role="region" aria-labelledby="<?php echo esc_attr( $oikos_q_id ); ?>">
				<div class="oikos-accordion__panel-inner">
					<div class="oikos-accordion__answer oikos-text-body">
						<?php echo wp_kses_post( $oikos_item['answer'] ); ?>
					</div>
				</div>
			</div>
		</div>
	<?php endforeach; ?>
</div>
