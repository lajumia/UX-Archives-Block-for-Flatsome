<?php
/*
Plugin Name: UX Archives Block for Flatsome
Description: Adds a Flatsome UX Builder Archives Grid element for the 'archives' post type (filtering, lightbox/single, load more).
Version: 2.0
Author: Md Laju Miah
Text Domain: ux-archives-block
*/

if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! defined( 'UX_ARCHIVES_PLUGIN_DIR' ) ) {
	define( 'UX_ARCHIVES_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
	define( 'UX_ARCHIVES_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
}

/**
 * Optional: Register CPT & taxonomies if they don't already exist.
 * If you already registered 'archives' and 'archives_category'/'archives_tag' in your theme, skip this block.
 */
add_action( 'init', function() {
	if ( ! post_type_exists( 'archives' ) ) {

		$labels = array(
			'name'               => 'Archives',
			'singular_name'      => 'Archive',
			'menu_name'          => 'Archives',
			'name_admin_bar'     => 'Archive',
			'add_new'            => 'Add New',
			'add_new_item'       => 'Add New ',
			'new_item'           => 'New Archive',
			'edit_item'          => 'Edit Archive',
			'view_item'          => 'View Archive',
			'all_items'          => 'All Archives',
			'search_items'       => 'Search Archives',
		);

		register_post_type( 'archives', array(
			'labels' => $labels,
			'public' => true,
			'show_ui' => true,
			'has_archive' => true,
			'rewrite' => array( 'slug' => 'archives' ),
			'menu_position' => 9,
        	'menu_icon' => 'dashicons-portfolio',
			'supports' => array( 'title', 'editor', 'excerpt', 'thumbnail', 'custom-fields' ),
			'show_in_rest' => true,
		) );
	}

	// categories
	if ( ! taxonomy_exists( 'archives_category' ) ) {
		register_taxonomy( 'archives_category', array( 'archives' ), array(
			'hierarchical' => true,
			'labels' => array(
				'name' => 'Categories',
				'singular_name' => 'Archive Category',
			),
			'show_ui' => true,
			'show_admin_column' => true,
			'rewrite' => array( 'slug' => 'archives-category' ),
			'show_in_rest' => true,
		) );
	}

	// tags
	if ( ! taxonomy_exists( 'archives_tag' ) ) {
		register_taxonomy( 'archives_tag', array( 'archives' ), array(
			'hierarchical' => false,
			'labels' => array(
				'name' => 'Tags',
				'singular_name' => 'Archive Tag',
			),
			'show_ui' => true,
			'show_admin_column' => true,
			'rewrite' => array( 'slug' => 'archives-tag' ),
			'show_in_rest' => true,
		) );
	}
}, 5 );


/* ---------------------------
 * Register UX Builder element
 * --------------------------*/
add_action( 'ux_builder_setup', function() {

	// Only register element if Flatsome/UX Builder is active
	if ( ! function_exists( 'add_ux_builder_shortcode' ) ) return;

	add_ux_builder_shortcode( 'ux_archives', array(
		'name'            => __( 'Archives Grid', 'ux-archives-block' ),
		'category'        => __( 'Content', 'ux-archives-block' ),
		'render_template' => UX_ARCHIVES_PLUGIN_DIR . 'templates/ux-archives-template.php',
		'icon' => 'icon-portfolio',
		'options' => array(
			'posts_per_page' => array(
				'type'    => 'slider',
				'heading' => __( 'Posts per page', 'ux-archives-block' ),
				'default' => 8,
				'min'     => 1,
				'max'     => 48,
			),
			'columns' => array(
				'type'    => 'select',
				'heading' => __( 'Columns', 'ux-archives-block' ),
				'options' => array( '2' => 2, '3' => 3, '4' => 4 ),
				'default' => 4,
			),
			'show_filter' => array(
				'type'    => 'checkbox',
				'heading' => __( 'Show category filter', 'ux-archives-block' ),
				'default' => true,
			),
			'filter_taxonomy' => array(
				'type'    => 'select',
				'heading' => __( 'Filter taxonomy', 'ux-archives-block' ),
				'options' => array(
					'archives_category' => __( 'Categories', 'ux-archives-block' ),
					'archives_tag'      => __( 'Tags', 'ux-archives-block' ),
				),
				'default' => 'archives_category',
			),
			'link_type' => array(
				'type'    => 'select',
				'heading' => __( 'Item link type', 'ux-archives-block' ),
				'options' => array(
					'lightbox' => __( 'Open image in lightbox', 'ux-archives-block' ),
					'single'   => __( 'Go to single post', 'ux-archives-block' ),
				),
				'default' => 'lightbox',
			),
			'enable_loadmore' => array(
				'type' => 'checkbox',
				'heading' => __( 'Enable Load More button', 'ux-archives-block' ),
				'default' => 'no',
			),
		),
	) );

} );

/* ---------------------------
 * Shortcode handler (front-end)
 * --------------------------*/
add_shortcode( 'ux_archives', function( $atts ) {

	$defaults = array(
		'posts_per_page'  => 8,
		'columns'         => 4,
		'show_filter'     => 'yes',
		'filter_taxonomy' => 'archives_category',
		'link_type'       => 'lightbox',
		'enable_loadmore' => 'no',
	);

	$atts = shortcode_atts( $defaults, $atts, 'ux_archives' );
	$atts['unique_id'] = 'ux-archives-' . wp_rand( 10000, 99999 );

	// prepare the query args (we will JSON-encode this for the JS -> AJAX)
	$query_args = array(
		'post_type'      => 'archives',
		'posts_per_page' => max( 1, (int) $atts['posts_per_page'] ),
		'paged'          => 1,
	);

	// If a taxonomy is selected and you want initial filtering by one term (left blank here).
	// (UX Builder option to choose a specific category could be added later.)
	$atts['_query_args'] = $query_args;

	// expose $atts to template
	$atts_for_template = $atts;

	ob_start();

	$tpl = UX_ARCHIVES_PLUGIN_DIR . 'templates/ux-archives-template.php';
	if ( file_exists( $tpl ) ) {
		include $tpl;
	} else {
		echo '<p>' . esc_html__( 'Archives template not found.', 'ux-archives-block' ) . '</p>';
	}

	return ob_get_clean();
} );


/* ---------------------------
 * AJAX Load More handler
 * --------------------------*/
add_action( 'wp_ajax_nopriv_ux_archives_load', 'ux_archives_loadmore_ajax' );
add_action( 'wp_ajax_ux_archives_load', 'ux_archives_loadmore_ajax' );

function ux_archives_loadmore_ajax() {
	// Check nonce
	if ( empty( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'ux_archives_load' ) ) {
		wp_send_json_error( 'invalid_nonce', 403 );
	}

	// Validate and decode query
	$raw = wp_unslash( $_POST['query'] ?? '' );
	$page = isset( $_POST['page'] ) ? intval( $_POST['page'] ) : 0;

	$args = json_decode( $raw, true );
	if ( ! is_array( $args ) ) wp_send_json_error( 'invalid_query' );

	// Safety: enforce post_type = archives
	$args['post_type'] = 'archives';
	$args['paged'] = $page + 1;
	$args['posts_per_page'] = isset( $args['posts_per_page'] ) ? min( 48, intval( $args['posts_per_page'] ) ) : 8;

	// Prevent dangerous keys
	$allowed = array( 'post_type', 'posts_per_page', 'paged', 'orderby', 'order', 'tax_query' );
	$safe_args = array();
	foreach ( $allowed as $k ) {
		if ( isset( $args[ $k ] ) ) $safe_args[ $k ] = $args[ $k ];
	}

	$q = new WP_Query( $safe_args );
	ob_start();

	if ( $q->have_posts() ) {
		while ( $q->have_posts() ) {
			$q->the_post();
			$partial = UX_ARCHIVES_PLUGIN_DIR . 'templates/partials/ux-archives-item.php';
			if ( file_exists( $partial ) ) {
				include $partial;
			}
		}
	}
	wp_reset_postdata();

	$html = ob_get_clean();
	wp_send_json_success( $html );
}


/* ---------------------------
 * Enqueue scripts (only when needed)
 * --------------------------*/

add_action('wp_enqueue_scripts', function() {
    
	wp_enqueue_style('ux-archives-css', UX_ARCHIVES_PLUGIN_URL . 'assets/archives.css', [], null);
	//wp_enqueue_script('ux-archives-js', UX_ARCHIVES_PLUGIN_URL . 'assets/archives-fb.js', ['jquery'], null, true);

	global $post;
    // Check if the current post/page has the [ux_archives] shortcode
    if ( isset($post->post_content) && has_shortcode( $post->post_content, 'ux_archives' ) ) {

        // Path to isotope inside your plugin's assets folder
        $isotope_path = plugin_dir_url( __FILE__ ) . 'assets/isotope.pkgd.min.js';

        // Enqueue Isotope with jQuery as dependency
        wp_enqueue_script(
            'ux-archives-isotope-js',
            $isotope_path,
            array('jquery'),
            '3.19.15',
            true
        );

		
    }

	


}, 10 );


/**
 * Load single archive template
 */
add_filter( 'single_template', function( $single ) {
	global $post;
	if ( $post->post_type === 'archives' ) {
		$file = UX_ARCHIVES_PLUGIN_DIR . 'templates/ux-single-archive-template.php';
		if ( file_exists( $file ) ) {
			return $file;
		}
	}
	return $single;
});


	


