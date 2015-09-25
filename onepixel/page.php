<?php get_header(); ?>

<div class="container">
	<div class="row">
		<div class="col-sm-8">
			<?php onepixel_post_thumbnail(); ?>
			<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>
			<div <?php post_class($content_classname); ?>>
				<h1><?php the_title(); ?></h1>
				<?php the_content(); ?>
			</div>
			<?php endwhile; wp_reset_query(); ?>
			<?php
				wp_link_pages( array(
					'nextpagelink' => '<span class="meta-nav" aria-hidden="true">' . __( 'Next page:', 'onepixel' ) . '</span> ' .
						'<span class="screen-reader-text">' . __( 'Next page', 'onepixel' ) . '</span> ' .
						'<span class="post-title">%title</span>',
					'prevpagelink' => '<span class="meta-nav" aria-hidden="true">' . __( 'Previous page:', 'onepixel' ) . '</span> ' .
						'<span class="screen-reader-text">' . __( 'Previous page', 'onepixel' ) . '</span> ' .
						'<span class="post-title">%title</span>'
				) );
			?>
		</div>
		<?php if ( is_active_sidebar( 'main-sidebar' ) ) : ?>
		<?php get_sidebar( 'main-sidebar' ); ?>
		<?php endif; ?>
	</div>
</div>

<?php get_footer(); ?>