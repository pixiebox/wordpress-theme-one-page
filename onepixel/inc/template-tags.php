<?php
function onepixel_post_thumbnail( $size = 'post-thumbnail' ) {
	if ( post_password_required() || is_attachment() || !has_post_thumbnail() )
		return;

	if ( is_singular() ) :
	?>
	<div class="post-thumbnail">
		<?php the_post_thumbnail( $size ); ?>
	</div><!-- .post-thumbnail -->
	<?php else : ?>
	<a class="post-thumbnail" href="<?php the_permalink(); ?>" aria-hidden="true">
		<?php
			the_post_thumbnail( $size, array( 'alt' => get_the_title() ) );
		?>
	</a>
	<?php endif; // End is_singular()
}

function frontpage_sticky_posts(){
	
	$html = '';

	if( is_front_page() ) :

		/* Get all sticky posts */
		$sticky = get_option( 'sticky_posts' );

		if( !empty( $sticky ) ) :
			/* Sort the stickies with the newest ones at the top */
			rsort( $sticky );

			/* Get the 4 newest stickies (change 4 for a different number) */
			$sticky = array_slice( $sticky, 0, 4 );

			/* Query sticky posts */
			$the_query = new WP_Query( array( 'post__in' => $sticky, 'ignore_sticky_posts' => 1 ) );

			// The Loop
			if ( $the_query->have_posts() ) :

				$html .= '<div class="sm-panel container" id="sticky">
					<div class="row">';

				$i = 1;

				while ( $the_query->have_posts() ) :

					$the_query->the_post();

					if ($i % 4 == 0) :
						$html .= '
							</div>
							<div class="row">';
					endif;

					$html .= '
					<div class="col-sm-3 short-entry">
						<a class="h4" href="' . get_permalink(). '" title="'  . get_the_title() . '">' . get_the_title() . '</a>
						' . get_the_excerpt() . '
					</div>';

					$i++;

				endwhile;

				$html .= '</div>
					</div>';
			
			else :
				// no posts found
			endif;

			/* Restore original Post Data */
			wp_reset_postdata();
		endif;
	endif;

	return $html;
}