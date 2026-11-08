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

<?php get_template_part( 'template-parts/layout/site-header' ); ?>

<main id="main" class="oikos-main">
