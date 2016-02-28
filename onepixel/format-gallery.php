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

$i = 0;

foreach( $gallery['src'] as $src ) :
	$thumb_img = get_post( $gallery_ids[$i] ); // Get post by ID
	$i++;
?>
	<div class="<?php echo $class_name; ?>">
		<a href="<?php echo get_permalink( $thumb_img->ID ); ?>" rel="bookmark" title="<?php echo $thumb_img->post_title; ?>">
			<img src="<?php echo $src; ?>" />
		</a>
		<?php if (!empty($thumb_img->post_excerpt)) : ?>
		<p class="gallery-caption">
			<?php echo $thumb_img->post_excerpt; // Display Caption ?>
		</p>
		<?php endif; ?>
	</div>
<?php endforeach; ?>
</div>