<!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js">
<head>
	<script>(function(H){H.className=H.className.replace(/\bno-js\b/,'js')})(document.documentElement)</script>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">

	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
	<?php wp_body_open(); ?>

	<?php Posty\render( 'components/skip-link' ); ?>
	<?php Posty\render( 'components/header' ); ?>

	<div class="wrapper">
