<?php
/**
 * Sección: preguntas frecuentes (todas las páginas, preguntas distintas en cada una).
 *
 * Título + botón a la izquierda, acordeón a la derecha y, debajo, el bloque
 * "¿Tienes dudas?" con WhatsApp. No lee ACF: las plantillas la llaman con
 * oikos_the_faq_section( 'faq-<pagina>' ) (inc/faq.php), que le pasa los datos.
 * El schema FAQPage lo agrega inc/schema.php con las mismas preguntas.
 *
 * Args:
 * - title          (string) Título (estilo Heading).
 * - heading_level  (int)    2–4. Las preguntas van un nivel más abajo.
 * - items          (array)  array{question:string, answer:string}.
 * - link           (array)  url, title, target (ya resuelto con oikos_link_or_cta()).
 * - track_location (string) "faq-<pagina>" para el botón y WhatsApp.
 *
 * @package oikos
 */

defined( 'ABSPATH' ) || exit;

$oikos_args = wp_parse_args(
	isset( $args ) && is_array( $args ) ? $args : array(),
	array(
		'title'          => '',
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
					'<h%1$d id="%2$s" class="oikos-faq__title oikos-text-heading">%3$s</h%1$d>',
					(int) $oikos_level,
					esc_attr( $oikos_title_id ),
					esc_html( $oikos_title )
				);
				?>

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

			<div class="oikos-faq__accordion">
				<?php
				get_template_part(
					'template-parts/components/accordion',
					null,
					array(
						'items'         => $oikos_args['items'],
						'heading_level' => $oikos_level + 1,
						'id_prefix'     => $oikos_title_id,
					)
				);
				?>
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
