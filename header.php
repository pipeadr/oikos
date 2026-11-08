<?php
/**
 * Cabecera del sitio.
 *
 * @package oikos
 */

defined( 'ABSPATH' ) || exit;
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a class="oikos-skip-link" href="#main"><?php esc_html_e( 'Saltar al contenido', 'oikos' ); ?></a>

<header class="oikos-header">
	<div class="oikos-header__brand">
		<?php if ( has_custom_logo() ) : ?>
			<?php the_custom_logo(); ?>
		<?php else : ?>
			<a class="oikos-header__site-name" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
				<?php bloginfo( 'name' ); ?>
			</a>
		<?php endif; ?>
	</div>

	<?php if ( has_nav_menu( 'primary' ) ) : ?>
		<nav class="oikos-header__nav" aria-label="<?php esc_attr_e( 'Menú principal', 'oikos' ); ?>">
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'primary',
					'container'      => false,
					'menu_class'     => 'oikos-header__menu',
					'fallback_cb'    => false,
				)
			);
			?>
		</nav>
	<?php endif; ?>
</header>

<main id="main" class="oikos-main">
