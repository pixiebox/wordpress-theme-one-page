<?php get_header(); ?>

<div class="container">
	<div class="row">
		<?php
		$content_classname = 'list ' . (is_active_sidebar( 'main-sidebar' ) 
			? 'col-sm-8'
			: 'col-xs-12');
		?>
		<div class="<?php echo $content_classname; ?>">
			<div class="list-heading">
				<h1>
					<?php if( is_category() ): ?>
						Category: <?php single_cat_title(); ?>
					<?php elseif( is_tag() ): ?>
						Tag: <?php single_tag_title(); ?>
					<?php elseif( is_year() ): ?>
						Archive for <?php the_time('Y'); ?>
					<?php elseif( is_month() ): ?>
						Archive for <?php the_time('F Y'); ?>
					<?php else: ?>
						Archive
					<?php endif; ?>
				</h1>
			</div>

			<?php if ( have_posts() ): ?>
				<?php while ( have_posts() ) : the_post(); ?>
					<?php
						$dateTime = new DateTime();
						$dateTime->setTimestamp(get_the_time('U'));
					?>
					<div class="hfeed">
						<div class="entry-comments"><span><?php echo get_comments_number(); ?></span> <?php _e( 'Comments', 'onepixel' ); ?></div>
						<h2 class="entry-title"><a href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a></h2>
						<?php unset( $previousday ); the_date( get_option( 'date_format' ), '<span class="published" title="' . $dateTime->format( 'Y-m-d H:i:s' ) . '">', '</span>' ); ?>
						<div class="entry-summary">
						<?php the_excerpt(); ?>
						</div>
					</div>
				<?php endwhile; wp_reset_query(); ?>
			<?php else: ?>
				<h2>No posts found</h2>
			<?php endif; ?>

			<?php if ( $wp_query->max_num_pages > 1 ) : ?>
				<?php the_posts_navigation( array(
						'next_text' => '<span class="meta-nav" aria-hidden="true">' . __( 'Next:', 'onepixel' ) . '</span> ' .
							'<span class="screen-reader-text">' . __( 'Newer posts &rarr;', 'onepixel' ) . '</span> ',
						'prev_text' => '<span class="meta-nav" aria-hidden="true">' . __( 'Previous:', 'onepixel' ) . '</span> ' .
							'<span class="screen-reader-text">' . __( '&larr; Older posts', 'onepixel' ) . '</span> '
					) );
				?>
			<?php endif; ?>
		</div>

		<?php if (is_active_sidebar ('main-sidebar')) : ?>
		<?php get_sidebar('main-sidebar'); ?>
		<?php endif; ?>
	</div>
</div>

<?php get_footer(); ?>