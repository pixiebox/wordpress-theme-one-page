<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?php wp_title( '|', true, 'right' ); ?></title>

	<?php wp_head(); ?>
	
	<!--[if lte IE 8]>
	<script src="<?php echo esc_url( get_template_directory_uri() ); ?>/js/respond.js" type="text/javascript"></script>
	<script src="<?php echo esc_url( get_template_directory_uri() ); ?>/js/html5shiv.js" type="text/javascript"></script>
	<![endif]-->
</head>

<body <?php body_class(); ?> data-offset="50" data-spy="scroll" data-target="#sidebar.scroll-nav">

<?php bootstrap_nav_menu(); ?>

<?php

if ( is_front_page() ) :
	$custom_header = get_custom_header()->url;
	$path_parts = pathinfo( $custom_header );
	$data_img = preg_replace(
		'/(.+\\-)(([0-9]+)x([0-9]+)-)?x([0-9]+)(.jpg|.jpeg|.png|.gif)/',
		'$1-{folder}$3',
		$path_parts['basename']
	);

?>

	<div role="banner" class="header sm-panel srcbox-header" data-breakpoint="<?php echo $path_parts ["dirname"] . '/'; ?>" data-img="<?php echo $data_img; ?>">
		<div class="info">
			<div class="head"><?php echo get_bloginfo( 'name' ); ?></div>
			<p><?php echo get_bloginfo( 'description' ); ?></p>
		</div>
	</div>

<?php endif; ?>