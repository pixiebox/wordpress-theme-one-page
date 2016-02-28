<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?php wp_title( '|', true, 'right' ); ?></title>

	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?> data-offset="50" data-spy="scroll" data-target="#sidebar.scroll-nav">

<?php bootstrap_nav_menu(); ?>

<?php if ( is_front_page() ) : ?>
	<div role="banner" class="header sm-panel" style="background-image: url(<?php echo get_custom_header()->url ?>); background-repeat: no-repeat;">
		<div class="info">
			<div class="head"><?php echo get_bloginfo( 'name' ); ?></div>
			<p><?php echo get_bloginfo( 'description' ); ?></p>
		</div>
	</div>

<?php endif; ?>