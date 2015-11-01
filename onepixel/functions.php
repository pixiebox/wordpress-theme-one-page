<?php
/*
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 *
 * @since One Pixel 1.0.0
*/

/**
 * Set the content width based on the theme's design and stylesheet.
 *
 * @since One Pixel 1.0.0
 */
if ( ! isset( $content_width ) ) {
	$content_width = 780;
}

function onepixel_setup(){
	load_theme_textdomain( 'onepixel', get_template_directory() . '/languages' );

	/* This theme uses bootstrap_nav_menu() */
	register_nav_menus( array(
		'primary' 		=> __( 'Primary Menu', 'onepixel' ),
		'secundairy' 	=> __( 'Secundairy Menu', 'onepixel' ),
	) );

	/* Add default posts and comments RSS feed links to head. */
	add_theme_support( 'automatic-feed-links' );

	/* Setup the WordPress core custom background feature. */
	add_theme_support( 'custom-background' );

	/*
	 * Switch default core markup for search form, comment form, and comments
	 * to output valid HTML5.
	 */
	add_theme_support( 'html5', array(
		'comment-form', 'comment-list', 'gallery', 'search-form'
	) );

	/*
	 * Enable support for Post Formats on posts.
	 *
	 * @link https://codex.wordpress.org/Function_Reference/add_theme_support#Post_Formats
	 */
	add_theme_support( 'post-formats', array( 'gallery', 'quote' ) );

	/*
	 * Let WordPress manage the document title.
	 * By adding theme support, we declare that this theme does not use a
	 * hard-coded <title> tag in the document head, and expect WordPress to
	 * provide it for us.
	 */
	add_theme_support( "title-tag" );

	/*
	 * Enable support for Post Thumbnails on posts and pages and declare five sizes.
	 *
	 * @link https://codex.wordpress.org/Function_Reference/add_theme_support#Post_Thumbnails
	 */
	add_theme_support( 'post-thumbnails' );
	add_image_size( 'mobile', 480 );
	add_image_size( 'retina@2', 640 );
	add_image_size( 'tablet', 900 );
	add_image_size( 'desktop', 1170 );
	add_image_size( 'retina@3', 2048 );

	/* Set default breakpoints for srcBox responsive images. */
	$options = get_option( 'srcbox_settings' );

	if ( empty( $options ) ) :
		$_breakpoints = srcbox_breakpoints();

		foreach ( $_breakpoints as $breakpoint ) :
			$update_breakpoints[] = $breakpoint['breakpoint_id'];
		endforeach;

		update_option( 'srcbox_settings', $update_breakpoints );
	endif;

	/* add post-formats to post_type 'page' */
	add_post_type_support( 'page', 'post-formats' );

	/* add 'One page' category */
	if ( file_exists ( ABSPATH.'/wp-admin/includes/taxonomy.php' ) ) :
		require_once ( ABSPATH.'/wp-admin/includes/taxonomy.php' ); 

		if ( ! get_cat_ID( 'One page' ) ) :
			$onepage_cat_id = wp_create_category( 'One page' );
			define( 'PROTECTED_TERM_ID', $onepage_cat_id );
		endif;
	endif;

	if ( !defined( 'PROTECTED_TERM_ID' ) ) :
		$idObj = get_category_by_slug( 'one-page' ); 
		$onepage_cat_id = $idObj->term_id;
		define( 'PROTECTED_TERM_ID', $onepage_cat_id );
	endif;

	/* Do not spam the database with numerous revisions */
	define( 'WP_POST_REVISIONS', 2 );
}
add_action( 'after_setup_theme', 'onepixel_setup' );

/**
 * Register widget area.
 *
 * @since One Pixel 1.0.0 
 * 
 * @link https://codex.wordpress.org/Function_Reference/register_sidebar
 */
function onepixel_widgets_init(){
	register_sidebar( array(
		'class'	=> 'col-sm-4',
		'id'		=> 'main-sidebar',
		'name' 	=> __( 'Main Sidebar', 'onepixel' )
	) );
}
add_action( 'widgets_init', 'onepixel_widgets_init' );

/**
 * Adds custom sizes to Media Library.
 *
 * @since One Pixel 1.0.0
 *
 * @param array $defaultSizes WordPress Sizes.
 *
 * @return array Default sizes merged with custom sizes.
 */
function addMySizes ( $defaultSizes ) {
	$mySizes = array(
		'desktop' 	=> 'desktop',
		'mobile' 		=> 'mobile',
		'retina@2' 	=> 'retina@2',
		'retina@3' 	=> 'retina@3',
		'tablet' 		=> 'tablet'
	);  

	return array_merge( $defaultSizes, $mySizes );
}  
add_filter( 'image_size_names_choose', 'addMySizes' );

/**
 * Remove the delete option for the 'One page' category.
 *
 * @since One Pixel 1.0.0
 *
 * @param array $actions 	WordPress' default row actions.
 * @param array $tag 			List item.
 *
 * @return array Default sizes merged with custom sizes.
 */
function category_row_actions( $actions, $tag ) {
	if ( $tag->term_id == PROTECTED_TERM_ID ) unset( $actions['delete'] );

	return $actions;
}

/**
 * Toggle Custom Meta Boxes when 'One page' category or 'Gallery' format is chosen
 *
 * @since One Pixel 1.0.0
 *
 * @param string $hook The current Admin Dashboard page
 */
function admin_post( $hook ){
	if( $hook != 'post.php' ) 
		return;

	add_action( 'admin_footer', 'admin_post_js' );
}

function admin_post_js() {
?>
	<script type="text/javascript">
	jQuery(document).ready(function() {
		var el_cat_onepage = jQuery('#categorychecklist input[value="<?php echo PROTECTED_TERM_ID; ?>"]');
		var properties = jQuery('#postbox-container-2');
		var onepage_properties = jQuery('#background-panel-metabox, #background-parallax-metabox, #layout_metabox', properties);

		function toggle_onepage_properties () {
			if (el_cat_onepage.prop('checked')) {
				onepage_properties.show();
			} else {
				onepage_properties.hide();
			}
		}

		toggle_onepage_properties();

		el_cat_onepage.change(function (e) {
			toggle_onepage_properties();
		});

		var elems_format = jQuery('#post-formats-select .post-format');
		var format_properties = jQuery('#gallery_metabox', properties);

		function toggle_gallery_properties () {
			if (elems_format.filter(':checked').val() == 'gallery') {
				format_properties.show();
			} else {
				format_properties.hide();
			}
		}

		toggle_gallery_properties();

		elems_format.on('change', function () {
			toggle_gallery_properties();
		});
	});
	</script>
	<?php
}

if ( is_admin() ) :
	add_filter( 'category_row_actions', 'category_row_actions', 10, 2 );
	add_action( 'admin_enqueue_scripts', 'admin_post' );
endif;

/**
 * Implement the Custom Header feature.
 *
 * @since One Pixel 1.0.0
 */
require get_template_directory() . '/inc/custom-header.php';

/**
 * Implement the Template tag features.
 *
 * @since One Pixel 1.0.0
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Register Lato Google font for Twenty Fourteen.
 *
 * @since One Pixel 1.0.0
 *
 * @return string
 */
function onepage_font_url(){
	$query_args = array(
		'family' => urlencode( 'Didact+Gothic|Lato:400,700' ),
		'subset' => urlencode( 'latin,latin-ext' )
	);
	$font_url = add_query_arg( $query_args, '//fonts.googleapis.com/css' );

	return $font_url;
}

/**
 * Make the enabled breakpoints available for srcBox and SliderBox on the frontend
 *
 * @since One Pixel 1.0.0
 */
function breakpoints(){
	$breakpoints = get_option( 'srcbox_settings' );

	foreach ($breakpoints as $value) :
		$breakpoint = explode( 'breakpoint_', $value )[1];

		if ( $breakpoint == '480' ) :
			$array[] = array(
				'folder' 		=> $breakpoint,
				'maxWidth' 	=> (int)$breakpoint
			);
		elseif ( $breakpoint == '640' ) :
			$array[] = array(
				'folder' 		=> $breakpoint,
				'minWidth' 	=> 481,
				'maxWidth' 	=> 767
			);

			$array[] = array(
				'folder' 							=> '640',
				'maxWidth' 						=> 320,
				'minDevicePixelRatio' => 2
			);
		elseif ( $breakpoint == '900' ) :
			$array[] = array(
				'folder'	 	=> '900',
				'minWidth' 	=> 768,
				'maxWidth' 	=> 900
			);

			$array[] = array(
				'folder' 		=> '900',
				'minWidth' 	=> 748,
				'maxWidth' 	=> 1024
			);
		elseif ( $breakpoint == '1170' ) :
			$array[] = array(
				'folder' 							=> '1170',
				'minWidth' 						=> 320,
				'maxWidth' 						=> 667,
				'minDevicePixelRatio' => 2
			);

			$array[] = array(
				'folder' 		=> '1170',
				'minWidth'	=> 992
			);
		elseif ( $breakpoint == '2048' ) :
			$array[] = array(
				'folder' 							=> '2048',
				'minWidth' 						=> 748,
				'maxWidth' 						=> 1024,
				'minDevicePixelRatio'	=> 2
			);

			$array[] = array(
				'folder' 							=> '2048',
				'minWidth' 						=> 414,
				'maxWidth' 						=> 736,
				'minDevicePixelRatio' => 3
			);
		endif;
	endforeach;

?>
	<script type="text/javascript">
		var json_breakpoints = <?php echo json_encode( $array ); ?>;
	</script>
<?php
}
add_action( 'wp_head', 'breakpoints' );

function html_menu_class(){
	$menu = wp_nav_menu(
    array (
			'echo'				=> FALSE,
			'fallback_cb'	=> '__return_false'
    )
	);

	if ( ! empty ( $menu ) ) :
?>
	<script type="text/javascript">
		var el_html = document.querySelector('html');
		el_html.className = el_html.className + 'has-menu';
	</script>
<?php
	endif;
}
add_action( 'wp_head', 'html_menu_class' );

/**
 * Enqueue scripts and styles for the front end.
 *
 * @since One Pixel 1.0.0
 */
function add_theme_scripts(){
	wp_enqueue_script( 'jquery' );

	wp_register_style( 'bootstrap-css', get_template_directory_uri() . '/libs/bootstrap/css/bootstrap.css' );
	wp_enqueue_style( 'bootstrap-css' );

	// Add Lato font, used in the main stylesheet.
	wp_enqueue_style( 'onepage-lato', onepage_font_url() );

	wp_register_style( 'dialogbox-css', get_template_directory_uri() . '/css/dialogBox.css' );
	wp_enqueue_style( 'dialogbox-css' );

	wp_register_style( 'style', get_template_directory_uri() . '/style.css' );
	wp_enqueue_style( 'style' );

	wp_register_script( 'bootstrap-js', get_template_directory_uri() . '/libs/bootstrap/js/bootstrap.js' );
	wp_enqueue_script( 'bootstrap-js' );

	wp_register_script( 'srcbox-js', get_template_directory_uri() . '/js/srcBox.js' );
	wp_enqueue_script( 'srcbox-js' );

	wp_register_script( 'dialogbox-js', get_template_directory_uri() . '/js/dialogBox.js', array( 'jquery' ) );
	wp_enqueue_script( 'dialogbox-js' );

	wp_register_script( 'head-load-js', get_template_directory_uri() . '/js/head.load.js' );
	wp_enqueue_script( 'head-load-js' );

	wp_enqueue_script( 'jquery-masonry' );

	wp_register_script( 'one-pixel', get_template_directory_uri() . '/js/one-pixel.js', array( 'jquery', 'jquery-masonry', 'srcbox-js', 'bootstrap-js' ) );
	wp_enqueue_script( 'one-pixel' );

	wp_localize_script( 'one-pixel', 'objectL10n', array(
		'read_more' => __( 'Read More &raquo;', 'onepixel' )
	) );

	if ( is_singular() && comments_open() ) :
		wp_enqueue_script( 'onepixel.comments', get_template_directory_uri() . '/js/comments.js', array( 'jquery' ), '1.0.0', true );

		$comment_i18n = array( 
			'emailInvalid' 	=> __( 'That email appears to be invalid.', 'onepixel' ),
			'error' 				=> __( 'There were errors in submitting your comment; complete the missing fields and try again!', 'onepixel' ),
			'flood' 				=> sprintf( __( 'Your comment was either a duplicate or you are posting too rapidly. <a href="%s">Edit your comment</a>', 'onepixel' ), '#comment' ),
			'processing' 		=> __( 'Processing...', 'onepixel' ),
			'required' 			=> __( 'This is a required field.', 'onepixel' )
		);
	endif;

	wp_localize_script( 'onepixel.comments', 'onepixelComments', $comment_i18n );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) )
		wp_enqueue_script( 'comment-reply' );
}
add_action( 'wp_enqueue_scripts', 'add_theme_scripts' );

/**
 * Enqueue scripts and styles for the back end.
 *
 * @since One Pixel 1.0.0
 */
function onepage_admin_css(){
	global $post_type;

	if ( in_array( $post_type, array( 'onepage_panels', 'onepage_panels' ) ) ) :	
		echo '<link type="text/css" rel="stylesheet" href="' . esc_url( get_template_directory_uri() )  . '/css/onepage-admin.css" />';
	endif;

}
add_action( 'admin_head', 'onepage_admin_css' );

/**
 * Provide responses to comments.js based on detecting an XMLHttpRequest parameter.
 *
 * @since One Pixel 1.0.0
 *
 * @param $comment_ID     ID of new comment.
 * @param $comment_status Status of new comment. 
 *
 * @return echo JSON encoded responses with HTML structured comment, success, and status notice.
 */
function onepage_ajax_comments( $comment_ID, $comment_status ){
	if ( !empty( $_SERVER['HTTP_X_REQUESTED_WITH'] )
	&& strtolower( $_SERVER['HTTP_X_REQUESTED_WITH'] ) == 'xmlhttprequest' ) :
		/* This is an AJAX request. Handle response data. */
		switch ( $comment_status ) {
			case '0':
				/* Comment needs moderation; notify comment moderator. */
				wp_notify_moderator( $comment_ID );
				$return = array( 
					'response' => '', 
					'success'  => 1, 
					'status'   => __( 'Your comment has been sent for moderation. It should be approved soon!', 'onepixel' ) 
				);
				wp_send_json( $return );
				break;
			case '1':
				/* Approved comment; generate comment output and notify post author. */
				$comment            = get_comment( $comment_ID );
				$comment_class      = comment_class( 'onepixel-ajax-comment', $comment_ID, $comment->comment_post_ID, false );
				$comment_output     = '
						<li id="comment-' . $comment->comment_ID . '"' . $comment_class . ' tabindex="-1">
							<article id="div-comment-' . $comment->comment_ID . '" class="comment-body">
								<footer class="comment-meta">
								<div class="comment-author vcard">' .
									get_avatar( $comment->comment_author_email )
									. '<b class="fn">' . __( 'You said:', 'onepixel' ) . '</b> </div>

								<div class="comment-meta commentmetadata"><a href="#comment-'. $comment->comment_ID .'">' . 
									get_comment_date( 'F j, Y \a\t g:i a', $comment->comment_ID ) . '</a>
								</div>
								</footer>
								
								<div class="comment-content">' . $comment->comment_content . '</div>
							</article>
						</li>';
				
				$output = $comment->comment_parent == 0
					? $comment_output
					: '<ul class="children">' . $comment_output . '</ul>';

				wp_notify_postauthor( $comment_ID );
				$return = array( 
					'response'=>$output, 
					'success' => 1, 
					'status'=> sprintf( __( 'Thanks for commenting! Your comment has been approved. <a href="%s">Read your comment</a>', 'onepixel' ), "#comment-$comment_ID" ) 
				);
				wp_send_json( $return );
				break;
			default:
				/* The comment status was not a valid value. Only 0 or 1 should be returned by the comment_post action. */
				$return = array( 
					'response' => '', 
					'success'  => 0, 
					'status'   => __( 'There was an error posting your comment. Try again later!', 'onepixel' ) 
				);
				wp_send_json( $return );
		}
	endif;
}
add_action( 'comment_post', 'onepage_ajax_comments', 20, 2 );

/**
 * Add Custom Meta Boxes for posts and pages
 *
 * @since One Pixel 1.0.0
 *
 * @return array $meta_boxes
 * 
 * @link https://github.com/humanmade/Custom-Meta-Boxes/wiki
 * @link https://github.com/humanmade/Custom-Meta-Boxes/blob/master/example-functions.php
 */
if ( is_admin() ) :
	if ( file_exists( dirname( __FILE__ ) . '/inc/cmb2/init.php' ) ) {
		require_once dirname( __FILE__ ) . '/inc/cmb2/init.php';
	} elseif ( file_exists( dirname( __FILE__ ) . '/inc/CMB2/init.php' ) ) {
		require_once dirname( __FILE__ ) . '/inc/CMB2/init.php';
	}

	/**
	 * Create Custom Meta Boxes
	 *
	 * @since One Pixel 1.0.0
	 *
	 * @param array $meta_boxes Etend array of meta boxes
	 *
	 * @return array $meta_boxes
	 */
	function cmb_onepage_layout_metaboxes(){
		$cmb_layout = new_cmb2_box( array(
			'context'    		=> 'normal',
			'id'            => 'layout_metabox',
			'object_types'  => array( 'post' ), // Post type
			'priority'   		=> 'high',
			'title'         => __( 'Layout Properties', 'onepixel' ),
		) );

		$cmb_layout->add_field(array(
			'default'	=> 'full-width',
			'id'      => 'width',
			'name'    => __( 'Width', 'onepixel' ),
			'options' => array(
									'full-width'	=> __( 'Full width', 'onepixel' ),
									'container'	 	=> __( 'Container', 'onepixel' )
								),
			'type'    => 'radio'
		));

		$cmb_layout->add_field(array(
			'default'		=> 'default',
			'id'      	=> 'orientation',
			'name'    	=> __( 'Swipe orientation', 'onepixel' ),
			'options' 	=> array(
								'default' => __( 'default', 'onepixel' ),
								'ltr'	 		=> __( 'left to right', 'onepixel' ),
								'rtl' 		=> __( 'right to left', 'onepixel' ),
								'up' 			=> __( 'upwards', 'onepixel' )
							),
			'type'    	=> 'radio'
		));

		$arr_background_color = array(
			'name'  => __( 'Choose background color', 'onepixel' ),
			'id'  	=> 'backgroundColor',
			'type'  => 'colorpicker'
		);

		$arr_background_image = array( 
			'id'   => 'backgroundImage', 
			'name' => __( 'Choose background image', 'onepixel' ), 
			'type' => 'file'
		);

		$arr_srcbox = array( 
			'id'   => 'srcBox', 
			'name' => __( 'Enable srcBox', 'onepixel' ),
			'type' => 'checkbox'
		);

		$arr_textcolor = array(
			'id'  	=> 'textColor',
			'name'  => __( 'Choose text color', 'onepixel' ),
			'type'  => 'colorpicker'
		);

		$cmb_group_panel = new_cmb2_box( array(
			'id'           => 'background-panel-metabox',
			'object_types' => array( 'post' ),
			'title'        => __( 'Panel Background properties', 'onepixel' )
		) );

		$cmb_group_field_panel = $cmb_group_panel->add_field( array(
			'id'   				=> 'background-panel-group',
			'description' => __( 'Properties', 'onepixel' ),
			'options'     => array(
				'group_title'   => __( 'Panel', 'onepixel' ) // {#} gets replaced by row number
			),
			'repeatable' 	=> false,
			'type'        => 'group'
		) );

		/**
		 * Group fields works the same, except ids only need
		 * to be unique to the group. Prefix is not needed.
		 *
		 * The parent field's id needs to be passed as the first argument.
		 */
		$cmb_group_panel->add_group_field( $cmb_group_field_panel, $arr_background_color );
		$cmb_group_panel->add_group_field( $cmb_group_field_panel, $arr_background_image );
		$cmb_group_panel->add_group_field( $cmb_group_field_panel, $arr_srcbox );
		$cmb_group_panel->add_group_field( $cmb_group_field_panel, $arr_textcolor );

		$cmb_group_parallax = new_cmb2_box( array(
			'id'           => 'background-parallax-metabox',
			'object_types' => array( 'post' ),
			'title'        => __( 'Parallax Background properties', 'onepixel' )
		) );

		$cmb_group_field_parallax = $cmb_group_parallax->add_field( array(
			'description' => __( 'Properties', 'onepixel' ),
			'id'   				=> 'background-parallax-group',
			'options'     => array(
				'add_button'    => __( 'Add Another Entry', 'cmb2' ),
				'group_title'   => __( 'Parallax {#}', 'onepixel' ), // {#} gets replaced by row number
				'remove_button' => __( 'Remove Entry', 'cmb2' ),
				'repeatable' 		=> true,
				'sortable'      => true // beta
			),
			'type'        => 'group'
		) ) ;

		/**
		 * Group fields works the same, except ids only need
		 * to be unique to the group. Prefix is not needed.
		 *
		 * The parent field's id needs to be passed as the first argument.
		 */
		$cmb_group_parallax->add_group_field( $cmb_group_field_parallax, $arr_background_color );
		$cmb_group_parallax->add_group_field( $cmb_group_field_parallax, $arr_background_image );
		$cmb_group_parallax->add_group_field( $cmb_group_field_parallax, $arr_srcbox );
		$cmb_group_parallax->add_group_field( $cmb_group_field_parallax, $arr_textcolor );
	}
	add_filter( 'cmb2_init', 'cmb_onepage_layout_metaboxes' );

	/**
	 * Create Custom Meta Boxes
	 *
	 * @since One Pixel 1.0.0
	 *
	 * @param array $meta_boxes Etend array of meta boxes
	 *
	 * @return array $meta_boxes
	 */
	function cmb_new_gallery_metaboxes(){
		$cmb_gallery = new_cmb2_box( array(
			'id'            => 'gallery_metabox',
			'title'         => __( 'Gallery', 'onepixel' ),
			'object_types'  => array( 'page', 'post' ), // Post type
			 'context'    => 'normal',
			 'priority'   => 'high',
		) );

		$cmb_gallery->add_field(array(
			'default'	=> 'one-column',
			'id'      	=> 'gallery-layout',
			'name'    	=> __( 'Layout', 'onepixel' ),
			'options' 	=> array(
											'one-column' 		=> __( '1 column', 'onepixel' ),
											'two-columns' 	=> __( '2 columns', 'onepixel' ),
											'three-columns' => __( '3 columns', 'onepixel' ),
										),
			'type'    	=> 'radio'
		));
		$cmb_gallery->add_field(array(
			'id'      	=> 'masonry',
			'name'    	=> __( 'Apply Masonry', 'onepixel' ),
			'type'    	=> 'checkbox'
		));
		$cmb_gallery->add_field(array(
			'id'      	=> 'srcBox',
			'name'    	=>  __( 'Enable srcBox', 'onepixel' ),
			'type'    	=> 'checkbox'
		));
	}
	add_filter( 'cmb2_init', 'cmb_new_gallery_metaboxes' );
endif;

/**
 * Flush your rewrite rules
 *
 * @since One Pixel 1.0.0
 */
function onepage_flush_rewrite_rules(){
	flush_rewrite_rules();
}
add_action( 'after_switch_theme', 'onepage_flush_rewrite_rules' );

/**
 * always show certain screen options.
 * These are the metakeys we will need to update
 *
 * @since One Pixel 1.0.0
 *
 * @param string $user_id User who assigned metaboxes
 */
function set_user_metaboxes ( $user_id = NULL ) {
	$meta_key['order'] = 'meta-box-order_post';
	$meta_key['hidden'] = 'metaboxhidden_post';

	/* So this can be used without hooking into user_register */
	if ( ! $user_id )
		$user_id = get_current_user_id(); 

	/* Set the default order if it has not been set yet */
	if ( ! get_user_meta( $user_id, $meta_key['order'], true ) ) :
		$meta_value = array(
			'advanced' 	=> '',
			'normal' 		=> 'postexcerpt, tagsdiv-post_tag, postcustom, commentstatusdiv, commentsdiv, trackbacksdiv, slugdiv, authordiv, revisionsdiv',
			'side' 			=> 'submitdiv, formatdiv, categorydiv, postimagediv',
		);
		update_user_meta( $user_id, $meta_key['order'], $meta_value );
	endif;

	/* Set the default hiddens if it has not been set yet */
	if ( ! get_user_meta( $user_id, $meta_key['hidden'], true ) ) :
		$meta_value = array(
			'tagsdiv-post_tag',
			'postcustom',
			'commentstatusdiv',
			'commentsdiv',
			'trackbacksdiv',
			'slugdiv',
			'authordiv',
			'revisionsdiv'
		);
		update_user_meta( $user_id, $meta_key['hidden'], $meta_value );
	endif;
}
add_action( 'user_register', 'set_user_metaboxes' );
add_action( 'admin_init', 'set_user_metaboxes' );

/**
 * Add Twitter Bootstrap Menu
 *
 * @since One Pixel 1.0.0
 *
 * @link https://github.com/twittem/wp-bootstrap-navwalker
 */
require_once( 'inc/wp_bootstrap_navwalker.php' );

function bootstrap_nav_menu(){
	 wp_nav_menu( array(
		'container' 			=> false,
		'container' 			=> false,
		'depth' 					=> 2,
		'fallback_cb'			=> 'wp_bootstrap_navwalker::fallback',
		'menu_class' 			=> 'nav navbar-nav',
		'menu_id' 				=> 'nav',
		'theme_location' 	=> 'primary',
		'items_wrap' 			=> '
			<div class="navbar navbar-default navbar-fixed-top" id="navbar-header">
				<div class="container">
					<nav role="navigation" aria-label="' . __( 'Primary Menu', 'onepixel' ) . '" class="clearfix navbar-inner">
						<div class="clearfix navbar-header">
							<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
								<span class="screen-reader-text">Toggle navigation</span>
								<span class="icon-bar"></span>
								<span class="icon-bar"></span>
								<span class="icon-bar"></span>
							</button>' .
							get_search_form( false ) .
						'</div>
						<div class="clearfix navbar-collapse collapse">
							<ul class="clearfix nav navbar-nav menu" id="nav" role="menubar">
								%3$s
							</ul>
						</div>
					</nav>
				</div>
			</div>',
		/* Process nav menu using our custom nav walker */
		'walker'	=> new wp_bootstrap_navwalker()
	));
}

/**
 * Add options page for srcbox
 *
 * @since One Pixel 1.0.0
 */
function srcbox_add_admin_menu(){ 
	add_theme_page( 'srcBox', 'srcBox', 'manage_options', 'srcBox', 'srcbox_options_page' );
}

/**
 * Register srcbox settings page.
 * Add settings sections for srcbox settings page.
 * Add settings fields to sections for srcbox settings page.
 *
 * @since One Pixel 1.0.0
 */
function srcbox_settings_init(){ 
	register_setting( 'srcPage', 'srcbox_settings' );

	add_settings_section(
		'srcbox_srcPage_section', 
		__( 'srcBox breakpoints', 'onepixel' ), 
		'srcbox_settings_section_callback',
		'srcPage'
	);

	add_settings_field(  
		'srcbox_checkbox_field_dimensions',  
		__( 'Breakpoints', 'onepixel' ),
		'srcbox_checkbox_field_dimensions_render', 
		'srcPage', 
		'srcbox_srcPage_section'
	);
}

/**
 * Add values to srcBox settings page.
 *
 * @since One Pixel 1.0.0
 *
 * @return array 	breakpoint values
 */
function srcbox_breakpoints(){
	return array(
		array(
			'breakpoint_id' => 'breakpoint_480',
			'name'					=> __( 'Breakpoint mobile', 'onepixel' )
		),
		array(
			'breakpoint_id' => 'breakpoint_640',
			'name'					=> __( 'Breakpoint mobile retina @2', 'onepixel' )
		),
		array(
			'breakpoint_id' => 'breakpoint_900',
			'name'					=> __( 'Breakpoint tablet', 'onepixel' )
		),
		array(
			'breakpoint_id' => 'breakpoint_1170',
			'name'					=> __( 'Breakpoint desktop', 'onepixel' )
		),
		array(
			'breakpoint_id' => 'breakpoint_2048',
			'name'					=> __( 'Breakpoint tablet retina @2 / mobile retina @3', 'onepixel' )
		),
	);
}

/**
 * Render labels and input fields for srcbox settings page.
 *
 * @since One Pixel 1.0.0
 */
function srcbox_checkbox_field_dimensions_render(){ 
	$options = get_option( 'srcbox_settings' );
	if ( !$options ) $options = array();
	
	$_breakpoints = srcbox_breakpoints();

	foreach ( $_breakpoints as $breakpoint) :
		$checked = ( empty( $options ) && $breakpoint['breakpoint_id'] !== 'breakpoint_2048' )
		|| in_array($breakpoint['breakpoint_id'], $options )
			? 'checked="checked"'
			: '';
?>
<tr>
	<th>
	<?php
	echo sprintf(
		'<label><input type="checkbox" id="%1$s[%2$s]" name="%1$s[]" value="%2$s" %4$s /> %3$s</label><br />',
		'srcbox_settings',
		$breakpoint['breakpoint_id'],
		explode( '_', $breakpoint['breakpoint_id'] )[1],
		$checked
	);
?>
	</th>
	<td>
	<?php
		echo $breakpoint['name'];
	?>
	</td>
</tr>
<?php
	endforeach;
}

/**
 * Callback for srcbox settings page.
 *
 * @since One Pixel 1.0.0
 *
 * echo callback
 */
function srcbox_settings_section_callback(){ 
	_e( 'Preferably check all breakpoints to load the most appropiate image for each device.', 'onepixel' );
}

/**
 * Create srcbox settings page.
 */
function srcbox_options_page(){
?>
	<form action='options.php' method='post'>
		<h2>srcBox</h2>
		<?php
		settings_fields( 'srcPage' );
		do_settings_sections( 'srcPage' );
		submit_button();
		?>
	</form>
<?php
}
add_action( 'admin_menu', 'srcbox_add_admin_menu' );
add_action( 'admin_init', 'srcbox_settings_init' );

/**
 * The filter runs when resizing an image to make a thumbnail or intermediate size.
 * The new image filename is more suitable for the srcBox and SliderBox functionalities.
 *
 * @since One Pixel 1.0.0
 *
 * @param string $image
 *
 * @return string $image	New image name
 */
function srcbox_rename_intermediates( $image ){
	$new_name = preg_replace(
		'/(.+\\-)(([0-9]+)x([0-9]+)-)?([0-9]+)x([0-9]+)(.jpg|.jpeg|.png|.gif)/',
		'$1$2$3$4$5$7',
		$image
	);
	$did_it = rename( $image, $new_name );
	if( $did_it ) return $new_name;

	return $image;
}
add_filter( 'image_make_intermediate_size', 'srcbox_rename_intermediates' );

/**
 * Apply data attributes on thumbnails for srcBox responsive images.
 *
 * @since One Pixel 1.0.0
 *
 * @link http://pixiebox.com/code/docs/srcbox
 * @param string $html
 *
 * @return string $html
 */
function srcbox_post_thumbnail_html( $html ){
	$options = get_option( 'srcbox_settings' );

	if ( empty( $html ) || empty( $options ) ) return $html;

	$upload_dir = wp_upload_dir();

	$dom = new DOMDocument('1.0', 'UTF-8');
	libxml_use_internal_errors(true);
	$dom->loadHTML('<div>' .mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8').'</div>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
	$dom->encoding = 'UTF-8';

	$images = $dom->getElementsByTagName('img');

	foreach ( $images as $img ) :
		$img_src = $img->getAttribute( 'src' );

		preg_match(
			'/(https?:\/\/.+\/uploads\/)(.+\/)(.+\-[0-9]+[x[0-9]+]?\.jpg|\.jpeg|\.png|\.gif)/i',
			preg_replace(
				'/(https?:\/\/.+\/wp-content\/.+\/.+\-)([0-9]+[x[0-9]+]?)(.jpg|.jpeg|.png|.gif)/i',
				'${1}640$3',
				$img_src
			),
			$is_src_boxed
		);

		if ( !empty( $is_src_boxed )
		&& file_exists( $upload_dir['basedir'] . '/' .$is_src_boxed[2] . '/' .$is_src_boxed[3] ) ) :

			$img->removeAttribute( 'height' );
			$img->removeAttribute( 'width' );
			$img->setAttribute( 'src', get_template_directory_uri() . '/images/dot.gif' );
			$img->setAttribute( 'class', $img->getAttribute( 'class' ) . ' lag srcbox' );
			$img->setAttribute( 'data-breakpoint',  $upload_dir['baseurl'] . '/' .  $is_src_boxed[2] );
			$img->setAttribute( 'data-img', $is_src_boxed[3] );
		endif;
	endforeach;

	return $dom->saveHTML($dom->documentElement->firstChild) . PHP_EOL . PHP_EOL;
}
add_filter( 'the_content', 'srcbox_post_thumbnail_html', 10 );
add_filter( 'get_avatar', 'srcbox_post_thumbnail_html', 10 );

/**
 * Page Slug Body Class
 *
 * @param array $classes
 *
 * @return array $classes
 *
 * @since One Pixel 1.0.0
 */
function add_slug_body_class( $classes ){
	global $post;

	if ( isset( $post ) )
		$classes[] = $post->post_type . '-' . $post->post_name;

	return $classes;
}
add_filter( 'body_class', 'add_slug_body_class' );

/**
 * Exclude 'One page' category from Category Widget
 *
 * @param array $args widget_categories_args() arguments.
 *
 * @since One Pixel 1.0.0
 */
function exclude_cat_from_cat_widget( $args ){
	$exclude = get_cat_ID( 'One page' );
	$args['exclude'] = $exclude;

	return $args;
}
add_filter( 'widget_categories_args', 'exclude_cat_from_cat_widget' );

/**
 * Exclude 'One page' posts from Recent Post Widget
 *
 * @since One Pixel 1.0.0
 */
function exclude_posts_from_recentPostWidget_by_cat(){
	$onepage_id = get_cat_ID( 'One page' );
	$exclude = array( 'cat' => '-' . $onepage_id );

	return $exclude;
}
add_filter( 'widget_posts_args', 'exclude_posts_from_recentPostWidget_by_cat' );

/**
 * Changing excerpt more
 *
 * @since One Pixel 1.0.0
 */
function custom_excerpt_more(){
	global $post;

	return '...<a href="'. get_permalink( $post->ID ) . '">' . __( 'Read More &raquo;', 'onepixel' ) . '</a>';
}
add_filter( 'excerpt_more', 'custom_excerpt_more' );

/**
 * Add a `screen-reader-text` class to the search form's submit button.
 *
 * @param string $html Search form
 *
 * @return string Html search form.
 *
 * @since One Pixel 1.0.0
 */
function onepixel_search_form_modify( $html ){
	return str_replace( 'class="search-submit"', 'class="search-submit screen-reader-text"', $html );
}
add_filter( 'get_search_form', 'onepixel_search_form_modify' );