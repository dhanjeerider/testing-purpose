<!DOCTYPE html>
<html <?php language_attributes(); ?> class="scroll-smooth">
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="profile" href="https://gmpg.org/xfn/11">
<?php wp_head(); ?>
</head>
<body <?php body_class( 'bg-gray-50 dark:bg-gray-950 text-gray-900 dark:text-gray-100 font-body antialiased min-h-screen' ); ?>>
<?php wp_body_open(); ?>
<div id="aspv5-scroll-bar" aria-hidden="true"></div>
<a class="sr-only focus:not-sr-only focus:absolute focus:top-4 focus:left-4 focus:z-50 focus:bg-primary focus:text-white focus:px-4 focus:py-2 focus:rounded-lg" href="#main">
	<?php esc_html_e( 'Skip to content', 'aspv5' ); ?>
</a>
<?php get_template_part( 'template-parts/global/site-header' ); ?>
