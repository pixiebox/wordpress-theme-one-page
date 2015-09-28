<?php
$the_title = get_the_title();
?>
<div class="<?php echo $width_class; ?>">
	<div class="clearfix page-content">
		<q>
			<?php echo get_the_content(); ?>
		</q>
	</div>
	<?php if (!empty($the_title)) : ?>
	<p>
		<cite><?php echo $the_title; ?></cite>
	</p>
	<?php endif; ?>
</div>