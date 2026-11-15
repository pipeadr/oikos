<?php
/**
 * Banda newsletter (gradiente radial verde): logo y lema a la izquierda;
 * título, descripción y formulario de Contact Form 7 a la derecha.
 *
 * El formulario se elige en "Oikos — Global" → Newsletter. Sin formulario
 * (o sin CF7 activo) la banda muestra solo los textos.
 *
 * @package oikos
 */

defined( 'ABSPATH' ) || exit;

$oikos_nl_title   = (string) oikos_option( 'oikos_global_newsletter_titulo', __( 'Un acompañamiento que llega a tu correo', 'oikos' ) );
$oikos_nl_text    = (string) oikos_option( 'oikos_global_newsletter_texto' );
$oikos_nl_form_id = oikos_newsletter_form_id();
$oikos_tagline    = (string) oikos_option( 'oikos_global_footer_lema', __( 'Sana la raíz, transforma el vínculo', 'oikos' ) );
?>
<section class="oikos-newsletter" aria-labelledby="oikos-newsletter-title">
	<div class="oikos-container oikos-newsletter__inner">
		<div class="oikos-newsletter__brand">
			<?php get_template_part( 'template-parts/components/logo', null, array( 'variant' => 'inverse' ) ); ?>
			<?php if ( '' !== $oikos_tagline ) : ?>
				<p class="oikos-newsletter__tagline"><?php echo esc_html( $oikos_tagline ); ?></p>
			<?php endif; ?>
		</div>

		<div class="oikos-newsletter__content">
			<h2 class="oikos-text-heading oikos-newsletter__title" id="oikos-newsletter-title"><?php echo esc_html( $oikos_nl_title ); ?></h2>

			<?php if ( '' !== $oikos_nl_text ) : ?>
				<p class="oikos-newsletter__text"><?php echo wp_kses( $oikos_nl_text, array( 'br' => array() ) ); ?></p>
			<?php endif; ?>

			<?php if ( $oikos_nl_form_id > 0 && class_exists( 'WPCF7' ) ) : ?>
				<div class="oikos-newsletter__form">
					<?php
					echo do_shortcode( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- markup de CF7.
						sprintf(
							'[contact-form-7 id="%d" html_class="oikos-form oikos-form--inverse oikos-form--newsletter" html_title="%s"]',
							$oikos_nl_form_id,
							esc_attr( str_replace( array( '"', '[', ']' ), '', $oikos_nl_title ) ) // aria-label del <form>.
						)
					);
					?>
				</div>
			<?php endif; ?>
		</div>
	</div>
</section>
