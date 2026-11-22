<?php
/**
 * Sección: preguntas frecuentes (todas las páginas, preguntas distintas en cada una).
 *
 * Título + botón a la izquierda, preguntas a la derecha y, debajo, el bloque
 * "¿Tienes dudas?" con WhatsApp. No lee ACF: las plantillas la preparan con
 * oikos_prepare_faq_section( 'faq-<pagina>' ) antes de get_header() y la
 * imprimen con oikos_the_faq_section() (inc/faq.php). El schema FAQPage
 * (inc/schema.php) solo sale si esta plantilla produjo HTML.
 *
 * Cada pregunta es <details>/<summary> nativo con el encabezado (h3) dentro
 * del <summary>: funciona sin JS, todas empiezan cerradas, se pueden abrir
 * varias y la respuesta siempre está en el HTML. assets/js/faq.js solo agrega
 * la animación de alto (mejora progresiva).
 *
 * Args:
 * - title          (string) Título (estilo Heading). Admite saltos de línea
 *                           (visibles desde 768).
 * - subtitle       (string) Texto opcional bajo el título (Body text/secondary).
 * - heading_level  (int)    2–4. Las preguntas van un nivel más abajo.
 * - items          (array)  array{question:string, answer:string}.
 * - link           (array)  url, title, target. Vacío → sin botón (el respaldo
 *                           al CTA global lo decide oikos_prepare_faq_section()).
 * - track_location (string) "faq-<pagina>" para el botón y WhatsApp.
 *
 * @package oikos
 */

defined( 'ABSPATH' ) || exit;

$oikos_args = wp_parse_args(
	isset( $args ) && is_array( $args ) ? $args : array(),
	array(
		'title'          => '',
		'subtitle'       => '',
		'heading_level'  => 2,
		'items'          => array(),
		'link'           => array(),
		'track_location' => '',
	)
);

if ( empty( $oikos_args['items'] ) ) {
	return;
}

$oikos_title    = trim( (string) $oikos_args['title'] );
$oikos_title    = '' !== $oikos_title ? $oikos_title : __( 'Preguntas frecuentes', 'oikos' );
$oikos_level    = min( 4, max( 2, (int) $oikos_args['heading_level'] ) );
$oikos_link     = is_array( $oikos_args['link'] ) ? $oikos_args['link'] : array();
$oikos_title_id = wp_unique_id( 'oikos-faq-' );
?>
<section class="oikos-section oikos-faq" aria-labelledby="<?php echo esc_attr( $oikos_title_id ); ?>">
	<div class="oikos-container">
		<div class="oikos-faq__inner">
			<div class="oikos-faq__intro">
				<?php
				printf(
					'<h%1$d id="%2$s" class="oikos-faq__heading oikos-text-heading">%3$s</h%1$d>',
					(int) $oikos_level,
					esc_attr( $oikos_title_id ),
					oikos_title_lines_html( $oikos_title, 'oikos-faq__break' ) // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escapa línea por línea.
				);
				?>

				<?php if ( '' !== trim( (string) $oikos_args['subtitle'] ) ) : ?>
					<p class="oikos-faq__subtitle oikos-text-body oikos-text-secondary"><?php echo esc_html( trim( (string) $oikos_args['subtitle'] ) ); ?></p>
				<?php endif; ?>

				<?php if ( ! empty( $oikos_link['url'] ) && ! empty( $oikos_link['title'] ) ) : ?>
					<a
						class="oikos-btn oikos-btn--primary oikos-btn--block-mobile oikos-faq__cta"
						href="<?php echo esc_url( $oikos_link['url'] ); ?>"
						<?php echo ( isset( $oikos_link['target'] ) && '_blank' === $oikos_link['target'] ) ? 'target="_blank" rel="noopener"' : ''; ?>
						data-track="agenda-cita"
						data-track-location="<?php echo esc_attr( $oikos_args['track_location'] ); ?>"
					><?php echo esc_html( $oikos_link['title'] ); ?></a>
				<?php endif; ?>
			</div>

			<div class="oikos-faq__list">
				<?php foreach ( $oikos_args['items'] as $oikos_item ) : ?>
					<details class="oikos-faq__item">
						<summary class="oikos-faq__question">
							<?php
							printf(
								'<h%1$d class="oikos-faq__title">%2$s</h%1$d>',
								(int) $oikos_level + 1,
								esc_html( $oikos_item['question'] )
							);
							?>
							<span class="oikos-faq__chevron" aria-hidden="true"><?php oikos_the_icon( 'chevron-down' ); ?></span>
						</summary>
						<div class="oikos-faq__answer oikos-text-body">
							<?php echo wp_kses_post( $oikos_item['answer'] ); ?>
						</div>
					</details>
				<?php endforeach; ?>
			</div>
		</div>

		<?php
		get_template_part(
			'template-parts/components/whatsapp-cta',
			null,
			array(
				'track_location' => $oikos_args['track_location'],
				'class'          => 'oikos-faq__whatsapp',
			)
		);
		?>
	</div>
</section>
