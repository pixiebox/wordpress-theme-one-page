<?php get_header(); ?>

<div class="container single-content">
	<div class="row">
		<?php
		if ( have_posts() ) :
			while ( have_posts() ) :
				the_post();
				$format = get_post_format( get_the_id() );
				$content_classname = is_active_sidebar( 'main-sidebar' ) 
					? 'col-sm-8'
					: 'col-xs-12';
		?>
				<article <?php post_class($content_classname); ?>>
					<?php
						if ( in_array( $format, array('gallery', 'quote') ) ) :
							get_template_part( 'format', $format );
						else:
							onepixel_post_thumbnail( 'large' );
							
							$dateTime = new DateTime();
							$dateTime->setTimestamp(get_the_time('U'));
					?>
						<div class="entry-heading">
							<div class="entry-comments"><a href="#comment-list" title="comment list"><span><?php echo get_comments_number(); ?></span> <?php _e( 'Comments', 'onepixel' ); ?></a></div>
							<h1 class="entry-title"><?php the_title(); ?></h1>
							<?php the_date( get_option( 'date_format' ), '<span class="published" title="' . $dateTime->format( 'Y-m-d H:i:s' ) . '">', '</span>' ); ?>
						</div>

						<div class="entry-content">
						<?php the_content(); ?>
						<p class="bypostauthor"><?php echo sprintf( __( 'This post was written by %s', 'onepixel' ), get_the_author() ); ?></p>
						</div>

						<?php the_tags( '<div class="entry-tags">', '', '</div>' ); ?> 

						<?php
							// If comments are open or we have at least one comment, load up the comment template.
							if ( comments_open() || get_comments_number() ) :
								comments_template();
						?>
							<div id="comment-list">
							<?php
									$comments = get_comments( array( 'post_id' => get_the_id() ) );
									wp_list_comments( array(
										'format' => 'html5',
										'style' => 'div'
									), $comments );
							?>
							</div>
						<?php	endif; ?>
					<?php	endif; ?>
				<?php endwhile; ?>
				<?php
				if ( !is_attachment() ) :
					// Previous/next post navigation.
					the_post_navigation( array(
						'next_text' => '<span class="meta-nav" aria-hidden="true">' . __( 'Next:', 'onepixel' ) . '</span> ' .
							'<span class="screen-reader-text">' . __( 'Next post:', 'onepixel' ) . '</span> ' .
							'<span class="post-title">%title</span>',
						'prev_text' => '<span class="meta-nav" aria-hidden="true">' . __( 'Previous:', 'onepixel' ) . '</span> ' .
							'<span class="screen-reader-text">' . __( 'Previous post:', 'onepixel' ) . '</span> ' .
							'<span class="post-title">%title</span>'
					) );
				endif;
				?>
			<?php endif; ?>
			<?php	wp_reset_query();	?>
			</article>	
			<?php	if ( is_active_sidebar( 'main-sidebar' ) ) get_sidebar( 'main-sidebar' );	?>
	</div>
</div>

<?php get_footer(); ?>