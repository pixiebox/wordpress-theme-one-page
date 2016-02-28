<?php /* Template Name: OnePage Template */ ?>
<?php get_header(); ?>

<div role="main">
<?php
echo frontpage_sticky_posts();

$pin_container_counter = 0;
$close_pin_on_next_default = false;
$scroll_nav = '';
$orientation_nav = '<div class="orientation-nav">';

if ( have_posts() ) while ( have_posts() ) : the_post();
?>
<h1><?php the_title(); ?></h1>
<?php the_content(); ?>
<?php $args = array(
	'orderby' 			=> 'date',
	'order'   			=> 'ASC',
  'post_parent '  => get_the_ID()
  
	//'category_name'	=> 'one-page',
);

// The Query
$onepage_query = new WP_Query( $args );

// The Loop
if ( $onepage_query->have_posts() ) :
	while ( $onepage_query->have_posts() ) : $onepage_query->the_post();
		$id = str_replace('-', '_', basename(get_permalink()));
		$orientation = get_post_meta( get_the_id(), 'orientation', false )[0];
		$next_post = get_next_post();
		$next_orientation = ( !empty($next_post) && is_a( $next_post , 'WP_Post' ) )
			? get_post_meta( $next_post->ID, 'orientation', false )[0]
			: 'default';
		$group_background_panel = get_post_meta( get_the_id(), 'background-panel-group' )[0][0];
		$group_background_parallax = get_post_meta( get_the_id(), 'background-parallax-group' )[0];
		$article_class = get_post_meta( get_the_id(), 'width', false )[0];
		$format = get_post_format( get_the_id() );
		$panel_class = 'sm-panel ' . $format;
		$parallax_class = 'cover';
		$page_content_class = $page_content_style = $panel_data = $panel_style = '';
		$parallax = array();

		if ( !empty($group_background_panel['backgroundColor']) )
			$panel_style = 'background-color: ' . $group_background_panel['backgroundColor'] . ';';


		if ( !empty($group_background_panel['backgroundImage']) ) :
			$panel_style .= 'background-image: url(\'' . wp_get_attachment_url( $group_background_panel['backgroundImage'], 'full' ) . '\');';
		endif;

		if ( !wp_is_mobile() ) :
			$i = 0;

			if ( !empty( $group_background_parallax ) ) :
				foreach ( $group_background_parallax as $background ) :
					$parallax[$i]['class'] = $parallax[$i]['data'] = $parallax[$i]['style'] = '';

					if ( !empty( $background['backgroundColor'] ) ) :
						$parallax[$i]['style'] .= 'background-color: ' . $background['backgroundColor'] . ';';
						$parallax[$i]['class'] .= 'bg-color';
					endif;

					if ( !empty( $background['backgroundImage'] ) ) :
						$parallax[$i]['class'] .= ' bg-image';

						if ( $background['srcBox'] == '0' ) :
							$parallax[$i]['style'] .= 'background-image: url(\'' . wp_get_attachment_url( $background['backgroundImage'], 'full' ) . '\');';
						else :
							$parallax[$i]['data'] = preg_replace(
								'/(https?:\/\/.+\/)(.*)(.jpg|.jpeg|.png|.gif)/',
								'data-breakpoint="$1" data-img="$2-{folder}$3"',
								wp_get_attachment_url( $background['backgroundImage'], 'full' ) 
							);
						endif;
					endif;

					if ( !empty( $background['textColor'] ) ) :
						$parallax[$i]['style'] .= 'color: ' . $background['textColor'] . ';';
					endif;

					$i++;
				endforeach;
			endif;
		endif;

		if ( $orientation == 'default' ) :
			/**
			 * create a container for customized direction of swipes
			 * http://janpaepke.github.io/ScrollMagic/examples/advanced/section_wipes_manual.html
			 */
			if ( $next_orientation != 'default' ) :
				$close_pin_on_next_default = true;
				$href_anchor = '#pin-container-' . $pin_container_counter;
	?>

	<div class="pin-container" id="pin-container-<?php echo $pin_container_counter; ?>">
	<?php
				$pin_container_counter++;
			else :
				$href_anchor = '#panel-' . $id;
			endif;

			$scroll_nav .= '<li><a href="' . $href_anchor . '" title="' . get_the_title() . '"><span class="screen-reader-text">' . get_the_title() . '</span>&nbsp;</a></li>';
		endif;

		if ( !empty( $panel_data) )
			$panel_class .= ' srcbox cover';

		if ( $orienation != 'default' )
			$panel_data .= ' data-direction="' . $orientation . '"';
	?>
		<div class="<?php echo $panel_class; ?>" id="panel-<?php echo $id; ?>" style="<?php echo $panel_style; ?>" <?php echo $panel_data; ?>>
		<?php
			foreach ( $parallax as $data ) :
				if ( !empty( $data['data'] ) ) $parallax_class .= ' srcbox';
		?>	 	 
			<div class="parallax <?php echo $parallax_class; ?>" id="parallax-<?php echo $id; ?>" <?php echo $data['data']; ?>>
				<div class="bg-image" style="<?php echo $data['style']; ?>"></div>
			</div>
		<?php	endforeach; ?>

		<div class="<?php echo $article_class; ?>">
			<div class="page-content clearfix" id="content_<?php echo $id; ?>">	 	 
				<div style="<?php echo $page_content_style; ?>">
				<?php
				if ( in_array( $format, array('gallery', 'quote') ) ) :
					get_template_part( 'format', $format );
				else:
					$layout_class = get_post_meta( get_the_id(), 'layout', false )[0];
					$the_title = get_the_title();
				?>
					<div class="<?php echo $layout_class; ?>">
						<?php if (!empty($the_title)) : ?>
						<h2><?php echo $the_title; ?></h2>
						<?php endif; ?>
						<div class="clearfix page-content">	 	 
							<?php if ( has_post_thumbnail() ) :?>
							<div class="col-xs-4">
								<?php the_post_thumbnail( 'medium' ); ?>
							</div>
							<?php endif; ?>
							<?php the_content(); ?>
						</div>
					</div>
					<?php endif; ?>
					</div>
				</div>
			</div>
		</div>
	<?php
		if ($close_pin_on_next_default) :
			if ( $next_orientation == 'default') :
				$close_pin_on_next_default = false;
	?>
	</div>
	<?php
			endif;
		endif;
	endwhile;
endif;

wp_reset_postdata();
?>
<?php endwhile; wp_reset_query(); ?>
</div>

<?php if ( $scroll_nav != '' ) : ?>
<div id="sidebar" role="complementary" class="scroll-nav">
	<div>
		<ul class="nav">
		<?php	echo $scroll_nav;	?>
		</ul>
	</div>
</div>
<?php endif; ?>

<?php get_footer(); ?>