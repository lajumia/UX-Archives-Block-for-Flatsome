<?php
/*
Plugin Name: UX Archives Block for Flatsome
Description: Adds a Flatsome UX Builder Archives Grid element for the 'archives' post type (filtering, lightbox/single, pagination more).
Version: 3.0.0
Author: Md Laju Miah
Author URI: https://wpspeedpress.com/
Text Domain: ux-archives-block
*/

if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! defined( 'UX_ARCHIVES_PLUGIN_DIR' ) ) {
	define( 'UX_ARCHIVES_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
	define( 'UX_ARCHIVES_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
}

/**
 * Register CPT & taxonomies if they don't already exist.
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
			'filter_taxonomy' => array(
				'type'    => 'select',
				'heading' => __( 'Filter taxonomy', 'ux-archives-block' ),
				'options' => array(
					'archives_category' => __( 'Categories', 'ux-archives-block' ),
					'archives_tag'      => __( 'Tags', 'ux-archives-block' ),
				),
				'default' => 'archives_category',
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
		'show_pagination' => true,
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
 * Enqueue scripts (only when needed)
 * --------------------------*/
add_action('wp_enqueue_scripts', function() {

    wp_enqueue_style('ux-archives-css', UX_ARCHIVES_PLUGIN_URL . 'assets/archives.css', [], null);

    global $post;
    $should_load_lightbox = false;

    // Load on shortcode page
    if ($post && isset($post->post_content) && has_shortcode($post->post_content, 'ux_archives')) {
        $should_load_lightbox = true;
    }

    // Load on single "archives" post type
    if (is_singular('archives')) {
        $should_load_lightbox = true;
    }

    if ($should_load_lightbox) {

        // Isotope
        $isotope_path = plugin_dir_url(__FILE__) . 'assets/isotope.pkgd.min.js';
        wp_enqueue_script(
            'ux-archives-isotope-js',
            $isotope_path,
            array('jquery'),
            '3.19.15',
            true
        );

        // GLightbox CSS
        wp_enqueue_style(
            'glightbox-css',
            'https://cdn.jsdelivr.net/npm/glightbox/dist/css/glightbox.min.css',
            [],
            '3.2.0'
        );

        // GLightbox JS
        wp_enqueue_script(
            'glightbox-js',
            'https://cdn.jsdelivr.net/npm/glightbox/dist/js/glightbox.min.js',
            array('jquery'),
            '3.2.0',
            true
        );

    }

}, 20);

/**
 * Load single archive template
 */
add_filter( 'single_template', function( $single ) {
	global $post;
	if ( $post->post_type === 'archives' ) {
		$file = UX_ARCHIVES_PLUGIN_DIR . 'templates/ux-single-archives-template.php';
		if ( file_exists( $file ) ) {
			return $file;
		}
	}
	return $single;
});


	
// AJAX handler for UX Archives dynamic loading
add_action('wp_ajax_ux_filter_posts', 'ux_filter_posts_callback');
add_action('wp_ajax_nopriv_ux_filter_posts', 'ux_filter_posts_callback');

function ux_filter_posts_callback() {
    // Sanitize incoming data
    $taxonomy        = isset($_POST['taxonomy']) ? sanitize_text_field($_POST['taxonomy']) : '';
    $term_slug       = isset($_POST['term_slug']) ? sanitize_text_field($_POST['term_slug']) : '';
    $posts_per_page  = isset($_POST['posts_per_page']) ? intval($_POST['posts_per_page']) : 8;
	$columns 		= isset($_POST['columns']) ? intval($_POST['columns']) : 4;
    $paged           = isset($_POST['paged']) ? intval($_POST['paged']) : 1;

    // Post type — change if needed
    $post_type = 'archives';

    // Build query
    $query_args = array(
        'post_type'      => $post_type,
        'posts_per_page' => $posts_per_page,
        'paged'          => $paged,
    );

    if ($taxonomy && $term_slug) {
        $query_args['tax_query'] = array(
            array(
                'taxonomy' => $taxonomy,
                'field'    => 'slug',
                'terms'    => $term_slug,
            ),
        );
    }

    $query = new WP_Query($query_args);

    // Capture HTML output
    ob_start();

    if ($query->have_posts()) :
        while ($query->have_posts()) : $query->the_post();
            // Include your template partial
            include UX_ARCHIVES_PLUGIN_DIR . 'templates/partials/ux-archives-item.php';
        endwhile;
    else :
        echo '<p>No items found.</p>';
    endif;

    $html = ob_get_clean();

    // Return JSON response with pagination info
    $response = array(
        'html'         => $html,
        'total_pages'  => $query->max_num_pages,
        'current_page' => $paged,
    );

    wp_send_json($response); // Proper JSON response
}



