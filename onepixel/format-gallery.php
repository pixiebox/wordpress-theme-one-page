<?php
$gallery = get_post_gallery( get_the_ID(), false );
$gallery_ids = explode(',', $gallery['ids']);
$width_class = get_post_meta( get_the_id(), 'width', false )[0];
$layout_class = get_post_meta( get_the_id(), 'layout', false )[0];
$class_name = 'item ' . $layout_class;
$masonry = get_post_meta( get_the_id(), 'masonry', false )[0];
$the_title = get_the_title();

if (!empty($the_title)) :
?>
<h2><?php echo $the_title; ?></h2>
<?php
endif;

if ($masonry == '1') :
?>
<div class="grid portfolio">
<?php else : ?>
<div class="clearfix <?php echo $width_class; ?>">
<?php
endif;

$srcbox = get_post_meta( get_the_id(), 'srcBox', false )[0];
$i = 0;

foreach( $gallery['src'] as $src ) :
	$thumb_img = get_post( $gallery_ids[$i] ); // Get post by ID
	$i++;
?>
	<div class="<?php echo $class_name; ?>">
		<a href="<?php echo get_permalink( $thumb_img->ID ); ?>" rel="bookmark" title="<?php echo $thumb_img->post_title; ?>">
		<?php
			if ($srcbox == '1') :
				$data = preg_replace(
					'/(https?:\\/\\/.+\\/)(.+\\-)([0-9]+)(.jpg|.jpeg|.png|.gif)/',
					'data-breakpoint="$1" data-img="$2{folder}$4"',
					$src 
				);
			?>
			<img alt="<?php echo $thumb_img->post_title; ?>" class="srcbox-portfolio lag" src="<?php echo get_template_directory_uri() ?>/images/dot.gif" <?php echo $data; ?> />
		<?php else : ?>
			<img src="<?php echo $src; ?>" />
		<?php endif; ?>
		</a>
		<?php if (!empty($thumb_img->post_excerpt)) : ?>
		<p class="gallery-caption">
			<?php echo $thumb_img->post_excerpt; // Display Caption ?>
		</p>
		<?php endif; ?>
	</div>
<?php endforeach; ?>
</div>