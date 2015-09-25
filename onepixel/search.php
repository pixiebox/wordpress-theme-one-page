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
				<h1><?php printf( __( 'Search Results for: %s', 'onepixel' ), '' . get_search_query() . '' ); ?></h1>
			</div>
			<?php if ( have_posts() ) : ?>
				<?php while (have_posts()) : the_post(); ?>
					<?php
						$dateTime = new DateTime();
						$dateTime->setTimestamp(get_the_time('U'));
					?>
					<div class="search-entry">
						<div class="entry-comments"><span><?php echo get_comments_number(); ?></span> <?php _e( 'Comments', 'onepixel' ); ?></div>
						<h2 class="entry-title"><a href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a></h2>
						<?php unset( $previousday ); the_date( get_option( 'date_format' ), '<span class="published" title="' . $dateTime->format( 'Y-m-d H:i:s' ) . '">', '</span>' ); ?>
						<div class="search-summary">
						<?php the_excerpt(); ?>
						</div>
					</div>
				<?php endwhile; ?>
			<?php else: ?>
				<div class="search-summary">
					<h2><?php _e('Nothing Found', 'onepixel' ); ?></h2>
					<p>
						<?php _e('Sorry, but nothing matched your search criteria. Please try again with some different keywords.', 'onepixel' ); ?>
					</p>
				</div>
			<?php endif; ?>

			<?php if ( $wp_query->max_num_pages > 1 ) : ?>
				<?php the_posts_navigation( array(
						'next_text' => '<span class="meta-nav" aria-hidden="true">' . __( 'Next:', 'onepixel' ) . '</span> ' .
							'<span class="screen-reader-text">' . __( 'Next search results:', 'onepixel' ) . '</span> ',
						'prev_text' => '<span class="meta-nav" aria-hidden="true">' . __( 'Previous:', 'onepixel' ) . '</span> ' .
							'<span class="screen-reader-text">' . __( 'Previous search results:', 'onepixel' ) . '</span> '
					) );
				?>
			<?php endif; ?>
		</div>
		<?php if (is_active_sidebar( 'main-sidebar' )) get_sidebar('main-sidebar'); ?>
	</div>
</div>

<?php get_footer(); ?>
