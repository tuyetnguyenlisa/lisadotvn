<?php
define( TEMP_URL , get_bloginfo('template_url'));
define( VERSIONOG ,'1.0');
//require get_template_directory() . '/inc/copyright/copyright_svl.php';
//require get_template_directory() . '/inc/woocommerce_int/woo_int.php';
require get_template_directory() . '/inc/style_script_int.php';
/*
 * Setup theme
 */
function devvn_setup() {
	load_theme_textdomain( 'devvn', get_template_directory() . '/languages' );
	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );
	//Add thumbnail to post
	add_theme_support( 'post-thumbnails' );
	//Shortcode in widget
	add_filter('widget_text', 'do_shortcode');
	//menu
	/********
	 * Call: wp_nav_menu(array('theme_location'  => 'primary','container'=> ''));
	 * *********/
	register_nav_menus( array(
		'header' => __( 'Header menu', 'devvn' ),
		'footer'  => __( 'Footer menu', 'devvn' ),
	));
	/*
	 * Switch default core markup for search form, comment form, and comments
	 * to output valid HTML5.
	 */
	add_theme_support( 'html5', array(
		'search-form', 'comment-form', 'comment-list', 'gallery', 'caption'
	) );
	//Remove version
	remove_action('wp_head', 'wp_generator');
	//Remove Default WordPress Image Sizes
	function svl_remove_default_image_sizes( $sizes) {
		//unset( $sizes['thumbnail']);
		unset( $sizes['medium']);
		unset( $sizes['large']);
		 
		return $sizes;
	}
	add_filter('intermediate_image_sizes_advanced', 'svl_remove_default_image_sizes');
	if ( function_exists( 'add_image_size' ) ) {
		//add_image_size( 'homepage-thumb', 50, 50, true ); //(cropped)
	}
}
add_action( 'after_setup_theme', 'devvn_setup' );
//Sidebar
/*
 <?php if (!function_exists('dynamic_sidebar') || !dynamic_sidebar('Main Sidebar')) : ?><?php endif; ?>
 */
add_action( 'widgets_init', 'theme_slug_widgets_init' );
function theme_slug_widgets_init() {
    register_sidebar( array(
        'name' => __( 'Header Left', 'devvn' ),
        'id' => 'header-left-sidebar',        
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '',
		'after_title'   => '',
    ));
}
//Title
function svl_wp_title( $title, $sep ) {
	global $paged, $page;
	if ( is_feed() )
		return $title;
	$title .= get_bloginfo( 'name', 'display' );
	$site_description = get_bloginfo( 'description', 'display' );
	if ( $site_description && ( is_home() || is_front_page() ) )
		$title = "$title $sep $site_description";
	if ( ( $paged >= 2 || $page >= 2 ) && ! is_404() )
		$title = "$title $sep " . sprintf( __( 'Page %s', 'devvn' ), max( $paged, $page ) );
	return $title;
}
add_filter( 'wp_title', 'svl_wp_title', 10, 2 );
// Add specific CSS class by filter
add_filter( 'body_class', 'devvn_mobile_class' );
function devvn_mobile_class( $classes ) {
	if(wp_is_mobile()){
		$classes[] = 'devvn_mobile';
	}else{
		$classes[] ="devvn_desktop";
	}
	return $classes;
}
//Theme Options
function my_acf_options_page_settings( $settings )
{
	$settings['title'] = 'Theme Options';
	$settings['pages'] = array('General');

	return $settings;
}

add_filter('acf/options_page/settings', 'my_acf_options_page_settings');
//Code phan trang
function wp_corenavi_table() {
		global $wp_query;
		$big = 999999999; 
		$translated = "";
		$total = $wp_query->max_num_pages;
		if($total > 1) echo '<div class="paginate_links">';
		echo paginate_links( array(
			'base' 		=> str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
			'format' 	=> '?paged=%#%',
			'current' 	=> max( 1, get_query_var('paged') ),
			'total' 	=> $wp_query->max_num_pages,
			'mid_size'	=> '10',
			'prev_text'    => __('Previous'),
			'next_text'    => __('Next'),
		) );
		if($total > 1) echo '</div>';
}