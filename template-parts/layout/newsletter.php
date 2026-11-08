<?php
/**
 * Banda newsletter (verde con gradiente): logo, lema y formulario.
 *
 * La plataforma de newsletter está pendiente. Para conectarla:
 * - Llenar "URL de envío del formulario" en "Oikos — Global", o usar el
 *   filtro 'oikos_newsletter_form_action'.
 * - Si la plataforma pide otro nombre para el campo de correo, usar el filtro
 *   'oikos_newsletter_email_name' (por defecto 'email').
 * - Campos ocultos extra (listas, tokens): acción 'oikos_newsletter_form_fields'.
 * Mientras no haya URL, main.js evita el envío y muestra el mensaje pendiente.
 *
 * @package oikos
 */

defined( 'ABSPATH' ) || exit;

$oikos_nl_title       = (string) oikos_option( 'oikos_global_newsletter_titulo', __( 'Recibe ideas para tus relaciones', 'oikos' ) );
$oikos_nl_text        = (string) oikos_option( 'oikos_global_newsletter_texto', __( 'Contenido práctico sobre pareja, familia y bienestar emocional, directo a tu correo.', 'oikos' ) );
$oikos_nl_placeholder = (string) oikos_option( 'oikos_global_newsletter_placeholder', __( 'Tu correo electrónico', 'oikos' ) );
$oikos_nl_button      = (string) oikos_option( 'oikos_global_newsletter_boton', __( 'Suscribirme', 'oikos' ) );
$oikos_nl_pending     = (string) oikos_option( 'oikos_global_newsletter_mensaje_pendiente', __( 'Muy pronto podrás suscribirte. ¡Gracias por tu interés!', 'oikos' ) );
$oikos_nl_action      = (string) apply_filters( 'oikos_newsletter_form_action', (string) oikos_option( 'oikos_global_newsletter_action' ) );
$oikos_nl_email_name  = (string) apply_filters( 'oikos_newsletter_email_name', 'email' );
$oikos_tagline        = (string) oikos_option( 'oikos_global_footer_lema', __( 'Sana la raíz, transforma el vínculo', 'oikos' ) );
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
			<h2 class="oikos-newsletter__title" id="oikos-newsletter-title"><?php echo esc_html( $oikos_nl_title ); ?></h2>

			<?php if ( '' !== $oikos_nl_text ) : ?>
				<p class="oikos-newsletter__text"><?php echo wp_kses( $oikos_nl_text, array( 'br' => array() ) ); ?></p>
			<?php endif; ?>

			<form
				class="oikos-newsletter__form"
				method="post"
				<?php if ( '' !== $oikos_nl_action ) : ?>
					action="<?php echo esc_url( $oikos_nl_action ); ?>"
				<?php else : ?>
					data-oikos-newsletter-pending="<?php echo esc_attr( $oikos_nl_pending ); ?>"
				<?php endif; ?>
				data-oikos-newsletter
			>
				<label class="screen-reader-text" for="oikos-newsletter-email"><?php esc_html_e( 'Correo electrónico', 'oikos' ); ?></label>
				<div class="oikos-newsletter__row">
					<input
						class="oikos-newsletter__input"
						type="email"
						id="oikos-newsletter-email"
						name="<?php echo esc_attr( $oikos_nl_email_name ); ?>"
						placeholder="<?php echo esc_attr( $oikos_nl_placeholder ); ?>"
						autocomplete="email"
						inputmode="email"
						required
					>
					<button class="oikos-btn oikos-btn--light oikos-newsletter__submit" type="submit" data-track="suscripcion" data-track-location="footer-newsletter">
						<?php echo esc_html( $oikos_nl_button ); ?>
					</button>
				</div>
				<?php do_action( 'oikos_newsletter_form_fields' ); ?>
				<p class="oikos-newsletter__status" role="status" aria-live="polite"></p>
			</form>
		</div>
	</div>
</section>
