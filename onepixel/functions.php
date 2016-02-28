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

	/* add post-formats to post_type 'page' */
	add_post_type_support( 'page', 'post-formats' );

  
	/* Do not spam the database with numerous revisions */
	define( 'WP_POST_REVISIONS', 2 );
}
add_action( 'after_setup_theme', 'onepixel_setup' );

function add_taxonomies_to_pages() {
  register_taxonomy_for_object_type( 'post_tag', 'page' );
  register_taxonomy_for_object_type( 'category', 'page' );
}
add_action( 'init', 'add_taxonomies_to_pages' );

if ( ! is_admin() ) {
  add_action( 'pre_get_posts', 'category_and_tag_archives' );
}

function category_and_tag_archives( $wp_query ) {
  $my_post_array = array('post','page');
 
  if ( $wp_query->get( 'category_name' ) || $wp_query->get( 'cat' ) ) {
    $wp_query->set( 'post_type', $my_post_array );
  }

  if ( $wp_query->get( 'tag' ) ) {
    $wp_query->set( 'post_type', $my_post_array );
  }
}
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
		el_html.className = el_html.className + ' has-menu';
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

	wp_register_style( 'style', get_template_directory_uri() . '/style.css' );
	wp_enqueue_style( 'style' );

	wp_register_script( 'bootstrap-js', get_template_directory_uri() . '/libs/bootstrap/js/bootstrap.js' );
	wp_enqueue_script( 'bootstrap-js' );

	wp_register_script( 'head-load-js', get_template_directory_uri() . '/js/head.load.js' );
	wp_enqueue_script( 'head-load-js' );

	wp_enqueue_script( 'jquery-masonry' );

	wp_register_script( 'one-pixel', get_template_directory_uri() . '/js/one-pixel.js', array( 'jquery', 'jquery-masonry', /*'srcbox-js',*/ 'bootstrap-js' ) );
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
					'response'  => $output,
					'success'   => 1,
					'status'    => sprintf( __( 'Thanks for commenting! Your comment has been approved. <a href="%s">Read your comment</a>', 'onepixel' ), "#comment-$comment_ID" ) 
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
			'object_types'  => array( 'page', 'post' ), // Post type
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